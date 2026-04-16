<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class CustomerQuickCreate extends Component
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone = '';

    public string $companyName = '';

    public ?string $statusMessage = null;

    public function createCustomer(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50'],
            'companyName' => ['required', 'string', 'max:255'],
        ]);

        $customer = User::query()->create([
            ...$validated,
            'password' => Hash::make(Str::random(20)),
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => null,
        ]);

        $this->reset(['first_name', 'last_name', 'email', 'phone', 'companyName']);
        $this->resetValidation();
        $this->statusMessage = 'Customer created and selected.';

        $this->dispatch('admin-customer-created', customer: [
            'id' => $customer->id,
            'name' => $customer->displayName(),
            'email' => $customer->email,
            'phone' => $customer->phone,
            'companyName' => $customer->companyName,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.customer-quick-create');
    }
}

