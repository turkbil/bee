<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Opis\JsonSchema\Validator;
use Opis\JsonSchema\Errors\ErrorFormatter;

class SeoLanguageManager
{
    /**
     * Bulletproof JSON Language Management - 7 Layer Security
     * 
     * Layer 1: JSON Structure Validation
     * Layer 2: Data Type Safety 
     * Layer 3: Fallback Chain
     * Layer 4: Error Recovery
     * Layer 5: Data Sanitization
     * Layer 6: Cache Invalidation
     * Layer 7: Audit Logging
     */

    // Dinamik dil listesi - artık hardcode değil
    private static ?array $supportedLanguages = null;
    private static ?string $defaultLanguage = null;
    private static int $maxStringLength = 500;
    private static int $maxArrayItems = 50;

    /**
     * Dinamik dil listesi alma - TenantLanguageProvider entegrasyonu
     */
    private static function initializeLanguages(): void
    {
        if (self::$supportedLanguages === null) {
            try {
                self::$supportedLanguages = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();
                self::$defaultLanguage = \App\Services\TenantLanguageProvider::getDefaultLanguageCode();
            } catch (\Exception $e) {
                // Fallback to basic languages if service fails
                self::$supportedLanguages = ['tr', 'en'];
                self::$defaultLanguage = 'tr';
            }
        }
    }

