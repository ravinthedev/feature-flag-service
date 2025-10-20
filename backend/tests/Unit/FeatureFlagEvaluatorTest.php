<?php

namespace Tests\Unit;

use App\Domain\FeatureFlags\EvaluationResult;
use App\Domain\FeatureFlags\FeatureFlag;
use App\Domain\FeatureFlags\FeatureFlagEvaluator;
use App\Domain\FeatureFlags\FeatureFlagRepositoryInterface;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

class FeatureFlagEvaluatorTest extends TestCase
{
    private FeatureFlagRepositoryInterface $repository;
    private FeatureFlagEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(FeatureFlagRepositoryInterface::class);
        $this->evaluator = new FeatureFlagEvaluator($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_false_when_feature_flag_not_found(): void
    {
        $this->repository->shouldReceive('findByKey')
            ->with('non-existent-flag')
            ->andReturn(null);

        $result = $this->evaluator->evaluate('non-existent-flag');

        $this->assertFalse($result->isEnabled());
        $this->assertEquals('Feature flag not found', $result->reason());
        $this->assertEquals('non-existent-flag', $result->key());
    }

    public function test_returns_false_when_feature_flag_is_disabled(): void
    {
        $flag = new FeatureFlag(
            key: 'disabled-flag',
            name: 'Disabled Flag',
            isEnabled: false
        );

        $this->repository->shouldReceive('findByKey')
            ->with('disabled-flag')
            ->andReturn($flag);

        $result = $this->evaluator->evaluate('disabled-flag');

        $this->assertFalse($result->isEnabled());
        $this->assertEquals('Feature flag is disabled', $result->reason());
    }

    public function test_returns_true_for_simple_boolean_rollout(): void
    {
        $flag = new FeatureFlag(
            key: 'simple-flag',
            name: 'Simple Flag',
            isEnabled: true
        );

        $this->repository->shouldReceive('findByKey')
            ->with('simple-flag')
            ->andReturn($flag);

        $result = $this->evaluator->evaluate('simple-flag');

        $this->assertTrue($result->isEnabled());
        $this->assertEquals('Boolean rollout - enabled', $result->reason());
    }

    public function test_evaluates_percentage_rollout(): void
    {
        $flag = new FeatureFlag(
            key: 'percentage-flag',
            name: 'Percentage Flag',
            isEnabled: true,
            conditions: ['percentage' => 100]
        );

        $this->repository->shouldReceive('findByKey')
            ->with('percentage-flag')
            ->andReturn($flag);

        $result = $this->evaluator->evaluate('percentage-flag', ['user_id' => 'test-user']);
        $this->assertTrue($result->isEnabled());
    }

    public function test_evaluates_user_list_rollout(): void
    {
        $flag = new FeatureFlag(
            key: 'user-list-flag',
            name: 'User List Flag',
            isEnabled: true,
            conditions: ['users' => ['admin@example.com']]
        );

        $this->repository->shouldReceive('findByKey')
            ->with('user-list-flag')
            ->andReturn($flag);

        $result = $this->evaluator->evaluate('user-list-flag', ['user_email' => 'admin@example.com']);
        $this->assertTrue($result->isEnabled());
    }

    public function test_evaluates_scheduled_rollout(): void
    {
        $past = new DateTimeImmutable('-1 day');
        $flag = new FeatureFlag(
            key: 'expired-flag',
            name: 'Expired Flag',
            isEnabled: true,
            endsAt: $past
        );

        $this->repository->shouldReceive('findByKey')
            ->with('expired-flag')
            ->andReturn($flag);

        $result = $this->evaluator->evaluate('expired-flag');
        $this->assertFalse($result->isEnabled());
    }
}
