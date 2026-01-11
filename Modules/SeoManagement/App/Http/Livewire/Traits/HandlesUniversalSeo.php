<?php

declare(strict_types=1);

namespace Modules\SeoManagement\app\Http\Livewire\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HandlesUniversalSeo
{
    public array $availableLanguages = [];
    public string $currentLanguage = 'tr';
    public array $seoDataCache = [];
    public array $allLanguagesSeoData = [];

    protected function initializeUniversalSeoState(
        ?array $languages = null,
        ?string $activeLanguage = null,
        ?array $cache = null
    ): void {
        $this->availableLanguages = $this->resolveAvailableLanguages($languages);
        $this->currentLanguage = $this->resolveActiveLanguage($activeLanguage, $this->availableLanguages);
        $this->seoDataCache = $this->normalizeSeoCache($cache ?? $this->seoDataCache, $this->availableLanguages);
        $this->allLanguagesSeoData = $this->seoDataCache;
    }

    protected function resolveAvailableLanguages(?array $languages = null): array
    {
        if (!empty($languages)) {
            return $this->sanitizeLanguageList($languages);
        }

        $detected = [];

        try {
            if (class_exists('Modules\\LanguageManagement\\app\\Models\\TenantLanguage')) {
                $detected = \Modules\LanguageManagement\app\Models\TenantLanguage::query()
                    ->where('is_active', true)
                    ->pluck('code')
                    ->filter()
                    ->values()
                    ->all();
            }
        } catch (\Throwable $exception) {
        }

        if (empty($detected)) {
            $fallback = config('seomanagement.universal_seo.multilingual.supported_languages', []);
            $detected = $this->sanitizeLanguageList($fallback);
        }

        if (empty($detected)) {
            $detected = [$this->defaultLanguage()];
        }

        return array_values(array_unique($detected));
    }

    protected function resolveActiveLanguage(?string $preferred, array $languages): string
    {
        $preferredOrder = array_filter([
            $preferred,
            $this->currentLanguage ?? null,
            session('page_manage_language'),
            session('site_default_language'),
            config('seomanagement.universal_seo.multilingual.default_language'),
            config('app.locale'),
            $languages[0] ?? null,
            $this->defaultLanguage(),
        ]);

        foreach ($preferredOrder as $candidate) {
            if ($candidate && in_array($candidate, $languages, true)) {
                return $candidate;
            }
        }

        return $languages[0] ?? $this->defaultLanguage();
    }

    protected function normalizeSeoCache(array $cache, array $languages): array
    {
        $normalized = [];

        foreach ($languages as $lang) {
            $existing = $cache[$lang] ?? [];
            $normalized[$lang] = array_merge($this->languageSeoDefaults(), $existing);
        }

        return $normalized;
    }

    protected function defaultLanguage(): string
    {
        return session('site_default_language', config('app.locale', 'tr'));
    }

    protected function languageSeoDefaults(): array
    {
        return [
            'seo_title' => '',
            'seo_description' => '',
            'seo_keywords' => '',
            'focus_keywords' => '',
            'og_title' => '',
            'og_description' => '',
            'og_image' => '',
            'content_type' => 'website',
            'content_type_custom' => '',
            'priority_score' => 5,
            'author_name' => '',
            'author_url' => '',
            'og_custom_enabled' => false,
        ];
    }

    protected function sanitizeLanguageList(array $languages): array
    {
        return array_values(array_filter(array_map(
            static fn ($code) => is_string($code) ? strtolower(trim($code)) : null,
            $languages
        )));
    }
}
