@php
    use Illuminate\Support\Str;

    $rootLabel = $rootLabel ?? 'Home';
    $rootRoute = $rootRoute ?? null;
    $skipSegments = (array) ($skipSegments ?? []);

    $labelMap = [
        'admin' => 'Admin',
        'dashboard' => 'Dashboard',
        'products' => 'Products',
        'product-categories' => 'Product Categories',
        'categories' => 'Categories',
        'services' => 'Services',
        'orders' => 'Orders',
        'support' => 'Support',
        'support-tickets' => 'Support Tickets',
        'track-order' => 'Track Order',
        'manage-invoices' => 'Invoices',
        'profile' => 'Profile',
        'create' => 'Create',
        'edit' => 'Edit',
        'show' => 'Details',
        'download' => 'Download',
        'index' => 'Overview',
        'blog' => 'Blog',
        'quotes' => 'Quotes',
        'partners' => 'Partners',
        'settings' => 'Settings',
        'notifications' => 'Notifications',
        'finance' => 'Finance',
        'staff' => 'Staff',
    ];

    $formatLabel = static function (string $segment) use ($labelMap): string {
        if (is_numeric($segment)) {
            return '#'.$segment;
        }

        $normalized = Str::lower($segment);

        if (isset($labelMap[$normalized])) {
            return $labelMap[$normalized];
        }

        return (string) Str::of($segment)
            ->replace(['-', '_'], ' ')
            ->title();
    };

    $crumbs = [];
    $crumbs[] = [
        'label' => $rootLabel,
        'url' => $rootRoute ? route($rootRoute) : url('/'),
    ];

    $currentPath = '';
    foreach (request()->segments() as $segment) {
        $currentPath .= '/'.$segment;

        if (in_array($segment, $skipSegments, true)) {
            continue;
        }

        $crumbs[] = [
            'label' => $formatLabel((string) $segment),
            'url' => url($currentPath),
        ];
    }
@endphp

<div class="border-b border-slate-100 bg-slate-50/70">
    <div class="mx-auto max-w-7xl px-4 py-2 sm:px-6 lg:px-8">
        <div class="breadcrumbs text-xs sm:text-sm text-slate-500">
            <ul>
                @foreach ($crumbs as $index => $crumb)
                    @if ($loop->last)
                        <li class="font-semibold text-slate-700">{{ $crumb['label'] }}</li>
                    @else
                        <li>
                            <a href="{{ $crumb['url'] }}" class="hover:text-pink-600 transition-colors">
                                {{ $crumb['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>

