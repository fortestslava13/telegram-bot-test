<?php

namespace Tests\Feature;

use Tests\TestCase;

class WebRoutesTest extends TestCase
{
    /**
     * Test that the root URL returns a successful response.
     */
    public function test_root_url_returns_successful_response(): void
    {
        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);
    }
}
