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

        return view('admin.activity-logs.index', [
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
