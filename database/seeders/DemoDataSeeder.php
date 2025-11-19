<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Task;
use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // Leads
            'view-leads', 'view-all-leads', 'create-leads', 'edit-leads', 'edit-all-leads',
            'delete-leads', 'delete-all-leads', 'assign-leads', 'convert-leads',
            // Contacts
            'view-contacts', 'view-all-contacts', 'create-contacts', 'edit-contacts',
            'edit-all-contacts', 'delete-contacts', 'delete-all-contacts',
            // Companies
            'view-companies', 'view-all-companies', 'create-companies', 'edit-companies',
            'edit-all-companies', 'delete-companies', 'delete-all-companies',
            // Deals
            'view-deals', 'view-all-deals', 'create-deals', 'edit-deals', 'edit-all-deals',
            'delete-deals', 'delete-all-deals', 'manage-stages-deals',
            // Tasks
            'view-tasks', 'view-all-tasks', 'create-tasks', 'edit-tasks', 'edit-all-tasks',
            'delete-tasks', 'delete-all-tasks',
            // Activities
            'view-activities', 'view-all-activities', 'create-activities', 'edit-activities',
            'delete-activities',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $sales = Role::firstOrCreate(['name' => 'Sales Rep']);

        // Assign all permissions to Admin
        $admin->syncPermissions(Permission::all());

        // Manager permissions (view-all, edit-all, but not delete-all)
        $manager->syncPermissions([
            'view-all-leads', 'create-leads', 'edit-all-leads', 'assign-leads', 'convert-leads',
            'view-all-contacts', 'create-contacts', 'edit-all-contacts',
            'view-all-companies', 'create-companies', 'edit-all-companies',
            'view-all-deals', 'create-deals', 'edit-all-deals', 'manage-stages-deals',
            'view-all-tasks', 'create-tasks', 'edit-all-tasks',
            'view-all-activities', 'create-activities', 'edit-activities',
        ]);

        // Sales Rep permissions (view own, edit own)
        $sales->syncPermissions([
            'view-leads', 'create-leads', 'edit-leads',
            'view-contacts', 'create-contacts', 'edit-contacts',
            'view-companies', 'create-companies', 'edit-companies',
            'view-deals', 'create-deals', 'edit-deals',
            'view-tasks', 'create-tasks', 'edit-tasks',
            'view-activities', 'create-activities', 'edit-activities',
        ]);

        // Create Demo Users
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@crm.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $adminUser->assignRole('Admin');

        $managerUser = User::firstOrCreate(
            ['email' => 'manager@crm.test'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $managerUser->assignRole('Manager');

        $salesUser = User::firstOrCreate(
            ['email' => 'sales@crm.test'],
            [
                'name' => 'Sales Rep',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $salesUser->assignRole('Sales Rep');

        // Create Deal Stages
        $stages = [
            ['name' => 'Prospecting', 'order' => 1, 'color' => '#3B82F6'],
            ['name' => 'Qualification', 'order' => 2, 'color' => '#8B5CF6'],
            ['name' => 'Proposal', 'order' => 3, 'color' => '#F59E0B'],
            ['name' => 'Negotiation', 'order' => 4, 'color' => '#EF4444'],
            ['name' => 'Won', 'order' => 5, 'color' => '#10B981'],
            ['name' => 'Lost', 'order' => 6, 'color' => '#6B7280'],
        ];

        foreach ($stages as $stage) {
            DealStage::firstOrCreate(['name' => $stage['name']], $stage);
        }

        // Create Demo Companies
        $company1 = Company::firstOrCreate(
            ['name' => 'Acme Corporation'],
            [
                'industry' => 'Technology',
                'website' => 'https://acme.example.com',
                'phone' => '+1-555-0100',
                'address' => '123 Tech Street, San Francisco, CA 94102',
                'owner_id' => $salesUser->id,
            ]
        );

        $company2 = Company::firstOrCreate(
            ['name' => 'Global Industries'],
            [
                'industry' => 'Manufacturing',
                'website' => 'https://globalind.example.com',
                'phone' => '+1-555-0200',
                'address' => '456 Industrial Ave, Detroit, MI 48201',
                'owner_id' => $managerUser->id,
            ]
        );

        // Create Demo Leads
        Lead::firstOrCreate(
            ['email' => 'john.doe@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '+1-555-0101',
                'company' => 'Tech Startup Inc',
                'job_title' => 'CTO',
                'source' => 'Website',
                'status' => 'New',
                'assigned_to_id' => $salesUser->id,
                'notes' => 'Interested in enterprise package',
            ]
        );

        Lead::firstOrCreate(
            ['email' => 'jane.smith@example.com'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'phone' => '+1-555-0102',
                'company' => 'Marketing Pro',
                'job_title' => 'Marketing Director',
                'source' => 'Referral',
                'status' => 'Contacted',
                'assigned_to_id' => $salesUser->id,
                'notes' => 'Referral from existing client',
            ]
        );

        // Create Demo Contacts
        $contact1 = Contact::firstOrCreate(
            ['email' => 'bob.johnson@acme.example.com'],
            [
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'phone' => '+1-555-0103',
                'job_title' => 'VP of Sales',
                'company_id' => $company1->id,
                'owner_id' => $salesUser->id,
            ]
        );

        $contact2 = Contact::firstOrCreate(
            ['email' => 'alice.williams@globalind.example.com'],
            [
                'first_name' => 'Alice',
                'last_name' => 'Williams',
                'phone' => '+1-555-0104',
                'job_title' => 'Procurement Manager',
                'company_id' => $company2->id,
                'owner_id' => $managerUser->id,
            ]
        );

        // Create Demo Deals
        $prospectingStage = DealStage::where('name', 'Prospecting')->first();
        $qualificationStage = DealStage::where('name', 'Qualification')->first();

        Deal::firstOrCreate(
            ['name' => 'Enterprise Software License'],
            [
                'contact_id' => $contact1->id,
                'deal_stage_id' => $prospectingStage->id,
                'amount' => 50000.00,
                'closing_date' => now()->addDays(30),
                'probability' => 60,
                'assigned_to_id' => $salesUser->id,
                'description' => 'Annual enterprise license for 100 users',
            ]
        );

        Deal::firstOrCreate(
            ['name' => 'Manufacturing Equipment'],
            [
                'contact_id' => $contact2->id,
                'deal_stage_id' => $qualificationStage->id,
                'amount' => 150000.00,
                'closing_date' => now()->addDays(60),
                'probability' => 40,
                'assigned_to_id' => $managerUser->id,
                'description' => 'New production line equipment',
            ]
        );

        // Create Demo Tasks
        Task::firstOrCreate(
            ['title' => 'Follow up with John Doe'],
            [
                'description' => 'Send product demo link and pricing information',
                'status' => 'Pending',
                'priority' => 'High',
                'due_date' => now()->addDays(2),
                'assigned_to_id' => $salesUser->id,
                'related_to_type' => 'App\\Models\\Lead',
                'related_to_id' => 1,
            ]
        );

        Task::firstOrCreate(
            ['title' => 'Prepare proposal for Acme Corp'],
            [
                'description' => 'Create detailed proposal document',
                'status' => 'In Progress',
                'priority' => 'High',
                'due_date' => now()->addDays(5),
                'assigned_to_id' => $salesUser->id,
                'related_to_type' => 'App\\Models\\Deal',
                'related_to_id' => 1,
            ]
        );

        // Create Demo Activities
        Activity::firstOrCreate(
            [
                'description' => 'Initial contact call - discussed requirements',
                'user_id' => $salesUser->id,
            ],
            [
                'type' => 'Call',
                'subject_type' => 'App\\Models\\Lead',
                'subject_id' => 1,
                'activity_date' => now()->subDays(1),
            ]
        );

        Activity::firstOrCreate(
            [
                'description' => 'Sent follow-up email with product brochure',
                'user_id' => $salesUser->id,
            ],
            [
                'type' => 'Email',
                'subject_type' => 'App\\Models\\Contact',
                'subject_id' => 1,
                'activity_date' => now(),
            ]
        );

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('');
        $this->command->info('Demo Users:');
        $this->command->info('Admin: admin@crm.test / password');
        $this->command->info('Manager: manager@crm.test / password');
        $this->command->info('Sales Rep: sales@crm.test / password');
    }
}
