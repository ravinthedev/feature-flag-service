<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlags;

final readonly class EvaluationResult
{
    public function __construct(
        private string $key,
        private bool $enabled,
        private string $reason,
        private ?FeatureFlag $featureFlag = null,
    ) {}

    public function key(): string
    {
        return $this->key;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function featureFlag(): ?FeatureFlag
    {
        return $this->featureFlag;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'enabled' => $this->enabled,
            'reason' => $this->reason,
            'feature_flag' => $this->featureFlag ? [
                'key' => $this->featureFlag->key(),
                'name' => $this->featureFlag->name(),
                'description' => $this->featureFlag->description(),
            ] : null,
        ];
    }
}
