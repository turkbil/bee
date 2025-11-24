<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * IndexNow Service
 *
 * Arama motorlarına URL değişikliklerini anında bildirir
 * Destekleyen: Bing, Yandex, Seznam, Naver
 * Google: Desteklemiyor (robots.txt + sitemap ile çalışır)
 *
 * @see https://www.indexnow.org/documentation
 */
class IndexNowService
{
    /**
     * IndexNow API endpoint'leri
     */
    private const ENDPOINTS = [
        'bing' => 'https://www.bing.com/indexnow',
        'yandex' => 'https://yandex.com/indexnow',
        // 'indexnow' => 'https://api.indexnow.org/indexnow', // Alternatif
    ];

    /**
     * Rate limiting: Dakikada max 100 URL
     */
    private const RATE_LIMIT = 100;
    private const RATE_LIMIT_WINDOW = 60; // saniye

    /**
     * Tek URL submit et
     */
    public static function submitUrl(string $url): bool
    {
        return self::submitUrls([$url]);
    }

    /**
     * Birden fazla URL submit et (batch)
     */
    public static function submitUrls(array $urls): bool
    {
        if (empty($urls)) {
            return false;
        }

        // Tenant domain'ini al
        $host = self::getTenantHost();
        if (!$host) {
            Log::warning('IndexNow: Tenant host bulunamadı');
            return false;
        }

        // IndexNow key'i al veya oluştur
        $key = self::getOrCreateKey($host);
        if (!$key) {
            Log::warning('IndexNow: Key oluşturulamadı');
            return false;
        }

        // Rate limiting kontrolü
        if (!self::checkRateLimit($host, count($urls))) {
            Log::warning('IndexNow: Rate limit aşıldı', ['host' => $host, 'urls' => count($urls)]);
            return false;
        }

        // URL'leri temizle ve validate et
        $validUrls = self::validateUrls($urls, $host);
        if (empty($validUrls)) {
            return false;
        }

        $success = true;

        // Her endpoint'e gönder (birinde başarılı olması yeterli)
        foreach (self::ENDPOINTS as $name => $endpoint) {
            try {
                $response = Http::timeout(10)
                    ->post($endpoint, [
                        'host' => $host,
                        'key' => $key,
                        'urlList' => $validUrls,
                    ]);

                if ($response->successful() || $response->status() === 200 || $response->status() === 202) {
                    Log::info("IndexNow: {$name} başarılı", [
                        'host' => $host,
                        'urls' => count($validUrls),
                        'status' => $response->status()
                    ]);
                    // Bir endpoint başarılı olduysa diğerlerine gerek yok
                    break;
                } else {
                    Log::warning("IndexNow: {$name} başarısız", [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("IndexNow: {$name} hata", ['error' => $e->getMessage()]);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Model için URL submit et
     */
    public static function submitModel($model): bool
    {
        if (!method_exists($model, 'getUrl')) {
            Log::warning('IndexNow: Model getUrl() metodu yok', ['model' => get_class($model)]);
            return false;
        }

        $urls = [];

        // Çok dilli ise tüm dillerde URL'leri al
        if (method_exists($model, 'getTranslated')) {
            try {
                $languages = \App\Services\TenantLanguageProvider::getActiveLanguages();
                foreach ($languages as $language) {
                    $locale = is_object($language) ? $language->code : $language['code'];
                    $url = $model->getUrl($locale);
                    if ($url) {
                        $urls[] = $url;
                    }
                }
            } catch (\Exception $e) {
                // Fallback: sadece current locale
                $urls[] = $model->getUrl();
            }
        } else {
            $urls[] = $model->getUrl();
        }

        return self::submitUrls(array_unique($urls));
    }

    /**
     * Tenant host'unu al
     */
    private static function getTenantHost(): ?string
    {
        try {
            if (function_exists('tenant') && tenant()) {
                // Tenant domain'lerinden ilkini al
                $domains = tenant()->domains ?? [];
                if (!empty($domains)) {
                    $domain = is_object($domains[0]) ? $domains[0]->domain : $domains[0];
                    return parse_url("https://{$domain}", PHP_URL_HOST) ?? $domain;
                }
            }

            // Fallback: request host
            return request()->getHost();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * IndexNow key'i al veya oluştur
     */
    private static function getOrCreateKey(string $host): ?string
    {
        $cacheKey = "indexnow_key_{$host}";

        // Cache'den al
        $key = Cache::get($cacheKey);
        if ($key) {
            return $key;
        }

        // Yeni key oluştur (32 karakter hex)
        $key = bin2hex(random_bytes(16));

        // Cache'e kaydet (1 yıl)
        Cache::put($cacheKey, $key, now()->addYear());

        // Key dosyasını oluştur (IndexNow doğrulaması için)
        self::createKeyFile($key);

        Log::info('IndexNow: Yeni key oluşturuldu', ['host' => $host]);

        return $key;
    }

    /**
     * Key dosyasını public dizine oluştur
     */
    private static function createKeyFile(string $key): void
    {
        try {
            $filePath = public_path("{$key}.txt");
            file_put_contents($filePath, $key);

            // Permission düzelt
            @chmod($filePath, 0644);
            @chown($filePath, 'tuufi.com_');
        } catch (\Exception $e) {
            Log::error('IndexNow: Key dosyası oluşturulamadı', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Rate limiting kontrolü
     */
    private static function checkRateLimit(string $host, int $urlCount): bool
    {
        $cacheKey = "indexnow_rate_{$host}";
        $current = Cache::get($cacheKey, 0);

        if ($current + $urlCount > self::RATE_LIMIT) {
            return false;
        }

        Cache::put($cacheKey, $current + $urlCount, self::RATE_LIMIT_WINDOW);
        return true;
    }

    /**
     * URL'leri validate et
     */
    private static function validateUrls(array $urls, string $host): array
    {
        $valid = [];

        foreach ($urls as $url) {
            // URL formatını kontrol et
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            // Host eşleşmesi kontrol et
            $urlHost = parse_url($url, PHP_URL_HOST);
            if ($urlHost !== $host && !str_ends_with($urlHost, ".{$host}")) {
                continue;
            }

            $valid[] = $url;
        }

        return array_unique($valid);
    }

    /**
     * Sitemap'teki tüm URL'leri submit et (günlük cron için)
     */
    public static function submitSitemap(): bool
    {
        try {
            $sitemap = TenantSitemapService::generate();
            $urls = [];

            // Sitemap'ten URL'leri çıkar
            foreach ($sitemap->getTags() as $tag) {
                if (method_exists($tag, 'url')) {
                    $urls[] = $tag->url;
                }
            }

            // Batch'ler halinde gönder (max 10,000 URL/gün)
            $chunks = array_chunk($urls, 100);
            $success = true;

            foreach ($chunks as $chunk) {
                if (!self::submitUrls($chunk)) {
                    $success = false;
                }
                // Rate limiting için bekle
                usleep(100000); // 0.1 saniye
            }

            Log::info('IndexNow: Sitemap submit tamamlandı', ['total_urls' => count($urls)]);
            return $success;

        } catch (\Exception $e) {
            Log::error('IndexNow: Sitemap submit hatası', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
