<?php

namespace App\Services;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Modules\Page\App\Models\Page;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\Announcement\App\Models\Announcement;

class TenantSitemapService
{
    /**
     * Tenant için sitemap oluştur
     */
    public static function generate(): Sitemap
    {
        $sitemap = Sitemap::create();

        // Ana sayfa
        $sitemap->add(
            Url::create('/')
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        // Sayfalar (Page modülü)
        self::addPages($sitemap);

        // Portfolio
        self::addPortfolio($sitemap);

        // Announcements
        self::addAnnouncements($sitemap);

        return $sitemap;
    }

    /**
     * Page modülü sayfalarını ekle
     */
    private static function addPages(Sitemap $sitemap): void
    {
        try {
            $pages = Page::where('is_active', true)->get();
            
            foreach ($pages as $page) {
                $sitemap->add(
                    Url::create('/' . $page->slug)
                        ->setLastModificationDate($page->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8)
                );
            }
        } catch (\Exception $e) {
            // Page modülü yoksa skip
        }
    }

    /**
     * Portfolio sayfalarını ekle
     */
    private static function addPortfolio(Sitemap $sitemap): void
    {
        try {
            // Portfolio kategorileri
            $categories = PortfolioCategory::where('is_active', true)->get();
            foreach ($categories as $category) {
                $sitemap->add(
                    Url::create('/portfolio/' . $category->slug)
                        ->setLastModificationDate($category->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7)
                );
            }

            // Portfolio detay sayfaları
            $portfolios = Portfolio::where('is_active', true)->with('category')->get();
            foreach ($portfolios as $portfolio) {
                $sitemap->add(
                    Url::create('/portfolio/' . $portfolio->category->slug . '/' . $portfolio->slug)
                        ->setLastModificationDate($portfolio->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.6)
                );
            }
        } catch (\Exception $e) {
            // Portfolio modülü yoksa skip
        }
    }

    /**
     * Announcement sayfalarını ekle
     */
    private static function addAnnouncements(Sitemap $sitemap): void
    {
        try {
            $announcements = Announcement::where('is_active', true)->get();
            
            foreach ($announcements as $announcement) {
                $sitemap->add(
                    Url::create('/announcements/' . $announcement->slug)
                        ->setLastModificationDate($announcement->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.5)
                );
            }
        } catch (\Exception $e) {
            // Announcement modülü yoksa skip
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