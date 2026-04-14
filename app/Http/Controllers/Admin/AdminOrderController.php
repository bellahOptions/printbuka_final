<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(): View
    {
        return view('admin.orders.index', [
            'orders' => Order::query()
                ->with('product', 'invoice', 'designer', 'productionOfficer')
                ->latest()
                ->paginate(20),
            'workflowPhases' => config('printbuka_admin.workflow_phases'),
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load('product', 'invoice', 'briefReceiver', 'designer', 'productionOfficer', 'qcOfficer', 'dispatcher', 'verifier'),
            'staff' => User::query()
                ->where('role', '!=', 'customer')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'workflowPhases' => config('printbuka_admin.workflow_phases'),
            'jobTypes' => config('printbuka_admin.job_types'),
            'sizes' => config('printbuka_admin.sizes'),
            'priorities' => config('printbuka_admin.priorities'),
            'jobStatuses' => config('printbuka_admin.job_statuses'),
            'paymentStatuses' => config('printbuka_admin.payment_statuses'),
            'materials' => config('printbuka_admin.materials'),
            'finishes' => config('printbuka_admin.finishes'),
            'deliveryMethods' => config('printbuka_admin.delivery_methods'),
            'reviewStatuses' => config('printbuka_admin.review_statuses'),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'job_order_number' => ['nullable', 'string', 'max:255', Rule::unique('orders', 'job_order_number')->ignore($order->id)],
            'job_type' => ['nullable', 'string', 'max:255'],
            'size_format' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'brief_received_by_id' => ['nullable', 'exists:users,id'],
            'brief_received_at' => ['nullable', 'date'],
            'assigned_designer_id' => ['nullable', 'exists:users,id'],
            'design_started_at' => ['nullable', 'date'],
            'design_approved_by_client' => ['nullable', 'boolean'],
            'design_approved_at' => ['nullable', 'date'],
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
        ]);

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

        if ($user->canAdmin('*')) {
            $fields = array_keys($validated);
        }

        if (in_array('verified_by_id', $fields, true) && ! array_key_exists('verified_by_id', $validated)) {
            $validated['verified_by_id'] = $user->id;
        }

        return Arr::only($validated, array_unique($fields));
    }
}
