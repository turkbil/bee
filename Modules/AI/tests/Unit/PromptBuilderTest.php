<?php

declare(strict_types=1);

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Services\AI\PromptBuilder;
use Illuminate\Support\Facades\Log;

/**
 * PromptBuilder Test Suite
 *
 * Bu testler PromptBuilder'ın doğru çalıştığını garantiler:
 * - Tenant-specific prompt'ları doğru çekiyor mu?
 * - Validation kuralları çalışıyor mu?
 * - Exception'lar doğru atılıyor mu?
 * - Config mapping çalışıyor mu?
 *
 * @package Tests\Unit\AI
 */
class PromptBuilderTest extends TestCase
{
    /**
     * Test: Tenant 2 için prompt oluşturma
     */
    public function test_builds_prompt_for_tenant_2(): void
    {
        // Config mock
        config([
            'ai-tenants.prompt_services.2' => \Modules\AI\App\Services\Tenant\Tenant2PromptService::class,
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // Act
        $prompt = PromptBuilder::buildSystemPrompt(2, '');

        // Assert
        $this->assertNotEmpty($prompt);
        $this->assertGreaterThan(1000, strlen($prompt));
        $this->assertStringContainsString('ULTRA KRİTİK', $prompt);
    }

    /**
     * Test: Tenant 1001 için prompt oluşturma
     */
    public function test_builds_prompt_for_tenant_1001(): void
    {
        // Config mock
        config([
            'ai-tenants.prompt_services.1001' => \Modules\AI\App\Services\Tenant\Tenant1001PromptService::class,
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // Act
        $prompt = PromptBuilder::buildSystemPrompt(1001, '');

        // Assert
        $this->assertNotEmpty($prompt);
        $this->assertGreaterThan(1000, strlen($prompt));
    }

    /**
     * Test: Generic prompt (tenant config yok)
     */
    public function test_falls_back_to_generic_prompt(): void
    {
        // Config mock (tenant 999 yok)
        config([
            'ai-tenants.prompt_services' => [],
            'ai-tenants.validation.min_prompt_length' => 50, // Generic için düşük threshold
        ]);

        // Act
        $prompt = PromptBuilder::buildSystemPrompt(999, '');

        // Assert
        $this->assertNotEmpty($prompt);
        $this->assertStringContainsString('asistanısın', $prompt);
    }

    /**
     * Test: Context ekleme
     */
    public function test_adds_context_to_prompt(): void
    {
        // Config mock
        config([
            'ai-tenants.prompt_services.2' => \Modules\AI\App\Services\Tenant\Tenant2PromptService::class,
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        $context = "Test context içeriği";

        // Act
        $prompt = PromptBuilder::buildSystemPrompt(2, $context);

        // Assert
        $this->assertStringContainsString($context, $prompt);
        $this->assertStringContainsString('BAĞLAM BİLGİLERİ', $prompt);
    }

    /**
     * Test: Minimum uzunluk validasyonu
     */
    public function test_validation_enforces_minimum_length(): void
    {
        // Config mock
        config([
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        $shortPrompt = "Çok kısa prompt";

        // Act
        $result = PromptBuilder::validate($shortPrompt, 2);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test: Tenant 2/3 kritik kurallar kontrolü
     */
    public function test_validation_checks_critical_keywords_for_tenant_2(): void
    {
        // Config mock
        config([
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // ULTRA KRİTİK kelimesi olmayan uzun prompt
        $promptWithoutCritical = str_repeat("Lorem ipsum dolor sit amet. ", 100);

        // Act
        $result = PromptBuilder::validate($promptWithoutCritical, 2);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test: Valid prompt geçer
     */
    public function test_validation_passes_for_valid_prompt(): void
    {
        // Config mock
        config([
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // Geçerli prompt (uzun + kritik kelimeler içeren)
        $validPrompt = str_repeat("Test content. ", 50) . " ULTRA KRİTİK KURAL: Test. " . str_repeat("More content. ", 50);

        // Act
        $result = PromptBuilder::validate($validPrompt, 2);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test: Exception atılması (boş prompt)
     */
    public function test_throws_exception_for_empty_prompt(): void
    {
        // Expect exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tenant prompt missing');

        // Config mock (yanlış class)
        config([
            'ai-tenants.prompt_services.999' => 'NonExistentClass',
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        // Act (should throw)
        PromptBuilder::buildSystemPrompt(999, '');
    }

    /**
     * Test: Config mapping çalışıyor
     */
    public function test_config_mapping_works(): void
    {
        // Config mock
        config([
            'ai-tenants.prompt_services' => [
                2 => \Modules\AI\App\Services\Tenant\Tenant2PromptService::class,
                3 => \Modules\AI\App\Services\Tenant\Tenant2PromptService::class,
                1001 => \Modules\AI\App\Services\Tenant\Tenant1001PromptService::class,
            ],
        ]);

        $tenant2Class = config('ai-tenants.prompt_services.2');
        $tenant1001Class = config('ai-tenants.prompt_services.1001');

        // Assert
        $this->assertEquals(\Modules\AI\App\Services\Tenant\Tenant2PromptService::class, $tenant2Class);
        $this->assertEquals(\Modules\AI\App\Services\Tenant\Tenant1001PromptService::class, $tenant1001Class);
    }

    /**
     * Test: Genel kurallar ekleniyor
     */
    public function test_adds_general_rules(): void
    {
        // Config mock
        config([
            'ai-tenants.prompt_services.2' => \Modules\AI\App\Services\Tenant\Tenant2PromptService::class,
            'ai-tenants.validation.min_prompt_length' => 1000,
        ]);

        app()->setLocale('tr');

        // Act
        $prompt = PromptBuilder::buildSystemPrompt(2, '');

        // Assert
        $this->assertStringContainsString('GENEL KURALLAR', $prompt);
        $this->assertStringContainsString('Türkçe yanıt ver', $prompt);
        $this->assertStringContainsString('Markdown formatı kullan', $prompt);
    }
}
