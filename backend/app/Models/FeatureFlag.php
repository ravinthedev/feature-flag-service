<?php

namespace App\Models;

use App\Domain\FeatureFlags\FeatureFlag as FeatureFlagEntity;
use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    protected $fillable = [
        'name',
        'key',
        'description',
        'is_enabled',
        'rollout_type',
        'rollout_value',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'rollout_value' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function toDomainEntity(): FeatureFlagEntity
    {
        return new FeatureFlagEntity(
            key: $this->key,
            name: $this->name,
            isEnabled: $this->is_enabled,
            description: $this->description,
            conditions: $this->rollout_value,
            createdAt: $this->created_at?->toDateTimeImmutable(),
            updatedAt: $this->updated_at?->toDateTimeImmutable(),
            startsAt: $this->starts_at?->toDateTimeImmutable(),
            endsAt: $this->ends_at?->toDateTimeImmutable()
        );
    }

    public static function fromDomainEntity(FeatureFlagEntity $entity): array
    {
        return [
            'key' => $entity->key(),
            'name' => $entity->name(),
            'description' => $entity->description(),
            'is_enabled' => $entity->isEnabled(),
            'rollout_type' => 'boolean',
            'rollout_value' => $entity->conditions(),
        ];
    }

    public function isActive(): bool
    {
        if (!$this->is_enabled) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->isBefore($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->isAfter($this->ends_at)) {
            return false;
        }

        return true;
    }
}
