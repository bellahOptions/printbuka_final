<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyStaffActivitySummaryMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    /**
     * @param  array{
     *     total:int,
     *     by_staff:\Illuminate\Support\Collection<int, array{name:string,role:string,department:string,actions:int,entries:\Illuminate\Support\Collection<int, array{time:string,action:string,route:string,subject:string}>}>,
     *     by_route:\Illuminate\Support\Collection<int, array{route:string,count:int}>
     * }  $summary
     */
    public function __construct(
        public User $recipient,
        public CarbonInterface $reportDate,
        public array $summary
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('Daily staff activity summary - '.$this->reportDate->format('M j, Y')))
            ->view('mail.staff.daily-activity-summary')
            ->with([
                'recipient' => $this->recipient,
                'reportDate' => $this->reportDate,
                'summary' => $this->summary,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'staff.daily_activity_summary';
    }

    protected function templateVariables(): array
    {
        return [
            'recipient_name' => $this->recipient->displayName(),
            'report_date' => $this->reportDate->format('l, F j, Y'),
            'total_actions' => number_format($this->summary['total'] ?? 0),
        ];
    }
}
