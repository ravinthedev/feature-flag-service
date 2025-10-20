<?php

namespace App\Domain\Reports;

interface CarReportRepositoryInterface
{
    public function findById(int $id): ?CarReport;
    
    public function findAll(): array;
    
    public function findByStatus(string $status): array;
    
    public function save(CarReport $report): CarReport;
    
    public function delete(int $id): bool;
}
