<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(SystemApiKeySeeder::class);

        $defaultPassword = Hash::make('SpaceVision2026!');

        $users = [
            [
                'name' => 'Commander Flight Director',
                'email' => 'admin@spacevision.nasa',
                'password' => $defaultPassword,
                'role' => 'super-admin',
            ],
            [
                'name' => 'Space Vision Operator',
                'email' => 'operator@spacevision.nasa',
                'password' => $defaultPassword,
                'role' => 'admin',
            ],
            [
                'name' => 'Dr. Evelyn Vance (Flight Surgeon)',
                'email' => 'surgeon@spacevision.nasa',
                'password' => $defaultPassword,
                'role' => 'flight-surgeon',
            ],
            [
                'name' => 'Dr. Marcus Chen (SANS Researcher)',
                'email' => 'researcher@spacevision.nasa',
                'password' => $defaultPassword,
                'role' => 'researcher',
            ],
            [
                'name' => 'CDR Alex Mercer (Artemis Crew)',
                'email' => 'astronaut@spacevision.nasa',
                'password' => $defaultPassword,
                'role' => 'astronaut',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, ['email_verified_at' => now()])
            );

            $user->syncRoles([$role]);
        }

        $this->call(OphthalmologySeeder::class);
    }
}
