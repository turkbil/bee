<?php

declare(strict_types=1);

namespace Modules\Subscription\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;
use Modules\Subscription\App\Models\Subscription;
use Modules\Subscription\App\Models\SubscriptionPlan;

#[Layout('admin.layout')]
class SubscriptionStatsComponent extends Component
{
    #[Computed]
    public function stats()
    {
        // Trial: has_trial=true VE trial_ends_at gelecekte (status ne olursa olsun)
        $trialCount = Subscription::where('has_trial', true)
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->count();

        // Active: Premium (trial olmayan aktif) subscriptions
        $activeCount = Subscription::where('status', 'active')
            ->where(function($q) {
                $q->whereNull('current_period_end')
                  ->orWhere('current_period_end', '>', now());
            })
            ->where(function($q) {
                $q->where('has_trial', false)
                  ->orWhereNull('has_trial')
                  ->orWhere('trial_ends_at', '<=', now())
                  ->orWhereNull('trial_ends_at');
            })
            ->count();

        // Expired: Süresi dolmuş (status expired veya period_end geçmiş)
        $expiredCount = Subscription::where(function($q) {
            $q->where('status', 'expired')
              ->orWhere(function($q2) {
                  $q2->whereNotNull('current_period_end')
                     ->where('current_period_end', '<=', now())
                     ->where('status', '!=', 'cancelled');
              });
        })->count();

        // Cancelled: İptal edilmiş
        $cancelledCount = Subscription::cancelled()->count();

        return [
            'active' => $activeCount,
            'trial' => $trialCount,
            'expired' => $expiredCount,
            'cancelled' => $cancelledCount,
        ];
    }

    #[Computed]
    public function expiringStats()
    {
        // 24 saat altı (KRİTİK)
        $critical24h = Subscription::whereIn('status', ['active', 'trial'])
            ->where(function($q) {
                $q->where(function($q2) {
                    $q2->where('current_period_end', '<=', now()->addDay())
                       ->where('current_period_end', '>', now());
                })
                ->orWhere(function($q2) {
                    $q2->where('trial_ends_at', '<=', now()->addDay())
                       ->where('trial_ends_at', '>', now());
                });
            })
            ->count();

        // 7 gün altı
        $warning7d = Subscription::whereIn('status', ['active', 'trial'])
            ->where(function($q) {
                $q->where(function($q2) {
                    $q2->where('current_period_end', '<=', now()->addDays(7))
                       ->where('current_period_end', '>', now());
                })
                ->orWhere(function($q2) {
                    $q2->where('trial_ends_at', '<=', now()->addDays(7))
                       ->where('trial_ends_at', '>', now());
                });
            })
            ->count();

        // 30 gün altı
        $warning30d = Subscription::whereIn('status', ['active', 'trial'])
            ->where(function($q) {
                $q->where(function($q2) {
                    $q2->where('current_period_end', '<=', now()->addDays(30))
                       ->where('current_period_end', '>', now());
                })
                ->orWhere(function($q2) {
                    $q2->where('trial_ends_at', '<=', now()->addDays(30))
                       ->where('trial_ends_at', '>', now());
                });
            })
            ->count();

        // Bugün bitenler
        $expiringToday = Subscription::where(function($q) {
            $q->whereBetween('current_period_end', [now()->startOfDay(), now()->endOfDay()])
              ->orWhereBetween('trial_ends_at', [now()->startOfDay(), now()->endOfDay()]);
        })->count();

        // Bu hafta bitenler
        $expiringThisWeek = Subscription::where(function($q) {
            $q->whereBetween('current_period_end', [now()->startOfWeek(), now()->endOfWeek()])
              ->orWhereBetween('trial_ends_at', [now()->startOfWeek(), now()->endOfWeek()]);
        })->count();

        // Otomatik yenileme kapalı
        $autoRenewOff = Subscription::whereIn('status', ['active', 'trial'])
            ->where('auto_renew', false)
            ->count();

        return [
            'critical_24h' => $critical24h,
            'warning_7d' => $warning7d,
            'warning_30d' => $warning30d,
            'expiring_today' => $expiringToday,
            'expiring_this_week' => $expiringThisWeek,
            'auto_renew_off' => $autoRenewOff,
        ];
    }

    #[Computed]
    public function planStats()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->get();
        $totalSubscriptions = Subscription::whereIn('status', ['active', 'trial'])->count();

