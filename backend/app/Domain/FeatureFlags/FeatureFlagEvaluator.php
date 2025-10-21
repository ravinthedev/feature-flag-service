<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlags;

use DateTimeImmutable;

class FeatureFlagEvaluator
{
    public function __construct(
        private FeatureFlagRepositoryInterface $repository
    ) {}

    public function isEnabled(string $key, array $context = []): bool
    {
        $result = $this->evaluate($key, $context);
        return $result->isEnabled();
    }

    public function evaluate(string $key, array $context = []): EvaluationResult
    {
        $featureFlag = $this->repository->findByKey($key);

        if (!$featureFlag) {
            return new EvaluationResult(
                key: $key,
                enabled: false,
                reason: 'Feature flag not found'
            );
        }

        if (!$featureFlag->isEnabled()) {
            return new EvaluationResult(
                key: $key,
                enabled: false,
                reason: 'Feature flag is disabled',
                featureFlag: $featureFlag
            );
        }

        $scheduledResult = $this->evaluateScheduled($featureFlag);
        if (!$scheduledResult['enabled']) {
            return new EvaluationResult(
                key: $key,
                enabled: false,
                reason: $scheduledResult['reason'],
                featureFlag: $featureFlag
            );
        }

        $rolloutResult = $this->evaluateRollout($featureFlag, $context);

        return new EvaluationResult(
            key: $key,
            enabled: $rolloutResult['enabled'],
            reason: $rolloutResult['reason'],
            featureFlag: $featureFlag
        );
    }

    private function evaluateScheduled(FeatureFlag $featureFlag): array
    {
        $now = new DateTimeImmutable();
        
        if ($featureFlag->startsAt() && $now < $featureFlag->startsAt()) {
            return ['enabled' => false, 'reason' => 'Feature flag is scheduled to start later'];
        }
        
        if ($featureFlag->endsAt() && $now > $featureFlag->endsAt()) {
            return ['enabled' => false, 'reason' => 'Feature flag has expired'];
        }
        
        return ['enabled' => true, 'reason' => 'Schedule check passed'];
    }

    private function evaluateRollout(FeatureFlag $featureFlag, array $context): array
    {
        $rolloutValue = $featureFlag->conditions();
        
        if (!$rolloutValue) {
            return ['enabled' => true, 'reason' => 'Boolean rollout - enabled'];
        }
        
        if (isset($rolloutValue['percentage'])) {
            $percentage = $rolloutValue['percentage'];
            $enabled = $this->evaluatePercentageRollout($percentage, $context);
            return [
                'enabled' => $enabled,
                'reason' => $enabled 
                    ? "User is in {$percentage}% rollout group" 
                    : "User is not in {$percentage}% rollout group"
            ];
        }
        
        if (isset($rolloutValue['users']) || isset($rolloutValue['roles'])) {
            $enabled = $this->evaluateUserListRollout($rolloutValue, $context);
            return [
                'enabled' => $enabled,
                'reason' => $enabled ? 'User is in allowed list' : 'User is not in allowed list'
            ];
        }
        
        return ['enabled' => true, 'reason' => 'No specific rollout conditions'];
    }

    private function evaluatePercentageRollout(int $percentage, array $context): bool
    {
        if ($percentage <= 0) return false;
        if ($percentage >= 100) return true;
        
        $identifier = $context['user_id'] ?? $context['session_id'] ?? '';
        if (empty($identifier)) return false;
        
        $hash = crc32((string) $identifier);
        $bucket = abs($hash) % 100;
        
        return $bucket < $percentage;
    }

    private function evaluateUserListRollout(array $rolloutValue, array $context): bool
    {
        $userEmail = $context['user_email'] ?? $context['email'] ?? '';
        $userRole = $context['user_role'] ?? $context['role'] ?? '';
        
        if (isset($rolloutValue['users']) && in_array($userEmail, $rolloutValue['users'], true)) {
            return true;
        }
        
        if (isset($rolloutValue['roles']) && in_array($userRole, $rolloutValue['roles'], true)) {
            return true;
        }
        
        return false;
    }

    private function evaluateCondition(array $condition, array $context): bool
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? null;
        $value = $condition['value'] ?? null;

        if (!$field || !$operator) {
            return false;
        }

        $contextValue = $this->getNestedValue($context, $field);

        return match ($operator) {
            'equals' => $contextValue === $value,
            'not_equals' => $contextValue !== $value,
            'in' => is_array($value) && in_array($contextValue, $value, true),
            'not_in' => is_array($value) && !in_array($contextValue, $value, true),
            'greater_than' => is_numeric($contextValue) && is_numeric($value) && $contextValue > $value,
            'less_than' => is_numeric($contextValue) && is_numeric($value) && $contextValue < $value,
            'greater_than_or_equal' => is_numeric($contextValue) && is_numeric($value) && $contextValue >= $value,
            'less_than_or_equal' => is_numeric($contextValue) && is_numeric($value) && $contextValue <= $value,
            'contains' => is_string($contextValue) && is_string($value) && str_contains($contextValue, $value),
            'starts_with' => is_string($contextValue) && is_string($value) && str_starts_with($contextValue, $value),
            'ends_with' => is_string($contextValue) && is_string($value) && str_ends_with($contextValue, $value),
            'regex' => is_string($contextValue) && is_string($value) && preg_match($value, $contextValue),
            'percentage' => $this->evaluatePercentage($contextValue, $value, $context),
            default => false,
        };
    }

    private function evaluatePercentage(mixed $contextValue, mixed $percentage, array $context): bool
    {
        if (!is_numeric($percentage) || $percentage < 0 || $percentage > 100) {
            return false;
        }

        $identifier = $context['user_id'] ?? $context['session_id'] ?? $contextValue ?? '';
        $hash = crc32((string) $identifier);
        $bucket = abs($hash) % 100;

        return $bucket < $percentage;
    }

    private function getNestedValue(array $array, string $key): mixed
    {
        $keys = explode('.', $key);
        $value = $array;

        foreach ($keys as $nestedKey) {
            if (!is_array($value) || !array_key_exists($nestedKey, $value)) {
                return null;
            }
            $value = $value[$nestedKey];
        }

        return $value;
    }
}
