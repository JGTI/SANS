<?php

namespace Tests\Feature;

use App\Models\AstronautProfile;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisionLabTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_access_vision_lab(): void
    {
        $this->get('/admin/vision-lab')->assertRedirect('/login');
        $this->get('/admin/vision-lab/create')->assertRedirect('/login');
    }

    public function test_flight_surgeon_can_view_vision_lab_index(): void
    {
        $surgeon = User::factory()->create();
        $surgeon->assignRole('flight-surgeon');

        $astronaut = AstronautProfile::create([
            'code' => 'TEST-01',
            'name' => 'Test Astronaut',
            'agency' => 'NASA',
            'mission_name' => 'ISS Test',
            'role' => 'Commander',
            'days_in_microgravity' => 45,
            'status' => 'active_mission',
            'baseline_se_od' => 0.00,
            'baseline_se_os' => 0.00,
            'is_simulated' => true,
        ]);

        $this->actingAs($surgeon)
            ->get('/admin/vision-lab')
            ->assertOk()
            ->assertSee('Vision Lab');
    }

    public function test_flight_surgeon_can_record_vision_evaluation(): void
    {
        $surgeon = User::factory()->create();
        $surgeon->assignRole('flight-surgeon');

        $astronaut = AstronautProfile::create([
            'code' => 'TEST-02',
            'name' => 'Alex Test',
            'agency' => 'NASA',
            'mission_name' => 'Artemis Test',
            'role' => 'Commander',
            'days_in_microgravity' => 60,
            'status' => 'active_mission',
            'baseline_se_od' => 0.00,
            'baseline_se_os' => 0.00,
            'is_simulated' => true,
        ]);

        $payload = [
            'astronaut_id' => $astronaut->id,
            'evaluated_at' => now()->format('Y-m-d H:i:s'),
            'mission_day' => 60,
            'flight_phase' => 'in_flight',
            'eye' => 'OD',
            'sphere' => 1.50,
            'cylinder' => -0.50,
            'axis' => 180,
            'snellen_numerator' => 20,
            'snellen_denominator' => 40,
            'intraocular_pressure' => 17.5,
            'frisen_scale_grade' => 2,
            'posterior_globe_flattening' => 1,
            'choroidal_folds' => 1,
        ];

        $response = $this->actingAs($surgeon)
            ->post('/admin/vision-lab', $payload);

        $response->assertRedirect('/admin/vision-lab?astronaut_id='.$astronaut->id);

        // Verificar que SE se calculó automáticamente: 1.50 + (-0.50 / 2) = 1.25 D
        $this->assertDatabaseHas('vision_evaluations', [
            'astronaut_id' => $astronaut->id,
            'mission_day' => 60,
            'eye' => 'OD',
            'spherical_equivalent' => 1.25,
            'hyperopic_shift' => 1.25,
            'logmar' => 0.301,
        ]);

        // Verificar que la observación SANS se creó con el cálculo de riesgo
        $this->assertDatabaseHas('sans_observations', [
            'astronaut_id' => $astronaut->id,
            'mission_day' => 60,
            'frisen_scale_grade' => 2,
            'posterior_globe_flattening' => 1,
            'choroidal_folds' => 1,
        ]);
    }

    public function test_user_can_export_csv(): void
    {
        $surgeon = User::factory()->create();
        $surgeon->assignRole('flight-surgeon');

        $response = $this->actingAs($surgeon)
            ->get(route('admin.vision-lab.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
