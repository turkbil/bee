<?php

declare(strict_types=1);

namespace Modules\SeoManagement\app\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\Page\app\Models\Page;
use Modules\Portfolio\app\Models\Portfolio;
use Modules\Portfolio\app\Models\PortfolioCategory;
use Modules\Announcement\App\Models\Announcement;
use Modules\Blog\App\Models\Blog;

/**
 * Schema.org Otomatik Oluşturma Servisi
 * 
 * Model tipine göre otomatik Schema.org structured data üretir
 * SEO ve sosyal medya paylaşımları için optimize edilmiştir
 * 
 * V2: Dinamik model discovery ile sınırsız modül desteği
 */
class SchemaGeneratorService
{
    private SchemaRegistryService $registry;

    public function __construct(SchemaRegistryService $registry)
    {
        $this->registry = $registry;
    }
    /**
     * Ana schema oluşturma metodu - V2 Dinamik
     */
    public function generateSchema(Model $model, string $language = 'tr'): array
    {
        try {
            $className = get_class($model);
            
            // 1. Registry'den model bilgisini al
            $modelInfo = $this->registry->getModelInfo($className);
            
            if ($modelInfo && $modelInfo['confidence'] >= 70) {
                // Yüksek güvenilirlik - registry bilgisini kullan
                $schema = $this->generateSchemaFromRegistry($model, $modelInfo, $language);
            } else {
                // Düşük güvenilirlik veya registry'de yok - hardcode fallback
                $schema = $this->generateHardcodedSchema($model, $language);
            }

            // @context'i EN BAŞA ekle (JSON-LD standardı)
            $finalSchema = ['@context' => 'https://schema.org'];
            $finalSchema = array_merge($finalSchema, $schema);
            
            // Log::info('Schema.org generated successfully', [
            //     'model' => $className,
            //     'id' => $model->getKey(),
            //     'language' => $language,
            //     'method' => $modelInfo ? 'registry' : 'hardcoded',
            //     'confidence' => $modelInfo['confidence'] ?? 0
            // ]);

            return $finalSchema;

        } catch (\Exception $e) {
            Log::error('Schema generation failed', [
                'model' => get_class($model),
                'error' => $e->getMessage()
            ]);

            return $this->getDefaultSchema();
        }
    }

    /**
     * Registry bilgisinden schema oluştur (YENİ)
     */
    private function generateSchemaFromRegistry(Model $model, array $modelInfo, string $language): array
    {
        $schemaType = $modelInfo['schema_type'];
        $detectedFields = $modelInfo['detected_fields'];
        $hasTranslations = $modelInfo['has_translations'];
        
        // Base schema
        $schema = [
            '@type' => $schemaType,
            'url' => url()->current(),
            'inLanguage' => $language
        ];

        // Başlık alanını bul ve ekle
        if (isset($detectedFields['title'])) {
            $titleField = $detectedFields['title'][0];
            $title = $hasTranslations ? 
                ($model->getTranslated($titleField, $language) ?? $model->$titleField) : 
                $model->$titleField;
            
            // Title'ı da temizle
            $cleanTitle = $this->cleanHtmlContent($title);
            $schema['name'] = $cleanTitle;
            $schema['headline'] = $cleanTitle; // Article types için
        }

        // Açıklama alanını bul ve ekle
        if (isset($detectedFields['description'])) {
            $descField = $detectedFields['description'][0];
            $description = $hasTranslations ? 
                ($model->getTranslated($descField, $language) ?? $model->$descField) : 
                $model->$descField;
            
            if ($description) {
                $schema['description'] = $this->extractDescription($description);
                if (in_array($schemaType, ['Article', 'NewsArticle'])) {
                    $schema['articleBody'] = $this->cleanArticleBody($description);
                }
            }
        }

        // Resim alanını bul ve ekle
        if (isset($detectedFields['image'])) {
            $imageField = $detectedFields['image'][0];
            if ($model->$imageField) {
                $schema['image'] = [
                    '@type' => 'ImageObject',
                    'url' => asset('storage/' . $model->$imageField),
                    'caption' => $schema['name'] ?? 'Image'
                ];
            }
        }

        // Tarih alanlarını ekle
        $schema['dateCreated'] = $model->created_at?->toISOString();
        $schema['dateModified'] = $model->updated_at?->toISOString();
        
        if (in_array($schemaType, ['Article', 'NewsArticle'])) {
            $schema['datePublished'] = $model->created_at?->toISOString();
            $schema['author'] = $this->getAuthorData($model);
            $schema['publisher'] = $this->getPublisherData($model);
        }

        // E-ticaret alanları
        if ($schemaType === 'Product' && isset($detectedFields['price'])) {
            $priceField = $detectedFields['price'][0];
            if ($model->$priceField) {
                $schema['offers'] = [
                    '@type' => 'Offer',
                    'price' => $model->$priceField,
                    'priceCurrency' => 'TRY'
                ];
            }
        }

        return $schema;
    }

