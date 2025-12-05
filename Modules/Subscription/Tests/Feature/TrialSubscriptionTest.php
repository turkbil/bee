<?php

namespace Modules\Subscription\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Subscription\App\Models\SubscriptionPlan;
use Modules\Subscription\App\Models\Subscription;
use Modules\Subscription\App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrialSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_gets_trial_subscription_after_registration()
    {
        // Arrange: Trial plan oluştur
        $trialPlan = SubscriptionPlan::factory()->create([
            'is_trial' => true,
            'is_active' => true,
            'billing_cycles' => [
                'trial-7-days' => [
                    'name' => ['tr' => '7 Günlük Deneme'],
                    'duration_days' => 7,
                    'price' => 0,
                ],
            ],
        ]);

        // Act: Kullanıcı kayıt ol
        $user = User::factory()->create(['has_used_trial' => false]);
        
        $subscriptionService = app(SubscriptionService::class);
        $subscription = $subscriptionService->createTrialForUser($user);

        // Assert
        $this->assertNotNull($subscription);
        $this->assertEquals('active', $subscription->status);
        $this->assertEquals(7, now()->diffInDays($subscription->current_period_end));
        $this->assertTrue($user->fresh()->has_used_trial);
    }

    /** @test */
    public function trial_subscription_expires_after_duration()
    {
        // Arrange
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create(['is_trial' => true]);
        
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->subscription_plan_id,
            'status' => 'active',
            'current_period_start' => now()->subDays(8),
            'current_period_end' => now()->subDay(), // Dün bitti
        ]);

        // Act: Cron job simülasyonu
        $subscription->update(['status' => 'expired']);

        // Assert
        $this->assertEquals('expired', $subscription->fresh()->status);
    }

    /** @test */
    public function expired_user_gets_preview_access_only()
    {
        // Arrange
        $user = User::factory()->create(['has_used_trial' => true]);

        $subscriptionService = app(SubscriptionService::class);
        
        // Act
        $access = $subscriptionService->checkUserAccess($user);

        // Assert
        $this->assertEquals('preview', $access['status']);
        $this->assertEquals(30, $access['duration']);
    }

    /** @test */
    public function premium_user_gets_unlimited_access()
    {
        // Arrange
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create([
            'is_trial' => false,
            'is_active' => true,
        ]);
        
        Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->subscription_plan_id,
            'status' => 'active',
            'current_period_end' => now()->addDays(30),
        ]);

        $subscriptionService = app(SubscriptionService::class);

        // Act
        $access = $subscriptionService->checkUserAccess($user);

        // Assert
        $this->assertEquals('unlimited', $access['status']);
        $this->assertFalse($access['is_trial']);
    }

    /** @test */
    public function device_limit_hierarchy_works_correctly()
    {
        // Arrange
        $subscriptionService = app(SubscriptionService::class);

        // Test 1: User override (en yüksek öncelik)
        $userWithOverride = User::factory()->create(['device_limit' => 5]);
        $this->assertEquals(5, $subscriptionService->getDeviceLimit($userWithOverride));

        // Test 2: Plan default (user override yoksa)
        $user = User::factory()->create(['device_limit' => null]);
        $plan = SubscriptionPlan::factory()->create(['device_limit' => 3]);
        Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->subscription_plan_id,
            'status' => 'active',
            'current_period_end' => now()->addDays(30),
        ]);
        
        $this->assertEquals(3, $subscriptionService->getDeviceLimit($user));

        // Test 3: Setting fallback (ne user ne plan varsa)
        $userNoSub = User::factory()->create(['device_limit' => null]);
        $limit = $subscriptionService->getDeviceLimit($userNoSub);
        $this->assertIsInt($limit);
    }
}
