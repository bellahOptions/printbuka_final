<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminDirectCustomerMessageMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));

        $customers = User::query()
            ->where('role', 'customer')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery
                        ->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('companyName', 'like', '%'.$search.'%');
                });
            })
            ->withCount('orders')
            ->withCount([
                'orders as invoices_count' => fn ($query) => $query->whereHas('invoice'),
            ])
            ->withSum('orders as total_paid', 'amount_paid')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function updateStatus(Request $request, User $customer): RedirectResponse
    {
        $this->assertCustomer($customer);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $customer->forceFill([
            'is_active' => (bool) $validated['is_active'],
        ])->save();

        return back()->with('status', 'Customer status updated.');
    }

    public function sendMessage(Request $request, User $customer): RedirectResponse
    {
        $this->assertCustomer($customer);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if (! filled($customer->email)) {
            throw ValidationException::withMessages([
                'message' => 'The selected customer has no email address.',
            ]);
        }

        $admin = $request->user();

        Mail::to($customer->email)->send(new AdminDirectCustomerMessageMail(
            senderName: $admin->displayName(),
            senderEmail: (string) $admin->email,
            recipientName: $customer->displayName(),
            subjectLine: (string) $validated['subject'],
            body: (string) $validated['message'],
        ));

        return back()->with('status', 'Direct message sent to '.$customer->displayName().'.');
    }

    public function destroy(Request $request, User $customer): RedirectResponse
    {
        abort_unless(($request->user()?->role ?? null) === 'super_admin', 403);

        $this->assertCustomer($customer);

        $customerName = $customer->displayName();
        $customer->delete();

        return back()->with('status', 'Customer '.$customerName.' deleted.');
    }

    private function assertCustomer(User $user): void
    {
        abort_unless($user->role === 'customer', 404);
    }
}
