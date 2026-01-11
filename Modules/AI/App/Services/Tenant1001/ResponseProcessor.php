<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Tenant1001;

use Illuminate\Support\Facades\Log;

/**
 * Tenant 1001 (muzibu.com.tr) AI Response Processor
 *
 * AI yanıtlarını post-processing ile düzeltir:
 * - Fiyat hataları (4000 → 400 gibi)
 * - Playlist isimleri (generic → akıllı)
 *
 * @package Modules\AI\App\Services\Tenant1001
 * @version 1.0
 */
class ResponseProcessor
{
    /**
     * AI yanıtını düzelt
     *
     * @param string $response AI'dan gelen yanıt
     * @param string $userMessage Kullanıcının mesajı
     * @return string Düzeltilmiş yanıt
     */
    public static function process(string $response, string $userMessage): string
    {
        $originalResponse = $response;

        // ═══════════════════════════════════════════════════════════════
        // 1️⃣ FİYAT DÜZELTMELERİ
        // ═══════════════════════════════════════════════════════════════
        // AI "4000 TRY" yerine "400 TRY" yazıyor, düzelt
        // Yıllık paket: 4000 TRY (KDV Hariç), 4800 TRY (KDV Dahil)
        // Aylık paket: 600 TRY (KDV Hariç), 720 TRY (KDV Dahil)

        // Yıllık fiyat düzeltmeleri (10x)
        $response = preg_replace('/\b400\s*TRY\b/i', '4000 TRY', $response);
        $response = preg_replace('/\b480\s*TRY\b/i', '4800 TRY', $response);
        $response = preg_replace('/\b400\s*TL\b/i', '4000 TL', $response);
        $response = preg_replace('/\b480\s*TL\b/i', '4800 TL', $response);

        // Aylık fiyat düzeltmeleri (olası hatalar)
        $response = preg_replace('/\b60\s*TRY\b/i', '600 TRY', $response);
        $response = preg_replace('/\b72\s*TRY\b/i', '720 TRY', $response);
        $response = preg_replace('/\b60\s*TL\b/i', '600 TL', $response);
        $response = preg_replace('/\b72\s*TL\b/i', '720 TL', $response);

        // ═══════════════════════════════════════════════════════════════
        // 2️⃣ PLAYLIST İSİM DÜZELTMELERİ
        // ═══════════════════════════════════════════════════════════════
        $response = self::fixPlaylistName($response, $userMessage);

        // Log if any changes were made
        if ($response !== $originalResponse) {
            Log::info("🔧 Tenant1001 ResponseProcessor: AI yanıtı düzeltildi");
        }

        return $response;
    }

    /**
     * Playlist ismini düzelt
     *
     * @param string $response AI yanıtı
     * @param string $userMessage Kullanıcı mesajı
     * @return string Düzeltilmiş yanıt
     */
    private static function fixPlaylistName(string $response, string $userMessage): string
    {
        // ACTION tag'deki playlist ismini düzelt
        if (preg_match('/\[ACTION:CREATE_PLAYLIST:[^\]]*title=([^\]\|]+)/i', $response, $matches)) {
            $playlistTitle = trim($matches[1]);

            // Generic isim mi kontrol et
            $genericNames = [
                'Özel Playlist',
                'Ozel Playlist',
                'Müzik Listesi',
                'Muzik Listesi',
                'Playlist',
                'Şarkılar',
                'Sarkilar',
                'Sizin İçin',
                'Sizin Icin',
                'Karışık Playlist',
                'Karisik Playlist',
            ];

            $isGeneric = false;
            foreach ($genericNames as $genericName) {
                if (stripos($playlistTitle, $genericName) !== false) {
                    $isGeneric = true;
                    break;
                }
            }

            if ($isGeneric) {
                // Kullanıcı mesajından akıllı isim oluştur
                $newTitle = self::generateSmartPlaylistName($userMessage);

                // Eski ismi yenisiyle değiştir
                $response = str_replace(
                    "title={$playlistTitle}",
                    "title={$newTitle}",
                    $response
                );

                // Markdown'daki playlist başlığını da düzelt
                $response = str_ireplace($playlistTitle, $newTitle, $response);

                Log::info("🎵 Playlist ismi düzeltildi", [
                    'old' => $playlistTitle,
                    'new' => $newTitle,
                ]);
            }
        }

        return $response;
    }

    /**
     * Kullanıcı mesajından akıllı playlist ismi oluştur
     *
     * @param string $userMessage Kullanıcının mesajı
     * @return string Playlist ismi
     */
    private static function generateSmartPlaylistName(string $userMessage): string
    {
        $userMessage = mb_strtolower($userMessage);

        // Anahtar kelimelerden isim oluştur
        $keywords = [
            'motivasyon' => 'Motivasyon Şarkıları',
            'motive' => 'Motivasyon Şarkıları',
            'enerji' => 'Enerjik Mix',
            'enerjik' => 'Enerjik Mix',
            'romantik' => 'Romantik Anlar',
            'aşk' => 'Aşk Şarkıları',
            'hüzün' => 'Hüzünlü Anlar',
            'üzgün' => 'Hüzünlü Anlar',
            'neşeli' => 'Neşeli Şarkılar',
            'mutlu' => 'Mutluluk Playlist',
            'sakin' => 'Sakin Melodiler',
            'rahatlatıcı' => 'Rahatlatıcı Sesler',
            'çalışma' => 'Çalışırken Dinle',
            'konsantrasyon' => 'Konsantrasyon Mix',
            'spor' => 'Spor Motivasyonu',
            'egzersiz' => 'Egzersiz Şarkıları',
            'parti' => 'Parti Mix',
            'dans' => 'Dans Şarkıları',
            'pop' => 'Pop Hits',
            'rock' => 'Rock Klasikleri',
            'arabesk' => 'Arabesk Klasikleri',
            'türkçe' => 'Türkçe Favori',
            'yabancı' => 'Yabancı Hits',
            'nostalji' => 'Nostalji Playlist',
            'eski' => 'Nostaljik Anlar',
            '90' => '90lar Nostaljisi',
            '80' => '80ler Klasikleri',
            '2000' => '2000ler Hitleri',
            'sabah' => 'Günaydın Playlist',
            'gece' => 'Gece Şarkıları',
            'akşam' => 'Akşam Keyfi',
            'yaz' => 'Yaz Şarkıları',
            'kış' => 'Kış Melodileri',
        ];

        foreach ($keywords as $keyword => $name) {
            if (mb_strpos($userMessage, $keyword) !== false) {
                return $name . ' | Muzibu AI';
            }
        }

        // Varsayılan: Premium Mix
        return 'Premium Mix | Muzibu AI';
    }
}
