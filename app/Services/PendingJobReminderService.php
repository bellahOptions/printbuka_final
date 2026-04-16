<?php

namespace App\Services;

use App\Mail\PendingJobsReminderMail;
use App\Models\Order;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PendingJobReminderService
{
    /**
     * @return int Number of emails sent
     */
    public function sendReminders(): int
    {
        $thresholdHours = max(1, (int) SiteSettings::get('pending_job_reminder_hours', 24));
        $cutoff = now()->subHours($thresholdHours);
        $emailsSent = 0;
        $itemsByRecipient = [];

        $phases = collect(config('printbuka_admin.workflow_phases', []));

        foreach ($phases as $phase) {
            $status = (string) ($phase['status'] ?? '');
            $permission = (string) ($phase['permission'] ?? '');

            if ($status === '' || $permission === '') {
                continue;
            }

            $orders = Order::query()
                ->with('product')
                ->where('status', $status)
                ->where('updated_at', '<=', $cutoff)
                ->get();

            foreach ($orders as $order) {
                $recipients = $this->recipientsForOrder($order, $permission);

                foreach ($recipients as $recipient) {
                    $itemsByRecipient[$recipient->id]['recipient'] = $recipient;
                    $itemsByRecipient[$recipient->id]['items'][] = [
                        'order' => $order,
                        'phase' => (string) ($phase['phase'] ?? $status),
                        'status' => $status,
                        'stuck_hours' => (int) max(1, now()->diffInHours($order->updated_at)),
                    ];
                }
            }
        }

        foreach ($itemsByRecipient as $payload) {
            /** @var User $recipient */
            $recipient = $payload['recipient'];
            $items = $payload['items'] ?? [];

            if (! filled($recipient->email) || $items === []) {
                continue;
            }

            try {
                Mail::to($recipient->email)->send(new PendingJobsReminderMail($recipient, $items));
                $emailsSent++;
            } catch (\Throwable $exception) {
                Log::error('Pending jobs reminder failed.', [
                    'recipient_id' => $recipient->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $emailsSent;
    }

    /**
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function recipientsForOrder(Order $order, string $permission)
    {
        $assigneeField = $this->assigneeFieldForPermission($permission);
        $assigneeId = $assigneeField ? (int) ($order->{$assigneeField} ?? 0) : 0;

        if ($assigneeId > 0) {
            $assignee = User::query()->find($assigneeId);

            if ($assignee && $assignee->is_active && $assignee->role !== 'customer') {
                return collect([$assignee]);
            }
        }

        return User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->get()
            ->filter(fn (User $user): bool => $user->canAdmin($permission) || $user->canAdmin('*'))
            ->values();
    }

    private function assigneeFieldForPermission(string $permission): ?string
    {
        return match ($permission) {
            'orders.intake' => 'brief_received_by_id',
            'design.update' => 'assigned_designer_id',
            'production.update' => 'production_officer_id',
            'qc.update' => 'qc_checked_by_id',
            'delivery.update' => 'dispatched_by_id',
            default => null,
        };
    }
}
