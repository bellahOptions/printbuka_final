<?php

namespace App\Services;

use App\Support\SiteSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ImportantActionNotifier
{
    public function notify(string $subject, string $message): void
    {
        foreach ($this->recipients() as $recipient) {
            try {
                Mail::raw($message, function ($mail) use ($recipient, $subject): void {
                    $mail->to($recipient)->subject('[Printbuka] '.$subject);
                });
            } catch (\Throwable $exception) {
                Log::error('Important action notification failed.', [
                    'recipient' => $recipient,
                    'subject' => $subject,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }

    /**
     * @return array<int, string>
     */
    private function recipients(): array
    {
        return collect(preg_split('/[\s,;]+/', (string) SiteSettings::get('important_action_notification_emails', '')) ?: [])
            ->map(fn (string $email): string => trim($email))
            ->filter(fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
            ->unique()
            ->values()
            ->all();
    }
}
