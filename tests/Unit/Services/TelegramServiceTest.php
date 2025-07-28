<?php

namespace Tests\Unit\Services;

use App\Dto\Telegram\CommandDto;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramServiceTest extends TestCase
{
    use RefreshDatabase;
    protected TelegramService $telegramService;
    protected string $apiUrl = 'https://api.telegram.org/';
    protected string $token = 'test_token';
    protected string $webhookSecret = 'test_secret';

    protected function setUp(): void
    {
        parent::setUp();

        // Mock config values
        Config::set('services.telegram.api_url', $this->apiUrl);
        Config::set('services.telegram.token', $this->token);
        Config::set('services.telegram.webhook_secret', $this->webhookSecret);

        $this->telegramService = new TelegramService();

        // Mock HTTP facade
        Http::fake();
    }

    /**
     * Test that sendMessage method sends correct request to Telegram API.
     */
    public function test_send_message(): void
    {
        // Arrange
        $chatId = '123456789';
        $message = 'Test message';

        // Act
        $this->telegramService->sendMessage($chatId, $message);

        // Assert
        Http::assertSent(function ($request) use ($chatId, $message) {
            return $request->url() === $this->apiUrl . 'bot' . $this->token . '/sendMessage' &&
                   $request['chat_id'] === $chatId &&
                   $request['text'] === $message;
        });
    }

    /**
     * Test that setWebhook method sends correct request to Telegram API.
     */
    public function test_set_webhook(): void
    {
        // Arrange
        $url = 'https://example.com/webhook';

        // Act
        $this->telegramService->setWebhook($url);

        // Assert
        Http::assertSent(function ($request) use ($url) {
            return $request->url() === $this->apiUrl . 'bot' . $this->token . '/setWebhook' &&
                   $request['url'] === $url &&
                   $request['drop_pending_updates'] === true &&
                   $request['secret_token'] === $this->webhookSecret;
        });
    }

    /**
     * Test that handleWebhook method creates a new user if one doesn't exist.
     */
    public function test_handle_webhook_creates_new_user(): void
    {
        // Arrange
        $telegramId = '123456789';
        $username = 'test_user';
        $command = '/start';

        $commandDto = new CommandDto(
            telegramId: $telegramId,
            username: $username,
            command: $command
        );

        // Act
        $this->telegramService->handleWebhook($commandDto);

        // Assert
        $this->assertDatabaseHas('users', [
            'telegram_id' => $telegramId,
            'name' => $username,
            'subscribed' => true
        ]);

        // Verify message was sent
        Http::assertSent(function ($request) use ($telegramId) {
            return $request->url() === $this->apiUrl . 'bot' . $this->token . '/sendMessage' &&
                   $request['chat_id'] === $telegramId &&
                   $request['text'] === 'subscribed';
        });
    }

    /**
     * Test that handleWebhook method updates an existing user.
     */
    public function test_handle_webhook_updates_existing_user(): void
    {
        // Arrange
        $telegramId = '123456789';
        $username = 'test_user';
        $command = '/stop';

        // Create user
        User::factory()->create([
            'telegram_id' => $telegramId,
            'name' => $username,
            'subscribed' => true
        ]);

        $commandDto = new CommandDto(
            telegramId: $telegramId,
            username: $username,
            command: $command
        );

        // Act
        $this->telegramService->handleWebhook($commandDto);

        // Assert
        $this->assertDatabaseHas('users', [
            'telegram_id' => $telegramId,
            'name' => $username,
            'subscribed' => false
        ]);

        // Verify message was sent
        Http::assertSent(function ($request) use ($telegramId) {
            return $request->url() === $this->apiUrl . 'bot' . $this->token . '/sendMessage' &&
                   $request['chat_id'] === $telegramId &&
                   $request['text'] === 'unsubscribed';
        });
    }
}
