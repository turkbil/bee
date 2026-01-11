<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin\Tokens;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Modules\AI\App\Models\AICreditPackage;
use Modules\AI\App\Models\AICreditPurchase;
use Modules\AI\App\Models\AICreditUsage;
use Modules\AI\App\Services\AICreditService;
use Modules\AI\App\Exceptions\AICreditException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TokenManagementController extends Controller
{
    private AICreditService $creditService;

    public function __construct()
    {        
        // Use manual service resolution to avoid constructor injection issues
        $this->creditService = app(AICreditService::class);
    }
    /**
     * Display AI kredi management dashboard
     */
    public function index()
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $tenants = Tenant::select([
            'id',
            'title',
            'ai_enabled',
            'ai_tokens_balance',
            'ai_tokens_used_this_month',
            'ai_monthly_token_limit',
            'ai_last_used_at'
        ])->orderBy('created_at', 'desc')->paginate(20);

        $systemStats = [
            'total_tenants' => Tenant::count(),
            'active_ai_tenants' => Tenant::where('ai_enabled', true)->count(),
            'total_credits_distributed' => AICreditPurchase::where('status', 'completed')->sum('credit_amount'),
            'total_credits_used' => AICreditUsage::sum('credit_used'),
        ];

        return view('ai::admin.tokens.index', compact('tenants', 'systemStats'));
    }

    /**
     * Show tenant AI details
     */
    public function show(Tenant $tenant)
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $tenant->load(['aiCreditPurchases' => function($query) {
            $query->with('package')->latest()->limit(10);
        }]);

        $recentUsage = AICreditUsage::where('tenant_id', $tenant->id)
            ->latest('used_at')
            ->limit(20)
            ->get();

        $monthlyUsage = AICreditUsage::where('tenant_id', $tenant->id)
            ->whereBetween('used_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('DATE(used_at) as date, SUM(credit_used) as total_credits')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate real credit statistics using new service
        try {
            $creditBalance = $this->creditService->getTenantCreditBalance($tenant->id);
            $totalPurchasedCredits = $creditBalance['total_purchased'];
            $totalUsedCredits = $creditBalance['total_used'];
            $realCreditBalance = $creditBalance['remaining_balance'];
        } catch (AICreditException $e) {
            $totalPurchasedCredits = 0;
            $totalUsedCredits = 0;
            $realCreditBalance = 0;
        }
        
        // Bu ay kullanımı hesapla
        $monthlyUsedCredits = AICreditUsage::where('tenant_id', $tenant->id)
            ->whereBetween('used_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('credit_used');

        return view('ai::admin.tokens.show', compact('tenant', 'recentUsage', 'monthlyUsage', 'totalPurchasedCredits', 'totalUsedCredits', 'realCreditBalance', 'monthlyUsedCredits'));
    }

    /**
     * Update tenant AI settings
     */
    public function updateTenantSettings(Request $request, Tenant $tenant): JsonResponse|RedirectResponse
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $request->validate([
            'ai_enabled' => 'boolean',
            'ai_monthly_token_limit' => 'integer|min:0',
            'token_adjustment' => 'integer',
            'adjustment_reason' => 'string|nullable'
        ]);

        // Calculate real credit balance using service
        try {
            $creditBalance = $this->creditService->getTenantCreditBalance($tenant->id);
            $realBalance = $creditBalance['remaining_balance'];
        } catch (AICreditException $e) {
            $realBalance = 0;
        }

        $oldBalance = $tenant->ai_tokens_balance; // OLD METHOD
        $oldRealBalance = $realBalance; // NEW CORRECT METHOD

        // Update basic settings
        $tenant->update([
            'ai_enabled' => $request->boolean('ai_enabled'),
            'ai_monthly_token_limit' => $request->integer('ai_monthly_token_limit', 0)
        ]);

        // Handle credit adjustment using service
        if ($request->filled('token_adjustment') && $request->integer('token_adjustment') != 0) {
            $adjustment = (float) $request->integer('token_adjustment');
            
            try {
                if ($adjustment > 0) {
                    // Add credits using service
                    $result = $this->creditService->addCreditsToTenant(
                        tenantId: $tenant->id,
                        amount: $adjustment,
                        reason: $request->input('adjustment_reason', 'Admin tarafından kredi ekleme'),
                        adminUserId: auth()->id()
                    );
                } else {
                    // Deduct credits using service
                    $result = $this->creditService->deductCreditsFromTenant(
                        tenantId: $tenant->id,
                        amount: abs($adjustment),
                        reason: $request->input('adjustment_reason', 'Admin tarafından kredi düşürme'),
                        adminUserId: auth()->id()
                    );
                }
                
                if (!$result->success) {
                    if (request()->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $result->message
                        ]);
                    }
                    return redirect()->back()->with('error', $result->message);
                }
                
            } catch (AICreditException $e) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kiracı AI ayarları güncellendi.']);
        }

        return redirect()->back()->with('success', 'Kiracı AI ayarları güncellendi.');
    }

    /**
     * Kredi packages management (Root admin)
     */
    public function adminPackages()
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $packages = AICreditPackage::ordered()->get();
        
        return view('ai::admin.tokens.packages', compact('packages'));
    }

    /**
     * Store new token package
     */
    public function storePackage(Request $request)
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'token_amount' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'sort_order' => 'integer|min:0'
        ]);

        AICreditPackage::create($request->all());

        return redirect()->back()->with('success', 'Kredi paketi oluşturuldu.');
    }

    /**
     * Show package edit data
     */
    public function editPackage(AICreditPackage $package): JsonResponse
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            return response()->json(['success' => false, 'message' => 'Bu sayfaya erişim yetkiniz bulunmamaktadır.'], 403);
        }

        // Features'i array olarak döndür
        $packageData = $package->toArray();
        if ($packageData['features']) {
            $packageData['features'] = is_string($packageData['features']) 
                ? json_decode($packageData['features'], true) 
                : $packageData['features'];
        }

        return response()->json([
            'success' => true,
            'package' => $packageData
        ]);
    }

    /**
     * Update token package
     */
    public function updatePackage(Request $request, AICreditPackage $package): JsonResponse|RedirectResponse
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'token_amount' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        $package->update($request->all());

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kredi paketi güncellendi.']);
        }

        return redirect()->back()->with('success', 'Kredi paketi güncellendi.');
    }

    /**
     * Delete kredi package
     */
    public function destroyPackage(AICreditPackage $package): JsonResponse|RedirectResponse
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        // Check if package has purchases
        if ($package->purchases()->exists()) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Bu paketten satın alma yapılmış, silinemiyor.']);
            }
            return redirect()->back()->with('error', 'Bu paketten satın alma yapılmış, silinemiyor.');
        }

        $package->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kredi paketi silindi.']);
        }

        return redirect()->back()->with('success', 'Kredi paketi silindi.');
    }

    /**
     * Provider bazlı kullanım analizi (Credit sistemi için güncellenmiş)
     */
    private function getProviderAnalysis($baseQuery): array
    {
        // Provider bazlı kullanım al
        $providerUsage = (clone $baseQuery)
            ->selectRaw('provider_name, SUM(credits_used) as total_credits, SUM(input_tokens + output_tokens) as total_tokens, COUNT(*) as usage_count')
            ->whereNotNull('provider_name')
            ->where('provider_name', '!=', '')
            ->groupBy('provider_name')
            ->orderByDesc('total_credits')
            ->get();

        // Provider istatistikleri
        $providerStats = [];
        $totalCredits = $providerUsage->sum('total_credits');
        $totalTokens = $providerUsage->sum('total_tokens');
        $totalUsage = $providerUsage->sum('usage_count');
        
        foreach ($providerUsage as $usage) {
            $providerName = $usage->provider_name;

            $providerStats[$providerName] = [
                'name' => ucfirst($providerName),
                'total_usage' => $usage->usage_count,
                'total_tokens' => $usage->total_tokens,
                'total_credits' => $usage->total_credits,
                'usage_percentage' => $totalUsage > 0 ? round(($usage->usage_count / $totalUsage) * 100, 1) : 0,
                'token_percentage' => $totalTokens > 0 ? round(($usage->total_tokens / $totalTokens) * 100, 1) : 0,
                'credit_percentage' => $totalCredits > 0 ? round(($usage->total_credits / $totalCredits) * 100, 1) : 0
            ];
        }

        // En çok credit kullanılandan az kullanılana sırala
        uasort($providerStats, fn($a, $b) => $b['total_credits'] <=> $a['total_credits']);

        return [
            'providers' => $providerStats,
            'total_credits' => $totalCredits,
            'total_tokens' => $totalTokens,
            'total_usage' => $totalUsage,
            'unique_providers' => $providerUsage->count(),
            'top_provider' => $providerUsage->first()?->provider_name ?? 'N/A'
        ];
    }

    /**
     * All purchases history (Root admin)
     */
    public function allPurchases(Request $request)
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        // Tenant filtresi
        $selectedTenant = $request->get('tenant_id');
        $tenants = Tenant::select('id', 'title')->get();

        // Base query
        $baseQuery = AICreditPurchase::with(['tenant', 'package']);
        if ($selectedTenant) {
            $baseQuery->where('tenant_id', $selectedTenant);
        }

        $purchases = $baseQuery->latest()->paginate(50);

        return view('ai::admin.tokens.purchases', compact('purchases', 'tenants', 'selectedTenant'));
    }

    /**
     * Credit usage statistics page (alias for allUsageStats)
     */
    public function usageStats(Request $request)
    {
        return $this->allUsageStats($request);
    }
    
    /**
     * All usage statistics (Root admin)
     */
    public function allUsageStats(Request $request)
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        // Tenant filtresi
        $selectedTenant = $request->get('tenant_id');
        $tenants = \App\Models\Tenant::select('id', 'title')->get();

        // Base query - Credit sistemi kullan
        $baseQuery = AICreditUsage::query();
        if ($selectedTenant) {
            $baseQuery->where('tenant_id', $selectedTenant);
        }

        // Genel istatistikler - Credit alanları kullan
        $stats = [
            'total_usage' => (clone $baseQuery)->sum('credits_used'),
            'today_usage' => (clone $baseQuery)->whereDate('used_at', today())->sum('credits_used'),
            'week_usage' => (clone $baseQuery)->where('used_at', '>=', now()->startOfWeek())->sum('credits_used'),
            'month_usage' => (clone $baseQuery)->where('used_at', '>=', now()->startOfMonth())->sum('credits_used'),
        ];

        // Provider bazlı kullanım
        $stats['by_provider'] = (clone $baseQuery)->selectRaw('provider_name, SUM(credits_used) as total')
            ->groupBy('provider_name')
            ->orderByDesc('total')
            ->pluck('total', 'provider_name')
            ->toArray();

        // Provider bazlı analiz ekle
        $stats['provider_analysis'] = $this->getProviderAnalysis($baseQuery);

        // Feature bazlı kullanım
        $stats['by_feature'] = (clone $baseQuery)->selectRaw('feature_slug, SUM(credits_used) as total')
            ->whereNotNull('feature_slug')
            ->groupBy('feature_slug')
            ->orderByDesc('total')
            ->pluck('total', 'feature_slug')
            ->toArray();

        // Amaç bazlı kullanım (usage_type)
        $stats['by_purpose'] = (clone $baseQuery)->selectRaw('usage_type, SUM(credits_used) as total')
            ->whereNotNull('usage_type')
            ->groupBy('usage_type')
            ->orderByDesc('total')
            ->pluck('total', 'usage_type')
            ->toArray();

        // Son 30 gün günlük kullanım (grafik için)
        $dailyUsageData = (clone $baseQuery)->selectRaw('DATE(used_at) as date, SUM(credits_used) as total_credits')
            ->where('used_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_credits', 'date')
            ->toArray();

        // Son 30 gün için eksik günleri sıfır ile doldur
        $stats['daily_usage'] = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $stats['daily_usage'][$date] = $dailyUsageData[$date] ?? 0;
        }

        // Detaylı kullanım kayıtları (sayfalama ile)
        $usageRecords = (clone $baseQuery)->with(['tenant', 'user'])
            ->latest('used_at')
            ->paginate(50);

        return view('ai::admin.credits.usage-stats', compact('stats', 'usageRecords', 'tenants', 'selectedTenant'));
    }

    /**
     * General statistics overview (Root admin)
     */
    public function statisticsOverview(Request $request)
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        // Genel sistem istatistikleri (TokenHelper ile)
        $purchasedTokens = AITokenPurchase::where('status', 'completed')->sum('token_amount') ?? 0;
        
        // Eğer satın alım kaydı yoksa 500K sistem başlangıcı kabul et
        if ($purchasedTokens <= 0) {
            $purchasedTokens = 500000; // Sistem başlangıç token'ı
        }
        
        // AI enabled kontrolü için farklı yöntem dene
        $activeAITenants = Tenant::where('ai_enabled', true)
            ->count();
        
        $totalUsed = AITokenUsage::sum('tokens_used') ?? 0;
        
        $systemStats = [
            'total_tenants' => Tenant::count(),
            'active_ai_tenants' => $activeAITenants,
            'total_credits_distributed' => $purchasedTokens,
            'total_credits_used' => $totalUsed,
            'total_purchases' => AITokenPurchase::count(),
            'total_revenue' => AITokenPurchase::where('status', 'completed')->sum('price_paid') ?? 0,
            'avg_tokens_per_tenant' => $activeAITenants > 0 ? (($purchasedTokens - $totalUsed) / $activeAITenants) : 0,
            'most_active_tenant' => null, // Geçici olarak null
        ];

        // Son 30 gün tenant aktivitesi
        $tenantActivity = AITokenUsage::selectRaw('tenant_id, SUM(tokens_used) as total_credits')
            ->with('tenant')
            ->where('used_at', '>=', now()->subDays(30))
            ->groupBy('tenant_id')
            ->orderByDesc('total_credits')
            ->limit(10)
            ->get();

        // Her tenant için gerçek bakiye hesaplaması
        $tenantActivity->each(function ($activity) {
            if ($activity->tenant) {
                $totalPurchases = AITokenPurchase::where('tenant_id', $activity->tenant_id)
                    ->where('status', 'completed')
                    ->sum('token_amount');
                $totalUsage = AITokenUsage::where('tenant_id', $activity->tenant_id)
                    ->sum('tokens_used');
                $activity->tenant->real_balance = max(0, $totalPurchases - $totalUsage);
            }
        });

        // Aylık kullanım trendi (son 12 ay)
        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthlyTrend[$monthKey] = AITokenUsage::whereYear('used_at', $date->year)
                ->whereMonth('used_at', $date->month)
                ->sum('tokens_used');
        }

        // En çok kullanılan modeller
        $topModels = AITokenUsage::selectRaw('model, SUM(tokens_used) as total_credits')
            ->groupBy('model')
            ->orderByDesc('total_credits')
            ->limit(5)
            ->get();

        // Haftalık karşılaştırma
        $currentWeekUsage = AITokenUsage::where('used_at', '>=', now()->startOfWeek())->sum('tokens_used');
        $previousWeekUsage = AITokenUsage::whereBetween('used_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tokens_used');
        $weeklyGrowth = $previousWeekUsage > 0 ? (($currentWeekUsage - $previousWeekUsage) / $previousWeekUsage * 100) : 0;

        return view('ai::admin.tokens.statistics-overview', compact(
            'systemStats',
            'tenantActivity',
            'monthlyTrend',
            'topModels',
            'currentWeekUsage',
            'previousWeekUsage',
            'weeklyGrowth'
        ));
    }

    /**
     * Tenant specific statistics (Root admin)
     */
    public function tenantStatistics(Request $request, $tenantId)
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $tenant = Tenant::findOrFail($tenantId);

        // Tenant specific stats with real balance calculation
        $totalPurchases = AITokenPurchase::where('tenant_id', $tenantId)->where('status', 'completed')->sum('token_amount');
        $totalUsage = AITokenUsage::where('tenant_id', $tenantId)->sum('tokens_used');
        $realBalance = max(0, $totalPurchases - $totalUsage);

        $stats = [
            'total_usage' => $totalUsage,
            'total_purchases' => $totalPurchases,
            'current_balance' => $tenant->ai_tokens_balance, // OLD METHOD - for comparison
            'real_balance' => $realBalance, // NEW CORRECT METHOD
            'monthly_usage' => AITokenUsage::where('tenant_id', $tenantId)->where('used_at', '>=', now()->startOfMonth())->sum('tokens_used'),
            'weekly_usage' => AITokenUsage::where('tenant_id', $tenantId)->where('used_at', '>=', now()->startOfWeek())->sum('tokens_used'),
            'today_usage' => AITokenUsage::where('tenant_id', $tenantId)->whereDate('used_at', today())->sum('tokens_used'),
        ];

        // Model kullanım dağılımı
        $modelUsage = AITokenUsage::where('tenant_id', $tenantId)
            ->selectRaw('model, SUM(tokens_used) as total')
            ->groupBy('model')
            ->orderByDesc('total')
            ->get();

        // Son 30 gün günlük kullanım
        $dailyUsage = AITokenUsage::where('tenant_id', $tenantId)
            ->selectRaw('DATE(used_at) as date, SUM(tokens_used) as total_tokens')
            ->where('used_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_tokens', 'date')
            ->toArray();

        // Eksik günleri sıfır ile doldur
        $formattedDailyUsage = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $formattedDailyUsage[$date] = $dailyUsage[$date] ?? 0;
        }

        // Son kullanım kayıtları
        $recentUsage = AITokenUsage::where('tenant_id', $tenantId)
            ->with('user')
            ->latest('used_at')
            ->limit(20)
            ->get();

        return view('ai::admin.tokens.tenant-statistics', compact(
            'tenant',
            'stats',
            'modelUsage',
            'formattedDailyUsage',
            'recentUsage'
        ));
    }

    /**
     * Update package order
     */
    public function updatePackageOrder(Request $request)
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            return response()->json(['success' => false, 'message' => 'Bu işlem için yetkiniz bulunmamaktadır.'], 403);
        }

        $request->validate([
            'packages' => 'required|array',
            'packages.*.id' => 'required|integer|exists:ai_token_packages,id',
            'packages.*.sort_order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->packages as $packageData) {
                AICreditPackage::where('id', $packageData['id'])
                    ->update(['sort_order' => $packageData['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paket sıralaması güncellendi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncellenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle AI status for tenant
     */
    public function toggleAI(Request $request, Tenant $tenant)
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            return response()->json(['success' => false, 'message' => 'Bu işlem için yetkiniz bulunmamaktadır.'], 403);
        }

        $tenant->update([
            'ai_enabled' => !$tenant->ai_enabled
        ]);

        $statusText = $tenant->ai_enabled ? 'aktif' : 'pasif';

        return response()->json([
            'success' => true,
            'message' => "AI kullanımı {$statusText} hale getirildi.",
            'ai_enabled' => $tenant->ai_enabled
        ]);
    }

    /**
     * Adjust tenant token balance
     */
    public function adjustTokens(Request $request, Tenant $tenant)
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            return response()->json(['success' => false, 'message' => 'Bu işlem için yetkiniz bulunmamaktadır.'], 403);
        }

        $request->validate([
            'tokenAmount' => 'required|integer|not_in:0',
            'adjustmentReason' => 'required|string|min:10|max:500'
        ]);

        // Calculate real kredi balance for validation
        $totalPurchased = AITokenPurchase::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->sum('token_amount');
        $totalUsed = AITokenUsage::where('tenant_id', $tenant->id)
            ->sum('tokens_used');
        $realBalance = max(0, $totalPurchased - $totalUsed);

        $oldBalance = $tenant->ai_tokens_balance; // OLD METHOD
        $adjustment = $request->integer('tokenAmount');
        $newBalance = $oldBalance + $adjustment;

        // Negatif bakiye kontrolü (gerçek bakiye üzerinden)
        if ($adjustment < 0 && abs($adjustment) > $realBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Yeterli kredi bakiyesi yok. Gerçek bakiye: ' . number_format($realBalance) . ' kredi'
            ]);
        }

        try {
            // Bakiyeyi güncelle
            $tenant->update(['ai_tokens_balance' => $newBalance]);

            // İşlemi logla
            AITokenUsage::create([
                'tenant_id' => $tenant->id,
                'user_id' => auth()->id(),
                'tokens_used' => abs($adjustment),
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'usage_type' => 'admin_adjustment',
                'model' => 'admin',
                'purpose' => $adjustment > 0 ? 'token_addition' : 'token_deduction',
                'description' => $request->input('adjustmentReason'),
                'metadata' => json_encode([
                    'admin_id' => auth()->id(),
                    'adjustment_amount' => $adjustment,
                    'old_balance' => $oldBalance,
                    'new_balance' => $newBalance
                ]),
                'used_at' => now()
            ]);

            // If tokens are added, record as purchase (free admin addition)
            if ($adjustment > 0) {
                AITokenPurchase::create([
                    'tenant_id' => $tenant->id,
                    'user_id' => auth()->id(),
                    'package_id' => null, // No package for admin additions
                    'token_amount' => $adjustment,
                    'price_paid' => 0, // Free admin addition
                    'amount' => 0,
                    'currency' => 'TRY',
                    'status' => 'completed',
                    'payment_method' => 'admin_free',
                    'payment_transaction_id' => null,
                    'payment_data' => null,
                    'notes' => 'Admin tarafından ücretsiz kredi ekleme: ' . $request->input('adjustmentReason'),
                    'purchased_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kredi bakiyesi başarıyla güncellendi.',
                'remaining_credits' => $newBalance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kredi ayarlama sırasında hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
}