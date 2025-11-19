<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Roles and Permissions first
        $this->call(RolePermissionSeeder::class);

        // Create Super Admin User
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@advancedcrm.com',
            'password' => bcrypt('password'), // Change this in production!
            'is_active' => true,
        ]);
        $superAdmin->assignRole('Super Admin');

        // Create Test Admin User
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $admin->assignRole('Admin');

        // Create Test Manager User
        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $manager->assignRole('Manager');

        // Create Test Sales Rep User
        $salesRep = User::factory()->create([
            'name' => 'Sales Rep User',
            'email' => 'sales@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $salesRep->assignRole('Sales Rep');

        $this->command->info('Test users created with default password: password');
        $this->command->warn('Please change passwords in production!');
    }
}
