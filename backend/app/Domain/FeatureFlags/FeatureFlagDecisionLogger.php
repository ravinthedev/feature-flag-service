<?php

namespace App\Domain\FeatureFlags;

use App\Models\FeatureFlagDecision;

class FeatureFlagDecisionLogger
{
    public function logDecision(
        string $flagKey,
        bool $enabled,
        string $reason,
        array $context = [],
        ?string $userId = null,
        ?string $sessionId = null
    ): void {
        FeatureFlagDecision::create([
            'flag_key' => $flagKey,
            'enabled' => $enabled,
            'reason' => $reason,
            'context' => $context,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'evaluated_at' => now(),
        ]);
    }

    public function getFlagStats(string $flagKey, int $hours = 24): array
    {
        $decisions = FeatureFlagDecision::forFlag($flagKey)->recent($hours)->get();
        
        $total = $decisions->count();
        $enabled = $decisions->where('enabled', true)->count();
        
        return [
            'flag_key' => $flagKey,
            'period_hours' => $hours,
            'total_decisions' => $total,
            'enabled_count' => $enabled,
            'disabled_count' => $total - $enabled,
            'enabled_percentage' => $total > 0 ? round(($enabled / $total) * 100, 2) : 0,
            'reasons' => $decisions->groupBy('reason')->map->count(),
        ];
    }

    public function getUserFlagHistory(string $userId, int $limit = 50): array
    {
        return FeatureFlagDecision::forUser($userId)
            ->orderBy('evaluated_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
