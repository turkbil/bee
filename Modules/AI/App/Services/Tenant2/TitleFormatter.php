<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Tenant2;

/**
 * Tenant 2 (ixtif.com) Title Formatter
 *
 * iXTİF ürün başlıklarını formatlar ve temizler.
 * Database'deki başlıklar özel formatlama gerektirebilir.
 *
 * Örnekler:
 * - "İXTİF EPT20-20ETC - 2. Ton..." → "İXTİF EPT20-20ETC - 2 Ton..."
 * - "Litef EPT15 - 1.5. Ton" → "Litef EPT15 - 1.5 Ton"
 *
 * @package Modules\AI\App\Services\Tenant2
 * @version 1.0
 */
class TitleFormatter
{
    /**
     * Ürün başlığını formatla
     *
     * Türkçe'de sayılarda nokta kullanılmaz:
     * - "2 ton" doğru
     * - "2. ton" yanlış
     *
     * @param string $title Ürün başlığı
     * @return string Formatlanmış başlık
     */
    public static function format(string $title): string
    {
        // Sayı + nokta + boşluk + Ton/ton formatını düzelt
        // Örnek: "2. Ton" → "2 Ton"
        $title = preg_replace('/(\d+)\.\s+(Ton|ton)/u', '$1 $2', $title);

        return $title;
    }

    /**
     * iXTİF ürün başlığı mı kontrol et
     *
     * @param string $title Başlık
     * @return bool
     */
    public static function isIxtifProduct(string $title): bool
    {
        return stripos($title, 'ixtif') !== false
            || stripos($title, 'İXTİF') !== false
            || stripos($title, 'ıxtıf') !== false;
    }

    /**
     * Litef ürün başlığı mı kontrol et
     *
     * @param string $title Başlık
     * @return bool
     */
    public static function isLitefProduct(string $title): bool
    {
        return stripos($title, 'litef') !== false
            || stripos($title, 'Litef') !== false
            || stripos($title, 'LITEF') !== false;
    }

    /**
     * Kapasite bilgisini çıkar (ton cinsinden)
     *
     * @param string $title Başlık
     * @return float|null Kapasite (ton) veya null
     */
    public static function extractCapacity(string $title): ?float
    {
        // "2 Ton", "1.5 Ton", "2000 kg" gibi formatları yakala
        if (preg_match('/(\d+(?:\.\d+)?)\s*(Ton|ton|TON)/u', $title, $matches)) {
            return (float) $matches[1];
        }

        // kg formatı: 2000 kg = 2 ton
        if (preg_match('/(\d+)\s*(kg|KG)/u', $title, $matches)) {
            return (float) $matches[1] / 1000;
        }

        return null;
    }

    /**
     * Ürün tipi çıkar (transpalet, forklift, reach truck vb.)
     *
     * @param string $title Başlık
     * @return string|null Ürün tipi
     */
    public static function extractProductType(string $title): ?string
    {
        $types = [
            'transpalet' => ['transpalet', 'palet', 'ept', 'manual pallet'],
            'forklift' => ['forklift', 'fork lift', 'cpd', 'diesel forklift'],
            'reach_truck' => ['reach truck', 'reach', 'reachtruck'],
            'stacker' => ['stacker', 'istif', 'istifleme'],
            'order_picker' => ['order picker', 'sipariş toplama'],
        ];

        $titleLower = mb_strtolower($title);

        foreach ($types as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (mb_strpos($titleLower, $keyword) !== false) {
                    return $type;
                }
            }
        }

        return null;
    }
}
