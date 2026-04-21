<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $role = trim((string) $request->input('role', ''));
        $route = trim((string) $request->input('route_name', ''));

        $logs = AdminActivityLog::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery
                        ->where('action', 'like', '%'.$search.'%')
                        ->orWhere('method', 'like', '%'.$search.'%')
                        ->orWhere('route_name', 'like', '%'.$search.'%')
                        ->orWhere('url', 'like', '%'.$search.'%')
                        ->orWhereHas('user', function ($userQuery) use ($search): void {
                            $userQuery
                                ->where('first_name', 'like', '%'.$search.'%')
                                ->orWhere('last_name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
                        });
                });
            })
            ->when($role !== '', fn ($query) => $query->where('role', $role))
            ->when($route !== '', fn ($query) => $query->where('route_name', $route))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('admin.activity-logs.index', [
            'logs' => $logs,
            'search' => $search,
            'role' => $role,
            'routeName' => $route,
            'roles' => AdminActivityLog::query()
                ->select('role')
                ->whereNotNull('role')
                ->distinct()
                ->orderBy('role')
                ->pluck('role')
                ->all(),
            'routeNames' => AdminActivityLog::query()
                ->select('route_name')
                ->whereNotNull('route_name')
                ->distinct()
                ->orderBy('route_name')
                ->pluck('route_name')
                ->take(100)
                ->all(),
        ]);
    }
}
