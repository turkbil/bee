<?php

namespace Modules\Studio\App\Helpers;

use Illuminate\Support\Facades\File;

class StudioHelper
{
    /**
     * Studio asset URL'si oluştur
     * 
     * @param string $path Asset yolu
     * @return string Asset URL'si
     */
    public static function asset(string $path): string
    {
        $publicPath = 'modules/studio/' . ltrim($path, '/');
        
        // Dosya varsa versiyonunu ekle (cache busting)
        if (File::exists(public_path($publicPath))) {
            return asset($publicPath) . '?v=' . filemtime(public_path($publicPath));
        }
        
        return asset($publicPath);
    }
    
    /**
     * Studio dosya yolu oluştur
     * 
     * @param string $path Dosya yolu
     * @return string Tam dosya yolu
     */
    public static function path(string $path): string
    {
        return module_path('Studio', ltrim($path, '/'));
    }
    
    /**
     * Tenant kontrolü
     * 
     * @return bool Tenant mi değil mi
     */
    public static function isTenant(): bool
    {
        return function_exists('tenant') && tenant() !== null;
    }
    
    /**
     * Tenant ID'sini döndür
     * 
     * @return string|null Tenant ID
     */
    public static function getTenantId(): ?string
    {
        if (self::isTenant()) {
            return tenant()->getTenantKey();
        }
        
        return null;
    }
    
    /**
     * önbellek anahtarı oluştur
     * 
     * @param string $key Anahtar
     * @return string Önbellek anahtarı
     */
    public static function cacheKey(string $key): string
    {
        $prefix = config('studio.cache.prefix', 'studio_');
        $tenantPrefix = self::isTenant() ? self::getTenantId() . '_' : 'central_';
        
        return "{$prefix}{$tenantPrefix}{$key}";
    }
    
    /**
     * HTML içeriğini temizle
     * 
     * @param string|null $html HTML içeriği
     * @return string Temizlenmiş HTML
     */
    public static function cleanHtml(?string $html): string
    {
        if (!$html) {
            return '';
        }
        
        // Tehlikeli etiketleri temizle
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        
        // URL'leri temizle
        $html = preg_replace('/\bon\w+\s*=\s*(["\']).*?\1/i', '', $html);
        $html = preg_replace('/\bhref\s*=\s*(["\'])javascript:.*?\1/i', 'href="javascript:void(0)"', $html);
        
        return $html;
    }
    
    /**
     * Body içeriğini çıkar
     * 
     * @param string|null $html HTML içeriği
     * @return string Body içeriği
     */
    public static function extractBodyContent(?string $html): string
    {
        if (!$html) {
            return '';
        }
        
        $bodyMatchRegex = '/<body[^>]*>([\s\S]*?)<\/body>/i';
        
        if (preg_match($bodyMatchRegex, $html, $matches)) {
            return trim($matches[1]);
        }
        
        return $html;
    }
}