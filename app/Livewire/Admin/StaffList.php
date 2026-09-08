<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class StaffList extends Component
{
    public bool $canAssignRoles = false;

    public bool $canManageEmployment = false;

    public bool $canManageKyc = false;

    /**
     * @var array<string, string>
     */
    public array $roles = [];

    public int $perPage = 12;

    public int $page = 1;

    public bool $hasMore = true;

    /**
     * @param  array<string, string>  $roles
     */
    public function mount(
        bool $canAssignRoles = false,
        bool $canManageEmployment = false,
        bool $canManageKyc = false,
        array $roles = []
    ): void {
        $this->canAssignRoles = $canAssignRoles;
        $this->canManageEmployment = $canManageEmployment;
        $this->canManageKyc = $canManageKyc;
        $this->roles = $roles;
    }

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    public function render(): View
    {
        $query = $this->staffQuery();
        $totalCount = (clone $query)->count();
        $staff = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $staff->count() < $totalCount;

        return view('livewire.admin.staff-list', [
            'staff' => $staff,
            'totalCount' => $totalCount,
        ]);
    }

    private function staffQuery(): Builder
    {
        return User::query()
            ->where('role', '!=', 'customer')
            ->where('role', '!=', 'staff_pending')
            ->where('employment_status', '!=', 'terminated')
            ->with('staffProfile')
            ->latest();
    }
}
