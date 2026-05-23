<?php

namespace App\Mail;

use App\Models\DailyTodo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskReviewOutcomeMail extends Mailable
{
    use Queueable, SerializesModels;

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
            ->subject('Task '.$outcome.' ('.$this->rating.'/5): '.$this->todo->task)
            ->view('mail.staff.task-review-outcome')
            ->with([
                'recipient' => $this->recipient,
                'todo' => $this->todo,
                'rating' => $this->rating,
                'outcome' => $outcome,
            ]);
    }
}

