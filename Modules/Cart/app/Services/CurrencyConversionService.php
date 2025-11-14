<?php

declare(strict_types=1);

namespace Modules\Cart\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Currency Conversion Service
 *
 * Universal service for converting product prices to base currency (TRY)
 * Works with any module that provides currency information
 */
class CurrencyConversionService
{
    /**
     * Base currency (default: TRY)
     */
    protected string $baseCurrency = 'TRY';

    /**
     * Cache duration for exchange rates (in seconds)
     */
    protected int $cacheDuration = 3600; // 1 hour

    /**
     * Convert amount from source currency to base currency (TRY)
     *
     * @param float $amount Amount to convert
     * @param string $fromCurrency Source currency code (USD, EUR, etc.)
     * @return float Converted amount in TRY
     */
    public function convertToBaseCurrency(float $amount, string $fromCurrency): float
    {
        // If already in base currency, no conversion needed
        if ($fromCurrency === $this->baseCurrency) {
            return $amount;
        }

        // Get exchange rate
        $rate = $this->getExchangeRate($fromCurrency);

        // Convert
        $convertedAmount = $amount * $rate;

        Log::info('Currency conversion', [
            'amount' => $amount,
            'from' => $fromCurrency,
            'to' => $this->baseCurrency,
            'rate' => $rate,
            'result' => $convertedAmount,
        ]);

        return round($convertedAmount, 2);
    }

    /**
     * Get exchange rate from cache or database
     *
     * @param string $currencyCode Currency code (USD, EUR, etc.)
     * @return float Exchange rate
     */
    protected function getExchangeRate(string $currencyCode): float
    {
        // Try cache first
        $cacheKey = "currency_rate_{$currencyCode}";

        $rate = Cache::remember($cacheKey, $this->cacheDuration, function () use ($currencyCode) {
            return $this->fetchExchangeRateFromDatabase($currencyCode);
        });

        return $rate;
    }

    /**
     * Fetch exchange rate from database
     *
     * @param string $currencyCode Currency code
     * @return float Exchange rate (defaults to 1.0 if not found)
     */
    protected function fetchExchangeRateFromDatabase(string $currencyCode): float
    {
        // Try to use ShopCurrency if available
        if (class_exists(\Modules\Shop\App\Models\ShopCurrency::class)) {
            $currency = \Modules\Shop\App\Models\ShopCurrency::where('code', $currencyCode)
                ->where('is_active', true)
                ->first();

            if ($currency && $currency->exchange_rate > 0) {
                return (float) $currency->exchange_rate;
            }
        }

        // Fallback rates (approximate, for safety)
        $fallbackRates = [
            'USD' => 30.0,
            'EUR' => 32.0,
            'GBP' => 38.0,
        ];

        $rate = $fallbackRates[$currencyCode] ?? 1.0;

        Log::warning('Using fallback exchange rate', [
            'currency' => $currencyCode,
            'rate' => $rate,
        ]);

        return $rate;
    }

    /**
     * Clear currency rate cache
     *
     * @param string|null $currencyCode Specific currency or all
     */
    public function clearCache(?string $currencyCode = null): void
    {
        if ($currencyCode) {
            Cache::forget("currency_rate_{$currencyCode}");
        } else {
            // Clear all currency caches
            $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CHF'];
            foreach ($currencies as $code) {
                Cache::forget("currency_rate_{$code}");
            }
        }

        Log::info('Currency cache cleared', ['currency' => $currencyCode ?? 'all']);
    }

    /**
     * Get current exchange rate (for display purposes)
     *
     * @param string $currencyCode Currency code
     * @return float Current rate
     */
    public function getCurrentRate(string $currencyCode): float
    {
        return $this->getExchangeRate($currencyCode);
    }

    /**
     * Convert amount with metadata (for detailed tracking)
     *
     * @param float $amount Original amount
     * @param string $fromCurrency Source currency
     * @return array ['converted_amount', 'original_amount', 'rate', 'from_currency', 'to_currency']
     */
    public function convertWithMetadata(float $amount, string $fromCurrency): array
    {
        $rate = $this->getExchangeRate($fromCurrency);
        $convertedAmount = $this->convertToBaseCurrency($amount, $fromCurrency);

        return [
            'converted_amount' => $convertedAmount,
            'original_amount' => $amount,
            'rate' => $rate,
            'from_currency' => $fromCurrency,
            'to_currency' => $this->baseCurrency,
            'converted_at' => now(),
        ];
    }

    /**
     * Check if currency conversion is needed
     *
     * @param string $currencyCode Currency code
     * @return bool True if conversion needed
     */
    public function needsConversion(string $currencyCode): bool
    {
        return $currencyCode !== $this->baseCurrency;
    }
}
