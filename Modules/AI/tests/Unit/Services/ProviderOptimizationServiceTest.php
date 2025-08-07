<?php

declare(strict_types=1);

namespace Modules\AI\Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Cache, Redis};
use Modules\AI\App\Services\{ProviderOptimizationService, AICreditService};
use Modules\AI\App\Models\{AIProvider, AICreditUsage};
use Modules\AI\App\Exceptions\ProviderMultiplierException;

/**
 * Provider Optimization Service Test Suite
 * 
 * Tests advanced provider optimization functionality including:
 * - Real-time provider selection
 * - Performance metrics calculation
 * - Cost optimization analysis
 * - Provider switching recommendations
 */
class ProviderOptimizationServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private ProviderOptimizationService $service;
    private $mockCreditService;
    private AIProvider $testProvider;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockCreditService = Mockery::mock(AICreditService::class);
        $this->service = new ProviderOptimizationService($this->mockCreditService);
        
        // Create test provider
        $this->testProvider = AIProvider::factory()->create([
            'name' => 'openai',
            'display_name' => 'OpenAI',
            'is_active' => true,
            'is_default' => true,
            'priority' => 10,
            'token_cost_multiplier' => 1.0,
            'average_response_time' => 2.5,
        ]);
        
        // Clear caches
        Cache::flush();
        Redis::flushall();
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    /** @test */
    public function it_can_get_optimal_provider_for_feature()
    {
        // Create additional providers
        AIProvider::factory()->create([
            'name' => 'claude',
            'display_name' => 'Claude',
            'is_active' => true,
            'priority' => 8,
            'token_cost_multiplier' => 1.2,
        ]);
        
        AIProvider::factory()->create([
            'name' => 'gemini',
            'display_name' => 'Gemini',
            'is_active' => true,
            'priority' => 6,
            'token_cost_multiplier' => 0.8,
        ]);
        
        $result = $this->service->getOptimalProvider('seo_analysis', [
            'max_response_time' => 5.0,
            'min_quality_score' => 0.7,
        ]);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('provider', $result);
        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('reasoning', $result);
        $this->assertArrayHasKey('alternatives', $result);
        $this->assertArrayHasKey('performance_metrics', $result);
        $this->assertArrayHasKey('cost_estimate', $result);
        $this->assertArrayHasKey('confidence_level', $result);
        
        $this->assertInstanceOf(AIProvider::class, $result['provider']);
        $this->assertIsFloat($result['score']);
        $this->assertGreaterThanOrEqual(0, $result['score']);
        $this->assertLessThanOrEqual(1, $result['score']);
    }
    
    /** @test */
    public function it_returns_fallback_provider_when_optimization_fails()
    {
        // Mock an exception in the optimization process
        Cache::shouldReceive('remember')->andThrow(new \Exception('Test exception'));
        
        $result = $this->service->getOptimalProvider('translation');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('provider', $result);
        $this->assertEquals('low', $result['confidence_level']);
        $this->assertStringContains('fallback', strtolower($result['reasoning']));
    }
    
    /** @test */
    public function it_can_get_realtime_performance_metrics()
    {
        // Mock Redis data
        Redis::shouldReceive('get')
            ->with("provider_metrics:{$this->testProvider->id}")
            ->andReturn(json_encode([
                'response_time' => 0.85,
                'success_rate' => 0.95,
                'cost_efficiency' => 0.80,
                'quality_score' => 0.88,
                'availability' => 0.99,
                'usage_count' => 150,
                'last_used' => now()->toISOString(),
            ]));
        
        $metrics = $this->service->getRealtimePerformanceMetrics();
        
        $this->assertIsArray($metrics);
        $this->assertArrayHasKey($this->testProvider->name, $metrics);
        $this->assertArrayHasKey('_analysis', $metrics);
        
        $providerMetrics = $metrics[$this->testProvider->name];
        $this->assertArrayHasKey('response_time', $providerMetrics);
        $this->assertArrayHasKey('success_rate', $providerMetrics);
        $this->assertArrayHasKey('cost_efficiency', $providerMetrics);
        $this->assertArrayHasKey('quality_score', $providerMetrics);
        $this->assertArrayHasKey('availability', $providerMetrics);
    }
    
    /** @test */
    public function it_can_perform_cost_optimization_analysis()
    {
        // Create test usage data
        AICreditUsage::factory()->count(10)->create([
            'tenant_id' => 1,
            'feature_slug' => 'seo_analysis',
            'credits_used' => 5,
            'created_at' => now()->subDays(rand(1, 30)),
        ]);
        
        $analysis = $this->service->performCostOptimizationAnalysis(1, 30);
        
        $this->assertIsArray($analysis);
        $this->assertArrayHasKey('current_costs', $analysis);
        $this->assertArrayHasKey('optimization_opportunities', $analysis);
        $this->assertArrayHasKey('predictions', $analysis);
        $this->assertArrayHasKey('potential_savings', $analysis);
        $this->assertArrayHasKey('recommendations', $analysis);
        $this->assertArrayHasKey('risk_analysis', $analysis);
        $this->assertArrayHasKey('generated_at', $analysis);
        
        // Verify current costs structure
        $currentCosts = $analysis['current_costs'];
        $this->assertArrayHasKey('total', $currentCosts);
        $this->assertArrayHasKey('average_daily', $currentCosts);
        $this->assertArrayHasKey('by_provider', $currentCosts);
        $this->assertArrayHasKey('trend', $currentCosts);
        
        $this->assertIsNumeric($currentCosts['total']);
        $this->assertIsNumeric($currentCosts['average_daily']);
    }
    
    /** @test */
    public function it_can_generate_provider_switching_recommendations()
    {
        // Create alternative providers
        $claudeProvider = AIProvider::factory()->create([
            'name' => 'claude',
            'display_name' => 'Claude',
            'is_active' => true,
            'token_cost_multiplier' => 1.2,
            'average_response_time' => 1.8,
        ]);
        
        $geminiProvider = AIProvider::factory()->create([
            'name' => 'gemini',
            'display_name' => 'Gemini',
            'is_active' => true,
            'token_cost_multiplier' => 0.8,
            'average_response_time' => 3.2,
        ]);
        
        $recommendations = $this->service->getProviderSwitchingRecommendations(
            'openai',
            'content_writing',
            1
        );
        
        $this->assertIsArray($recommendations);
        $this->assertArrayHasKey('current_provider', $recommendations);
        $this->assertArrayHasKey('current_metrics', $recommendations);
        $this->assertArrayHasKey('recommendations', $recommendations);
        $this->assertArrayHasKey('should_switch', $recommendations);
        $this->assertArrayHasKey('analysis_timestamp', $recommendations);
        
        $this->assertEquals('openai', $recommendations['current_provider']);
        $this->assertIsBool($recommendations['should_switch']);
        $this->assertIsArray($recommendations['recommendations']);
        
        // If there are recommendations, verify their structure
        foreach ($recommendations['recommendations'] as $recommendation) {
            $this->assertArrayHasKey('provider', $recommendation);
            $this->assertArrayHasKey('benefit_score', $recommendation);
            $this->assertArrayHasKey('cost_reduction', $recommendation);
            $this->assertArrayHasKey('performance_gain', $recommendation);
            $this->assertArrayHasKey('switching_cost', $recommendation);
            $this->assertArrayHasKey('recommendation_strength', $recommendation);
            $this->assertArrayHasKey('reasoning', $recommendation);
        }
    }
    
    /** @test */
    public function it_can_analyze_performance_trends()
    {
        $trends = $this->service->analyzePerformanceTrends(7);
        
        $this->assertIsArray($trends);
        $this->assertArrayHasKey('period_days', $trends);
        $this->assertArrayHasKey('provider_trends', $trends);
        $this->assertArrayHasKey('overall_health', $trends);
        $this->assertArrayHasKey('critical_alerts', $trends);
        $this->assertArrayHasKey('optimization_suggestions', $trends);
        $this->assertArrayHasKey('generated_at', $trends);
        
        $this->assertEquals(7, $trends['period_days']);
        $this->assertIsArray($trends['provider_trends']);
        $this->assertIsArray($trends['critical_alerts']);
        $this->assertIsArray($trends['optimization_suggestions']);
        
        // Verify provider trends structure
        foreach ($trends['provider_trends'] as $providerName => $trend) {
            $this->assertArrayHasKey('provider_id', $trend);
            $this->assertArrayHasKey('display_name', $trend);
            $this->assertArrayHasKey('current_performance', $trend);
            $this->assertArrayHasKey('trend_direction', $trend);
            $this->assertArrayHasKey('trend_strength', $trend);
            $this->assertArrayHasKey('anomalies', $trend);
            $this->assertArrayHasKey('forecast', $trend);
            $this->assertArrayHasKey('health_score', $trend);
            $this->assertArrayHasKey('alerts', $trend);
        }
    }
    
    /** @test */
    public function it_can_get_load_balanced_provider()
    {
        // Mock Redis for load distribution
        Redis::shouldReceive('hgetall')
            ->with('provider_load_distribution')
            ->andReturn([
                (string)$this->testProvider->id => '10',
            ]);
        
        Redis::shouldReceive('hget')
            ->andReturn('100'); // capacity
        
        Redis::shouldReceive('hincrby')
            ->andReturn(11); // incremented load
            
        $result = $this->service->getLoadBalancedProvider('seo_analysis');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('provider', $result);
        $this->assertArrayHasKey('current_load', $result);
        $this->assertArrayHasKey('capacity_remaining', $result);
        $this->assertArrayHasKey('load_balanced', $result);
        $this->assertArrayHasKey('balancing_strategy', $result);
        
        $this->assertTrue($result['load_balanced']);
        $this->assertIsNumeric($result['current_load']);
    }
    
    /** @test */
    public function it_can_generate_actionable_insights()
    {
        // Create usage data that would trigger insights
        AICreditUsage::factory()->count(50)->create([
            'tenant_id' => 1,
            'credits_used' => rand(10, 50),
            'feature_slug' => 'expensive_analysis',
            'created_at' => now()->subDays(rand(1, 30)),
        ]);
        
        $insights = $this->service->generateActionableInsights(1);
        
        $this->assertIsArray($insights);
        $this->assertArrayHasKey('insights', $insights);
        $this->assertArrayHasKey('summary', $insights);
        $this->assertArrayHasKey('generated_at', $insights);
        
        $this->assertIsArray($insights['insights']);
        $this->assertLessThanOrEqual(10, count($insights['insights']));
        
        // Verify insight structure
        foreach ($insights['insights'] as $insight) {
            $this->assertArrayHasKey('type', $insight);
            $this->assertArrayHasKey('priority', $insight);
            $this->assertArrayHasKey('title', $insight);
            $this->assertArrayHasKey('description', $insight);
            $this->assertArrayHasKey('action', $insight);
            
            $this->assertContains($insight['type'], ['cost_saving', 'performance', 'usage_pattern']);
            $this->assertContains($insight['priority'], ['critical', 'high', 'medium', 'low']);
        }
    }
    
    /** @test */
    public function it_handles_redis_failures_gracefully()
    {
        // Mock Redis to throw exception
        Redis::shouldReceive('get')->andThrow(new \Exception('Redis connection failed'));
        
        $result = $this->service->getOptimalProvider('translation');
        
        // Should still return a result (fallback behavior)
        $this->assertIsArray($result);
        $this->assertArrayHasKey('provider', $result);
    }
    
    /** @test */
    public function it_validates_feature_type_parameter()
    {
        $result = $this->service->getOptimalProvider('invalid_feature_type');
        
        // Should handle invalid feature types gracefully
        $this->assertIsArray($result);
        $this->assertArrayHasKey('provider', $result);
    }
    
    /** @test */
    public function it_respects_cost_ceiling_requirements()
    {
        // Create expensive and cheap providers
        $expensiveProvider = AIProvider::factory()->create([
            'name' => 'expensive',
            'token_cost_multiplier' => 5.0,
            'is_active' => true,
        ]);
        
        $cheapProvider = AIProvider::factory()->create([
            'name' => 'cheap',
            'token_cost_multiplier' => 0.5,
            'is_active' => true,
        ]);
        
        $result = $this->service->getOptimalProvider('translation', [
            'max_cost' => 1.0, // Low cost ceiling
        ]);
        
        // Should select provider that meets cost requirement
        $this->assertLessThanOrEqual(1.0, $result['cost_estimate']);
    }
    
    /** @test */
    public function it_throws_exception_when_no_providers_meet_requirements()
    {
        $this->expectException(ProviderMultiplierException::class);
        
        $this->service->getOptimalProvider('translation', [
            'min_performance' => 0.99, // Impossible requirement
        ]);
    }
    
    /** @test */
    public function it_caches_performance_metrics_appropriately()
    {
        // First call should hit the database
        $metrics1 = $this->service->getRealtimePerformanceMetrics();
        
        // Second call should use cache
        $metrics2 = $this->service->getRealtimePerformanceMetrics();
        
        $this->assertEquals($metrics1, $metrics2);
    }
}