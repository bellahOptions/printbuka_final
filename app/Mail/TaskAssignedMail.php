<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\DailyTodo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAssignedMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public DailyTodo $todo,
        public User $assigner
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('New task assigned: '.$this->todo->task))
            ->view('mail.staff.task-assigned')
            ->with([
                'recipient' => $this->recipient,
                'todo' => $this->todo,
                'assigner' => $this->assigner,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'staff.task_assigned';
    }

    protected function templateVariables(): array
    {
        return [
            'recipient_name' => $this->recipient->displayName(),
            'assigner_name' => $this->assigner->displayName(),
            'task' => (string) $this->todo->task,
        ];
    }
}

