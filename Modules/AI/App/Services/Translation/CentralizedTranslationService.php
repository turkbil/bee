<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Translation;

use Illuminate\Support\Facades\Log;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\AI\App\Services\OpenAIService;
use Modules\AI\App\Services\AICreditService;

/**
 * Centralized Translation Service
 * Handles both single and bulk translations with unified logic
 */
readonly class CentralizedTranslationService
{
    public function __construct(
        private OpenAIService $aiService,
        private AICreditService $creditService
    ) {}

    /**
     * Estimate tokens and cost for translation
     */
    public function estimateTokensAndCost(array $config): array
    {
        $items = $config['items'] ?? [];
        $module = $config['module'] ?? 'page';
        $sourceLanguage = $config['source_language'] ?? 'tr';
        $targetLanguages = $config['target_languages'] ?? [];
        $includeSeo = $config['include_seo'] ?? false;
        
        $totalTokens = 0;
        $totalCharacters = 0;
        
        foreach ($items as $itemId) {
            $model = $this->getModel($module, $itemId);
            if (!$model) continue;
            
            // Get translatable fields
            $translatableFields = $this->getTranslatableFields($model);
            
            // Add SEO fields if requested
            if ($includeSeo && method_exists($model, 'seoSetting')) {
                $seoFields = ['seo_title', 'seo_description', 'seo_keywords', 'canonical_url'];
                $translatableFields = array_unique(array_merge($translatableFields, $seoFields));
            }
            
            foreach ($translatableFields as $field) {
                $content = $this->getFieldContent($model, $field, $sourceLanguage);
                if (!empty($content)) {
                    $content = strip_tags($content); // Remove HTML for estimation
                    $totalCharacters += strlen($content);
                }
            }
        }
        
        // Estimate ~4 characters per token
        $tokensPerLanguage = (int) ceil($totalCharacters / 4);
        $totalTokens = $tokensPerLanguage * count($targetLanguages);
        
        // Translation category cost multiplier
        $estimatedCost = $totalTokens * 1.5;
        
        return [
            'total_tokens' => $totalTokens,
            'total_characters' => $totalCharacters,
            'estimated_cost' => $estimatedCost
        ];
    }

    /**
     * Translation with progress callback
     */
    public function translateItemsWithProgress(array $config, callable $progressCallback = null): array
    {
        $items = $config['items'] ?? [];
        $targetLanguages = $config['target_languages'] ?? [];
        $sourceLanguage = $config['source_language'] ?? 'tr';
        
        // Get language names
        $languageNames = TenantLanguage::where('is_active', true)
            ->pluck('name', 'code')
            ->toArray();
        
        $results = [];
        $totalCreditsUsed = 0.0;
        $languageIndex = 0;
        
        foreach ($targetLanguages as $targetLang) {
            if ($targetLang === $sourceLanguage) continue;
            
            $languageIndex++;
            $langName = $languageNames[$targetLang] ?? $targetLang;
            
            // Progress callback - dil başladı
            if ($progressCallback) {
                $progressCallback($targetLang, 0, "'{$langName}' diline çeviri başladı...");
            }
            
            Log::info("🌐 Starting translation for language", [
                'language' => $targetLang,
                'name' => $langName,
                'index' => $languageIndex
            ]);
            
            // Her dil için tüm item'ları çevir
            foreach ($items as $itemId) {
                try {
                    $config['target_languages'] = [$targetLang]; // Tek dil
                    $config['items'] = [$itemId]; // Tek item
                    
                    $result = $this->translateItems($config);
                    
                    if (isset($result['summary']['total_credits_used'])) {
                        $totalCreditsUsed += $result['summary']['total_credits_used'];
                    }
                    
                    $results[] = $result;
                    
                } catch (\Exception $e) {
                    Log::error("Translation failed for item", [
                        'item_id' => $itemId,
                        'language' => $targetLang,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Progress callback - dil tamamlandı
            if ($progressCallback) {
                $progressCallback($targetLang, 100, "'{$langName}' dili tamamlandı ✓");
            }
            
            Log::info("✅ Language translation completed", [
                'language' => $targetLang,
                'name' => $langName
            ]);
        }
        
        return [
            'success' => true,
            'results' => $results,
            'summary' => [
                'total_languages' => count($targetLanguages),
                'total_items' => count($items),
                'total_credits_used' => $totalCreditsUsed
            ]
        ];
    }
    
    /**
     * Main translation method - handles both single and bulk operations
     */
    public function translateItems(array $config): array
    {
        $items = $config['items'] ?? [];
        $module = $config['module'] ?? 'page';
        $sourceLanguage = $config['source_language'] ?? 'tr';
        $targetLanguages = $config['target_languages'] ?? [];
        $quality = $config['quality'] ?? 'balanced';
        $includeSeO = $config['include_seo'] ?? false;
        $userId = $config['user_id'] ?? auth()->id();

        Log::info('🌍 Centralized Translation Started', [
            'module' => $module,
            'items_count' => count($items),
            'source_language' => $sourceLanguage,
            'target_languages' => $targetLanguages,
            'include_seo' => $includeSeO,
            'user_id' => $userId
        ]);

        // Credit pre-check
        $creditCheck = $this->checkCreditsForOperation($items, $module, $targetLanguages, $userId);
        if (!$creditCheck['sufficient']) {
            Log::warning('💳 Insufficient credits for translation', [
                'estimated_cost' => $creditCheck['estimated_cost'],
                'current_balance' => $creditCheck['balance'],
                'user_id' => $userId
            ]);
            
            return [
                'success' => false,
                'message' => $creditCheck['message'],
                'current_balance' => $creditCheck['balance'],
                'required_credits' => $creditCheck['estimated_cost']
            ];
        }

        // Get available languages for naming
        $availableLanguages = TenantLanguage::where('is_active', true)
            ->pluck('name', 'code')
            ->toArray();

        $results = [];
        $totalCreditsUsed = 0.0;
        $totalTranslations = 0;

        foreach ($items as $itemId) {
            try {
                $itemResult = $this->translateSingleItem([
                    'item_id' => $itemId,
                    'module' => $module,
                    'source_language' => $sourceLanguage,
                    'target_languages' => $targetLanguages,
                    'quality' => $quality,
                    'include_seo' => $includeSeO,
                    'available_languages' => $availableLanguages,
                    'user_id' => $userId
                ]);

                $results[] = $itemResult;
                $totalCreditsUsed += $itemResult['credits_used'] ?? 0;
                $totalTranslations += $itemResult['translations_count'] ?? 0;

                Log::info("✅ Item {$itemId} translation completed", [
                    'module' => $module,
                    'credits_used' => $itemResult['credits_used'] ?? 0,
                    'translations_count' => $itemResult['translations_count'] ?? 0
                ]);

            } catch (\Exception $e) {
                Log::error("❌ Item {$itemId} translation failed", [
                    'module' => $module,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                $results[] = [
                    'item_id' => $itemId,
                    'module' => $module,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                    'credits_used' => 0,
                    'translations_count' => 0
                ];
            }
        }

        Log::info('🏁 Centralized Translation Completed', [
            'module' => $module,
            'total_items' => count($items),
            'successful_items' => count(array_filter($results, fn($r) => $r['status'] === 'success')),
            'failed_items' => count(array_filter($results, fn($r) => $r['status'] === 'failed')),
            'total_credits_used' => $totalCreditsUsed,
            'total_translations' => $totalTranslations
        ]);

        return [
            'success' => true,
            'results' => $results,
            'summary' => [
                'total_items' => count($items),
                'successful_items' => count(array_filter($results, fn($r) => $r['status'] === 'success')),
                'failed_items' => count(array_filter($results, fn($r) => $r['status'] === 'failed')),
                'total_credits_used' => $totalCreditsUsed,
                'total_translations' => $totalTranslations
            ]
        ];
    }

    /**
     * Translate a single item with all fields and SEO
     */
    private function translateSingleItem(array $config): array
    {
        $itemId = $config['item_id'];
        $module = $config['module'];
        $sourceLanguage = $config['source_language'];
        $targetLanguages = $config['target_languages'];
        $quality = $config['quality'];
        $includeSeO = $config['include_seo'];
        $availableLanguages = $config['available_languages'];
        $userId = $config['user_id'];

        // Get model instance
        $model = $this->getModel($module, $itemId);
        if (!$model) {
            throw new \Exception("Item {$itemId} not found in module {$module}");
        }

        // Get translatable fields (including SEO if requested)
        $translatableFields = $this->getTranslatableFields($model, $includeSeO);
        $translations = [];
        $creditsUsed = 0.0;
        $translationsCount = 0;

        foreach ($translatableFields as $field) {
            // Get current field translations for ALL languages
            $fieldTranslations = $this->getFieldTranslations($model, $field);
            
            // Get source content - specifically from the selected source language
            $sourceContent = $fieldTranslations[$sourceLanguage] ?? '';
            
            // DEBUG LOG
            Log::info("🔍 Translation Field Check", [
                'field' => $field,
                'source_language' => $sourceLanguage,
                'source_content_exists' => !empty($sourceContent),
                'source_content_length' => strlen($sourceContent),
                'all_field_translations' => array_keys($fieldTranslations),
                'target_languages' => $targetLanguages
            ]);
            
            if (empty($sourceContent)) {
                // If no source content, keep existing translations but don't overwrite
                Log::warning("⚠️ No source content for field", [
                    'field' => $field,
                    'source_language' => $sourceLanguage,
                    'item_id' => $itemId
                ]);
                
                // Keep existing translations, don't clear them
                $translations[$field] = $fieldTranslations;
                continue;
            }
            
            // Translate to each target language
            foreach ($targetLanguages as $targetLang) {
                if ($targetLang === $sourceLanguage) {
                    continue; // Skip same language
                }

                try {
                    $targetLangName = $availableLanguages[$targetLang] ?? $targetLang;
                    
                    Log::info("🌍 ÇEVIRI BAŞLADI", [
                        'item_id' => $itemId,
                        'model' => $module,
                        'field' => $field,
                        'source_lang' => $sourceLanguage,
                        'target_lang' => $targetLang,
                        'original_content' => $sourceContent,
                        'content_length' => strlen($sourceContent),
                        'content_preview' => mb_substr($sourceContent, 0, 100) . '...'
                    ]);
                    
                    $translationResult = $this->translateField([
                        'content' => $sourceContent,
                        'source_lang' => $sourceLanguage,
                        'target_lang' => $targetLang,
                        'target_lang_name' => $targetLangName,
                        'field_type' => $field,
                        'quality' => $quality,
                        'user_id' => $userId,
                        'item_id' => $itemId,
                        'module' => $module
                    ]);

                    $fieldTranslations[$targetLang] = $translationResult['translated_text'];
                    $creditsUsed += $translationResult['credits_used'];
                    $translationsCount++;

                    Log::info("✅ ÇEVIRI TAMAMLANDI", [
                        'item_id' => $itemId,
                        'model' => $module,
                        'field' => $field,
                        'source_lang' => $sourceLanguage,
                        'target_lang' => $targetLang,
                        'original_content' => $sourceContent,
                        'translated_content' => $translationResult['translated_text'],
                        'translation_length' => strlen($translationResult['translated_text']),
                        'tokens_used' => $translationResult['tokens_used'],
                        'credits_used' => $translationResult['credits_used']
                    ]);

                    Log::info("📝 Field translated successfully", [
                        'field' => $field,
                        'source_lang' => $sourceLanguage,
                        'target_lang' => $targetLang,
                        'credits_used' => $translationResult['credits_used'],
                        'content_length' => strlen($sourceContent),
                        'translated_preview' => mb_substr($translationResult['translated_text'], 0, 100) . '...'
                    ]);

                } catch (\Exception $e) {
                    Log::error("❌ Field translation failed", [
                        'field' => $field,
                        'source_lang' => $sourceLanguage,
                        'target_lang' => $targetLang,
                        'error' => $e->getMessage()
                    ]);

                    // Use source content as fallback
                    $fieldTranslations[$targetLang] = $sourceContent;
                }
            }

            $translations[$field] = $fieldTranslations;
        }

        // Handle slug generation from title
        if (in_array('slug', $translatableFields) && isset($translations['title'])) {
            foreach ($targetLanguages as $targetLang) {
                if (!empty($translations['title'][$targetLang]) && $targetLang !== $sourceLanguage) {
                    $translations['slug'][$targetLang] = \Illuminate\Support\Str::slug($translations['title'][$targetLang]);
                }
            }
        }

        // Update model with all translations
        $updateData = [];
        foreach ($translations as $field => $fieldTranslations) {
            $updateData[$field] = $fieldTranslations;
        }

        // DETAYLI LOG - Model güncelleme öncesi
        Log::info("📝 MODEL UPDATE BAŞLANIYOR", [
            'item_id' => $itemId,
            'module' => $module,
            'model_class' => get_class($model),
            'fields_to_update' => array_keys($updateData),
            'languages_updated' => $targetLanguages,
            'source_language' => $sourceLanguage,
            'update_data_structure' => array_map(function($field, $data) {
                return [
                    'field' => $field,
                    'languages' => array_keys($data),
                    'content_lengths' => array_map('strlen', $data)
                ];
            }, array_keys($updateData), $updateData)
        ]);

        // Model güncelleme
        $updated = $model->update($updateData);

        // DETAYLI LOG - Model güncelleme sonrası
        Log::info("📝 MODEL UPDATE TAMAMLANDI", [
            'item_id' => $itemId,
            'module' => $module,
            'update_success' => $updated,
            'updated_at' => $model->updated_at,
            'final_data_check' => array_map(function($field) use ($model) {
                $value = $model->$field ?? null;
                return [
                    'field' => $field,
                    'type' => gettype($value),
                    'is_json' => is_string($value) && json_decode($value) !== null,
                    'languages_after_update' => is_array($value) ? array_keys($value) : 
                        (is_string($value) && json_decode($value) ? array_keys(json_decode($value, true) ?: []) : [])
                ];
            }, array_keys($updateData))
        ]);

        return [
            'item_id' => $itemId,
            'module' => $module,
            'status' => 'success',
            'fields_translated' => array_keys($translations),
            'languages' => $targetLanguages,
            'credits_used' => $creditsUsed,
            'translations_count' => $translationsCount
        ];
    }

    /**
     * Translate a single field with AI
     */
    private function translateField(array $config): array
    {
        $content = $config['content'];
        $sourceLang = $config['source_lang'];
        $targetLang = $config['target_lang'];
        $targetLangName = $config['target_lang_name'];
        $fieldType = $config['field_type'];
        $quality = $config['quality'];
        $userId = $config['user_id'];

        // Estimate cost
        $estimatedTokens = (int) ceil(strlen($content) / 4);
        $estimatedCost = $estimatedTokens * 1.5;

        // Credit pre-check
        $currentBalance = $this->creditService->getCurrentBalance($userId);
        if ($currentBalance < $estimatedCost) {
            Log::warning('💳 Insufficient credits for field translation', [
                'field_type' => $fieldType,
                'required' => $estimatedCost,
                'available' => $currentBalance,
                'user_id' => $userId
            ]);
            throw new \Exception("Yetersiz kredi. Gerekli: {$estimatedCost}, Mevcut: {$currentBalance}");
        }
        
        Log::info('💳 Credit check passed', [
            'field_type' => $fieldType,
            'estimated_cost' => $estimatedCost,
            'current_balance' => $currentBalance,
            'user_id' => $userId
        ]);

        // Generate field-specific prompt
        $prompt = $this->generateFieldPrompt($fieldType, $content, $sourceLang, $targetLang, $targetLangName);

        // Make AI translation request
        $response = $this->aiService->ask([
            ['role' => 'user', 'content' => $prompt]
        ]);

        if (!isset($response['success']) || !$response['success']) {
            throw new \Exception('OpenAI API failed: ' . ($response['error'] ?? 'Unknown error'));
        }

        // Get actual token usage
        $actualTokensUsed = $response['tokens_used'] ?? $response['total_tokens'] ?? $response['token_count'] ?? $estimatedTokens;
        $actualCost = $actualTokensUsed * 1.5;

        // Consume credits
        try {
            $this->creditService->consumeCredits($userId, $actualCost, 'translation', [
                'field_type' => $fieldType,
                'source_lang' => $sourceLang,
                'target_lang' => $targetLang,
                'tokens_used' => $actualTokensUsed,
                'content_length' => strlen($content)
            ]);
            
            Log::info('💳 Credits consumed successfully', [
                'field_type' => $fieldType,
                'actual_cost' => $actualCost,
                'tokens_used' => $actualTokensUsed,
                'user_id' => $userId
            ]);
        } catch (\Exception $e) {
            Log::error('💳 Failed to consume credits', [
                'error' => $e->getMessage(),
                'field_type' => $fieldType,
                'cost' => $actualCost
            ]);
            // Continue anyway - don't fail translation if credit deduction fails
        }

        // Clean and return translated content
        $translatedText = $response['response'] ?? '';
        $cleanedText = $this->cleanMarkdownFormatting($translatedText);

        return [
            'translated_text' => trim($cleanedText),
            'credits_used' => $actualCost,
            'tokens_used' => $actualTokensUsed
        ];
    }

    /**
     * Generate field-specific translation prompts
     */
    private function generateFieldPrompt(string $fieldType, string $content, string $sourceLang, string $targetLang, string $targetLangName): string
    {
        // Dil isimlerini veritabanından al - DİNAMİK SİSTEM
        $sourceLanguage = TenantLanguage::where('code', $sourceLang)->first();
        $targetLanguage = TenantLanguage::where('code', $targetLang)->first();
        
        // Eğer veritabanında bulunamazsa, parametreden gelen veya kod kullan
        $sourceLangName = $sourceLanguage ? $sourceLanguage->name : ucfirst($sourceLang);
        $targetLangFullName = $targetLanguage ? $targetLanguage->name : ($targetLangName ?: ucfirst($targetLang));
        
        $prompts = [
            'title' => "Translate the following title from {$sourceLangName} to {$targetLangFullName}. Keep it concise and SEO-friendly. Return ONLY the translated text without any markdown formatting:\n\n{$content}",
            
            'body' => "Translate the following HTML content from {$sourceLangName} to {$targetLangFullName}. Preserve all HTML tags and formatting exactly. Return ONLY the translated HTML without any markdown code blocks or formatting:\n\n{$content}",
            
            'slug' => "Translate the following slug from {$sourceLangName} to {$targetLangFullName}. Return ONLY the translated text without any markdown formatting:\n\n{$content}",
            
            // SEO Fields
            'meta_title' => "Translate the following SEO meta title from {$sourceLangName} to {$targetLangFullName}. Keep it under 60 characters and SEO-optimized. Return ONLY the translated text without any markdown formatting:\n\n{$content}",
            
            'meta_description' => "Translate the following SEO meta description from {$sourceLangName} to {$targetLangFullName}. Keep it under 160 characters and compelling. Return ONLY the translated text without any markdown formatting:\n\n{$content}",
            
            'meta_keywords' => "Translate the following SEO keywords from {$sourceLangName} to {$targetLangFullName}. Return ONLY the translated keywords separated by commas, without any markdown formatting:\n\n{$content}",
            
            'canonical_url' => "Translate the following canonical URL path from {$sourceLangName} to {$targetLangFullName}. Return ONLY the translated URL path without any markdown formatting:\n\n{$content}",
            
            // Other content fields
            'description' => "Translate the following description from {$sourceLangName} to {$targetLangFullName}. Return ONLY the translated text without any markdown formatting:\n\n{$content}",
            
            'summary' => "Translate the following summary from {$sourceLangName} to {$targetLangFullName}. Keep it concise. Return ONLY the translated text without any markdown formatting:\n\n{$content}",
            
            'default' => "Translate the following text from {$sourceLangName} to {$targetLangFullName}. Return ONLY the translated text without any markdown formatting:\n\n{$content}"
        ];

        return $prompts[$fieldType] ?? $prompts['default'];
    }

    /**
     * Check if user has enough credits for the operation
     */
    private function checkCreditsForOperation(array $items, string $module, array $targetLanguages, int $userId): array
    {
        // Estimate total cost
        $totalCost = 0.0;
        
        foreach ($items as $itemId) {
            $model = $this->getModel($module, $itemId);
            if (!$model) continue;

            $translatableFields = $this->getTranslatableFields($model, true); // Include SEO
            
            foreach ($translatableFields as $field) {
                $fieldTranslations = $this->getFieldTranslations($model, $field);
                $sourceContent = $fieldTranslations['tr'] ?? ''; // Assume TR as source
                
                if (!empty($sourceContent)) {
                    $estimatedTokens = (int) ceil(strlen($sourceContent) / 4);
                    $fieldCost = $estimatedTokens * 1.5 * count($targetLanguages);
                    $totalCost += $fieldCost;
                }
            }
        }

        $currentBalance = $this->creditService->getCurrentBalance($userId);
        $sufficient = $currentBalance >= $totalCost;

        return [
            'sufficient' => $sufficient,
            'estimated_cost' => $totalCost,
            'balance' => $currentBalance,
            'message' => $sufficient ? 'Sufficient credits available' : 'Insufficient credits. Please purchase more credits.'
        ];
    }

    /**
     * Get model instance based on module - DİNAMİK SİSTEM (MODULES TABLOSU İLE)
     */
    private function getModel(string $module, $id)
    {
        // Modül adını normalize et (page -> Page)
        $normalizedModule = ucfirst(strtolower($module));
        
        // Modules tablosundan modül bilgisini al - case insensitive search
        $moduleRecord = \Modules\ModuleManagement\App\Models\Module::whereRaw('LOWER(name) = ?', [strtolower($module)])
            ->first();
        
        if (!$moduleRecord) {
            Log::warning("Module not found in database: {$module}");
            // Fallback: Direkt model sınıfını dene
            $modelClass = "\\Modules\\{$normalizedModule}\\App\\Models\\{$normalizedModule}";
            
            if (class_exists($modelClass)) {
                Log::info("Using fallback model loading", [
                    'module' => $module,
                    'model_class' => $modelClass,
                    'id' => $id
                ]);
                return $modelClass::find($id);
            }
            
            return null;
        }
        
        // Modül adını al ve normalize et
        $moduleName = ucfirst($moduleRecord->name);
        
        // Model sınıfı - Standart: Modules\{ModuleName}\App\Models\{ModuleName}
        $modelClass = "\\Modules\\{$moduleName}\\App\\Models\\{$moduleName}";
        
        if (!class_exists($modelClass)) {
            // Alternatif model isimleri dene
            $alternativeNames = [
                "\\Modules\\{$moduleName}\\App\\Models\\" . str_replace('Management', '', $moduleName),
                "\\Modules\\{$moduleName}\\App\\Models\\" . rtrim($moduleName, 's'),
                "\\Modules\\{$moduleName}\\App\\Models\\" . $moduleName . 'Model'
            ];
            
            foreach ($alternativeNames as $altClass) {
                if (class_exists($altClass)) {
                    $modelClass = $altClass;
                    break;
                }
            }
            
            if (!class_exists($modelClass)) {
                Log::warning("Model class not found: {$modelClass}", [
                    'tried_alternatives' => $alternativeNames
                ]);
                return null;
            }
        }
        
        Log::info("Dynamic model loading", [
            'module' => $module,
            'normalized_module' => $normalizedModule,
            'module_record_name' => $moduleRecord->name ?? 'N/A',
            'model_class' => $modelClass,
            'id' => $id
        ]);

        return $modelClass::find($id);
    }

    /**
     * Get field content for estimation
     */
    private function getFieldContent($model, string $field, string $language): string
    {
        // Check if it's an SEO field
        if (in_array($field, ['seo_title', 'seo_description', 'seo_keywords', 'canonical_url'])) {
            $translations = $this->getSeoFieldTranslations($model, $field);
            return $translations[$language] ?? '';
        }

        // Check if field exists
        if (!property_exists($model, $field) && !isset($model->$field)) {
            return '';
        }

        // Regular model field
        if (method_exists($model, 'getTranslated')) {
            try {
                return $model->getTranslated($field, $language) ?? '';
            } catch (\Exception $e) {
                Log::debug("Error getting translated field {$field}: " . $e->getMessage());
            }
        }
        
        // Fallback: get field translations manually
        $translations = $this->getFieldTranslations($model, $field);
        return $translations[$language] ?? '';
    }

    /**
     * Get translatable fields for a model
     */
    private function getTranslatableFields($model, bool $includeSeo = false): array
    {
        $fields = [];

        // Basic translatable fields
        if (property_exists($model, 'translatable') && is_array($model->translatable)) {
            $fields = $model->translatable;
        } else {
            // Try to detect available fields from model
            $defaultFields = ['title', 'body', 'slug', 'description', 'summary', 'content'];
            $availableFields = [];
            
            foreach ($defaultFields as $field) {
                if (property_exists($model, $field) || isset($model->$field)) {
                    $availableFields[] = $field;
                }
            }
            
            $fields = !empty($availableFields) ? $availableFields : ['title', 'body'];
        }

        // Add SEO fields if requested and model has SEO relationship
        if ($includeSeo && method_exists($model, 'seoSetting')) {
            $seoFields = ['meta_title', 'meta_description', 'meta_keywords', 'canonical_url'];
            $fields = array_merge($fields, $seoFields);
        }

        return array_unique($fields);
    }

    /**
     * Get field translations ensuring proper format
     */
    private function getFieldTranslations($model, string $field): array
    {
        // Check if it's an SEO field
        if (in_array($field, ['meta_title', 'meta_description', 'meta_keywords', 'canonical_url'])) {
            return $this->getSeoFieldTranslations($model, $field);
        }

        // Check if field exists in model
        if (!property_exists($model, $field) && !isset($model->$field)) {
            Log::debug("Field {$field} does not exist in model " . get_class($model));
            return [];
        }

        // Regular model field
        $value = $model->$field ?? null;

        if (!$value) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Get SEO field translations
     */
    private function getSeoFieldTranslations($model, string $field): array
    {
        if (!method_exists($model, 'seoSetting')) {
            return [];
        }

        $seoSetting = $model->seoSetting;
        if (!$seoSetting) {
            return [];
        }

        $value = $seoSetting->$field ?? null;
        if (!$value) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Clean markdown formatting from AI response
     */
    private function cleanMarkdownFormatting(string $text): string
    {
        // Remove markdown code blocks
        $text = preg_replace('/^```[a-zA-Z]*\s*\n/m', '', $text);
        $text = preg_replace('/\n```\s*$/m', '', $text);
        $text = preg_replace('/```[a-zA-Z]*\s*/', '', $text);
        $text = preg_replace('/```/', '', $text);

        // Remove extra newlines created by code block removal
        $text = preg_replace('/^\s*\n+/', '', $text);
        $text = preg_replace('/\n+\s*$/', '', $text);

        // Remove any remaining markdown artifacts
        $text = preg_replace('/^\s*\\\s*/', '', $text);
        $text = preg_replace('/\\\s*$/', '', $text);

        return $text;
    }
}