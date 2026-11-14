<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\OphthalmologySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DemoTheaterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(OphthalmologySeeder::class);

        Http::fake([
            'osdr.nasa.gov/*' => Http::response([], 500),
            'ntrs.nasa.gov/*' => Http::response([], 500),
            'api.nasa.gov/*' => Http::response([], 500),
        ]);
    }

    public function test_guest_cannot_access_demo_theater(): void
    {
        $response = $this->get(route('admin.demo-theater'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_demo_theater(): void
    {
        $user = User::factory()->create();
        $user->assignRole('flight-surgeon');

        $response = $this->actingAs($user)->get(route('admin.demo-theater'));

        $response->assertOk();
        $response->assertSee('Recorrido Guiado de Demostración');
        $response->assertSee('El Desafío: Síndrome Neuroocular Asociado al Vuelo Espacial');
        $response->assertSee('CDR Alex Mercer');
    }

    public function test_authenticated_user_can_view_and_print_flight_report(): void
    {
        $user = User::factory()->create();
        $user->assignRole('researcher');

        $response = $this->actingAs($user)->get(route('admin.demo-theater.report', ['code' => 'ASTRO-01']));

        $response->assertOk();
        $response->assertSee('NASA Flight Medical Evaluation');
        $response->assertSee('Expediente de Evaluación Neurooftalmológica');
        $response->assertSee('CDR Alex Mercer');
        $response->assertSee('Shift Hipermetrópico');
        $response->assertSee('RIESGO ALTO (TIER 3)');
        $response->assertSee('NASA NTRS');
    }
}
