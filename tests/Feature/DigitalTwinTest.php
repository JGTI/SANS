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

    public function test_public_dual_view_links_all_six_astronauts_and_real_clinical_samples(): void
    {
        $cases = [
            'CASE-PROG-02' => ['name' => 'CDR Alex Mercer', 'sample' => 'OBS-PR02-OD-D30'],
            'CASE-STABLE-01' => ['name' => 'Dr. Sara Lindqvist', 'sample' => 'OBS-ST01-OD-D30'],
            'CASE-NOSCAN-03A' => ['name' => 'Kenji Sato', 'sample' => 'OBS-NS03A-IOP-ONLY'],
            'CASE-ASO-05' => ['name' => 'Maj. Elena Rostova', 'sample' => 'OBS-ASO05-OD-D30'],
            'CASE-NORECENT-03B' => ['name' => 'Dr. Marcus Thorne', 'sample' => 'VISIT-NR03B-D30-MISSING'],
            'CASE-TILT-04' => ['name' => 'Capt. Mateo Vargas', 'sample' => 'OBS-TILT04-EXP1-OD-FIRST-POST'],
        ];

        foreach ($cases as $caseId => $expected) {
            $response = $this->get(route('public.digital-twin', ['mode' => 'dual', 'case_id' => $caseId]));
            $response->assertOk();
            $response->assertSee($expected['name']);
            $response->assertSee($caseId);
            $response->assertSee($expected['sample']);
        }
    }
}
