<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NasaObservatoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        // Http::fake para pruebas ultrarrápidas y deterministas
        Http::fake([
            'osdr.nasa.gov/*' => Http::response([], 500), // Prueba tolerancia y fallback local
            'ntrs.nasa.gov/*' => Http::response([], 500),
            'api.nasa.gov/DONKI/*' => Http::response([], 500),
            'api.nasa.gov/planetary/*' => Http::response([], 500),
        ]);
    }

    public function test_guest_cannot_access_nasa_observatory(): void
    {
        $response = $this->get(route('admin.nasa-data'));
        $response->assertRedirect(route('login'));
    }

    public function test_researcher_can_view_nasa_observatory(): void
    {
        $user = User::factory()->create();
        $user->assignRole('researcher');

        $response = $this->actingAs($user)->get(route('admin.nasa-data'));

        $response->assertOk();
        $response->assertSee('NASA Data Observatory');
        $response->assertSee('NASA OSDR');
        $response->assertSee('OSD-137');
    }

    public function test_can_filter_ntrs_publications_and_donki(): void
    {
        $user = User::factory()->create();
        $user->assignRole('flight-surgeon');

        $ntrsResponse = $this->actingAs($user)->get(route('admin.nasa-data', ['tab' => 'ntrs']));
        $ntrsResponse->assertOk();
        $ntrsResponse->assertSee('NASA Technical Reports Server');
        $ntrsResponse->assertSee('Stenger');

        $donkiResponse = $this->actingAs($user)->get(route('admin.nasa-data', ['tab' => 'donki']));
        $donkiResponse->assertOk();
        $donkiResponse->assertSee('Telemetría de Clima Espacial');
        $donkiResponse->assertSee('DECLARACIÓN DE RESPONSABILIDAD CIENTÍFICA');
    }

    public function test_api_health_diagnostics_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $response = $this->actingAs($user)->get(route('admin.nasa-data', ['tab' => 'health']));
        $response->assertOk();
        $response->assertSee('Diagnóstico de Integraciones NASA');
        $response->assertSee('NASA Open Science Data Repository (OSDR)');
    }
}
