<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureFlagDecision extends Model
{
    protected $fillable = [
        'flag_key',
        'enabled',
        'reason',
        'context',
        'user_id',
        'session_id',
        'evaluated_at',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'context' => 'array',
        'evaluated_at' => 'datetime',
    ];

    public function scopeForFlag($query, string $flagKey)
    {
        return $query->where('flag_key', $flagKey);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeDisabled($query)
    {
        return $query->where('enabled', false);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('evaluated_at', '>=', now()->subHours($hours));
    }
}