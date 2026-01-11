<?php

namespace Modules\Payment\App\Services;

use Modules\Payment\App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;

class PayTRInstallmentService
{
    /**
     * PayTR'den taksit oranlarını çek ve güncelle
     */
    public function updateInstallmentRates(PaymentMethod $paymentMethod): array
    {
        if ($paymentMethod->gateway !== 'paytr') {
            return [
                'success' => false,
                'message' => 'Bu servis sadece PayTR için çalışır'
            ];
        }

        $config = $paymentMethod->gateway_config;

        if (empty($config['merchant_id']) || empty($config['merchant_key']) || empty($config['merchant_salt'])) {
            return [
                'success' => false,
                'message' => 'PayTR merchant bilgileri eksik'
            ];
        }

        $merchantId = $config['merchant_id'];
        $merchantKey = $config['merchant_key'];
        $merchantSalt = $config['merchant_salt'];
        $requestId = time();

        // Token oluştur
        $paytrToken = base64_encode(hash_hmac('sha256', $merchantId . $requestId . $merchantSalt, $merchantKey, true));

        // POST parametreleri
        $postData = [
            'merchant_id' => $merchantId,
            'request_id' => $requestId,
            'paytr_token' => $paytrToken,
            'single_ratio' => 1, // Tek çekim oranı için
        ];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/taksit-oranlari");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 90);

            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);

                Log::error('PayTR Taksit Oranları API Hatası', [
                    'error' => $error,
                    'payment_method_id' => $paymentMethod->payment_method_id
                ]);

                return [
                    'success' => false,
                    'message' => 'PayTR API bağlantı hatası: ' . $error
                ];
            }

            curl_close($ch);

            $result = json_decode($result, true);

            if (empty($result) || !isset($result['status'])) {
                return [
                    'success' => false,
                    'message' => 'PayTR API geçersiz yanıt döndü'
                ];
            }

            if ($result['status'] !== 'success') {
                $errorMsg = $result['err_msg'] ?? 'Bilinmeyen hata';

                Log::error('PayTR Taksit Oranları API Hatası', [
                    'error' => $errorMsg,
                    'result' => $result
                ]);

                return [
                    'success' => false,
                    'message' => 'PayTR API hatası: ' . $errorMsg
                ];
            }

            // Oranları işle ve kaydet
            $installmentOptions = $this->processInstallmentRates($result);

            // PaymentMethod güncelle
            $paymentMethod->update([
                'installment_options' => $installmentOptions,
                'max_installments' => $result['max_inst_non_bus'] ?? 12,
            ]);

            Log::info('PayTR Taksit Oranları Güncellendi', [
                'payment_method_id' => $paymentMethod->payment_method_id,
                'max_installments' => $paymentMethod->max_installments,
                'installment_count' => count($installmentOptions)
            ]);

            return [
                'success' => true,
                'message' => 'Taksit oranları başarıyla güncellendi',
                'max_installments' => $paymentMethod->max_installments,
                'installment_options' => $installmentOptions
            ];

        } catch (\Exception $e) {
            Log::error('PayTR Taksit Oranları Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ];
        }
    }

    /**
     * PayTR'den gelen oranları işle
     *
     * Ortalama oran hesaplama: Tüm kart tiplerinin ortalaması
     */
    private function processInstallmentRates(array $result): array
    {
        $installmentOptions = [];

        if (empty($result['oranlar'])) {
            return [];
        }

        $oranlar = $result['oranlar'];

        // Kart tipleri: axess, world, maximum, cardfinans, paraf, advantage, combo, bonus
        $cardTypes = ['axess', 'world', 'maximum', 'cardfinans', 'paraf', 'advantage', 'combo', 'bonus'];

        // Maksimum taksit sayısını bul
        $maxInstallment = $result['max_inst_non_bus'] ?? 12;

        // Her taksit sayısı için ortalama oran hesapla
        for ($i = 2; $i <= $maxInstallment; $i++) {
            $rates = [];

            foreach ($cardTypes as $cardType) {
                if (isset($oranlar[$cardType]) && isset($oranlar[$cardType][$i])) {
                    $rates[] = (float) $oranlar[$cardType][$i];
                }
            }

            if (!empty($rates)) {
                // Ortalama hesapla
                $averageRate = array_sum($rates) / count($rates);
                $installmentOptions[(string) $i] = round($averageRate, 2);
            }
        }

        return $installmentOptions;
    }

    /**
     * Tüm PayTR ödeme yöntemlerinin taksit oranlarını güncelle
     */
    public function updateAllPayTRRates(): array
    {
        $paytrMethods = PaymentMethod::where('gateway', 'paytr')
            ->where('is_active', true)
            ->get();

        if ($paytrMethods->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Aktif PayTR ödeme yöntemi bulunamadı'
            ];
        }

        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($paytrMethods as $method) {
            $result = $this->updateInstallmentRates($method);

            $results[] = [
                'payment_method_id' => $method->payment_method_id,
                'title' => $method->getTranslated('title', 'tr'),
                'success' => $result['success'],
                'message' => $result['message']
            ];

            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        return [
            'success' => $failCount === 0,
            'message' => "Toplam: {$paytrMethods->count()}, Başarılı: {$successCount}, Hatalı: {$failCount}",
            'results' => $results
        ];
    }
}
