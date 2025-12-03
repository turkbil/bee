<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Session middleware'in cache header'larını ezmesini engeller
 *
 * Laravel'in StartSession middleware'i otomatik olarak
 * "Cache-Control: private, must-revalidate" header'ı ekler.
 * Bu middleware, cache'lenebilir response'lar için bunu düzeltir.
 */
class FixResponseCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // JSON Response UTF-8 Sanitization
        $this->sanitizeJsonResponse($response);

        // Admin sayfaları zaten cache'lenmiyor, dokunma
        if ($request->is('admin/*') || $request->is('*/admin/*')) {
            return $response;
        }

        // ResponseCache tarafından cache'lenen response'larda header var mı kontrol et
        if ($response->headers->has('laravel-responsecache')) {
            // Cache'den gelmiş, PREFETCH-FRIENDLY header'lar
            $response->headers->set('Cache-Control', 'public, max-age=3600, immutable');
            $response->headers->remove('Pragma');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
            return $response;
        }

        // Eğer bu GET request ve başarılı response ise, cache'lenebilir
        if ($request->isMethod('get') && $response->isSuccessful() && !$request->ajax()) {
            // TenantCacheProfile kontrollerini uygula
            $cacheProfile = app(config('responsecache.cache_profile'));

            if ($cacheProfile->shouldCacheRequest($request) && $cacheProfile->shouldCacheResponse($response)) {
                // PREFETCH-FRIENDLY: Session/StartSession middleware override etmesin
                $response->headers->set('Cache-Control', 'public, max-age=3600, immutable');
                $response->headers->remove('Pragma');
                $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
            }
        }

        return $response;
    }

    /**
     * JSON response'lardaki bozuk UTF-8 karakterleri temizler
     *
     * @param Response $response
     * @return void
     */
    protected function sanitizeJsonResponse(Response $response): void
    {
        // Sadece JSON response'ları kontrol et
        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'application/json')) {
            return;
        }

        $content = $response->getContent();

        // Zaten temiz bir JSON mu kontrol et
        if (json_validate($content)) {
            return;
        }

        // UTF-8 temizliği yap
        $cleanContent = $this->cleanUtf8($content);

        // Temizlenmiş içeriği tekrar kontrol et
        if (json_validate($cleanContent)) {
            $response->setContent($cleanContent);
            return;
        }

        // JSON decode/encode ile ek temizlik
        $decoded = json_decode($cleanContent, true);
        if ($decoded !== null) {
            $response->setContent(json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }

    /**
     * String'deki bozuk UTF-8 karakterleri temizler
     *
     * @param string $string
     * @return string
     */
    protected function cleanUtf8(string $string): string
    {
        // Yöntem 1: mb_convert_encoding ile temizlik
        $clean = mb_convert_encoding($string, 'UTF-8', 'UTF-8');

        // Yöntem 2: iconv ile ek temizlik
        $clean = iconv('UTF-8', 'UTF-8//IGNORE', $clean);

        // Yöntem 3: Regex ile kontrol karakterlerini temizle
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $clean);

        return $clean;
    }
}
