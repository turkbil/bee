<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CorsHelper
{
    /**
     * Get all allowed origins for CORS
     *
     * @return array
     */
    public static function getAllowedOrigins(): array
    {
        return Cache::remember('cors_allowed_origins', 3600, function () {
            try {
                // Domains tablosundan tüm domain'leri çek
                $domains = DB::connection('central')
                    ->table('domains')
                    ->pluck('domain')
                    ->toArray();

                // Her domain için https:// ve http:// ekle
                $origins = [];
                foreach ($domains as $domain) {
                    $origins[] = 'https://' . $domain;
                    $origins[] = 'http://' . $domain;
                }

                // Localhost ekle (development için)
                $origins[] = 'http://localhost';
                $origins[] = 'http://localhost:3000';
                $origins[] = 'http://127.0.0.1';
                $origins[] = 'http://127.0.0.1:8000';

                return array_unique($origins);

            } catch (\Exception $e) {
                \Log::error('❌ CORS: getAllowedOrigins failed', [
                    'error' => $e->getMessage()
                ]);

                // Fallback: En azından temel domain'leri döndür
                return [
                    'https://tuufi.com',
                    'https://ixtif.com',
                    'https://muzibu.com',
                    'http://localhost',
                ];
            }
        });
    }

    /**
     * Clear CORS cache
     *
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget('cors_allowed_origins');
    }
}
