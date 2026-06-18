<?php

namespace App\Http\Controllers;

use App\Support\SafeCache;
use App\Support\ServiceCatalog;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = SafeCache::remember('services:index:v1', now()->addMinutes(5), function (): array {
            return collect(ServiceCatalog::all())
                ->map(function (array $service, string $slug): array {
                    return [
                        ...$service,
                        'slug' => $slug,
                        'price' => ServiceCatalog::priceForSlug($slug),
                    ];
                })
                ->values()
                ->all();
        });

        return Inertia::render('Service/Index', [
            'services' => $services,
        ]);
    }

    public function show(string $service): Response
    {
        $serviceData = ServiceCatalog::find($service);
        abort_if(! $serviceData, 404);

        return Inertia::render('Service/Show', [
            'service' => [...$serviceData, 'slug' => $service],
        ]);
    }
}
