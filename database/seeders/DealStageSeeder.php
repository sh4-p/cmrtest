<?php

namespace Database\Seeders;

use App\Models\DealStage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DealStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Lead',
                'order' => 1,
                'color' => '#6B7280', // gray-500
            ],
            [
                'name' => 'Contacted',
                'order' => 2,
                'color' => '#3B82F6', // blue-500
            ],
            [
                'name' => 'Qualified',
                'order' => 3,
                'color' => '#8B5CF6', // violet-500
            ],
            [
                'name' => 'Proposal',
                'order' => 4,
                'color' => '#F59E0B', // amber-500
            ],
            [
                'name' => 'Negotiation',
                'order' => 5,
                'color' => '#F97316', // orange-500
            ],
            [
                'name' => 'Won',
                'order' => 6,
                'color' => '#10B981', // green-500
            ],
            [
                'name' => 'Lost',
                'order' => 7,
                'color' => '#EF4444', // red-500
            ],
        ];

        foreach ($stages as $stage) {
            DealStage::create($stage);
        }
    }
}
