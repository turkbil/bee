<?php

namespace Modules\Muzibu\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\MuzibuCorporateAccount;
use Modules\Muzibu\App\Models\SongPlay;
use Modules\Muzibu\App\Services\MuzibuCorporateService;
use Modules\Subscription\App\Models\SubscriptionPlan;
use Modules\Subscription\App\Models\Subscription;
use Modules\Cart\App\Services\CartService;
use Illuminate\Support\Facades\DB;

class CorporateFrontController extends Controller
{
    protected MuzibuCorporateService $corporateService;

    public function __construct(MuzibuCorporateService $corporateService)
    {
        $this->corporateService = $corporateService;
    }

    /**
     * Kurumsal tanıtım sayfası (public)
     */
    public function index(Request $request)
    {
        // Giriş yapmış kullanıcının kurumsal durumu
        $userCorporate = null;
        if (auth()->check()) {
            $userCorporate = MuzibuCorporateAccount::where('user_id', auth()->id())->first();
        }

        return view('themes.muzibu.corporate.index', compact('userCorporate'));
    }

    /**
     * Kurumsal tanıtım API (SPA)
     */
    public function apiIndex(Request $request)
    {
        $userCorporate = null;
        if (auth()->check()) {
            $userCorporate = MuzibuCorporateAccount::where('user_id', auth()->id())->first();
        }

        $html = view('themes.muzibu.partials.corporate-index-content', compact('userCorporate'))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => 'Kurumsal Muzibu',
                'description' => 'Şirketiniz için müzik çözümü'
            ]
        ]);
    }

    /**
     * Ana şube dashboard
     * SADECE ana şubeler (parent_id = null) erişebilir
     * Alt üyeler my-corporate'a, kurumsal olmayanlar join'e yönlendirilir
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        // Önce kullanıcının herhangi bir kurumsal hesabı var mı kontrol et
        $anyAccount = MuzibuCorporateAccount::where('user_id', $user->id)->first();

        if (!$anyAccount) {
            // Kurumsal hesabı yok → join sayfasına
            return redirect()->route('muzibu.corporate.join')
                ->with('info', 'Bir kurumsal hesaba katılmak için davet kodu girin.');
        }

        // Kurumsal hesabı var ama alt üye mi?
        if ($anyAccount->parent_id !== null) {
            // Alt üye → my-corporate sayfasına
            return redirect()->route('muzibu.corporate.my')
                ->with('info', 'Kurumsal üyelik bilgilerinizi burada görüntüleyebilirsiniz.');
        }

        // Ana şube - dashboard göster
        $account = $anyAccount->load(['children.owner']);
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 50;
        $branchStats = $this->getBranchStats($account, $page, $perPage);

        return view('themes.muzibu.corporate.dashboard', compact('account', 'branchStats', 'page', 'perPage'));
    }

    /**
     * Ana şube dashboard API (SPA)
     * SADECE ana şubeler erişebilir
     */
    public function apiDashboard(Request $request)
    {
        $user = auth()->user();

        // Önce herhangi bir kurumsal hesap var mı
        $anyAccount = MuzibuCorporateAccount::where('user_id', $user->id)->first();

        if (!$anyAccount) {
            return response()->json([
                'error' => true,
                'message' => 'Kurumsal hesabınız bulunmuyor.',
                'redirect' => '/corporate/join'
            ], 403);
        }

        // Alt üye ise my-corporate'a yönlendir
        if ($anyAccount->parent_id !== null) {
            return response()->json([
                'error' => true,
                'message' => 'Bu sayfaya erişim yetkiniz yok.',
                'redirect' => '/corporate/my-corporate'
            ], 403);
        }

        // Ana şube - dashboard ver
        $account = $anyAccount->load(['children.owner']);
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 50;
        $branchStats = $this->getBranchStats($account, $page, $perPage);
        $html = view('themes.muzibu.partials.corporate-dashboard-content', compact('account', 'branchStats', 'page', 'perPage'))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => ($account->company_name ?? 'Kurumsal') . ' - Yönetim Paneli',
                'description' => 'Kurumsal hesap yönetimi'
            ]
        ]);
    }

    /**
     * Şube istatistiklerini getir (N+1 query optimized + Pagination)
     */
    private function getBranchStats(MuzibuCorporateAccount $parentAccount, int $page = 1, int $perPage = 50): array
    {
        // Toplam üye sayısı (pagination için)
        $totalChildren = $parentAccount->children()->count();
        $totalMembers = $totalChildren + 1; // +1 for owner

        // Pagination hesapla
        $totalPages = max(1, ceil($totalMembers / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        // İlk sayfada owner her zaman gösterilir
        $showOwner = ($page === 1);
        $childrenOffset = $showOwner ? max(0, $offset - 1) : $offset;
        $childrenLimit = $showOwner ? ($perPage - 1) : $perPage;

        // Paginated children
        $paginatedChildren = $parentAccount->children()
            ->with('owner')
            ->skip($childrenOffset)
            ->take($childrenLimit)
            ->get();

        // Sadece gösterilecek üyelerin user_id'leri
        $displayUserIds = $paginatedChildren->pluck('user_id')->toArray();
        if ($showOwner) {
            array_unshift($displayUserIds, $parentAccount->user_id);
        }

        // 🚀 TOPLAM İSTATİSTİKLER (tüm üyeler için - hafif sorgu)
        $allUserIds = $parentAccount->children()->pluck('user_id')->push($parentAccount->user_id)->toArray();

        // Toplam dinleme sayısı
        $totalPlaysCount = SongPlay::whereIn('user_id', $allUserIds)->count();

        // Haftalık dinleme
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyPlaysCount = SongPlay::whereIn('user_id', $allUserIds)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Toplam süre (saniye) - optimize: sadece sum
        $totalSeconds = (int) SongPlay::whereIn('user_id', $allUserIds)
            ->join('muzibu_songs', 'muzibu_song_plays.song_id', '=', 'muzibu_songs.song_id')
            ->sum('muzibu_songs.duration');

        $totalStats = [
            'total_members' => $totalMembers,
            'total_plays' => $totalPlaysCount,
            'weekly_plays' => $weeklyPlaysCount,
            'total_hours' => round($totalSeconds / 3600, 1),
        ];

        // 🚀 DETAYLI İSTATİSTİKLER (sadece görünen üyeler için)
        $stats = [];

        if (!empty($displayUserIds)) {
            // Song plays for displayed users
            $allPlays = SongPlay::whereIn('user_id', $displayUserIds)
                ->select('user_id', 'created_at', 'song_id')
                ->get()
                ->groupBy('user_id');

            // Son dinlenen şarkılar
            $lastPlays = SongPlay::whereIn('user_id', $displayUserIds)
                ->select('user_id', 'song_id', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('user_id')
                ->map(fn($plays) => $plays->first());

            // Eager load songs
            $songIds = $lastPlays->pluck('song_id')->filter()->unique()->toArray();
            if (!empty($songIds)) {
                $songs = \Modules\Muzibu\App\Models\Song::whereIn('song_id', $songIds)
                    ->with(['album.artist', 'coverMedia', 'album.coverMedia'])
                    ->get()
                    ->keyBy('song_id');

                foreach ($lastPlays as $userId => $play) {
                    if ($play && isset($songs[$play->song_id])) {
                        $play->setRelation('song', $songs[$play->song_id]);
                    }
                }
            }

            // Song durations
            $songDurations = \Modules\Muzibu\App\Models\Song::whereIn('song_id',
                $allPlays->flatten()->pluck('song_id')->filter()->unique()
            )->pluck('duration', 'song_id');

            // Subscriptions
            $subscriptions = DB::table('subscriptions')
                ->whereIn('user_id', $displayUserIds)
                ->where('status', 'active')
                ->where('current_period_end', '>', now())
                ->select('user_id', 'current_period_end')
                ->orderBy('current_period_end', 'desc')
                ->get()
                ->groupBy('user_id')
                ->map(fn($subs) => $subs->first());

            // Owner stats (sadece ilk sayfada)
            if ($showOwner) {
                $stats['owner'] = $this->buildMemberStats(
                    $parentAccount,
                    true,
                    $allPlays->get($parentAccount->user_id, collect()),
                    $lastPlays->get($parentAccount->user_id),
                    $subscriptions->get($parentAccount->user_id),
                    $songDurations,
                    $weekStart,
                    $weekEnd
                );
            }

            // Children stats
            foreach ($paginatedChildren as $child) {
                $stats[$child->id] = $this->buildMemberStats(
                    $child,
                    false,
                    $allPlays->get($child->user_id, collect()),
                    $lastPlays->get($child->user_id),
                    $subscriptions->get($child->user_id),
                    $songDurations,
                    $weekStart,
                    $weekEnd
                );
            }
        }

        return [
            'members' => $stats,
            'totals' => $totalStats,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
                'total_items' => $totalMembers,
                'has_prev' => $page > 1,
                'has_next' => $page < $totalPages,
            ],
        ];
    }

    /**
     * Tek üye için istatistik hesapla (DEPRECATED - use buildMemberStats instead)
     */
    private function getMemberStats(MuzibuCorporateAccount $account, bool $isOwner = false): array
    {
        $userId = $account->user_id;

        // Son dinlenen şarkı
        $lastPlay = SongPlay::where('user_id', $userId)
            ->with(['song.album.artist', 'song.coverMedia', 'song.album.coverMedia'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Toplam dinleme
        $totalPlays = SongPlay::where('user_id', $userId)->count();

        // Bu hafta dinleme
        $weeklyPlays = SongPlay::where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // 🔥 FIX: Gerçek şarkı sürelerini kullan (SANİYE cinsinden)
        $totalSeconds = (int) SongPlay::where('user_id', $userId)
            ->join('muzibu_songs', 'muzibu_song_plays.song_id', '=', 'muzibu_songs.song_id')
            ->sum('muzibu_songs.duration');

        // Üyelik durumu (tenant db'den - subscriptions tenant'a ait)
        // 🔥 FIX: expires_at yerine current_period_end kullan (expires_at NULL olabiliyor)
        $subscription = DB::table('subscriptions')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('current_period_end', '>', now())
            ->orderBy('current_period_end', 'desc')
            ->first();

        $subscriptionInfo = null;
        if ($subscription) {
            $expiresAt = \Carbon\Carbon::parse($subscription->current_period_end);
            // 🔥 FIX: diffInDays negatif olabilir, max(0) ile sınırla
            $daysLeft = (int) now()->diffInDays($expiresAt, false);
            $subscriptionInfo = [
                'is_active' => true,
                'days_left' => max(0, $daysLeft),
                'ends_at' => $expiresAt->format('d.m.Y'),
            ];
        } else {
            $subscriptionInfo = [
                'is_active' => false,
                'days_left' => 0,
                'ends_at' => null,
            ];
        }

        return [
            'account' => $account,
            'user' => $account->owner,
            'is_owner' => $isOwner,
            'last_play' => $lastPlay,
            'last_play_time' => $lastPlay ? $lastPlay->created_at->diffForHumans() : null,
            'total_plays' => $totalPlays,
            'weekly_plays' => $weeklyPlays,
            'total_seconds' => $totalSeconds,
            'total_hours' => round($totalSeconds / 3600, 1), // SANİYE → SAAT
            'subscription' => $subscriptionInfo,
        ];
    }

    /**
     * Optimized: Batch data ile member stats oluştur
     */
    private function buildMemberStats(
        MuzibuCorporateAccount $account,
        bool $isOwner,
        $userPlays,
        $lastPlay,
        $subscription,
        $songDurations,
        $weekStart,
        $weekEnd
    ): array {
        // Toplam dinleme
        $totalPlays = $userPlays->count();

        // Bu hafta dinleme
        $weeklyPlays = $userPlays->whereBetween('created_at', [$weekStart, $weekEnd])->count();

        // 🔥 FIX: Gerçek şarkı sürelerini kullan (SANİYE cinsinden)
        $totalSeconds = 0;
        foreach ($userPlays as $play) {
            $totalSeconds += $songDurations->get($play->song_id, 210); // Default 3.5dk = 210sn
        }

        // Subscription bilgisi
        $subscriptionInfo = null;
        if ($subscription) {
            $expiresAt = \Carbon\Carbon::parse($subscription->current_period_end);
            // 🔥 FIX: diffInDays negatif olabilir, max(0) ile sınırla
            $daysLeft = (int) now()->diffInDays($expiresAt, false);
            $subscriptionInfo = [
                'is_active' => true,
                'days_left' => max(0, $daysLeft),
                'ends_at' => $expiresAt->format('d.m.Y'),
            ];
        } else {
            $subscriptionInfo = [
                'is_active' => false,
                'days_left' => 0,
                'ends_at' => null,
            ];
        }

        return [
            'account' => $account,
            'user' => $account->owner,
            'is_owner' => $isOwner,
            'last_play' => $lastPlay,
            'last_play_time' => $lastPlay ? $lastPlay->created_at->diffForHumans() : null,
            'total_plays' => $totalPlays,
            'weekly_plays' => $weeklyPlays,
            'total_seconds' => $totalSeconds,
            'total_hours' => round($totalSeconds / 3600, 1), // SANİYE → SAAT
            'subscription' => $subscriptionInfo,
        ];
    }

    /**
     * Kod ile katıl formu
     */
    public function join(Request $request)
    {
        $user = auth()->user();

        // Zaten kurumsal üye mi?
        $existingAccount = MuzibuCorporateAccount::where('user_id', $user->id)->first();

        return view('themes.muzibu.corporate.join', compact('existingAccount'));
    }

    /**
     * Kod ile katıl API
     */
    public function apiJoin(Request $request)
    {
        $user = auth()->user();
        $existingAccount = MuzibuCorporateAccount::where('user_id', $user->id)->first();

        $html = view('themes.muzibu.partials.corporate-join-content', compact('existingAccount'))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => 'Kurumsal Kod ile Katıl - Muzibu',
                'description' => 'Kurumsal kodunuzu girerek şirketinize bağlanın'
            ]
        ]);
    }

    /**
     * Kod ile bağlanma işlemi
     */
    public function doJoin(Request $request)
    {
        $request->validate([
            'corporate_code' => 'required|string|size:8'
        ], [
            'corporate_code.required' => 'Kurumsal kod gereklidir.',
            'corporate_code.size' => 'Kurumsal kod 8 karakter olmalıdır.'
        ]);

        $user = auth()->user();
        $code = strtoupper($request->corporate_code);

        // Zaten kurumsal üye mi?
        $existing = MuzibuCorporateAccount::where('user_id', $user->id)->first();
        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zaten bir kurumsal hesaba bağlısınız.'
                ], 400);
            }
            return back()->with('error', 'Zaten bir kurumsal hesaba bağlısınız.');
        }

        // Kodu bul
        $parentAccount = MuzibuCorporateAccount::where('corporate_code', $code)
            ->whereNull('parent_id') // Sadece ana şubeler kod verebilir
            ->where('is_active', true)
            ->first();

        if (!$parentAccount) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz veya aktif olmayan kurumsal kod.'
                ], 404);
            }
            return back()->with('error', 'Geçersiz veya aktif olmayan kurumsal kod.');
        }

        // Yeni şube oluştur
        MuzibuCorporateAccount::create([
            'user_id' => $user->id,
            'parent_id' => $parentAccount->id,
            'corporate_code' => null, // Alt şubelerin kodu olmaz
            'company_name' => null,
            'branch_name' => $user->name, // Varsayılan olarak kullanıcı adı
            'is_active' => true,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => ($parentAccount->company_name ?? 'Kurum') . ' hesabına başarıyla katıldınız!',
                'redirect' => '/corporate/my-corporate'
            ]);
        }

        return redirect()->route('muzibu.corporate.my')
            ->with('success', ($parentAccount->company_name ?? 'Kurum') . ' hesabına başarıyla katıldınız!');
    }

    /**
     * Üye kullanıcı görünümü
     */
    public function myCorporate(Request $request)
    {
        $user = auth()->user();
        $account = MuzibuCorporateAccount::where('user_id', $user->id)
            ->with(['parent.owner'])
            ->first();

        if (!$account) {
            return redirect()->route('muzibu.corporate.join');
        }

        // Ana şube ise dashboard'a yönlendir
        if ($account->isParent()) {
            return redirect()->route('muzibu.corporate.dashboard');
        }

        return view('themes.muzibu.corporate.my-corporate', compact('account'));
    }

    /**
     * Üye görünümü API
     */
    public function apiMyCorporate(Request $request)
    {
        $user = auth()->user();
        $account = MuzibuCorporateAccount::where('user_id', $user->id)
            ->with(['parent.owner'])
            ->first();

        if (!$account) {
            return response()->json([
                'redirect' => '/corporate/join'
            ]);
        }

        if ($account->isParent()) {
            return response()->json([
                'redirect' => '/corporate/dashboard'
            ]);
        }

        $html = view('themes.muzibu.partials.corporate-my-content', compact('account'))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => 'Kurumsal Üyeliğim - Muzibu',
                'description' => 'Kurumsal üyelik bilgileriniz'
            ]
        ]);
    }

    /**
     * Kurumdan ayrıl
     */
    public function leave(Request $request)
    {
        $user = auth()->user();
        $account = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNotNull('parent_id') // Sadece alt şubeler ayrılabilir
            ->first();

        if (!$account) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ayrılacak kurumsal hesap bulunamadı.'
                ], 404);
            }
            return back()->with('error', 'Ayrılacak kurumsal hesap bulunamadı.');
        }

        $companyName = $account->parent->company_name ?? 'Kurum';
        $account->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $companyName . ' hesabından ayrıldınız.',
                'redirect' => '/corporate'
            ]);
        }

        return redirect()->route('muzibu.corporate.index')
            ->with('success', $companyName . ' hesabından ayrıldınız.');
    }

    /**
     * Kod güncelle/yenile (ana şube)
     * code parametresi gönderilirse manuel kod, yoksa rastgele
     */
    public function regenerateCode(Request $request)
    {
        $user = auth()->user();
        $account = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->first();

        if (!$account) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kurumsal hesap bulunamadı.'
                ], 404);
            }
            return back()->with('error', 'Kurumsal hesap bulunamadı.');
        }

        // Manuel kod gönderildiyse validate et (8 karakter)
        if ($request->has('code') && $request->code) {
            $request->validate([
                'code' => 'required|string|min:8|max:8|alpha_num'
            ], [
                'code.min' => 'Kod tam olarak 8 karakter olmalıdır.',
                'code.max' => 'Kod tam olarak 8 karakter olmalıdır.',
                'code.alpha_num' => 'Kod sadece harf ve rakam içerebilir.'
            ]);

            $newCode = strtoupper($request->code);

            // Aynı kod başka birinde var mı kontrol et
            $exists = MuzibuCorporateAccount::where('corporate_code', $newCode)
                ->where('id', '!=', $account->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kod zaten kullanımda. Başka bir kod deneyin.'
                ], 400);
            }
        } else {
            // Rastgele 8 karakterlik OKUNABILIR kod oluştur
            // Karıştırılan karakterler hariç: 0/O, 1/I/L
            $newCode = $this->generateReadableCode();
        }

        $account->update(['corporate_code' => $newCode]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kurumsal kod güncellendi.',
                'new_code' => $newCode
            ]);
        }

        return back()->with('success', 'Kurumsal kod güncellendi: ' . $newCode);
    }

    /**
     * Üye çıkar (ana şube)
     */
    public function removeMember(Request $request, int $id)
    {
        $user = auth()->user();
        $parentAccount = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->first();

        if (!$parentAccount) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yetkiniz yok.'
                ], 403);
            }
            return back()->with('error', 'Yetkiniz yok.');
        }

        // Çıkarılacak üye bu ana şubeye bağlı mı?
        $member = MuzibuCorporateAccount::where('id', $id)
            ->where('parent_id', $parentAccount->id)
            ->first();

        if (!$member) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Üye bulunamadı.'
                ], 404);
            }
            return back()->with('error', 'Üye bulunamadı.');
        }

        $memberName = $member->owner->name ?? 'Üye';
        $member->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $memberName . ' kurumsal hesaptan çıkarıldı.'
            ]);
        }

        return back()->with('success', $memberName . ' kurumsal hesaptan çıkarıldı.');
    }

    /**
     * Şube adı güncelle
     */
    public function updateBranchName(Request $request, int $id)
    {
        $request->validate([
            'branch_name' => 'nullable|string|max:100'
        ]);

        $user = auth()->user();
        $parentAccount = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->first();

        if (!$parentAccount) {
            return response()->json(['success' => false, 'message' => 'Yetkiniz yok.'], 403);
        }

        $member = MuzibuCorporateAccount::where('id', $id)
            ->where('parent_id', $parentAccount->id)
            ->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Üye bulunamadı.'], 404);
        }

        $member->update(['branch_name' => $request->branch_name]);

        return response()->json([
            'success' => true,
            'message' => 'Şube adı güncellendi.'
        ]);
    }

    /**
     * Kurumsal üyeliği sonlandır (ana şube)
     * Tüm alt üyeleri siler, ana hesabı da siler
     */
    public function disband(Request $request)
    {
        $user = auth()->user();
        $parentAccount = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->first();

        if (!$parentAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Kurumsal hesap bulunamadı veya yetkiniz yok.'
            ], 403);
        }

        $companyName = $parentAccount->company_name ?? 'Kurumsal Hesap';
        $memberCount = $parentAccount->children()->count();

        // Önce tüm alt üyeleri sil
        $parentAccount->children()->delete();

        // Ana hesabı sil
        $parentAccount->delete();

        return response()->json([
            'success' => true,
            'message' => $companyName . ' kurumsal hesabı sonlandırıldı. ' . $memberCount . ' üye kurumdan çıkarıldı.',
            'redirect' => '/dashboard'
        ]);
    }

    /**
     * Şirket adını güncelle
     */
    public function updateCompanyName(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:100'
        ]);

        $user = auth()->user();
        $account = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Kurumsal hesap bulunamadı.'
            ], 404);
        }

        $account->company_name = $request->company_name;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Şirket adı güncellendi.',
            'company_name' => $account->company_name
        ]);
    }

    /**
     * Kurumsal hesap oluştur (Ana şube ol)
     * Kullanıcı kendi kurumsal hesabını açar
     */
    public function createCorporate(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:100|min:2',
            'corporate_code' => 'nullable|string|size:8|alpha_num'
        ], [
            'company_name.required' => 'Şirket adı gereklidir.',
            'company_name.min' => 'Şirket adı en az 2 karakter olmalıdır.',
            'company_name.max' => 'Şirket adı en fazla 100 karakter olabilir.',
            'corporate_code.size' => 'Kurumsal kod tam olarak 8 karakter olmalıdır.',
            'corporate_code.alpha_num' => 'Kurumsal kod sadece harf ve rakam içerebilir.'
        ]);

        $user = auth()->user();

        // Zaten kurumsal hesabı var mı?
        $existing = MuzibuCorporateAccount::where('user_id', $user->id)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Zaten bir kurumsal hesabınız var.'
            ], 400);
        }

        // Kullanıcı kod gönderdiyse onu kullan, yoksa otomatik oluştur
        if ($request->filled('corporate_code')) {
            $code = strtoupper($request->corporate_code);

            // Benzersizlik kontrolü
            $exists = MuzibuCorporateAccount::where('corporate_code', $code)->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kod zaten kullanımda. Başka bir kod deneyin.'
                ], 400);
            }
        } else {
            // Benzersiz 8 karakterlik OKUNABILIR kod oluştur
            $code = $this->generateReadableCode();
        }

        // Kurumsal hesap oluştur
        $account = MuzibuCorporateAccount::create([
            'user_id' => $user->id,
            'parent_id' => null, // Ana şube
            'corporate_code' => $code,
            'company_name' => $request->company_name,
            'branch_name' => 'Ana Şube',
            'is_active' => true,
        ]);

        \Log::info('🏢 New Corporate Account Created', [
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'corporate_code' => $code,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kurumsal hesabınız oluşturuldu! Kodunuz: ' . $code,
            'corporate_code' => $code,
            'redirect' => '/corporate/dashboard'
        ]);
    }

    /**
     * Kurumsal üyelik yönetim sayfası
     * Planları ve tüm üyeleri (owner dahil) listeler
     */
    public function subscriptions(Request $request)
    {
        $user = auth()->user();
        $account = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->with(['children.owner'])
            ->first();

        if (!$account) {
            return redirect()->route('muzibu.corporate.index')
                ->with('error', 'Kurumsal hesabınız bulunmuyor.');
        }

        // Aktif planları getir (trial hariç)
        $plans = SubscriptionPlan::where('is_active', true)
            ->where('is_public', true)
            ->where('is_trial', false)
            ->orderBy('sort_order')
            ->get();

        // Tüm üyeleri al (owner dahil)
        $members = $this->getAllMembersWithSubscription($account);

        return view('themes.muzibu.corporate.subscriptions', compact('account', 'plans', 'members'));
    }

    /**
     * Kurumsal üyelik yönetim API (SPA)
     */
    public function apiSubscriptions(Request $request)
    {
        $user = auth()->user();
        $account = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->with(['children.owner'])
            ->first();

        if (!$account) {
            return response()->json([
                'error' => true,
                'message' => 'Kurumsal hesabınız bulunmuyor.',
                'redirect' => '/corporate'
            ], 403);
        }

        $plans = SubscriptionPlan::where('is_active', true)
            ->where('is_public', true)
            ->where('is_trial', false)
            ->orderBy('sort_order')
            ->get();

        $members = $this->getAllMembersWithSubscription($account);

        $html = view('themes.muzibu.partials.corporate-subscriptions-content', compact('account', 'plans', 'members'))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => 'Üyelikleri Yönet - ' . ($account->company_name ?? 'Kurumsal'),
                'description' => 'Kurumsal üyelerin premium üyeliklerini yönetin'
            ]
        ]);
    }

    /**
     * Tüm üyeleri subscription bilgileriyle getir (owner dahil)
     */
    private function getAllMembersWithSubscription(MuzibuCorporateAccount $parentAccount): array
    {
        $members = [];

        // 1. Owner'ı ekle
        $members[] = $this->getMemberSubscriptionInfo($parentAccount, true);

        // 2. Alt üyeleri ekle
        foreach ($parentAccount->children as $child) {
            $members[] = $this->getMemberSubscriptionInfo($child, false);
        }

        return $members;
    }

    /**
     * Tek üyenin subscription bilgisi
     */
    private function getMemberSubscriptionInfo(MuzibuCorporateAccount $account, bool $isOwner): array
    {
        $userId = $account->user_id;
        $user = $account->owner;

        // 🔥 FIX: expires_at yerine current_period_end kullan (expires_at NULL olabiliyor)
        $subscription = DB::table('subscriptions')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('current_period_end', '>', now())
            ->orderBy('current_period_end', 'desc')
            ->first();

        $subscriptionInfo = null;
        if ($subscription) {
            $expiresAt = \Carbon\Carbon::parse($subscription->current_period_end);
            // 🔥 FIX: diffInDays negatif olabilir, max(0) ile sınırla
            $daysLeft = (int) now()->diffInDays($expiresAt, false);
            $subscriptionInfo = [
                'is_active' => true,
                'days_left' => max(0, $daysLeft),
                'ends_at' => $expiresAt->format('d.m.Y'),
                'status' => ($daysLeft >= 0 && $daysLeft <= 7) ? 'expiring' : 'active', // 7 gün kala uyarı
            ];
        } else {
            $subscriptionInfo = [
                'is_active' => false,
                'days_left' => 0,
                'ends_at' => null,
                'status' => 'expired',
            ];
        }

        return [
            'user_id' => $userId,
            'account_id' => $account->id,
            'name' => $user->name ?? 'Bilinmeyen',
            'email' => $user->email ?? '',
            'branch_name' => $isOwner ? 'Ana Şube' : ($account->branch_name ?? $user->name),
            'is_owner' => $isOwner,
            'subscription' => $subscriptionInfo,
            'initials' => $this->getInitials($user->name ?? ''),
        ];
    }

    /**
     * İsimden baş harfleri al
     */
    private function getInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }
        return $initials ?: '?';
    }

    /**
     * Okunabilir 8 karakterlik kod üret
     * Karıştırılan karakterler hariç tutulur: 0/O, 1/I/L
     * Format: 4 harf + 4 rakam (örn: ABCD2345)
     */
    private function generateReadableCode(): string
    {
        // Kolay okunan harfler (I, L, O hariç)
        $letters = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        // Kolay okunan rakamlar (0, 1 hariç)
        $numbers = '23456789';

        $code = '';

        // 4 harf
        for ($i = 0; $i < 4; $i++) {
            $code .= $letters[random_int(0, strlen($letters) - 1)];
        }

        // 4 rakam
        for ($i = 0; $i < 4; $i++) {
            $code .= $numbers[random_int(0, strlen($numbers) - 1)];
        }

        // Benzersizlik kontrolü
        $exists = MuzibuCorporateAccount::where('corporate_code', $code)->exists();
        if ($exists) {
            return $this->generateReadableCode(); // Tekrar dene
        }

        return $code;
    }

    /**
     * Kurumsal kod benzersizlik kontrolü (AJAX)
     */
    public function checkCodeAvailability(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:8|alpha_num'
        ]);

        $code = strtoupper($request->code);
        $exists = MuzibuCorporateAccount::where('corporate_code', $code)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Bu kod zaten kullanımda' : 'Kod kullanılabilir'
        ]);
    }

    /**
     * Üye dinleme geçmişi sayfası
     * SADECE ana şubeler (owner) erişebilir
     */
    public function memberHistory(Request $request, int $id)
    {
        $user = auth()->user();

        // Ana şube kontrolü
        $parentAccount = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->first();

        if (!$parentAccount) {
            return redirect()->route('muzibu.corporate.index')
                ->with('error', 'Bu sayfaya erişim yetkiniz yok.');
        }

        // Üye bu kurumsal hesaba ait mi kontrol et
        $member = $this->findCorporateMember($parentAccount, $id);

        if (!$member) {
            return redirect()->route('muzibu.corporate.dashboard')
                ->with('error', 'Üye bulunamadı.');
        }

        // Dinleme geçmişini getir
        $history = SongPlay::where('user_id', $member['user_id'])
            ->with(['song.album.artist', 'song.album.coverMedia', 'song.coverMedia'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('themes.muzibu.corporate.member-history', compact('member', 'history', 'parentAccount'));
    }

    /**
     * Üye dinleme geçmişi API (SPA)
     */
    public function apiMemberHistory(Request $request, int $id)
    {
        $user = auth()->user();

        // Ana şube kontrolü
        $parentAccount = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->first();

        if (!$parentAccount) {
            return response()->json([
                'error' => true,
                'message' => 'Bu sayfaya erişim yetkiniz yok.',
                'redirect' => '/corporate'
            ], 403);
        }

        // Üye bu kurumsal hesaba ait mi kontrol et
        $member = $this->findCorporateMember($parentAccount, $id);

        if (!$member) {
            return response()->json([
                'error' => true,
                'message' => 'Üye bulunamadı.',
                'redirect' => '/corporate/dashboard'
            ], 404);
        }

        // Dinleme geçmişini getir
        $history = SongPlay::where('user_id', $member['user_id'])
            ->with(['song.album.artist', 'song.album.coverMedia', 'song.coverMedia'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $html = view('themes.muzibu.partials.member-history-content', compact('member', 'history', 'parentAccount'))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => ($member['name'] ?? 'Üye') . ' - Dinleme Geçmişi',
                'description' => 'Kurumsal üye dinleme geçmişi'
            ]
        ]);
    }

    /**
     * Kurumsal üyeyi bul (owner veya children)
     * @param int $id Kurumsal hesap ID (account_id) - user_id DEĞİL!
     */
    private function findCorporateMember(MuzibuCorporateAccount $parentAccount, int $id): ?array
    {
        // Owner kontrolü (parent account id ile eşleşiyorsa)
        if ($id === $parentAccount->id) {
            return [
                'user_id' => $parentAccount->user_id,
                'account_id' => $parentAccount->id,
                'name' => $parentAccount->owner->name ?? 'Ana Şube',
                'email' => $parentAccount->owner->email ?? '',
                'branch_name' => 'Ana Şube',
                'is_owner' => true,
                'initials' => $this->getInitials($parentAccount->owner->name ?? ''),
            ];
        }

        // Alt üye - SADECE account_id ile arama (user_id ile DEĞİL - güvenlik)
        $member = MuzibuCorporateAccount::where('parent_id', $parentAccount->id)
            ->where('id', $id)
            ->with('owner')
            ->first();

        if (!$member) {
            return null;
        }

        return [
            'user_id' => $member->user_id,
            'account_id' => $member->id,
            'name' => $member->owner->name ?? 'Bilinmeyen',
            'email' => $member->owner->email ?? '',
            'branch_name' => $member->branch_name ?? $member->owner->name,
            'is_owner' => false,
            'initials' => $this->getInitials($member->owner->name ?? ''),
        ];
    }

    /**
     * Seçilen üyeler için subscription satın al
     * Cart'a ekler ve checkout'a yönlendirir
     */
    public function purchaseSubscriptions(Request $request)
    {
        try {
            \Log::info('🏢 purchaseSubscriptions STARTED', [
                'request_all' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            $request->validate([
                'plan_id' => 'required|integer',
                'cycle_key' => 'required|string',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'integer',
            ]);

            \Log::info('🏢 Validation passed');

            $user = auth()->user();
        $parentAccount = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->first();

        if (!$parentAccount) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kurumsal hesabınız bulunmuyor.'
                ], 403);
            }
            return back()->with('error', 'Kurumsal hesabınız bulunmuyor.');
        }

        // Plan kontrolü
        $plan = SubscriptionPlan::where('subscription_plan_id', $request->plan_id)
            ->where('is_active', true)
            ->first();

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz plan.'
            ], 404);
        }

        // Cycle kontrolü
        $cycle = $plan->getCycle($request->cycle_key);
        if (!$cycle) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz ödeme döngüsü.'
            ], 400);
        }

        // Seçilen kullanıcıların bu kurumsal hesaba ait olduğunu doğrula
        $validUserIds = $this->validateCorporateUsers($parentAccount, $request->user_ids);

        if (empty($validUserIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Seçilen üyeler kurumsal hesabınıza ait değil.'
            ], 400);
        }

        $quantity = count($validUserIds);
        $pricePerUser = $cycle['price'] ?? 0;
        $totalPrice = $pricePerUser * $quantity;

        // Cart'a ekle
        try {
            $cartService = app(CartService::class);
            $sessionId = session()->getId();
            $customerId = auth()->id();

            $cart = $cartService->findOrCreateCart($customerId, $sessionId);

            // 🔥 Tüm sepeti temizle (kurumsal satın alma için sıfırdan başla)
            foreach ($cart->items as $item) {
                $cartService->removeItem($item);
            }

            // Yeni item ekle - metadata ile seçilen user_ids kaydet
            $options = [
                'item_title' => 'Kurumsal Üyelik - ' . ($plan->getTranslated('title') ?? $plan->title),
                'item_description' => $quantity . ' üye için ' . ($cycle['label'][app()->getLocale()] ?? $cycle['label']['tr'] ?? $request->cycle_key),
                'metadata' => [
                    'type' => 'corporate_bulk',
                    'corporate_account_id' => $parentAccount->id,
                    'selected_user_ids' => $validUserIds,
                    'plan_id' => $plan->subscription_plan_id,
                    'cycle_key' => $request->cycle_key,
                    'cycle_metadata' => $cycle,
                    'price_per_user' => $pricePerUser,
                    'quantity' => $quantity,
                ],
            ];

            $cartItem = $cartService->addItem($cart, $plan, $quantity, $options);

            // Özel fiyat override (toplu fiyat)
            $cartItem->update([
                'unit_price' => $pricePerUser,
                'total' => $totalPrice,
            ]);

            $cart->refresh();
            $cart->recalculateTotals();

            \Log::info('🏢 Corporate Subscription AddToCart', [
                'corporate_account_id' => $parentAccount->id,
                'plan_id' => $plan->subscription_plan_id,
                'user_ids' => $validUserIds,
                'quantity' => $quantity,
                'total_price' => $totalPrice,
            ]);

            // 🔥 Form submit mı JSON request mi?
            if ($request->wantsJson()) {
                // AJAX/Fetch request
                return response()->json([
                    'success' => true,
                    'message' => $quantity . ' üye için üyelik sepete eklendi.',
                    'redirect' => '/cart/checkout?focus=payment&t=' . time(),
                    'summary' => [
                        'quantity' => $quantity,
                        'price_per_user' => $pricePerUser,
                        'total' => $totalPrice,
                    ]
                ]);
            } else {
                // Form submit - redirect
                return redirect('/cart/checkout?focus=payment&t=' . time())
                    ->with('success', $quantity . ' üye için üyelik sepete eklendi.');
            }

        } catch (\Exception $e) {
            \Log::error('❌ Corporate Subscription Cart ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }

        } catch (\Throwable $e) {
            \Log::error('❌ Corporate Subscription FATAL ERROR', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sunucu hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Seçilen user_id'lerin bu kurumsal hesaba ait olduğunu doğrula
     * Owner dahil (owner = parentAccount->user_id)
     * 30 günden fazla süresi olanları hariç tut
     */
    private function validateCorporateUsers(MuzibuCorporateAccount $parentAccount, array $userIds): array
    {
        $validIds = [];

        // Owner'ın user_id'si
        $ownerUserId = $parentAccount->user_id;

        // Alt üyelerin user_id'leri
        $childUserIds = $parentAccount->children()->pluck('user_id')->toArray();

        // Tüm geçerli user_id'ler
        $allValidUserIds = array_merge([$ownerUserId], $childUserIds);

        // 30+ gün süresi olanları bul
        $lockedUserIds = [];
        $subscriptions = DB::table('subscriptions')
            ->whereIn('user_id', $allValidUserIds)
            ->where('status', 'active')
            ->where('current_period_end', '>', now()->addDays(30)) // 30 günden fazla
            ->pluck('user_id')
            ->toArray();
        $lockedUserIds = array_map('intval', $subscriptions);

        foreach ($userIds as $userId) {
            $uid = (int)$userId;
            // Kurumsal üye mi ve 30+ gün süresi yok mu
            if (in_array($uid, $allValidUserIds) && !in_array($uid, $lockedUserIds)) {
                $validIds[] = $uid;
            }
        }

        return $validIds;
    }
}
