<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\AIService;
use Modules\SeoManagement\App\Models\SeoSetting;

/**
 * Universal Translation Service
 * 
 * Registry-based sistem için universal çeviri servisi
 * Herhangi bir entity'yi çevirebilir
 */
class UniversalTranslationService
{
    protected AIService $aiService;
    
    public function __construct()
    {
        $this->aiService = new AIService();
    }
    
    /**
     * Entity'yi çevir
     */
    public function translateEntity(
        string $entityType,
        int $entityId,
        string $sourceLanguage,
        array $targetLanguages,
        array $entityConfig,
        string $sessionId
    ): bool {
        \Log::info('🔥 Universal translation started', [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'session_id' => $sessionId,
            'config' => $entityConfig
        ]);
        
        try {
            // Model'i al
            $modelClass = $entityConfig['model_class'];
            $model = $modelClass::find($entityId);
            
            if (!$model) {
                throw new \Exception("Entity not found: {$entityType} #{$entityId}");
            }
            
            // Ana entity'yi çevir
            $this->translateMainEntity($model, $entityConfig, $sourceLanguage, $targetLanguages, $sessionId);
            
            // SEO çevirisi (eğer destekleniyorsa)
            if ($entityConfig['seo_enabled'] ?? false) {
                $this->translateSeoSettings($model, $sourceLanguage, $targetLanguages, $sessionId);
            }
            
            \Log::info('✅ Universal translation completed', [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'session_id' => $sessionId
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error('❌ Universal translation failed', [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Ana entity'nin alanlarını çevir
     */
    protected function translateMainEntity($model, array $entityConfig, string $sourceLanguage, array $targetLanguages, string $sessionId): void
    {
        $fieldsToTranslate = $entityConfig['fields'] ?? [];
        
        if (empty($fieldsToTranslate)) {
            \Log::warning('No translatable fields found', [
                'entity' => get_class($model),
                'config' => $entityConfig
            ]);
            return;
        }
        
        foreach ($targetLanguages as $targetLanguage) {
            if ($targetLanguage === $sourceLanguage) {
                continue; // Aynı dili atlayın
            }
            
            \Log::info("🔄 Translating to {$targetLanguage}", [
                'model' => get_class($model),
                'fields' => $fieldsToTranslate,
                'session_id' => $sessionId
            ]);
            
            foreach ($fieldsToTranslate as $field) {
                $this->translateField($model, $field, $sourceLanguage, $targetLanguage, $sessionId);
            }
        }
        
        // Model'i kaydet
        $model->save();
    }
    
    /**
     * Tek bir alanı çevir
     */
    protected function translateField($model, string $field, string $sourceLanguage, string $targetLanguage, string $sessionId): void
    {
        try {
            // Kaynak metni al
            $sourceText = $this->getFieldValue($model, $field, $sourceLanguage);
            
            if (empty($sourceText)) {
                \Log::info("Source text empty for {$field}, skipping", [
                    'model' => get_class($model),
                    'field' => $field,
                    'source_lang' => $sourceLanguage
                ]);
                return;
            }
            
            // Çeviriyi yap
            $translatedText = $this->aiService->translateText(
                $sourceText,
                $sourceLanguage,
                $targetLanguage,
                $this->getTranslationContext($field)
            );
            
            if ($translatedText) {
                // Çeviriyi kaydet
                $this->setFieldValue($model, $field, $targetLanguage, $translatedText);
                
                \Log::info("✅ Field translated: {$field}", [
                    'source_lang' => $sourceLanguage,
                    'target_lang' => $targetLanguage,
                    'session_id' => $sessionId
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error("❌ Field translation failed: {$field}", [
                'error' => $e->getMessage(),
                'source_lang' => $sourceLanguage,
                'target_lang' => $targetLanguage,
                'session_id' => $sessionId
            ]);
        }
    }
    
    /**
     * SEO ayarlarını çevir
     */
    protected function translateSeoSettings($model, string $sourceLanguage, array $targetLanguages, string $sessionId): void
    {
        try {
            if (!method_exists($model, 'seoSettings')) {
                return;
            }
            
            $seoSetting = $model->seoSettings;
            
            if (!$seoSetting) {
                return;
            }
            
            $seoFields = ['seo_title', 'seo_description', 'seo_keywords'];
            
            foreach ($targetLanguages as $targetLanguage) {
                if ($targetLanguage === $sourceLanguage) {
                    continue;
                }
                
                foreach ($seoFields as $field) {
                    $this->translateSeoField($seoSetting, $field, $sourceLanguage, $targetLanguage, $sessionId);
                }
            }
            
            $seoSetting->save();
            
        } catch (\Exception $e) {
            \Log::error('❌ SEO translation failed', [
                'model' => get_class($model),
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);
        }
    }
    
    /**
     * SEO alanını çevir
     */
    protected function translateSeoField($seoSetting, string $field, string $sourceLanguage, string $targetLanguage, string $sessionId): void
    {
        try {
            $sourceText = $this->getFieldValue($seoSetting, $field, $sourceLanguage);
            
            if (empty($sourceText)) {
                return;
            }
            
            $translatedText = $this->aiService->translateText(
                $sourceText,
                $sourceLanguage,
                $targetLanguage,
                $this->getSeoTranslationContext($field)
            );
            
            if ($translatedText) {
                $this->setFieldValue($seoSetting, $field, $targetLanguage, $translatedText);
                
                \Log::info("✅ SEO field translated: {$field}", [
                    'source_lang' => $sourceLanguage,
                    'target_lang' => $targetLanguage,
                    'session_id' => $sessionId
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error("❌ SEO field translation failed: {$field}", [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);
        }
    }
    
    /**
     * Alan değerini al (JSON veya normal)
     */
    protected function getFieldValue($model, string $field, string $language): ?string
    {
        $value = $model->{$field};
        
        // JSON field mi kontrol et
        if (is_array($value)) {
            return $value[$language] ?? null;
        }
        
        return $value;
    }
    
    /**
     * Alan değerini set et (JSON veya normal)
     */
    protected function setFieldValue($model, string $field, string $language, string $value): void
    {
        $currentValue = $model->{$field};
        
        // JSON field mi kontrol et
        if (is_array($currentValue)) {
            $currentValue[$language] = $value;
            $model->{$field} = $currentValue;
        } else {
            // JSON olmayan field'lar için dil suffix'li alan oluştur
            $languageField = $field . '_' . $language;
            if (in_array($languageField, $model->getFillable())) {
                $model->{$languageField} = $value;
            } else {
                // JSON'a çevir
                $model->{$field} = [
                    $language => $value
                ];
            }
        }
    }
    
    /**
     * Alan türüne göre çeviri context'i
     */
    protected function getTranslationContext(string $field): string
    {
        $contexts = [
            'title' => 'This is a title or heading',
            'name' => 'This is a name or label',
            'content' => 'This is main content or article text',
            'body' => 'This is main content or article text',
            'description' => 'This is a description or summary',
            'excerpt' => 'This is a short excerpt or summary',
            'summary' => 'This is a brief summary',
            'slug' => 'This is a URL slug - keep it SEO friendly'
        ];
        
        return $contexts[$field] ?? 'Please translate this text accurately';
    }
    
    /**
     * SEO alanları için özel context
     */
    protected function getSeoTranslationContext(string $field): string
    {
        $contexts = [
            'seo_title' => 'This is an SEO meta title - keep it under 60 characters and make it compelling for search engines',
            'seo_description' => 'This is an SEO meta description - keep it under 160 characters and make it compelling for search engines',
            'seo_keywords' => 'These are SEO keywords - translate them appropriately for the target language and market'
        ];
        
        return $contexts[$field] ?? 'This is SEO content - optimize for search engines';
    }
}