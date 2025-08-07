<?php

declare(strict_types=1);

namespace Modules\AI\Tests\Unit\Services;

use Tests\TestCase;
use Modules\AI\App\Services\AICreditService;
use Modules\AI\App\Models\AICreditPackage;
use Modules\AI\App\Models\AICreditPurchase;
use Modules\AI\App\Models\AICreditUsage;
use Modules\AI\App\Exceptions\AICreditException;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class AICreditServiceTest extends TestCase
{
    use RefreshDatabase;

    private AICreditService $creditService;
    private User $user;
    private Tenant $tenant;
    private AICreditPackage $package;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->creditService = app(AICreditService::class);
        $this->user = User::factory()->create();
        $this->tenant = Tenant::factory()->create();
        $this->package = AICreditPackage::factory()->create([
            'credit_amount' => 1000,
            'price' => 10.00,
            'is_active' => true,
        ]);
    }

    /**
     * Test credit purchase functionality
     */
    public function test_can_purchase_credits(): void
    {
        $purchase = $this->creditService->purchaseCredits(
            $this->user,
            $this->package,
            ['payment_method' => 'test']
        );

        $this->assertInstanceOf(AICreditPurchase::class, $purchase);
        $this->assertEquals(1000, $purchase->credit_amount);
        $this->assertEquals('completed', $purchase->status);
        $this->assertEquals($this->user->id, $purchase->user_id);
    }

    /**
     * Test credit usage tracking
     */
    public function test_can_track_credit_usage(): void
    {
        // First purchase credits
        $purchase = $this->creditService->purchaseCredits(
            $this->user,
            $this->package
        );

        // Then use some credits
        $usage = $this->creditService->recordCreditUsage(
            tenantId: $this->tenant->id,
            userId: $this->user->id,
            creditsUsed: 50.0,
            usageType: 'chat',
            featureSlug: 'ai-chat',
            metadata: ['test' => true]
        );

        $this->assertInstanceOf(AICreditUsage::class, $usage);
        $this->assertEquals(50.0, $usage->credit_used);
        $this->assertEquals('chat', $usage->usage_type);
        $this->assertEquals('ai-chat', $usage->feature_slug);
    }

    /**
     * Test tenant credit balance calculation
     */
    public function test_can_calculate_tenant_credit_balance(): void
    {
        // Purchase 1000 credits
        $this->creditService->purchaseCredits(
            $this->user,
            $this->package
        );

        // Use 200 credits
        $this->creditService->recordCreditUsage(
            tenantId: $this->tenant->id,
            userId: $this->user->id,
            creditsUsed: 200.0,
            usageType: 'chat',
            featureSlug: 'ai-chat'
        );

        $balance = $this->creditService->getTenantCreditBalance($this->tenant->id);

        $this->assertEquals(1000, $balance['total_purchased']);
        $this->assertEquals(200, $balance['total_used']);
        $this->assertEquals(800, $balance['remaining_balance']);
    }

    /**
     * Test insufficient credits exception
     */
    public function test_throws_exception_for_insufficient_credits(): void
    {
        $this->expectException(AICreditException::class);
        
        // Try to use credits without purchasing
        $this->creditService->recordCreditUsage(
            tenantId: $this->tenant->id,
            userId: $this->user->id,
            creditsUsed: 100.0,
            usageType: 'chat',
            featureSlug: 'ai-chat',
            throwOnInsufficientCredits: true
        );
    }

    /**
     * Test credit forecasting
     */
    public function test_can_forecast_credit_needs(): void
    {
        // Purchase and use credits over time
        $this->creditService->purchaseCredits($this->user, $this->package);
        
        // Record multiple usages
        for ($i = 0; $i < 5; $i++) {
            $this->creditService->recordCreditUsage(
                tenantId: $this->tenant->id,
                userId: $this->user->id,
                creditsUsed: 20.0,
                usageType: 'chat',
                featureSlug: 'ai-chat'
            );
        }

        $forecast = $this->creditService->forecastCreditNeeds(
            $this->tenant->id,
            30 // Next 30 days
        );

        $this->assertArrayHasKey('predicted_usage', $forecast);
        $this->assertArrayHasKey('recommended_purchase', $forecast);
        $this->assertArrayHasKey('confidence_score', $forecast);
    }

    /**
     * Test provider cost optimization
     */
    public function test_can_optimize_provider_costs(): void
    {
        $optimization = $this->creditService->optimizeProviderCosts(
            $this->tenant->id,
            'chat',
            1000 // Estimated tokens
        );

        $this->assertArrayHasKey('recommended_provider', $optimization);
        $this->assertArrayHasKey('estimated_cost', $optimization);
        $this->assertArrayHasKey('savings_potential', $optimization);
    }

    /**
     * Test usage analytics
     */
    public function test_can_get_usage_analytics(): void
    {
        // Create some usage data
        $this->creditService->purchaseCredits($this->user, $this->package);
        
        for ($i = 0; $i < 10; $i++) {
            $this->creditService->recordCreditUsage(
                tenantId: $this->tenant->id,
                userId: $this->user->id,
                creditsUsed: rand(10, 50),
                usageType: ['chat', 'image', 'text'][rand(0, 2)],
                featureSlug: 'ai-feature-' . $i
            );
        }

        $analytics = $this->creditService->getUsageAnalytics(
            $this->tenant->id,
            now()->subDays(30),
            now()
        );

        $this->assertArrayHasKey('total_usage', $analytics);
        $this->assertArrayHasKey('by_type', $analytics);
        $this->assertArrayHasKey('by_feature', $analytics);
        $this->assertArrayHasKey('daily_average', $analytics);
        $this->assertArrayHasKey('peak_usage', $analytics);
    }

    /**
     * Test admin credit adjustment
     */
    public function test_admin_can_adjust_credits(): void
    {
        $adminUser = User::factory()->create(['is_admin' => true]);
        
        // Add credits
        $result = $this->creditService->addCreditsToTenant(
            tenantId: $this->tenant->id,
            amount: 500.0,
            reason: 'Test bonus',
            adminUserId: $adminUser->id
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Credits added successfully', $result->message);

        $balance = $this->creditService->getTenantCreditBalance($this->tenant->id);
        $this->assertEquals(500, $balance['remaining_balance']);

        // Deduct credits
        $result = $this->creditService->deductCreditsFromTenant(
            tenantId: $this->tenant->id,
            amount: 100.0,
            reason: 'Test deduction',
            adminUserId: $adminUser->id
        );

        $this->assertTrue($result->success);
        
        $balance = $this->creditService->getTenantCreditBalance($this->tenant->id);
        $this->assertEquals(400, $balance['remaining_balance']);
    }

    /**
     * Test cache functionality
     */
    public function test_credit_balance_is_cached(): void
    {
        $this->creditService->purchaseCredits($this->user, $this->package);
        
        // First call should cache
        $balance1 = $this->creditService->getTenantCreditBalance($this->tenant->id);
        
        // Second call should use cache
        $balance2 = $this->creditService->getTenantCreditBalance($this->tenant->id);
        
        $this->assertEquals($balance1, $balance2);
        
        // Verify cache exists
        $cacheKey = "tenant_credit_balance:{$this->tenant->id}";
        $this->assertTrue(Cache::has($cacheKey));
    }

    /**
     * Test market average cost calculation
     */
    public function test_can_calculate_market_average_cost(): void
    {
        // Create multiple purchases with different costs
        AICreditPurchase::factory()->count(5)->create([
            'credit_amount' => 1000,
            'price_paid' => 10.00,
            'status' => 'completed',
        ]);

        AICreditPurchase::factory()->count(3)->create([
            'credit_amount' => 500,
            'price_paid' => 6.00,
            'status' => 'completed',
        ]);

        $marketAverage = $this->creditService->calculateMarketAverageCostPerCredit();
        
        $this->assertIsFloat($marketAverage);
        $this->assertGreaterThan(0, $marketAverage);
    }

    /**
     * Test credit expiry detection
     */
    public function test_can_detect_expired_credits(): void
    {
        // Create old purchase
        $oldPurchase = AICreditPurchase::factory()->create([
            'credit_amount' => 1000,
            'purchased_at' => now()->subYear()->subDay(),
            'status' => 'completed',
        ]);

        $this->assertTrue($oldPurchase->isExpired());

        // Create recent purchase
        $recentPurchase = AICreditPurchase::factory()->create([
            'credit_amount' => 1000,
            'purchased_at' => now()->subDays(30),
            'status' => 'completed',
        ]);

        $this->assertFalse($recentPurchase->isExpired());
    }
}