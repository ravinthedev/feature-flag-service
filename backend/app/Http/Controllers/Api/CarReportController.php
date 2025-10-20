<?php

namespace App\Http\Controllers\Api;

use App\Domain\Reports\CarReportRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarReportController extends Controller
{
    public function __construct(
        private CarReportRepositoryInterface $repository
    ) {}

    public function index(): JsonResponse
    {
        $reports = $this->repository->findAll();
        
        return response()->json([
            'data' => array_map(fn($report) => $report->toArray(), $reports)
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $report = $this->repository->findById($id);
        
        if (!$report) {
            return response()->json(['error' => 'Car report not found'], 404);
        }

        return response()->json(['data' => $report->toArray()]);
    }

    public function byStatus(string $status): JsonResponse
    {
        $reports = $this->repository->findByStatus($status);
        
        return response()->json([
            'data' => array_map(fn($report) => $report->toArray(), $reports)
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'car_model' => 'required|string|max:255',
            'description' => 'required|string',
            'damage_type' => 'required|in:minor,moderate,severe,total_loss',
            'photo_url' => 'nullable|url',
        ]);

        $report = new \App\Domain\Reports\CarReport(
            make: explode(' ', $request->car_model)[0] ?? 'Unknown',
            model: $request->car_model,
            year: 2020, // TODO: extract year from car_model properly
            condition: \App\Domain\Reports\CarCondition::from($request->damage_type),
            description: $request->description
        );

        $savedReport = $this->repository->save($report);

        return response()->json(['data' => $savedReport->toArray()], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $report = $this->repository->findById($id);
        
        if (!$report) {
            return response()->json(['error' => 'Car report not found'], 404);
        }

        $request->validate([
            'description' => 'sometimes|string',
            'damage_type' => 'sometimes|in:minor,moderate,severe,total_loss',
        ]);

        if ($request->has('description')) {
            $report = $report->withDescription($request->description);
        }
        if ($request->has('damage_type')) {
            $report = $report->withCondition(\App\Domain\Reports\CarCondition::from($request->damage_type));
        }

        $updatedReport = $this->repository->save($report);

        return response()->json(['data' => $updatedReport->toArray()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        
        if (!$deleted) {
            return response()->json(['error' => 'Car report not found'], 404);
        }

        return response()->json(['message' => 'Car report deleted successfully']);
    }
}
