<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\ScientificEngineService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdvancedAiSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create([
            'email' => 'admin@spacevision.nasa',
            'name' => 'Commander Flight Director',
        ]);
        $this->adminUser->assignRole('super-admin');
    }

    public function test_guest_cannot_access_advanced_ai_suite(): void
    {
        $routes = [
            '/admin/ai-command',
            '/admin/model-lab',
            '/admin/explainability',
            '/admin/data-quality',
            '/admin/model-registry',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_authenticated_user_can_view_ai_command_center(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/ai-command');
        $response->assertStatus(200);
        $response->assertSee('AI Command Center');
        $response->assertSee('Stack Científico');
    }

    public function test_authenticated_user_can_view_model_laboratory(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/model-lab?n_astronauts=30&n_splits=3&seed=42');
        $response->assertStatus(200);
        $response->assertSee('Model Laboratory');
        $response->assertSee('Random Forest Classifier');
        $response->assertSee('Gradient Boosting Classifier');
    }

    public function test_authenticated_user_can_export_model_benchmark(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/model-lab/export?n_astronauts=20&n_splits=2&seed=42');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonStructure([
            'project',
            'module',
            'exported_at',
            'exported_by',
            'cohort_parameters',
            'benchmark_results',
            'scientific_disclaimer',
        ]);
    }

    public function test_authenticated_user_can_view_explainability_center(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/explainability?hyperopic_shift=1.50&rnfl_delta_microns=25.0');
        $response->assertStatus(200);
        $response->assertSee('AI Explainability Center');
        $response->assertSee('Desplazamiento Hipermetrópico');
    }

    public function test_authenticated_user_can_view_data_quality_observatory(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/data-quality');
        $response->assertStatus(200);
        $response->assertSee('Data Quality Observatory');
        $response->assertSee('Auditoría de Límites Fisiológicos');
    }

    public function test_authenticated_user_can_view_model_registry(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/model-registry');
        $response->assertStatus(200);
        $response->assertSee('Model Registry');
        $response->assertSee('SANS-Risk-Stratifier-Deterministic');
        $response->assertSee('RandomForest-SANS-Classifier');
    }

    public function test_scientific_engine_service_evaluation(): void
    {
        $service = app(ScientificEngineService::class);
        $result = $service->evaluateOphthalmology([
            'sphere' => 1.50,
            'cylinder' => -0.50,
            'axis' => 90.0,
            'snellen_numerator' => 20,
            'snellen_denominator' => 20,
            'baseline_sphere' => 0.0,
            'baseline_cylinder' => -0.50,
            'frisen_grade' => 1,
            'rnfl_thickness' => 110.0,
            'flight_day' => 45,
        ]);

        $this->assertEquals(1.25, $result['spherical_equivalent']);
        $this->assertArrayHasKey('thibos_vectors', $result);
        $this->assertArrayHasKey('sans_risk', $result);
    }

    public function test_scientific_engine_artisan_status(): void
    {
        $this->artisan('scientific:engine', ['action' => 'status'])
            ->assertExitCode(0);
    }
}
