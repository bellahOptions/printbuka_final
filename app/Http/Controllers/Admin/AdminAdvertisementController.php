<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminAdvertisementController extends Controller
{
    public function index(): View
    {
        return view('admin.advertisements.index', [
            'advertisements' => Advertisement::query()->latest()->paginate(15),
            'placements' => Advertisement::placements(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'placement' => ['required', Rule::in(array_keys(Advertisement::placements()))],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'url', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        Advertisement::query()->create([
            ...$validated,
            'user_id' => $request->user()->id,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return back()->with('status', 'Advertisement published.');
    }

    public function destroy(Advertisement $advertisement): RedirectResponse
    {
        $advertisement->delete();

        return back()->with('status', 'Advertisement deleted.');
    }
}
