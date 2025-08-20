<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use Modules\AI\App\Services\ModelBasedCreditService;
use Illuminate\Support\Facades\Cache;

/**
 * Credit Warning Component
 * 
 * Real-time kredi uyarı sistemi - Admin panelinde görüntülenir
 */
class CreditWarningComponent extends Component
{
    public array $warningData = [];
    public bool $showWarning = false;
    public string $warningType = 'none';
    public bool $isDismissed = false;

    protected ModelBasedCreditService $creditService;

    public function boot(): void
    {
        $this->creditService = app(ModelBasedCreditService::class);
    }

    public function mount(): void
    {
        $this->checkCreditWarning();
    }

    public function render()
    {
        return view('ai::admin.livewire.credit-warning-component');
    }

    /**
     * Kredi uyarısını kontrol et
     */
    public function checkCreditWarning(): void
    {
        $tenant = tenant();
        
        if (!$tenant) {
            $this->showWarning = false;
            return;
        }

        // Cache key for dismissed warnings
        $dismissCacheKey = "credit_warning_dismissed_{$tenant->id}_" . now()->format('Y-m-d');
        
        // Check if warning was dismissed today
        if (Cache::has($dismissCacheKey)) {
            $this->isDismissed = true;
            $this->showWarning = false;
            return;
        }

        // Get warning level
        $this->warningData = $this->creditService->getCreditWarningLevel($tenant);
        $this->warningType = $this->warningData['level'] ?? 'none';
        $this->showWarning = $this->creditService->shouldShowCreditWarning($tenant);
        $this->isDismissed = false;
    }

    /**
     * Uyarıyı kapat (bugün için)
     */
    public function dismissWarning(): void
    {
        $tenant = tenant();
        
        if ($tenant) {
            $dismissCacheKey = "credit_warning_dismissed_{$tenant->id}_" . now()->format('Y-m-d');
            Cache::put($dismissCacheKey, true, now()->endOfDay());
        }

        $this->isDismissed = true;
        $this->showWarning = false;

        $this->dispatch('credit-warning-dismissed');
    }

    /**
     * Kredi satın al sayfasına yönlendir
     */
    public function buyCredits()
    {
        return redirect()->route('admin.ai.credits.purchase');
    }

    /**
     * Kredi durumunu yenile
     */
    public function refreshCredits(): void
    {
        $this->checkCreditWarning();
        $this->dispatch('credit-status-refreshed');
    }

    /**
     * Credit warning level'a göre CSS class döndür
     */
    public function getWarningClass(): string
    {
        return match($this->warningType) {
            'critical' => 'alert-danger',
            'low' => 'alert-warning', 
            'moderate' => 'alert-info',
            default => 'alert-secondary'
        };
    }

    /**
     * Credit warning icon döndür
     */
    public function getWarningIcon(): string
    {
        return match($this->warningType) {
            'critical' => 'ti-alert-triangle',
            'low' => 'ti-alert-circle',
            'moderate' => 'ti-info-circle',
            default => 'ti-check'
        };
    }

    /**
     * Kredi durumunu detaylı bilgi olarak al
     */
    public function getCreditDetails(): array
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return [
                'current_credits' => 'Unlimited',
                'warning_threshold' => 0,
                'recommendation' => 'System tenant - no credit limits'
            ];
        }

        $currentCredits = $tenant->ai_credits ?? $tenant->credits ?? 0;
        $warning = $this->warningData;

        $recommendation = match($warning['level'] ?? 'sufficient') {
            'critical' => 'Acilen kredi satın alın! AI özellikler çalışmayabilir.',
            'low' => 'Yakında kredi satın alın. Stok azalıyor.',
            'moderate' => 'Kredi durumunuzu takip edin.',
            default => 'Kredi durumunuz yeterli.'
        };

        return [
            'current_credits' => $currentCredits,
            'warning_threshold' => $warning['threshold'] ?? 0,
            'recommendation' => $recommendation,
            'percentage' => $warning['threshold'] ? round(($currentCredits / $warning['threshold']) * 100, 1) : 100
        ];
    }
}