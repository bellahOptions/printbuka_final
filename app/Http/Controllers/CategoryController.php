<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', [
            'categories' => ProductCategory::query()
                ->with(['products' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
