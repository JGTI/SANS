<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\OphthalmologySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionSimulatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(OphthalmologySeeder::class);
    }

    public function test_guest_cannot_access_mission_simulator(): void
    {
        $response = $this->get(route('admin.mission-simulator'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_mission_simulator(): void
    {
        $user = User::factory()->create();
        $user->assignRole('researcher');

        $response = $this->actingAs($user)->get(route('admin.mission-simulator'));

        $response->assertOk();
        $response->assertSee('Simulador de Misiones');
        $response->assertSee('Curva Longitudinal de Shift Hipermetrópico');
        $response->assertSee('AVISO DE MODELADO CIENTÍFICO');
    }

    public function test_can_switch_scenarios_and_seeds(): void
    {
        $user = User::factory()->create();
        $user->assignRole('flight-surgeon');

        $response = $this->actingAs($user)->get(route('admin.mission-simulator', [
            'scenario' => 'scenario-1-stable',
            'seed' => 1234,
        ]));

        $response->assertOk();
        $response->assertSee('1. Control Estable (Normofisiológico)');
    }
}
