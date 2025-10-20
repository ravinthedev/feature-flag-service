<?php

namespace App\Http\Controllers\Api;

use App\Domain\FeatureFlags\EvaluateFeatureFlagsService;
use App\Domain\FeatureFlags\FeatureFlagDecisionLogger;
use App\Domain\FeatureFlags\FeatureFlagRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    public function __construct(
        private FeatureFlagRepositoryInterface $repository,
        private EvaluateFeatureFlagsService $evaluationService,
        private FeatureFlagDecisionLogger $logger
    ) {}

    public function index(): JsonResponse
    {
        $flags = $this->repository->findAll();
        
        return response()->json([
            'data' => array_map(fn($flag) => $flag->toArray(), $flags)
        ]);
    }

    public function show(string $key): JsonResponse
    {
        $flag = $this->repository->findByKey($key);
        
        if (!$flag) {
            return response()->json(['error' => 'Feature flag not found'], 404);
        }

        return response()->json(['data' => $flag->toArray()]);
    }

    public function evaluate(string $key, Request $request): JsonResponse
    {
        $context = $request->get('context', []);
        $result = $this->evaluationService->evaluateFlag($key, $context);
        
        return response()->json(['data' => $result->toArray()]);
    }

    public function check(string $key, Request $request): JsonResponse
    {
        $context = $request->get('context', []);
        $isEnabled = $this->evaluationService->isEnabled($key, $context);
        
        return response()->json([
            'key' => $key,
            'enabled' => $isEnabled
        ]);
    }

    public function getActiveFlags(Request $request): JsonResponse
    {
        $context = $request->get('context', []);
        $activeFlags = $this->evaluationService->getActiveFlags($context);
        
        return response()->json(['data' => $activeFlags]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:feature_flags,key',
            'description' => 'nullable|string',
            'is_enabled' => 'boolean',
            'rollout_value' => 'nullable|array',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $flag = new \App\Domain\FeatureFlags\FeatureFlag(
            key: $request->key,
            name: $request->name,
            description: $request->description,
            isEnabled: $request->boolean('is_enabled', false),
            conditions: $request->rollout_value ?? [],
            startsAt: $request->starts_at ? new \DateTimeImmutable($request->starts_at) : null,
            endsAt: $request->ends_at ? new \DateTimeImmutable($request->ends_at) : null
        );

        $savedFlag = $this->repository->save($flag);

        return response()->json(['data' => $savedFlag->toArray()], 201);
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $flag = $this->repository->findByKey($key);
        
        if (!$flag) {
            return response()->json(['error' => 'Feature flag not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_enabled' => 'sometimes|boolean',
            'rollout_value' => 'nullable|array',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        if ($request->has('name')) {
            $flag = $flag->withName($request->name);
        }
        if ($request->has('description')) {
            $flag = $flag->withDescription($request->description);
        }
        if ($request->has('is_enabled')) {
            $flag = $request->boolean('is_enabled') ? $flag->enable() : $flag->disable();
        }
        if ($request->has('rollout_value')) {
            $flag = $flag->withConditions($request->rollout_value ?? []);
        }
        if ($request->has('starts_at') || $request->has('ends_at')) {
            $startsAt = $request->starts_at ? new \DateTimeImmutable($request->starts_at) : null;
            $endsAt = $request->ends_at ? new \DateTimeImmutable($request->ends_at) : null;
            $flag = $flag->withSchedule($startsAt, $endsAt);
        }

        $updatedFlag = $this->repository->save($flag);

        return response()->json(['data' => $updatedFlag->toArray()]);
    }

    public function destroy(string $key): JsonResponse
    {
        $deleted = $this->repository->delete($key);
        
        if (!$deleted) {
            return response()->json(['error' => 'Feature flag not found'], 404);
        }

        return response()->json(['message' => 'Feature flag deleted successfully']);
    }

    public function analytics(string $key, Request $request): JsonResponse
    {
        $hours = $request->get('hours', 24);
        $stats = $this->logger->getFlagStats($key, $hours);
        
        return response()->json(['data' => $stats]);
    }

    public function userHistory(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        $limit = $request->get('limit', 50);
        
        if (!$userId) {
            return response()->json(['error' => 'user_id is required'], 400);
        }
        
        $history = $this->logger->getUserFlagHistory($userId, $limit);
        
        return response()->json(['data' => $history]);
    }
}
