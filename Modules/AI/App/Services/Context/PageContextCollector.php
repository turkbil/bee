<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Context;

use Illuminate\Support\Facades\Log;
use Modules\Page\app\Models\Page;
use Modules\Portfolio\app\Models\Portfolio;
use Modules\Announcement\app\Models\Announcement;

/**
 * Page Context Collector
 * Sayfa/içerik bilgilerini toplar ve context oluşturur
 */
class PageContextCollector extends ContextCollector
{
    public function __construct()
    {
        parent::__construct('page');
        // Page context kısa cache'lenir (10 dakika)
        $this->cacheTtl = 600;
    }

    /**
     * Page context'ini topla
     */
    protected function collectContext(array $options): array
    {
        $context = [
            'type' => 'no_context',
            'has_page' => false,
            'context_text' => '📄 NO PAGE: Genel içerik modu.',
            'priority' => 4
        ];

        // Page ID veya URL'den sayfa bilgisi al
        $pageData = $this->getPageData($options);
        
        if (!$pageData) {
            return $context;
        }

        $context = [
            'type' => 'page_context',
            'has_page' => true,
            'page_type' => $pageData['type'],
            'page_id' => $pageData['id'],
            'priority' => 2 // High priority for page context
        ];

        // Sayfa detaylarını ekle
        $context['page_details'] = $pageData['details'];
        
        // Content analysis
        $context['content_analysis'] = $this->analyzeContent($pageData);
        
        // SEO context
        $context['seo_context'] = $this->buildSeoContext($pageData);
        
        // AI için context metni oluştur
        $context['context_text'] = $this->buildContextText($context, $pageData);

        Log::debug('Page context collected', [
            'page_type' => $pageData['type'],
            'page_id' => $pageData['id'],
            'title' => $pageData['details']['title'] ?? 'Unknown',
            'context_size' => strlen($context['context_text'])
        ]);

        return $context;
    }

    /**
     * Sayfa verilerini getir (Page, Portfolio, Announcement)
     */
    private function getPageData(array $options): ?array
    {
        // Page ID'den direkt getir
        if (isset($options['page_id']) && isset($options['page_type'])) {
            return $this->getPageById($options['page_type'], $options['page_id']);
        }

        // URL'den sayfa bul
        if (isset($options['url']) || isset($options['slug'])) {
            $identifier = $options['url'] ?? $options['slug'];
            return $this->findPageByUrl($identifier);
        }

        // Request'ten otomatik algıla
        return $this->detectCurrentPage();
    }

    /**
     * ID ile sayfa getir
     */
    private function getPageById(string $type, int $id): ?array
    {
        $page = null;
        
        switch ($type) {
            case 'page':
                $page = Page::find($id);
                break;
                
            case 'portfolio':
                $page = Portfolio::find($id);
                break;
                
            case 'announcement':
                $page = Announcement::find($id);
                break;
        }

        if (!$page) {
            return null;
        }

        return [
            'type' => $type,
            'id' => $id,
            'details' => $this->extractPageDetails($page, $type)
        ];
    }

    /**
     * URL/slug ile sayfa bul
     */
    private function findPageByUrl(string $identifier): ?array
    {
        // Page'lerde ara
        if ($page = Page::whereJsonContains('slug', $identifier)->first()) {
            return [
                'type' => 'page',
                'id' => $page->page_id,
                'details' => $this->extractPageDetails($page, 'page')
            ];
        }

        // Portfolio'da ara
        if ($page = Portfolio::whereJsonContains('slug', $identifier)->first()) {
            return [
                'type' => 'portfolio',
                'id' => $page->id,
                'details' => $this->extractPageDetails($page, 'portfolio')
            ];
        }

        // Announcement'ta ara
        if ($page = Announcement::whereJsonContains('slug', $identifier)->first()) {
            return [
                'type' => 'announcement',
                'id' => $page->id,
                'details' => $this->extractPageDetails($page, 'announcement')
            ];
        }

        return null;
    }

    /**
     * Mevcut request'ten sayfa algıla
     */
    private function detectCurrentPage(): ?array
    {
        $currentPath = request()->path();
        
        // Admin paneli değilse current page'i algılamaya çalış
        if (!str_starts_with($currentPath, 'admin')) {
            return $this->findPageByUrl($currentPath);
        }

        return null;
    }

