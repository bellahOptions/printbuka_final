<?php

namespace App\Mail;

use App\Models\DailyTodo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public DailyTodo $todo,
        public User $assigner
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('New task assigned: '.$this->todo->task)
            ->view('mail.staff.task-assigned')
            ->with([
                'recipient' => $this->recipient,
                'todo' => $this->todo,
                'assigner' => $this->assigner,
            ]);
    }
}

