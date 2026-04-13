<?php

namespace App\Http\Controllers;

use App\Models\PartnerApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function create(): View
    {
        return view('partners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'business_type' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'client_volume' => ['nullable', 'string', 'max:255'],
            'services_needed' => ['required', 'string', 'max:2000'],
            'delivery_packaging_needs' => ['nullable', 'string', 'max:2000'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        PartnerApplication::create($validated);

        return back()->with('status', 'Your partner request has been submitted. The Printbuka team will reach out to you shortly.');
    }
}
