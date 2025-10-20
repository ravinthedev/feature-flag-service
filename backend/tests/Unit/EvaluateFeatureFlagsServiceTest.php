<?php

namespace Tests\Unit;

use App\Domain\FeatureFlags\EvaluateFeatureFlagsService;
use App\Domain\FeatureFlags\EvaluationResult;
use App\Domain\FeatureFlags\FeatureFlagEvaluator;
use App\Domain\FeatureFlags\FeatureFlagRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Mockery;
use PHPUnit\Framework\TestCase;

class EvaluateFeatureFlagsServiceTest extends TestCase
{
    private FeatureFlagRepositoryInterface $repository;
    private FeatureFlagEvaluator $evaluator;
    private EvaluateFeatureFlagsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(FeatureFlagRepositoryInterface::class);
        $this->evaluator = Mockery::mock(FeatureFlagEvaluator::class);
        $this->service = new EvaluateFeatureFlagsService($this->repository, $this->evaluator);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_evaluate_flag_uses_cache(): void
    {
        $result = new EvaluationResult('test-flag', true, 'test');

        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) use ($result) {
                return $result;
            });

        $actual = $this->service->evaluateFlag('test-flag');
        $this->assertTrue($actual->isEnabled());
    }

    public function test_is_enabled_returns_boolean(): void
    {
        $result = new EvaluationResult('test-flag', true, 'test');

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($result);

        $isEnabled = $this->service->isEnabled('test-flag');
        $this->assertTrue($isEnabled);
    }
}
