<?php

namespace App\Domain\FeatureFlags;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EvaluateFeatureFlagsService
{
    private const CACHE_TTL = 60; // TODO: make this configurable

    public function __construct(
        private FeatureFlagRepositoryInterface $repository,
        private FeatureFlagEvaluator $evaluator,
        private FeatureFlagDecisionLogger $logger
    ) {}

    public function evaluateFlag(string $key, array $context = []): EvaluationResult
    {
        $cacheKey = "flag:{$key}:" . md5(json_encode($context));
        
        $result = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($key, $context) {
            return $this->evaluator->evaluate($key, $context);
        });

        // Log feature flag decision for monitoring
        $this->logger->logDecision(
            flagKey: $key,
            enabled: $result->isEnabled(),
            reason: $result->reason(),
            context: $context,
            userId: $context['user_id'] ?? null,
            sessionId: $context['session_id'] ?? null
        );

        return $result;
    }

    public function isEnabled(string $key, array $context = []): bool
    {
        return $this->evaluateFlag($key, $context)->isEnabled();
    }

    public function getActiveFlags(array $context = []): array
    {
        $cacheKey = "active_flags:" . md5(json_encode($context));
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($context) {
            $flags = $this->repository->findAll();
            $active = [];
            
            foreach ($flags as $flag) {
                if ($this->evaluator->isEnabled($flag->key(), $context)) {
                    $active[$flag->key()] = [
                        'key' => $flag->key(),
                        'name' => $flag->name(),
                        'description' => $flag->description(),
                    ];
                }
            }
            
            return $active;
        });
    }
}
