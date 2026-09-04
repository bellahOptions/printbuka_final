<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffExportController extends Controller
{
    /**
     * One-way export of staff accounts for migration into a sibling brand's
     * system. Deliberately excludes `role = customer` (not staff) and — unless
     * explicitly requested — `staff_pending` (not yet approved). Also excludes
     * bank/tax/next-of-kin fields entirely: the receiving system has nowhere
     * to store or protect that PII, so it never leaves this database.
     */
    public function index(Request $request): JsonResponse
    {
        $includePending = $request->boolean('include_pending');

        $query = User::query()
            ->with('staffProfile')
            ->where('role', '!=', 'customer')
            ->when(! $includePending, fn ($q) => $q->where('role', '!=', 'staff_pending'))
            ->orderBy('id');

        $page = $query->paginate(min((int) $request->integer('per_page', 100), 200));

        return response()->json([
            'data' => $page->getCollection()->map(function (User $user) {
                return [
                    'id'                 => $user->id,
                    'first_name'         => $user->first_name,
                    'last_name'          => $user->last_name,
                    'email'              => $user->email,
                    'phone'              => $user->phone,
                    'password'           => $user->password, // bcrypt hash — never plaintext
                    'role'               => $user->role,
                    'is_active'          => $user->is_active,
                    'employment_status'  => $user->employment_status,
                    'department'         => $user->department,
                    'designation'        => $user->staffProfile?->designation,
                    'date_of_employment' => optional($user->staffProfile?->date_of_employment)->toDateString(),
                ];
            }),
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'total'        => $page->total(),
            ],
        ]);
    }
}
