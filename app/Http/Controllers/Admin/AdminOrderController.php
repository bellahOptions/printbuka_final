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
            'priorities' => config('printbuka_admin.priorities'),
            'jobStatuses' => config('printbuka_admin.job_statuses'),
            'paymentStatuses' => config('printbuka_admin.payment_statuses'),
            'deliveryMethods' => config('printbuka_admin.delivery_methods'),
            'reviewStatuses' => config('printbuka_admin.review_statuses'),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'job_order_number' => ['nullable', 'string', 'max:255', Rule::unique('orders', 'job_order_number')->ignore($order->id)],
            'priority' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'brief_received_by_id' => ['nullable', 'exists:users,id'],
            'brief_received_at' => ['nullable', 'date'],
            'assigned_designer_id' => ['nullable', 'exists:users,id'],
            'design_started_at' => ['nullable', 'date'],
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

        $map = [
            'orders.intake' => ['job_order_number', 'priority', 'status', 'brief_received_by_id', 'brief_received_at', 'assigned_designer_id', 'internal_notes'],
            'design.update' => ['status', 'design_started_at', 'design_approved_at', 'internal_notes'],
            'production.update' => ['status', 'production_officer_id', 'production_started_at', 'material_substrate', 'finish_lamination', 'internal_notes'],
            'qc.update' => ['status', 'qc_checked_by_id', 'qc_checked_at', 'qc_result', 'internal_notes'],
            'delivery.update' => ['status', 'estimated_delivery_at', 'actual_delivery_at', 'delivery_method', 'dispatched_by_id', 'internal_notes'],
            'client_review.update' => ['status', 'client_review_status', 'after_sales_action', 'after_sales_resolved_at', 'internal_notes'],
            'invoices.manage' => ['amount_paid', 'payment_status', 'internal_notes'],
            'orders.verify' => ['verified_by_id', 'verified_at', 'internal_notes'],
        ];

        foreach ($map as $permission => $permissionFields) {
            if ($user->canAdmin($permission)) {
                $fields = array_merge($fields, $permissionFields);
            }
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
