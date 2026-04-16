<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceService;
use App\Support\JobAssetUpload;
use App\Support\ReferenceCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(): View
    {
        return view('admin.orders.index', [
            'orders' => Order::query()
                ->with('product', 'invoice', 'designer', 'productionOfficer', 'creatorAdmin', 'briefReceiver')
                ->latest()
                ->paginate(20),
            'workflowPhases' => config('printbuka_admin.workflow_phases'),
            'canViewAmounts' => request()->user()->canAdmin('finance.view_amounts') || request()->user()->canAdmin('invoices.manage'),
        ]);
    }

    public function create(): View
    {
        return view('admin.orders.create', [
            'products' => Product::query()->orderBy('name')->get(),
            'staff' => User::query()
                ->where('role', '!=', 'customer')
                ->where('is_active', true)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'customers' => User::query()
                ->where('role', 'customer')
                ->where('is_active', true)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'jobTypes' => config('printbuka_admin.job_types'),
            'sizes' => config('printbuka_admin.sizes'),
            'priorities' => config('printbuka_admin.priorities'),
            'channels' => config('printbuka_admin.job_channels'),
            'paymentStatuses' => config('printbuka_admin.payment_statuses'),
            'materials' => config('printbuka_admin.materials'),
            'finishes' => config('printbuka_admin.finishes'),
            'deliveryMethods' => config('printbuka_admin.delivery_methods'),
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load('product', 'invoice', 'briefReceiver', 'creatorAdmin', 'designer', 'productionOfficer', 'qcOfficer', 'dispatcher', 'verifier'),
            'staff' => User::query()
                ->where('role', '!=', 'customer')
                ->where('is_active', true)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'workflowPhases' => config('printbuka_admin.workflow_phases'),
            'jobTypes' => config('printbuka_admin.job_types'),
            'sizes' => config('printbuka_admin.sizes'),
            'priorities' => config('printbuka_admin.priorities'),
            'channels' => config('printbuka_admin.job_channels'),
            'jobStatuses' => config('printbuka_admin.job_statuses'),
            'paymentStatuses' => config('printbuka_admin.payment_statuses'),
            'materials' => config('printbuka_admin.materials'),
            'finishes' => config('printbuka_admin.finishes'),
            'deliveryMethods' => config('printbuka_admin.delivery_methods'),
            'reviewStatuses' => config('printbuka_admin.review_statuses'),
            'canViewAmounts' => request()->user()->canAdmin('finance.view_amounts') || request()->user()->canAdmin('invoices.manage'),
        ]);
    }

    public function store(Request $request, InvoiceService $invoiceService): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'customer_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query->where('role', 'customer')->where('is_active', true)
                ),
            ],
            'channel' => ['required', 'string', Rule::in(config('printbuka_admin.job_channels'))],
            'job_type' => ['required', 'string', 'max:255'],
            'size_format' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'delivery_preference' => ['required', Rule::in(['pickup', 'delivery'])],
            'delivery_method' => ['nullable', Rule::in(config('printbuka_admin.delivery_methods'))],
            'delivery_city' => ['nullable', 'required_if:delivery_preference,delivery', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'required_if:delivery_preference,delivery', 'string', 'max:500'],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'string', 'max:255'],
            'brief_received_at' => ['nullable', 'date'],
            'assigned_designer_id' => ['nullable', 'exists:users,id'],
            'material_substrate' => ['nullable', 'string', 'max:255'],
            'finish_lamination' => ['nullable', 'string', 'max:255'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['required', 'string', 'max:255'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
            'job_asset_files' => ['nullable', 'array'],
            'job_asset_files.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,svg,zip', 'max:20480'],
        ]);
        unset($validated['job_asset_files']);

        $selectedCustomer = null;

        if (filled($validated['customer_id'] ?? null)) {
            $selectedCustomer = User::query()
                ->whereKey($validated['customer_id'])
                ->where('role', 'customer')
                ->where('is_active', true)
                ->first();
        }

        if (! $selectedCustomer && filled($validated['customer_email'] ?? null)) {
            $selectedCustomer = User::query()
                ->where('role', 'customer')
                ->where('is_active', true)
                ->where('email', $validated['customer_email'])
                ->first();
        }

        if ($selectedCustomer) {
            $validated['customer_name'] = $selectedCustomer->displayName();
            $validated['customer_email'] = $selectedCustomer->email;
            $validated['customer_phone'] = $selectedCustomer->phone ?: $validated['customer_phone'];
        }

        $deliveryPreference = $validated['delivery_preference'] ?? 'delivery';
        unset($validated['delivery_preference'], $validated['customer_id']);

        if ($deliveryPreference === 'pickup') {
            $validated['delivery_method'] = 'Client Pickup';
            $validated['delivery_city'] = null;
            $validated['delivery_address'] = null;
        } else {
            $validated['delivery_method'] = $validated['delivery_method'] ?: 'Dispatch Rider';
        }

        $quantity = (int) $validated['quantity'];
        $unitPrice = (float) $validated['unit_price'];
        $amountPaid = (float) ($validated['amount_paid'] ?? 0);

        $order = Order::query()->create([
            ...$validated,
            'user_id' => $selectedCustomer?->id,
            'created_by_admin_id' => Auth::id(),
            'service_type' => 'print',
            'job_order_number' => ReferenceCode::jobOrderNumber('print'),
            'total_price' => $quantity * $unitPrice,
            'status' => 'Analyzing Job Brief',
            'brief_received_by_id' => Auth::id(),
            'brief_received_at' => $validated['brief_received_at'] ?? now(),
            'amount_paid' => $amountPaid,
            'job_image_assets' => JobAssetUpload::fromRequest($request),
        ]);

        $invoice = $invoiceService->createForOrder($order);
        $sent = $invoiceService->sendInvoice($invoice);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with(
                $sent ? 'status' : 'warning',
                $sent
                    ? 'Job created. Invoice generated and emailed to the client.'
                    : 'Job created and invoice generated, but the invoice email could not be sent.'
            );
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'job_order_number' => ['nullable', 'string', 'max:255', Rule::unique('orders', 'job_order_number')->ignore($order->id)],
            'channel' => ['nullable', 'string', Rule::in(config('printbuka_admin.job_channels'))],
            'job_type' => ['nullable', 'string', 'max:255'],
            'size_format' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
            'brief_received_by_id' => ['nullable', 'exists:users,id'],
            'brief_received_at' => ['nullable', 'date'],
            'assigned_designer_id' => ['nullable', 'exists:users,id'],
            'design_started_at' => ['nullable', 'date'],
            'design_approved_by_client' => ['nullable', 'boolean'],
            'design_approved_at' => ['nullable', 'date'],
            'design_file' => ['nullable', 'file', 'max:10240'],
            'job_asset_files' => ['nullable', 'array'],
            'job_asset_files.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,svg,zip', 'max:20480'],
            'production_officer_id' => ['nullable', 'exists:users,id'],
            'production_started_at' => ['nullable', 'date'],
            'material_substrate' => ['nullable', 'string', 'max:255'],
            'finish_lamination' => ['nullable', 'string', 'max:255'],
            'qc_checked_by_id' => ['nullable', 'exists:users,id'],
            'qc_checked_at' => ['nullable', 'date'],
            'qc_result' => ['nullable', 'string', 'max:255'],
            'estimated_delivery_at' => ['nullable', 'date'],
            'actual_delivery_at' => ['nullable', 'date'],
            'delivery_method' => ['nullable', 'string', 'max:255'],
            'dispatched_by_id' => ['nullable', 'exists:users,id'],
            'client_review_status' => ['nullable', 'string', 'max:255'],
            'after_sales_action' => ['nullable', 'string', 'max:2000'],
            'after_sales_resolved_at' => ['nullable', 'date'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['nullable', 'string', 'max:255'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
            'verified_by_id' => ['nullable', 'exists:users,id'],
            'verified_at' => ['nullable', 'date'],
            'phase_approval_status' => ['nullable', 'string', 'max:255'],
            'phase_approval_comment' => ['nullable', 'string', 'max:3000'],
        ]);
        unset($validated['job_asset_files']);

        if ($request->hasFile('design_file') && $request->user()->canAdmin('design.upload')) {
            $validated['final_design_path'] = $request->file('design_file')->store('designs', 'public');
            try {
                Mail::raw(
                    'Hello '.$order->customer_name.",\n\nYour Printbuka design file for ".$order->displayNumber()." is attached for review.\n\nPrintbuka",
                    function ($message) use ($order, $validated): void {
                        $message
                            ->to($order->customer_email)
                            ->subject('Design file for '.$order->displayNumber())
                            ->attachFromStorageDisk('public', $validated['final_design_path']);
                    }
                );
            } catch (\Throwable $exception) {
                Log::error('Design file email failed to send.', [
                    'order_id' => $order->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        if ($request->hasFile('job_asset_files') && ($request->user()->canAdmin('orders.intake') || $request->user()->canAdmin('design.upload') || $request->user()->canAdmin('*'))) {
            $validated['job_image_assets'] = JobAssetUpload::fromRequest($request, $order->job_image_assets ?? []);
        }

        $order->fill($this->allowedChanges($request, $validated));
        $order->save();

        return back()->with('status', 'Workflow update saved.');
    }

    private function allowedChanges(Request $request, array $validated): array
    {
        $user = $request->user();
        $fields = [];

        foreach (config('printbuka_admin.workflow_phases', []) as $phase) {
            if ($user->canAdmin($phase['permission'])) {
                $fields = array_merge($fields, $phase['fields']);
            }
        }

        if ($user->canAdmin('invoices.manage')) {
            $fields = array_merge($fields, ['amount_paid', 'payment_status', 'internal_notes']);
        }

        if ($user->canAdmin('orders.verify')) {
            $fields = array_merge($fields, ['verified_by_id', 'verified_at', 'internal_notes']);
        }

        if ($user->canAdmin('workflow.approve')) {
            $fields = array_merge($fields, ['status', 'phase_approval_status', 'phase_approval_comment', 'phase_approved_by_id', 'phase_approved_at', 'internal_notes']);
        }

        if ($user->canAdmin('*')) {
            $fields = array_keys($validated);
        }

        if (in_array('verified_by_id', $fields, true) && ! array_key_exists('verified_by_id', $validated)) {
            $validated['verified_by_id'] = $user->id;
        }

        if (in_array('phase_approved_by_id', $fields, true) && array_key_exists('phase_approval_status', $validated)) {
            $validated['phase_approved_by_id'] = $user->id;
            $validated['phase_approved_at'] = now();
        }

        return Arr::only($validated, array_unique($fields));
    }
}
