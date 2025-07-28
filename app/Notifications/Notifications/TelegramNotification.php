<?php

namespace App\Notifications\Notifications;

use App\Notifications\Channels\TelegramNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TelegramNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param string $message
     */
    public function __construct(private readonly string $message)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): string
    {
        return TelegramNotificationChannel::class;
    }

    public function  toTelegram(): string
    {
        return $this->message;
    }
}
