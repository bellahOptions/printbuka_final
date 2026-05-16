<?php

namespace App\Livewire;

use App\Models\Training;
use Livewire\Component;

class TrainingEmailAvailability extends Component
{
    public ?string $email = '';

    public bool $exists = false;

    public function mount(?string $email = null): void
    {
        $this->email = (string) $email;
        $this->checkEmail();
    }

    public function updatedEmail(): void
    {
        $this->checkEmail();
    }

    public function render()
    {
        return view('livewire.training-email-availability');
    }

    private function checkEmail(): void
    {
        $email = str((string) $this->email)->lower()->trim()->toString();

        $this->exists = filter_var($email, FILTER_VALIDATE_EMAIL)
            ? Training::query()->whereRaw('LOWER(email) = ?', [$email])->exists()
            : false;

        $this->dispatch('training-email-availability', exists: $this->exists);
    }
}
