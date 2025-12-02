<?php

namespace Modules\Blog\app\Services\Tenants;

use Modules\Shop\app\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Tenant 2 (ixtif.com) Blog Product Injector
 *
 * Injects shop product cards and CTA banners into blog content for Tenant 2
 *
 * Pattern: 3 product + 1 CTA + 3 product + 1 CTA ...
 */
class Tenant2BlogProductInjector
{
    /**
     * Ana kategoriler (İxtif için)
     */
    private array $mainCategories = [
        ['id' => 1, 'title' => 'Forklift', 'slug' => 'forklift', 'icon' => 'fa-forklift'],
        ['id' => 2, 'title' => 'Transpalet', 'slug' => 'transpalet', 'icon' => 'fa-dolly'],
        ['id' => 3, 'title' => 'İstif Makinesi', 'slug' => 'istif-makinesi', 'icon' => 'fa-boxes-stacked'],
        ['id' => 4, 'title' => 'Order Picker', 'slug' => 'siparis-toplama-makinesi', 'icon' => 'fa-hand-holding-box'],
        ['id' => 5, 'title' => 'Otonom Sistemler', 'slug' => 'otonom-sistemler', 'icon' => 'fa-robot'],
        ['id' => 6, 'title' => 'Reach Truck', 'slug' => 'reach-truck', 'icon' => 'fa-truck-loading'],
        ['id' => 7, 'title' => 'Yedek Parça', 'slug' => 'yedek-parca', 'icon' => 'fa-gears'],
    ];

    /**
     * CTA bantları - dinamik ve çeşitli
     */
    private array $ctaBanners = [];

    /**
     * CTA Temaları - 6 farklı renk teması (Random seçilir)
     */
    private array $ctaThemes = [
        'blue' => [
            'bgClass' => 'cta-bg-blue',
            'bgStyle' => 'background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 25%, #2563eb 50%, #1e40af 75%, #1e3a8a 100%); background-size: 200% 200%; animation: cta-bg-shift 8s ease infinite;',
        ],
        'green' => [
            'bgClass' => 'cta-bg-green',
            'bgStyle' => 'background: linear-gradient(135deg, #065f46 0%, #047857 25%, #059669 50%, #047857 75%, #065f46 100%); background-size: 200% 200%; animation: cta-bg-shift 8s ease infinite;',
        ],
        'orange' => [
            'bgClass' => 'cta-bg-orange',
            'bgStyle' => 'background: linear-gradient(135deg, #9a3412 0%, #c2410c 25%, #ea580c 50%, #c2410c 75%, #9a3412 100%); background-size: 200% 200%; animation: cta-bg-shift 8s ease infinite;',
        ],
        'purple' => [
            'bgClass' => 'cta-bg-purple',
            'bgStyle' => 'background: linear-gradient(135deg, #5b21b6 0%, #6d28d9 25%, #7c3aed 50%, #6d28d9 75%, #5b21b6 100%); background-size: 200% 200%; animation: cta-bg-shift 8s ease infinite;',
        ],
        'gold' => [
            'bgClass' => 'cta-bg-gold',
            'bgStyle' => 'background: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #334155 50%, #1e293b 75%, #0f172a 100%); background-size: 200% 200%; animation: cta-bg-shift 8s ease infinite;',
            'isGold' => true,
        ],
        'red' => [
            'bgClass' => 'cta-bg-red',
            'bgStyle' => 'background: linear-gradient(135deg, #991b1b 0%, #b91c1c 25%, #dc2626 50%, #b91c1c 75%, #991b1b 100%); background-size: 200% 200%; animation: cta-bg-shift 8s ease infinite;',
        ],
    ];

    /**
     * Blog içeriğinden çıkarılan anahtar kelimeler
     */
    private array $blogKeywords = [];

