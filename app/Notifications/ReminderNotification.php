<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderNotification extends Notification
{
    use Queueable;

    protected string $message;

    protected string $title;

    public function __construct(string $message, string $title = 'CareerCraft — Daily Reminder')
    {
        $this->message = $message;
        $this->title = $title;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->message)
            ->line('Log in to CareerCraft and keep making progress.')
            ->action('Open CareerCraft', config('app.url'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
        ];
    }
}
