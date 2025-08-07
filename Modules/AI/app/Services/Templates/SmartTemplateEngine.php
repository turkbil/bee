<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Templates;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

/**
 * 🎨 Smart Template Engine - AI Response Template System with Inheritance
 * 
 * Özellikler:
 * - Template inheritance (parent-child relationships)
 * - Dynamic variable replacement
 * - Conditional rendering
 * - Cache optimization
 * - Feature-specific template loading
 */
readonly class SmartTemplateEngine
{
    private const CACHE_PREFIX = 'smart_templates';
    private const CACHE_TTL = 1800; // 30 minutes
    
    public function __construct(
        private TemplateRepository $templateRepository
    ) {}

    /**
     * 🏗️ Template'i render et - inheritance desteği ile
     *
     * @param string $templateName Template identifier
     * @param array $variables Template variables
     * @param array $options Rendering options
     * @return string Rendered template
     */
    public function render(string $templateName, array $variables = [], array $options = []): string
    {
        try {
            // Template'i cache'den al veya yükle
            $template = $this->getTemplate($templateName);
            if (!$template) {
                return $this->renderFallbackTemplate($variables, $options);
            }

            // Inheritance chain'i oluştur
            $templateChain = $this->buildInheritanceChain($template);

            // Template'leri merge et (parent → child)
            $mergedTemplate = $this->mergeTemplateChain($templateChain);

            // Variables'ı process et
            $processedVariables = $this->processVariables($variables, $options);

            // Template'i render et
            $rendered = $this->renderTemplate($mergedTemplate, $processedVariables, $options);

            return $this->postProcessTemplate($rendered, $options);

        } catch (\Exception $e) {
            \Log::warning('Template rendering failed', [
                'template' => $templateName,
                'error' => $e->getMessage(),
                'variables' => array_keys($variables)
            ]);

            return $this->renderFallbackTemplate($variables, $options);
        }
    }

    /**
     * 🔗 Inheritance chain oluştur
     *
     * @param array $template Base template
     * @return array Template chain (parent → child)
     */
    private function buildInheritanceChain(array $template): array
    {
        $chain = [$template];
        $current = $template;

        // Parent template'leri takip et
        while (!empty($current['parent_template'])) {
            $parent = $this->getTemplate($current['parent_template']);
            if (!$parent) {
                break;
            }

            // Circular inheritance kontrolü
            if ($this->hasCircularInheritance($chain, $parent['name'])) {
                \Log::warning('Circular inheritance detected', [
                    'template' => $template['name'],
                    'parent' => $parent['name']
                ]);
                break;
            }

            array_unshift($chain, $parent);
            $current = $parent;
        }

        return $chain;
    }

    /**
     * 🔄 Template chain'i merge et
     *
     * @param array $templateChain Parent'tan child'a template listesi
     * @return array Merged template
     */
    private function mergeTemplateChain(array $templateChain): array
    {
        $merged = [
            'sections' => [],
            'variables' => [],
            'conditions' => [],
            'format' => 'markdown',
            'structure' => []
        ];

        foreach ($templateChain as $template) {
            // Sections'ları merge et
            if (!empty($template['sections'])) {
                foreach ($template['sections'] as $sectionName => $sectionContent) {
                    $merged['sections'][$sectionName] = $sectionContent;
                }
            }

            // Variables'ları merge et
            if (!empty($template['variables'])) {
                $merged['variables'] = array_merge($merged['variables'], $template['variables']);
            }

            // Conditions'ları merge et
            if (!empty($template['conditions'])) {
                $merged['conditions'] = array_merge($merged['conditions'], $template['conditions']);
            }

            // Format'ı override et (child wins)
            if (!empty($template['format'])) {
                $merged['format'] = $template['format'];
            }

            // Structure'ı merge et
            if (!empty($template['structure'])) {
                $merged['structure'] = array_merge($merged['structure'], $template['structure']);
            }
        }

        return $merged;
    }

    /**
     * 🔄 Variables'ları process et ve extend et
     *
     * @param array $variables Input variables
     * @param array $options Processing options
     * @return array Processed variables
     */
    private function processVariables(array $variables, array $options): array
    {
        $processed = $variables;

        // Context-aware variables
        $processed['_mode'] = $options['mode'] ?? 'default';
        $processed['_timestamp'] = now()->format('Y-m-d H:i:s');
        $processed['_feature'] = $options['feature_name'] ?? null;

        // Tenant context variables
        if ($tenantContext = $this->getTenantContextVariables()) {
            $processed = array_merge($processed, $tenantContext);
        }

        // Dynamic variables
        $processed['_word_count_target'] = $this->calculateWordCountTarget($variables, $options);
        $processed['_response_tone'] = $this->determineResponseTone($variables, $options);
        $processed['_content_type'] = $this->determineContentType($variables, $options);

        return $processed;
    }

    /**
     * 🎨 Template'i render et - variable replacement ile
     *
     * @param array $template Merged template
     * @param array $variables Processed variables
     * @param array $options Rendering options
     * @return string Rendered content
     */
    private function renderTemplate(array $template, array $variables, array $options): string
    {
        $sections = [];

        // Template structure'ını takip et
        $structure = $template['structure'] ?? array_keys($template['sections'] ?? []);

        foreach ($structure as $sectionName) {
            if (!isset($template['sections'][$sectionName])) {
                continue;
            }

            $sectionContent = $template['sections'][$sectionName];

            // Conditional rendering kontrolü
            if (!$this->shouldRenderSection($sectionName, $template, $variables, $options)) {
                continue;
            }

            // Variable replacement
            $renderedSection = $this->replaceVariables($sectionContent, $variables);

            // Section post-processing
            $renderedSection = $this->postProcessSection($renderedSection, $sectionName, $options);

            if (!empty(trim($renderedSection))) {
                $sections[] = $renderedSection;
            }
        }

        return implode("\n\n", $sections);
    }

    /**
     * 🔍 Section render edilmeli mi kontrol et
     *
     * @param string $sectionName Section adı
     * @param array $template Template data
     * @param array $variables Variables
     * @param array $options Options
     * @return bool Should render
     */
    private function shouldRenderSection(string $sectionName, array $template, array $variables, array $options): bool
    {
        // Condition kontrolü
        if (!empty($template['conditions'][$sectionName])) {
            $condition = $template['conditions'][$sectionName];
            return $this->evaluateCondition($condition, $variables, $options);
        }

        // Mode-based rendering
        $sectionMode = $template['sections'][$sectionName . '_mode'] ?? null;
        if ($sectionMode && $sectionMode !== ($options['mode'] ?? 'default')) {
            return false;
        }

        return true;
    }

    /**
     * 🔄 Variable replacement işlemi
     *
     * @param string $content Template content
     * @param array $variables Variable values
     * @return string Content with replaced variables
     */
    private function replaceVariables(string $content, array $variables): string
    {
        // Simple variable replacement: {{variable_name}}
        $content = preg_replace_callback('/\{\{([^}]+)\}\}/', function ($matches) use ($variables) {
            $varName = trim($matches[1]);
            
            // Nested variable support: {{user.name}}
            if (str_contains($varName, '.')) {
                return $this->getNestedVariable($varName, $variables);
            }

            return $variables[$varName] ?? $matches[0];
        }, $content);

        // Conditional variable replacement: {{?condition:content}}
        $content = preg_replace_callback('/\{\{\?([^:]+):([^}]+)\}\}/', function ($matches) use ($variables) {
            $condition = trim($matches[1]);
            $conditionContent = trim($matches[2]);

            if ($this->evaluateSimpleCondition($condition, $variables)) {
                return $conditionContent;
            }

            return '';
        }, $content);

        // Loop replacement: {{#foreach items:item}}content{{/foreach}}
        $content = preg_replace_callback('/\{\{#foreach\s+([^:]+):([^}]+)\}\}(.*?)\{\{\/foreach\}\}/s', function ($matches) use ($variables) {
            $arrayVar = trim($matches[1]);
            $itemVar = trim($matches[2]);
            $loopContent = $matches[3];

            if (!isset($variables[$arrayVar]) || !is_array($variables[$arrayVar])) {
                return '';
            }

            $output = [];
            foreach ($variables[$arrayVar] as $item) {
                $loopVariables = array_merge($variables, [$itemVar => $item]);
                $output[] = $this->replaceVariables($loopContent, $loopVariables);
            }

            return implode("\n", $output);
        }, $content);

        return $content;
    }

    /**
     * 🔍 Nested variable al (user.name gibi)
     *
     * @param string $varPath Variable path
     * @param array $variables Variables array
     * @return string Variable value
     */
    private function getNestedVariable(string $varPath, array $variables): string
    {
        $parts = explode('.', $varPath);
        $value = $variables;

        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return "{{$varPath}}";
            }
            $value = $value[$part];
        }

        return is_string($value) ? $value : json_encode($value);
    }

    /**
     * ✅ Basit condition evaluate et
     *
     * @param string $condition Condition string
     * @param array $variables Variables
     * @return bool Condition result
     */
    private function evaluateSimpleCondition(string $condition, array $variables): bool
    {
        // Simple conditions: variable_name, !variable_name
        if (str_starts_with($condition, '!')) {
            $varName = substr($condition, 1);
            return empty($variables[$varName]);
        }

        return !empty($variables[$condition]);
    }

    /**
     * ✅ Complex condition evaluate et
     *
     * @param array $condition Condition definition
     * @param array $variables Variables
     * @param array $options Options
     * @return bool Condition result
     */
    private function evaluateCondition(array $condition, array $variables, array $options): bool
    {
        $type = $condition['type'] ?? 'simple';

        return match ($type) {
            'simple' => !empty($variables[$condition['variable'] ?? '']),
            'equals' => ($variables[$condition['variable'] ?? ''] ?? null) === ($condition['value'] ?? null),
            'contains' => str_contains($variables[$condition['variable'] ?? ''] ?? '', $condition['value'] ?? ''),
            'mode' => ($options['mode'] ?? 'default') === ($condition['mode'] ?? ''),
            'feature' => ($options['feature_name'] ?? '') === ($condition['feature'] ?? ''),
            default => true
        };
    }

    /**
     * 📄 Template'i cache'den al veya load et
     *
     * @param string $templateName Template name
     * @return array|null Template data
     */
    private function getTemplate(string $templateName): ?array
    {
        $cacheKey = self::CACHE_PREFIX . ":{$templateName}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($templateName) {
            return $this->templateRepository->findByName($templateName);
        });
    }

    /**
     * 🔄 Circular inheritance kontrolü
     *
     * @param array $chain Current template chain
     * @param string $templateName New template name
     * @return bool Has circular reference
     */
    private function hasCircularInheritance(array $chain, string $templateName): bool
    {
        foreach ($chain as $template) {
            if ($template['name'] === $templateName) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🎯 Word count target hesapla
     *
     * @param array $variables Variables
     * @param array $options Options
     * @return int Target word count
     */
    private function calculateWordCountTarget(array $variables, array $options): int
    {
        // User input'tan word count tespiti
        $userInput = $variables['user_input'] ?? '';
        
        if (preg_match('/(\d+)\s*(kelime|word)/i', $userInput, $matches)) {
            return (int)$matches[1];
        }

        // Content type based defaults
        $contentType = $this->determineContentType($variables, $options);
        
        return match ($contentType) {
            'tweet' => 50,
            'summary' => 200,
            'blog_post' => 800,
            'article' => 1200,
            'detailed' => 1500,
            default => 600
        };
    }

    /**
     * 🎭 Response tone belirle
     *
     * @param array $variables Variables
     * @param array $options Options
     * @return string Response tone
     */
    private function determineResponseTone(array $variables, array $options): string
    {
        $mode = $options['mode'] ?? 'feature';
        
        if ($mode === 'chat') {
            return 'friendly';
        }

        // Feature'a göre tone belirleme
        $featureName = $options['feature_name'] ?? '';
        
        if (str_contains($featureName, 'professional')) {
            return 'professional';
        }
        
        if (str_contains($featureName, 'creative')) {
            return 'creative';
        }

        return 'informative';
    }

    /**
     * 📝 Content type belirle
     *
     * @param array $variables Variables
     * @param array $options Options
     * @return string Content type
     */
    private function determineContentType(array $variables, array $options): string
    {
        $userInput = strtolower($variables['user_input'] ?? '');
        
        if (str_contains($userInput, 'tweet')) return 'tweet';
        if (str_contains($userInput, 'özet')) return 'summary';
        if (str_contains($userInput, 'blog')) return 'blog_post';
        if (str_contains($userInput, 'makale')) return 'article';
        if (str_contains($userInput, 'detaylı')) return 'detailed';

        return 'general';
    }

    /**
     * 🏢 Tenant context variables al
     *
     * @return array Tenant variables
     */
    private function getTenantContextVariables(): array
    {
        try {
            $tenant = tenant();
            if (!$tenant) {
                return [];
            }

            return [
                'tenant_id' => $tenant->id,
                'tenant_domain' => $tenant->domains()->first()->domain ?? 'unknown',
                'company_name' => 'Company Name' // Bu AITenantProfile'dan gelecek
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 🔧 Section post-processing
     *
     * @param string $content Section content
     * @param string $sectionName Section name
     * @param array $options Options
     * @return string Processed content
     */
    private function postProcessSection(string $content, string $sectionName, array $options): string
    {
        // Section-specific formatting
        if ($sectionName === 'title') {
            return "# " . trim($content);
        }

        if ($sectionName === 'subtitle') {
            return "## " . trim($content);
        }

        if ($sectionName === 'list_item') {
            return "- " . trim($content);
        }

        return trim($content);
    }

    /**
     * 🔧 Template post-processing
     *
     * @param string $content Rendered content
     * @param array $options Options
     * @return string Final processed content
     */
    private function postProcessTemplate(string $content, array $options): string
    {
        // Empty line cleanup
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        
        // Trim whitespace
        $content = trim($content);

        // Format-specific post-processing
        $format = $options['format'] ?? 'markdown';
        
        if ($format === 'html') {
            $content = $this->convertMarkdownToHtml($content);
        }

        return $content;
    }

    /**
     * 🚨 Fallback template render et
     *
     * @param array $variables Variables
     * @param array $options Options
     * @return string Fallback content
     */
    private function renderFallbackTemplate(array $variables, array $options): string
    {
        $sections = [];

        // Basic structure
        if (!empty($variables['title'])) {
            $sections[] = "# " . $variables['title'];
        }

        if (!empty($variables['content'])) {
            $sections[] = $variables['content'];
        }

        // Default content if nothing available
        if (empty($sections)) {
            $sections[] = "İçerik template'i render edilemedi. Lütfen template konfigürasyonunu kontrol edin.";
        }

        return implode("\n\n", $sections);
    }

    /**
     * 🔄 Markdown to HTML convert et
     *
     * @param string $markdown Markdown content
     * @return string HTML content
     */
    private function convertMarkdownToHtml(string $markdown): string
    {
        // Basic markdown to HTML conversion
        $html = $markdown;
        
        // Headers
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        
        // Paragraphs
        $html = preg_replace('/\n\n/', '</p><p>', $html);
        $html = '<p>' . $html . '</p>';
        
        // Clean up empty paragraphs
        $html = str_replace('<p></p>', '', $html);
        
        return $html;
    }

    /**
     * 🗑️ Template cache'ini temizle
     *
     * @param string|null $templateName Specific template or all
     */
    public function clearCache(?string $templateName = null): void
    {
        if ($templateName) {
            $cacheKey = self::CACHE_PREFIX . ":{$templateName}";
            Cache::forget($cacheKey);
        } else {
            // Clear all template caches
            Cache::flush(); // Bu production'da daha selective olmalı
        }
    }
}