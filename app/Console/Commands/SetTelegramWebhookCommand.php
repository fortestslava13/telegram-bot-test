<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetTelegramWebhookCommand extends Command
{
    protected $signature = 'set-telegram-webhook';

    protected $description = 'Set webhook url for telegram bot';

    public function handle(TelegramService $service)
    {
        $public_url = Http::get('127.0.0.1:4040/api/tunnels')->json('tunnels.0.public_url').'/api/telegram/webhook';
        $service->setWebhook($public_url);
    }
}
