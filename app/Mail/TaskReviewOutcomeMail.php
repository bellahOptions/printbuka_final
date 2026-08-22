<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\DailyTodo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskReviewOutcomeMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public DailyTodo $todo,
        public int $rating
    ) {
    }

    public function build(): self
    {
        $outcome = $this->rating === 1 ? 'Warning' : 'Appraisal';

        return $this
            ->subject($this->templateSubject('Task '.$outcome.' ('.$this->rating.'/5): '.$this->todo->task))
            ->view('mail.staff.task-review-outcome')
            ->with([
                'recipient' => $this->recipient,
                'todo' => $this->todo,
                'rating' => $this->rating,
                'outcome' => $outcome,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'staff.task_review_outcome';
    }

    protected function templateVariables(): array
    {
        return [
            'recipient_name' => $this->recipient->displayName(),
            'task' => (string) $this->todo->task,
            'rating' => (string) $this->rating,
        ];
    }
}

