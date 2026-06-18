<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $to = config('mail.from.address', 'info@printbuka.com');

        Mail::raw(
            "Name: {$data['name']}\nEmail: {$data['email']}\nPhone: {$data['phone']}\nSubject: {$data['subject']}\n\n{$data['message']}",
            fn ($m) => $m->to($to)->subject('Contact Form: ' . ($data['subject'] ?? 'New Message'))
        );

        return redirect()->route('contact')->with('success', 'Your message has been sent. We will get back to you soon.');
    }
}
