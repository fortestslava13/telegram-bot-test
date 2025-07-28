<?php

namespace App\Notifications\Channels;

use App\Services\TelegramService;
use Illuminate\Notifications\Notification;

class TelegramNotificationChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toTelegram();

        $this->getTelegramService()->sendMessage($notifiable->telegram_id, $message);
    }

    protected function getTelegramService(): TelegramService
    {
        return new TelegramService();
    }
}
