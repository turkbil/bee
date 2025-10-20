<?php

namespace App\Services;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class TenantSitemapService
{
    /**
     * Tenant için çok dilli dinamik sitemap oluştur
     */
    public static function generate(): Sitemap
    {
        $sitemap = Sitemap::create();
        
        // Aktif dilleri al
        $languages = self::getActiveLanguages();
        $defaultLanguage = get_tenant_default_locale();

        // Ana sayfa - tüm diller için
        foreach ($languages as $language) {
            // Object veya array olabilir, type check yap
            $languageCode = is_object($language) ? $language->code : $language['code'];
            $url = $languageCode === $defaultLanguage ? '/' : '/' . $languageCode;
            $sitemap->add(
                Url::create($url)
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(1.0)
            );
        }

        // Dinamik modül içerikleri - tenant'a atanmış content modüllerini işle
        self::addDynamicModuleContent($sitemap, $languages, $defaultLanguage);

        // Shop modülü - Ürünler ve Kategoriler
        self::addShopContent($sitemap, $languages, $defaultLanguage);

        // Blog modülü - Yazılar ve Kategoriler
        self::addBlogContent($sitemap, $languages, $defaultLanguage);

        // Portfolio kategorileri
        self::addPortfolioCategoryContent($sitemap, $languages, $defaultLanguage);

        return $sitemap;
    }
    
