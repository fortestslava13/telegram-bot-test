<?php

namespace App\Services;

use App\Dto\Telegram\CommandDto;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    private $api_url;

    public function __construct()
    {
        $this->api_url = config('services.telegram.api_url');
    }

    /**
     * @param CommandDto $commandDto
     * @return void
     */
    public function handleWebhook(CommandDto $commandDto): void
    {
        $user = User::where('telegram_id', $commandDto->telegramId)->first();

        if (! $user) {
            $user = User::create([
                'name' => $commandDto->username,
                'telegram_id' => $commandDto->telegramId,
            ]);
        }

        $method ='command' . ucfirst(trim($commandDto->command, '/'));

        if (method_exists($this, $method)) {
            $this->$method($user);
        }
    }

    /**
     * @param User $user
     * @return void
     */
    public function commandStart(User $user): void
    {
        $user->update(['subscribed' => true]);
        $this->sendMessage($user->telegram_id, 'subscribed');
    }

    /**
     * @param User $user
     * @return void
     */
    public function commandStop(User $user): void
    {
        $user->update(['subscribed' => false]);
        $this->sendMessage($user->telegram_id, 'unsubscribed');
    }

    /**
     * @param string $chat_id
     * @param string $text
     * @return void
     */
    public function sendMessage(string $chat_id, string $text): void
    {
        Http::post($this->getEndpoint('sendMessage'), [
            'chat_id' => $chat_id,
            'text' => $text,
        ]);
    }

    public function setWebhook(string $url): void
    {
        Http::post($this->getEndpoint('setWebhook'), [
            'url' => $url,
            'drop_pending_updates' => true,
            'secret_token' => config('services.telegram.webhook_secret'),
        ]);
    }

    /**
     * @param string $method
     * @return string
     */
    private function getEndpoint(string $method): string
    {
        return $this->api_url.'bot'.config('services.telegram.token').'/'.$method;
    }
}