    /**
     * Sayfa detaylarını çıkar
     */
    private function extractPageDetails($page, string $type): array
    {
        $details = [
            'type' => $type,
            'id' => $page->id ?? $page->page_id ?? null
        ];

        // Multi-language destekli title ve content
        if (method_exists($page, 'getTranslated')) {
            $details['title'] = $page->getTranslated('title') ?? 'Untitled';
            $details['body'] = $page->getTranslated('body') ?? '';
            $details['slug'] = $page->getTranslated('slug') ?? '';
        } else {
            // Fallback for non-multi-language
            $details['title'] = $page->title ?? 'Untitled';
            $details['body'] = $page->body ?? $page->content ?? '';
            $details['slug'] = $page->slug ?? '';
        }

        // Sayfa durumu
        $details['is_active'] = $page->is_active ?? true;
        $details['created_at'] = $page->created_at;
        $details['updated_at'] = $page->updated_at;

        // Type-specific bilgiler
        switch ($type) {
            case 'page':
                $details['is_homepage'] = $page->is_homepage ?? false;
                break;
                
            case 'portfolio':
                $details['category_id'] = $page->category_id ?? null;
                $details['featured_image'] = $page->featured_image ?? null;
                break;
                
            case 'announcement':
                $details['published_at'] = $page->published_at ?? null;
                break;
        }

        return $details;
    }

    /**
     * İçerik analizi yap
     */
    private function analyzeContent(array $pageData): array
    {
        $content = $pageData['details']['body'] ?? '';
        $title = $pageData['details']['title'] ?? '';
        
        return [
            'word_count' => str_word_count(strip_tags($content)),
            'char_count' => strlen(strip_tags($content)),
            'has_images' => str_contains($content, '<img'),
            'has_links' => str_contains($content, '<a '),
            'paragraph_count' => substr_count($content, '<p>'),
            'title_length' => strlen($title),
            'content_type' => $this->detectContentType($content),
            'reading_time' => max(1, ceil(str_word_count(strip_tags($content)) / 200)) // 200 wpm average
        ];
    }

    /**
     * İçerik tipini tespit et
     */
    private function detectContentType(string $content): string
    {
        $content_lower = strtolower($content);
        
        if (str_contains($content_lower, 'hizmet') || str_contains($content_lower, 'service')) {
            return 'service';
        }
        
        if (str_contains($content_lower, 'hakkımızda') || str_contains($content_lower, 'about')) {
            return 'about';
        }
        
        if (str_contains($content_lower, 'iletişim') || str_contains($content_lower, 'contact')) {
            return 'contact';
        }
        
        if (str_contains($content_lower, 'blog') || str_contains($content_lower, 'makale')) {
            return 'blog';
        }
        
        return 'general';
    }

    /**
     * SEO context oluştur
     */
    private function buildSeoContext(array $pageData): array
    {
        $details = $pageData['details'];
        
        return [
            'title' => $details['title'] ?? '',
            'slug' => $details['slug'] ?? '',
            'meta_description' => $this->generateMetaDescription($details['body'] ?? ''),
            'keywords' => $this->extractKeywords($details['title'] ?? '', $details['body'] ?? ''),
            'url_structure' => $this->analyzeUrlStructure($details['slug'] ?? '')
        ];
    }

