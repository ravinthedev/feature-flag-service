<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $featureFlags = [
            [
                'name' => 'Photo Upload',
                'key' => 'upload_photos',
                'description' => 'Upload photos with damage reports',
                'is_enabled' => true,
                'rollout_type' => 'boolean',
                'rollout_value' => null,
                'starts_at' => null,
                'ends_at' => null,
            ],
            [
                'name' => 'Advanced Search',
                'key' => 'premium_analytics',
                'description' => 'Filter reports by type, date, and status',
                'is_enabled' => true,
                'rollout_type' => 'percentage',
                'rollout_value' => ['percentage' => 50],
                'starts_at' => null,
                'ends_at' => null,
            ],
            [
                'name' => 'Beta Dashboard',
                'key' => 'beta_dashboard',
                'description' => 'New dashboard for testing',
                'is_enabled' => true,
                'rollout_type' => 'user_list',
                'rollout_value' => [
                    'users' => ['admin@example.com'],
                    'roles' => ['admin']
                ],
                'starts_at' => null,
                'ends_at' => null,
            ],
            [
                'name' => 'AI Assessment',
                'key' => 'ai_damage_assessment',
                'description' => 'Auto-detect damage severity using AI',
                'is_enabled' => false,
                'rollout_type' => 'boolean',
                'rollout_value' => null,
                'starts_at' => null,
                'ends_at' => null,
            ],
            [
                'name' => 'Mobile App',
                'key' => 'mobile_app_integration',
                'description' => 'Submit reports via mobile app',
                'is_enabled' => true,
                'rollout_type' => 'percentage',
                'rollout_value' => ['percentage' => 75],
                'starts_at' => null,
                'ends_at' => null,
            ],
        ];

        foreach ($featureFlags as $flag) {
            FeatureFlag::updateOrCreate(
                ['key' => $flag['key']],
                $flag
            );
        }
    }
}
