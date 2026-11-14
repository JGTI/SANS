<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Nexus × NASA Space Apps Challenge')
            ->assertSee('images/nexus-logo.png')
            ->assertSee('images/space-apps-logo.png');

        $this->assertFileExists(public_path('images/nexus-logo.png'));
        $this->assertFileExists(public_path('images/space-apps-logo.png'));
    }
}
