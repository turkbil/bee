<?php

namespace Modules\Payment\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payment\App\Services\PayTRCallbackService;
use Illuminate\Support\Facades\Log;

/**
 * PayTR Callback Controller
 *
 * PayTR'den gelen ödeme bildirimlerini işler (POST request).
 * Tenant-aware: merchant_oid içinde tenant ID parse eder.
 */
class PayTRCallbackController extends Controller
{
    public function __construct(
        private PayTRCallbackService $callbackService
    ) {}

    /**
     * PayTR callback endpoint
     *
     * NOT: Bu route CSRF koruması bypass etmeli (VerifyCsrfToken middleware exception)
     * NOT: Session/Auth gerektirmez (PayTR external service)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        try {
            // POST verilerini al
            $callbackData = $request->all();

            if (setting('paytr_debug', false)) {
                Log::info('📨 PayTR callback received', $callbackData);
            }

            // Tenant ID parse et (merchant_oid formatı: T{tenant_id}-ORD-20251112-ABC)
            $merchantOid = $callbackData['merchant_oid'] ?? '';
            $tenantId = $this->parseTenantId($merchantOid);

            if (!$tenantId) {
                Log::error('❌ PayTR callback: Tenant ID parse edilemedi', [
                    'merchant_oid' => $merchantOid
                ]);
                return response('FAIL: Invalid merchant_oid format', 400);
            }

            // Tenant context'e gir
            $tenant = \App\Models\Tenant::find($tenantId);

            if (!$tenant) {
                Log::error('❌ PayTR callback: Tenant bulunamadı', [
                    'tenant_id' => $tenantId,
                    'merchant_oid' => $merchantOid
                ]);
                return response('FAIL: Tenant not found', 404);
            }

            tenancy()->initialize($tenant);

            // Callback'i işle
            $result = $this->callbackService->handleCallback($callbackData);

            // PayTR'ye cevap dön (ZORUNLU!)
            if ($result['success']) {
                return response('OK', 200);
            } else {
                return response('FAIL: ' . $result['message'], 400);
            }

        } catch (\Exception $e) {
            Log::error('❌ PayTR callback exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('FAIL: Internal error', 500);
        }
    }

    /**
     * merchant_oid içinden tenant ID parse et
     *
     * Format: T{tenant_id}PAY{year}{number} (tiresiz - PayTR için)
     * Örnek: T2PAY202500010 → tenant_id = 2
     *
     * Fallback: T prefix'i yoksa (eski format) default tenant 2 kullan
     */
    private function parseTenantId(string $merchantOid): ?int
    {
        // Yeni format: T ile başlayıp rakam devam ediyorsa: T2PAY..., T2ORD..., T1001PAY...
        if (preg_match('/^T(\d+)/', $merchantOid, $matches)) {
            return (int) $matches[1];
        }

        // Eski format fallback: PAY ile başlıyorsa default tenant 2 (ixtif)
        if (preg_match('/^PAY|^ORD/', $merchantOid)) {
            Log::info('⚠️ PayTR callback: Eski format merchant_oid, default tenant 2 kullanılıyor', [
                'merchant_oid' => $merchantOid
            ]);
            return 2;
        }

        return null;
    }
}
