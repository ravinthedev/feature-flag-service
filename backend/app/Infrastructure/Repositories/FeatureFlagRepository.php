<?php

namespace App\Infrastructure\Repositories;

use App\Domain\FeatureFlags\FeatureFlag as FeatureFlagEntity;
use App\Domain\FeatureFlags\FeatureFlagRepositoryInterface;
use App\Models\FeatureFlag;

final class FeatureFlagRepository implements FeatureFlagRepositoryInterface
{
    public function findByKey(string $key): ?FeatureFlagEntity
    {
        $featureFlag = FeatureFlag::where('key', $key)->first();

        if (!$featureFlag) {
            return null;
        }

        return $featureFlag->toDomainEntity();
    }

    public function findAll(): array
    {
        return FeatureFlag::all()
            ->map(fn (FeatureFlag $flag) => $flag->toDomainEntity())
            ->toArray();
    }

    public function findEnabled(): array
    {
        return FeatureFlag::where('is_enabled', true)
            ->get()
            ->map(fn (FeatureFlag $flag) => $flag->toDomainEntity())
            ->toArray();
    }

    public function save(FeatureFlagEntity $entity): FeatureFlagEntity
    {
        $data = FeatureFlag::fromDomainEntity($entity);
        
        $featureFlag = FeatureFlag::updateOrCreate(
            ['key' => $entity->key()],
            $data
        );

        return $featureFlag->toDomainEntity();
    }

    public function delete(string $key): bool
    {
        return FeatureFlag::where('key', $key)->delete() > 0;
    }

    public function findByKeys(array $keys): array
    {
        return FeatureFlag::whereIn('key', $keys)
            ->get()
            ->map(fn (FeatureFlag $flag) => $flag->toDomainEntity())
            ->toArray();
    }

    public function isEnabled(string $key): bool
    {
        $featureFlag = FeatureFlag::where('key', $key)->first();

        if (!$featureFlag) {
            return false;
        }

        return $featureFlag->isActive();
    }
}
