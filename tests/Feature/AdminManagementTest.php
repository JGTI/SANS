<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_access_user_crud_or_maintenance(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
        $this->get('/admin/roles')->assertRedirect('/login');
        $this->get('/admin/maintenance')->assertRedirect('/login');
    }

    public function test_super_admin_can_access_users_roles_and_maintenance(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/roles')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/maintenance')
            ->assertOk()
            ->assertSee('Mantenimiento y Comandos de Misión');
    }

    public function test_super_admin_can_run_artisan_command_via_maintenance(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $response = $this->actingAs($admin)
            ->postJson('/admin/maintenance/run', [
                'action' => 'about',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('command', 'about');
    }

    public function test_maintenance_rejects_unauthorized_commands(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $response = $this->actingAs($admin)
            ->postJson('/admin/maintenance/run', [
                'action' => 'non-existent-or-malicious-command',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}
