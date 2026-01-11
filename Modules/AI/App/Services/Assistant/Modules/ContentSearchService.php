<?php

namespace Modules\AI\App\Services\Assistant\Modules;

use Modules\AI\App\Contracts\ModuleSearchInterface;
use Illuminate\Support\Facades\Log;

/**
 * Content Search Service
 *
 * Blog/Haber/İçerik için AI arama servisi.
 *
 * @package Modules\AI\App\Services\Assistant\Modules
 */
class ContentSearchService implements ModuleSearchInterface
{
    protected string $locale;

    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

    /**
     * @inheritDoc
     */
    public function search(string $query, array $filters = [], int $limit = 50): array
    {
        try {
            // Blog modülü varsa ara
            if (class_exists(\Modules\Blog\App\Models\Blog::class)) {
                $results = \Modules\Blog\App\Models\Blog::where('is_active', true)
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('content', 'LIKE', "%{$query}%")
                          ->orWhere('excerpt', 'LIKE', "%{$query}%");
                    })
                    ->orderBy('published_at', 'desc')
                    ->limit($limit)
                    ->get();

                return [
                    'success' => true,
                    'items' => $results->toArray(),
                    'total' => $results->count(),
                    'module_type' => 'content',
                ];
            }

            return [
                'success' => false,
                'items' => [],
                'total' => 0,
                'module_type' => 'content',
                'message' => 'Blog modülü bulunamadı',
            ];

        } catch (\Exception $e) {
            Log::error('ContentSearchService: Search failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'items' => [], 'total' => 0, 'module_type' => 'content'];
        }
    }

    /**
     * @inheritDoc
     */
    public function buildContextForAI(array $results): string
    {
        if (empty($results['items'])) {
            return '';
        }

        $context = "## 📝 İlgili Yazılar\n\n";

        foreach ($results['items'] as $item) {
            $title = $this->translate($item['title'] ?? '');
            $slug = $this->translate($item['slug'] ?? '');
            $excerpt = $this->translate($item['excerpt'] ?? '');

            $context .= "### {$title}\n";
            if ($excerpt) {
                $context .= "{$excerpt}\n";
            }
            $context .= "[Yazıyı Oku](/blog/{$slug})\n\n";
        }

        return $context;
    }

    /**
     * @inheritDoc
     */
    public function getQuickActions(): array
    {
        return [
            [
                'label' => 'Son Yazılar',
                'message' => 'En son yayınlanan yazılar nelerdir?',
                'icon' => 'fas fa-newspaper',
                'color' => 'blue',
            ],
            [
                'label' => 'Kategoriler',
                'message' => 'Hangi kategorilerde yazılarınız var?',
                'icon' => 'fas fa-folder',
                'color' => 'green',
            ],
            [
                'label' => 'Popüler',
                'message' => 'En çok okunan yazılar hangileri?',
                'icon' => 'fas fa-fire',
                'color' => 'orange',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function detectFilters(string $message): ?array
    {
        // Kategori tespiti yapılabilir
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPromptRules(): string
    {
        return "
## İÇERİK ASSISTANT KURALLARI

1. **Yazı Önerme:**
   - Context'teki yazıları öner
   - Kısa özet ver
   - Link ekle

2. **Ton:**
   - Bilgilendirici ol
   - Okumaya teşvik et
";
    }

    /**
     * @inheritDoc
     */
    public function getModuleType(): string
    {
        return 'content';
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string
    {
        return 'İçerik Asistanı';
    }

    protected function translate($data): string
    {
        if (is_string($data)) return $data;
        if (is_array($data)) return $data[$this->locale] ?? $data['tr'] ?? reset($data) ?? '';
        return '';
    }
}
