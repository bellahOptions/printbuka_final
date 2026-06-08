@php
    use Illuminate\Support\Str;

    $rootLabel    = $rootLabel    ?? 'Home';
    $rootRoute    = $rootRoute    ?? null;
    $skipSegments = (array)($skipSegments ?? []);

    $labelMap = [
        'admin'              => 'Admin',
        'dashboard'          => 'Dashboard',
        'products'           => 'Products',
        'product-categories' => 'Product Categories',
        'categories'         => 'Categories',
        'services'           => 'Services',
        'orders'             => 'Jobs',
        'support'            => 'Support',
        'support-tickets'    => 'Support Tickets',
        'track-order'        => 'Track Order',
        'manage-invoices'    => 'Invoices',
        'invoices'           => 'Invoices',
        'profile'            => 'Profile',
        'create'             => 'Create',
        'edit'               => 'Edit',
        'show'               => 'Details',
        'download'           => 'Download',
        'index'              => 'Overview',
        'blog'               => 'Blog',
        'quotes'             => 'Quotes',
        'partners'           => 'Partners',
        'settings'           => 'Settings',
        'notifications'      => 'Notifications',
        'finance'            => 'Finance',
        'staff'              => 'Staff',
        'customers'          => 'Customers',
        'activity-logs'      => 'Audit Logs',
        'audit-logs'         => 'Audit Logs',
        'newsletters'        => 'Newsletters',
        'training'           => 'Training',
        'advertisements'     => 'Ads',
        'policies'           => 'Policies',
        'tasks'              => 'Tasks',
        'job-log'            => 'Job Log',
    ];

    $formatLabel = static function (string $segment) use ($labelMap): string {
        if (is_numeric($segment)) {
            return '#'.$segment;
        }
        $key = Str::lower($segment);
        if (isset($labelMap[$key])) {
            return $labelMap[$key];
        }
        return (string) Str::of($segment)->replace(['-', '_'], ' ')->title();
    };

    $crumbs   = [];
    $crumbs[] = ['label' => $rootLabel, 'url' => $rootRoute ? route($rootRoute) : url('/')];

    $currentPath = '';
    foreach (request()->segments() as $segment) {
        $currentPath .= '/'.$segment;
        if (in_array($segment, $skipSegments, true)) {
            continue;
        }
        $crumbs[] = ['label' => $formatLabel((string)$segment), 'url' => url($currentPath)];
    }
@endphp

<div class="border-b border-slate-200/70 bg-white/90 backdrop-blur-sm">
    <div class="mx-auto max-w-[1440px] px-4 py-2.5 sm:px-6 lg:px-8">
        <nav aria-label="Breadcrumb">
            <ol class="flex min-w-0 flex-wrap items-center gap-1 text-xs">
                @foreach($crumbs as $crumb)
                    <li class="flex items-center gap-1 min-w-0">
                        @if($loop->last)
                            <span class="rounded-md bg-brand-50 px-2.5 py-1 font-semibold text-brand-700 truncate max-w-[16rem]">
                                {{ $crumb['label'] }}
                            </span>
                        @else
                            <a href="{{ $crumb['url'] }}"
                               class="rounded-md px-2 py-1 font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 truncate max-w-[12rem]">
                                {{ $crumb['label'] }}
                            </a>
                            <svg class="h-3 w-3 shrink-0 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>
</div>