        $stats = [];
        foreach ($plans as $plan) {
            $count = Subscription::where('subscription_plan_id', $plan->subscription_plan_id)
                ->whereIn('status', ['active', 'trial'])
                ->count();

            // Gelir hesaplama (aktif abonelikler için)
            $revenue = Subscription::where('subscription_plan_id', $plan->subscription_plan_id)
                ->whereIn('status', ['active', 'trial'])
                ->sum('price_per_cycle');

            $percentage = $totalSubscriptions > 0 ? round(($count / $totalSubscriptions) * 100) : 0;

            $stats[] = [
                'plan' => $plan,
                'count' => $count,
                'revenue' => $revenue,
                'percentage' => $percentage,
            ];
        }

        return $stats;
    }

    #[Computed]
    public function trialStats()
    {
        // Aktif deneme
        $activeTrial = Subscription::where('has_trial', true)
            ->where('trial_ends_at', '>', now())
            ->count();

        // Premium'a geçenler (deneme bitti, aktif)
        $trialToPremium = Subscription::where('has_trial', true)
            ->where('trial_ends_at', '<=', now())
            ->where('status', 'active')
            ->count();

        // İptal edenler (deneme bitti, iptal/expired)
        $trialToCancel = Subscription::where('has_trial', true)
            ->where('trial_ends_at', '<=', now())
            ->whereIn('status', ['cancelled', 'expired'])
            ->count();

        // Bu hafta trial'ı bitenler
        $trialEndingThisWeek = Subscription::where('has_trial', true)
            ->whereBetween('trial_ends_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Dönüşüm oranı
        $totalEnded = $trialToPremium + $trialToCancel;
        $conversionRate = $totalEnded > 0 ? round(($trialToPremium / $totalEnded) * 100) : 0;

        return [
            'active_trial' => $activeTrial,
            'trial_to_premium' => $trialToPremium,
            'trial_to_cancel' => $trialToCancel,
            'trial_ending_this_week' => $trialEndingThisWeek,
            'conversion_rate' => $conversionRate,
        ];
    }

    #[Computed]
    public function revenueStats()
    {
        // Aylık gelir (aylık abonelikler - gün bazlı + enum)
        $monthlyRevenue = Subscription::where(function($q) {
                // 28-31 gün arası (aylık) VEYA billing_cycle='monthly'
                $q->where('billing_cycle', 'monthly')
                  ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 28 AND 31");
            })
            ->whereIn('status', ['active', 'trial'])
            ->sum('price_per_cycle');

        $monthlyCount = Subscription::where(function($q) {
                $q->where('billing_cycle', 'monthly')
                  ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 28 AND 31");
            })
            ->whereIn('status', ['active', 'trial'])
            ->count();

        // Yıllık gelir (yıllık abonelikler - gün bazlı + enum)
        $yearlyRevenue = Subscription::where(function($q) {
                // 365-366 gün arası (yıllık) VEYA billing_cycle='yearly'
                $q->where('billing_cycle', 'yearly')
                  ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 365 AND 366");
            })
            ->whereIn('status', ['active', 'trial'])
            ->sum('price_per_cycle');

        $yearlyCount = Subscription::where(function($q) {
                $q->where('billing_cycle', 'yearly')
                  ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 365 AND 366");
            })
            ->whereIn('status', ['active', 'trial'])
            ->count();

        // Bu ay yenilenecekler (beklenen gelir)
        $expectedRevenue = Subscription::whereIn('status', ['active', 'trial'])
            ->where('auto_renew', true)
            ->where(function($q) {
                $q->whereBetween('current_period_end', [now()->startOfMonth(), now()->endOfMonth()])
                  ->orWhereBetween('next_billing_date', [now()->startOfMonth(), now()->endOfMonth()]);
            })
            ->sum('price_per_cycle');

        $renewingCount = Subscription::whereIn('status', ['active', 'trial'])
            ->where('auto_renew', true)
            ->where(function($q) {
                $q->whereBetween('current_period_end', [now()->startOfMonth(), now()->endOfMonth()])
                  ->orWhereBetween('next_billing_date', [now()->startOfMonth(), now()->endOfMonth()]);
            })
            ->count();

        return [
            'monthly_revenue' => $monthlyRevenue,
            'monthly_count' => $monthlyCount,
            'yearly_revenue' => $yearlyRevenue,
            'yearly_count' => $yearlyCount,
            'expected_revenue' => $expectedRevenue,
            'renewing_count' => $renewingCount,
        ];
    }

    public function render()
    {
        return view('subscription::admin.livewire.subscription-stats-component', [
            'stats' => $this->stats,
            'expiringStats' => $this->expiringStats,
            'planStats' => $this->planStats,
            'trialStats' => $this->trialStats,
            'revenueStats' => $this->revenueStats,
        ]);
    }
}
