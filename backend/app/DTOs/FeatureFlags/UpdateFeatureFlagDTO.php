<?php

declare(strict_types=1);

namespace App\DTOs\FeatureFlags;

readonly class UpdateFeatureFlagDTO
{
    public function __construct(
        public ?string $name,
        public ?string $description,
        public ?bool $isEnabled,
        public ?string $rolloutType,
        public ?array $rolloutValue,
        public ?string $startsAt,
        public ?string $endsAt,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            isEnabled: $data['is_enabled'] ?? null,
            rolloutType: $data['rollout_type'] ?? null,
            rolloutValue: $data['rollout_value'] ?? null,
            startsAt: $data['starts_at'] ?? null,
            endsAt: $data['ends_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'is_enabled' => $this->isEnabled,
            'rollout_type' => $this->rolloutType,
            'rollout_value' => $this->rolloutValue,
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
        ], fn($value) => $value !== null);
    }
}
