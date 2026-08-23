<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LargeFormatRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLargeFormatController extends Controller
{
    public function index(): View
    {
        return view('admin.large-format.index', [
            'rates' => LargeFormatRate::query()->orderBy('material')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'material' => ['required', 'string', 'max:255', 'unique:large_format_rates,material'],
            'rate_per_sqft' => ['required', 'numeric', 'min:0'],
        ]);

        LargeFormatRate::query()->create([
            ...$validated,
            'is_active' => true,
            'updated_by_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Added "'.$validated['material'].'" to large-format rates.');
    }

    public function update(Request $request, LargeFormatRate $rate): RedirectResponse
    {
        $validated = $request->validate([
            'rate_per_sqft' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $rate->update([
            'rate_per_sqft' => $validated['rate_per_sqft'],
            'is_active' => $request->boolean('is_active'),
            'updated_by_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Rate updated for "'.$rate->material.'".');
    }

    public function destroy(LargeFormatRate $rate): RedirectResponse
    {
        $rate->delete();

        return back()->with('status', '"'.$rate->material.'" removed.');
    }

    public function calculator(): View
    {
        return view('admin.large-format.calculator', [
            'rates' => LargeFormatRate::query()->where('is_active', true)->orderBy('material')->get(['id', 'material', 'rate_per_sqft']),
        ]);
    }
}
