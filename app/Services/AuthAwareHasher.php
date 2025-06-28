<?php

namespace App\Services;

use Spatie\ResponseCache\Hasher\DefaultHasher;
use Illuminate\Http\Request;

class AuthAwareHasher extends DefaultHasher
{
    public function getHashFor(Request $request): string
    {
        $baseHash = parent::getHashFor($request);
        
        // Auth durumu + dil durumunu hash'e ekle
        $authStatus = auth()->check() ? 'auth_' . auth()->id() : 'guest';
        $locale = app()->getLocale();
        $siteLocale = session('site_locale', $locale);
        
        // Final hash: base + auth + locale
        $finalHash = $baseHash . '_' . $authStatus . '_' . $siteLocale;
        
        // Debug için sadece önemli durumlarda log
        if (request()->has('debug_cache')) {
            \Log::info('🔍 AUTH+LOCALE AWARE HASHER', [
                'url' => $request->fullUrl(),
                'auth_status' => $authStatus,
                'locale' => $siteLocale,
                'final_hash' => $finalHash
            ]);
        }
        
        return $finalHash;
    }
}