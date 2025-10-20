<?php

namespace Database\Seeders;

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
                'name' => 'Upload Photos',
                'key' => 'upload_photos',
                'description' => 'Allow users to upload photos for car damage reports',
                'is_enabled' => true,
                'rollout_type' => 'boolean',
                'rollout_value' => null,
                'starts_at' => now(),
                'ends_at' => null,
            ],
            [
                'name' => 'Premium Analytics',
                'key' => 'premium_analytics',
                'description' => 'Advanced analytics dashboard for premium users',
                'is_enabled' => false,
                'rollout_type' => 'percentage',
                'rollout_value' => ['percentage' => 25],
                'starts_at' => now()->addDays(7),
                'ends_at' => null,
            ],
            [
                'name' => 'Beta Dashboard',
                'key' => 'beta_dashboard',
                'description' => 'New dashboard interface for beta testing',
                'is_enabled' => true,
                'rollout_type' => 'user_list',
                'rollout_value' => [
                    'users' => ['admin@example.com', 'beta@example.com'],
                    'roles' => ['admin', 'beta_tester']
                ],
                'starts_at' => now()->subDays(5),
                'ends_at' => now()->addDays(30),
            ],
            [
                'name' => 'AI Damage Assessment',
                'key' => 'ai_damage_assessment',
                'description' => 'Automatic damage assessment using AI',
                'is_enabled' => false,
                'rollout_type' => 'boolean',
                'rollout_value' => null,
                'starts_at' => null,
                'ends_at' => null,
            ],
            [
                'name' => 'Mobile App Integration',
                'key' => 'mobile_app_integration',
                'description' => 'Integration with mobile application',
                'is_enabled' => true,
                'rollout_type' => 'percentage',
                'rollout_value' => ['percentage' => 75],
                'starts_at' => now()->subDays(10),
                'ends_at' => null,
            ],
        ];

        foreach ($featureFlags as $flag) {
            \App\Models\FeatureFlag::updateOrCreate(
                ['key' => $flag['key']],
                $flag
            );
        }
    }
}
