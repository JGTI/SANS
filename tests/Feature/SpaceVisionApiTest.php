<?php

namespace Tests\Feature;

use Database\Seeders\OphthalmologySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SpaceVisionApiTest extends TestCase
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

    public function test_api_returns_missions(): void
    {
        $response = $this->getJson('/api/space-vision/v1/missions');

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure(['data' => [['mission', 'agency', 'crew_count']]]);
    }

    public function test_api_returns_crew(): void
    {
        $response = $this->getJson('/api/space-vision/v1/crew');

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $response->assertJsonFragment(['code' => 'ASTRO-01']);
    }

    public function test_api_returns_ocular_exams(): void
    {
        $response = $this->getJson('/api/space-vision/v1/ocular-exams?astronaut_code=ASTRO-01');

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure(['data' => [['mission_day', 'eye', 'refraction']]]);
    }

    public function test_api_returns_simulation_scenarios(): void
    {
        $response = $this->getJson('/api/space-vision/v1/simulator/scenarios');

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $response->assertJsonFragment(['id' => 'scenario-3-sans-critical']);
    }

    public function test_api_evaluates_calculator(): void
    {
        $payload = [
            'sphere' => 1.50,
            'cylinder' => -0.50,
            'axis' => 90,
            'snellen_fraction' => '20/25',
            'baseline_se' => 0.00,
            'frisen_grade' => 2,
            'globe_flattening' => true,
            'choroidal_folds' => true,
        ];

        $response = $this->postJson('/api/space-vision/v1/calculator/evaluate', $payload);

        $response->assertOk();
        $response->assertJsonPath('results.spherical_equivalent', 1.25);
        $response->assertJsonPath('results.hyperopic_shift', 1.25);
        $response->assertJsonPath('results.sans_risk.tier', 'severe');
    }

    public function test_api_returns_nasa_sources(): void
    {
        $osdrResponse = $this->getJson('/api/space-vision/v1/nasa/osdr');
        $osdrResponse->assertOk();
        $osdrResponse->assertJsonFragment(['accession' => 'OSD-137']);

        $ntrsResponse = $this->getJson('/api/space-vision/v1/nasa/ntrs');
        $ntrsResponse->assertOk();
        $ntrsResponse->assertJsonFragment(['id' => '20200002341']);

        $donkiResponse = $this->getJson('/api/space-vision/v1/nasa/donki');
        $donkiResponse->assertOk();
        $donkiResponse->assertJsonStructure(['data' => [['messageType', 'messageID']]]);
    }
}
