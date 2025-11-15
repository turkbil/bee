<?php

namespace Modules\Blog\App\Services;

use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Models\BlogCategory;
use Modules\Blog\App\Services\TenantPrompts\TenantPromptLoader;
use Modules\AI\App\Services\OpenAIService;
use App\Services\AI\TenantBlogPromptEnhancer;
use Illuminate\Support\Facades\Log;

/**
 * Blog AI Draft Generator Service
 *
 * AI ile blog taslakları oluşturur
 * Duplicate check yapar, credit yönetir
 */
class BlogAIDraftGenerator
{
    protected TenantPromptLoader $promptLoader;
    protected OpenAIService $openaiService;
    protected TenantBlogPromptEnhancer $tenantEnhancer;

    public function __construct(
        TenantPromptLoader $promptLoader,
        TenantBlogPromptEnhancer $tenantEnhancer
    ) {
        $this->promptLoader = $promptLoader;
        $this->tenantEnhancer = $tenantEnhancer;
        // Mevcut AI sistemi - AIProvider modelinden API key çeker
        $this->openaiService = new OpenAIService();
    }

    /**
     * Blog taslakları oluştur
     *
     * @param int $count Kaç taslak oluşturulacak (varsayılan: 10)
     * @return array Oluşturulan taslaklar
     */
    public function generateDrafts(int $count = 10): array
    {
        // Credit kontrolü - araştırma toplam 1.0 kredi
        if (!ai_can_use_credits(1.0)) {
            throw new \Exception('Yetersiz AI kredisi. Lütfen kredi satın alın.');
        }

        // Duplicate check: Mevcut blog başlıkları + draft'lar
        $existingTitles = $this->getExistingTitles();
        $existingDrafts = $this->getExistingDrafts();

        // Kategorileri çek
        $categories = $this->getCategories();

        // Tenant context
        $context = $this->promptLoader->getTenantContext();

        // Tenant-specific enhancement al (varsa)
        $tenantEnhancement = $this->tenantEnhancer->getEnhancement();

        // AI Prompt
        $prompt = $this->promptLoader->getDraftPrompt();

        // Tenant-specific context ekle
        $tenantContext = '';
        if (!empty($tenantEnhancement)) {
            $tenantContext = $this->tenantEnhancer->buildPromptContext($tenantEnhancement);
        }

        // System message
        $systemMessage = $prompt . "\n\n" . $tenantContext . "\n\n" . $this->buildContextString($context, $categories, $existingTitles, $existingDrafts, $count);

        try {
            // OpenAI API call (mevcut AI sistemini kullan)
            // max_tokens dinamik ayarla: Her taslak ~200 token → count * 250
            // Minimum 1000, Maximum 16000 (GPT-4 output limit)
            $maxTokens = min(16000, max(1000, $count * 250));

            $userPrompt = "Lütfen {$count} adet blog taslağı üret. JSON array formatında döndür.";
            $response = $this->openaiService->ask($userPrompt, false, [
                'custom_prompt' => $systemMessage,
                'temperature' => 0.7,
                'max_tokens' => $maxTokens,
            ]);

            // ask() metodu direkt string döndürür
            $content = $response;

            // DEBUG: OpenAI response'u dosyaya yaz
            file_put_contents('/tmp/openai-response.txt', $content);
            Log::info('🤖 OpenAI Response saved to /tmp/openai-response.txt', [
                'length' => strlen($content),
                'sample' => substr($content, 0, 500),
            ]);

            // JSON parse
            $drafts = $this->parseAIResponse($content);

            // Database'e kaydet
            $savedDrafts = $this->saveDrafts($drafts);

            // Credit düş - araştırma toplam 1.0 kredi
            ai_use_credits(1.0, null, [
                'usage_type' => 'blog_draft_generation',
                'draft_count' => count($savedDrafts),
                'tenant_id' => tenant('id'),
            ]);

            Log::info('Blog AI Drafts Generated', [
                'count' => count($savedDrafts),
                'tenant_id' => tenant('id'),
            ]);

            return $savedDrafts;

        } catch (\Exception $e) {
            Log::error('Blog AI Draft Generation Failed', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id'),
            ]);

            throw $e;
        }
    }

    /**
     * Mevcut blog başlıklarını çek (duplicate check için)
     */
    protected function getExistingTitles(): array
    {
        return Blog::query()
            ->get()
            ->pluck('title')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Mevcut draft'ları çek (duplicate check için)
     */
    protected function getExistingDrafts(): array
    {
        return BlogAIDraft::query()
            ->pluck('topic_keyword')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Blog kategorilerini çek
     */
    protected function getCategories(): array
    {
        return BlogCategory::query()
            ->where('is_active', true)
            ->get()
            ->map(function ($cat) {
                $title = is_array($cat->title) ? ($cat->title['tr'] ?? $cat->title['en'] ?? '') : $cat->title;
                return [
                    'id' => $cat->id,
                    'title' => $title,
                    'slug' => $cat->slug,
                ];
            })
            ->toArray();
    }

    /**
     * Context string oluştur (AI için)
     */
    protected function buildContextString(array $context, array $categories, array $existingTitles, array $existingDrafts, int $count): string
    {
        $contextString = "\n\n**CONTEXT BİLGİLERİ:**\n";

        // Firma Bilgileri
        if (!empty($context['company_info'])) {
            $contextString .= "\n**FİRMA BİLGİLERİ:**\n";
            $contextString .= "- Firma Adı: " . ($context['company_info']['name'] ?? 'N/A') . "\n";
            $contextString .= "- Site Başlığı: " . ($context['company_info']['title'] ?? 'N/A') . "\n";
            $contextString .= "- Slogan: " . ($context['company_info']['slogan'] ?? 'N/A') . "\n";
            $contextString .= "- Website: " . ($context['company_info']['website'] ?? 'N/A') . "\n";
        }

        // İletişim Bilgileri
        if (!empty($context['contact_info'])) {
            $contextString .= "\n**İLETİŞİM BİLGİLERİ:**\n";
            $contextString .= "- Email: " . ($context['contact_info']['email'] ?? 'N/A') . "\n";
            $contextString .= "- Telefon: " . ($context['contact_info']['phone'] ?? 'N/A') . "\n";
            $contextString .= "- Adres: " . ($context['contact_info']['address'] ?? 'N/A') . "\n";
        }

        $contextString .= "\n**SEKTÖR & HEDEF:**\n";
        $contextString .= "- Sektör: " . ($context['industry'] ?? $context['sector'] ?? 'Genel') . "\n";
        $contextString .= "- Odak: " . ($context['focus'] ?? 'Genel içerik') . "\n";
        $contextString .= "- Hedef Kitle: " . ($context['target_audience'] ?? 'Genel okuyucu') . "\n";

        if (!empty($context['keywords'])) {
            $contextString .= "- Anahtar Kelimeler: " . implode(', ', $context['keywords']) . "\n";
        }

        $contextString .= "\n**MEVCUT KATEGORİLER:**\n";
        foreach ($categories as $cat) {
            $contextString .= "- ID: {$cat['id']} | {$cat['title']}\n";
        }

        if (!empty($existingTitles)) {
            $contextString .= "\n**MEVCUT BLOG BAŞLIKLARI (BUNLARI TEKRARLAMA):**\n";
            $contextString .= implode("\n", array_slice($existingTitles, 0, 50)); // İlk 50 tanesi
        }

        if (!empty($existingDrafts)) {
            $contextString .= "\n\n**MEVCUT DRAFT KONULARI (BUNLARI TEKRARLAMA):**\n";
            $contextString .= implode("\n", array_slice($existingDrafts, 0, 50)); // İlk 50 tanesi
        }

        $contextString .= "\n\n**İSTENEN TASLAK SAYISI:** {$count}";

        return $contextString;
    }

    /**
     * AI response'u parse et
     */
    protected function parseAIResponse(string $content): array
    {
        // OpenAI response'u log'la (debug için)
        Log::info('🤖 OpenAI Raw Response', [
            'content_length' => strlen($content),
            'first_200_chars' => substr($content, 0, 200),
            'last_200_chars' => substr($content, -200),
        ]);

        // JSON extract (bazen markdown code block içinde geliyor)
        // Pattern: ```json ... ``` veya ``` ... ```
        // Greedy match yerine lazy match (.*?) kullan
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
            Log::info('✅ JSON code block found and extracted (```json)');
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
            Log::info('✅ Code block found and extracted (```)');
        }

        // Ekstra temizlik: JSON başlangıcından önceki text'i kaldır
        // OpenAI bazen "İşte 100 taslak:\n\n```json..." şeklinde döndürür
        if (preg_match('/^\s*\[/s', $content) === 0) {
            // JSON "[" ile başlamıyorsa, "[" karakterine kadar olan kısmı at
            if (preg_match('/(\[.*\])\s*$/s', $content, $matches)) {
                $content = $matches[1];
                Log::info('✅ Removed text before JSON array');
            }
        }

        $decoded = json_decode(trim($content), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('❌ JSON Parse Failed', [
                'error' => json_last_error_msg(),
                'content_sample' => substr(trim($content), 0, 500),
            ]);
            throw new \Exception('AI response JSON parse error: ' . json_last_error_msg());
        }

        if (!is_array($decoded)) {
            Log::error('❌ Decoded value is not an array', [
                'type' => gettype($decoded),
                'value' => $decoded,
                'content_length' => strlen($content),
                'content_sample' => substr($content, 0, 1000),
            ]);
            throw new \Exception('AI response is not an array. Type: ' . gettype($decoded) . ', Value: ' . json_encode($decoded));
        }

        Log::info('✅ JSON parsed successfully', [
            'draft_count' => count($decoded),
        ]);

        // Format converter: OpenAI farklı format döndürebilir
        $normalized = $this->normalizeAIResponse($decoded);

        return $normalized;
    }

    /**
     * AI response formatını normalize et
     *
     * OpenAI bazen farklı formatlar döndürebilir:
     * Format 1: [{"topic_keyword": "...", "meta_description": "...", ...}]
     * Format 2: [{"SEO Meta Bilgileri": {...}, "Blog Anahattı": {...}}]
     *
     * Her ikisini de bizim formatımıza çevir
     */
    protected function normalizeAIResponse(array $decoded): array
    {
        $normalized = [];

        foreach ($decoded as $item) {
            // Format 1: Direkt bizim formatımız (topic_keyword var)
            if (isset($item['topic_keyword'])) {
                $normalized[] = $item;
                continue;
            }

            // Format 2: Nested format (SEO Meta Bilgileri, Blog Anahattı vb.)
            // OpenAI bazen "1. SEO Meta Bilgileri" gibi numaralandırabilir
            $seoKey = $this->findKeyContaining($item, 'SEO Meta');
            $outlineKey = $this->findKeyContaining($item, 'Blog Anahat');

            if ($seoKey || $outlineKey) {
                $seoMeta = $seoKey ? ($item[$seoKey] ?? []) : [];
                $blogOutline = $outlineKey ? ($item[$outlineKey] ?? []) : [];

                $normalized[] = [
                    'topic_keyword' => $seoMeta['Title Tag'] ?? $blogOutline['H1'] ?? '',
                    'meta_description' => $seoMeta['Meta Description'] ?? '',
                    'seo_keywords' => array_merge(
                        isset($seoMeta['Focus Keyword']) ? [$seoMeta['Focus Keyword']] : [],
                        $seoMeta['Secondary Keywords'] ?? []
                    ),
                    'category_suggestions' => [], // Context'ten çıkarsayacağız
                    'outline' => $blogOutline,
                ];

                Log::info('✅ Converted nested format to standard format', [
                    'topic_keyword' => $normalized[count($normalized) - 1]['topic_keyword'],
                ]);

                continue;
            }

            // Format 3: Bilinmeyen format - log ve skip
            Log::warning('⚠️ Unknown AI response format, skipping item', [
                'item' => $item,
            ]);
        }

        Log::info('✅ Normalized AI response', [
            'original_count' => count($decoded),
            'normalized_count' => count($normalized),
        ]);

        return $normalized;
    }

    /**
     * Array içinde belirli string içeren key bul
     *
     * Örn: "SEO Meta" arar ve "1. SEO Meta Bilgileri" bulur
     */
    protected function findKeyContaining(array $array, string $needle): ?string
    {
        foreach (array_keys($array) as $key) {
            if (stripos($key, $needle) !== false) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Taslakları database'e kaydet
     */
    protected function saveDrafts(array $drafts): array
    {
        $saved = [];

        foreach ($drafts as $draft) {
            try {
                $saved[] = BlogAIDraft::create([
                    'topic_keyword' => $draft['topic_keyword'] ?? '',
                    'category_suggestions' => $draft['category_suggestions'] ?? [],
                    'seo_keywords' => $draft['seo_keywords'] ?? [],
                    'outline' => $draft['outline'] ?? [],
                    'meta_description' => $draft['meta_description'] ?? null,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to save draft', [
                    'draft' => $draft,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $saved;
    }
}
