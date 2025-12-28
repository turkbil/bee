<?php

namespace Modules\Muzibu\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Muzibu\App\Models\{Song, SongPlay, Playlist, MuzibuCorporateAccount};
use Modules\Favorite\App\Models\Favorite;
use Modules\Subscription\App\Services\SubscriptionService;
use Modules\Subscription\App\Models\Subscription;

class DashboardController extends Controller
{
    /**
     * Dashboard ana sayfası
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Subscription bilgisi
        $subscriptionService = app(SubscriptionService::class);
        $access = $subscriptionService->checkUserAccess($user);

        // İstatistikler
        $stats = $this->getUserStats($user->id);

        // Son dinlenenler (10 adet)
        $recentlyPlayed = $this->getRecentlyPlayed($user->id, 10);

        // Favoriler (6 adet preview)
        $favorites = $this->getFavoriteSongs($user->id, 6);

        // Playlistler
        $playlists = $this->getUserPlaylists($user->id, 6);

        // Kurumsal bilgi
        $corporate = $this->getCorporateInfo($user->id);

        // Kalan süre hesaplama (users.subscription_expires_at'dan)
        $timeLeft = $this->calculateTimeLeft($user->subscription_expires_at);

        // Abonelik bilgileri
        $subscriptionInfo = $this->getSubscriptionInfo($user->id);

        return view('themes.muzibu.dashboard', compact(
            'user',
            'access',
            'stats',
            'recentlyPlayed',
            'favorites',
            'playlists',
            'corporate',
            'timeLeft',
            'subscriptionInfo'
        ));
    }

    /**
     * SPA için Dashboard API
     */
    public function apiIndex(Request $request)
    {
        $user = auth()->user();

        $subscriptionService = app(SubscriptionService::class);
        $access = $subscriptionService->checkUserAccess($user);
        $stats = $this->getUserStats($user->id);
        $recentlyPlayed = $this->getRecentlyPlayed($user->id, 10);
        $favorites = $this->getFavoriteSongs($user->id, 6);
        $playlists = $this->getUserPlaylists($user->id, 6);
        $corporate = $this->getCorporateInfo($user->id);
        $timeLeft = $this->calculateTimeLeft($user->subscription_expires_at);
        $subscriptionInfo = $this->getSubscriptionInfo($user->id);

        $html = view('themes.muzibu.partials.dashboard-content', compact(
            'user',
            'access',
            'stats',
            'recentlyPlayed',
            'favorites',
            'playlists',
            'corporate',
            'timeLeft',
            'subscriptionInfo'
        ))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => 'Dashboard - Muzibu',
                'description' => 'Kişisel müzik paneliniz'
            ]
        ]);
    }

    /**
     * Dinleme geçmişi sayfası
     */
    public function history(Request $request)
    {
        $user = auth()->user();

        $history = SongPlay::where('user_id', $user->id)
            ->with(['song.album.artist', 'song.album.coverMedia', 'song.coverMedia'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('themes.muzibu.listening-history', compact('history'));
    }

    /**
     * Dinleme geçmişi API
     */
    public function apiHistory(Request $request)
    {
        $user = auth()->user();

        $history = SongPlay::where('user_id', $user->id)
            ->with(['song.album.artist', 'song.album.coverMedia', 'song.coverMedia'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $html = view('themes.muzibu.partials.listening-history-content', compact('history'))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => 'Dinleme Geçmişi - Muzibu',
                'description' => 'Tüm dinleme geçmişiniz'
            ]
        ]);
    }

    /**
     * Abonelik geçmişi sayfası
     */
    public function subscriptions(Request $request)
    {
        $user = auth()->user();
        $subscriptionInfo = $this->getSubscriptionInfo($user->id);
        $timeLeft = $this->calculateTimeLeft($user->subscription_expires_at);
        $allPayments = $this->getPaymentHistory($user->id);

        return view('themes.muzibu.my-subscriptions', compact(
            'user',
            'subscriptionInfo',
            'timeLeft',
            'allPayments'
        ));
    }

    /**
     * Abonelik geçmişi API
     */
    public function apiSubscriptions(Request $request)
    {
        $user = auth()->user();
        $subscriptionInfo = $this->getSubscriptionInfo($user->id);
        $timeLeft = $this->calculateTimeLeft($user->subscription_expires_at);
        $allPayments = $this->getPaymentHistory($user->id);

        $html = view('themes.muzibu.partials.my-subscriptions-content', compact(
            'user',
            'subscriptionInfo',
            'timeLeft',
            'allPayments'
        ))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => 'Aboneliklerim - Muzibu',
                'description' => 'Abonelik ve ödeme geçmişiniz'
            ]
        ]);
    }

    /**
     * Ödeme geçmişi - onaylanan ödemeler + manuel abonelikler
     */
    private function getPaymentHistory(int $userId): array
    {
        $payments = collect();

        // 1. Gerçek ödemeler (paid/completed orders)
        if (class_exists(\Modules\Cart\App\Models\Order::class)) {
            $orders = \Modules\Cart\App\Models\Order::where('user_id', $userId)
                ->whereIn('payment_status', ['paid', 'completed'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    return [
                        'type' => 'order',
                        'order_number' => $order->order_number,
                        'total' => $order->total_amount,
                        'currency' => $order->currency ?? 'TRY',
                        'payment_method' => $order->metadata['payment_method'] ?? 'Kredi Kartı',
                        'created_at' => $order->created_at,
                    ];
                });
            $payments = $payments->merge($orders);
        }

        // 2. Manuel abonelikler (order_id yok, active/pending durumda)
        $manualSubs = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'pending'])
            ->where(function($q) {
                $q->whereNull('metadata->order_id')
                  ->orWhere('metadata->order_id', '');
            })
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sub) {
                return [
                    'type' => 'manual',
                    'order_number' => $sub->subscription_number,
                    'total' => $sub->price_per_cycle,
                    'currency' => $sub->currency ?? 'TRY',
                    'payment_method' => 'Manuel Onay',
                    'created_at' => $sub->created_at,
                    'plan_name' => $sub->plan?->title_text ?? 'Premium',
                ];
            });
        $payments = $payments->merge($manualSubs);

        // Tarihe göre sırala
        return $payments->sortByDesc('created_at')->values()->toArray();
    }

    /**
     * Kullanıcı istatistikleri
     */
    private function getUserStats(int $userId): array
    {
        return [
            'playlists_count' => DB::table('muzibu_playlists')->where('user_id', $userId)->count(),
            'favorites_count' => DB::table('favorites')->where('user_id', $userId)->count(),
            'plays_count' => DB::table('muzibu_song_plays')->where('user_id', $userId)->count(),
            'plays_this_week' => DB::table('muzibu_song_plays')
                ->where('user_id', $userId)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];
    }

    /**
     * Son dinlenen şarkılar
     */
    private function getRecentlyPlayed(int $userId, int $limit = 10)
    {
        return SongPlay::where('user_id', $userId)
            ->with(['song.album.artist', 'song.album.coverMedia', 'song.coverMedia'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->unique('song_id') // Aynı şarkıyı tekrar gösterme
            ->values();
    }

    /**
     * Favori şarkılar
     */
    private function getFavoriteSongs(int $userId, int $limit = 6)
    {
        return Favorite::where('user_id', $userId)
            ->where('favoritable_type', Song::class)
            ->with(['favoritable.album.artist', 'favoritable.album.coverMedia', 'favoritable.coverMedia'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn($fav) => $fav->favoritable)
            ->filter(); // null olanları çıkar
    }

    /**
     * Kullanıcı playlistleri
     */
    private function getUserPlaylists(int $userId, int $limit = 6)
    {
        return Playlist::where('user_id', $userId)
            ->where('is_active', 1)
            ->withCount(['songs' => fn($q) => $q->where('is_active', 1)])
            ->with('coverMedia')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Kurumsal bilgi
     */
    private function getCorporateInfo(int $userId): ?array
    {
        $account = MuzibuCorporateAccount::where('user_id', $userId)->first();

        if (!$account) {
            return null;
        }

        $isOwner = $account->isParent();

        if ($isOwner) {
            // Ana şube - kendi bilgileri + üye sayısı
            return [
                'is_owner' => true,
                'account' => $account,
                'company_name' => $account->company_name ?? $account->owner->name,
                'corporate_code' => $account->corporate_code,
                'members_count' => $account->children()->count(),
                'is_active' => $account->is_active,
            ];
        } else {
            // Alt şube - bağlı olduğu kurum
            $parent = $account->parent;
            return [
                'is_owner' => false,
                'account' => $account,
                'parent_account' => $parent,
                'company_name' => $parent->company_name ?? $parent->owner->name ?? 'Kurum',
                'branch_name' => $account->branch_name,
                'is_active' => $account->is_active && $parent->is_active,
            ];
        }
    }

    /**
     * Kalan süre hesaplama
     * @param \Carbon\Carbon|null $expiresAt users.subscription_expires_at
     */
    private function calculateTimeLeft($expiresAt): array
    {
        if (!$expiresAt) {
            return ['days' => 0, 'hours' => 0, 'minutes' => 0, 'expired' => true];
        }

        // Carbon instance değilse parse et
        $expiry = $expiresAt instanceof \Carbon\Carbon
            ? $expiresAt
            : \Carbon\Carbon::parse($expiresAt);

        $now = now();

        if ($expiry->isPast()) {
            return ['days' => 0, 'hours' => 0, 'minutes' => 0, 'expired' => true];
        }

        $diffInSeconds = (int) floor($now->diffInSeconds($expiry, false));

        return [
            'days' => (int) floor($diffInSeconds / 86400),
            'hours' => (int) floor(($diffInSeconds % 86400) / 3600),
            'minutes' => (int) floor(($diffInSeconds % 3600) / 60),
            'expired' => false,
        ];
    }

    /**
     * Abonelik bilgileri - users.subscription_expires_at + ödemeler
     */
    private function getSubscriptionInfo(int $userId): array
    {
        $user = \App\Models\User::find($userId);

        // Ödeme bekleyen abonelikler (bunlar önemli - ödeme butonu gösterilecek)
        $pendingPaymentSubscriptions = Subscription::where('user_id', $userId)
            ->where('status', 'pending_payment')
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ödeme geçmişi - kullanıcının tüm ödemeleri
        $paymentHistory = [];
        if (class_exists(\Modules\Cart\App\Models\Order::class)) {
            $paymentHistory = \Modules\Cart\App\Models\Order::where('user_id', $userId)
                ->whereIn('payment_status', ['paid', 'completed'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'order_number' => $order->order_number,
                        'total' => $order->total_amount,
                        'currency' => $order->currency ?? 'TRY',
                        'payment_status' => $order->payment_status,
                        'payment_method' => $order->metadata['payment_method'] ?? null,
                        'created_at' => $order->created_at,
                    ];
                })
                ->toArray();
        }

        return [
            'pending_payment' => $pendingPaymentSubscriptions->map(function ($sub) {
                return [
                    'id' => $sub->subscription_id,
                    'plan_name' => $sub->plan?->title_text ?? 'Premium',
                    'cycle_label' => $sub->getCycleLabel() ?? $sub->billing_cycle,
                    'price' => $sub->price_per_cycle,
                    'currency' => $sub->currency ?? 'TRY',
                    'created_at' => $sub->created_at,
                ];
            })->toArray(),
            'payments' => $paymentHistory,
            'has_subscription' => $user && $user->subscription_expires_at && $user->subscription_expires_at->isFuture(),
        ];
    }
}
