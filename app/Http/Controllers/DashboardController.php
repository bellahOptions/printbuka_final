<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'productCount' => Product::count(),
            'orderCount' => Order::count(),
            'recentProducts' => Product::query()
                ->latest()
                ->limit(4)
                ->get(),
        ]);
    }
}