    /**
     * Aktif dilleri al
     */
    private static function getActiveLanguages(): array
    {
        try {
            $languages = \App\Services\TenantLanguageProvider::getActiveLanguages();
            // Collection'ı array'e çevir
            return $languages->map(function ($lang) {
                return [
                    'code' => $lang->code,
                    'name' => $lang->native_name,
                    'direction' => $lang->direction ?? 'ltr'
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [
                ['code' => 'tr', 'name' => 'Türkçe', 'direction' => 'ltr'],
                ['code' => 'en', 'name' => 'English', 'direction' => 'ltr']
            ];
        }
    }
    
    /**
     * Tenant'a atanmış aktif content modüllerini al
     */
    private static function getAssignedContentModules(): array
    {
        try {
            $tenantId = tenant()?->id ?? 1;
            
            return TenantHelpers::central(function () use ($tenantId) {
                return DB::table('modules')
                    ->join('module_tenants', 'modules.module_id', '=', 'module_tenants.module_id')
                    ->where('modules.is_active', true)
                    ->where('modules.type', 'content')
                    ->where('module_tenants.tenant_id', $tenantId)
                    ->where('module_tenants.is_active', true)
                    ->select('modules.name', 'modules.display_name')
                    ->get()
                    ->toArray();
            });
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Dinamik modül içeriklerini ekle
     */
    private static function addDynamicModuleContent(Sitemap $sitemap, array $languages, string $defaultLanguage): void
    {
        $modules = self::getAssignedContentModules();
        
        foreach ($modules as $module) {
            $moduleName = $module->name;
            
            try {
                // Modül model sınıfını oluştur
                $modelClass = "Modules\\{$moduleName}\\App\\Models\\{$moduleName}";
                
                if (!class_exists($modelClass)) {
                    continue;
                }
                
                // Model instance'ı oluştur ve aktif kayıtları al
                $model = new $modelClass();
                $records = $model->where('is_active', true)->get();
                
                foreach ($records as $record) {
                    // 🏠 HOMEPAGE FILTER: is_homepage olan sayfaları sitemap'e ekleme (duplicate content)
                    // Homepage zaten ana sayfa route'u ile (/) sitemap'e ekleniyor
                    if (strtolower($moduleName) === 'page' && isset($record->is_homepage) && $record->is_homepage) {
                        \Log::info('🏠 Sitemap: Homepage page skipped', [
                            'module' => $moduleName,
                            'page_id' => $record->id ?? 'unknown',
                            'is_homepage' => $record->is_homepage
                        ]);
                        continue; // Homepage sayfalarını atla
                    }

                    // HasTranslations trait'i var mı kontrol et
                    $hasTranslations = method_exists($record, 'getTranslated');

                    foreach ($languages as $language) {
                        $languageCode = is_object($language) ? $language->code : $language['code'];
                        
                        if ($hasTranslations) {
                            // Çok dilli modül
                            $slug = $record->getTranslated('slug', $languageCode);
                            if (empty($slug)) {
                                continue; // Bu dilde içerik yoksa atla
                            }
                        } else {
                            // Tek dilli modül
                            $slug = self::extractStringFromMultiLang($record->slug ?? null);
                            if (empty($slug)) {
                                continue;
                            }
                        }
                        
                        // URL pattern'ini belirle (modül yapısına göre)
                        $url = self::buildModuleUrl($moduleName, $slug, $languageCode, $defaultLanguage, $record);
                        
                        if ($url) {
                            $sitemap->add(
                                Url::create($url)
                                    ->setLastModificationDate($record->updated_at ?? now())
                                    ->setChangeFrequency(self::getModuleChangeFrequency($moduleName))
                                    ->setPriority(self::getModulePriority($moduleName, $record))
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                // Modül yüklenemiyorsa skip
                continue;
            }
        }
    }
    
    /**
     * Modül URL'ini oluştur
     */
    private static function buildModuleUrl(string $moduleName, string $slug, string $languageCode, string $defaultLanguage, $record): ?string
    {
        $baseUrl = '';
        
        // Dil prefix'i ekle (gerekirse)
        if ($languageCode !== $defaultLanguage) {
            $baseUrl = '/' . $languageCode;
        }
        
        // Modül tipine göre URL pattern'i
        switch (strtolower($moduleName)) {
            case 'page':
                return $baseUrl . '/page/' . $slug;
                
            case 'portfolio':
                // Portfolio kategori ile birlikte
                if (isset($record->category) && $record->category) {
                    $categorySlug = method_exists($record->category, 'getTranslated') 
                        ? $record->category->getTranslated('slug', $languageCode)
                        : $record->category->slug;
                    return $baseUrl . '/portfolio/' . $categorySlug . '/' . $slug;
                }
                return $baseUrl . '/portfolio/' . $slug;
                
            case 'announcement':
                return $baseUrl . '/announcements/' . $slug;
                
            default:
                // Varsayılan pattern: /module-name/slug
                $moduleSlug = strtolower($moduleName);
                return $baseUrl . '/' . $moduleSlug . '/' . $slug;
        }
    }
    
    /**
     * Modül için değişiklik sıklığını belirle
     */
    private static function getModuleChangeFrequency(string $moduleName): string
    {
        switch (strtolower($moduleName)) {
            case 'page':
                return Url::CHANGE_FREQUENCY_WEEKLY;
            case 'announcement':
                return Url::CHANGE_FREQUENCY_MONTHLY;
            case 'portfolio':
                return Url::CHANGE_FREQUENCY_MONTHLY;
            default:
                return Url::CHANGE_FREQUENCY_MONTHLY;
        }
    }
    
    /**
     * 🚀 BASIT VE ETKİLİ PRİORİTY SİSTEMİ
     * Priority score'a dayalı doğrudan hesaplama
     */
    private static function getModulePriority(string $moduleName, $record = null): float
    {
        $basePriority = 0.5; // Varsayılan orta priority
        
        // SEO priority_score'dan direkt priority hesapla
        if ($record && method_exists($record, 'seoSetting') && $record->seoSetting) {
            $priorityScore = $record->seoSetting->priority_score ?? 5;
            // 1-10 arası score'u 0.1-1.0 arası priority'ye çevir
            $basePriority = ($priorityScore / 10) * 0.9 + 0.1; // 1->0.19, 5->0.55, 10->1.0
        }
        
        // Modül tipine göre bonus/ceza
        $moduleBonus = match(strtolower($moduleName)) {
            'page' => 0.1,          // Page'ler daha önemli
            'shopproduct' => 0.05,  // Shop ürünleri önemli
            'blog' => 0.0,          // Blog neutral
            'portfolio' => 0.0,     // Neutral
            'announcement' => -0.1, // Daha az önemli
            default => 0.0
        };
        
        $finalPriority = $basePriority + $moduleBonus;
        
        // Homepage bonus
        if ($record && isset($record->is_homepage) && $record->is_homepage) {
            $finalPriority = 1.0; // Homepage her zaman maksimum
        }
        
        return round(max(0.1, min(1.0, $finalPriority)), 2);
    }
    
    /**
     * 🚀 BİN MODÜL İÇİN GLOBAL STATISTICS HESAPLA
     */
    private static function calculateGlobalPriorityStats(): array
    {
        try {
            // Tüm SEO settings'lerdeki priority_score dağılımını al
            $priorityDistribution = DB::table('seo_settings')
                ->select('priority_score', DB::raw('COUNT(*) as count'))
                ->groupBy('priority_score')
                ->get()
                ->pluck('count', 'priority_score')
                ->toArray();
                
            $totalPages = array_sum($priorityDistribution);
            
            // Yüksek skorlu sayfalar çok fazlaysa (>30%) range compress et
            $veryHighCount = array_sum(array_filter($priorityDistribution, function($score) {
                return $score >= 9; // 9-10 arası çok yüksek
            }, ARRAY_FILTER_USE_KEY));
            
            $highCount = array_sum(array_filter($priorityDistribution, function($score) {
                return $score >= 7 && $score <= 8; // 7-8 arası yüksek
            }, ARRAY_FILTER_USE_KEY));
            
            $veryHighPercentage = $veryHighCount / max(1, $totalPages);
            $highPercentage = $highCount / max(1, $totalPages);
            
            return [
                'total_pages' => $totalPages,
                'very_high_percentage' => $veryHighPercentage,
                'high_percentage' => $highPercentage,
                'distribution' => $priorityDistribution,
                // Range compression factor
                'compression_factor' => $veryHighPercentage > 0.3 ? 0.5 : 1.0
            ];
        } catch (\Exception $e) {
            // Fallback stats
            return [
                'total_pages' => 100,
                'very_high_percentage' => 0.1,
                'high_percentage' => 0.2,
                'compression_factor' => 1.0
            ];
        }
    }
    
    /**
     * 🚀 STEP 2: Ham skor hesaplama
     */
    private static function calculateRawScore(string $moduleName, $record = null): float
    {
        $baseScore = 50; // 0-100 arası ham skor
        
        // SEO priority_score contribution (0-40 points)
        if ($record && method_exists($record, 'seoSetting')) {
            try {
                $seoSettings = $record->seoSetting;
                if ($seoSettings && isset($seoSettings->priority_score)) {
                    // 1-10 arası priority_score'u 0-40 points'e çevir
                    $priorityScore = $seoSettings->priority_score;
                    $baseScore += ($priorityScore / 10) * 40; // 1->4, 5->20, 10->40 points
                } else {
                    $baseScore += 20; // Default varsayılan (5/10 * 40)
                }
            } catch (\Exception $e) {
                $baseScore += 20; // Default varsayılan
            }
        } else {
            $baseScore += 20; // Default varsayılan
        }
        
        // Module type contribution (0-15 points)
        $baseScore += match(strtolower($moduleName)) {
            'page' => 15,
            'portfolio' => 8,
            'announcement' => 3,
            default => 5
        };
        
        // Quality factors (0-25 points)
        if ($record) {
            $baseScore += self::calculateQualityScore($record);     // 0-10 points
            $baseScore += self::calculateFreshnessScore($record);   // 0-8 points  
            $baseScore += self::calculateDepthScore($record);       // 0-7 points
        }
        
        return max(0, min(100, $baseScore));
    }
    
    /**
     * 🚀 STEP 3: Statistical distribution - BİN MODÜL İÇİN 
     */
    private static function applyStatisticalDistribution(float $rawScore, array $stats): float
    {
        // Raw score'u percentile'a çevir
        $percentile = $rawScore / 100;
        
        // Compression factor uygula (çok fazla yüksek skor varsa sıkıştır)
        $compression = $stats['compression_factor'];
        
        // Smart bucketing - bin modül olsa bile doğru dağıtım
        if ($percentile >= 0.9) {
            // Top 10% - en yüksek zone (0.85-1.0 arası dağıt)
            $minPriority = 0.85;
            $maxPriority = 1.0;
            $normalizedPercentile = ($percentile - 0.9) / 0.1;
        } elseif ($percentile >= 0.7) {
            // High zone (0.65-0.84 arası)
            $minPriority = 0.65 * $compression;
            $maxPriority = 0.84;
            $normalizedPercentile = ($percentile - 0.7) / 0.2;
        } elseif ($percentile >= 0.4) {
            // Medium zone (0.4-0.64 arası)
            $minPriority = 0.4 * $compression;
            $maxPriority = 0.64;
            $normalizedPercentile = ($percentile - 0.4) / 0.3;
        } else {
            // Low zone (0.1-0.39 arası)
            $minPriority = 0.1;
            $maxPriority = 0.39;
            $normalizedPercentile = $percentile / 0.4;
        }
        
        // Linear interpolation
        $finalPriority = $minPriority + ($maxPriority - $minPriority) * $normalizedPercentile;
        
        // Random micro-variance (aynı skorlu sayfalar için)
        $microVariance = (mt_rand(-5, 5) / 1000); // ±0.005
        $finalPriority += $microVariance;
        
        return round(max(0.1, min(1.0, $finalPriority)), 3);
    }
    
    /**
     * Modül önem faktörü (0.0-0.1 arası)
     * Bu sistem HEPSI ÖNEMLİ problemi için relative importance sağlar
     */
    private static function getModuleImportanceFactor(string $moduleName): float
    {
        return match(strtolower($moduleName)) {
            'page' => 0.1,        // En önemli içerik tipi
            'portfolio' => 0.05,  // Orta önemli
            'announcement' => 0.02, // Düşük önemli
            default => 0.0
        };
    }
    
    /**
     * İçerik kalitesi faktörü (+/-0.05 arası)
     * HEPSI ÖNEMLİ PROBLEM: İçerik kalitesine göre micro-adjustment
     */
    private static function getContentQualityFactor($record): float
    {
        if (!$record) return 0.0;
        
        $qualityScore = 0.0;
        
        try {
            // Title uzunluğu kontrolü (SEO optimum: 30-60 karakter)
            $title = self::extractStringFromMultiLang($record->title ?? '');
            $titleLength = mb_strlen($title);
            if ($titleLength >= 30 && $titleLength <= 60) {
                $qualityScore += 0.02; // İyi title = +0.02
            } elseif ($titleLength > 60) {
                $qualityScore -= 0.01; // Uzun title = -0.01
            }
            
            // Body/content uzunluğu kontrolü 
            $body = self::extractStringFromMultiLang($record->body ?? $record->content ?? '');
            $bodyLength = mb_strlen(strip_tags($body));
            if ($bodyLength >= 300) {
                $qualityScore += 0.02; // Detaylı içerik = +0.02
            } elseif ($bodyLength < 100) {
                $qualityScore -= 0.02; // Kısa içerik = -0.02
            }
            
            // Meta description varlığı
            if (method_exists($record, 'seoSetting') && $record->seoSetting) {
                $descriptions = $record->seoSetting->descriptions ?? [];
                if (!empty($descriptions)) {
                    $qualityScore += 0.01; // Meta desc var = +0.01
                }
            }
            
        } catch (\Exception $e) {
            // Hata durumunda neutral
        }
        
        return round(max(-0.05, min(0.05, $qualityScore)), 3);
    }
    
    /**
     * Güncellik faktörü (+0.0-0.03 arası)
     * HEPSI ÖNEMLİ PROBLEM: Güncel içerik daha yüksek priority alır
     */
    private static function getFreshnessBoost($record): float
    {
        if (!$record || !isset($record->updated_at)) return 0.0;
        
        try {
            $daysSinceUpdate = now()->diffInDays($record->updated_at);
            
            return match(true) {
                $daysSinceUpdate <= 7 => 0.03,    // Son 1 hafta = +0.03
                $daysSinceUpdate <= 30 => 0.02,   // Son 1 ay = +0.02  
                $daysSinceUpdate <= 90 => 0.01,   // Son 3 ay = +0.01
                default => 0.0                     // Eski içerik = 0
            };
        } catch (\Exception $e) {
            return 0.0;
        }
    }
    
    /**
     * URL derinlik cezası (-0.0-0.1 arası)
     * HEPSI ÖNEMLİ PROBLEM: Derin URL'ler daha düşük priority alır
     */
    private static function getUrlDepthPenalty($record): float
    {
        if (!$record) return 0.0;
        
        try {
            $slug = self::extractStringFromMultiLang($record->slug ?? '');
            if (empty($slug)) return 0.0;
            
            // Slug'daki '/' sayısını say (URL derinliği)
            $depth = substr_count($slug, '/');
            
            return match(true) {
                $depth <= 1 => 0.0,      // Kök seviye = ceza yok
                $depth == 2 => -0.02,    // 2. seviye = -0.02
                $depth == 3 => -0.05,    // 3. seviye = -0.05
                $depth >= 4 => -0.1,     // 4+ seviye = -0.1
                default => 0.0
            };
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * 🚀 İçerik kalitesi skoru hesapla (0-10 points)
     * BİN MODÜL PROBLEMİ: İçerik kalitesine göre detaylı skorlama
     */
    private static function calculateQualityScore($record): float
    {
        if (!$record) return 0.0;
        
        $qualityScore = 0.0;
        
        try {
            // 1. Title kalitesi (0-3 points)
            $title = self::extractStringFromMultiLang($record->title ?? '');
            $titleLength = mb_strlen($title);
            if ($titleLength >= 30 && $titleLength <= 60) {
                $qualityScore += 3.0; // Optimal SEO title = +3
            } elseif ($titleLength >= 20 && $titleLength < 30) {
                $qualityScore += 2.0; // Kısa ama ok = +2
            } elseif ($titleLength > 60 && $titleLength <= 80) {
                $qualityScore += 1.5; // Uzun ama ok = +1.5
            } elseif ($titleLength > 80) {
                $qualityScore += 0.5; // Çok uzun = +0.5
            }
            
            // 2. İçerik uzunluğu kalitesi (0-3 points)
            $body = self::extractStringFromMultiLang($record->body ?? $record->content ?? '');
            $bodyLength = mb_strlen(strip_tags($body));
            if ($bodyLength >= 1000) {
                $qualityScore += 3.0; // Detaylı makale = +3
            } elseif ($bodyLength >= 500) {
                $qualityScore += 2.5; // Orta makale = +2.5
            } elseif ($bodyLength >= 300) {
                $qualityScore += 2.0; // Kısa makale = +2
            } elseif ($bodyLength >= 100) {
                $qualityScore += 1.0; // Çok kısa = +1
            }
            
            // 3. SEO ayarları kalitesi (0-2 points)
            if (method_exists($record, 'seoSetting') && $record->seoSetting) {
                $seoSettings = $record->seoSetting;
                
                // Meta description varlığı
                $descriptions = $seoSettings->descriptions ?? [];
                if (!empty($descriptions)) {
                    $qualityScore += 1.0; // Meta description var = +1
                }
                
                // Keywords varlığı
                $keywords = $seoSettings->keywords ?? [];
                if (!empty($keywords)) {
                    $qualityScore += 0.5; // Keywords var = +0.5
                }
                
                // Custom title varlığı
                $titles = $seoSettings->titles ?? [];
                if (!empty($titles)) {
                    $qualityScore += 0.5; // Custom SEO title = +0.5
                }
            }
            
            // 4. İçerik yapısı kalitesi (0-2 points)
            if (!empty($body)) {
                // HTML tag sayısı (yapılandırılmış içerik)
                $htmlTagCount = substr_count($body, '<');
                if ($htmlTagCount >= 10) {
                    $qualityScore += 1.0; // İyi yapılandırılmış = +1
                } elseif ($htmlTagCount >= 5) {
                    $qualityScore += 0.5; // Orta yapılandırılmış = +0.5
                }
                
                // Paragraf sayısı
                $paragraphCount = substr_count($body, '<p>') + substr_count($body, '</p>');
                if ($paragraphCount >= 6) {
                    $qualityScore += 1.0; // Çok paragraflı = +1
                } elseif ($paragraphCount >= 3) {
                    $qualityScore += 0.5; // Az paragraflı = +0.5
                }
            }
            
        } catch (\Exception $e) {
            // Hata durumunda neutral
        }
        
        return round(max(0.0, min(10.0, $qualityScore)), 1);
    }

    /**
     * 🛡️ JSON array veya string'den güvenli string çıkarma
     * ULTRA DEEP THINK: Her text alanı JSON olabilir
     */
    private static function extractStringFromMultiLang($value): string
    {
        // Null check
        if ($value === null) {
            return '';
        }
        
        // Array ise (JSON multi-language)
        if (is_array($value)) {
            // Boş array kontrolü
            if (empty($value)) {
                return '';
            }
            
            // İlk değeri al (genelde default dil)
            $firstValue = reset($value);
            
            // Recursive check - nested array olabilir
            if (is_array($firstValue)) {
                return self::extractStringFromMultiLang($firstValue);
            }
            
            return (string) $firstValue;
        }
        
        // String ise direkt döndür
        return (string) $value;
    }
    
    /**
     * 🚀 Güncellik skoru hesapla (0-8 points)
     * BİN MODÜL PROBLEMİ: Güncel içerik daha yüksek priority alır
     */
    private static function calculateFreshnessScore($record): float
    {
        if (!$record || !isset($record->updated_at)) return 0.0;
        
        try {
            $now = now();
            $daysSinceUpdate = $now->diffInDays($record->updated_at);
            $daysSinceCreation = isset($record->created_at) ? $now->diffInDays($record->created_at) : 365;
            
            // Son güncelleme tarihine göre scoring
            $updateScore = match(true) {
                $daysSinceUpdate <= 1 => 4.0,     // Bugün güncellenmiş = +4
                $daysSinceUpdate <= 7 => 3.5,     // Son 1 hafta = +3.5
                $daysSinceUpdate <= 30 => 3.0,    // Son 1 ay = +3
                $daysSinceUpdate <= 90 => 2.0,    // Son 3 ay = +2
                $daysSinceUpdate <= 180 => 1.0,   // Son 6 ay = +1
                $daysSinceUpdate <= 365 => 0.5,   // Son 1 yıl = +0.5
                default => 0.0                     // Eski içerik = 0
            };
            
            // Oluşturulma tarihine göre bonus scoring
            $creationScore = match(true) {
                $daysSinceCreation <= 30 => 2.0,   // Yeni içerik = +2
                $daysSinceCreation <= 90 => 1.5,   // Nispeten yeni = +1.5
                $daysSinceCreation <= 180 => 1.0,  // Orta yaşlı = +1
                $daysSinceCreation <= 365 => 0.5,  // Yaşlı = +0.5
                default => 0.0                      // Çok eski = 0
            };
            
            // Güncelleme sıklığı bonusu
            $updateFrequencyScore = 0.0;
            if ($daysSinceCreation > 0) {
                $updateRate = $daysSinceUpdate / max(1, $daysSinceCreation);
                if ($updateRate < 0.1) { // Sık güncellenen
                    $updateFrequencyScore = 2.0;
                } elseif ($updateRate < 0.3) {
                    $updateFrequencyScore = 1.0;
                } elseif ($updateRate < 0.5) {
                    $updateFrequencyScore = 0.5;
                }
            }
            
            $totalScore = $updateScore + $creationScore + $updateFrequencyScore;
            
        } catch (\Exception $e) {
            return 0.0;
        }
        
        return round(max(0.0, min(8.0, $totalScore)), 1);
    }
    
    /**
     * 🚀 URL derinlik ve yapı skoru hesapla (0-7 points, negatif olabilir)
     * BİN MODÜL PROBLEMİ: URL yapısına göre SEO değeri belirleme
     */
    private static function calculateDepthScore($record): float
    {
        if (!$record) return 0.0;
        
        try {
            $slug = self::extractStringFromMultiLang($record->slug ?? '');
            if (empty($slug)) return 0.0;
            
            $depthScore = 7.0; // Başlangıç puanı (maksimum)
            
            // 1. URL derinlik analizi
            $depth = substr_count($slug, '/');
            $depthPenalty = match(true) {
                $depth <= 1 => 0.0,      // Kök seviye = ceza yok
                $depth == 2 => -1.0,     // 2. seviye = -1
                $depth == 3 => -2.5,     // 3. seviye = -2.5
                $depth == 4 => -4.0,     // 4. seviye = -4
                $depth >= 5 => -5.0,     // 5+ seviye = -5
                default => 0.0
            };
            
            // 2. URL uzunluğu analizi
            $urlLength = mb_strlen($slug);
            $lengthPenalty = match(true) {
                $urlLength <= 50 => 0.0,    // Kısa URL = bonus
                $urlLength <= 100 => -0.5,  // Orta URL = az ceza
                $urlLength <= 150 => -1.0,  // Uzun URL = orta ceza
                $urlLength > 150 => -2.0,   // Çok uzun URL = yüksek ceza
                default => 0.0
            };
            
            // 3. URL kalitesi analizi
            $qualityBonus = 0.0;
            
            // SEO dostu karakterler (sadece harf, rakam, tire)
            if (preg_match('/^[a-z0-9\-\/]+$/', $slug)) {
                $qualityBonus += 1.0; // SEO dostu URL = +1
            }
            
            // Anlamlı kelime sayısı
            $words = explode('-', str_replace('/', '-', $slug));
            $meaningfulWords = array_filter($words, function($word) {
                return mb_strlen($word) >= 3; // 3+ karakter anlamlı kelime
            });
            
            $wordCountBonus = match(true) {
                count($meaningfulWords) >= 4 => 1.0,  // Çok kelimeli = +1
                count($meaningfulWords) >= 2 => 0.5,  // Orta kelimeli = +0.5
                count($meaningfulWords) == 1 => 0.0,  // Tek kelime = neutral
                default => -0.5                        // Anlamsız = -0.5
            };
            
            // 4. Homepage bonus
            if ($slug === '/' || $slug === '' || 
                (method_exists($record, 'is_homepage') && $record->is_homepage)) {
                $qualityBonus += 2.0; // Homepage = +2 bonus
            }
            
            $totalScore = $depthScore + $depthPenalty + $lengthPenalty + $qualityBonus + $wordCountBonus;
            
        } catch (\Exception $e) {
            return 0.0;
        }
        
        return round(max(0.0, min(7.0, $totalScore)), 1);
    }

    /**
     * Shop içeriklerini ekle (Ürünler ve Kategoriler)
     */
    private static function addShopContent(Sitemap $sitemap, array $languages, string $defaultLanguage): void
    {
        try {
            // Shop ürünlerini ekle
            $shopProducts = \Modules\Shop\App\Models\ShopProduct::where('is_active', true)->get();

            foreach ($shopProducts as $product) {
                foreach ($languages as $language) {
                    $languageCode = is_object($language) ? $language->code : $language['code'];
                    $slug = $product->getTranslated('slug', $languageCode);

                    if (empty($slug)) {
                        continue;
                    }

                    // URL pattern: /shop/product/{slug} veya /{locale}/shop/product/{slug}
                    $baseUrl = $languageCode !== $defaultLanguage ? '/' . $languageCode : '';
                    $url = $baseUrl . '/shop/product/' . $slug;

                    $sitemap->add(
                        Url::create($url)
                            ->setLastModificationDate($product->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(self::getModulePriority('ShopProduct', $product))
                    );
                }
            }

            // Shop kategorilerini ekle
            $shopCategories = \Modules\Shop\App\Models\ShopCategory::where('is_active', true)->get();

            foreach ($shopCategories as $category) {
                foreach ($languages as $language) {
                    $languageCode = is_object($language) ? $language->code : $language['code'];
                    $slug = $category->getTranslated('slug', $languageCode);

                    if (empty($slug)) {
                        continue;
                    }

                    // URL pattern: /shop/category/{slug}
                    $baseUrl = $languageCode !== $defaultLanguage ? '/' . $languageCode : '';
                    $url = $baseUrl . '/shop/category/' . $slug;

                    $sitemap->add(
                        Url::create($url)
                            ->setLastModificationDate($category->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                }
            }
        } catch (\Exception $e) {
            // Shop modülü yoksa veya hata varsa skip
            \Log::warning('Shop sitemap generation failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Blog içeriklerini ekle (Yazılar ve Kategoriler)
     */
    private static function addBlogContent(Sitemap $sitemap, array $languages, string $defaultLanguage): void
    {
        try {
            // Blog yazılarını ekle
            $blogs = \Modules\Blog\App\Models\Blog::where('is_active', true)->get();

            foreach ($blogs as $blog) {
                foreach ($languages as $language) {
                    $languageCode = is_object($language) ? $language->code : $language['code'];
                    $slug = $blog->getTranslated('slug', $languageCode);

                    if (empty($slug)) {
                        continue;
                    }

                    // URL pattern: /blog/{slug}
                    $baseUrl = $languageCode !== $defaultLanguage ? '/' . $languageCode : '';
                    $url = $baseUrl . '/blog/' . $slug;

                    $sitemap->add(
                        Url::create($url)
                            ->setLastModificationDate($blog->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                            ->setPriority(self::getModulePriority('Blog', $blog))
                    );
                }
            }

            // Blog kategorilerini ekle
            $blogCategories = \Modules\Blog\App\Models\BlogCategory::where('is_active', true)->get();

            foreach ($blogCategories as $category) {
                foreach ($languages as $language) {
                    $languageCode = is_object($language) ? $language->code : $language['code'];
                    $slug = $category->getTranslated('slug', $languageCode);

                    if (empty($slug)) {
                        continue;
                    }

                    // URL pattern: /blog/category/{slug}
                    $baseUrl = $languageCode !== $defaultLanguage ? '/' . $languageCode : '';
                    $url = $baseUrl . '/blog/category/' . $slug;

                    $sitemap->add(
                        Url::create($url)
                            ->setLastModificationDate($category->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                            ->setPriority(0.6)
                    );
                }
            }
        } catch (\Exception $e) {
            // Blog modülü yoksa veya hata varsa skip
            \Log::warning('Blog sitemap generation failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Portfolio kategorilerini ekle
     */
    private static function addPortfolioCategoryContent(Sitemap $sitemap, array $languages, string $defaultLanguage): void
    {
        try {
            $portfolioCategories = \Modules\Portfolio\App\Models\PortfolioCategory::where('is_active', true)->get();

            foreach ($portfolioCategories as $category) {
                foreach ($languages as $language) {
                    $languageCode = is_object($language) ? $language->code : $language['code'];
                    $slug = $category->getTranslated('slug', $languageCode);

                    if (empty($slug)) {
                        continue;
                    }

                    // URL pattern: /portfolio/category/{slug}
                    $baseUrl = $languageCode !== $defaultLanguage ? '/' . $languageCode : '';
                    $url = $baseUrl . '/portfolio/category/' . $slug;

                    $sitemap->add(
                        Url::create($url)
                            ->setLastModificationDate($category->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                            ->setPriority(0.6)
                    );
                }
            }
        } catch (\Exception $e) {
            // Portfolio modülü yoksa veya hata varsa skip
            \Log::warning('Portfolio category sitemap generation failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Sitemap'i dosyaya kaydet
     */
    public static function generateAndSave(): string
    {
        $sitemap = self::generate();
        $filename = 'sitemap.xml';
        $path = public_path($filename);

        $sitemap->writeToFile($path);

        return $filename;
    }
}