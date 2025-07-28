<?php

namespace Tests\Unit\Notifications\Channels;

use App\Notifications\Channels\TelegramNotificationChannel;
use App\Services\TelegramService;
use Illuminate\Notifications\Notification;
use Mockery;
use PHPUnit\Framework\TestCase;

class TelegramNotificationChannelTest extends TestCase
{
    /**
     * Test that the channel sends a message via the TelegramService.
     */
    public function test_send_notification(): void
    {
        // Arrange
        $notifiable = new class {
            public $telegram_id = '123456789';
        };

        $message = 'Test notification message';

        $notification = Mockery::mock(Notification::class);
        $notification->shouldReceive('toTelegram')
            ->once()
            ->andReturn($message);

        $telegramService = Mockery::mock(TelegramService::class);
        $telegramService->shouldReceive('sendMessage')
            ->once()
            ->with($notifiable->telegram_id, $message);

        // Use Mockery to replace the TelegramService instantiation
        $channel = new class($telegramService) extends TelegramNotificationChannel {
            private $telegramService;

            public function __construct($telegramService)
            {
                $this->telegramService = $telegramService;
            }

            protected function getTelegramService(): \App\Services\TelegramService
            {
                return $this->telegramService;
            }
        };

        // Act
        $channel->send($notifiable, $notification);

        // Assert
        $this->assertTrue(true, 'The notification was sent successfully');
        // Additional assertions are handled by the mock expectations
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
