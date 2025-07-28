<?php

use Illuminate\Support\Facades\Route;

Route::any('telegram/webhook', \App\Http\Controllers\Telegram\WebhookController::class)
    ->name('telegram.webhook');