    /**
     * Layer 1: JSON Structure Validation
     * Validates JSON structure against predefined schema
     */
    public static function validateJsonStructure(array $data, string $type = 'basic'): array
    {
        $validator = new Validator();
        $schema = self::getJsonSchema($type);
        
        try {
            $result = $validator->validate((object)$data, $schema);
            
            if (!$result->isValid()) {
                $formatter = new ErrorFormatter();
                $errors = $formatter->format($result->error());
                
                Log::warning('SEO JSON validation failed', [
                    'type' => $type,
                    'errors' => $errors,
                    'data' => $data
                ]);
                
                return self::getDefaultStructure($type);
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::error('SEO JSON validation exception', [
                'type' => $type,
                'exception' => $e->getMessage(),
                'data' => $data
            ]);
            
            return self::getDefaultStructure($type);
        }
    }

    /**
     * Layer 2: Data Type Safety
     * Ensures data types are correct and safe
     */
    public static function sanitizeLanguageData($data): array
    {
        self::initializeLanguages();
        
        if (!is_array($data)) {
            if (is_string($data)) {
                // Convert string to default language structure
                return [self::$defaultLanguage => self::sanitizeString($data)];
            }
            return [];
        }

        $sanitized = [];
        
        foreach ($data as $locale => $value) {
            // Validate locale code
            if (!self::isValidLocale($locale)) {
                continue;
            }
            
            // Sanitize value based on type
            if (is_string($value)) {
                $sanitized[$locale] = self::sanitizeString($value);
            } elseif (is_array($value)) {
                $sanitized[$locale] = self::sanitizeArray($value);
            }
        }
        
        return $sanitized;
    }

    /**
     * Layer 3: Fallback Chain
     * Implements intelligent fallback mechanism
     */
    public static function getSafeValue(array|string $data, string $locale, string $fallbackLocale = null): mixed
    {
        self::initializeLanguages();
        $fallbackLocale = $fallbackLocale ?? self::$defaultLanguage;
        
        // Eğer string gelirse direkt döndür
        if (is_string($data)) {
            return $data;
        }
        
        // Primary: Requested locale
        if (isset($data[$locale]) && !empty($data[$locale])) {
            return $data[$locale];
        }
        
        // Secondary: Fallback locale
        if (isset($data[$fallbackLocale]) && !empty($data[$fallbackLocale])) {
            return $data[$fallbackLocale];
        }
        
        // Tertiary: Default language
        if (isset($data[self::$defaultLanguage]) && !empty($data[self::$defaultLanguage])) {
            return $data[self::$defaultLanguage];
        }
        
        // Quaternary: First available language
        foreach (self::$supportedLanguages as $supportedLang) {
            if (isset($data[$supportedLang]) && !empty($data[$supportedLang])) {
                return $data[$supportedLang];
            }
        }
        
        // Final: Any non-empty value
        foreach ($data as $value) {
            if (!empty($value)) {
                return $value;
            }
        }
        
        return null;
    }

    /**
     * Layer 4: Error Recovery
     * Recovers from corrupted JSON data
     */
    public static function recoverCorruptedData(array $data, string $type = 'basic'): array
    {
        $recovered = [];
        
        foreach ($data as $locale => $value) {
            try {
                // Attempt to validate and fix locale
                $validLocale = self::validateAndFixLocale($locale);
                if (!$validLocale) {
                    continue;
                }
                
                // Attempt to recover value
                $recoveredValue = self::recoverValue($value, $type);
                if ($recoveredValue !== null) {
                    $recovered[$validLocale] = $recoveredValue;
                }
                
            } catch (\Exception $e) {
                Log::warning('Failed to recover SEO data item', [
                    'locale' => $locale,
                    'value' => $value,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
        
        // Ensure at least default language exists
        if (empty($recovered) || !isset($recovered[self::$defaultLanguage])) {
            $recovered[self::$defaultLanguage] = self::getDefaultValue($type);
        }
        
        return $recovered;
    }

    /**
     * Layer 5: Data Sanitization
     * Deep sanitizes all input data
     */
    public static function sanitizeString(string $value): string
    {
        // Remove null bytes and control characters
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        // Trim whitespace
        $value = trim($value);
        
        // Limit length
        if (mb_strlen($value) > self::$maxStringLength) {
            $value = mb_substr($value, 0, self::$maxStringLength - 3) . '...';
        }
        
        // Escape special characters
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        return $value;
    }

    /**
     * Layer 5: Array Sanitization
     */
    public static function sanitizeArray(array $value): array
    {
        $sanitized = [];
        $count = 0;
        
        foreach ($value as $item) {
            if ($count >= self::$maxArrayItems) {
                break;
            }
            
            if (is_string($item)) {
                $sanitizedItem = self::sanitizeString($item);
                if (!empty($sanitizedItem)) {
                    $sanitized[] = $sanitizedItem;
                    $count++;
                }
            }
        }
        
        return array_values(array_unique($sanitized));
    }

    /**
     * Layer 6: Cache Invalidation
     * Manages cache for language data
     */
    public static function invalidateLanguageCache(string $model, int $id): void
    {
        $cacheKeys = [
            "seo_lang_{$model}_{$id}",
            "seo_meta_{$model}_{$id}",
            "seo_og_{$model}_{$id}",
            "seo_twitter_{$model}_{$id}",
            "seo_schema_{$model}_{$id}"
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        // Clear pattern-based cache
        Cache::tags(["seo_{$model}", "seo_lang"])->flush();
    }

    /**
     * Layer 7: Audit Logging
     * Logs all critical language operations
     */
    public static function auditLanguageChange(string $operation, array $context): void
    {
        Log::info('SEO Language Operation', [
            'operation' => $operation,
            'timestamp' => now()->toISOString(),
            'context' => $context,
            'user_id' => auth()->id(),
            'tenant_id' => tenant('id'),
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Language Management Operations
     */
    
    /**
     * Add new language support
     */
    public static function addLanguageSupport(array &$data, string $locale, $value = null): array
    {
        if (!self::isValidLocale($locale)) {
            throw new \InvalidArgumentException("Invalid locale: {$locale}");
        }
        
        $data = self::sanitizeLanguageData($data);
        
        if ($value !== null) {
            if (is_string($value)) {
                $data[$locale] = self::sanitizeString($value);
            } elseif (is_array($value)) {
                $data[$locale] = self::sanitizeArray($value);
            }
        } else {
            // Copy from default language as template
            $defaultValue = $data[self::$defaultLanguage] ?? '';
            $data[$locale] = $defaultValue;
        }
        
        self::auditLanguageChange('add_language', [
            'locale' => $locale,
            'value_provided' => $value !== null
        ]);
        
        return $data;
    }

    /**
     * Remove language support
     */
    public static function removeLanguageSupport(array &$data, string $locale): array
    {
        if ($locale === self::$defaultLanguage) {
            throw new \InvalidArgumentException("Cannot remove default language: {$locale}");
        }
        
        if (isset($data[$locale])) {
            unset($data[$locale]);
            
            self::auditLanguageChange('remove_language', [
                'locale' => $locale
            ]);
        }
        
        // Ensure at least default language exists
        if (empty($data)) {
            $data[self::$defaultLanguage] = '';
        }
        
        return $data;
    }

    /**
     * Migrate language data
     */
    public static function migrateLanguageData(array $oldData, array $languageMapping): array
    {
        $migrated = [];
        
        foreach ($oldData as $oldLocale => $value) {
            $newLocale = $languageMapping[$oldLocale] ?? $oldLocale;
            
            if (self::isValidLocale($newLocale)) {
                $migrated[$newLocale] = $value;
            }
        }
        
        self::auditLanguageChange('migrate_languages', [
            'mapping' => $languageMapping,
            'old_count' => count($oldData),
            'new_count' => count($migrated)
        ]);
        
        return self::sanitizeLanguageData($migrated);
    }

    /**
     * Bulk update language data
     */
    public static function bulkUpdateLanguageData(array &$data, array $updates): array
    {
        foreach ($updates as $locale => $value) {
            if (!self::isValidLocale($locale)) {
                continue;
            }
            
            if (is_string($value)) {
                $data[$locale] = self::sanitizeString($value);
            } elseif (is_array($value)) {
                $data[$locale] = self::sanitizeArray($value);
            } elseif ($value === null) {
                unset($data[$locale]);
            }
        }
        
        self::auditLanguageChange('bulk_update', [
            'locales' => array_keys($updates),
            'count' => count($updates)
        ]);
        
        return $data;
    }

    /**
     * Helper Methods
     */
    
    private static function isValidLocale(string $locale): bool
    {
        return in_array($locale, self::$supportedLanguages) || 
               preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $locale);
    }

    private static function validateAndFixLocale(string $locale): ?string
    {
        // Fix common issues
        $locale = strtolower(trim($locale));
        
        if (strlen($locale) === 2 && ctype_alpha($locale)) {
            return $locale;
        }
        
        // Try to extract valid locale
        if (preg_match('/([a-z]{2})/', $locale, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    private static function recoverValue($value, string $type): mixed
    {
        if (is_string($value)) {
            return self::sanitizeString($value);
        }
        
        if (is_array($value)) {
            return self::sanitizeArray($value);
        }
        
        // Try to convert other types
        if (is_numeric($value) || is_bool($value)) {
            return self::sanitizeString((string)$value);
        }
        
        return null;
    }

    private static function getDefaultValue(string $type): mixed
    {
        return match($type) {
            'title', 'description' => '',
            'keywords' => [],
            'robots' => ['index' => true, 'follow' => true],
            'schema' => [],
            default => ''
        };
    }

    private static function getDefaultStructure(string $type): array
    {
        return [
            self::$defaultLanguage => self::getDefaultValue($type)
        ];
    }

    private static function getJsonSchema(string $type): object
    {
        $baseSchema = [
            'type' => 'object',
            'patternProperties' => [
                '^[a-z]{2}(-[A-Z]{2})?$' => []
            ],
            'additionalProperties' => false
        ];

        $schemas = [
            'title' => [
                'patternProperties' => [
                    '^[a-z]{2}(-[A-Z]{2})?$' => [
                        'type' => 'string',
                        'maxLength' => self::$maxStringLength
                    ]
                ]
            ],
            'description' => [
                'patternProperties' => [
                    '^[a-z]{2}(-[A-Z]{2})?$' => [
                        'type' => 'string',
                        'maxLength' => self::$maxStringLength
                    ]
                ]
            ],
            'keywords' => [
                'patternProperties' => [
                    '^[a-z]{2}(-[A-Z]{2})?$' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'string',
                            'maxLength' => 100
                        ],
                        'maxItems' => self::$maxArrayItems
                    ]
                ]
            ],
            'basic' => $baseSchema
        ];

        return (object)array_merge($baseSchema, $schemas[$type] ?? $schemas['basic']);
    }

    /**
     * Quick access methods for common operations
     */
    
    public static function quickSanitize($data): array
    {
        return self::sanitizeLanguageData($data);
    }

    public static function quickValidate(array $data, string $type = 'basic'): array
    {
        return self::validateJsonStructure($data, $type);
    }

    public static function quickRecover(array $data, string $type = 'basic'): array
    {
        return self::recoverCorruptedData($data, $type);
    }

    public static function getSupportedLanguages(): array
    {
        self::initializeLanguages();
        return self::$supportedLanguages;
    }

    public static function getDefaultLanguage(): string
    {
        self::initializeLanguages();
        return self::$defaultLanguage;
    }

    public static function setDefaultLanguage(string $locale): void
    {
        if (!self::isValidLocale($locale)) {
            throw new \InvalidArgumentException("Invalid default locale: {$locale}");
        }
        
        self::$defaultLanguage = $locale;
    }

    public static function addSupportedLanguage(string $locale): void
    {
        if (!self::isValidLocale($locale)) {
            throw new \InvalidArgumentException("Invalid locale: {$locale}");
        }
        
        if (!in_array($locale, self::$supportedLanguages)) {
            self::$supportedLanguages[] = $locale;
        }
    }
}