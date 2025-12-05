<?php

namespace Modules\Subscription\Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Modules\Subscription\App\Models\SubscriptionPlan;
use Modules\Subscription\App\Models\Subscription;
use Modules\Subscription\App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SubscriptionService::class);
    }

    /** @test */
    public function it_can_get_trial_plan()
    {
        // Arrange: Trial plan oluştur
        $trialPlan = SubscriptionPlan::factory()->create([
            'is_trial' => true,
            'is_active' => true,
        ]);

        // Act
        $result = $this->service->getTrialPlan();

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($trialPlan->subscription_plan_id, $result->subscription_plan_id);
        $this->assertTrue($result->is_trial);
    }

    /** @test */
    public function it_returns_null_when_no_trial_plan_exists()
    {
        // Act
        $result = $this->service->getTrialPlan();

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_get_trial_duration()
    {
        // Arrange
        $trialPlan = SubscriptionPlan::factory()->create([
            'is_trial' => true,
            'is_active' => true,
            'billing_cycles' => [
                'trial-7-days' => [
                    'name' => ['tr' => '7 Gün', 'en' => '7 Days'],
                    'duration_days' => 7,
                    'price' => 0,
                ],
            ],
        ]);

        // Act
        $duration = $this->service->getTrialDuration();

        // Assert
        $this->assertEquals(7, $duration);
    }

    /** @test */
    public function it_creates_trial_for_new_user()
    {
        // Arrange
        $user = User::factory()->create(['has_used_trial' => false]);
        $trialPlan = SubscriptionPlan::factory()->create([
            'is_trial' => true,
            'is_active' => true,
            'billing_cycles' => [
                'trial-7-days' => [
                    'name' => ['tr' => '7 Gün'],
                    'duration_days' => 7,
                    'price' => 0,
                ],
            ],
        ]);

        // Act
        $subscription = $this->service->createTrialForUser($user);

        // Assert
        $this->assertNotNull($subscription);
        $this->assertEquals($user->id, $subscription->user_id);
        $this->assertEquals('active', $subscription->status);
        $this->assertTrue($user->fresh()->has_used_trial);
    }

    /** @test */
    public function it_does_not_create_trial_if_already_used()
    {
        // Arrange
        $user = User::factory()->create(['has_used_trial' => true]);
        SubscriptionPlan::factory()->create(['is_trial' => true, 'is_active' => true]);

        // Act
        $subscription = $this->service->createTrialForUser($user);

        // Assert
        $this->assertNull($subscription);
    }

    /** @test */
    public function it_gets_device_limit_from_user_override()
    {
        // Arrange
        $user = User::factory()->create(['device_limit' => 5]);

        // Act
        $limit = $this->service->getDeviceLimit($user);

        // Assert
        $this->assertEquals(5, $limit);
    }

    /** @test */
    public function it_checks_user_access_unlimited_for_active_subscription()
    {
        // Arrange
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create(['is_trial' => true]);
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->subscription_plan_id,
            'status' => 'active',
            'current_period_end' => now()->addDays(7),
        ]);

        // Act
        $access = $this->service->checkUserAccess($user);

        // Assert
        $this->assertEquals('unlimited', $access['status']);
        $this->assertTrue($access['is_trial']);
    }

    /** @test */
    public function it_checks_user_access_preview_for_expired_subscription()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $access = $this->service->checkUserAccess($user);

        // Assert
        $this->assertEquals('preview', $access['status']);
        $this->assertEquals(30, $access['duration']);
    }
}