    /**
     * Meta description oluştur
     */
    private function generateMetaDescription(string $content): string
    {
        $cleanContent = strip_tags($content);
        $sentences = preg_split('/[.!?]+/', $cleanContent, -1, PREG_SPLIT_NO_EMPTY);
        
        if (empty($sentences)) {
            return '';
        }
        
        $description = '';
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($description . ' ' . $sentence) <= 155) {
                $description .= ($description ? ' ' : '') . $sentence . '.';
            } else {
                break;
            }
        }
        
        return $description;
    }

    /**
     * Anahtar kelimeleri çıkar
     */
    private function extractKeywords(string $title, string $content): array
    {
        $text = $title . ' ' . strip_tags($content);
        $words = str_word_count($text, 1, 'çğıöşüâîûÇĞIÖŞÜÂÎÛ');
        
        // Stop words (Turkish + English)
        $stopWords = ['ve', 'ile', 'bir', 'bu', 'da', 'de', 'en', 'and', 'the', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'as'];
        
        $filteredWords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array(strtolower($word), $stopWords);
        });
        
        $wordCounts = array_count_values(array_map('strtolower', $filteredWords));
        arsort($wordCounts);
        
        return array_slice(array_keys($wordCounts), 0, 10);
    }

    /**
     * URL yapısını analiz et
     */
    private function analyzeUrlStructure(string $slug): array
    {
        return [
            'slug' => $slug,
            'length' => strlen($slug),
            'word_count' => substr_count($slug, '-') + 1,
            'is_seo_friendly' => $this->isSeoFriendlySlug($slug)
        ];
    }

    /**
     * SEO dostu slug kontrolü
     */
    private function isSeoFriendlySlug(string $slug): bool
    {
        return !empty($slug) && 
               strlen($slug) <= 75 && 
               preg_match('/^[a-z0-9\-]+$/', $slug) &&
               !str_starts_with($slug, '-') &&
               !str_ends_with($slug, '-');
    }

    /**
     * AI için context metni oluştur
     */
    private function buildContextText(array $context, array $pageData): string
    {
        if (!$context['has_page']) {
            return '📄 NO PAGE: Genel içerik modu - belirli sayfa context yok.';
        }

        $details = $pageData['details'];
        $analysis = $context['content_analysis'];
        
        $parts = [];
        
        // Sayfa bilgisi
        $parts[] = "📄 PAGE CONTEXT: '{$details['title']}' sayfası için AI assistant.";
        $parts[] = "🏷️ PAGE TYPE: " . ucfirst($pageData['type']);
        
        // İçerik bilgisi
        if ($analysis['word_count'] > 0) {
            $parts[] = "📊 CONTENT INFO: {$analysis['word_count']} kelime, {$analysis['reading_time']} dakika okuma süresi";
        }
        
        // İçerik tipi
        if ($analysis['content_type'] !== 'general') {
            $parts[] = "🎯 CONTENT TYPE: " . ucfirst($analysis['content_type']);
        }
        
        // SEO bilgisi
        if (!empty($context['seo_context']['keywords'])) {
            $keywords = array_slice($context['seo_context']['keywords'], 0, 5);
            $parts[] = "🔍 KEYWORDS: " . implode(', ', $keywords);
        }
        
        // Sayfa durumu
        if (!$details['is_active']) {
            $parts[] = "⚠️ STATUS: Sayfa aktif değil";
        }
        
        // Özel talimatlar
        $parts[] = "🎯 PAGE INSTRUCTION: Bu sayfa bağlamında ilgili ve tutarlı içerik üret.";
        $parts[] = "📝 CONTENT STYLE: Sayfanın tonuna ve amacına uygun yaklaşım kullan.";
        
        // Type-specific talimatlar
        switch ($pageData['type']) {
            case 'page':
                if ($details['is_homepage'] ?? false) {
                    $parts[] = "🏠 HOMEPAGE: Ana sayfa context - genel şirket tanıtımı odaklı ol.";
                }
                break;
                
            case 'portfolio':
                $parts[] = "💼 PORTFOLIO: Proje/hizmet odaklı profesyonel yaklaşım kullan.";
                break;
                
            case 'announcement':
                $parts[] = "📢 ANNOUNCEMENT: Duyuru formatında güncel ve bilgilendirici ol.";
                break;
        }

        return implode("\n", $parts);
    }

    /**
     * Context priority hesaplama (override)
     */
    protected function calculatePriority(array $context): int
    {
        if (!$context['has_page']) {
            return 4; // Medium-low priority
        }

        $priority = 2; // High priority for page context

        // Homepage gets highest priority
        if (isset($context['page_details']['is_homepage']) && $context['page_details']['is_homepage']) {
            $priority = 1;
        }

        // Inactive pages get lower priority
        if (isset($context['page_details']['is_active']) && !$context['page_details']['is_active']) {
            $priority = 3;
        }

        return $priority;
    }

    /**
     * Context validation (override)
     */
    protected function validateContext(array $context): bool
    {
        if (!$context['has_page']) {
            return true; // No page context is valid
        }

        return isset($context['page_type']) && 
               isset($context['page_id']) &&
               isset($context['page_details']);
    }

    /**
     * Sayfa güncellendiğinde cache temizle
     */
    public function handlePageUpdate(string $type, int $pageId): void
    {
        $this->clearPageCache($type, $pageId);
        
        Log::info('Page context cache cleared due to page update', [
            'page_type' => $type,
            'page_id' => $pageId
        ]);
    }

    /**
     * Belirli sayfa için cache temizle
     */
    private function clearPageCache(string $type, int $pageId): void
    {
        try {
            $tenantId = tenant('id') ?? 'default';
            $pattern = "context_page_{$tenantId}_*_{$type}_{$pageId}_*";
            $keys = \Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                \Cache::getRedis()->del($keys);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear page context cache pattern', [
                'page_type' => $type,
                'page_id' => $pageId,
                'error' => $e->getMessage()
            ]);
        }
    }
}