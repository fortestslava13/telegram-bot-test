<?php

namespace Tests\Feature\Telegram;

use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    protected string $webhookSecret = 'test_webhook_secret';

    protected function setUp(): void
    {
        parent::setUp();

        // Set webhook secret for testing
        Config::set('services.telegram.webhook_secret', $this->webhookSecret);
    }

    /**
     * Test that the webhook endpoint returns 401 if the secret token is missing.
     */
    public function test_webhook_requires_secret_token(): void
    {
        // Act
        $response = $this->postJson('/api/telegram/webhook', [
            'message' => [
                'chat' => [
                    'id' => 123456789,
                    'username' => 'test_user',
                ],
                'text' => '/start',
            ],
        ]);

        // Assert
        $response->assertStatus(403);
    }

    /**
     * Test that the webhook endpoint returns 401 if the secret token is incorrect.
     */
    public function test_webhook_requires_correct_secret_token(): void
    {
        // Act
        $response = $this->postJson('/api/telegram/webhook', [
            'message' => [
                'chat' => [
                    'id' => 123456789,
                    'username' => 'test_user',
                ],
                'text' => '/start',
            ],
        ], [
            'X-Telegram-Bot-Api-Secret-Token' => 'wrong_secret',
        ]);

        // Assert
        $response->assertStatus(403);
    }

    /**
     * Test that the webhook endpoint returns 422 if the request data is invalid.
     */
    public function test_webhook_validates_request_data(): void
    {
        // Act
        $response = $this->postJson('/api/telegram/webhook', [
            // Missing required fields
        ], [
            'X-Telegram-Bot-Api-Secret-Token' => $this->webhookSecret,
        ]);

        // Assert
        $response->assertUnprocessable();
    }

    /**
     * Test that the webhook endpoint processes valid start command.
     */
    public function test_webhook_processes_start_command(): void
    {
        // Arrange
        // Mock the TelegramService to verify handleWebhook is called
        $this->mock(TelegramService::class, function (MockInterface $mock) {
            $mock->shouldReceive('handleWebhook')
                ->once()
                ->withArgs(function ($commandDto) {
                    return $commandDto->telegramId === '123456789' &&
                           $commandDto->username === 'test_user' &&
                           $commandDto->command === '/start';
                });
        });

        // Act
        $response = $this->postJson('/api/telegram/webhook', [
            'message' => [
                'chat' => [
                    'id' => 123456789,
                    'username' => 'test_user',
                ],
                'text' => '/start',
            ],
        ], [
            'X-Telegram-Bot-Api-Secret-Token' => $this->webhookSecret,
        ]);

        // Assert
        $response->assertNoContent();
    }

    /**
     * Test that the webhook endpoint processes valid stop command.
     */
    public function test_webhook_processes_stop_command(): void
    {
        // Arrange
        // Mock the TelegramService to verify handleWebhook is called
        $this->mock(TelegramService::class, function (MockInterface $mock) {
            $mock->shouldReceive('handleWebhook')
                ->once()
                ->withArgs(function ($commandDto) {
                    return $commandDto->telegramId === '123456789' &&
                           $commandDto->username === 'test_user' &&
                           $commandDto->command === '/stop';
                });
        });

        // Act
        $response = $this->postJson('/api/telegram/webhook', [
            'message' => [
                'chat' => [
                    'id' => 123456789,
                    'username' => 'test_user',
                ],
                'text' => '/stop',
            ],
        ], [
            'X-Telegram-Bot-Api-Secret-Token' => $this->webhookSecret,
        ]);

        // Assert
        $response->assertNoContent();
    }
}
