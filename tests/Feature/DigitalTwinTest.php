<?php

namespace Tests\Feature;

use App\Models\AstronautProfile;
use App\Models\User;
use Database\Seeders\OphthalmologySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DigitalTwinTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(OphthalmologySeeder::class);
    }

    public function test_guest_cannot_view_digital_twin(): void
    {
        $response = $this->get(route('admin.digital-twin'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_digital_twin(): void
    {
        $user = User::factory()->create();
        $user->assignRole('flight-surgeon');

        $response = $this->actingAs($user)->get(route('admin.digital-twin'));

        $response->assertOk();
        $response->assertSee('Astronaut Digital Twin');
        $response->assertSee('CDR Alex Mercer');
        $response->assertSee('Gemelo Digital Neurooftalmológico');
        $response->assertSee('Corte Sagital Anatómico');
    }

    public function test_digital_twin_can_switch_astronaut(): void
    {
        $user = User::factory()->create();
        $user->assignRole('researcher');

        $response = $this->actingAs($user)->get(route('admin.digital-twin', ['astro' => 'ASTRO-02']));

        $response->assertOk();
        $response->assertSee('Dr. Sara Lindqvist');
    }

    public function test_oct_telemetry_endpoint(): void
    {
        $user = User::factory()->create();
        $user->assignRole('flight-surgeon');

        $response = $this->actingAs($user)->getJson(route('admin.digital-twin.oct-telemetry', [
            'code' => 'ASTRO-01',
            'name' => 'CDR Alex Mercer',
            'day' => 45,
            'scenario' => 'B'
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'workstation_model',
            'protocol',
            'summary_table' => [
                'central_subfield_um',
                'volume_cube_mm3',
                'avg_thickness_cube_um',
            ],
            'etdrs_grid' => [
                'od',
                'os',
            ],
            'biomarkers',
        ]);
    }

    public function test_authenticated_user_can_view_oct_analysis_workstation(): void
    {
        $user = User::factory()->create();
        $user->assignRole('flight-surgeon');

        $response = $this->actingAs($user)->get(route('admin.oct-analysis'));

        $response->assertOk();
        $response->assertSee('Cirrus™ HD-OCT');
        $response->assertSee('SPECTRAL DOMAIN TECHNOLOGY');
        $response->assertSee('Macular Cube 512x128');
    }
}
