<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Services\UniversalTranslationService;
use Modules\AI\app\Services\AIService;
use Modules\AI\App\Services\ConversationTracker;
use Modules\AI\App\Services\AICreditService;
use Modules\AI\App\Services\EnhancedJavaScriptProtector;

/**
 * 🚀 CLEAN FAST HTML TRANSLATION SERVICE
 * 
 * Sadece HTML çevirisi ve Universal entity çevirisi
 * Tüm deprecated metodlar kaldırıldı - %100 Dynamic!
 */
class FastHtmlTranslationService
{
    private $aiService;
    private $creditService;
    private $universalTranslationService;

    public function __construct(
        AIService $aiService, 
        AICreditService $creditService,
        UniversalTranslationService $universalTranslationService
    ) {
        $this->aiService = $aiService;
        $this->creditService = $creditService;
        $this->universalTranslationService = $universalTranslationService;
    }

    /**
     * 🚀 SÜPER HIZLI HTML ÇEVİRİ SİSTEMİ
     * HTML'den text'leri çıkarır, toplu çevirir, geri yerleştirir
     * Enhanced JavaScript Protection sistemi ile
     */
    public function translateHtmlContentFast(string $html, string $fromLang, string $toLang, string $context): string
    {
        try {
            // 🚨 ULTRA-ENHANCED JAVASCRIPT PROTECTION - Using dedicated protector service
            $jsProtector = new EnhancedJavaScriptProtector();
            $htmlWithPlaceholders = $jsProtector->protectJavaScript($html);
            
            Log::info('🛡️ Ultra-enhanced JavaScript protection applied via EnhancedJavaScriptProtector', [
                'original_length' => strlen($html),
                'protected_length' => strlen($htmlWithPlaceholders),
                'phase_1_attributes' => 'Protected',
                'phase_2_expressions' => 'Protected'
            ]);

            // HTML'den text'leri çıkar
            $textsToTranslate = $this->extractTextsFromHtml($htmlWithPlaceholders);
            
            if (empty($textsToTranslate)) {
                Log::info('📝 Çevrilecek text bulunamadı');
                
                // 🚨 CRITICAL: Restore JavaScript even if no text to translate
                $restoredHtml = $jsProtector->restoreJavaScript($htmlWithPlaceholders);
                
                Log::info('✅ JavaScript koruması geri yüklendi (çevirilecek text yok)', [
                    'restored_length' => strlen($restoredHtml)
                ]);
                
                return $restoredHtml;
            }

            // Unique separator oluştur
            $separator = 'UUID_' . str_replace('-', '_', Str::uuid()) . '_SEPARATOR';
            
            // Toplu çeviri için birleştir
            $combinedText = implode("\n{$separator}\n", $textsToTranslate);
            
            // AI ile çevir
            $translatedCombined = $this->aiService->translateText(
                $combinedText,
                $fromLang,
                $toLang,
                ['context' => $context]
            );
            
            // Çevrilen text'leri ayır
            $translatedTexts = $this->parseBulkTranslationResponse($translatedCombined, $separator, count($textsToTranslate));
            
            // HTML'de text'leri değiştir
            $translatedHtml = $this->replaceTextsInHtml($htmlWithPlaceholders, $textsToTranslate, $translatedTexts);
            
            // 🚨 CRITICAL: Restore ALL protected JavaScript via EnhancedJavaScriptProtector
            $translatedHtml = $jsProtector->restoreJavaScript($translatedHtml);
            
            Log::info('✅ SÜPER HIZLI HTML çeviri tamamlandı - EnhancedJavaScriptProtector ile', [
                'original_length' => strlen($html),
                'translated_length' => strlen($translatedHtml),
                'texts_translated' => count($translatedTexts),
                'performance' => 'BULK_TRANSLATION_WITH_ENHANCED_JS_PROTECTION',
                'javascript_protection' => '2-PHASE_ENHANCED_PROTECTION_SUCCESS'
            ]);
            
            return $translatedHtml;

        } catch (\Exception $e) {
            Log::error('❌ SÜPER HIZLI HTML çeviri hatası', [
                'error' => $e->getMessage(),
                'html_length' => strlen($html)
            ]);
            
            // Fallback: JavaScript korumasını geri yükle ve orijinal HTML'i döndür
            try {
                $jsProtector = new EnhancedJavaScriptProtector();
                return $jsProtector->restoreJavaScript($html);
            } catch (\Exception $fallbackError) {
                Log::error('❌ Fallback JavaScript restore hatası', [
                    'error' => $fallbackError->getMessage()
                ]);
                return $html;
            }
        }
    }

