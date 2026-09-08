<?php

namespace App\Services;

use App\Mail\JobCompletedAppreciationMail;
use App\Mail\JobConcludedStaffNotificationMail;
use App\Mail\JobConclusionSummaryMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderConclusionService
{
    public function __construct(
        private readonly JobWorkflowNotificationService $jobWorkflowNotificationService,
        private readonly InvoiceLifecycleService $invoiceLifecycleService,
    ) {
    }

    public static function canConclude(?User $user): bool
    {
        return in_array((string) ($user?->role ?? ''), ['super_admin', 'operations_manager'], true);
    }

    /**
     * Concludes the order: locks it, auto-settles its invoice, and fires the
     * conclusion notification emails. No-op if the order is already concluded.
     */
    public function conclude(Order $order, User $user): void
    {
        if ($order->is_concluded) {
            return;
        }

        $original = $order->getOriginal();
        $now = Carbon::now();

        $order->forceFill([
            'status' => 'Delivered',
            'actual_delivery_at' => $order->actual_delivery_at ?? $now,
            'is_concluded' => true,
            'concluded_by_id' => $user->id,
            'concluded_at' => $now,
            'phase_approval_status' => 'Approved',
            'requested_next_status' => null,
            'phase_approval_comment' => 'Job concluded by '.$user->displayName().'.',
            'phase_approved_by_id' => $user->id,
            'phase_approved_at' => $now,
        ])->save();

        // Auto-settle the invoice when job is concluded
        $order->load('invoice');
        if ($order->invoice && (string) $order->invoice->status !== 'paid') {
            $previousStatus = (string) $order->invoice->status;
            $order->invoice->forceFill([
                'status' => 'paid',
                'paid_at' => $now,
            ])->save();
            $this->invoiceLifecycleService->handleStatusChange($order->invoice->fresh(['order.product']), $previousStatus);
        }

        $freshOrder = $order->fresh(['product', 'designer', 'creatorAdmin', 'concludedBy']);
        $this->jobWorkflowNotificationService->handleOrderUpdated($freshOrder, $original);
        $this->sendConclusionEmails($freshOrder);
    }

    private function sendConclusionEmails(Order $order): void
    {
        $order->loadMissing([
            'product',
            'invoice',
            'briefReceiver',
            'creatorAdmin',
            'designer',
            'productionOfficer',
            'qcOfficer',
            'dispatcher',
            'verifier',
            'concludedBy',
        ]);

        if (filled($order->customer_email)) {
            try {
                Mail::to((string) $order->customer_email)->send(new JobCompletedAppreciationMail($order));
            } catch (\Throwable $exception) {
                Log::error('Client appreciation email failed after job conclusion.', [
                    'order_id' => $order->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $mdRecipients = User::query()
            ->where('role', 'managing_director')
            ->where('is_active', true)
            ->get()
            ->filter(fn (User $recipient): bool => filled($recipient->email));

        foreach ($mdRecipients as $recipient) {
            try {
                Mail::to((string) $recipient->email)->send(new JobConclusionSummaryMail($recipient, $order));
            } catch (\Throwable $exception) {
                Log::error('Managing director conclusion summary email failed.', [
                    'order_id' => $order->id,
                    'recipient_id' => $recipient->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        // Managing directors already receive the detailed summary above —
        // everyone else on staff gets a lightweight "job concluded" notice.
        $staffRecipients = User::query()
            ->where('role', '!=', 'customer')
            ->where('role', '!=', 'managing_director')
            ->where('is_active', true)
            ->get()
            ->filter(fn (User $recipient): bool => filled($recipient->email));

        foreach ($staffRecipients as $recipient) {
            try {
                Mail::to((string) $recipient->email)->send(new JobConcludedStaffNotificationMail($recipient, $order));
            } catch (\Throwable $exception) {
                Log::error('Staff job conclusion notification email failed.', [
                    'order_id' => $order->id,
                    'recipient_id' => $recipient->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
