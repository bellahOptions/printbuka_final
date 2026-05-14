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
        'customers' => 'Customers',
        'audit-logs' => 'Audit Logs',
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

<div class="border-y border-slate-200/70 bg-white/85 shadow-sm shadow-slate-200/40 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
        <nav aria-label="Breadcrumb" class="overflow-x-auto">
            <ol class="flex min-w-0 items-center gap-2 whitespace-nowrap text-xs font-black uppercase tracking-wide text-slate-500 sm:text-sm">
                @foreach ($crumbs as $index => $crumb)
                    <li class="flex min-w-0 items-center gap-2">
                        @if ($loop->last)
                            <span class="inline-flex max-w-[14rem] items-center truncate rounded-full border border-pink-200 bg-pink-50 px-3 py-1.5 text-pink-700 sm:max-w-none">
                                {{ $crumb['label'] }}
                            </span>
                        @else
                            <a href="{{ $crumb['url'] }}" class="inline-flex max-w-[12rem] items-center truncate rounded-full px-2.5 py-1.5 text-slate-600 transition hover:bg-pink-50 hover:text-pink-700 sm:max-w-none">
                                {{ $crumb['label'] }}
                            </a>
                            <span aria-hidden="true" class="text-slate-300">/</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>
</div>
