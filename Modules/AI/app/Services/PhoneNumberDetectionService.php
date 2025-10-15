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
     */
    private const PHONE_PATTERNS = [
        // +90 555 123 4567 veya +90 555 123 45 67
        '/\+90\s?[0-9]{3}\s?[0-9]{3}\s?[0-9]{2}\s?[0-9]{2}/',

        // 0555 123 4567 veya 0555 123 45 67
        '/0[0-9]{3}\s?[0-9]{3}\s?[0-9]{2}\s?[0-9]{2}/',

        // 90 555 123 4567
        '/90\s?[0-9]{3}\s?[0-9]{3}\s?[0-9]{2}\s?[0-9]{2}/',

        // 05551234567 (boşluksuz)
        '/0[0-9]{10}/',

        // +905551234567 (boşluksuz)
        '/\+90[0-9]{10}/',
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
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // Boşlukları ve özel karakterleri temizle
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // +90 ile başlayanları 0 ile değiştir
        if (str_starts_with($phone, '+90')) {
            $phone = '0' . substr($phone, 3);
        } elseif (str_starts_with($phone, '90') && strlen($phone) === 12) {
            $phone = '0' . substr($phone, 2);
        }

        // 05551234567 formatına çevir (11 haneli)
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            return $phone;
        }

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