    /**
     * Hardcoded schema (eski sistem - fallback)
     */
    private function generateHardcodedSchema(Model $model, string $language): array
    {
        return match (get_class($model)) {
            Page::class => $this->generatePageSchema($model, $language),
            Portfolio::class => $this->generatePortfolioSchema($model, $language),
            PortfolioCategory::class => $this->generatePortfolioCategorySchema($model, $language),
            Announcement::class => $this->generateAnnouncementSchema($model, $language),
            Blog::class => $this->generateBlogSchema($model, $language),
            default => $this->generateGenericSchema($model, $language)
        };
    }

    /**
     * Page için WebPage schema - UPDATED 2025
     */
    private function generatePageSchema(Page $model, string $language): array
    {
        $title = $model->getTranslated('title', $language) ?: $model->getTranslated('title', 'tr');
        $content = $model->getTranslated('body', $language) ?: $model->getTranslated('body', 'tr');
        $slug = $model->getTranslated('slug', $language) ?: $model->getTranslated('slug', 'tr');
        
        // TEMİZ başlık (site adı olmadan)
        $cleanTitle = $this->cleanHtmlContent($title);
        $siteTitle = setting('site_title', 'Website');
        
        // URL oluşturma
        $pageUrl = $language === 'tr' ? 
            url("/page/{$slug}") : 
            url("/{$language}/page/{$slug}");

        return [
            '@type' => 'WebPage',
            'name' => $cleanTitle,
            'description' => $this->extractDescription($content),
            'url' => $pageUrl,
            'dateCreated' => $model->created_at?->toISOString(),
            'dateModified' => $model->updated_at?->toISOString(),
            'inLanguage' => $language,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => $siteTitle,
                'url' => url('/')
            ]
        ];
    }

    /**
     * Portfolio için CreativeWork schema
     */
    private function generatePortfolioSchema(Portfolio $model, string $language): array
    {
        $title = $model->getTranslated('title', $language) ?: $model->getTranslated('title', 'tr');
        $description = $model->getTranslated('description', $language) ?: $model->getTranslated('description', 'tr');
        
        $cleanTitle = $this->cleanHtmlContent($title);
        $cleanDescription = $this->cleanHtmlContent($description);
        
        return [
            '@type' => 'CreativeWork',
            'name' => $cleanTitle,
            'description' => $cleanDescription,
            'url' => url("/portfolio/{$model->id}"),
            'dateCreated' => $model->created_at?->toISOString(),
            'dateModified' => $model->updated_at?->toISOString(),
            'creator' => $this->getAuthorData($model),
            'about' => $model->category ? [
                '@type' => 'Thing',
                'name' => $this->cleanHtmlContent($model->category->getTranslated('title', $language))
            ] : null,
            'image' => $model->image ? [
                '@type' => 'ImageObject',
                'url' => asset('storage/' . $model->image),
                'caption' => $cleanTitle
            ] : null,
            'inLanguage' => $language
        ];
    }

    /**
     * Portfolio Category için Thing schema
     */
    private function generatePortfolioCategorySchema(PortfolioCategory $model, string $language): array
    {
        $title = $model->getTranslated('title', $language) ?: $model->getTranslated('title', 'tr');
        $description = $model->getTranslated('description', $language) ?: $model->getTranslated('description', 'tr');
        
        $cleanTitle = $this->cleanHtmlContent($title);
        $cleanDescription = $this->cleanHtmlContent($description);

        return [
            '@type' => 'Thing',
            'name' => $cleanTitle,
            'description' => $cleanDescription,
            'url' => url("/portfolio/category/{$model->id}"),
            'inLanguage' => $language,
            'mainEntity' => [
                '@type' => 'ItemList',
                'name' => $cleanTitle . ' Portfolio Items',
                'numberOfItems' => $model->portfolios()->count()
            ]
        ];
    }

    /**
     * Announcement için NewsArticle schema
     */
    private function generateAnnouncementSchema(Announcement $model, string $language): array
    {
        $title = $model->getTranslated('title', $language) ?: $model->getTranslated('title', 'tr');
        $content = $model->getTranslated('content', $language) ?: $model->getTranslated('content', 'tr');

        $cleanTitle = $this->cleanHtmlContent($title);
        $siteTitle = setting('site_title', 'Website');

        return [
            '@type' => 'NewsArticle',
            'name' => $cleanTitle,
            'headline' => $cleanTitle,
            'description' => $this->extractDescription($content),
            'articleBody' => $this->cleanArticleBody($content),
            'url' => url("/announcement/{$model->id}"),
            'dateCreated' => $model->created_at?->toISOString(),
            'datePublished' => $model->created_at?->toISOString(),
            'dateModified' => $model->updated_at?->toISOString(),
            'author' => $this->getAuthorData($model),
            'publisher' => $this->getPublisherData($model),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url("/announcement/{$model->id}")
            ],
            'inLanguage' => $language
        ];
    }

    /**
     * Blog için BlogPosting schema - 2025 SEO OPTIMIZED
     */
    private function generateBlogSchema(Blog $model, string $language): array
    {
        $title = $model->getTranslated('title', $language) ?: $model->getTranslated('title', 'tr');
        $body = $model->getTranslated('body', $language) ?: $model->getTranslated('body', 'tr');
        $excerpt = $model->getTranslated('excerpt', $language) ?: $model->getTranslated('excerpt', 'tr');
        $slug = $model->getTranslated('slug', $language) ?: $model->getTranslated('slug', 'tr');

        $cleanTitle = $this->cleanHtmlContent($title);
        $cleanBody = $this->cleanArticleBody($body);

        // Blog URL'ini oluştur (modül slug'ını dikkate al)
        $moduleSlug = \App\Services\ModuleSlugService::getSlug('Blog', 'show');
        $defaultLocale = get_tenant_default_locale();

        $blogUrl = $language === $defaultLocale ?
            url("/{$moduleSlug}/{$slug}") :
            url("/{$language}/{$moduleSlug}/{$slug}");

        // Base schema
        $schema = [
            '@type' => 'BlogPosting',
            'name' => $cleanTitle,
            'headline' => $cleanTitle,
            'description' => $excerpt ? $this->cleanHtmlContent($excerpt) : $this->extractDescription($body),
            'articleBody' => $cleanBody,
            'url' => $blogUrl,
            'dateCreated' => $model->created_at?->toISOString(),
            'dateModified' => $model->updated_at?->toISOString(),
            'author' => $this->getAuthorData($model),
            'publisher' => $this->getPublisherData($model),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $blogUrl
            ],
            'inLanguage' => $language,
            'isPartOf' => [
                '@type' => 'Blog',
                'name' => setting('site_title', 'Website') . ' Blog',
                'url' => $language === $defaultLocale ?
                    url("/{$moduleSlug}") :
                    url("/{$language}/{$moduleSlug}")
            ]
        ];

        // Published date (yayın tarihi varsa ekle)
        if ($model->published_at && $model->isPublished()) {
            $schema['datePublished'] = $model->published_at->toISOString();
        } else {
            $schema['datePublished'] = $model->created_at?->toISOString();
        }

        // Reading time (içerikten hesapla)
        $readingTime = $model->calculateReadingTime($language);
        if ($readingTime > 0) {
            $schema['timeRequired'] = 'PT' . $readingTime . 'M'; // ISO 8601 duration format
        }

        // Blog category (kategori varsa ekle)
        if ($model->category) {
            $categoryName = $model->category->getTranslated('name', $language) ?:
                           $model->category->getTranslated('name', 'tr');

            $schema['about'] = [
                '@type' => 'Thing',
                'name' => $this->cleanHtmlContent($categoryName)
            ];

            // Blog kategorisini tag olarak da ekle
            $schema['keywords'] = [$this->cleanHtmlContent($categoryName)];
        }

        // Tags (etiketler varsa ekle)
        if (method_exists($model, 'tags')) {
            $model->loadMissing('tags');
            $tagNames = $model->tags
                ->pluck('name')
                ->filter()
                ->map(fn ($name) => $this->cleanHtmlContent($name))
                ->values()
                ->all();

            if (!empty($tagNames)) {
                $existingKeywords = $schema['keywords'] ?? [];
                $schema['keywords'] = array_values(array_unique(array_merge($existingKeywords, $tagNames)));
            }
        }

        // Featured image (öne çıkan resim varsa ekle)
        try {
            $featuredImage = $model->getFirstMediaUrl('featured_image');
            if ($featuredImage) {
                $schema['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $featuredImage,
                    'caption' => $cleanTitle,
                    'width' => 1200,
                    'height' => 630
                ];
            }
        } catch (\Exception $e) {
            // Media yoksa sessizce devam et
        }

        // Blog content'den resim extract et (featured image yoksa)
        if (!isset($schema['image']) && $body) {
            preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $body, $matches);
            if (!empty($matches[1])) {
                $schema['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $matches[1],
                    'caption' => $cleanTitle
                ];
            }
        }

        // Comment count (yorum sayısı - eğer yorum sistemi varsa)
        if (method_exists($model, 'comments')) {
            $commentCount = $model->comments()->count();
            if ($commentCount > 0) {
                $schema['commentCount'] = $commentCount;
                $schema['interactionStatistic'] = [
                    '@type' => 'InteractionCounter',
                    'interactionType' => 'https://schema.org/CommentAction',
                    'userInteractionCount' => $commentCount
                ];
            }
        }

        return $schema;
    }

    /**
     * Generic model için Thing schema
     */
    private function generateGenericSchema(Model $model, string $language): array
    {
        $title = $model->title ?? $model->name ?? 'Content';
        $cleanTitle = $this->cleanHtmlContent($title);
        
        return [
            '@type' => 'Thing',
            'name' => $cleanTitle,
            'url' => url()->current(),
            'inLanguage' => $language
        ];
    }

    /**
     * GLOBAL HTML TEMİZLEME SİSTEMİ
     * Her yerde kullanılacak ultra temizleme
     */
    private function cleanHtmlContent(string $content): string
    {
        if (empty($content)) {
            return '';
        }
        
        // 1. HTML etiketlerini tamamen temizle
        $text = strip_tags($content);
        
        // 2. HTML entity'leri decode et (&amp; → &, &lt; → <, vb.)
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // 3. Çoklu boşlukları tek boşluğa çevir
        $text = preg_replace('/\s+/', ' ', $text);
        
        // 4. Satır başı/sonu boşlukları temizle
        $text = trim($text);
        
        return $text;
    }
    
    /**
     * Article body için özel temizleme
     */
    private function cleanArticleBody(string $content): string
    {
        $cleaned = $this->cleanHtmlContent($content);
        
        // Article için ekstra temizlik
        $cleaned = preg_replace('/\r\n|\r|\n/', ' ', $cleaned);
        $cleaned = preg_replace('/\s{2,}/', ' ', $cleaned);
        
        return trim($cleaned);
    }

    /**
     * İçerikten açıklama çıkar (160 karakter) - YENİ VERSİYON
     */
    private function extractDescription(string $content, int $length = 160): string
    {
        $text = $this->cleanHtmlContent($content);
        
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        // Kelime sınırında kes
        $truncated = mb_substr($text, 0, $length - 3);
        $lastSpace = strrpos($truncated, ' ');
        
        if ($lastSpace !== false && $lastSpace > $length * 0.7) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }
        
        return $truncated . '...';
    }

    /**
     * Default schema (fallback)
     */
    private function getDefaultSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => setting('site_title', 'Website'),
            'url' => url()->current(),
            'inLanguage' => app()->getLocale()
        ];
    }

    /**
     * Breadcrumb schema oluştur
     */
    public function generateBreadcrumbSchema(array $breadcrumbs): array
    {
        $listItems = [];
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url']
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems
        ];
    }

    /**
     * Organization schema oluştur
     */
    public function generateOrganizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => setting('site_title', 'Website'),
            'url' => url('/'),
            'logo' => setting('site_logo', asset('logo.png')),
            'description' => setting('site_description', ''),
            'sameAs' => array_filter([
                setting('social_facebook'),
                setting('social_twitter'),
                setting('social_instagram'),
                setting('social_linkedin')
            ])
        ];
    }

    /**
     * Author bilgisini al - SEO settings'den veya fallback
     */
    private function getAuthorData(Model $model): array
    {
        // SEO setting varsa ve author bilgisi doluysa kullan
        if (method_exists($model, 'seoSetting') && $model->seoSetting) {
            $seoSetting = $model->seoSetting;

            if (!empty($seoSetting->author)) {
                return [
                    '@type' => 'Organization',
                    'name' => $seoSetting->author,
                    'url' => $seoSetting->author_url ?: url('/')
                ];
            }
        }

        // Fallback: site settings
        return [
            '@type' => 'Organization',
            'name' => setting('site_title', 'Website'),
            'url' => url('/')
        ];
    }

    /**
     * Publisher bilgisini al - SEO settings'den veya fallback
     */
    private function getPublisherData(Model $model): array
    {
        // Publisher genellikle site owner olur, author ile aynı olabilir
        // Önce author data'yı dene, fallback olarak site settings
        return $this->getAuthorData($model);
    }
}
