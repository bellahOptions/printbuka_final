<?php

namespace App\Services;

use App\Mail\DailyStaffActivitySummaryMail;
use App\Models\StaffActivity;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StaffActivitySummaryService
{
    public function sendDailySummary(?CarbonInterface $reportDate = null): int
    {
        $timezone = (string) config('app.business_timezone', 'Africa/Lagos');
        $day = ($reportDate ? $reportDate->copy() : now($timezone))->setTimezone($timezone);
        $startUtc = $day->copy()->startOfDay()->setTimezone('UTC');
        $endUtc = $day->copy()->endOfDay()->setTimezone('UTC');

        $activities = StaffActivity::query()
            ->with('user')
            ->whereBetween('created_at', [$startUtc, $endUtc])
            ->orderBy('created_at')
            ->get();

        $summary = $this->buildSummary($activities, $timezone);

        $hrRecipients = User::query()
            ->where('role', 'hr')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get();

        $emailsSent = 0;

        foreach ($hrRecipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new DailyStaffActivitySummaryMail($recipient, $day, $summary));
                $emailsSent++;
            } catch (\Throwable $exception) {
                Log::error('Failed to send daily staff activity summary.', [
                    'recipient_id' => $recipient->id,
                    'recipient_email' => $recipient->email,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $emailsSent;
    }

    /**
     * @return array{
     *     total:int,
     *     by_staff:\Illuminate\Support\Collection<int, array{name:string,role:string,department:string,actions:int,entries:\Illuminate\Support\Collection<int, array{time:string,action:string,route:string,subject:string}>}>,
     *     by_route:\Illuminate\Support\Collection<int, array{route:string,count:int}>
     * }
     */
    private function buildSummary(Collection $activities, string $timezone): array
    {
        $byStaff = $activities
            ->groupBy(fn (StaffActivity $activity): string => (string) ($activity->user_id ?? 'unknown'))
            ->map(function (Collection $group) use ($timezone): array {
                $first = $group->first();
                $user = $first?->user;
                $roleLabel = (string) config('printbuka_admin.role_labels.'.($first?->role ?? ''), $first?->role ?? 'Unknown');

                return [
                    'name' => $user?->displayName() ?? 'Unknown staff',
                    'role' => $roleLabel,
                    'department' => (string) ($first?->department ?: 'Unassigned'),
                    'actions' => $group->count(),
                    'entries' => $group->map(function (StaffActivity $activity) use ($timezone): array {
                        $subject = $activity->subject_type && $activity->subject_id
                            ? $activity->subject_type.' #'.$activity->subject_id
                            : 'N/A';

                        return [
                            'time' => $activity->created_at?->copy()->setTimezone($timezone)->format('H:i') ?? '--:--',
                            'action' => $activity->action,
                            'route' => (string) ($activity->route_name ?: 'N/A'),
                            'subject' => $subject,
                        ];
                    })->values(),
                ];
            })
            ->sortByDesc('actions')
            ->values();

        $byRoute = $activities
            ->groupBy(fn (StaffActivity $activity): string => (string) ($activity->route_name ?: 'N/A'))
            ->map(fn (Collection $group, string $route): array => [
                'route' => $route,
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->values();

        return [
            'total' => $activities->count(),
            'by_staff' => $byStaff,
            'by_route' => $byRoute,
        ];
    }
}
