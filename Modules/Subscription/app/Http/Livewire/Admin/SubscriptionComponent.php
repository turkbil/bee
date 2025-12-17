<?php

declare(strict_types=1);

namespace Modules\Subscription\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed, Url};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Subscription\App\Models\Subscription;
use Modules\Subscription\App\Models\SubscriptionPlan;

#[Layout('admin.layout')]
class SubscriptionComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $filterStatus = 'active'; // Varsayılan: Sadece aktif abonelikler

    #[Url]
    public $filterPlan = '';

    #[Url]
    public $filterCycle = '';

    #[Url]
    public $filterExpiryRange = ''; // Yeni: Bitiş tarihi aralığı

    #[Url]
    public $filterRemainingDays = ''; // Yeni: Kalan süre

    #[Url]
    public $filterAutoRenew = ''; // Yeni: Otomatik yenileme

    #[Url]
    public $filterTrialStatus = ''; // Yeni: Deneme durumu

    #[Url]
    public $perPage = 25;

    #[Url]
    public $sortField = 'subscription_id';

    #[Url]
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterPlan' => ['except' => ''],
        'filterCycle' => ['except' => ''],
        'filterExpiryRange' => ['except' => ''],
        'filterRemainingDays' => ['except' => ''],
        'filterAutoRenew' => ['except' => ''],
        'filterTrialStatus' => ['except' => ''],
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->perPage = (int) $this->perPage;
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function subscriptions()
    {
        $query = Subscription::query()
            ->with(['customer', 'plan']);

        // Search
        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $query->where(function($q) use ($search) {
                $q->where('subscription_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status (Akıllı filtre)
        if (!empty($this->filterStatus)) {
            switch ($this->filterStatus) {
                case 'trial':
                    // Trial: has_trial=true VE trial_ends_at gelecekte
                    $query->where('has_trial', true)
                          ->where('trial_ends_at', '>', now());
                    break;

                case 'active':
                    // Active: Premium (trial olmayan) VE period_end gelecekte veya null
                    $query->where('status', 'active')
                          ->where(function($q) {
                              $q->whereNull('current_period_end')
                                ->orWhere('current_period_end', '>', now());
                          })
                          ->where(function($q) {
                              $q->where('has_trial', false)
                                ->orWhereNull('has_trial')
                                ->orWhere('trial_ends_at', '<=', now())
                                ->orWhereNull('trial_ends_at');
                          });
                    break;

                case 'expired':
                    // Expired: Status expired VEYA period_end geçmiş
                    $query->where(function($q) {
                        $q->where('status', 'expired')
                          ->orWhere(function($q2) {
                              $q2->whereNotNull('current_period_end')
                                 ->where('current_period_end', '<=', now())
                                 ->where('status', '!=', 'cancelled');
                          });
                    });
                    break;

                case 'cancelled':
                    // Cancelled: Status cancelled
                    $query->where('status', 'cancelled');
                    break;

                case 'paused':
                    // Paused: Status paused
                    $query->where('status', 'paused');
                    break;

                case 'pending_payment':
                    // Pending payment: Status pending_payment
                    $query->where('status', 'pending_payment');
                    break;

                default:
                    // Diğer durumlar için direkt status kontrolü
                    $query->where('status', $this->filterStatus);
                    break;
            }
        }

        // Filter by plan
        if (!empty($this->filterPlan)) {
            $query->where('subscription_plan_id', $this->filterPlan);
        }

        // Filter by billing cycle (gün bazlı + enum destekli)
        if (!empty($this->filterCycle)) {
            $query->where(function($q) {
                switch ($this->filterCycle) {
                    case 'monthly':
                        // Aylık: 28-31 gün arası VEYA billing_cycle='monthly'
                        $q->where('billing_cycle', 'monthly')
                          ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 28 AND 31");
                        break;

                    case 'yearly':
                        // Yıllık: 365-366 gün arası VEYA billing_cycle='yearly'
                        $q->where('billing_cycle', 'yearly')
                          ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 365 AND 366");
                        break;

                    case 'quarterly':
                        // 3 Aylık: 90-92 gün arası VEYA billing_cycle='quarterly'
                        $q->where('billing_cycle', 'quarterly')
                          ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') BETWEEN 90 AND 92");
                        break;

                    case 'weekly':
                        // Haftalık: 7 gün VEYA billing_cycle='weekly'
                        $q->where('billing_cycle', 'weekly')
                          ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') = 7");
                        break;

                    case 'daily':
                        // Günlük: 1 gün VEYA billing_cycle='daily'
                        $q->where('billing_cycle', 'daily')
                          ->orWhereRaw("JSON_EXTRACT(cycle_metadata, '$.duration_days') = 1");
                        break;

                    default:
                        // Direkt cycle_key veya billing_cycle kontrolü
                        $q->where('cycle_key', $this->filterCycle)
                          ->orWhere('billing_cycle', $this->filterCycle);
                        break;
                }
            });
        }

        // Filter by expiry range (Yeni)
        if (!empty($this->filterExpiryRange)) {
            switch ($this->filterExpiryRange) {
                case 'today':
                    $query->where(function($q) {
                        $q->whereBetween('current_period_end', [now()->startOfDay(), now()->endOfDay()])
                          ->orWhereBetween('trial_ends_at', [now()->startOfDay(), now()->endOfDay()]);
                    });
                    break;
                case 'this_week':
                    $query->where(function($q) {
                        $q->whereBetween('current_period_end', [now()->startOfWeek(), now()->endOfWeek()])
                          ->orWhereBetween('trial_ends_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    });
                    break;
                case 'this_month':
                    $query->where(function($q) {
                        $q->whereBetween('current_period_end', [now()->startOfMonth(), now()->endOfMonth()])
                          ->orWhereBetween('trial_ends_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    });
                    break;
                case 'next_3_months':
                    $query->where(function($q) {
                        $q->whereBetween('current_period_end', [now(), now()->addMonths(3)])
                          ->orWhereBetween('trial_ends_at', [now(), now()->addMonths(3)]);
                    });
                    break;
            }
        }

        // Filter by remaining days (Yeni)
        if (!empty($this->filterRemainingDays)) {
            switch ($this->filterRemainingDays) {
                case 'critical': // 24 saat altı
                    $query->where(function($q) {
                        $q->where('current_period_end', '<=', now()->addDay())
                          ->where('current_period_end', '>', now())
                          ->orWhere(function($q2) {
                              $q2->where('trial_ends_at', '<=', now()->addDay())
                                 ->where('trial_ends_at', '>', now());
                          });
                    });
                    break;
                case 'warning': // 7 gün altı
                    $query->where(function($q) {
                        $q->where('current_period_end', '<=', now()->addDays(7))
                          ->where('current_period_end', '>', now())
                          ->orWhere(function($q2) {
                              $q2->where('trial_ends_at', '<=', now()->addDays(7))
                                 ->where('trial_ends_at', '>', now());
                          });
                    });
                    break;
                case 'month': // 30 gün altı
                    $query->where(function($q) {
                        $q->where('current_period_end', '<=', now()->addDays(30))
                          ->where('current_period_end', '>', now())
                          ->orWhere(function($q2) {
                              $q2->where('trial_ends_at', '<=', now()->addDays(30))
                                 ->where('trial_ends_at', '>', now());
                          });
                    });
                    break;
            }
        }

        // Filter by auto renew (Yeni)
        if ($this->filterAutoRenew !== '') {
            $query->where('auto_renew', $this->filterAutoRenew === '1');
        }

        // Filter by trial status (Yeni)
        if (!empty($this->filterTrialStatus)) {
            switch ($this->filterTrialStatus) {
                case 'active_trial':
                    $query->where('has_trial', true)
                          ->where('trial_ends_at', '>', now());
                    break;
                case 'trial_to_premium':
                    $query->where('has_trial', true)
                          ->where('trial_ends_at', '<=', now())
                          ->where('status', 'active');
                    break;
                case 'trial_to_cancel':
                    $query->where('has_trial', true)
                          ->where('trial_ends_at', '<=', now())
                          ->whereIn('status', ['cancelled', 'expired']);
                    break;
            }
        }

        return $query->orderBy($this->sortField, $this->sortDirection)->paginate((int) $this->perPage);
    }

    #[Computed]
    public function plans()
    {
        return SubscriptionPlan::orderBy('sort_order')->get();
    }

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

    // Hızlı filtre metodları
    public function quickFilterCritical()
    {
        $this->filterRemainingDays = 'critical';
        $this->resetPage();
    }

    public function quickFilterWarning()
    {
        $this->filterRemainingDays = 'warning';
        $this->resetPage();
    }

    public function quickFilterToday()
    {
        $this->filterExpiryRange = 'today';
        $this->resetPage();
    }

    public function quickFilterAutoRenewOff()
    {
        $this->filterAutoRenew = '0';
        $this->resetPage();
    }

    public function quickFilterActiveTrial()
    {
        $this->filterTrialStatus = 'active_trial';
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterPlan', 'filterCycle', 'filterExpiryRange', 'filterRemainingDays', 'filterAutoRenew', 'filterTrialStatus']);
        $this->filterStatus = 'active'; // Varsayılan'a dön
        $this->resetPage();
    }

    public function showAllSubscriptions()
    {
        $this->filterStatus = '';
        $this->resetPage();
    }

    public function showActiveOnly()
    {
        $this->filterStatus = 'active';
        $this->resetPage();
    }

    public function cancel(int $id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->cancel();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('subscription::admin.subscriptions.cancelled'),
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('subscription::admin.livewire.subscription-component', [
            'subscriptions' => $this->subscriptions,
            'plans' => $this->plans,
            'stats' => $this->stats,
            'expiringStats' => $this->expiringStats,
            'planStats' => $this->planStats,
            'trialStats' => $this->trialStats,
            'revenueStats' => $this->revenueStats,
        ]);
    }
}
