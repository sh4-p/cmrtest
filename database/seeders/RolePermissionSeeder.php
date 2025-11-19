<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // User Management
            'manage-users',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Leads
            'view-leads',
            'view-all-leads',
            'create-leads',
            'edit-leads',
            'edit-all-leads',
            'delete-leads',
            'delete-all-leads',
            'assign-leads',
            'convert-leads',

            // Contacts
            'view-contacts',
            'view-all-contacts',
            'create-contacts',
            'edit-contacts',
            'edit-all-contacts',
            'delete-contacts',
            'delete-all-contacts',

            // Companies
            'view-companies',
            'view-all-companies',
            'create-companies',
            'edit-companies',
            'edit-all-companies',
            'delete-companies',
            'delete-all-companies',

            // Deals
            'view-deals',
            'view-all-deals',
            'create-deals',
            'edit-deals',
            'edit-all-deals',
            'delete-deals',
            'delete-all-deals',
            'manage-deal-stages',

            // Tasks
            'view-tasks',
            'view-all-tasks',
            'create-tasks',
            'edit-tasks',
            'edit-all-tasks',
            'delete-tasks',
            'delete-all-tasks',

            // Activities
            'view-activities',
            'view-all-activities',
            'create-activities',
            'delete-activities',

            // Reports
            'view-reports',
            'view-all-reports',
            'export-reports',

            // Settings
            'manage-settings',
            'view-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // Super Admin - All permissions
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - All except user management
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view-users',
            // Leads - All
            'view-leads', 'view-all-leads', 'create-leads', 'edit-leads', 'edit-all-leads',
            'delete-leads', 'delete-all-leads', 'assign-leads', 'convert-leads',
            // Contacts - All
            'view-contacts', 'view-all-contacts', 'create-contacts', 'edit-contacts',
            'edit-all-contacts', 'delete-contacts', 'delete-all-contacts',
            // Companies - All
            'view-companies', 'view-all-companies', 'create-companies', 'edit-companies',
            'edit-all-companies', 'delete-companies', 'delete-all-companies',
            // Deals - All
            'view-deals', 'view-all-deals', 'create-deals', 'edit-deals', 'edit-all-deals',
            'delete-deals', 'delete-all-deals', 'manage-deal-stages',
            // Tasks - All
            'view-tasks', 'view-all-tasks', 'create-tasks', 'edit-tasks', 'edit-all-tasks',
            'delete-tasks', 'delete-all-tasks',
            // Activities - All
            'view-activities', 'view-all-activities', 'create-activities', 'delete-activities',
            // Reports - All
            'view-reports', 'view-all-reports', 'export-reports',
            // Settings
            'view-settings',
        ]);

        // Manager - View all, edit/delete own + team
        $manager = Role::create(['name' => 'Manager']);
        $manager->givePermissionTo([
            'view-users',
            // Leads
            'view-leads', 'view-all-leads', 'create-leads', 'edit-leads', 'edit-all-leads',
            'delete-leads', 'assign-leads', 'convert-leads',
            // Contacts
            'view-contacts', 'view-all-contacts', 'create-contacts', 'edit-contacts',
            'edit-all-contacts', 'delete-contacts',
            // Companies
            'view-companies', 'view-all-companies', 'create-companies', 'edit-companies',
            'edit-all-companies', 'delete-companies',
            // Deals
            'view-deals', 'view-all-deals', 'create-deals', 'edit-deals', 'edit-all-deals',
            'delete-deals',
            // Tasks
            'view-tasks', 'view-all-tasks', 'create-tasks', 'edit-tasks', 'edit-all-tasks',
            'delete-tasks',
            // Activities
            'view-activities', 'view-all-activities', 'create-activities', 'delete-activities',
            // Reports
            'view-reports', 'view-all-reports', 'export-reports',
        ]);

        // Sales Rep - View all, edit/delete only own
        $salesRep = Role::create(['name' => 'Sales Rep']);
        $salesRep->givePermissionTo([
            'view-users',
            // Leads - Own only
            'view-leads', 'view-all-leads', 'create-leads', 'edit-leads', 'convert-leads',
            // Contacts - Own only
            'view-contacts', 'view-all-contacts', 'create-contacts', 'edit-contacts',
            // Companies - Own only
            'view-companies', 'view-all-companies', 'create-companies', 'edit-companies',
            // Deals - Own only
            'view-deals', 'view-all-deals', 'create-deals', 'edit-deals',
            // Tasks - Own only
            'view-tasks', 'view-all-tasks', 'create-tasks', 'edit-tasks',
            // Activities
            'view-activities', 'view-all-activities', 'create-activities',
            // Reports - View only
            'view-reports',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Created roles: Super Admin, Admin, Manager, Sales Rep');
        $this->command->info('Created ' . count($permissions) . ' permissions');
    }
}
