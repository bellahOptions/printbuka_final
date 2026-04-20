<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {

            $featuredProducts = Product::featured()
    ->orderBy('price', 'asc')
    ->limit(4)
    ->get();

    return view('welcome', compact('featuredProducts'));
    }
}
