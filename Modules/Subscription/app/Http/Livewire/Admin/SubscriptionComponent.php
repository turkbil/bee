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
    public $perPage = 25;

    #[Url]
    public $sortField = 'subscription_id';

    #[Url]
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterPlan' => ['except' => ''],
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    // Modal state
    public bool $showUserModal = false;
    public ?int $selectedUserId = null;
    public ?array $selectedUserData = null;

    // Bulk selection (modal içinde)
    public array $selectedSubscriptions = [];
    public bool $selectAllSubscriptions = false;

    // Modal filters
    public string $modalFilterStatus = '';

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

        return $query->orderBy($this->sortField, $this->sortDirection)->paginate((int) $this->perPage);
    }

    #[Computed]
    public function plans()
    {
        return SubscriptionPlan::orderBy('sort_order')->get();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterPlan']);
        $this->filterStatus = 'active'; // Varsayılan'a dön
        $this->resetPage();
    }

    /**
     * Kullanıcı detay modal'ını aç
     */
    public function openUserModal(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->selectedSubscriptions = [];
        $this->selectAllSubscriptions = false;
        $this->modalFilterStatus = '';
        $this->loadUserData();
        $this->showUserModal = true;
    }

    /**
     * Modal'ı kapat
     */
    public function closeUserModal(): void
    {
        $this->showUserModal = false;
        $this->selectedUserId = null;
        $this->selectedUserData = null;
        $this->selectedSubscriptions = [];
        $this->selectAllSubscriptions = false;
        $this->modalFilterStatus = '';
    }

    /**
     * Modal filtresi değişince veriyi yeniden yükle
     */
    public function updatedModalFilterStatus(): void
    {
        $this->selectedSubscriptions = [];
        $this->selectAllSubscriptions = false;
        $this->loadUserData();
    }

    /**
     * Tümünü seç/kaldır
     */
    public function updatedSelectAllSubscriptions(): void
    {
        if ($this->selectAllSubscriptions && $this->selectedUserData) {
            $this->selectedSubscriptions = collect($this->selectedUserData['subscriptions'])
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedSubscriptions = [];
        }
    }

    /**
     * Seçili abonelikleri toplu iptal et
     */
    public function bulkCancelSubscriptions(): void
    {
        if (empty($this->selectedSubscriptions)) {
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Lütfen en az bir abonelik seçin.',
                'type' => 'warning'
            ]);
            return;
        }

        $cancelled = 0;
        $errors = 0;

        foreach ($this->selectedSubscriptions as $id) {
            try {
                $subscription = Subscription::find($id);
                if ($subscription && in_array($subscription->status, ['active', 'trial', 'pending', 'pending_payment'])) {
                    $subscription->cancel();
                    $cancelled++;
                }
            } catch (\Exception $e) {
                $errors++;
                \Illuminate\Support\Facades\Log::error('Bulk cancel error', [
                    'subscription_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Reset selection
        $this->selectedSubscriptions = [];
        $this->selectAllSubscriptions = false;

        // Reload data
        $this->loadUserData();

        $this->dispatch('toast', [
            'title' => 'Toplu İptal',
            'message' => "{$cancelled} abonelik iptal edildi." . ($errors > 0 ? " ({$errors} hata)" : ''),
            'type' => $errors > 0 ? 'warning' : 'success'
        ]);
    }

    /**
     * Seçili abonelikleri toplu sil
     */
    public function bulkDeleteSubscriptions(): void
    {
        if (empty($this->selectedSubscriptions)) {
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Lütfen en az bir abonelik seçin.',
                'type' => 'warning'
            ]);
            return;
        }

        $deleted = 0;
        $errors = 0;

        // En son başlayandan başla (sondan başa sil) - current_period_start DESC
        $subscriptionsToDelete = Subscription::whereIn('subscription_id', $this->selectedSubscriptions)
            ->orderBy('current_period_start', 'desc')
            ->get();

        foreach ($subscriptionsToDelete as $subscription) {
            try {
                $subscription->deleteAndRechain();
                $deleted++;
            } catch (\Exception $e) {
                $errors++;
                \Illuminate\Support\Facades\Log::error('Bulk delete error', [
                    'subscription_id' => $subscription->subscription_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Reset selection
        $this->selectedSubscriptions = [];
        $this->selectAllSubscriptions = false;

        // Reload data
        $this->loadUserData();

        $this->dispatch('toast', [
            'title' => 'Toplu Silme',
            'message' => "{$deleted} abonelik silindi." . ($errors > 0 ? " ({$errors} hata)" : ''),
            'type' => $errors > 0 ? 'warning' : 'success'
        ]);
    }

    /**
     * Seçili abonelikleri toplu aktif et
     */
    public function bulkActivateSubscriptions(): void
    {
        if (empty($this->selectedSubscriptions)) {
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Lütfen en az bir abonelik seçin.',
                'type' => 'warning'
            ]);
            return;
        }

        $activated = 0;
        $errors = 0;

        foreach ($this->selectedSubscriptions as $id) {
            try {
                $subscription = Subscription::find($id);
                if ($subscription && in_array($subscription->status, ['pending', 'pending_payment'])) {
                    // Ödeme kontrolü (sadece order varsa)
                    $orderId = $subscription->metadata['order_id'] ?? null;
                    $paymentOk = true;
                    if ($orderId && class_exists(\Modules\Cart\App\Models\Order::class)) {
                        $order = \Modules\Cart\App\Models\Order::find($orderId);
                        if ($order && !in_array($order->payment_status, ['paid', 'completed'])) {
                            $paymentOk = false;
                        }
                    }
                    // Order yoksa admin onayı ile aktif edilebilir
                    if ($paymentOk || !$orderId) {
                        $subscription->update(['status' => 'active']);
                        $activated++;
                    }
                }
            } catch (\Exception $e) {
                $errors++;
            }
        }

        // Reset selection
        $this->selectedSubscriptions = [];
        $this->selectAllSubscriptions = false;

        // Reload data
        $this->loadUserData();

        $this->dispatch('toast', [
            'title' => 'Toplu Aktivasyon',
            'message' => "{$activated} abonelik aktif edildi." . ($errors > 0 ? " ({$errors} hata)" : ''),
            'type' => $errors > 0 ? 'warning' : 'success'
        ]);
    }

    /**
     * Kullanıcı verilerini yükle
     */
    protected function loadUserData(): void
    {
        if (!$this->selectedUserId) {
            return;
        }

        $user = \App\Models\User::find($this->selectedUserId);
        if (!$user) {
            return;
        }

        // Tüm subscriptions (filtre uygula)
        $query = Subscription::where('user_id', $this->selectedUserId)
            ->with('plan');

        // Modal filtresi uygula
        if (!empty($this->modalFilterStatus)) {
            $query->where('status', $this->modalFilterStatus);
        }

        $subscriptions = $query->orderBy('current_period_start', 'asc')
            ->get()
            ->map(function ($sub) {
                // Order bilgisi (metadata'dan order_id)
                $orderId = $sub->metadata['order_id'] ?? null;
                $order = null;
                $paymentStatus = null;
                $paymentMethod = null;
                $isCorporatePayment = false;
                $corporatePayerName = null;

                if ($orderId && class_exists(\Modules\Cart\App\Models\Order::class)) {
                    $order = \Modules\Cart\App\Models\Order::find($orderId);
                    if ($order) {
                        $paymentStatus = $order->payment_status;
                        $paymentMethod = $order->payment_method;

                        // Kurumsal ödeme kontrolü: Ödemeyi yapan user_id farklıysa
                        if ($order->user_id !== $sub->user_id) {
                            $isCorporatePayment = true;
                            $payer = \App\Models\User::find($order->user_id);
                            $corporatePayerName = $payer?->name ?? 'Kurumsal Hesap';
                        }

                        // Metadata'da corporate bilgisi varsa
                        if ($sub->metadata['corporate'] ?? false) {
                            $isCorporatePayment = true;
                        }
                    }
                }

                // Ödeme durumu etiketi
                if (!$orderId) {
                    // Order yok - manuel oluşturulmuş
                    if ($sub->status === 'pending_payment') {
                        $paymentLabel = 'Ödeme Bekleniyor';
                        $paymentStatus = 'pending';
                    } else {
                        $paymentLabel = 'Manuel Onay';
                        $paymentStatus = 'manual'; // Özel durum
                    }
                } else {
                    // Order var - gerçek ödeme
                    $paymentLabel = match($paymentStatus) {
                        'paid', 'completed' => 'Ödendi',
                        'pending' => 'Bekliyor',
                        'failed' => 'Başarısız',
                        'refunded' => 'İade',
                        default => $sub->status === 'pending_payment' ? 'Ödeme Bekleniyor' : 'Bilinmiyor'
                    };
                }

                // Sadece pending_payment aktif edilebilir (ödeme onayı için)
                // pending zaten ödenmiş, zincirde sırada - otomatik devreye girecek
                $canActivate = $sub->status === 'pending_payment';

                return [
                    'id' => $sub->subscription_id,
                    'plan_title' => is_array($sub->plan?->title)
                        ? ($sub->plan->title['tr'] ?? $sub->plan->title['en'] ?? 'Bilinmiyor')
                        : ($sub->plan?->title ?? 'Bilinmiyor'),
                    'status' => $sub->status,
                    'has_trial' => $sub->has_trial,
                    'trial_ends_at' => $sub->trial_ends_at?->format('d.m.Y H:i'),
                    'started_at' => $sub->started_at?->format('d.m.Y'),
                    'current_period_start' => $sub->current_period_start?->format('d.m.Y'),
                    'current_period_end' => $sub->current_period_end?->format('d.m.Y H:i'),
                    'price' => number_format((float) ($sub->price_per_cycle ?? 0), 2) . ' ' . ($sub->currency ?? 'TRY'),
                    'cycle_label' => is_array($sub->cycle_metadata['label'] ?? null)
                        ? ($sub->cycle_metadata['label']['tr'] ?? $sub->cycle_metadata['label']['en'] ?? $sub->billing_cycle ?? '-')
                        : ($sub->cycle_metadata['label'] ?? $sub->billing_cycle ?? '-'),
                    'chain_position' => $sub->chain_position,
                    'can_cancel' => in_array($sub->status, ['active', 'trial', 'pending', 'pending_payment']),
                    'can_delete' => true,
                    'can_activate' => $canActivate,
                    // Kalan gün hesaplama:
                    // - active/trial: Şu andan bitiş tarihine
                    // - pending (ödenmiş, sırada): Kendi süresi (başlangıç-bitiş arası)
                    // - pending_payment: 0 (yok sayılır, ödeme yapılana kadar)
                    'days_left' => match($sub->status) {
                        'pending_payment' => 0, // Ödeme bekliyor = yok sayılır
                        'pending' => ($sub->current_period_start && $sub->current_period_end)
                            ? (int) $sub->current_period_start->diffInDays($sub->current_period_end)
                            : 0,
                        default => ($sub->current_period_end && $sub->current_period_end->isFuture())
                            ? (int) now()->diffInDays($sub->current_period_end, false)
                            : 0,
                    },
                    // Ödeme bilgileri
                    'order_id' => $orderId,
                    'order_number' => $order?->order_number,
                    'payment_status' => $paymentStatus,
                    'payment_label' => $paymentLabel,
                    'payment_method' => $paymentMethod,
                    'is_corporate_payment' => $isCorporatePayment,
                    'corporate_payer_name' => $corporatePayerName,
                ];
            })
            ->toArray();

        // Kurumsal bilgi
        $corporateAccount = \Modules\Muzibu\App\Models\MuzibuCorporateAccount::where('user_id', $this->selectedUserId)->first();
        $isCorporate = $corporateAccount !== null;
        $corporateInfo = null;

        if ($corporateAccount) {
            if ($corporateAccount->parent_id) {
                // Alt hesap - parent'tan bilgi al
                $parent = $corporateAccount->parent;
                $corporateInfo = [
                    'type' => 'member',
                    'company_name' => $parent?->company_name ?? $parent?->owner?->name ?? 'Kurumsal',
                    'branch_name' => $corporateAccount->branch_name,
                    'code' => $parent?->corporate_code,
                ];
            } else {
                // Ana hesap
                $corporateInfo = [
                    'type' => 'owner',
                    'company_name' => $corporateAccount->company_name ?? $user->name,
                    'code' => $corporateAccount->corporate_code,
                    'members_count' => $corporateAccount->children()->count(),
                ];
            }
        }

        // Siparişler - subscription'ların metadata'sındaki order_id'lerden çek
        $orders = [];
        if (class_exists(\Modules\Cart\App\Models\Order::class)) {
            // Subscription'lardan order_id'leri topla
            $orderIds = collect($subscriptions)
                ->pluck('order_id')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (!empty($orderIds)) {
                $orders = \Modules\Cart\App\Models\Order::whereIn('order_id', $orderIds)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($order) use ($subscriptions) {
                        // Bu order hangi subscription'a ait?
                        $relatedSub = collect($subscriptions)->firstWhere('order_id', $order->order_id);
                        return [
                            'id' => $order->order_id,
                            'number' => $order->order_number,
                            'status' => $order->status,
                            'payment_status' => $order->payment_status,
                            'payment_method' => $order->payment_method,
                            'total' => number_format((float) ($order->total_amount ?? 0), 2) . ' ' . ($order->currency ?? 'TRY'),
                            'date' => $order->created_at->format('d.m.Y H:i'),
                            'plan_title' => $relatedSub['plan_title'] ?? 'Abonelik',
                        ];
                    })
                    ->toArray();
            }
        }

        $this->selectedUserData = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'subscription_expires_at' => $user->subscription_expires_at?->format('d.m.Y H:i'),
                'total_days_left' => $user->subscription_expires_at && $user->subscription_expires_at->isFuture()
                    ? (int) now()->diffInDays($user->subscription_expires_at, false)
                    : 0,
            ],
            'subscriptions' => $subscriptions,
            'is_corporate' => $isCorporate,
            'corporate_info' => $corporateInfo,
            'orders' => $orders,
            'stats' => [
                'total_subscriptions' => count($subscriptions),
                // Aktif sayısı: Sadece active ve pending (pending_payment hariç!)
                'active_count' => collect($subscriptions)->whereIn('status', ['active', 'pending'])->count(),
                // Ödenmiş toplam: active, pending, cancelled, expired (pending_payment HARİÇ!)
                'total_paid' => Subscription::where('user_id', $this->selectedUserId)
                    ->whereIn('status', ['active', 'cancelled', 'expired', 'pending'])
                    ->sum('price_per_cycle'),
                // Ödeme bekleyen abonelikler (bunlar yok sayılır, etkisiz)
                'pending_payment_count' => Subscription::where('user_id', $this->selectedUserId)
                    ->where('status', 'pending_payment')
                    ->count(),
                'pending_payment_total' => Subscription::where('user_id', $this->selectedUserId)
                    ->where('status', 'pending_payment')
                    ->sum('price_per_cycle'),
            ],
        ];
    }

    /**
     * Tek bir subscription'ı iptal et
     */
    public function cancelSubscription(int $subscriptionId): void
    {
        try {
            $subscription = Subscription::findOrFail($subscriptionId);
            $subscription->cancel();

            // Modal verilerini yenile
            $this->loadUserData();

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Abonelik iptal edildi.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'İptal işlemi başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Tek bir subscription'ı sil ve zinciri yeniden düzenle
     */
    public function deleteSubscription(int $subscriptionId): void
    {
        try {
            $subscription = Subscription::findOrFail($subscriptionId);
            $userId = $subscription->user_id;

            // deleteAndRechain metodu ile sil (zincir otomatik düzenlenir)
            $subscription->deleteAndRechain();

            // Modal verilerini yenile
            $this->loadUserData();

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Abonelik silindi ve zincir yeniden düzenlendi.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Silme işlemi başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Subscription'ı aktif et (pending → active)
     * Sadece ödemesi tamamlanmış abonelikler aktif edilebilir
     */
    public function activateSubscription(int $subscriptionId): void
    {
        try {
            $subscription = Subscription::findOrFail($subscriptionId);

            if (!in_array($subscription->status, ['pending', 'pending_payment'])) {
                throw new \Exception('Sadece bekleyen abonelikler aktif edilebilir.');
            }

            // Ödeme kontrolü (sadece order varsa kontrol et)
            $orderId = $subscription->metadata['order_id'] ?? null;
            if ($orderId && class_exists(\Modules\Cart\App\Models\Order::class)) {
                $order = \Modules\Cart\App\Models\Order::find($orderId);
                if ($order && !in_array($order->payment_status, ['paid', 'completed'])) {
                    throw new \Exception('Ödeme henüz tamamlanmamış. Ödeme durumu: ' . ($order->payment_status ?? 'bilinmiyor'));
                }
            }
            // Order yoksa admin onayı ile aktif edilebilir (manuel oluşturulmuş)

            $subscription->update(['status' => 'active']);

            // Modal verilerini yenile
            $this->loadUserData();

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Abonelik aktif edildi.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Aboneliği HEMEN sonlandır - erişim anında kesilir
     */
    public function terminateNow(int $id)
    {
        try {
            $subscription = Subscription::findOrFail($id);

            // Bitiş tarihini şu ana çek - erişim hemen kesilir
            $subscription->update([
                'current_period_end' => now(),
                'status' => 'expired',
            ]);

            // Kullanıcının subscription_expires_at güncelle
            if ($subscription->customer) {
                $subscription->customer->recalculateSubscriptionExpiry();
            }

            $this->dispatch('toast', [
                'title' => 'Sonlandırıldı',
                'message' => 'Abonelik hemen sonlandırıldı. Kullanıcının erişimi kesildi.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('subscription::admin.livewire.subscription-component', [
            'subscriptions' => $this->subscriptions,
            'plans' => $this->plans,
        ]);
    }
}
