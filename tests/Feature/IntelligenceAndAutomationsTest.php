<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IntelligenceAndAutomationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        Http::fake([
            'osdr.nasa.gov/*' => Http::response([], 500),
            'ntrs.nasa.gov/*' => Http::response([], 500),
            'api.nasa.gov/*' => Http::response([], 500),
        ]);
    }

    public function test_guest_cannot_access_intelligence_or_automations(): void
    {
        $res1 = $this->get(route('admin.research-intelligence'));
        $res1->assertRedirect(route('login'));

        $res2 = $this->get(route('admin.automations'));
        $res2->assertRedirect(route('login'));
    }

    public function test_researcher_can_view_research_intelligence(): void
    {
        $user = User::factory()->create();
        $user->assignRole('researcher');

        $response = $this->actingAs($user)->get(route('admin.research-intelligence'));

        $response->assertOk();
        $response->assertSee('Research Intelligence');
        $response->assertSee('Gradiente de Presión Translaminar');
        $response->assertSee('Polimorfismos Genéticos');
        $response->assertSee('Generador de Citas Científicas');
    }

    public function test_super_admin_can_access_automations_and_trigger_sync(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $response = $this->actingAs($admin)->get(route('admin.automations'));
        $response->assertOk();
        $response->assertSee('Centro de Automatizaciones');
        $response->assertSee('Sincronizador Biológico NASA OSDR');

        $postResponse = $this->actingAs($admin)->post(route('admin.automations.run-sync'));
        $postResponse->assertRedirect(route('admin.automations'));
        $postResponse->assertSessionHas('success');
    }
}
