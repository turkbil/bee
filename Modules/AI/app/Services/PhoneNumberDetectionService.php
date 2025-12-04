<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

/**
 * Phone Number Detection Service
 *
 * Türk telefon numaralarını tespit eder
 * Format: 0555 123 4567, +90 555 123 4567, 05551234567, 90 555 123 4567
 */
class PhoneNumberDetectionService
{
    /**
     * Türk telefon numarası regex pattern'leri
     * ULTRA ESNEk: Yanyana 8-12 rakam, boşluk/tire/nokta/parantez her şey kabul!
     * Hatalı, eksik, fazla rakam - HER ŞEYİ YAKALA!
     */
    private const PHONE_PATTERNS = [
        // +90 ile başlayan (10-13 haneli, ayırıcılarla beraber)
        // Örnekler: +905551234567, +90 555 123 4567, +90-555-123-4567
        '/\+90[\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9]?[\s\.\-\(\)\/]*[0-9]?[\s\.\-\(\)\/]*[0-9]?/',

        // 0 ile başlayan (10-12 haneli, ayırıcılarla beraber)
        // Örnekler: 05551234567, 0555 123 4567, 0555-123-4567, (0555) 123 45 67
        '/(?<!\d)0[\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9]?[\s\.\-\(\)\/]*[0-9]?/',

        // 90 ile başlayan (ülke kodu, + olmadan)
        // Örnekler: 905551234567, 90 555 123 4567
        '/(?<!\d)90[\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9]?[\s\.\-\(\)\/]*[0-9]?/',

        // 5XX ile başlayan (10 haneli, 0'sız - ayırıcılarla beraber)
        // Örnekler: 5382640840, 538 264 0840, 538-264-0840, (538) 264 08 40
        '/(?<!\d)[5][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9][\s\.\-\(\)\/]*[0-9]?[\s\.\-\(\)\/]*[0-9]?(?!\d)/',

        // Yanyana 10-11 rakam (0 veya 5 ile başlayan, boşluksuz)
        // Örnekler: 5382640840, 05382640840
        '/(?<!\d)[05][0-9]{9,10}(?!\d)/',

        // Yanyana 8-9 rakam (hatalı ama algılansın)
        // Örnekler: 82640840 (başındaki 53 eksik)
        '/(?<!\d)[0-9]{8,9}(?!\d)/',
    ];

    /**
     * Metinde telefon numarası var mı kontrol et
     */
    public function hasPhoneNumber(string $text): bool
    {
        foreach (self::PHONE_PATTERNS as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Metindeki tüm telefon numaralarını bul
     */
    public function extractPhoneNumbers(string $text): array
    {
        $phoneNumbers = [];

        foreach (self::PHONE_PATTERNS as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[0] as $match) {
                    $phoneNumbers[] = $this->normalizePhoneNumber($match);
                }
            }
        }

        return array_unique($phoneNumbers);
    }

    /**
     * İlk bulunan telefon numarasını al
     */
    public function extractFirstPhoneNumber(string $text): ?string
    {
        $numbers = $this->extractPhoneNumbers($text);

        return !empty($numbers) ? $numbers[0] : null;
    }

    /**
     * Telefon numarasını normalize et (temizle ve standart format)
     * HATA TOLERANSLI: Eksik 0, fazla rakam, yanlış format - her şeyi düzeltir!
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // Boşlukları ve özel karakterleri temizle (sadece rakam ve + kalsın)
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // +90 ile başlayanları 0 ile değiştir
        if (str_starts_with($phone, '+90')) {
            $phone = '0' . substr($phone, 3);
        } elseif (str_starts_with($phone, '90') && strlen($phone) >= 12) {
            // 90 ile başlayan (ülke kodu var ama + yok)
            $phone = '0' . substr($phone, 2);
        }

        // 10 haneli ise başına 0 ekle (5382640840 → 05382640840)
        if (strlen($phone) === 10 && str_starts_with($phone, '5')) {
            $phone = '0' . $phone;
        }

        // 9 haneli ise (hatalı) başına 05 ekle (382640840 → 05382640840)
        if (strlen($phone) === 9 && !str_starts_with($phone, '0')) {
            $phone = '05' . $phone;
        }

        // 8 haneli ise (çok hatalı) başına 053 ekle
        if (strlen($phone) === 8) {
            $phone = '053' . $phone;
        }

        // 12+ haneli ise (fazla rakam) son 11 karakteri al
        if (strlen($phone) > 11) {
            $phone = substr($phone, -11);
        }

        // 7 haneli ve daha kısa ise (çok eksik) olduğu gibi dön (algılanabilir olsun)
        // Final check: 11 haneli ve 0 ile başlıyorsa OK
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            return $phone;
        }

        // 10-11 haneli değilse bile dön (log için)
        return $phone;
    }

    /**
     * Telefon numarasını formatla (görüntüleme için)
     * 05551234567 -> 0555 123 4567
     */
    public function formatPhoneNumber(string $phone): string
    {
        $normalized = $this->normalizePhoneNumber($phone);

        if (strlen($normalized) === 11 && str_starts_with($normalized, '0')) {
            return substr($normalized, 0, 4) . ' '
                 . substr($normalized, 4, 3) . ' '
                 . substr($normalized, 7, 2) . ' '
                 . substr($normalized, 9, 2);
        }

        return $normalized;
    }
}
