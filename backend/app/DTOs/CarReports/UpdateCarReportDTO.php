<?php

declare(strict_types=1);

namespace App\DTOs\CarReports;

readonly class UpdateCarReportDTO
{
    public function __construct(
        public ?string $carModel,
        public ?string $description,
        public ?string $damageType,
        public ?string $photoUrl,
        public ?string $status,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            carModel: $data['car_model'] ?? null,
            description: $data['description'] ?? null,
            damageType: $data['damage_type'] ?? null,
            photoUrl: $data['photo_url'] ?? null,
            status: $data['status'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'car_model' => $this->carModel,
            'description' => $this->description,
            'damage_type' => $this->damageType,
            'photo_url' => $this->photoUrl,
            'status' => $this->status,
        ], fn($value) => $value !== null);
    }
}
