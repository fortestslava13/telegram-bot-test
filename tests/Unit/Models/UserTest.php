<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the user factory works correctly.
     */
    public function test_user_factory(): void
    {
        // Act
        $user = User::factory()->create([
            'telegram_id' => '123456789',
            'name' => 'Test User',
            'subscribed' => true,
        ]);

        // Assert
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'telegram_id' => '123456789',
            'name' => 'Test User',
            'subscribed' => true,
        ]);
    }

    /**
     * Test that the subscribed scope returns only subscribed users.
     */
    public function test_subscribed_scope(): void
    {
        // Arrange
        User::factory()->create([
            'telegram_id' => '111111111',
            'name' => 'Subscribed User 1',
            'subscribed' => true,
        ]);

        User::factory()->create([
            'telegram_id' => '222222222',
            'name' => 'Subscribed User 2',
            'subscribed' => true,
        ]);

        User::factory()->create([
            'telegram_id' => '333333333',
            'name' => 'Unsubscribed User',
            'subscribed' => false,
        ]);

        // Act
        $subscribedUsers = User::subscribed()->get();

        // Assert
        $this->assertCount(2, $subscribedUsers);
        $this->assertTrue($subscribedUsers->every(fn ($user) => $user->subscribed));
    }
}
