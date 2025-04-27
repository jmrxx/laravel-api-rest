<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        foreach (['superadmin', 'admin', 'user'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Create superadmin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'username' => 'admin',
                'lastname' => 'admin',
                'perfil_picture' => null,
                'biography' => 'Admin user by seeder',
                'password' => Hash::make('admin123'),
            ]
        );

        // Assign role if not already assigned
        if (!$admin->hasRole('superadmin')) {
            $admin->assignRole('superadmin');
        }
    }
}