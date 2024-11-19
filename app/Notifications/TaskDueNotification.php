<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;


class TaskDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    Log::info("Notification sent to {$notifiable->email} for task {$this->task->id}");
    return (new MailMessage)
        ->subject('Task Due Soon')
        ->line('The following task is due within the next 24 hours:')
        ->line('**Title:** ' . $this->task->title)
        ->line('**Description:** ' . $this->task->description)
        ->line('**Due Date:** ' . $this->task->due_date->format('Y-m-d H:i:s'))
        ->action('View Task', url('/tasks/' . $this->task->id))
        ->line('Please take action accordingly.');
}

    public function toArray($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'due_date' => $this->task->due_date,
        ];
    }
    
    
    
}

