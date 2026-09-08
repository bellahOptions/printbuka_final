<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PendingStaffList extends Component
{
    public bool $canAssignRoles = false;

    /**
     * @var array<string, string>
     */
    public array $roles = [];

    public int $perPage = 6;

    public int $page = 1;

    public bool $hasMore = true;

    /**
     * @param  array<string, string>  $roles
     */
    public function mount(bool $canAssignRoles = false, array $roles = []): void
    {
        $this->canAssignRoles = $canAssignRoles;
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
        $query = $this->pendingQuery();
        $totalCount = (clone $query)->count();
        $pendingStaff = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $pendingStaff->count() < $totalCount;

        return view('livewire.admin.pending-staff-list', [
            'pendingStaff' => $pendingStaff,
            'totalCount' => $totalCount,
        ]);
    }

    private function pendingQuery(): Builder
    {
        return User::query()
            ->where('role', 'staff_pending')
            ->orWhere(function (Builder $query): void {
                $query->where('is_active', false)->whereNotNull('requested_role');
            })
            ->latest();
    }
}
