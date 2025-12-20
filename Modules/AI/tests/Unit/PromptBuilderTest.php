<?php

declare(strict_types=1);

namespace Modules\AI\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\AI\PromptBuilder;

/**
 * PromptBuilder Test Suite
 *
 * NOTLAR:
 * - Bu testler PURE UNIT testlerdir (DB/tenant dependency YOK)
 * - Sadece PromptBuilder'ın core mantığını test eder
 * - Tenant servisleri çağrılmaz (mock edilir)
 * - Integration testleri ayrı dosyada yapılmalı
 *
 * @package Modules\AI\Tests\Unit
 */
class PromptBuilderTest extends TestCase
{
    /**
     * Test: Validation minimum uzunluk kontrolü
     */
    public function test_validation_enforces_minimum_length(): void
    {
        // Mock config
        config(['ai-tenants.validation.min_prompt_length' => 1000]);

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
        // Mock config
        config(['ai-tenants.validation.min_prompt_length' => 1000]);

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
        // Mock config
        config(['ai-tenants.validation.min_prompt_length' => 1000]);

        // Geçerli prompt (uzun + kritik kelimeler içeren)
        $validPrompt = str_repeat("Test content. ", 50) . " ULTRA KRİTİK KURAL: Test. " . str_repeat("More content. ", 50);

        // Act
        $result = PromptBuilder::validate($validPrompt, 2);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test: Config mapping çalışıyor
     */
    public function test_config_mapping_works(): void
    {
        // Mock config
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
     * Test: Generic tenant (kritik keyword kontrolü yapılmaz)
     */
    public function test_validation_skips_critical_keywords_for_generic_tenant(): void
    {
        // Mock config
        config(['ai-tenants.validation.min_prompt_length' => 1000]);

        // Uzun prompt ama kritik keyword yok (tenant 999 için OK)
        $longPrompt = str_repeat("Generic content. ", 100);

        // Act
        $result = PromptBuilder::validate($longPrompt, 999);

        // Assert
        $this->assertTrue($result); // Tenant 999 için kritik keyword kontrolü yok
    }

    /**
     * Test: Empty string validation
     */
    public function test_validation_fails_for_empty_string(): void
    {
        // Mock config
        config(['ai-tenants.validation.min_prompt_length' => 1000]);

        $emptyPrompt = "";

        // Act
        $result = PromptBuilder::validate($emptyPrompt, 2);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test: Exact minimum length
     */
    public function test_validation_fails_for_exact_minimum_length(): void
    {
        // Mock config
        config(['ai-tenants.validation.min_prompt_length' => 50]);

        $exactPrompt = str_repeat("X", 50); // Exactly 50 chars

        // Act
        $result = PromptBuilder::validate($exactPrompt, 999);

        // Assert - Should fail because < not <=
        $this->assertFalse($result);
    }

    /**
     * Test: Just above minimum length
     */
    public function test_validation_passes_for_above_minimum_length(): void
    {
        // Mock config
        config(['ai-tenants.validation.min_prompt_length' => 50]);

        $validPrompt = str_repeat("X", 51); // 51 chars (above minimum)

        // Act
        $result = PromptBuilder::validate($validPrompt, 999);

        // Assert
        $this->assertTrue($result);
    }
}
