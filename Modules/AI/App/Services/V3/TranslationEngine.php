<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\V3;

/**
 * TranslationEngine - V3 ROADMAP Enterprise Service
 * 
 * Multi-language translation with format preservation
 * Bulk translation processing
 * Context-aware translation selection
 */
readonly class TranslationEngine
{
    public function __construct(
        private \Illuminate\Database\DatabaseManager $database,
        private \Illuminate\Cache\Repository $cache
    ) {}

    /**
     * Modül record'ını tüm hedef dillere çevir
     */
    public function translateRecord(string $module, int $recordId, array $targetLanguages = []): array
    {
        // Translation mapping'ini al
        $mapping = $this->getTranslationMapping($module);
        
        if (!$mapping) {
            throw new \Exception("Translation mapping not found for module: {$module}");
        }

        // Hedef dilleri belirle
        if (empty($targetLanguages)) {
            $targetLanguages = $this->getAvailableLanguages();
        }

        // Record'ı getir
        $record = $this->database->table($mapping['table_name'])
            ->where('id', $recordId)
            ->first();

        if (!$record) {
            throw new \Exception("Record not found: {$recordId} in {$mapping['table_name']}");
        }

        $results = [];
        $translatableFields = $mapping['translatable_fields'];

        foreach ($translatableFields as $field) {
            if (!property_exists($record, $field)) {
                continue;
            }

            $originalValue = $record->{$field};
            
            // Field type'ına göre çeviri yap
            $fieldType = $mapping['field_types'][$field] ?? 'text';
            $maxLength = $mapping['max_lengths'][$field] ?? null;
            
            $translatedValues = [];
            
            foreach ($targetLanguages as $targetLang) {
                try {
                    $translatedValue = $this->translateField(
                        $originalValue,
                        'tr', // source language
                        $targetLang,
                        $fieldType,
                        $maxLength
                    );
                    
                    $translatedValues[$targetLang] = $translatedValue;
                    
                } catch (\Exception $e) {
                    $translatedValues[$targetLang] = $originalValue; // Fallback
                }
            }

            $results[$field] = $translatedValues;
        }

        // Results'ı veritabanına kaydet
        $this->saveTranslationResults($mapping['table_name'], $recordId, $results);

        return [
            'module' => $module,
            'record_id' => $recordId,
            'target_languages' => $targetLanguages,
            'translated_fields' => array_keys($results),
            'results' => $results,
            'success' => true
        ];
    }

    /**
     * Tek field'ı çevir
     */
    public function translateField(
        string $text, 
        string $fromLang, 
        string $toLang, 
        string $fieldType = 'text',
        ?int $maxLength = null
    ): string {
        // Cache key oluştur
        $cacheKey = "translation_" . md5($text . $fromLang . $toLang . $fieldType);
        
        return $this->cache->remember($cacheKey, 3600, function() use ($text, $fromLang, $toLang, $fieldType, $maxLength) {
            // Aynı dil ise direkt döndür
            if ($fromLang === $toLang) {
                return $text;
            }

            // Boş text kontrolü
            if (empty(trim($text))) {
                return $text;
            }

            // Field type'ına göre işlem yap
            $translatedText = match($fieldType) {
                'html' => $this->translateHtmlContent($text, $fromLang, $toLang),
                'json' => $this->translateJSON(json_decode($text, true), $fromLang, $toLang),
                'markdown' => $this->translateMarkdownContent($text, $fromLang, $toLang),
                default => $this->translatePlainText($text, $fromLang, $toLang)
            };

            // Max length kontrolü
            if ($maxLength && mb_strlen($translatedText) > $maxLength) {
                $translatedText = mb_substr($translatedText, 0, $maxLength - 3) . '...';
            }

            return $translatedText;
        });
    }

    /**
     * JSON object'i çevir
     */
    public function translateJSON(array $json, string $fromLang, string $toLang): string
    {
        $translatedJson = [];
        
        foreach ($json as $key => $value) {
            if (is_string($value)) {
                $translatedJson[$key] = $this->translatePlainText($value, $fromLang, $toLang);
            } elseif (is_array($value)) {
                // Nested array'leri recursive çevir
                $translatedJson[$key] = json_decode($this->translateJSON($value, $fromLang, $toLang), true);
            } else {
                // Non-string değerleri olduğu gibi bırak
                $translatedJson[$key] = $value;
            }
        }
        
        return json_encode($translatedJson, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Bulk translation - çoklu record'ları çevir
     */
    public function bulkTranslate(array $records, array $targetLanguages): array
    {
        $results = [];
        $errors = [];
        
        foreach ($records as $record) {
            $module = $record['module'];
            $recordId = $record['record_id'];
            
            try {
                $result = $this->translateRecord($module, $recordId, $targetLanguages);
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
            'total_records' => count($records),
            'successful' => count($results),
            'failed' => count($errors),
            'results' => $results,
            'errors' => $errors
        ];
    }

    /**
     * Modül için çevrilebilir field'ları getir
     */
    public function getTranslatableFields(string $module): array
    {
        $mapping = $this->getTranslationMapping($module);
        
        if (!$mapping) {
            return [];
        }
        
        return [
            'module' => $module,
            'table_name' => $mapping['table_name'],
            'translatable_fields' => $mapping['translatable_fields'],
            'field_types' => $mapping['field_types'],
            'max_lengths' => $mapping['max_lengths'],
            'special_rules' => $mapping['special_rules']
        ];
    }

    /**
     * HTML içeriği formatını koruyarak çevir
     */
    public function preserveFormatting(string $content, string $contentType = 'html'): array
    {
        return match($contentType) {
            'html' => $this->extractHtmlStructure($content),
            'markdown' => $this->extractMarkdownStructure($content),
            default => ['text' => $content, 'structure' => []]
        };
    }

    /**
     * Dil tespiti yap
     */
    public function detectLanguage(string $text): string
    {
        // Basit dil tespiti - Türkçe karakter kontrolü
        if (preg_match('/[ğüşıöçĞÜŞİÖÇ]/', $text)) {
            return 'tr';
        }
        
        // Diğer karakteristik pattern'lar
        if (preg_match('/[àáâäèéêëìíîïòóôöùúûü]/', $text)) {
            return 'fr'; // veya diğer Romance dilleri
        }
        
        // Default English
        return 'en';
    }

    /**
     * Translation quality score hesapla
     */
    public function calculateQualityScore(string $originalText, string $translatedText): float
    {
        // Basit quality metrics
        $originalLength = mb_strlen($originalText);
        $translatedLength = mb_strlen($translatedText);
        
        if ($originalLength === 0) return 0.0;
        
        // Length similarity score (0-1)
        $lengthRatio = min($originalLength, $translatedLength) / max($originalLength, $translatedLength);
        
        // Boş çeviri kontrolü
        if (empty(trim($translatedText))) {
            return 0.0;
        }
        
        // Aynı text kontrolü (çeviri yapılmamış)
        if ($originalText === $translatedText) {
            return 0.3; // Düşük skor
        }
        
        // Basit pattern matching
        $patternScore = $this->calculatePatternSimilarity($originalText, $translatedText);
        
        // Final score (0-1)
        $qualityScore = ($lengthRatio * 0.3) + ($patternScore * 0.7);
        
        return round($qualityScore, 2);
    }

    /**
     * Private helper methods
     */
    private function getTranslationMapping(string $module): ?array
    {
        $cacheKey = "translation_mapping_{$module}";
        
        return $this->cache->remember($cacheKey, 1800, function() use ($module) {
            $mapping = $this->database->table('ai_translation_mappings')
                ->where('module_name', $module)
                ->where('is_active', true)
                ->first();

            if (!$mapping) {
                return null;
            }

            return [
                'table_name' => $mapping->table_name,
                'translatable_fields' => json_decode($mapping->translatable_fields, true),
                'json_fields' => json_decode($mapping->json_fields ?? '[]', true),
                'seo_fields' => json_decode($mapping->seo_fields ?? '[]', true),
                'field_types' => json_decode($mapping->field_types, true),
                'max_lengths' => json_decode($mapping->max_lengths ?? '{}', true),
                'special_rules' => json_decode($mapping->special_rules ?? '{}', true)
            ];
        });
    }

    public function getAvailableLanguages(): array
    {
        return $this->cache->remember('available_languages', 1800, function() {
            return $this->database->table('tenant_languages')
                ->where('is_active', true)
                ->pluck('code')
                ->toArray();
        });
    }

    private function translatePlainText(string $text, string $fromLang, string $toLang): string
    {
        // Bu gerçek implementasyonda AI service call yapılacak
        // Şimdilik simulation
        
        // Çok kısa text'ler için özel handling
        if (mb_strlen($text) < 3) {
            return $text;
        }
        
        // Sayı, email, URL gibi özel pattern'ları koru
        if (preg_match('/^\d+$/', $text) || 
            filter_var($text, FILTER_VALIDATE_EMAIL) ||
            filter_var($text, FILTER_VALIDATE_URL)) {
            return $text;
        }
        
        // Basit çeviri simulation
        return $this->simulateTranslation($text, $toLang);
    }

    private function translateHtmlContent(string $html, string $fromLang, string $toLang): string
    {
        // HTML structure'ı koru, sadece text node'ları çevir
        $structure = $this->extractHtmlStructure($html);
        
        // Text kısımlarını çevir
        foreach ($structure['texts'] as $index => $text) {
            if (!empty(trim($text))) {
                $structure['texts'][$index] = $this->translatePlainText($text, $fromLang, $toLang);
            }
        }
        
        // HTML'i yeniden inşa et
        return $this->reconstructHtml($structure);
    }

    private function translateMarkdownContent(string $markdown, string $fromLang, string $toLang): string
    {
        // Markdown structure'ı koru
        $structure = $this->extractMarkdownStructure($markdown);
        
        // Text kısımlarını çevir
        foreach ($structure['texts'] as $index => $text) {
            if (!empty(trim($text))) {
                $structure['texts'][$index] = $this->translatePlainText($text, $fromLang, $toLang);
            }
        }
        
        return $this->reconstructMarkdown($structure);
    }

    private function saveTranslationResults(string $tableName, int $recordId, array $results): void
    {
        $updateData = [];
        
        foreach ($results as $field => $translations) {
            // JSON format'ında kaydet
            $updateData[$field] = json_encode($translations, JSON_UNESCAPED_UNICODE);
        }
        
        if (!empty($updateData)) {
            $updateData['updated_at'] = now();
            
            $this->database->table($tableName)
                ->where('id', $recordId)
                ->update($updateData);
        }
    }

    private function simulateTranslation(string $text, string $toLang): string
    {
        // Gerçek implementasyonda AI service call yapılacak
        $prefixes = [
            'en' => '[EN]',
            'de' => '[DE]',
            'fr' => '[FR]',
            'es' => '[ES]',
            'it' => '[IT]'
        ];
        
        $prefix = $prefixes[$toLang] ?? "[{$toLang}]";
        
        return $prefix . ' ' . $text;
    }

    private function extractHtmlStructure(string $html): array
    {
        // Basit HTML parsing
        $texts = [];
        $structure = [];
        
        // Text node'ları çıkar
        preg_match_all('/>([^<]+)</i', $html, $matches);
        
        if (isset($matches[1])) {
            $texts = array_map('trim', $matches[1]);
            $texts = array_filter($texts); // Boş text'leri kaldır
        }
        
        return [
            'original' => $html,
            'texts' => $texts,
            'structure' => $structure
        ];
    }

    private function extractMarkdownStructure(string $markdown): array
    {
        // Basit Markdown parsing
        $texts = [];
        
        // Headers, paragraphs, etc. ayır
        $lines = explode("\n", $markdown);
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            // Skip empty lines
            if (empty($trimmed)) continue;
            
            // Headers (#, ##, ###)
            if (preg_match('/^#{1,6}\s+(.+)$/', $trimmed, $matches)) {
                $texts[] = $matches[1];
                continue;
            }
            
            // Regular text
            if (!preg_match('/^[\-\*\+]\s|^\d+\.\s/', $trimmed)) {
                $texts[] = $trimmed;
            }
        }
        
        return [
            'original' => $markdown,
            'texts' => $texts,
            'structure' => []
        ];
    }

    private function reconstructHtml(array $structure): string
    {
        // Basit HTML reconstruction
        $html = $structure['original'];
        
        foreach ($structure['texts'] as $index => $translatedText) {
            // İlk bulunan text'i değiştir (basit approach)
            $html = preg_replace('/>([^<]+)</i', '>' . $translatedText . '<', $html, 1);
        }
        
        return $html;
    }

    private function reconstructMarkdown(array $structure): string
    {
        // Basit Markdown reconstruction
        return implode("\n\n", $structure['texts']);
    }

    private function calculatePatternSimilarity(string $text1, string $text2): float
    {
        // Basit pattern similarity
        $len1 = mb_strlen($text1);
        $len2 = mb_strlen($text2);
        
        if ($len1 === 0 && $len2 === 0) return 1.0;
        if ($len1 === 0 || $len2 === 0) return 0.0;
        
        // Levenshtein distance kullanarak similarity hesapla
        $distance = levenshtein(
            mb_substr($text1, 0, 255), // Levenshtein limiti
            mb_substr($text2, 0, 255)
        );
        
        $maxLen = max($len1, $len2);
        $similarity = 1 - ($distance / $maxLen);
        
        return max(0.0, min(1.0, $similarity));
    }
}