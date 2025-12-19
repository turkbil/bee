<?php

declare(strict_types=1);

namespace App\Services\AI;

use Modules\AI\App\Services\Tenant\Tenant2PromptService;
use Illuminate\Support\Facades\Log;

/**
 * 🎯 Merkezi AI Prompt Builder
 *
 * Tüm tenant'lar için AI system prompt'larını tek bir yerden yönetir.
 * Bu sınıf kullanılarak yanlış prompt servisi kullanımı ENGELLENIR!
 *
 * @package App\Services\AI
 * @version 1.0
 * @date 2025-12-20
 */
class PromptBuilder
{
    /**
     * Tenant için system prompt oluştur
     *
     * @param int $tenantId Tenant ID
     * @param string $context Modül context bilgisi
     * @return string System prompt
     * @throws \Exception Tenant prompt eksikse
     */
    public static function buildSystemPrompt(int $tenantId, string $context = ''): string
    {
        // 1. Tenant-specific prompt al (EN ÖNEMLİ!)
        $tenantPrompt = self::getTenantPrompt($tenantId);

        // 2. ZORUNLU KONTROL: Prompt boş olamaz!
        if (empty($tenantPrompt)) {
            Log::critical("🚨 CRITICAL: Tenant {$tenantId} prompt is EMPTY!");
            throw new \Exception("Tenant prompt missing for tenant {$tenantId}");
        }

        // 3. ZORUNLU KONTROL: Minimum uzunluk
        $minLength = config('ai-tenants.validation.min_prompt_length', 1000);
        if (strlen($tenantPrompt) < $minLength) {
            Log::critical("🚨 CRITICAL: Tenant {$tenantId} prompt too short! ({strlen($tenantPrompt)} < {$minLength})");
            throw new \Exception("Tenant prompt too short!");
        }

        // 4. Context ekle (varsa)
        if (!empty($context)) {
            $tenantPrompt .= "\n\n## BAĞLAM BİLGİLERİ\n{$context}";
        }

        // 5. Genel kurallar ekle (MİNİMAL!)
        $generalRules = self::getGeneralRules();
        $tenantPrompt .= "\n\n{$generalRules}";

        return $tenantPrompt;
    }

    /**
     * Tenant'a özel prompt servisini al
     *
     * @param int $tenantId
     * @return string Tenant prompt
     */
    private static function getTenantPrompt(int $tenantId): string
    {
        // Config'den prompt service class'ını al
        $promptServiceClass = config("ai-tenants.prompt_services.{$tenantId}");

        if (!$promptServiceClass) {
            Log::warning("⚠️ No prompt service configured for tenant {$tenantId}, using generic");
            return self::getGenericPrompt();
        }

        try {
            $promptService = app($promptServiceClass);

            // Service'de getPromptAsString metodu var mı kontrol et
            if (!method_exists($promptService, 'getPromptAsString')) {
                Log::error("❌ Prompt service {$promptServiceClass} has no getPromptAsString method!");
                return self::getGenericPrompt();
            }

            return $promptService->getPromptAsString();

        } catch (\Exception $e) {
            Log::error("❌ Error loading prompt service for tenant {$tenantId}: " . $e->getMessage());
            return self::getGenericPrompt();
        }
    }

    /**
     * Generic prompt (fallback)
     *
     * @return string
     */
    private static function getGenericPrompt(): string
    {
        $siteName = setting('site_name') ?? 'Site';

        return "Sen {$siteName} asistanısın.

## GÖREVLER
- Kullanıcıya yardımcı ol
- Kısa ve öz yanıtlar ver
- Emin olmadığın bilgiyi uydurma
- Markdown formatı kullan";
    }

    /**
     * Genel kurallar (tüm tenant'lar için)
     *
     * @return string
     */
    private static function getGeneralRules(): string
    {
        $locale = app()->getLocale();

        $langInstruction = match($locale) {
            'tr' => 'Türkçe yanıt ver.',
            'en' => 'Respond in English.',
            'de' => 'Antworte auf Deutsch.',
            default => 'Respond in the same language as the user message.',
        };

        return "## GENEL KURALLAR
- {$langInstruction}
- Markdown formatı kullan
- Sadece context'teki bilgileri kullan
- Emin olmadığın bilgiyi ASLA uydurma";
    }

    /**
     * Prompt'u validate et (runtime check)
     *
     * @param string $prompt
     * @param int $tenantId
     * @return bool
     */
    public static function validate(string $prompt, int $tenantId): bool
    {
        // Uzunluk kontrolü
        $minLength = config('ai-tenants.validation.min_prompt_length', 1000);
        if (strlen($prompt) < $minLength) {
            Log::error("🚨 Prompt validation FAILED: Too short ({strlen($prompt)} < {$minLength})");
            return false;
        }

        // Tenant 2/3 için özel kontroller
        if (in_array($tenantId, [2, 3])) {
            if (!str_contains($prompt, 'ULTRA KRİTİK') && !str_contains($prompt, 'KRİTİK KURAL')) {
                Log::warning("⚠️ Tenant {$tenantId} prompt missing CRITICAL rules!");
                return false;
            }
        }

        return true;
    }
}
