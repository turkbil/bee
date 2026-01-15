<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * 🚨 AI CREDIT WARNING COMPONENT v3.0
 * 
 * Global kredi uyarı sistemi - Tüm admin sayfalarında gösterilir
 * 
 * Özellikler:
 * - 💰 Real-time kredi durumu
 * - ⚠️ Düşük kredi uyarıları
 * - 🚫 Yetersiz kredi bildirimleri
 * - 🔔 Session flash mesajları (AIServiceWrapper entegrasyonu)
 * - 🎯 Auto-refresh capability
 * - 🛒 Kredi satın alma yönlendirme
 * 
 * @package Modules\AI\App\Http\Livewire\Admin
 * @author AI System v3.0
 * @version 3.0.0
 */
class CreditWarningComponent extends Component
{
    public $currentBalance;
    public $lowCreditThreshold = 10.0;
    public $criticalCreditThreshold = 5.0;
    public $showWarning = false;
    public $warningType = 'info'; // info, warning, critical, error
    public $warningMessage = '';
    public $buyCreditUrl = '';
    public $refreshInterval = 30; // seconds
    public $isDismissed = false;
    public $creditDetails = [];

    protected $listeners = [
        'creditBalanceUpdated' => 'refreshBalance',
        'aiOperationCompleted' => 'refreshBalance',
        'refreshCreditWarning' => 'refreshBalance'
    ];

    public function mount(): void
    {
        $this->refreshBalance();
        $this->checkSessionMessages();
    }

    public function render()
    {
        return view('ai::admin.livewire.credit-warning-component');
    }

