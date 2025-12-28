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
        // Active: Aktif abonelikler (period_end gelecekte)
        $activeCount = Subscription::where('status', 'active')
            ->where(function($q) {
                $q->whereNull('current_period_end')
                  ->orWhere('current_period_end', '>', now());
            })
            ->count();

        // Pending: Ödenmiş, sırada bekliyor (zincirde)
        $pendingCount = Subscription::where('status', 'pending')->count();

        // Pending Payment: Ödeme bekleniyor
        $pendingPaymentCount = Subscription::where('status', 'pending_payment')->count();

        // Expired: Süresi dolmuş
        $expiredCount = Subscription::where(function($q) {
            $q->where('status', 'expired')
              ->orWhere(function($q2) {
                  $q2->whereNotNull('current_period_end')
                     ->where('current_period_end', '<=', now())
                     ->whereNotIn('status', ['cancelled', 'pending']);
              });
        })->count();

        // Cancelled: İptal edilmiş
        $cancelledCount = Subscription::cancelled()->count();

        // Toplam aktif + sırada (gerçek abone sayısı)
        $totalActive = $activeCount + $pendingCount;

        // Kurumsal hesap sayısı
        $corporateCount = 0;
        if (class_exists(\Modules\Muzibu\App\Models\MuzibuCorporateAccount::class)) {
            $corporateCount = \Modules\Muzibu\App\Models\MuzibuCorporateAccount::whereNull('parent_id')->count();
        }

        return [
            'active' => $activeCount,
            'pending' => $pendingCount,
            'pending_payment' => $pendingPaymentCount,
            'expired' => $expiredCount,
            'cancelled' => $cancelledCount,
            'total_active' => $totalActive,
            'corporate' => $corporateCount,
        ];
    }

    #[Computed]
    public function expiringStats()
    {
        // 24 saat altı (KRİTİK)
        $critical24h = Subscription::where('status', 'active')
            ->where('current_period_end', '<=', now()->addDay())
            ->where('current_period_end', '>', now())
            ->count();

        // 7 gün altı
        $warning7d = Subscription::where('status', 'active')
            ->where('current_period_end', '<=', now()->addDays(7))
            ->where('current_period_end', '>', now())
            ->count();

        // 30 gün altı
        $warning30d = Subscription::where('status', 'active')
            ->where('current_period_end', '<=', now()->addDays(30))
            ->where('current_period_end', '>', now())
            ->count();

        // Bugün bitenler
        $expiringToday = Subscription::where('status', 'active')
            ->whereBetween('current_period_end', [now()->startOfDay(), now()->endOfDay()])
            ->count();

        // Bu hafta bitenler
        $expiringThisWeek = Subscription::where('status', 'active')
            ->whereBetween('current_period_end', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return [
            'critical_24h' => $critical24h,
            'warning_7d' => $warning7d,
            'warning_30d' => $warning30d,
            'expiring_today' => $expiringToday,
            'expiring_this_week' => $expiringThisWeek,
        ];
    }

    #[Computed]
    public function planStats()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->get();
        // Aktif + pending (gerçek aboneler)
        $totalSubscriptions = Subscription::whereIn('status', ['active', 'pending'])->count();

        $stats = [];
        foreach ($plans as $plan) {
            $count = Subscription::where('subscription_plan_id', $plan->subscription_plan_id)
                ->whereIn('status', ['active', 'pending'])
                ->count();

            // Gelir hesaplama (aktif + pending için)
            $revenue = Subscription::where('subscription_plan_id', $plan->subscription_plan_id)
                ->whereIn('status', ['active', 'pending'])
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
    public function revenueStats()
    {
        // Toplam aktif gelir (aktif + pending)
        $totalActiveRevenue = Subscription::whereIn('status', ['active', 'pending'])
            ->sum('price_per_cycle');

        $totalActiveCount = Subscription::whereIn('status', ['active', 'pending'])->count();

        // Aylık abonelikler
        $monthlyRevenue = Subscription::where(function($q) {
                $q->where('billing_cycle', 'monthly')
                  ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 28 AND 31");
            })
            ->whereIn('status', ['active', 'pending'])
            ->sum('price_per_cycle');

        $monthlyCount = Subscription::where(function($q) {
                $q->where('billing_cycle', 'monthly')
                  ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 28 AND 31");
            })
            ->whereIn('status', ['active', 'pending'])
            ->count();

        // Yıllık abonelikler
        $yearlyRevenue = Subscription::where(function($q) {
                $q->where('billing_cycle', 'yearly')
                  ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 365 AND 366");
            })
            ->whereIn('status', ['active', 'pending'])
            ->sum('price_per_cycle');

        $yearlyCount = Subscription::where(function($q) {
                $q->where('billing_cycle', 'yearly')
                  ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 365 AND 366");
            })
            ->whereIn('status', ['active', 'pending'])
            ->count();

        // Ödeme bekleyen potansiyel gelir
        $pendingPaymentRevenue = Subscription::where('status', 'pending_payment')
            ->sum('price_per_cycle');

        $pendingPaymentCount = Subscription::where('status', 'pending_payment')->count();

        return [
            'total_active_revenue' => $totalActiveRevenue,
            'total_active_count' => $totalActiveCount,
            'monthly_revenue' => $monthlyRevenue,
            'monthly_count' => $monthlyCount,
            'yearly_revenue' => $yearlyRevenue,
            'yearly_count' => $yearlyCount,
            'pending_payment_revenue' => $pendingPaymentRevenue,
            'pending_payment_count' => $pendingPaymentCount,
        ];
    }

    public function render()
    {
        return view('subscription::admin.livewire.subscription-stats-component', [
            'stats' => $this->stats,
            'expiringStats' => $this->expiringStats,
            'planStats' => $this->planStats,
            'revenueStats' => $this->revenueStats,
        ]);
    }
}
