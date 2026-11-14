<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\OphthalmologySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrewDossierTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(OphthalmologySeeder::class);
    }

    public function test_guest_cannot_access_crew_dossier(): void
    {
        $response = $this->get(route('admin.crew.show', ['code' => 'ASTRO-01']));
        $response->assertRedirect(route('login'));
    }

    public function test_flight_surgeon_can_view_crew_dossier(): void
    {
        $surgeon = User::factory()->create();
        $surgeon->assignRole('flight-surgeon');

        $response = $this->actingAs($surgeon)->get(route('admin.crew.show', ['code' => 'ASTRO-01']));

        $response->assertOk();
        $response->assertSee('CDR Alex Mercer');
        $response->assertSee('Ojo Derecho (OD - Oculus Dexter)');
        $response->assertSee('Ojo Izquierdo (OS - Oculus Sinister)');
        $response->assertSee('Biomarcadores de Fondo de Ojo');
    }
}