    /**
     * Inject products into blog content between H2 tags
     * Pattern: 3 product + 1 CTA banner (alternating)
     */
    public function injectProducts(string $content, $blog): string
    {
        try {
            // Tenant context kontrolü - tenant yoksa ürün inject etme
            if (!function_exists('tenant') || tenant() === null) {
                return $content;
            }

            // Cache key per blog (tenant-aware)
            $tenantId = tenant()->id ?? 'unknown';
            $cacheKey = "blog_product_injection_v4_{$tenantId}_{$blog->id}";

            $result = Cache::remember($cacheKey, 3600, function () use ($content, $blog) {
                // Blog anahtar kelimelerini çıkar
                $this->extractBlogKeywords($blog);

                // CTA bantlarını hazırla
                $this->prepareCTABanners($blog);

                // Get matching products
                $products = $this->getMatchingProducts($blog);

                if ($products->isEmpty()) {
                    return $content;
                }

                // Split content by H2 tags
                $parts = preg_split('/(<h2[^>]*>.*?<\/h2>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

                if (count($parts) <= 1) {
                    return $content;
                }

                $result = '';
                $h2Count = 0;
                $productIndex = 0;
                $ctaIndex = 0;
                $injectionCount = 0; // Toplam injection sayısı

                $isFirstInjection = true;

                foreach ($parts as $part) {
                    // Check if this is an H2 tag
                    if (preg_match('/<h2[^>]*>.*?<\/h2>/i', $part)) {
                        $h2Count++;

                        // Skip first H2, inject BEFORE subsequent H2s
                        if ($h2Count > 1) {
                            $injectionCount++;

                            // Pattern: 3 product + 1 CTA (tek/çift kontrolü)
                            if ($injectionCount % 2 === 1) {
                                // Tek sayı: 3 ürün göster
                                if ($productIndex < $products->count()) {
                                    $cardProducts = $products->slice($productIndex, 3);
                                    $result .= $this->renderProductCard($cardProducts, $isFirstInjection);
                                    $productIndex += 3;
                                    $isFirstInjection = false;
                                }
                            } else {
                                // Çift sayı: CTA bant göster
                                $result .= $this->renderCTABanner($ctaIndex);
                                $ctaIndex++;
                            }
                        }
                    }

                    $result .= $part;
                }

                return $result;
            });

            return $result;
        } catch (\Exception $e) {
            // Log error and return original content
            \Log::error('Tenant2BlogProductInjector error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return $content;
        }
    }

    /**
     * Blog içeriğinden anahtar kelimeler çıkar
     */
    private function extractBlogKeywords($blog): void
    {
        $currentLocale = app()->getLocale();
        $keywords = [];

        // Title'dan
        $title = $blog->getTranslated('title', $currentLocale) ?? '';
        $titleWords = explode(' ', strtolower($title));
        $keywords = array_merge($keywords, array_filter($titleWords, fn($w) => mb_strlen($w) > 3));

        // Tags'den
        if ($blog->tags && $blog->tags->isNotEmpty()) {
            $tagNames = $blog->tags->pluck('name')->map(fn($n) => strtolower($n))->toArray();
            $keywords = array_merge($keywords, $tagNames);
        }

        $this->blogKeywords = array_unique($keywords);
    }

    /**
     * CTA bantlarını hazırla - HER CTA'DA TELEFON + WHATSAPP + SİZİ ARAYALIM
     * Setting values'dan dinamik değerler çeker
     */
    private function prepareCTABanners($blog): void
    {
        // İlgili kategoriyi bul (blog keywords'e göre)
        $matchedCategory = $this->findMatchingCategory();
        $altCategory = $this->getAlternativeCategory($matchedCategory['id']);

        // Settings'den dinamik değerler
        $siteName = setting('site_name') ?: 'iXtif';
        $siteSlogan = setting('site_slogan') ?: 'Türkiye\'nin İstif Pazarı';
        $phone = setting('site_phone') ?: '0216 755 3 555';
        $whatsapp = setting('site_whatsapp') ?: '0501 005 67 58';

        // WhatsApp numarasını uluslararası formata çevir
        $whatsappClean = preg_replace('/[^0-9]/', '', $whatsapp);
        if (strlen($whatsappClean) === 10) {
            $whatsappClean = '90' . $whatsappClean;
        }

        // Ortak veriler - her CTA'da kullanılacak
        $commonData = [
            'phone' => $phone,
            'whatsapp' => $whatsappClean,
            'siteName' => $siteName,
            'siteSlogan' => $siteSlogan,
        ];

        // Kategori adlarını al
        $catName = $matchedCategory['title']; // Forklift, Transpalet vb.
        $altCatName = $altCategory['title'];

        // Dinamik headline'lar - kategori adı ile
        $this->ctaBanners = [
            // CTA 1: En Uygun Fiyat
            array_merge($commonData, [
                'type' => 'style_blue',
                'category' => $matchedCategory,
                'headline' => "{$catName}'lerde En Uygun Fiyat Garantisi iXtif'te",
                'subheadline' => 'Fiyat almadan karar vermeyin!',
            ]),
            // CTA 2: Hemen Ara
            array_merge($commonData, [
                'type' => 'style_green',
                'category' => $matchedCategory,
                'headline' => "{$catName} mi Arıyorsunuz? Hemen Arayın!",
                'subheadline' => '30 saniyede size dönüş yapalım',
            ]),
            // CTA 3: Fiyat Teklifi
            array_merge($commonData, [
                'type' => 'style_orange',
                'category' => $matchedCategory,
                'headline' => "{$catName} Fiyatı Almadan Karar Vermeyin!",
                'subheadline' => 'Türkiye\'nin her yerine teslimat',
            ]),
            // CTA 4: Uzman Danışmanlık
            array_merge($commonData, [
                'type' => 'style_purple',
                'category' => $matchedCategory,
                'headline' => "{$catName} Seçiminde Uzman Danışmanlık",
                'subheadline' => 'İhtiyacınıza özel çözümler sunuyoruz',
            ]),
            // CTA 5: Garantili Ürünler (Gold tema)
            array_merge($commonData, [
                'type' => 'style_dark',
                'category' => $matchedCategory,
                'headline' => "Garantili {$catName}'ler iXtif'te",
                'subheadline' => '500+ mutlu müşteri, binlerce başarılı proje',
            ]),
            // CTA 6: Stok Uyarısı
            array_merge($commonData, [
                'type' => 'style_red',
                'category' => $matchedCategory,
                'headline' => "{$catName} Stoklarımız Sınırlı, Acele Edin!",
                'subheadline' => 'Özel fiyatlar için hemen arayın',
            ]),
        ];
    }

    /**
     * Eşleşen kategoriden farklı alternatif kategori seç
     */
    private function getAlternativeCategory(int $excludeId): array
    {
        $alternatives = array_filter($this->mainCategories, fn($c) => $c['id'] !== $excludeId);
        return $alternatives[array_rand($alternatives)] ?? $this->mainCategories[0];
    }

    /**
     * Blog keywords'e göre en uygun kategoriyi bul
     */
    private function findMatchingCategory(): array
    {
        // Keyword eşleştirme
        $categoryKeywords = [
            1 => ['forklift', 'fork', 'lift', 'dizel', 'elektrikli', 'lpg'],
            2 => ['transpalet', 'palet', 'manuel', 'akülü'],
            3 => ['istif', 'stacker', 'istifleme', 'yükseltici'],
            4 => ['order', 'picker', 'sipariş', 'toplama'],
            5 => ['otonom', 'agv', 'amr', 'robot', 'otomatik'],
            6 => ['reach', 'truck', 'dar', 'koridor'],
            7 => ['yedek', 'parça', 'bakım', 'servis'],
        ];

        $maxScore = 0;
        $matchedId = 1; // Default: Forklift

        foreach ($categoryKeywords as $catId => $catKeywords) {
            $score = 0;
            foreach ($this->blogKeywords as $blogKeyword) {
                foreach ($catKeywords as $catKeyword) {
                    if (stripos($blogKeyword, $catKeyword) !== false || stripos($catKeyword, $blogKeyword) !== false) {
                        $score++;
                    }
                }
            }
            if ($score > $maxScore) {
                $maxScore = $score;
                $matchedId = $catId;
            }
        }

        // Eşleşen kategoriyi bul
        foreach ($this->mainCategories as $cat) {
            if ($cat['id'] === $matchedId) {
                return $cat;
            }
        }

        return $this->mainCategories[0]; // Fallback
    }

    /**
     * Get matching products for blog with smart matching and priority sorting
     *
     * MATCHING (hangi �r�nler): Tags � Title � Excerpt � Random
     * SORTING (nas1l s1ralans1n):
     *   1. show_on_homepage=1 olanlar
     *   2. show_on_homepage=0 ama stok>0 veya fiyat>0 olanlar
     *   3. Fiyat veya stok=0 olanlar
     *   4. Dier yak1n �r�nler
     */
    private function getMatchingProducts($blog): Collection
    {
        $matchedProducts = collect();
        $currentLocale = app()->getLocale();

        // 1� MATCHING PHASE: Try matching by TAGS
        if ($blog->tags && $blog->tags->isNotEmpty()) {
            $tagNames = $blog->tags->pluck('name')->toArray();

            foreach ($tagNames as $tagName) {
                $tagProducts = ShopProduct::where(function($q) use ($tagName, $currentLocale) {
                    $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"$currentLocale\"')) LIKE ?", ["%{$tagName}%"])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(short_description, '$.\"$currentLocale\"')) LIKE ?", ["%{$tagName}%"])
                      ->orWhere('tags', 'LIKE', "%{$tagName}%");
                })
                ->get();

                $matchedProducts = $matchedProducts->merge($tagProducts);
            }
        }

        // 2� MATCHING PHASE: Try matching by TITLE keywords
        $title = $blog->getTranslated('title', $currentLocale);
        if ($title) {
            $keywords = explode(' ', $title);
            $keywords = array_filter($keywords, fn($k) => mb_strlen($k) > 3);

            foreach ($keywords as $keyword) {
                $titleProducts = ShopProduct::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"$currentLocale\"')) LIKE ?", ["%{$keyword}%"])
                    ->get();

                $matchedProducts = $matchedProducts->merge($titleProducts);
            }
        }

