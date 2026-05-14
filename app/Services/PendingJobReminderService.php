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
    private const PAID_PAYMENT_STATUSES = ['Invoice Settled (70%)', 'Invoice Settled (100%)'];
    private const CLOSED_JOB_STATUSES = ['Delivered', 'Cancelled'];

    /**
     * @return int Number of emails sent
     */
    public function sendReminders(): int
    {
        $thresholdHours = max(1, (int) SiteSettings::get('pending_job_reminder_hours', 24));
        $itemsByRecipient = $this->todosByStaff(onlyStale: true, thresholdHours: $thresholdHours);

        return $this->sendTodoEmails($itemsByRecipient);
    }

    /**
     * @return int Number of emails sent
     */
    public function sendManualReminders(): int
    {
        return $this->sendTodoEmails($this->todosByStaff());
    }

    /**
     * @return array<int, array{recipient:User,items:array<int, array{order:Order,phase:string,status:string,stuck_hours:int,payment_status:string,task:string}>}>
     */
    public function todosByStaff(bool $onlyStale = false, ?int $thresholdHours = null): array
    {
        $cutoff = now()->subHours(max(1, $thresholdHours ?? (int) SiteSettings::get('pending_job_reminder_hours', 24)));
        $itemsByRecipient = [];

        foreach (collect(config('printbuka_admin.workflow_phases', [])) as $phase) {
            $status = (string) ($phase['status'] ?? '');
            $permission = (string) ($phase['permission'] ?? '');

            if ($status === '' || $permission === '') {
                continue;
            }

            $orders = Order::query()
                ->with('product')
                ->where('status', $status)
                ->whereIn('payment_status', self::PAID_PAYMENT_STATUSES)
                ->whereNotIn('status', self::CLOSED_JOB_STATUSES)
                ->when($onlyStale, fn ($query) => $query->where('updated_at', '<=', $cutoff))
                ->orderByDesc('is_express')
                ->oldest('updated_at')
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
                        'payment_status' => (string) $order->payment_status,
                        'task' => (string) ($phase['gates'][0] ?? 'Review and update this job.'),
                    ];
                }
            }
        }

        return $itemsByRecipient;
    }

    /**
     * @param  array<int, array{recipient:User,items:array<int, array{order:Order,phase:string,status:string,stuck_hours:int,payment_status:string,task:string}>}>  $itemsByRecipient
     */
    private function sendTodoEmails(array $itemsByRecipient): int
    {
        $emailsSent = 0;

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
