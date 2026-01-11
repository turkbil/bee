<?php

declare(strict_types=1);

namespace Modules\Shop\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TCMB (Türkiye Cumhuriyet Merkez Bankası) Döviz Kuru Servisi
 *
 * TCMB XML API'den güncel döviz kurlarını çeker
 */
class TcmbExchangeRateService
{
    /**
     * TCMB API Endpoint
     */
    private const TCMB_API_URL = 'https://www.tcmb.gov.tr/kurlar/today.xml';

    /**
     * TCMB'den güncel kurları çeker
     *
     * @return array{success: bool, rates: array, message: string}
     */
    public function fetchRates(): array
    {
        try {
            $response = Http::timeout(10)->get(self::TCMB_API_URL);

            if (!$response->successful()) {
                Log::error('TCMB API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'rates' => [],
                    'message' => 'TCMB API\'ye ulaşılamadı. HTTP Status: ' . $response->status(),
                ];
            }

            $xml = simplexml_load_string($response->body());

            if ($xml === false) {
                Log::error('TCMB XML parse failed');

                return [
                    'success' => false,
                    'rates' => [],
                    'message' => 'TCMB XML verisi okunamadı',
                ];
            }

            $rates = $this->parseXml($xml);

            if (empty($rates)) {
                return [
                    'success' => false,
                    'rates' => [],
                    'message' => 'TCMB\'den kur verisi alınamadı',
                ];
            }

            Log::info('TCMB rates fetched successfully', ['rates' => $rates]);

            return [
                'success' => true,
                'rates' => $rates,
                'message' => 'TCMB\'den kurlar başarıyla alındı',
            ];

        } catch (\Exception $e) {
            Log::error('TCMB API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'rates' => [],
                'message' => 'Hata: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * TCMB XML'ini parse eder
     *
     * @param \SimpleXMLElement $xml
     * @return array<string, float>
     */
    private function parseXml(\SimpleXMLElement $xml): array
    {
        $rates = [];

        foreach ($xml->Currency as $currency) {
            $code = (string) $currency['CurrencyCode'];

            // ForexSelling = Döviz Satış Kuru (En güncel, piyasa kuru)
            $forexSelling = (string) $currency->ForexSelling;

            if (!empty($code) && !empty($forexSelling)) {
                $rates[$code] = (float) $forexSelling;
            }
        }

        return $rates;
    }

    /**
     * Belirli bir para biriminin kurunu döndürür
     *
     * @param string $currencyCode (USD, EUR, GBP, etc.)
     * @return float|null
     */
    public function getRate(string $currencyCode): ?float
    {
        $result = $this->fetchRates();

        if (!$result['success']) {
            return null;
        }

        return $result['rates'][$currencyCode] ?? null;
    }

    /**
     * Desteklenen para birimlerini döndürür
     *
     * @return array<string>
     */
    public function getSupportedCurrencies(): array
    {
        return ['USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'CNY'];
    }
}
