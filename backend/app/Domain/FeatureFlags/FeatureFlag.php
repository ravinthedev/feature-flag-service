<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlags;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class FeatureFlag
{
    public function __construct(
        private string $key,
        private string $name,
        private bool $isEnabled,
        private ?string $description = null,
        private ?array $conditions = null,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
        private ?DateTimeImmutable $startsAt = null,
        private ?DateTimeImmutable $endsAt = null,
    ) {
        $this->validateKey($key);
        $this->validateName($name);
    }

    public function key(): string
    {
        return $this->key;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function conditions(): ?array
    {
        return $this->conditions;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function startsAt(): ?DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function endsAt(): ?DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function enable(): self
    {
        return new self(
            $this->key,
            $this->name,
            true,
            $this->description,
            $this->conditions,
            $this->createdAt,
            new DateTimeImmutable(),
            $this->startsAt,
            $this->endsAt
        );
    }

    public function disable(): self
    {
        return new self(
            $this->key,
            $this->name,
            false,
            $this->description,
            $this->conditions,
            $this->createdAt,
            new DateTimeImmutable(),
            $this->startsAt,
            $this->endsAt
        );
    }

    public function withConditions(array $conditions): self
    {
        return new self(
            $this->key,
            $this->name,
            $this->isEnabled,
            $this->description,
            $conditions,
            $this->createdAt,
            new DateTimeImmutable(),
            $this->startsAt,
            $this->endsAt
        );
    }

    public function withDescription(string $description): self
    {
        return new self(
            $this->key,
            $this->name,
            $this->isEnabled,
            $description,
            $this->conditions,
            $this->createdAt,
            new DateTimeImmutable(),
            $this->startsAt,
            $this->endsAt
        );
    }

    public function withSchedule(?DateTimeImmutable $startsAt, ?DateTimeImmutable $endsAt): self
    {
        return new self(
            $this->key,
            $this->name,
            $this->isEnabled,
            $this->description,
            $this->conditions,
            $this->createdAt,
            new DateTimeImmutable(),
            $startsAt,
            $endsAt
        );
    }

    public function withName(string $name): self
    {
        $this->validateName($name);
        
        return new self(
            $this->key,
            $name,
            $this->isEnabled,
            $this->description,
            $this->conditions,
            $this->createdAt,
            new DateTimeImmutable(),
            $this->startsAt,
            $this->endsAt
        );
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,
            'is_enabled' => $this->isEnabled,
            'conditions' => $this->conditions,
            'starts_at' => $this->startsAt?->format('Y-m-d H:i:s'),
            'ends_at' => $this->endsAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    private function validateKey(string $key): void
    {
        if (empty(trim($key)) || !preg_match('/^[a-zA-Z0-9_-]+$/', $key)) {
            throw new InvalidArgumentException('Invalid feature flag key');
        }
    }

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Feature flag name cannot be empty');
        }
    }
}
