<?php

namespace App\Dto\Telegram;

readonly class CommandDto
{
    public function __construct(
        public string $telegramId,
        public string $username,
        public string $command,
    )
    {
    }

}
