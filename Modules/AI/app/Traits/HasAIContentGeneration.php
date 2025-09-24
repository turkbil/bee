<?php

declare(strict_types=1);

namespace Modules\AI\app\Traits;

use Modules\AI\app\Services\Content\AIContentGeneratorService;
use Illuminate\Support\Facades\Log;

/**
 * GLOBAL AI Content Generation Trait
 *
 * Herhangi bir modül bu trait'i kullanarak AI content generation'dan faydalanabilir.
 * Module-agnostic tasarım ile tüm modüller için uyumlu.
 */
trait HasAIContentGeneration
{
    /**
     * AI ile içerik üret - GLOBAL kullanım
     */
    public function generateAIContent(array $params): array
    {
        try {
            // Module context'i otomatik belirle
            $moduleContext = $this->buildModuleContext($params);

            // Global parametreleri hazırla
            $globalParams = array_merge($params, [
                'module_context' => $moduleContext,
                'tenant_id' => $params['tenant_id'] ?? tenant('id')
            ]);

            Log::info('🌍 GLOBAL AI Content Generation başlatıldı', [
                'module' => $moduleContext['module'] ?? 'unknown',
                'entity_type' => $moduleContext['entity_type'] ?? 'unknown',
                'params_keys' => array_keys($params)
            ]);

            // Global AI Content Generator servisini kullan
            $aiContentGenerator = app(AIContentGeneratorService::class);
            $result = $aiContentGenerator->generateContent($globalParams);

            // Module-specific post-processing
            if (method_exists($this, 'postProcessAIContent')) {
                $result = $this->postProcessAIContent($result, $params);
            }

            Log::info('✅ GLOBAL AI Content Generation tamamlandı', [
                'success' => $result['success'] ?? false,
                'content_length' => isset($result['content']) ? strlen($result['content']) : 0,
                'module' => $moduleContext['module'] ?? 'unknown'
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('❌ GLOBAL AI Content Generation hatası', [
                'module' => get_class($this),
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            return [
                'success' => false,
                'error' => 'AI içerik üretiminde hata oluştu',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Module context'i oluştur - Her modül kendi context'ini belirler
     */
    protected function buildModuleContext(array $params): array
    {
        // Varsayılan context
        $context = [
            'module' => $this->getModuleName(),
            'entity_type' => $this->getEntityType(),
            'fields' => $this->getTargetFields($params),
            'instructions' => $this->getModuleInstructions()
        ];

        // Özel gereksinimler varsa ekle
        if (isset($params['specific_requirements'])) {
            $context['specific_requirements'] = $params['specific_requirements'];
        }

        return $context;
    }

    /**
     * Modül adını al - Her modül override edebilir
     */
    public function getModuleName(): string
    {
        $className = get_class($this);

        // Namespace'den modül adını çıkar
        if (preg_match('/Modules\\\\([^\\\\]+)\\\\/', $className, $matches)) {
            return $matches[1];
        }

        return 'Unknown';
    }

    /**
     * Entity tipini al - Her modül override edebilir
     */
    public function getEntityType(): string
    {
        return 'content';
    }

    /**
     * Hedef alanları al - Her modül override edebilir
     */
    public function getTargetFields(array $params): array
    {
        // Varsayılan alanlar
        $defaultFields = [
            'title' => 'string',
            'content' => 'html',
            'description' => 'text'
        ];

        // Params'dan belirli alan varsa onu kullan
        if (isset($params['target_field'])) {
            return [$params['target_field'] => 'html'];
        }

        return $defaultFields;
    }

    /**
     * Modül talimatlarını al - Her modül override edebilir
     */
    public function getModuleInstructions(): string
    {
        return 'Genel içerik üretimi. Modül-specific talimatlar için bu metodu override edin.';
    }

    /**
     * AI prompt'u modül context'ine göre zenginleştir
     */
    protected function enrichPromptWithModuleContext(string $basePrompt, array $moduleContext): string
    {
        $enrichedPrompt = $basePrompt;

        // Modül bilgisini ekle
        $moduleName = $moduleContext['module'] ?? 'Unknown';
        $entityType = $moduleContext['entity_type'] ?? 'content';

        $enrichedPrompt = "[{$moduleName} Modülü - {$entityType}] " . $enrichedPrompt;

        // Modül talimatlarını ekle
        if (isset($moduleContext['instructions'])) {
            $enrichedPrompt .= "\n\nMODÜL TALİMATLARI: {$moduleContext['instructions']}";
        }

        // Hedef alanları ekle
        if (isset($moduleContext['fields']) && is_array($moduleContext['fields'])) {
            $fieldsText = implode(', ', array_keys($moduleContext['fields']));
            $enrichedPrompt .= "\n\nHEDEF ALANLAR: {$fieldsText}";
        }

        return $enrichedPrompt;
    }

    /**
     * Batch content generation - Birden fazla field için
     */
    public function generateBatchAIContent(array $fields, array $baseParams): array
    {
        $results = [];

        foreach ($fields as $field => $prompt) {
            $params = array_merge($baseParams, [
                'prompt' => $prompt,
                'target_field' => $field
            ]);

            $results[$field] = $this->generateAIContent($params);
        }

        return $results;
    }

    /**
     * Content validation - AI üretilen içeriği doğrula
     */
    protected function validateAIContent(string $content, string $fieldType = 'html'): bool
    {
        // Boş içerik kontrolü
        if (empty(trim($content))) {
            return false;
        }

        // HTML field için temel validation
        if ($fieldType === 'html') {
            // Script tag'larının olmaması
            if (strpos($content, '<script') !== false) {
                return false;
            }

            // Temel HTML structure kontrolü
            if (!preg_match('/<[^>]+>/', $content)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Content formatting - Modül ihtiyacına göre format
     */
    protected function formatContentForModule(string $content, string $targetField): string
    {
        // Varsayılan formatting
        $formatted = trim($content);

        // Field tipine göre özel formatting
        switch ($targetField) {
            case 'title':
                // Title için HTML tag'larını kaldır
                $formatted = strip_tags($formatted);
                $formatted = trim($formatted);
                break;

            case 'description':
            case 'excerpt':
                // Description için HTML tag'larını kaldır ve kısalt
                $formatted = strip_tags($formatted);
                $formatted = mb_substr($formatted, 0, 300);
                break;

            case 'content':
            case 'body':
            default:
                // HTML content için special formatting yok
                break;
        }

        return $formatted;
    }

    /**
     * Module-specific error handling
     */
    protected function handleAIContentError(\Exception $e, array $params): array
    {
        Log::error('AI Content Generation Error in Module', [
            'module' => $this->getModuleName(),
            'entity_type' => $this->getEntityType(),
            'error' => $e->getMessage(),
            'params' => $params
        ]);

        return [
            'success' => false,
            'error' => 'Modül içerik üretiminde hata oluştu',
            'message' => $e->getMessage(),
            'module' => $this->getModuleName()
        ];
    }
}