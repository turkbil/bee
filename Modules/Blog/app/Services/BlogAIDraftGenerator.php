<?php

namespace Modules\Blog\App\Services;

use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Models\BlogCategory;
use Modules\Blog\App\Services\TenantPrompts\TenantPromptLoader;
use OpenAI;
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
    protected $openai;

    public function __construct(TenantPromptLoader $promptLoader)
    {
        $this->promptLoader = $promptLoader;
        $this->openai = OpenAI::client(config('services.openai.api_key'));
    }

    /**
     * Blog taslakları oluştur
     *
     * @param int $count Kaç taslak oluşturulacak (varsayılan: 100)
     * @return array Oluşturulan taslaklar
     */
    public function generateDrafts(int $count = 100): array
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

        // AI Prompt
        $prompt = $this->promptLoader->getDraftPrompt();

        // System message
        $systemMessage = $prompt . "\n\n" . $this->buildContextString($context, $categories, $existingTitles, $existingDrafts, $count);

        try {
            // OpenAI API call
            $response = $this->openai->chat()->create([
                'model' => config('modules.blog.openai.model', 'gpt-4-turbo-preview'),
                'messages' => [
                    ['role' => 'system', 'content' => $systemMessage],
                    ['role' => 'user', 'content' => "Lütfen {$count} adet blog taslağı üret. JSON array formatında döndür."],
                ],
                'temperature' => config('modules.blog.openai.draft_temperature', 0.7),
                'max_tokens' => config('modules.blog.openai.draft_max_tokens', 3000),
            ]);

            $content = $response->choices[0]->message->content;

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
            ->where('status', 'active')
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
        $contextString .= "- Tenant: " . ($context['tenant_name'] ?? 'Default') . "\n";
        $contextString .= "- Sektör: " . ($context['sector'] ?? 'Genel') . "\n";
        $contextString .= "- Odak: " . ($context['focus'] ?? 'Genel içerik') . "\n";

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
        // JSON extract (bazen markdown code block içinde geliyor)
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        }

        $decoded = json_decode(trim($content), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('AI response JSON parse error: ' . json_last_error_msg());
        }

        if (!is_array($decoded)) {
            throw new \Exception('AI response is not an array');
        }

        return $decoded;
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
