<?php

declare(strict_types=1);

namespace App\DTOs\CarReports;

readonly class CreateCarReportDTO
{
    public function __construct(
        public string $carModel,
        public string $description,
        public string $damageType,
        public ?string $photoUrl,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            carModel: $data['car_model'],
            description: $data['description'],
            damageType: $data['damage_type'],
            photoUrl: $data['photo_url'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'car_model' => $this->carModel,
            'description' => $this->description,
            'damage_type' => $this->damageType,
            'photo_url' => $this->photoUrl,
            'status' => 'pending',
        ];
    }
}
