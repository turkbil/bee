<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class RelatedContentService
{
    /**
     * Blog yazısı için ilgili içerikleri bul
     */
    public static function getRelatedBlogs(Model $blog, int $limit = 6): Collection
    {
        $relatedBlogs = collect();
        $blog->loadMissing('tags');

        // 1. Aynı kategorideki bloglar (en yüksek öncelik)
        if ($blog->blog_category_id) {
            $categoryBlogs = $blog->newQuery()
                ->where('blog_category_id', $blog->blog_category_id)
                ->where('blog_id', '!=', $blog->blog_id)
                ->where('is_active', true)
                ->where('status', 'published')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();

            $relatedBlogs = $relatedBlogs->merge($categoryBlogs);
        }

        // 2. Eğer yeterli blog yoksa, benzer etiketlere sahip bloglar
        if ($relatedBlogs->count() < $limit && $blog->tags->isNotEmpty()) {
            $tagSlugs = $blog->tags->pluck('slug')->filter()->values()->all();

            $tagBlogs = $blog->newQuery()
                ->with('tags')
                ->where('blog_id', '!=', $blog->blog_id)
                ->where('is_active', true)
                ->where('status', 'published')
                ->where('published_at', '<=', now())
                ->whereHas('tags', function (Builder $query) use ($tagSlugs) {
                    $query->whereIn('slug', $tagSlugs);
                })
                ->orderBy('published_at', 'desc')
                ->limit($limit - $relatedBlogs->count())
                ->get();

            $relatedBlogs = $relatedBlogs->merge($tagBlogs);
        }

        // 3. Eğer hala yeterli blog yoksa, başlık benzerliğine göre
        if ($relatedBlogs->count() < $limit) {
            $currentLocale = app()->getLocale();
            $currentTitle = $blog->getTranslated('title', $currentLocale);

            if ($currentTitle) {
                $titleWords = self::extractKeywords($currentTitle);

                if (!empty($titleWords)) {
                    $titleBlogs = $blog->newQuery()
                        ->where('blog_id', '!=', $blog->blog_id)
                        ->where('is_active', true)
                        ->where('status', 'published')
                        ->where('published_at', '<=', now())
                        ->where(function (Builder $query) use ($titleWords, $currentLocale) {
                            foreach ($titleWords as $word) {
                                $query->orWhere("title->{$currentLocale}", 'LIKE', "%{$word}%");
                            }
                        })
                        ->orderBy('published_at', 'desc')
                        ->limit($limit - $relatedBlogs->count())
                        ->get();

                    $relatedBlogs = $relatedBlogs->merge($titleBlogs);
                }
            }
        }

        // 4. Eğer hala yeterli blog yoksa, son yayınlanan bloglardan doldur
        if ($relatedBlogs->count() < $limit) {
            $recentBlogs = $blog->newQuery()
                ->where('blog_id', '!=', $blog->blog_id)
                ->where('is_active', true)
                ->where('status', 'published')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->limit($limit - $relatedBlogs->count())
                ->get();

            $relatedBlogs = $relatedBlogs->merge($recentBlogs);
        }

        // Dublicate'leri kaldır ve limit'e uy
        return $relatedBlogs
            ->unique('blog_id')
            ->take($limit)
            ->values();
    }

    /**
     * Genel kullanım için ilgili içerik bulma
     */
    public static function getRelatedContent(Model $model, int $limit = 6): Collection
    {
        $modelClass = get_class($model);

        return match ($modelClass) {
            \Modules\Blog\App\Models\Blog::class => self::getRelatedBlogs($model, $limit),
            \Modules\Portfolio\App\Models\Portfolio::class => self::getRelatedPortfolios($model, $limit),
            \Modules\Page\App\Models\Page::class => self::getRelatedPages($model, $limit),
            default => collect()
        };
    }

    /**
     * Portfolio için ilgili içerikler
     */
    private static function getRelatedPortfolios(Model $portfolio, int $limit = 6): Collection
    {
        $relatedPortfolios = collect();

        // Aynı kategorideki portfolyolar
        if ($portfolio->portfolio_category_id) {
            $categoryPortfolios = $portfolio->newQuery()
                ->where('portfolio_category_id', $portfolio->portfolio_category_id)
                ->where('portfolio_id', '!=', $portfolio->portfolio_id)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            $relatedPortfolios = $relatedPortfolios->merge($categoryPortfolios);
        }

        // Yeterli değilse son portfolyolar
        if ($relatedPortfolios->count() < $limit) {
            $recentPortfolios = $portfolio->newQuery()
                ->where('portfolio_id', '!=', $portfolio->portfolio_id)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit - $relatedPortfolios->count())
                ->get();

            $relatedPortfolios = $relatedPortfolios->merge($recentPortfolios);
        }

        return $relatedPortfolios
            ->unique('portfolio_id')
            ->take($limit)
            ->values();
    }

    /**
     * Page için ilgili sayfalar
     */
    private static function getRelatedPages(Model $page, int $limit = 6): Collection
    {
        $currentLocale = app()->getLocale();
        $currentTitle = $page->getTranslated('title', $currentLocale);

        if (!$currentTitle) {
            return collect();
        }

        $titleWords = self::extractKeywords($currentTitle);

        if (empty($titleWords)) {
            return collect();
        }

        return $page->newQuery()
            ->where('page_id', '!=', $page->page_id)
            ->where('is_active', true)
            ->where(function (Builder $query) use ($titleWords, $currentLocale) {
                foreach ($titleWords as $word) {
                    $query->orWhere("title->{$currentLocale}", 'LIKE', "%{$word}%");
                }
            })
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Metinden anahtar kelimeleri çıkar
     */
    private static function extractKeywords(string $text): array
    {
        // HTML tag'larını kaldır
        $text = strip_tags($text);

        // Küçük harfe çevir
        $text = mb_strtolower($text, 'UTF-8');

        // Türkçe stop words (yaygın kelimeler)
        $stopWords = [
            'bir', 'bu', 'şu', 'o', 've', 'ile', 'için', 'da', 'de', 'ta', 'te',
            'den', 'dan', 'deki', 'daki', 'nin', 'nın', 'nun', 'nün',
            'an', 'the', 'a', 'is', 'are', 'was', 'were', 'be', 'been', 'being',
            'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could',
            'should', 'may', 'might', 'must', 'can', 'shall', 'to', 'of', 'in',
            'on', 'at', 'by', 'for', 'with', 'as', 'from', 'up', 'out', 'off',
            'over', 'under', 'again', 'further', 'then', 'once', 'here', 'there',
            'when', 'where', 'why', 'how', 'all', 'any', 'both', 'each', 'few',
            'more', 'most', 'other', 'some', 'such', 'only', 'own', 'same', 'so',
            'than', 'too', 'very', 'that', 'these', 'they', 'them', 'their',
            'what', 'which', 'who', 'whom', 'this', 'it', 'its', 'i', 'me', 'my',
            'we', 'our', 'ours', 'you', 'your', 'yours', 'he', 'him', 'his', 'she',
            'her', 'hers'
        ];

        // Kelimeleri ayır
        $words = preg_split('/[^\p{L}\p{N}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Stop words'leri filtrele ve minimum uzunluk kontrolü
        $keywords = array_filter($words, function ($word) use ($stopWords) {
            return mb_strlen($word, 'UTF-8') >= 3 && !in_array($word, $stopWords);
        });

        // En fazla 5 anahtar kelime döndür
        return array_slice($keywords, 0, 5);
    }

    /**
     * İçerik benzerlik puanı hesapla (gelecekteki kullanım için)
     */
    private static function calculateSimilarityScore(Model $item1, Model $item2): float
    {
        $score = 0;
        $maxScore = 0;

        // Kategori benzerliği (40 puan)
        $maxScore += 40;
        if (isset($item1->category_id) && isset($item2->category_id) &&
            $item1->category_id === $item2->category_id) {
            $score += 40;
        }

        // Tag benzerliği (30 puan)
        $tags1 = self::extractTagNames($item1);
        $tags2 = self::extractTagNames($item2);

        if (!empty($tags1) && !empty($tags2)) {
            $maxScore += 30;
            $commonTags = array_intersect($tags1, $tags2);
            $totalTags = array_unique(array_merge($tags1, $tags2));

            if (!empty($totalTags)) {
                $tagSimilarity = count($commonTags) / count($totalTags);
                $score += $tagSimilarity * 30;
            }
        }

        // Başlık benzerliği (30 puan)
        $maxScore += 30;
        $currentLocale = app()->getLocale();

        if (method_exists($item1, 'getTranslated') && method_exists($item2, 'getTranslated')) {
            $title1 = $item1->getTranslated('title', $currentLocale);
            $title2 = $item2->getTranslated('title', $currentLocale);

            if ($title1 && $title2) {
                $words1 = self::extractKeywords($title1);
                $words2 = self::extractKeywords($title2);

                if (!empty($words1) && !empty($words2)) {
                    $commonWords = array_intersect($words1, $words2);
                    $totalWords = array_unique(array_merge($words1, $words2));

                    if (!empty($totalWords)) {
                        $titleSimilarity = count($commonWords) / count($totalWords);
                        $score += $titleSimilarity * 30;
                    }
                }
            }
        }

        return $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
    }

    /**
     * Modelden etiket isimlerini çıkart.
     */
    private static function extractTagNames($model): array
    {
        if (!$model) {
            return [];
        }

        if (method_exists($model, 'tags')) {
            $model->loadMissing('tags');

            if ($model->tags instanceof \Illuminate\Support\Collection) {
                return $model->tags
                    ->pluck('name')
                    ->filter()
                    ->map(fn ($name) => trim((string) $name))
                    ->unique()
                    ->values()
                    ->all();
            }
        }

        if (isset($model->tag_list) && is_array($model->tag_list)) {
            return array_values(array_unique(array_filter($model->tag_list)));
        }

        if (isset($model->tags)) {
            if ($model->tags instanceof \Illuminate\Support\Collection) {
                return $model->tags
                    ->filter(fn ($tag) => filled($tag))
                    ->map(fn ($tag) => trim((string) $tag))
                    ->unique()
                    ->values()
                    ->all();
            }

            if (is_array($model->tags)) {
                return array_values(array_unique(array_filter($model->tags)));
            }
        }

        return [];
    }
}
