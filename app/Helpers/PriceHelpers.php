<?php

if (!function_exists('formatPrice')) {
    /**
     * Format price with currency symbol
     * - Hides .00 decimals (2.350,00 → 2.350)
     * - Shows other decimals (2.350,50 → 2.350,50)
     * - Uses currency symbols instead of codes (USD → $, EUR → €, etc.)
     *
     * @param float|null $price
     * @param string|null $currency
     * @return string
     */
    function formatPrice(?float $price, ?string $currency = 'TRY'): string
    {
        if ($price === null || $price <= 0) {
            return '';
        }

        // Ondalık kısmı kontrol et
        $hasDecimals = fmod($price, 1) !== 0.0;

        // Fiyatı formatla
        if ($hasDecimals) {
            // .50 gibi ondalık varsa göster
            $formattedPrice = number_format($price, 2, ',', '.');
        } else {
            // .00 ise gizle
            $formattedPrice = number_format($price, 0, ',', '.');
        }

        // Para birimi sembolü mapping
        $currencySymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'TRY' => '₺',
            'JPY' => '¥',
            'CNY' => '¥',
            'RUB' => '₽',
            'INR' => '₹',
            'BRL' => 'R$',
            'KRW' => '₩',
            'CHF' => 'CHF',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'NZD' => 'NZ$',
            'MXN' => 'MX$',
            'ZAR' => 'R',
            'SEK' => 'kr',
            'NOK' => 'kr',
            'DKK' => 'kr',
            'PLN' => 'zł',
            'AED' => 'د.إ',
            'SAR' => '﷼',
        ];

        $symbol = $currencySymbols[$currency] ?? $currency;

        return $formattedPrice . ' ' . $symbol;
    }
}

if (!function_exists('getCurrencySymbol')) {
    /**
     * Get currency symbol from currency code
     *
     * @param string|null $currency
     * @return string
     */
    function getCurrencySymbol(?string $currency = 'TRY'): string
    {
        $currencySymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'TRY' => '₺',
            'JPY' => '¥',
            'CNY' => '¥',
            'RUB' => '₽',
            'INR' => '₹',
            'BRL' => 'R$',
            'KRW' => '₩',
            'CHF' => 'CHF',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'NZD' => 'NZ$',
            'MXN' => 'MX$',
            'ZAR' => 'R',
            'SEK' => 'kr',
            'NOK' => 'kr',
            'DKK' => 'kr',
            'PLN' => 'zł',
            'AED' => 'د.إ',
            'SAR' => '﷼',
        ];

        return $currencySymbols[$currency] ?? $currency;
    }
}
