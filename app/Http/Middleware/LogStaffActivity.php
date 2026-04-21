<?php

namespace App\Http\Middleware;

use App\Models\AdminActivityLog;
use App\Models\StaffActivity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogStaffActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            if ($this->canLog($request, $user)) {
                $statusCode = method_exists($exception, 'getStatusCode')
                    ? (int) $exception->getStatusCode()
                    : null;

                $this->logAuditActivity($request, $user->id, $user->role, $statusCode);
            }

            throw $exception;
        }

        if (! $this->canLog($request, $user)) {
            return $response;
        }

        $this->logStaffActivity($request, $user->id, $user->role, $user->department);
        $this->logAuditActivity($request, $user->id, $user->role, $response->getStatusCode());

        return $response;
    }

    private function describeAction(Request $request): string
    {
        $routeName = str_replace('admin.', '', (string) $request->route()?->getName());
        $routeName = str_replace(['.', '-'], ' ', $routeName);

        return trim($request->method().' '.$routeName) ?: $request->method().' admin action';
    }

    private function describeAuditAction(Request $request): string
    {
        if ($request->routeIs('livewire.update') && $this->isAdminLivewireRequest($request)) {
            return 'POST livewire admin action';
        }

        return $this->describeAction($request);
    }

    private function shouldLogAdminRequest(Request $request): bool
    {
        return $request->routeIs('admin.*') || $this->isAdminLivewireRequest($request);
    }

    private function isAdminLivewireRequest(Request $request): bool
    {
        if (! ($request->routeIs('livewire.update') || $request->is('livewire/update'))) {
            return false;
        }

        $referer = (string) $request->headers->get('referer', '');

        return Str::contains($referer, '/admin');
    }

    private function canLog(Request $request, mixed $user): bool
    {
        return $user !== null
            && $user->hasAdminAccess()
            && $this->shouldLogAdminRequest($request);
    }

    private function subjectType(Request $request): ?string
    {
        foreach (['order', 'customer', 'user', 'finance', 'invoice', 'product', 'product_category'] as $parameter) {
            if ($request->route($parameter)) {
                return $parameter;
            }
        }

        return null;
    }

    private function subjectId(Request $request): ?int
    {
        foreach (['order', 'customer', 'user', 'finance', 'invoice', 'product', 'product_category'] as $parameter) {
            $value = $request->route($parameter);

            if (is_object($value) && isset($value->id)) {
                return (int) $value->id;
            }
        }

        return null;
    }

    /**
     * @return array<string, scalar|null>
     */
    private function routeParameters(Request $request): array
    {
        $route = $request->route();

        if (! $route) {
            return [];
        }

        return collect($route->parameters())
            ->map(function (mixed $value): string|int|float|bool|null {
                if (is_object($value) && isset($value->id)) {
                    return (int) $value->id;
                }

                if (is_scalar($value) || $value === null) {
                    return $value;
                }

                return Str::limit((string) json_encode($value), 120, '');
            })
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadPreview(Request $request): array
    {
        $input = Arr::except($request->all(), [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            '_token',
        ]);

        return collect($input)
            ->map(fn (mixed $value): mixed => $this->previewValue($value))
            ->all();
    }

    private function previewValue(mixed $value): mixed
    {
        if ($value instanceof UploadedFile) {
            return '[uploaded-file]';
        }

        if (is_array($value)) {
            return collect($value)
                ->take(20)
                ->map(fn (mixed $item): mixed => $this->previewValue($item))
                ->values()
                ->all();
        }

        if (is_bool($value) || is_int($value) || is_float($value) || $value === null) {
            return $value;
        }

        return Str::limit((string) $value, 300, '');
    }

    private function logStaffActivity(Request $request, int $userId, ?string $role, ?string $department): void
    {
        if (! $request->routeIs('admin.*')) {
            return;
        }

        StaffActivity::query()->create([
            'user_id' => $userId,
            'role' => $role,
            'department' => $department,
            'action' => $this->describeAction($request),
            'subject_type' => $this->subjectType($request),
            'subject_id' => $this->subjectId($request),
            'ip_address' => $request->ip(),
            'route_name' => $request->route()?->getName(),
        ]);
    }

    private function logAuditActivity(Request $request, int $userId, ?string $role, ?int $statusCode): void
    {
        AdminActivityLog::query()->create([
            'user_id' => $userId,
            'role' => $role,
            'action' => $this->describeAuditAction($request),
            'method' => Str::upper((string) $request->method()),
            'route_name' => $request->route()?->getName(),
            'url' => Str::limit($request->fullUrl(), 2048, ''),
            'subject_type' => $this->subjectType($request),
            'subject_id' => $this->subjectId($request),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1024, ''),
            'status_code' => $statusCode,
            'context' => [
                'route_parameters' => $this->routeParameters($request),
                'query' => $request->query(),
                'payload_preview' => $this->payloadPreview($request),
                'referer' => $request->headers->get('referer'),
            ],
        ]);
    }
}
