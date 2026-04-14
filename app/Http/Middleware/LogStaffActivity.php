<?php

namespace App\Http\Middleware;

use App\Models\StaffActivity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogStaffActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = $request->user();

        if ($user && $user->hasAdminAccess() && $request->routeIs('admin.*')) {
            StaffActivity::query()->create([
                'user_id' => $user->id,
                'role' => $user->role,
                'department' => $user->department,
                'action' => $this->describeAction($request),
                'subject_type' => $this->subjectType($request),
                'subject_id' => $this->subjectId($request),
                'ip_address' => $request->ip(),
                'route_name' => $request->route()?->getName(),
            ]);
        }

        return $response;
    }

    private function describeAction(Request $request): string
    {
        $routeName = str_replace('admin.', '', (string) $request->route()?->getName());
        $routeName = str_replace(['.', '-'], ' ', $routeName);

        return trim($request->method().' '.$routeName) ?: $request->method().' admin action';
    }

    private function subjectType(Request $request): ?string
    {
        foreach (['order', 'user', 'finance', 'invoice', 'product', 'product_category'] as $parameter) {
            if ($request->route($parameter)) {
                return $parameter;
            }
        }

        return null;
    }

    private function subjectId(Request $request): ?int
    {
        foreach (['order', 'user', 'finance', 'invoice', 'product', 'product_category'] as $parameter) {
            $value = $request->route($parameter);

            if (is_object($value) && isset($value->id)) {
                return (int) $value->id;
            }
        }

        return null;
    }
}
