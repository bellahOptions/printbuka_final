<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\JobWorkflowNotificationService;
use App\Services\OrderFulfillmentService;
use App\Support\JobAssetUpload;
use App\Support\LivewireSecureUploads;
use App\Support\ReferenceCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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

    public function create(OrderFulfillmentService $orderFulfillmentService): View
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
            'expressSurcharge' => $orderFulfillmentService->expressSurcharge(),
            'sampleSurcharge' => $orderFulfillmentService->sampleSurcharge(),
        ]);
    }

    public function show(Order $order): View
    {
        /** @var User $user */
        $user = request()->user();

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
            'statusOptions' => $this->statusOptionsForUser($user, $order),
            'visibleWorkflowPhases' => $this->visibleWorkflowPhasesForUser($user),
        ]);
    }

    public function store(
        Request $request,
        InvoiceService $invoiceService,
        JobWorkflowNotificationService $jobWorkflowNotificationService,
        OrderFulfillmentService $orderFulfillmentService
    ): RedirectResponse
    {
        $isSampleRequested = $request->boolean('is_sample');

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
            'quantity' => [
                'required',
                'integer',
                'min:1',
                Rule::when($isSampleRequested, ['max:2']),
            ],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'is_express' => ['nullable', 'boolean'],
            'is_sample' => ['nullable', 'boolean'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'delivery_preference' => ['required', Rule::in(['pickup', 'delivery'])],
            'delivery_method' => ['nullable', Rule::in(config('printbuka_admin.delivery_methods'))],
            'delivery_city' => ['nullable', 'required_if:delivery_preference,delivery', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'required_if:delivery_preference,delivery', 'string', 'max:500'],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'string', 'max:255'],
            'assigned_designer_id' => ['nullable', 'exists:users,id'],
            'material_substrate' => ['nullable', 'string', 'max:255'],
            'finish_lamination' => ['nullable', 'string', 'max:255'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['required', 'string', 'max:255'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
            'job_asset_files' => ['nullable', 'array'],
            'job_asset_files.*' => ['file', 'mimes:pdf,svg,zip', 'max:20480'],
            'job_asset_image_paths' => ['nullable', 'array'],
            'job_asset_image_paths.*' => ['string', 'max:255'],
        ]);
        unset($validated['job_asset_files'], $validated['job_asset_image_paths']);

        $isSample = (bool) ($validated['is_sample'] ?? false);
        $isExpress = $isSample || (bool) ($validated['is_express'] ?? false);
        unset($validated['is_express'], $validated['is_sample']);

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
        $baseTotal = $quantity * $unitPrice;
        $pricingAdjustments = $orderFulfillmentService->pricingAdjustments($isExpress, $isSample);
        $total = $baseTotal + $pricingAdjustments['total_adjustment'];
        $expressPaymentAnchor = $isExpress && $this->hasExpressPaymentAnchor($validated, $amountPaid)
            ? now()
            : null;

        $order = Order::query()->create([
            ...$validated,
            'user_id' => $selectedCustomer?->id,
            'created_by_admin_id' => Auth::id(),
            'service_type' => 'print',
            'job_order_number' => ReferenceCode::jobOrderNumber('print'),
            'priority' => $isExpress ? '🔴 Urgent' : ($validated['priority'] ?? '🟡 Normal'),
            'is_express' => $isExpress,
            'is_sample' => $isSample,
            'total_price' => $total,
            'status' => 'Analyzing Job Brief',
            'brief_received_by_id' => Auth::id(),
            'brief_received_at' => now(),
            'estimated_delivery_at' => $orderFulfillmentService->estimateForNewOrder($isExpress, now(), $expressPaymentAnchor),
            'amount_paid' => $amountPaid,
            'pricing_breakdown' => [
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'base_total' => $baseTotal,
                'express_fee' => $pricingAdjustments['express_fee'],
                'sample_fee' => $pricingAdjustments['sample_fee'],
                'total' => $total,
            ],
            'job_image_assets' => JobAssetUpload::fromRequest($request),
        ]);

        $invoice = $invoiceService->createForOrder($order);
        $sent = $invoiceService->sendInvoice($invoice);
        $jobWorkflowNotificationService->handleOrderCreated($order->fresh(['product', 'designer', 'creatorAdmin']));

        return redirect()
            ->route('admin.orders.show', $order)
            ->with(
                $sent ? 'status' : 'warning',
                $sent
                    ? 'Job created. Invoice generated and emailed to the client.'
                    : 'Job created and invoice generated, but the invoice email could not be sent.'
            );
    }

    public function update(
        Request $request,
        Order $order,
        JobWorkflowNotificationService $jobWorkflowNotificationService
    ): RedirectResponse
    {
        $original = $order->getOriginal();
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
            'design_file' => ['nullable', 'file', 'mimes:pdf,svg,zip', 'max:10240'],
            'design_image_path' => ['nullable', 'string', 'max:255'],
            'job_asset_files' => ['nullable', 'array'],
            'job_asset_files.*' => ['file', 'mimes:pdf,svg,zip', 'max:20480'],
            'job_asset_image_paths' => ['nullable', 'array'],
            'job_asset_image_paths.*' => ['string', 'max:255'],
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
        unset($validated['job_asset_files'], $validated['job_asset_image_paths'], $validated['design_image_path']);

        $this->assertStatusTransitionAllowed(
            $request->user(),
            $order,
            $validated['status'] ?? null,
            $validated['payment_status'] ?? null
        );

        $designImagePath = null;
        $designImagePathInput = filled($request->input('design_image_path'))
            ? (string) $request->input('design_image_path')
            : null;

        if (! $request->hasFile('design_file') && $designImagePathInput !== null) {
            $designImagePath = LivewireSecureUploads::consumePath($request, $designImagePathInput, ['designs/images']);

            if ($designImagePath === null) {
                throw ValidationException::withMessages([
                    'design_image_path' => 'The uploaded design image is invalid or expired. Please upload it again.',
                ]);
            }
        }

        if ($request->hasFile('design_file') && $request->user()->canAdmin('design.upload')) {
            $validated['final_design_path'] = $request->file('design_file')->store('designs', 'public');
        } elseif ($designImagePath !== null && $request->user()->canAdmin('design.upload')) {
            $validated['final_design_path'] = $designImagePath;
        }

        if (array_key_exists('final_design_path', $validated) && filled($validated['final_design_path'])) {
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
        $jobWorkflowNotificationService->handleOrderUpdated(
            $order->fresh(['product', 'designer', 'creatorAdmin']),
            $original
        );

        return back()->with('status', 'Workflow update saved.');
    }

    private function assertStatusTransitionAllowed(
        User $user,
        Order $order,
        ?string $requestedStatus,
        ?string $incomingPaymentStatus
    ): void {
        if (! filled($requestedStatus) || $requestedStatus === $order->status) {
            return;
        }

        $phaseOneStatus = (string) (config('printbuka_admin.workflow_phases.0.status') ?? 'Analyzing Job Brief');

        if ($order->status === $phaseOneStatus && $requestedStatus !== $phaseOneStatus) {
            $effectivePaymentStatus = (string) $order->payment_status;

            if (filled($incomingPaymentStatus) && ($user->canAdmin('invoices.manage') || $user->canAdmin('*'))) {
                $effectivePaymentStatus = $incomingPaymentStatus;
            }

            if (! in_array($effectivePaymentStatus, ['Invoice Settled (70%)', 'Invoice Settled (100%)'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'A job cannot leave Phase 1 until payment is settled at 70% or 100%.',
                ]);
            }
        }

        if ($user->canAdmin('*') || $user->canAdmin('workflow.approve')) {
            return;
        }

        $targetPhase = collect(config('printbuka_admin.workflow_phases', []))
            ->first(fn (array $phase): bool => (string) ($phase['status'] ?? '') === $requestedStatus);

        if (! is_array($targetPhase)) {
            return;
        }

        $permission = (string) ($targetPhase['permission'] ?? '');

        if ($permission !== '' && ! $user->canAdmin($permission)) {
            throw ValidationException::withMessages([
                'status' => 'You cannot move this job to that phase. It is not part of your role privileges.',
            ]);
        }
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

    /**
     * @return array<int, string>
     */
    private function statusOptionsForUser(User $user, Order $order): array
    {
        $statuses = collect(config('printbuka_admin.job_statuses', []));

        if ($user->canAdmin('*') || $user->canAdmin('workflow.approve')) {
            return $statuses->values()->all();
        }

        $phaseStatuses = collect(config('printbuka_admin.workflow_phases', []))
            ->filter(fn (array $phase): bool => $user->canAdmin((string) ($phase['permission'] ?? '')))
            ->pluck('status')
            ->map(fn ($status): string => (string) $status);

        return $statuses
            ->filter(fn ($status): bool => (string) $status === (string) $order->status || $phaseStatuses->contains((string) $status))
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function visibleWorkflowPhasesForUser(User $user): array
    {
        $phases = collect(config('printbuka_admin.workflow_phases', []));

        if ($user->canAdmin('*') || $user->canAdmin('workflow.approve')) {
            return $phases->values()->all();
        }

        return $phases
            ->filter(fn (array $phase): bool => $user->canAdmin((string) ($phase['permission'] ?? '')))
            ->values()
            ->all();
    }

    private function hasExpressPaymentAnchor(array $validated, float $amountPaid): bool
    {
        if ($amountPaid > 0) {
            return true;
        }

        return in_array(
            (string) ($validated['payment_status'] ?? ''),
            ['Invoice Settled (70%)', 'Invoice Settled (100%)'],
            true
        );
    }
}
