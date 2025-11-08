<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Tracking Script Proxy Controller
 * AdBlock bypass için tracking script'lerini kendi sunucumuzdan serve eder
 */
class TrackingProxyController extends Controller
{
    /**
     * Facebook Pixel script proxy
     * /analytics/fb.js
     */
    public function facebookPixel()
    {
        $script = Cache::remember('fb_pixel_script', 3600, function () {
            try {
                $response = Http::timeout(10)->get('https://connect.facebook.net/en_US/fbevents.js');

                if ($response->successful()) {
                    return $response->body();
                }

                return '// Facebook Pixel script could not be loaded';
            } catch (\Exception $e) {
                \Log::warning('Facebook Pixel proxy failed: ' . $e->getMessage());
                return '// Facebook Pixel script could not be loaded';
            }
        });

        return response($script)
            ->header('Content-Type', 'application/javascript; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('X-Proxy-Source', 'ixtif-tracking-proxy');
    }

    /**
     * Google Analytics / GTM script proxy
     * /analytics/ga.js
     */
    public function googleAnalytics(Request $request)
    {
        $id = $request->input('id', 'GTM-P8HKHCG9');

        $script = Cache::remember('ga_script_' . $id, 3600, function () use ($id) {
            try {
                $response = Http::timeout(10)->get("https://www.googletagmanager.com/gtm.js?id={$id}");

                if ($response->successful()) {
                    return $response->body();
                }

                return '// Google Tag Manager script could not be loaded';
            } catch (\Exception $e) {
                \Log::warning('Google Analytics proxy failed: ' . $e->getMessage());
                return '// Google Tag Manager script could not be loaded';
            }
        });

        return response($script)
            ->header('Content-Type', 'application/javascript; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('X-Proxy-Source', 'ixtif-tracking-proxy');
    }

    /**
     * Generic script proxy
     * /analytics/proxy?url=https://...
     */
    public function proxy(Request $request)
    {
        $url = $request->input('url');

        if (empty($url)) {
            return response('// Missing URL parameter', 400)
                ->header('Content-Type', 'application/javascript');
        }

        // Sadece güvenilir domain'lere izin ver
        $allowedDomains = [
            'connect.facebook.net',
            'www.googletagmanager.com',
            'www.google-analytics.com',
            'analytics.google.com',
        ];

        $host = parse_url($url, PHP_URL_HOST);

        if (!in_array($host, $allowedDomains)) {
            return response('// Unauthorized domain', 403)
                ->header('Content-Type', 'application/javascript');
        }

        $cacheKey = 'tracking_proxy_' . md5($url);

        $script = Cache::remember($cacheKey, 3600, function () use ($url) {
            try {
                $response = Http::timeout(10)->get($url);

                if ($response->successful()) {
                    return $response->body();
                }

                return '// Script could not be loaded';
            } catch (\Exception $e) {
                \Log::warning('Tracking proxy failed for ' . $url . ': ' . $e->getMessage());
                return '// Script could not be loaded';
            }
        });

        return response($script)
            ->header('Content-Type', 'application/javascript; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('X-Proxy-Source', 'ixtif-tracking-proxy');
    }

    /**
     * Clear tracking proxy cache
     * /analytics/clear-cache
     */
    public function clearCache()
    {
        Cache::forget('fb_pixel_script');
        Cache::forget('ga_script_GTM-P8HKHCG9');

        return response()->json([
            'success' => true,
            'message' => 'Tracking proxy cache cleared'
        ]);
    }
}
