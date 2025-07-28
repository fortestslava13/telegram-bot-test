<?php

namespace Tests\Unit\Notifications\Notifications;

use App\Notifications\Channels\TelegramNotificationChannel;
use App\Notifications\Notifications\TelegramNotification;
use Tests\TestCase;

class TelegramNotificationTest extends TestCase
{
    /**
     * Test that the notification uses the correct channel.
     */
    public function test_notification_uses_telegram_channel(): void
    {
        // Arrange
        $message = 'Test message';
        $notification = new TelegramNotification($message);
        $notifiable = new \stdClass();

        // Act
        $channel = $notification->via($notifiable);

        // Assert
        $this->assertEquals(TelegramNotificationChannel::class, $channel);
    }

    /**
     * Test that the notification returns the correct message.
     */
    public function test_notification_returns_correct_message(): void
    {
        // Arrange
        $message = 'Test message';
        $notification = new TelegramNotification($message);

        // Act
        $result = $notification->toTelegram();

        // Assert
        $this->assertEquals($message, $result);
    }
}