        // 3� MATCHING PHASE: Try matching by EXCERPT keywords
        $excerpt = $blog->getTranslated('excerpt', $currentLocale);
        if ($excerpt) {
            $keywords = explode(' ', strip_tags($excerpt));
            $keywords = array_filter($keywords, fn($k) => mb_strlen($k) > 4);
            $keywords = array_slice($keywords, 0, 5);

            foreach ($keywords as $keyword) {
                $excerptProducts = ShopProduct::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"$currentLocale\"')) LIKE ?", ["%{$keyword}%"])
                    ->get();

                $matchedProducts = $matchedProducts->merge($excerptProducts);
            }
        }

        // Remove duplicates
        $matchedProducts = $matchedProducts->unique('product_id');

        // 4� MATCHING PHASE: If we don't have enough products (less than 9), add random products
        if ($matchedProducts->count() < 9) {
            $neededCount = 9 - $matchedProducts->count();
            $excludeIds = $matchedProducts->pluck('product_id')->toArray();

            $randomProducts = ShopProduct::whereNotIn('product_id', $excludeIds)
                ->inRandomOrder()
                ->take($neededCount)
                ->get();

            $matchedProducts = $matchedProducts->merge($randomProducts);
        }

        // <� SORTING PHASE: Apply priority sorting
        $sorted = $matchedProducts->sortBy(function($product) {
            $hasStock = ($product->current_stock ?? 0) > 0;
            $hasPrice = ($product->base_price ?? 0) > 0;
            $isHomepage = $product->show_on_homepage == 1;

            // Priority calculation (lower number = higher priority)
            if ($isHomepage) {
                return 1; // Highest priority
            } elseif (!$isHomepage && ($hasStock || $hasPrice)) {
                return 2; // Second priority
            } elseif (!$hasPrice || !$hasStock) {
                return 3; // Third priority
            } else {
                return 4; // Lowest priority
            }
        })->values();

        // Return top 9 products
        return $sorted->take(9);
    }

    /**
     * Render product cards using the EXACT same component as homepage
     *
     * NOTE: Blade::render() CANNOT be used here because the output is passed
     * to another Blade::render() call in show-content.blade.php, causing
     * "Cannot end a section without first starting one" error.
     *
     * Solution: Use view()->make()->render() which doesn't interfere with
     * section management.
     */
    private function renderProductCard(Collection $products): string
    {
        if ($products->isEmpty()) {
            return '';
        }

        $html = "\n\n";

        // "Yazı Devam Ediyor" badge (only on top)
        $html .= "<div class=\"flex justify-center items-center mb-4\">\n";
        $html .= "    <div class=\"inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-100/50 dark:bg-gray-800/30 backdrop-blur-sm border border-gray-200/30 dark:border-gray-700/30\">\n";
        $html .= "        <i class=\"fas fa-chevron-down text-gray-400 dark:text-gray-600 animate-bounce text-sm\"></i>\n";
        $html .= "        <span class=\"text-sm font-medium text-gray-400 dark:text-gray-600\">Yazı Devam Ediyor</span>\n";
        $html .= "        <i class=\"fas fa-chevron-down text-gray-400 dark:text-gray-600 animate-bounce text-sm\"></i>\n";
        $html .= "    </div>\n";
        $html .= "</div>\n\n";

        // Mobile/Tablet (xs, sm, md): Horizontal layout (single column)
        // Homepage list view ile aynı: gap-6
        $html .= "<div class=\"lg:hidden grid grid-cols-1 gap-6\">\n";
        foreach ($products as $index => $product) {
            try {
                $html .= view('components.ixtif.product-card', [
                    'product' => $product,
                    'layout' => 'horizontal',
                    'showAddToCart' => true,
                    'index' => $index
                ])->render();
            } catch (\Exception $e) {
                \Log::error('Product card render error (horizontal): ' . $e->getMessage());
            }
        }
        $html .= "</div>\n";

        // Desktop (lg+): Vertical layout (3 columns)
        // Homepage grid view ile aynı: gap-8
        $html .= "<div class=\"hidden lg:grid lg:grid-cols-3 gap-8\">\n";
        foreach ($products as $index => $product) {
            try {
                $html .= view('components.ixtif.product-card', [
                    'product' => $product,
                    'layout' => 'vertical',
                    'showAddToCart' => true,
                    'index' => $index
                ])->render();
            } catch (\Exception $e) {
                \Log::error('Product card render error (vertical): ' . $e->getMessage());
            }
        }
        $html .= "</div>\n\n";

        return $html;
    }

    /**
     * CTA Bant render et - HER CTA'DA TELEFON + WHATSAPP + SİZİ ARAYALIM
     * v7: Animasyonlu gradient arka planlar, X logosu, glass butonlar
     */
    private function renderCTABanner(int $index): string
    {
        $bannerIndex = $index % count($this->ctaBanners);
        $banner = $this->ctaBanners[$bannerIndex];

        // Random tema seç (6 tema)
        $themeKeys = array_keys($this->ctaThemes);
        $randomTheme = $themeKeys[array_rand($themeKeys)];
        $theme = $this->ctaThemes[$randomTheme];
        $isGold = $theme['isGold'] ?? false;

        $html = "\n\n";

        // Minimal divider
        $html .= "<div class=\"flex justify-center items-center mb-3\">\n";
        $html .= "    <div class=\"inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100/30 dark:bg-gray-800/20\">\n";
        $html .= "        <i class=\"fas fa-chevron-down text-gray-400 dark:text-gray-600 text-xs\"></i>\n";
        $html .= "        <span class=\"text-xs text-gray-400 dark:text-gray-600\">Yazı devam ediyor</span>\n";
        $html .= "        <i class=\"fas fa-chevron-down text-gray-400 dark:text-gray-600 text-xs\"></i>\n";
        $html .= "    </div>\n";
        $html .= "</div>\n\n";

        // Yeni v7 tasarımı ile render
        $html .= $this->renderModernCTA($banner, $theme, $isGold);

        return $html;
    }

    /**
     * MODERN CTA RENDER - v7 Tasarımı
     * Animasyonlu gradient arka plan, X logosu, glass butonlar
     */
    private function renderModernCTA(array $banner, array $theme, bool $isGold = false): string
    {
        $phone = $banner['phone'];
        $whatsapp = $banner['whatsapp'];
        $headline = $banner['headline'];
        $subheadline = $banner['subheadline'];
        $category = $banner['category'];
        $categoryTitle = $category['title'];
        $categoryIcon = $category['icon'] ?? 'fa-boxes-stacked';
        $bgStyle = $theme['bgStyle'];

        // Text gradient stilleri (kontrastlı)
        $headlineGradient = $isGold
            ? 'background-image: linear-gradient(90deg, #fbbf24, #f59e0b, #fcd34d, #fbbf24); background-size: 200% 200%; animation: cta-text-slide 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;'
            : 'background-image: linear-gradient(90deg, #ffffff, #fef3c7, #ffffff, #e0f2fe, #ffffff); background-size: 200% 200%; animation: cta-text-slide 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;';

        $html = "<div class=\"my-8 not-prose\">\n";

        // CSS Keyframes (inline style içinde)
        $html .= "<style>\n";
        $html .= "@keyframes cta-bg-shift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }\n";
        $html .= "@keyframes cta-text-slide { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }\n";
        $html .= "</style>\n";

        // Ana kart - Animasyonlu gradient arka plan
        $html .= "<div class=\"relative overflow-hidden rounded-2xl shadow-2xl\" style=\"{$bgStyle}\">\n";

        // Dekoratif elementler
        $html .= "  <div class=\"absolute inset-0 overflow-hidden pointer-events-none\">\n";
        $html .= "    <div class=\"absolute -top-20 -right-20 w-60 h-60 bg-white/10 rounded-full blur-3xl\"></div>\n";
        $html .= "    <div class=\"absolute -bottom-20 -left-20 w-60 h-60 bg-white/10 rounded-full blur-3xl\"></div>\n";
        $html .= "  </div>\n";

        // İçerik
        $html .= "  <div class=\"relative p-6 md:p-8\">\n";

        // Üst Bölüm: X Logo + Başlık
        $html .= "    <div class=\"flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6\">\n";

        // X Logo (turuncu görsel)
        $html .= "      <div class=\"w-14 h-14 md:w-16 md:h-16 rounded-xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center flex-shrink-0 shadow-lg\">\n";
        $html .= "        <img src=\"https://ixtif.com/storage/tenant2/355/x.png\" alt=\"iXtif\" class=\"w-10 h-10 md:w-12 md:h-12 object-contain\">\n";
        $html .= "      </div>\n";

        // Başlık ve alt başlık
        $html .= "      <div class=\"flex-1\">\n";
        $html .= "        <h3 class=\"text-xl md:text-2xl font-extrabold mb-1\" style=\"{$headlineGradient}\">{$headline}</h3>\n";
        $html .= "        <p class=\"text-white/80 text-sm md:text-base\">{$subheadline}</p>\n";
        $html .= "      </div>\n";

        // Kategori badge (sağ üst)
        $html .= "      <a href=\"/shop/kategori/{$category['slug']}\" class=\"inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/20 backdrop-blur-md border border-white/30 text-white text-sm font-semibold hover:bg-white/30 transition-all\">\n";
        $html .= "        <i class=\"fas {$categoryIcon}\"></i>\n";
        $html .= "        <span>{$categoryTitle}</span>\n";
        $html .= "        <i class=\"fas fa-arrow-right text-xs\"></i>\n";
        $html .= "      </a>\n";

        $html .= "    </div>\n";

        // Butonlar - PC'de 3'lü yan yana (flex), mobilde 2+1
        $html .= "    <div class=\"flex flex-col gap-3\">\n";

        // Mobil: Tel + WhatsApp yan yana, Sizi Arayalım altta
        // PC: Hepsi yan yana (flex-row)
        $html .= "      <div class=\"flex flex-col sm:flex-row gap-3\">\n";

        // Telefon butonu (glass efekt)
        $html .= "        <a href=\"tel:{$phone}\" class=\"flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white/20 backdrop-blur-md border border-white/30 text-white font-bold text-sm transition-all hover:bg-white/30 hover:scale-[1.02] hover:shadow-lg\">\n";
        $html .= "          <i class=\"fas fa-phone\"></i>\n";
        $html .= "          <span>{$phone}</span>\n";
        $html .= "        </a>\n";

        // WhatsApp butonu (yeşil glass)
        $html .= "        <a href=\"https://wa.me/{$whatsapp}?text=Merhaba,%20{$categoryTitle}%20hakkında%20bilgi%20almak%20istiyorum\" target=\"_blank\" rel=\"noopener\" class=\"flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-green-500/80 backdrop-blur-md border border-green-400/50 text-white font-bold text-sm transition-all hover:bg-green-500 hover:scale-[1.02] hover:shadow-lg\">\n";
        $html .= "          <i class=\"fab fa-whatsapp text-lg\"></i>\n";
        $html .= "          <span>WhatsApp</span>\n";
        $html .= "        </a>\n";

        // Sizi Arayalım butonu (glass efekt) - PC'de yan yana, mobilde tam genişlik
        $html .= "        <a href=\"/sizi-arayalim\" class=\"flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white/20 backdrop-blur-md border border-white/30 text-white font-bold text-sm transition-all hover:bg-white/30 hover:scale-[1.02] hover:shadow-lg\">\n";
        $html .= "          <i class=\"fas fa-phone-volume\"></i>\n";
        $html .= "          <span>Sizi Arayalım</span>\n";
        $html .= "        </a>\n";

        $html .= "      </div>\n";
        $html .= "    </div>\n";

        // Trust badges (masaüstü)
        $html .= "    <div class=\"hidden md:flex items-center justify-center gap-6 mt-6 pt-5 border-t border-white/20\">\n";
        $html .= "      <span class=\"inline-flex items-center gap-2 text-white/70 text-sm\">\n";
        $html .= "        <i class=\"fas fa-check-circle text-green-400\"></i> En Uygun Fiyat\n";
        $html .= "      </span>\n";
        $html .= "      <span class=\"inline-flex items-center gap-2 text-white/70 text-sm\">\n";
        $html .= "        <i class=\"fas fa-truck text-blue-300\"></i> Hızlı Teslimat\n";
        $html .= "      </span>\n";
        $html .= "      <span class=\"inline-flex items-center gap-2 text-white/70 text-sm\">\n";
        $html .= "        <i class=\"fas fa-shield-alt text-amber-400\"></i> Garanti\n";
        $html .= "      </span>\n";
        $html .= "      <span class=\"inline-flex items-center gap-2 text-white/70 text-sm\">\n";
        $html .= "        <i class=\"fas fa-headset text-purple-300\"></i> 7/24 Destek\n";
        $html .= "      </span>\n";
        $html .= "    </div>\n";

        $html .= "  </div>\n";
        $html .= "</div>\n";
        $html .= "</div>\n\n";

        return $html;
    }
}