    /**
     * 🔄 Kredi bakiyesini güncelle ve uyarıları kontrol et
     */
    public function refreshBalance()
    {
        try {
            $tenant = tenancy()->tenant ?? tenant();
            if (!$tenant) {
                $this->currentBalance = 0;
                $this->showWarning = false;
                return;
            }

            $this->currentBalance = ai_credit_balance($tenant);
            $this->buyCreditUrl = route('admin.ai.credits.purchases', ['tenant' => $tenant->id]);
            
            // Cache key for daily warning dismissal
            $cacheKey = "credit_warning_dismissed_{$tenant->id}_" . now()->format('Y-m-d');
            
            // Eğer bugün dismiss edilmişse gösterme (kritik durumlar hariç)
            if (Cache::has($cacheKey) && $this->currentBalance > 0) {
                $this->isDismissed = true;
                $this->showWarning = false;
                return;
            }
            
            // Kredi durumunu değerlendir
            $this->evaluateCreditStatus($tenant, $cacheKey);
            $this->prepareCreditDetails($tenant);

        } catch (\Exception $e) {
            Log::error('❌ Credit balance refresh failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            $this->currentBalance = 0;
            $this->showWarning = true;
            $this->warningType = 'error';
            $this->warningMessage = 'Kredi bilgileri alınamadı. Lütfen sayfayı yenileyin.';
        }
    }

    /**
     * 📊 Kredi durumunu değerlendir
     */
    private function evaluateCreditStatus(Tenant $tenant, string $cacheKey)
    {
        if ($this->currentBalance <= 0) {
            $this->showWarning = true;
            $this->warningType = 'error';
            $this->warningMessage = sprintf(
                '🚫 AI kredisi tükendi! Mevcut: %.2f kredi. AI özelliklerini kullanmak için kredi satın alın.',
                $this->currentBalance
            );
        }
        elseif ($this->currentBalance <= $this->criticalCreditThreshold) {
            $this->showWarning = true;
            $this->warningType = 'critical';
            $this->warningMessage = sprintf(
                '🔴 Kritik seviyede düşük AI kredisi! Mevcut: %.2f kredi. Acil kredi alımı yapmanız öneriliyor.',
                $this->currentBalance
            );
        }
        elseif ($this->currentBalance <= $this->lowCreditThreshold) {
            // Günlük bir kez göster (dismiss edilmemişse)
            if (!$this->isDismissed) {
                $this->showWarning = true;
                $this->warningType = 'warning';
                $this->warningMessage = sprintf(
                    '🟡 Düşük AI kredisi uyarısı! Mevcut: %.2f kredi. Yakında kredi alımı yapmanız öneriliyor.',
                    $this->currentBalance
                );
            }
        }
        else {
            $this->showWarning = false;
        }
    }

    /**
     * 💬 Session flash mesajlarını kontrol et (AIServiceWrapper entegrasyonu)
     */
    private function checkSessionMessages()
    {
        // AIServiceWrapper'dan gelen yetersiz kredi hatası
        if (session()->has('ai_credit_error')) {
            $errorData = session()->get('ai_credit_error');
            $this->showWarning = true;
            $this->warningType = 'error';
            $this->warningMessage = $errorData['message'] ?? 'Yetersiz AI kredisi!';
            $this->buyCreditUrl = $errorData['buy_credits_url'] ?? $this->buyCreditUrl;
            $this->isDismissed = false; // Kritik hata - dismiss edilemez
            
            session()->forget('ai_credit_error');
        }
        
        // AIServiceWrapper'dan gelen düşük kredi uyarısı
        if (session()->has('ai_credit_warning')) {
            $warningData = session()->get('ai_credit_warning');
            $this->showWarning = true;
            $this->warningType = $warningData['type'] ?? 'warning';
            $this->warningMessage = $warningData['message'] ?? 'Düşük AI kredisi!';
            $this->buyCreditUrl = $warningData['buy_credits_url'] ?? $this->buyCreditUrl;
            
            session()->forget('ai_credit_warning');
        }
    }

    /**
     * 📋 Kredi detaylarını hazırla
     */
    private function prepareCreditDetails(Tenant $tenant)
    {
        $this->creditDetails = [
            'current_credits' => $this->currentBalance,
            'low_threshold' => $this->lowCreditThreshold,
            'critical_threshold' => $this->criticalCreditThreshold,
            'percentage' => $this->currentBalance > 0 ? min(100, ($this->currentBalance / $this->lowCreditThreshold) * 100) : 0,
            'tenant_id' => $tenant->id,
            'last_updated' => now()->format('H:i:s')
        ];
    }

    /**
     * ❌ Uyarıyı kapat (bugün için)
     */
    public function dismissWarning(): void
    {
        $tenant = tenancy()->tenant ?? tenant();
        
        // Kritik durumları dismiss etme
        if ($this->warningType === 'error' || $this->currentBalance <= 0) {
            return;
        }
        
        if ($tenant) {
            $dismissCacheKey = "credit_warning_dismissed_{$tenant->id}_" . now()->format('Y-m-d');
            Cache::put($dismissCacheKey, true, now()->endOfDay());
        }

        $this->isDismissed = true;
        $this->showWarning = false;

        // Analytics için log
        Log::info('👋 Credit warning dismissed', [
            'tenant_id' => $tenant?->id,
            'warning_type' => $this->warningType,
            'balance' => $this->currentBalance
        ]);

        $this->dispatch('credit-warning-dismissed');
    }

    /**
     * 🛒 Kredi satın alma sayfasına yönlendir
     */
    public function buyCredits()
    {
        // Analytics için log
        Log::info('💰 Buy credit clicked from warning', [
            'tenant_id' => tenancy()->tenant?->id ?? tenant()?->id,
            'current_balance' => $this->currentBalance,
            'warning_type' => $this->warningType,
            'source' => 'credit_warning_component'
        ]);
        
        return redirect($this->buyCreditUrl);
    }

    /**
     * 🔄 Manuel kredi durumu yenileme
     */
    public function refreshCredits(): void
    {
        $this->refreshBalance();
        $this->checkSessionMessages();
        $this->dispatch('credit-status-refreshed');

        // Toast kaldırıldı - kullanıcı ilk girişte her seferinde mesaj görmüyordu
    }

    /**
     * 🎨 Warning alert class generator
     */
    public function getWarningClass(): string
    {
        return match($this->warningType) {
            'error' => 'alert-danger',
            'critical' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-secondary'
        };
    }

    /**
     * 🎭 Warning icon generator
     */
    public function getWarningIcon(): string
    {
        return match($this->warningType) {
            'error' => 'ti-x-circle',
            'critical' => 'ti-alert-triangle',
            'warning' => 'ti-alert-circle',
            'info' => 'ti-info-circle',
            default => 'ti-check'
        };
    }

    /**
     * 📊 Kredi durumunu detaylı bilgi olarak al
     */
    public function getCreditDetails(): array
    {
        return $this->creditDetails;
    }
    
    /**
     * 🌟 Kredi durumu özeti
     */
    public function getCreditSummary(): string
    {
        if ($this->currentBalance <= 0) {
            return 'Kredi tükendi';
        } elseif ($this->currentBalance <= $this->criticalCreditThreshold) {
            return 'Kritik seviye';
        } elseif ($this->currentBalance <= $this->lowCreditThreshold) {
            return 'Düşük seviye';
        } else {
            return 'Yeterli seviye';
        }
    }
    
    /**
     * 🎯 Component'in gösterilip gösterilmeyeceğini kontrol et
     */
    public function shouldRender(): bool
    {
        return $this->showWarning && !$this->isDismissed;
    }
    
    /**
     * 🔄 Otomatik refresh için lifecycle hook
     */
    public function hydrate()
    {
        // Her 30 saniyede bir otomatik refresh (opsiyonel)
        // JavaScript tarafında implement edilecek
    }
}