<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CarReports\CreateCarReportDTO;
use App\DTOs\CarReports\UpdateCarReportDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CarReports\CreateCarReportRequest;
use App\Http\Requests\CarReports\UpdateCarReportRequest;
use App\Http\Resources\CarReportResource;
use App\Models\CarReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarReportController extends Controller
{
    public function index(): JsonResponse
    {
        $reports = CarReport::latest()->get();
        
        return CarReportResource::collection($reports)->response();
    }

    public function show(int $id): JsonResponse
    {
        $report = CarReport::findOrFail($id);
        
        return (new CarReportResource($report))->response();
    }

    public function byStatus(string $status): JsonResponse
    {
        $reports = CarReport::byStatus($status)->latest()->get();
        
        return CarReportResource::collection($reports)->response();
    }

    public function store(CreateCarReportRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('car-reports', 'public');
            $validated['photo_url'] = $path;
        }
        
        unset($validated['photo']);
        
        $dto = CreateCarReportDTO::fromRequest($validated);
        $report = CarReport::create($dto->toArray());

        return (new CarReportResource($report))->response()->setStatusCode(201);
    }

    public function update(UpdateCarReportRequest $request, int $id): JsonResponse
    {
        $report = CarReport::findOrFail($id);
        $validated = $request->validated();
        
        if ($request->hasFile('photo')) {
            if ($report->photo_url) {
                Storage::disk('public')->delete($report->photo_url);
            }
            $path = $request->file('photo')->store('car-reports', 'public');
            $validated['photo_url'] = $path;
        }
        
        unset($validated['photo']);
        
        $dto = UpdateCarReportDTO::fromRequest($validated);
        $report->update($dto->toArray());

        return (new CarReportResource($report->fresh()))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $report = CarReport::findOrFail($id);
        
        if ($report->photo_url) {
            Storage::disk('public')->delete($report->photo_url);
        }
        
        $report->delete();

        return response()->json(['message' => 'Car report deleted successfully'], 200);
    }
}
