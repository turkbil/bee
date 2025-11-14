<?php

namespace App\Traits;

/**
 * Universal Schema Trait
 *
 * Tüm modüller (Blog, Shop, Page, Portfolio) için ortak schema sistemleri:
 * - FAQPage Schema
 * - BreadcrumbList Schema
 * - HowTo Schema
 *
 * Kullanım:
 * - Model'de: use HasUniversalSchemas;
 * - Database'de: faq_data, howto_data (json) field'ları olmalı
 */
trait HasUniversalSchemas
{
    /**
     * Generate FAQPage Schema from faq_data field
     *
     * @return array|null
     */
    public function getFaqSchema(): ?array
    {
        if (empty($this->faq_data) || !is_array($this->faq_data)) {
            return null;
        }

        $locale = app()->getLocale();
        $resolveLocalized = function ($data) use ($locale) {
            if (!is_array($data)) {
                return $data;
            }
            $defaultLocale = get_tenant_default_locale();
            return $data[$locale] ?? ($data[$defaultLocale] ?? ($data['en'] ?? reset($data)));
        };

        $faqEntries = collect($this->faq_data)
            ->map(fn($faq) => is_array($faq) ? [
                'question' => $resolveLocalized($faq['question'] ?? null),
                'answer' => $resolveLocalized($faq['answer'] ?? null),
            ] : null)
            ->filter(fn($faq) => $faq && $faq['question'] && $faq['answer'])
            ->values();

        if ($faqEntries->isEmpty()) {
            return null;
        }

        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];

        foreach ($faqEntries as $faq) {
            $faqSchema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        return $faqSchema;
    }

    /**
     * Generate HowTo Schema from howto_data field
     *
     * @return array|null
     */
    public function getHowToSchema(): ?array
    {
        if (empty($this->howto_data) || !is_array($this->howto_data)) {
            return null;
        }

        $locale = app()->getLocale();
        $resolveLocalized = function ($data) use ($locale) {
            if (!is_array($data)) {
                return $data;
            }
            $defaultLocale = get_tenant_default_locale();
            return $data[$locale] ?? ($data[$defaultLocale] ?? ($data['en'] ?? reset($data)));
        };

        // HowTo başlık ve açıklama
        $name = $resolveLocalized($this->howto_data['name'] ?? null);
        $description = $resolveLocalized($this->howto_data['description'] ?? null);

        if (!$name) {
            return null;
        }

        // Adımlar
        $steps = collect($this->howto_data['steps'] ?? [])
            ->map(fn($step, $index) => is_array($step) ? [
                'name' => $resolveLocalized($step['name'] ?? null),
                'text' => $resolveLocalized($step['text'] ?? null),
                'position' => $index + 1,
            ] : null)
            ->filter(fn($step) => $step && $step['name'])
            ->values();

        if ($steps->isEmpty()) {
            return null;
        }

        $howtoSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $name,
            'step' => [],
        ];

        if ($description) {
            $howtoSchema['description'] = $description;
        }

        foreach ($steps as $step) {
            $howtoSchema['step'][] = [
                '@type' => 'HowToStep',
                'position' => $step['position'],
                'name' => $step['name'],
                'text' => $step['text'] ?? $step['name'],
            ];
        }

        return $howtoSchema;
    }

    /**
     * Generate BreadcrumbList Schema
     *
     * Override this method in your model for custom breadcrumb logic
     *
     * @return array|null
     */
    public function getBreadcrumbSchema(): ?array
    {
        // Model'de override edilecek
        // Her modül kendi breadcrumb mantığını yazacak
        return null;
    }

    /**
     * Get all universal schemas (FAQ + HowTo + Breadcrumb)
     *
     * @return array
     */
    public function getUniversalSchemas(): array
    {
        $schemas = [];

        // FAQ Schema
        $faqSchema = $this->getFaqSchema();
        if ($faqSchema) {
            $schemas['faq'] = $faqSchema;
        }

        // HowTo Schema
        $howtoSchema = $this->getHowToSchema();
        if ($howtoSchema) {
            $schemas['howto'] = $howtoSchema;
        }

        // Breadcrumb Schema
        $breadcrumbSchema = $this->getBreadcrumbSchema();
        if ($breadcrumbSchema) {
            $schemas['breadcrumb'] = $breadcrumbSchema;
        }

        return $schemas;
    }

    /**
     * Render all schemas as JSON-LD script tags
     *
     * @return string
     */
    public function renderUniversalSchemas(): string
    {
        $schemas = $this->getUniversalSchemas();

        if (empty($schemas)) {
            return '';
        }

        $html = '';
        foreach ($schemas as $key => $schema) {
            $html .= '<script type="application/ld+json">' . PHP_EOL;
            $html .= json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $html .= PHP_EOL . '</script>' . PHP_EOL;
        }

        return $html;
    }
}