    /**
     * 🚀 UNIVERSAL Entity çeviri işlemi - Queue Job için
     * 💰 PER-LANGUAGE CREDIT SYSTEM: Her dil için ayrı kredi düşümü
     * 🌍 DYNAMIC MODULE SUPPORT: Tüm modüller otomatik desteklenir
     */
    public function translateEntity(string $entityType, int $entityId, string $sourceLanguage, string $targetLanguage): array
    {
        try {
            Log::info("🌍 UNIVERSAL FastHtml Entity çevirisi başlatıldı", [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage
            ]);

            // 💰 Pre-translation credit deduction - Per language basis
            try {
                $tenantId = tenancy()->tenant?->id ?? 1;
                $tenant = \App\Models\Tenant::find($tenantId);
                
                if ($tenant) {
                    // Per-language credit cost (1 credit per language translation)
                    $perLanguageCost = 1.0;
                    
                    ai_use_credits($perLanguageCost, $tenant->id, [
                        'usage_type' => 'translation',
                        'description' => "Universal AI Translation: {$entityType} #{$entityId} ({$sourceLanguage} → {$targetLanguage})",
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'source_language' => $sourceLanguage,
                        'target_language' => $targetLanguage,
                        'provider_name' => 'universal_translation_service'
                    ]);
                    
                    Log::info('💰 KREDİ DÜŞÜRÜLDİ: UNIVERSAL 1 DİL = 1 KREDİ', [
                        'tenant_id' => $tenant->id,
                        'credits_used' => $perLanguageCost,
                        'credit_rule' => 'UNIVERSAL 1 DİL = 1 KREDİ',
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'language_pair' => "{$sourceLanguage} → {$targetLanguage}",
                        'remaining_credits' => $tenant->fresh()->ai_credits_balance ?? 'unknown'
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('⚠️ Per-language credit deduction failed', [
                    'error' => $e->getMessage(),
                    'entity_type' => $entityType,
                    'entity_id' => $entityId
                ]);
                // Continue with translation even if credit deduction fails
            }

            // 🚀 UNIVERSAL TRANSLATION: Dinamik modül desteği
            $result = $this->universalTranslationService->translateEntity(
                $entityType, 
                $entityId, 
                $sourceLanguage, 
                $targetLanguage
            );

            if ($result['success']) {
                Log::info('✅ UNIVERSAL çeviri başarılı', [
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'translated_fields' => array_keys($result['data'])
                ]);

                return [
                    'success' => true,
                    'translated_data' => $result['data'],
                    'target_language' => $targetLanguage
                ];
            } else {
                throw new \Exception($result['error']);
            }

        } catch (\Exception $e) {
            Log::error("❌ UNIVERSAL FastHtmlTranslationService hatası", [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * HTML'den çevrilebilir text'leri çıkar
     */
    private function extractTextsFromHtml(string $html): array
    {
        $texts = [];
        
        // Text node'ları ve attribute'ları yakalama pattern'i
        $patterns = [
            // Alt, title, placeholder attribute'ları
            '/(?:alt|title|placeholder)=["\']([^"\']+)["\']/i',
            // Tag'lar arasındaki text'ler (HTML tag'larını exclude ederek)
            '/>([^<]+)</s'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $html, $matches)) {
                foreach ($matches[1] as $match) {
                    $cleanText = trim($match);
                    if ($this->isTranslatableText($cleanText)) {
                        $texts[] = $cleanText;
                    }
                }
            }
        }
        
        return array_unique($texts);
    }

    /**
     * Text'in çevrilebilir olup olmadığını kontrol et
     */
    private function isTranslatableText(string $text): bool
    {
        $trimmedText = trim($text);
        
        // Boş veya çok kısa text'ler
        if (empty($trimmedText) || strlen($trimmedText) < 2) {
            return false;
        }
        
        // Sadece rakam, boşluk, noktalama işaretleri
        if (preg_match('/^[\d\s\W]*$/', $trimmedText)) {
            // Eğer hiç harf yoksa atla
            if (!preg_match('/[\p{L}]/u', $trimmedText)) {
                return false;
            }
        }
        
        // Çok kısa text'ler - UTF-8 karakter desteği ile
        if (mb_strlen($trimmedText, 'UTF-8') < 2) {
            return false;
        }
        
        // URL, email, kod benzeri pattern'ler
        if (preg_match('/^(https?:\/\/|www\.|@|\{|\[|#[a-zA-Z]|[a-zA-Z]+\([^\)]*\))/', $trimmedText)) {
            return false;
        }
        
        return true;
    }

    /**
     * Toplu çeviri response'unu parse et
     */
    private function parseBulkTranslationResponse(string $response, string $separator, int $expectedCount): array
    {
        $parts = explode($separator, $response);
        $translatedTexts = [];
        
        foreach ($parts as $part) {
            $cleanPart = trim($part);
            if (!empty($cleanPart)) {
                $translatedTexts[] = $cleanPart;
            }
        }
        
        Log::info('🔍 Toplu çeviri parse edildi', [
            'expected_count' => $expectedCount,
            'parsed_count' => count($translatedTexts),
            'separator' => $separator
        ]);
        
        return $translatedTexts;
    }

    /**
     * HTML'de text'leri değiştir
     */
    private function replaceTextsInHtml(string $html, array $originalTexts, array $translatedTexts): string
    {
        $modifiedHtml = $html;
        
        $count = min(count($originalTexts), count($translatedTexts));
        
        for ($i = 0; $i < $count; $i++) {
            $original = $originalTexts[$i];
            $translated = $translatedTexts[$i];
            
            // HTML encode edilmiş versiyonları da dene
            $patterns = [
                $original,
                htmlspecialchars($original),
                htmlentities($original)
            ];
            
            foreach ($patterns as $pattern) {
                if (strpos($modifiedHtml, $pattern) !== false) {
                    $modifiedHtml = str_replace($pattern, $translated, $modifiedHtml);
                    break;
                }
            }
        }
        
        return $modifiedHtml;
    }
}