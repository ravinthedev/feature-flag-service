<?php

namespace App\Http\Controllers\Api;

use App\Domain\FeatureFlags\EvaluateFeatureFlagsService;
use App\Domain\FeatureFlags\FeatureFlag;
use App\Domain\FeatureFlags\FeatureFlagDecisionLogger;
use App\Domain\FeatureFlags\FeatureFlagRepositoryInterface;
use App\DTOs\FeatureFlags\CreateFeatureFlagDTO;
use App\DTOs\FeatureFlags\UpdateFeatureFlagDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeatureFlags\AnalyticsRequest;
use App\Http\Requests\FeatureFlags\CreateFeatureFlagRequest;
use App\Http\Requests\FeatureFlags\EvaluateFeatureFlagRequest;
use App\Http\Requests\FeatureFlags\UpdateFeatureFlagRequest;
use App\Http\Requests\FeatureFlags\UserHistoryRequest;
use App\Http\Resources\FeatureFlagResource;
use App\Models\FeatureFlag as FeatureFlagModel;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class FeatureFlagController extends Controller
{
    public function __construct(
        private FeatureFlagRepositoryInterface $repository,
        private EvaluateFeatureFlagsService $evaluationService,
        private FeatureFlagDecisionLogger $logger
    ) {}

    public function index(): JsonResponse
    {
        $flags = FeatureFlagModel::all();
        
        return FeatureFlagResource::collection($flags)->response();
    }

    public function show(string $key): JsonResponse
    {
        $flag = FeatureFlagModel::where('key', $key)->firstOrFail();
        
        return (new FeatureFlagResource($flag))->response();
    }

    public function evaluate(string $key, EvaluateFeatureFlagRequest $request): JsonResponse
    {
        $context = $request->validated()['context'] ?? [];
        $result = $this->evaluationService->evaluateFlag($key, $context);
        
        return response()->json(['data' => $result->toArray()]);
    }

    public function check(string $key, EvaluateFeatureFlagRequest $request): JsonResponse
    {
        $context = $request->validated()['context'] ?? [];
        $isEnabled = $this->evaluationService->isEnabled($key, $context);
        
        return response()->json([
            'key' => $key,
            'enabled' => $isEnabled
        ]);
    }


    public function store(CreateFeatureFlagRequest $request): JsonResponse
    {
        $dto = CreateFeatureFlagDTO::fromRequest($request->validated());

        $flag = new FeatureFlag(
            key: $dto->key,
            name: $dto->name,
            description: $dto->description,
            isEnabled: $dto->isEnabled,
            conditions: $dto->rolloutValue ?? [],
            startsAt: $dto->startsAt ? new DateTimeImmutable($dto->startsAt) : null,
            endsAt: $dto->endsAt ? new DateTimeImmutable($dto->endsAt) : null
        );

        $savedFlag = $this->repository->save($flag);
        $model = FeatureFlagModel::where('key', $savedFlag->key())->first();

        return (new FeatureFlagResource($model))->response()->setStatusCode(201);
    }

    public function update(UpdateFeatureFlagRequest $request, string $key): JsonResponse
    {
        $flag = FeatureFlagModel::where('key', $key)->firstOrFail();
        $dto = UpdateFeatureFlagDTO::fromRequest($request->validated());
        
        $flag->update($dto->toArray());
        $this->invalidateFlagCache($key);

        return (new FeatureFlagResource($flag->fresh()))->response();
    }

    public function destroy(string $key): JsonResponse
    {
        $deleted = $this->repository->delete($key);
        
        if (!$deleted) {
            return response()->json(['error' => 'Feature flag not found'], 404);
        }

        return response()->json(['message' => 'Feature flag deleted successfully'], 200);
    }

    public function analytics(string $key, AnalyticsRequest $request): JsonResponse
    {
        $hours = $request->integer('hours', 24);
        $stats = $this->logger->getFlagStats($key, $hours);
        
        return response()->json(['data' => $stats]);
    }

    public function userHistory(UserHistoryRequest $request): JsonResponse
    {
        $userId = $request->integer('user_id');
        $limit = $request->integer('limit', 50);
        $history = $this->logger->getUserFlagHistory($userId, $limit);
        
        return response()->json(['data' => $history]);
    }

    private function invalidateFlagCache(string $key): void
    {
        $patterns = [
            "flag:{$key}:*",
            "active_flags:*"
        ];

        foreach ($patterns as $pattern) {
            $keys = Redis::keys($pattern);
            if (!empty($keys)) {
                Redis::del($keys);
            }
        }
    }
}
