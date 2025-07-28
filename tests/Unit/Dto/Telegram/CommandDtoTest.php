<?php

namespace Tests\Unit\Dto\Telegram;

use App\Dto\Telegram\CommandDto;
use PHPUnit\Framework\TestCase;

class CommandDtoTest extends TestCase
{
    /**
     * Test that CommandDto correctly stores and returns values.
     */
    public function test_command_dto_stores_values(): void
    {
        // Arrange
        $telegramId = '123456789';
        $username = 'test_user';
        $command = '/start';

        // Act
        $dto = new CommandDto(
            telegramId: $telegramId,
            username: $username,
            command: $command
        );

        // Assert
        $this->assertEquals($telegramId, $dto->telegramId);
        $this->assertEquals($username, $dto->username);
        $this->assertEquals($command, $dto->command);
    }
}
