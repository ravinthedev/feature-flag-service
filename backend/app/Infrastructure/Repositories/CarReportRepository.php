<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Reports\CarReport as CarReportEntity;
use App\Domain\Reports\CarReportRepositoryInterface;
use App\Models\CarReport;

final class CarReportRepository implements CarReportRepositoryInterface
{
    public function findById(int $id): ?CarReportEntity
    {
        $carReport = CarReport::find($id);

        if (!$carReport) {
            return null;
        }

        return $carReport->toDomainEntity();
    }

    public function findAll(): array
    {
        return CarReport::all()
            ->map(fn (CarReport $report) => $report->toDomainEntity())
            ->toArray();
    }

    public function findByStatus(string $status): array
    {
        return CarReport::byStatus($status)
            ->get()
            ->map(fn (CarReport $report) => $report->toDomainEntity())
            ->toArray();
    }

    public function save(CarReportEntity $entity): CarReportEntity
    {
        $data = CarReport::fromDomainEntity($entity);
        
        $carReport = CarReport::create($data);

        return $carReport->toDomainEntity();
    }

    public function delete(int $id): bool
    {
        return CarReport::where('id', $id)->delete() > 0;
    }

    public function findByDamageType(string $damageType): array
    {
        return CarReport::byDamageType($damageType)
            ->get()
            ->map(fn (CarReport $report) => $report->toDomainEntity())
            ->toArray();
    }

    public function findRecent(int $limit = 10): array
    {
        return CarReport::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn (CarReport $report) => $report->toDomainEntity())
            ->toArray();
    }
}
