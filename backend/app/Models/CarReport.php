<?php

namespace App\Models;

use App\Domain\Reports\CarReport as CarReportEntity;
use App\Domain\Reports\CarCondition;
use Illuminate\Database\Eloquent\Model;

class CarReport extends Model
{
    protected $fillable = [
        'car_model',
        'description',
        'damage_type',
        'photo_url',
        'status',
    ];

    public function toDomainEntity(): CarReportEntity
    {
        return new CarReportEntity(
            make: $this->getCarMake(),
            model: $this->getCarModel(),
            year: $this->getCarYear(),
            condition: $this->mapDamageTypeToCondition($this->damage_type)
        );
    }

    public static function fromDomainEntity(CarReportEntity $entity): array
    {
        return [
            'car_model' => $entity->getMake() . ' ' . $entity->getModel() . ' ' . $entity->getYear(),
            'description' => $entity->getDescription(),
            'damage_type' => self::mapConditionToDamageType($entity->getCondition()),
            'status' => 'pending',
        ];
    }

    private function getCarMake(): string
    {
        $parts = explode(' ', $this->car_model);
        return $parts[0] ?? 'Unknown';
    }

    private function getCarModel(): string
    {
        $parts = explode(' ', $this->car_model);
        return $parts[1] ?? 'Unknown';
    }

    private function getCarYear(): int
    {
        $parts = explode(' ', $this->car_model);
        $year = end($parts);
        return is_numeric($year) ? (int) $year : 2020;
    }

    private function mapDamageTypeToCondition(string $damageType): CarCondition
    {
        return match ($damageType) {
            'minor' => CarCondition::GOOD,
            'moderate' => CarCondition::FAIR,
            'severe' => CarCondition::POOR,
            'total_loss' => CarCondition::POOR,
            default => CarCondition::FAIR,
        };
    }

    private static function mapConditionToDamageType(CarCondition $condition): string
    {
        return match ($condition) {
            CarCondition::EXCELLENT => 'minor',
            CarCondition::GOOD => 'minor',
            CarCondition::FAIR => 'moderate',
            CarCondition::POOR => 'severe',
            CarCondition::SALVAGE => 'total_loss',
        };
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDamageType($query, string $damageType)
    {
        return $query->where('damage_type', $damageType);
    }
}
