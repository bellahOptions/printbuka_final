<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    public function index(): View
    {
        return view('admin.staff.index', [
            'pendingStaff' => User::query()
                ->where('role', 'staff_pending')
                ->orWhere(function ($query): void {
                    $query->where('is_active', false)->whereNotNull('requested_role');
                })
                ->latest()
                ->get(),
            'staff' => User::query()
                ->where('role', '!=', 'customer')
                ->where('role', '!=', 'staff_pending')
                ->latest()
                ->get(),
            'roles' => config('printbuka_admin.role_labels'),
            'departments' => config('printbuka_admin.departments'),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(array_keys(config('printbuka_admin.roles', [])))],
            'department' => ['required', 'string', Rule::in(array_values(config('printbuka_admin.departments', [])))],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'role' => $validated['role'],
            'department' => $validated['department'],
            'is_active' => $request->boolean('is_active'),
            'approved_by_id' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('status', 'Staff access updated.');
    }
}
