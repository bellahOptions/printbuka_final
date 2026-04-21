<?php

namespace App\Services;

use App\Mail\JobAssignedDesignerMail;
use App\Mail\JobPhaseRoleAlertMail;
use App\Mail\JobStatusAdvancedCustomerMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class JobWorkflowNotificationService
{
    public function handleOrderCreated(Order $order): void
    {
        $order->loadMissing('product', 'designer', 'creatorAdmin');

        if ($order->assigned_designer_id) {
            $this->notifyDesignerAssignment($order, (int) $order->assigned_designer_id);
        }

        $currentStatus = (string) ($order->status ?? '');

        if ($currentStatus !== '') {
            $this->notifyResponsibleStaffForPhase($order, 'New job created', $currentStatus);
        }
    }

    /**
     * @param  array<string, mixed>  $original
     */
    public function handleOrderUpdated(Order $order, array $original): void
    {
        $order->loadMissing('product', 'designer', 'creatorAdmin');

        $oldStatus = (string) ($original['status'] ?? '');
        $newStatus = (string) ($order->status ?? '');

        if ($oldStatus !== '' && $newStatus !== '' && $oldStatus !== $newStatus) {
            $this->notifyCustomerStatusChange($order, $oldStatus, $newStatus);
            $this->notifyResponsibleStaffForPhase($order, $oldStatus, $newStatus);
        }

        $oldDesignerId = isset($original['assigned_designer_id']) ? (int) $original['assigned_designer_id'] : null;
        $newDesignerId = $order->assigned_designer_id ? (int) $order->assigned_designer_id : null;

        if ($newDesignerId && $oldDesignerId !== $newDesignerId) {
            $this->notifyDesignerAssignment($order, $newDesignerId);
        }
    }

    private function notifyCustomerStatusChange(Order $order, string $oldStatus, string $newStatus): void
    {
        if (! filled($order->customer_email)) {
            return;
        }

        try {
            Mail::to($order->customer_email)->send(new JobStatusAdvancedCustomerMail($order, $oldStatus, $newStatus));
        } catch (\Throwable $exception) {
            Log::error('Customer job status notification failed.', [
                'order_id' => $order->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function notifyDesignerAssignment(Order $order, int $designerId): void
    {
        $designer = User::query()->find($designerId);

        if (! $designer || ! $designer->is_active || ! filled($designer->email)) {
            return;
        }

        try {
            Mail::to($designer->email)->send(new JobAssignedDesignerMail($order, $designer));
        } catch (\Throwable $exception) {
            Log::error('Designer assignment notification failed.', [
                'order_id' => $order->id,
                'designer_id' => $designerId,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function notifyResponsibleStaffForPhase(Order $order, string $oldStatus, string $newStatus): void
    {
        $phase = collect(config('printbuka_admin.workflow_phases', []))
            ->first(fn (array $item): bool => (string) ($item['status'] ?? '') === $newStatus);

        if (! is_array($phase)) {
            return;
        }

        $permission = (string) ($phase['permission'] ?? '');

        if ($permission === '') {
            return;
        }

        $recipients = User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->get()
            ->filter(fn (User $user): bool => filled($user->email) && ($user->canAdmin($permission) || $user->canAdmin('*')));

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new JobPhaseRoleAlertMail($order, $recipient, $phase, $oldStatus, $newStatus));
            } catch (\Throwable $exception) {
                Log::error('Phase role notification failed.', [
                    'order_id' => $order->id,
                    'recipient_id' => $recipient->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
