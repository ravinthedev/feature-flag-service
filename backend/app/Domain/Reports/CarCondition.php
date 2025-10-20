<?php

declare(strict_types=1);

namespace App\Domain\Reports;

enum CarCondition: string
{
    case EXCELLENT = 'excellent';
    case GOOD = 'good';
    case FAIR = 'fair';
    case POOR = 'poor';
    case SALVAGE = 'salvage';

    public function label(): string
    {
        return match ($this) {
            self::EXCELLENT => 'Excellent',
            self::GOOD => 'Good',
            self::FAIR => 'Fair',
            self::POOR => 'Poor',
            self::SALVAGE => 'Salvage',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EXCELLENT => 'Like new condition with minimal wear',
            self::GOOD => 'Well maintained with minor cosmetic flaws',
            self::FAIR => 'Some wear and tear but mechanically sound',
            self::POOR => 'Significant wear, may need repairs',
            self::SALVAGE => 'Severely damaged or not roadworthy',
        };
    }

    public function marketValueMultiplier(): float
    {
        return match ($this) {
            self::EXCELLENT => 1.0,
            self::GOOD => 0.85,
            self::FAIR => 0.65,
            self::POOR => 0.45,
            self::SALVAGE => 0.15,
        };
    }

    public static function fromString(string $condition): self
    {
        return match (strtolower(trim($condition))) {
            'excellent' => self::EXCELLENT,
            'good' => self::GOOD,
            'fair' => self::FAIR,
            'poor' => self::POOR,
            'salvage' => self::SALVAGE,
            default => throw new \InvalidArgumentException("Invalid car condition: {$condition}"),
        };
    }
}
