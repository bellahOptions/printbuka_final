<?php

namespace App\Http\Controllers;

use App\Support\SafeCache;
use App\Support\ServiceCatalog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
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

        return view('services.index', [
            'services' => $services,
        ]);
    }

    public function show(Request $request, string $service): View
    {
        $serviceData = ServiceCatalog::find($service);
        abort_if(! $serviceData, 404);

        $customer = $request->user();

        return view('services.show', [
            'service' => $serviceData,
            'customer' => $customer,
            'deliveryMethods' => ['Client Pickup', 'Delivery Address'],
        ]);
    }
}
