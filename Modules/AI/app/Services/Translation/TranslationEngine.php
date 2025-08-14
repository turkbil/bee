<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Translation;

use Modules\AI\App\Models\AITranslationMappings;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Services\AIService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

readonly class TranslationEngine
{
    public function __construct(
        private AIService $aiService
    ) {}

    /**
     * Record'u çevir
     */
    public function translateRecord(string $module, int $recordId, array $languages, string $sourceLanguage = 'tr'): array
    {
        $mapping = $this->getTranslationMapping($module);
        
        if (!$mapping) {
            throw new \Exception("Translation mapping not found for module: {$module}");
        }

        $record = $this->getRecord($module, $mapping->table_name, $recordId);
        
        if (!$record) {
            throw new \Exception("Record not found: {$module}:{$recordId}");
        }

        $results = [];
        $translatableFields = $mapping->translatable_fields;
        
        foreach ($languages as $targetLanguage) {
            if ($targetLanguage === $sourceLanguage) {
                continue; // Aynı dili çevirme
            }

            $translatedFields = [];
            
            foreach ($translatableFields as $fieldName) {
                $originalValue = $this->getFieldValue($record, $fieldName, $mapping);
                
                if (empty($originalValue)) {
                    continue;
                }

                try {
                    $translatedValue = $this->translateField(
                        $originalValue, 
                        $sourceLanguage, 
                        $targetLanguage,
                        $this->getFieldType($fieldName, $mapping),
                        $this->getFieldContext($module, $fieldName)
                    );
                    
                    $translatedFields[$fieldName] = $translatedValue;
                    
                } catch (\Exception $e) {
                    Log::warning("Translation failed for field", [
                        'module' => $module,
                        'record_id' => $recordId,
                        'field' => $fieldName,
                        'error' => $e->getMessage()
                    ]);
                    
                    $translatedFields[$fieldName] = $originalValue; // Fallback to original
                }
            }
            
            // Çevrilmiş alanları kaydet
            $this->saveTranslatedFields($module, $mapping, $recordId, $translatedFields, $targetLanguage);
            
            $results[$targetLanguage] = $translatedFields;
        }
        
        return [
            'success' => true,
            'module' => $module,
            'record_id' => $recordId,
            'source_language' => $sourceLanguage,
            'target_languages' => $languages,
            'translated_fields' => $results,
            'total_fields' => count($translatableFields),
            'processed_at' => now()
        ];
    }

    /**
     * Tek field'i çevir
     */
    public function translateField(string $text, string $fromLang, string $toLang, string $fieldType = 'text', array $context = []): string
    {
        // Cache kontrolü
        $cacheKey = 'translation_' . md5($text . $fromLang . $toLang . $fieldType);
        
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        // AI çeviri feature'ını bul
        $translationFeature = $this->getTranslationFeature($fieldType, $context);
        
        if (!$translationFeature) {
            throw new \Exception("Translation feature not found for field type: {$fieldType}");
        }

        // Çeviri prompt'unu hazırla
        $prompt = $this->buildTranslationPrompt($text, $fromLang, $toLang, $fieldType, $context);
        
        // AI ile çevir
        $response = $this->aiService->processRequest([
            'feature_id' => $translationFeature->id,
            'prompt' => $prompt,
            'context' => [
                'source_language' => $fromLang,
                'target_language' => $toLang,
                'field_type' => $fieldType,
                'preserve_formatting' => $this->shouldPreserveFormatting($fieldType)
            ]
        ]);

        $translatedText = $this->extractTranslationFromResponse($response, $fieldType);
        
        // Cache'e kaydet
        Cache::put($cacheKey, $translatedText, 3600 * 24); // 24 saat
        
        return $translatedText;
    }

    /**
     * JSON field'ını çevir
     */
    public function translateJSON(array $json, string $fromLang, string $toLang, array $context = []): array
    {
        $translated = [];
        
        foreach ($json as $key => $value) {
            if (is_string($value) && !empty(trim($value))) {
                $translated[$key] = $this->translateField($value, $fromLang, $toLang, 'text', $context);
            } elseif (is_array($value)) {
                $translated[$key] = $this->translateJSON($value, $fromLang, $toLang, $context);
            } else {
                $translated[$key] = $value; // Non-string values geçir
            }
        }
        
        return $translated;
    }

    /**
     * Bulk çeviri
     */
    public function bulkTranslate(array $records, array $languages, string $sourceLanguage = 'tr'): array
    {
        $results = [];
        $errors = [];
        
        foreach ($records as $record) {
            $module = $record['module'];
            $recordId = $record['record_id'];
            
            try {
                $result = $this->translateRecord($module, $recordId, $languages, $sourceLanguage);
                $results[] = $result;
            } catch (\Exception $e) {
                $errors[] = [
                    'module' => $module,
                    'record_id' => $recordId,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return [
            'success' => count($results),
            'failed' => count($errors),
            'results' => $results,
            'errors' => $errors,
            'processed_at' => now()
        ];
    }

    /**
     * Çevrilebilir field'ları getir
     */
    public function getTranslatableFields(string $module): array
    {
        $mapping = $this->getTranslationMapping($module);
        
        if (!$mapping) {
            return [];
        }
        
        return [
            'module' => $module,
            'table' => $mapping->table_name,
            'translatable_fields' => $mapping->translatable_fields,
            'json_fields' => $mapping->json_fields ?? [],
            'seo_fields' => $mapping->seo_fields ?? [],
            'field_types' => $mapping->field_types,
            'max_lengths' => $mapping->max_lengths ?? [],
            'special_rules' => $mapping->special_rules ?? []
        ];
    }

    /**
     * Formatı koru
     */
    public function preserveFormatting(string $text): array
    {
        $format = [
            'type' => 'text',
            'has_html' => false,
            'has_markdown' => false,
            'has_urls' => false,
            'has_emails' => false,
            'placeholders' => []
        ];

        // HTML tespit et
        if (strip_tags($text) !== $text) {
            $format['has_html'] = true;
            $format['type'] = 'html';
        }

        // Markdown tespit et
        if (preg_match('/[*_#`\[\]]/', $text)) {
            $format['has_markdown'] = true;
            if ($format['type'] === 'text') {
                $format['type'] = 'markdown';
            }
        }

        // URL'leri bul ve placeholder'lara çevir
        $urlPattern = '/https?:\/\/[^\s]+/';
        if (preg_match_all($urlPattern, $text, $matches)) {
            $format['has_urls'] = true;
            foreach ($matches[0] as $index => $url) {
                $placeholder = "{{URL_" . ($index + 1) . "}}";
                $format['placeholders'][$placeholder] = $url;
                $text = str_replace($url, $placeholder, $text);
            }
        }

        // Email'leri bul
        $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        if (preg_match_all($emailPattern, $text, $matches)) {
            $format['has_emails'] = true;
            foreach ($matches[0] as $index => $email) {
                $placeholder = "{{EMAIL_" . ($index + 1) . "}}";
                $format['placeholders'][$placeholder] = $email;
                $text = str_replace($email, $placeholder, $text);
            }
        }

        $format['processed_text'] = $text;
        
        return $format;
    }

    /**
     * Formatı geri yükle
     */
    public function restoreFormatting(string $translatedText, array $format): string
    {
        // Placeholder'ları geri yükle
        foreach ($format['placeholders'] as $placeholder => $originalValue) {
            $translatedText = str_replace($placeholder, $originalValue, $translatedText);
        }
        
        return $translatedText;
    }

    /**
     * Private helper methods
     */
    private function getTranslationMapping(string $module): ?AITranslationMappings
    {
        return Cache::remember("translation_mapping_{$module}", 3600, function () use ($module) {
            return AITranslationMappings::where('module_name', $module)
                ->where('is_active', true)
                ->first();
        });
    }

    private function getRecord(string $module, string $tableName, int $recordId): ?object
    {
        return DB::table($tableName)
            ->where('id', $recordId)
            ->first();
    }

    private function getFieldValue($record, string $fieldName, AITranslationMappings $mapping): mixed
    {
        $jsonFields = $mapping->json_fields ?? [];
        
        if (in_array($fieldName, $jsonFields)) {
            // JSON field - sadece source language'i al
            $jsonValue = json_decode($record->{$fieldName}, true);
            return is_array($jsonValue) ? ($jsonValue['tr'] ?? '') : '';
        }
        
        return $record->{$fieldName} ?? '';
    }

    private function getFieldType(string $fieldName, AITranslationMappings $mapping): string
    {
        $fieldTypes = $mapping->field_types;
        return $fieldTypes[$fieldName] ?? 'text';
    }

    private function getFieldContext(string $module, string $fieldName): array
    {
        return [
            'module' => $module,
            'field' => $fieldName,
            'is_seo' => $this->isSEOField($fieldName),
            'is_content' => $this->isContentField($fieldName)
        ];
    }

    private function isSEOField(string $fieldName): bool
    {
        $seoFields = ['seo_title', 'meta_description', 'meta_keywords', 'slug'];
        return in_array($fieldName, $seoFields);
    }

    private function isContentField(string $fieldName): bool
    {
        $contentFields = ['title', 'body', 'content', 'description', 'summary'];
        return in_array($fieldName, $contentFields);
    }

    private function getTranslationFeature(string $fieldType, array $context): ?AIFeature
    {
        $cacheKey = "translation_feature_{$fieldType}_" . md5(serialize($context));
        
        return Cache::remember($cacheKey, 1800, function () use ($fieldType, $context) {
            $query = AIFeature::where('is_active', true)
                ->where('category', 'translation')
                ->whereJsonContains('supported_modules', ['translation']);

            // Field tipine göre özel feature seç
            if ($fieldType === 'html') {
                $query->where('slug', 'html-translation');
            } elseif ($fieldType === 'seo') {
                $query->where('slug', 'seo-translation');
            } elseif ($context['is_seo'] ?? false) {
                $query->where('slug', 'seo-translation');
            } else {
                $query->where('slug', 'general-translation');
            }

            return $query->first() ?? AIFeature::where('is_active', true)
                ->where('category', 'translation')
                ->first();
        });
    }

    private function buildTranslationPrompt(string $text, string $fromLang, string $toLang, string $fieldType, array $context): string
    {
        $languageNames = [
            'tr' => 'Türkçe',
            'en' => 'İngilizce',
            'de' => 'Almanca',
            'fr' => 'Fransızca',
            'es' => 'İspanyolca'
        ];

        $fromLanguageName = $languageNames[$fromLang] ?? $fromLang;
        $toLanguageName = $languageNames[$toLang] ?? $toLang;

        $prompt = "Lütfen aşağıdaki metni {$fromLanguageName}'den {$toLanguageName}'ye çevirin.\n\n";
        
        // Field tipine göre özel talimatlar
        if ($fieldType === 'html') {
            $prompt .= "Bu metin HTML içeriği. HTML etiketlerini koruyun, sadece metin içeriğini çevirin.\n\n";
        } elseif ($fieldType === 'seo') {
            $prompt .= "Bu SEO metni. Anahtar kelimeleri koruyarak, SEO dostu bir çeviri yapın.\n\n";
        } elseif ($context['is_seo'] ?? false) {
            $prompt .= "Bu SEO amaçlı metin. SEO değerini koruyarak çevirin.\n\n";
        }

        // Özel kurallar
        $prompt .= "Çeviri kuralları:\n";
        $prompt .= "- Doğal ve akıcı çeviri yapın\n";
        $prompt .= "- Teknik terimler varsa uygun karşılıklarını kullanın\n";
        $prompt .= "- Formatı koruyun\n";
        $prompt .= "- Sadece çeviriyi döndürün, başka açıklama eklemeyin\n\n";

        $prompt .= "Çevrilecek metin:\n{$text}";

        return $prompt;
    }

    private function shouldPreserveFormatting(string $fieldType): bool
    {
        return in_array($fieldType, ['html', 'markdown', 'json']);
    }

    private function extractTranslationFromResponse(array $response, string $fieldType): string
    {
        $content = $response['content'] ?? $response['response'] ?? '';
        
        if (empty($content)) {
            throw new \Exception('Empty translation response');
        }

        // Response template varsa kullan
        if (isset($response['formatted_response'])) {
            $formatted = $response['formatted_response'];
            
            if (isset($formatted['translated_text'])) {
                return $formatted['translated_text'];
            }
        }

        // Raw response'dan çeviriyi çıkar
        $cleanedContent = trim($content);
        
        // Eğer response template format'ındaysa parse et
        if (str_starts_with($cleanedContent, '{') && str_ends_with($cleanedContent, '}')) {
            $json = json_decode($cleanedContent, true);
            if ($json && isset($json['translation'])) {
                return $json['translation'];
            }
        }

        return $cleanedContent;
    }

    private function saveTranslatedFields(string $module, AITranslationMappings $mapping, int $recordId, array $translatedFields, string $targetLanguage): void
    {
        $jsonFields = $mapping->json_fields ?? [];
        $updateData = [];
        
        foreach ($translatedFields as $fieldName => $translatedValue) {
            if (in_array($fieldName, $jsonFields)) {
                // JSON field - mevcut değeri al ve güncelle
                $currentValue = DB::table($mapping->table_name)
                    ->where('id', $recordId)
                    ->value($fieldName);
                
                $jsonData = json_decode($currentValue, true) ?: [];
                $jsonData[$targetLanguage] = $translatedValue;
                
                $updateData[$fieldName] = json_encode($jsonData);
            } else {
                // Normal field - yeni kolon oluştur (field_name_lang format)
                $langFieldName = "{$fieldName}_{$targetLanguage}";
                
                // Check if column exists
                if ($this->columnExists($mapping->table_name, $langFieldName)) {
                    $updateData[$langFieldName] = $translatedValue;
                }
            }
        }
        
        if (!empty($updateData)) {
            DB::table($mapping->table_name)
                ->where('id', $recordId)
                ->update($updateData);
        }
    }

    private function columnExists(string $tableName, string $columnName): bool
    {
        return Cache::remember("column_exists_{$tableName}_{$columnName}", 3600, function () use ($tableName, $columnName) {
            return DB::getSchemaBuilder()->hasColumn($tableName, $columnName);
        });
    }
}