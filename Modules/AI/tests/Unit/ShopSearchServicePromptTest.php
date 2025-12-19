<?php

declare(strict_types=1);

namespace Tests\Unit\AI;

use Tests\TestCase;
use Modules\AI\App\Services\Assistant\Modules\ShopSearchService;
use Illuminate\Support\Facades\Log;

/**
 * ShopSearchService Prompt Test Suite
 *
 * Bu testler ShopSearchService'in getPromptRules() metodunun
 * doğru çalıştığını garantiler:
 * - PromptBuilder kullanıyor mu?
 * - Validation çalışıyor mu?
 * - Tenant 2/3 için Tenant2PromptService kullanılıyor mu?
 * - Fallback mekanizması çalışıyor mu?
 *
 * @package Tests\Unit\AI
 */
class ShopSearchServicePromptTest extends TestCase
{
    /**
     * Test: Tenant 2 için doğru prompt servisi kullanılıyor
     */
    public function test_uses_prompt_builder_for_tenant_2(): void
    {
        // Mock tenant context
        $this->createTenantContext(2);

        // Config mock
        config([
            'ai-tenants.prompt_services.2' => \Modules\AI\App\Services\Tenant\Tenant2PromptService::class,
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // Act
        $service = app(ShopSearchService::class);
        $rules = $service->getPromptRules();

        // Assert
        $this->assertNotEmpty($rules);
        $this->assertStringContainsString('ULTRA KRİTİK', $rules);
    }

    /**
     * Test: Generic tenant için fallback çalışıyor
     */
    public function test_falls_back_to_generic_rules(): void
    {
        // Mock tenant context (tenant 999 - config yok)
        $this->createTenantContext(999);

        // Config mock (tenant 999 yok)
        config([
            'ai-tenants.prompt_services' => [],
            'ai-tenants.validation.min_prompt_length' => 50,
        ]);

        // Act
        $service = app(ShopSearchService::class);
        $rules = $service->getPromptRules();

        // Assert
        $this->assertNotEmpty($rules);
        $this->assertStringContainsString('SHOP ASSISTANT KURALLARI', $rules);
    }

    /**
     * Test: Validation başarısız olursa fallback
     */
    public function test_fallback_on_validation_failure(): void
    {
        // Mock tenant context
        $this->createTenantContext(2);

        // Config mock (validation fail edecek şekilde)
        config([
            'ai-tenants.prompt_services.2' => 'NonExistentClass', // Hatalı class
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // Log spy (validation hatası loglanmalı)
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'ShopSearchService: PromptBuilder failed');
            });

        // Act
        $service = app(ShopSearchService::class);
        $rules = $service->getPromptRules();

        // Assert
        $this->assertNotEmpty($rules);
        $this->assertStringContainsString('SHOP ASSISTANT KURALLARI', $rules);
    }

    /**
     * Test: PromptBuilder kullanımı loglanıyor
     */
    public function test_logs_prompt_builder_usage(): void
    {
        // Mock tenant context
        $this->createTenantContext(2);

        // Config mock
        config([
            'ai-tenants.prompt_services.2' => \Modules\AI\App\Services\Tenant\Tenant2PromptService::class,
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // Log spy
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Using validated PromptBuilder for tenant 2');
            });

        // Act
        $service = app(ShopSearchService::class);
        $service->getPromptRules();
    }

    /**
     * Test: Prompt minimum uzunluk kontrolü
     */
    public function test_prompt_meets_minimum_length(): void
    {
        // Mock tenant context
        $this->createTenantContext(2);

        // Config mock
        config([
            'ai-tenants.prompt_services.2' => \Modules\AI\App\Services\Tenant\Tenant2PromptService::class,
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // Act
        $service = app(ShopSearchService::class);
        $rules = $service->getPromptRules();

        // Assert
        $this->assertGreaterThan(1000, strlen($rules));
    }

    /**
     * Test: Tenant 3 de Tenant2PromptService kullanıyor
     */
    public function test_tenant_3_uses_tenant2_prompt_service(): void
    {
        // Mock tenant context
        $this->createTenantContext(3);

        // Config mock
        config([
            'ai-tenants.prompt_services.3' => \Modules\AI\App\Services\Tenant\Tenant2PromptService::class,
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // Act
        $service = app(ShopSearchService::class);
        $rules = $service->getPromptRules();

        // Assert
        $this->assertNotEmpty($rules);
        $this->assertStringContainsString('ULTRA KRİTİK', $rules);
    }

    /**
     * Test: PromptBuilder exception handling
     */
    public function test_handles_prompt_builder_exception(): void
    {
        // Mock tenant context
        $this->createTenantContext(2);

        // Config mock (exception tetikleyecek)
        config([
            'ai-tenants.prompt_services.2' => 'InvalidClass',
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // Log spy
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'PromptBuilder failed');
            });

        // Act (exception yakalanmalı, fallback dönmeli)
        $service = app(ShopSearchService::class);
        $rules = $service->getPromptRules();

        // Assert
        $this->assertNotEmpty($rules);
    }

    /**
     * Helper: Tenant context oluştur (mock)
     */
    private function createTenantContext(int $tenantId): void
    {
        // Mock tenant() helper
        // NOT: Gerçek tenant context oluşturmak yerine,
        // tenant('id') çağrısı için mock yapılmalı.
        // Bu Laravel tenant package'ına göre değişir.

        // Örnek (Stancl\Tenancy kullanılıyorsa):
        // $tenant = \App\Models\Tenant::find($tenantId);
        // tenancy()->initialize($tenant);

        // Basit mock (test için):
        $this->app->instance('tenant.id', $tenantId);
    }
}
