<?php

declare(strict_types=1);

namespace App\DTOs\FeatureFlags;

readonly class CreateFeatureFlagDTO
{
    public function __construct(
        public string $key,
        public string $name,
        public ?string $description,
        public bool $isEnabled,
        public string $rolloutType,
        public ?array $rolloutValue,
        public ?string $startsAt,
        public ?string $endsAt,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            key: $data['key'],
            name: $data['name'],
            description: $data['description'] ?? null,
            isEnabled: $data['is_enabled'] ?? false,
            rolloutType: $data['rollout_type'] ?? 'boolean',
            rolloutValue: $data['rollout_value'] ?? null,
            startsAt: $data['starts_at'] ?? null,
            endsAt: $data['ends_at'] ?? null,
        );
    }
}
