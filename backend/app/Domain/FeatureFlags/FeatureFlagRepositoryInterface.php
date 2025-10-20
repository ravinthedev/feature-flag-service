<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlags;

interface FeatureFlagRepositoryInterface
{
    public function findByKey(string $key): ?FeatureFlag;
    
    public function findAll(): array;
    
    public function findEnabled(): array;
    
    public function save(FeatureFlag $entity): FeatureFlag;
    
    public function delete(string $key): bool;
    
    public function findByKeys(array $keys): array;
    
    public function isEnabled(string $key): bool;
}
