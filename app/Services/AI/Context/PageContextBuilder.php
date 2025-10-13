<?php

namespace App\Services\AI\Context;

use Modules\Page\App\Models\Page;

/**
 * Page Context Builder
 *
 * Page modülünden AI için context oluşturur.
 * Şirket hakkında, hizmetler, iletişim gibi sayfa bilgilerini hazırlar.
 */
class PageContextBuilder
{
    protected string $locale;

    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

    /**
     * Belirli bir sayfa için context oluştur
     */
    public function buildPageContext(string $slug): array
    {
        $page = Page::where('slug->' . $this->locale, $slug)
            ->orWhere('slug->tr', $slug)
            ->first();

        if (!$page) {
            return [];
        }

        return [
            'page_type' => 'page',
            'current_page' => $this->formatPage($page),
        ];
    }

    /**
     * Genel sayfa bilgileri (About, Services, Contact + ALL active pages)
     */
    public function buildGeneralPageContext(): array
    {
        $context = ['page_type' => 'general'];

        // Hakkımızda
        $about = $this->findPageBySlug(['hakkimizda', 'about', 'about-us']);
        if ($about) {
            $context['about'] = $this->formatPage($about);
        }

        // Hizmetler
        $services = $this->findPageBySlug(['hizmetlerimiz', 'services', 'our-services']);
        if ($services) {
            $context['services'] = $this->formatPage($services);
        }

        // İletişim
        $contact = $this->findPageBySlug(['iletisim', 'contact', 'contact-us']);
        if ($contact) {
            $context['contact'] = $this->formatPage($contact);
        }

        // TÜM AKTİF SAYFALAR (AI'nin tüm sayfa bilgisine erişimi için)
        $allPages = Page::where('is_active', true)
            ->select(['page_id', 'title', 'slug', 'body'])
            ->get();

        $context['all_pages'] = $allPages->map(fn($p) => $this->formatPageSummary($p))->toArray();
        $context['total_pages'] = $allPages->count();

        return $context;
    }

    /**
     * Sayfayı AI için formatla (detaylı)
     */
    protected function formatPage(Page $page): array
    {
        return [
            'id' => $page->page_id,
            'title' => $this->translate($page->title),
            'slug' => $this->translate($page->slug),
            'content' => $this->sanitize($this->translate($page->body), 1000),
            'summary' => $this->sanitize($this->translate($page->body), 200),
        ];
    }

    /**
     * Sayfa özeti formatla (hafif versiyon - listeleme için)
     */
    protected function formatPageSummary(Page $page): array
    {
        return [
            'id' => $page->page_id,
            'title' => $this->translate($page->title),
            'slug' => $this->translate($page->slug),
            'summary' => $this->sanitize($this->translate($page->body), 300),
        ];
    }

    /**
     * Slug'a göre sayfa bul
     */
    protected function findPageBySlug(array $slugs): ?Page
    {
        foreach ($slugs as $slug) {
            $page = Page::where('slug->' . $this->locale, $slug)
                ->orWhere('slug->tr', $slug)
                ->first();

            if ($page) {
                return $page;
            }
        }

        return null;
    }

    /**
     * JSON multi-language çeviri
     */
    protected function translate($data): string
    {
        if (is_string($data)) {
            return $data;
        }

        if (is_array($data)) {
            $defaultLocale = get_tenant_default_locale();
            return $data[$this->locale] ?? $data[$defaultLocale] ?? $data['en'] ?? reset($data) ?? '';
        }

        return '';
    }

    /**
     * HTML içeriği temizle
     */
    protected function sanitize(?string $content, int $limit = 0): string
    {
        if (empty($content)) {
            return '';
        }

        $cleaned = strip_tags($content);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned);

        if ($limit > 0 && mb_strlen($cleaned) > $limit) {
            $cleaned = mb_substr($cleaned, 0, $limit) . '...';
        }

        return $cleaned;
    }
}
