<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SystemApiKey;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SystemApiKeySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiKeyArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(SystemApiKeySeeder::class);

        $this->adminUser = User::factory()->create([
            'email' => 'admin@spacevision.nasa',
            'name' => 'Commander Flight Director',
        ]);
        $this->adminUser->assignRole('super-admin');
    }

    public function test_guest_cannot_access_api_keys(): void
    {
        $this->get('/admin/api-keys')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_api_keys_and_architecture_diagram(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/api-keys');
        $response->assertStatus(200);
        $response->assertSee('Gestión de Claves de APIs');
        $response->assertSee('Diagrama de Arquitectura de SPACE VISION');
        $response->assertSee('NASA_API_KEY');
        $response->assertSee('SCIENTIFIC_ENGINE_TOKEN');
    }

    public function test_super_admin_can_create_new_api_key(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/api-keys', [
            'key_name' => 'TEST_TELEMETRY_KEY',
            'service_label' => 'Test Telemetry Gateway',
            'key_value' => 'secret-test-token-12345',
            'category' => 'Aeroespacial & Telemetría',
            'endpoint_url' => 'https://api.test-gateway.nasa/v1',
            'description' => 'Gateway de prueba para recepción de telemetría orbital.',
            'status' => 'ACTIVE',
            'is_secret' => 1,
        ]);

        $response->assertRedirect('/admin/api-keys');
        $this->assertDatabaseHas('system_api_keys', [
            'key_name' => 'TEST_TELEMETRY_KEY',
            'service_label' => 'Test Telemetry Gateway',
        ]);
    }

    public function test_super_admin_can_update_existing_api_key(): void
    {
        $key = SystemApiKey::where('key_name', 'NASA_API_KEY')->firstOrFail();

        $response = $this->actingAs($this->adminUser)->put("/admin/api-keys/{$key->id}", [
            'service_label' => 'NASA Open Data & Telemetry Updated',
            'key_value' => 'NEW_NASA_KEY_12345',
            'category' => 'Aeroespacial & Telemetría',
            'endpoint_url' => 'https://api.nasa.gov/',
            'description' => 'Descripción actualizada de NASA API.',
            'status' => 'ACTIVE',
            'is_secret' => 1,
        ]);

        $response->assertRedirect('/admin/api-keys');
        $this->assertDatabaseHas('system_api_keys', [
            'id' => $key->id,
            'key_value' => 'NEW_NASA_KEY_12345',
            'service_label' => 'NASA Open Data & Telemetry Updated',
        ]);
    }

    public function test_core_keys_cannot_be_deleted(): void
    {
        $nasaKey = SystemApiKey::where('key_name', 'NASA_API_KEY')->firstOrFail();

        $response = $this->actingAs($this->adminUser)->delete("/admin/api-keys/{$nasaKey->id}");
        $response->assertRedirect('/admin/api-keys');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('system_api_keys', ['id' => $nasaKey->id]);
    }

    public function test_custom_key_can_be_deleted(): void
    {
        $custom = SystemApiKey::create([
            'key_name' => 'TEMP_DELETABLE_KEY',
            'service_label' => 'Temporary Key',
            'key_value' => 'temp-123',
            'category' => 'General',
            'status' => 'OPTIONAL',
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/api-keys/{$custom->id}");
        $response->assertRedirect('/admin/api-keys');

        $this->assertDatabaseMissing('system_api_keys', ['id' => $custom->id]);
    }

    public function test_sync_to_env_action(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/api-keys/sync-env');
        $response->assertRedirect('/admin/api-keys');
        $response->assertSessionHas('status');
    }

    public function test_ping_test_endpoint(): void
    {
        $tokenKey = SystemApiKey::where('key_name', 'SCIENTIFIC_ENGINE_TOKEN')->firstOrFail();

        $response = $this->actingAs($this->adminUser)->postJson("/admin/api-keys/{$tokenKey->id}/ping");
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'latency_ms', 'message']);
    }
}
