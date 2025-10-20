<?php

declare(strict_types=1);

namespace App\Domain\Reports;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class CarReport
{
    public function __construct(
        private string $make,
        private string $model,
        private int $year,
        private CarCondition $condition,
        private ?string $description = null,
        private ?DateTimeImmutable $createdAt = null,
    ) {
        if (empty(trim($make))) {
            throw new InvalidArgumentException('Car make cannot be empty');
        }
    }

    public function getMake(): string
    {
        return $this->make;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getCondition(): CarCondition
    {
        return $this->condition;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function fullName(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }

    public function withCondition(CarCondition $condition): self
    {
        return new self(
            $this->make,
            $this->model,
            $this->year,
            $condition,
            $this->description,
            $this->createdAt
        );
    }

    public function withDescription(string $description): self
    {
        return new self(
            $this->make,
            $this->model,
            $this->year,
            $this->condition,
            $description,
            $this->createdAt
        );
    }

    public function isVintage(): bool
    {
        $currentYear = (int) date('Y');
        return ($currentYear - $this->year) >= 25;
    }

    public function toArray(): array
    {
        return [
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'condition' => $this->condition->value,
            'description' => $this->description,
            'full_name' => $this->fullName(),
            'is_vintage' => $this->isVintage(),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }

}
