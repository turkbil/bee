<?php

namespace App\Http\Controllers;

use Modules\SeoManagement\App\Models\SeoSetting;

/**
 * Dynamic Robots.txt Generator
 *
 * Tenant-aware robots.txt generator
 * - Sitemap URL her tenant için dinamik
 * - AI crawler permissions database'den
 * - Admin/API paths otomatik disallow
 */
class RobotsController extends Controller
{
    /**
     * Generate dynamic robots.txt
     *
     * @return \Illuminate\Http\Response
     */
    public function generate()
    {
        $sitemapUrl = route('sitemap');
        $lines = [];

        // Base rules for all crawlers
        $lines[] = "User-agent: *";
        $lines[] = "Disallow: /admin/";
        $lines[] = "Disallow: /api/private/";
        $lines[] = "Disallow: /vendor/";
        $lines[] = "Disallow: /storage/logs/";
        $lines[] = "Allow: /";
        $lines[] = "";

        // AI Crawler permissions from Global SEO Settings
        // Global setting = seoable_id is null
        $globalSeo = SeoSetting::whereNull('seoable_id')
                              ->whereNull('seoable_type')
                              ->first();

        if ($globalSeo) {
            // GPTBot (ChatGPT crawler)
            if (!($globalSeo->allow_gptbot ?? true)) {
                $lines[] = "User-agent: GPTBot";
                $lines[] = "Disallow: /";
                $lines[] = "";
            }

            // Claude-Web (Anthropic crawler)
            if (!($globalSeo->allow_claudebot ?? true)) {
                $lines[] = "User-agent: Claude-Web";
                $lines[] = "Disallow: /";
                $lines[] = "";
            }

            // Google-Extended (Bard/Gemini crawler)
            if (!($globalSeo->allow_google_extended ?? true)) {
                $lines[] = "User-agent: Google-Extended";
                $lines[] = "Disallow: /";
                $lines[] = "";
            }

            // BingPreview (Bing AI crawler)
            if (!($globalSeo->allow_bingbot_ai ?? true)) {
                $lines[] = "User-agent: BingPreview";
                $lines[] = "Disallow: /";
                $lines[] = "";
            }
        }

        // Sitemap URL (Tenant-aware)
        $lines[] = "Sitemap: {$sitemapUrl}";

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'X-Robots-Tag' => 'noindex', // robots.txt kendisi indexlenmesin
        ]);
    }
}
