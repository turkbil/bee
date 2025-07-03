<?php

namespace Modules\AI\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Modules\AI\App\Models\AITokenPackage;
use Modules\AI\App\Models\AITokenPurchase;
use Modules\AI\App\Models\AITokenUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TokenManagementController extends Controller
{
    /**
     * Display AI token management dashboard
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
            'total_tokens_distributed' => AITokenPurchase::where('status', 'completed')->sum('token_amount'),
            'total_tokens_used' => AITokenUsage::sum('tokens_used'),
        ];

        return view('ai::admin.token-management.index', compact('tenants', 'systemStats'));
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

        $tenant->load(['aiTokenPurchases' => function($query) {
            $query->with('package')->latest()->limit(10);
        }]);

        $recentUsage = AITokenUsage::where('tenant_id', $tenant->id)
            ->latest('used_at')
            ->limit(20)
            ->get();

        $monthlyUsage = AITokenUsage::where('tenant_id', $tenant->id)
            ->whereBetween('used_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('DATE(used_at) as date, SUM(tokens_used) as total_tokens')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate real token statistics for this tenant
        $totalPurchasedTokens = AITokenPurchase::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->sum('token_amount');

        $totalUsedTokens = AITokenUsage::where('tenant_id', $tenant->id)
            ->sum('tokens_used');

        $realTokenBalance = max(0, $totalPurchasedTokens - $totalUsedTokens);

        return view('ai::admin.token-management.show', compact('tenant', 'recentUsage', 'monthlyUsage', 'totalPurchasedTokens', 'totalUsedTokens', 'realTokenBalance'));
    }

    /**
     * Update tenant AI settings
     */
    public function updateTenantSettings(Request $request, Tenant $tenant)
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

        // Calculate real token balance
        $totalPurchased = AITokenPurchase::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->sum('token_amount');
        $totalUsed = AITokenUsage::where('tenant_id', $tenant->id)
            ->sum('tokens_used');
        $realBalance = max(0, $totalPurchased - $totalUsed);

        $oldBalance = $tenant->ai_tokens_balance; // OLD METHOD
        $oldRealBalance = $realBalance; // NEW CORRECT METHOD

        // Update basic settings
        $tenant->update([
            'ai_enabled' => $request->boolean('ai_enabled'),
            'ai_monthly_token_limit' => $request->integer('ai_monthly_token_limit', 0)
        ]);

        // Handle token adjustment
        if ($request->filled('token_adjustment') && $request->integer('token_adjustment') != 0) {
            $adjustment = $request->integer('token_adjustment');
            
            // Validation for negative adjustments using real balance
            if ($adjustment < 0 && abs($adjustment) > $realBalance) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Mevcut bakiyeden fazla token çıkarılamaz. Gerçek bakiye: ' . number_format($realBalance) . ' token'
                    ]);
                }
                return redirect()->back()->with('error', 'Mevcut bakiyeden fazla token çıkarılamaz.');
            }
            
            // Calculate new balance (we still update the old field for backward compatibility)
            $newBalance = max(0, $oldBalance + $adjustment);
            
            // Update tenant balance
            $tenant->update(['ai_tokens_balance' => $newBalance]);
            
            // Log the adjustment
            AITokenUsage::create([
                'tenant_id' => $tenant->id,
                'user_id' => auth()->id(),
                'tokens_used' => abs($adjustment),
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'usage_type' => 'admin_adjustment',
                'model' => 'admin',
                'purpose' => $adjustment > 0 ? 'token_addition' : 'token_deduction',
                'description' => $request->input('adjustment_reason', 'Admin tarafından token düzenlemesi'),
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
                    'notes' => 'Admin tarafından ücretsiz token ekleme: ' . $request->input('adjustment_reason', ''),
                    'purchased_at' => now()
                ]);
            }
        }

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kiracı AI ayarları güncellendi.']);
        }

        return redirect()->back()->with('success', 'Kiracı AI ayarları güncellendi.');
    }

    /**
     * Token packages management (Root admin)
     */
    public function adminPackages()
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $packages = AITokenPackage::ordered()->get();
        
        return view('ai::admin.token-management.packages', compact('packages'));
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

        AITokenPackage::create($request->all());

        return redirect()->back()->with('success', 'Token paketi oluşturuldu.');
    }

    /**
     * Show package edit data
     */
    public function editPackage(AITokenPackage $package)
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
    public function updatePackage(Request $request, AITokenPackage $package)
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
            return response()->json(['success' => true, 'message' => 'Token paketi güncellendi.']);
        }

        return redirect()->back()->with('success', 'Token paketi güncellendi.');
    }

    /**
     * Delete token package
     */
    public function destroyPackage(AITokenPackage $package)
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
            return response()->json(['success' => true, 'message' => 'Token paketi silindi.']);
        }

        return redirect()->back()->with('success', 'Token paketi silindi.');
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
        $baseQuery = AITokenPurchase::with(['tenant', 'package']);
        if ($selectedTenant) {
            $baseQuery->where('tenant_id', $selectedTenant);
        }

        $purchases = $baseQuery->latest()->paginate(50);

        return view('ai::admin.token-management.purchases', compact('purchases', 'tenants', 'selectedTenant'));
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

        // Base query
        $baseQuery = AITokenUsage::query();
        if ($selectedTenant) {
            $baseQuery->where('tenant_id', $selectedTenant);
        }

        // Genel istatistikler
        $stats = [
            'total_usage' => (clone $baseQuery)->sum('tokens_used'),
            'today_usage' => (clone $baseQuery)->whereDate('used_at', today())->sum('tokens_used'),
            'week_usage' => (clone $baseQuery)->where('used_at', '>=', now()->startOfWeek())->sum('tokens_used'),
            'month_usage' => (clone $baseQuery)->where('used_at', '>=', now()->startOfMonth())->sum('tokens_used'),
        ];

        // Model bazlı kullanım
        $stats['by_model'] = (clone $baseQuery)->selectRaw('model, SUM(tokens_used) as total')
            ->groupBy('model')
            ->orderByDesc('total')
            ->pluck('total', 'model')
            ->toArray();

        // Amaç bazlı kullanım
        $stats['by_purpose'] = (clone $baseQuery)->selectRaw('purpose, SUM(tokens_used) as total')
            ->groupBy('purpose')
            ->orderByDesc('total')
            ->pluck('total', 'purpose')
            ->toArray();

        // Son 30 gün günlük kullanım (grafik için)
        $dailyUsageData = (clone $baseQuery)->selectRaw('DATE(used_at) as date, SUM(tokens_used) as total_tokens')
            ->where('used_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_tokens', 'date')
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

        return view('ai::admin.token-management.usage-stats', compact('stats', 'usageRecords', 'tenants', 'selectedTenant'));
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
            ->orWhere('ai_tokens_balance', '>', 0)
            ->count();
        
        $totalUsed = AITokenUsage::sum('tokens_used') ?? 0;
        
        $systemStats = [
            'total_tenants' => Tenant::count(),
            'active_ai_tenants' => $activeAITenants,
            'total_tokens_distributed' => $purchasedTokens,
            'total_tokens_used' => $totalUsed,
            'total_purchases' => AITokenPurchase::count(),
            'total_revenue' => AITokenPurchase::where('status', 'completed')->sum('price_paid') ?? 0,
            'avg_tokens_per_tenant' => $activeAITenants > 0 ? (($purchasedTokens - $totalUsed) / $activeAITenants) : 0,
            'most_active_tenant' => Tenant::orderBy('ai_tokens_used_this_month', 'desc')->first(),
        ];

        // Son 30 gün tenant aktivitesi
        $tenantActivity = AITokenUsage::selectRaw('tenant_id, SUM(tokens_used) as total_tokens')
            ->with('tenant')
            ->where('used_at', '>=', now()->subDays(30))
            ->groupBy('tenant_id')
            ->orderByDesc('total_tokens')
            ->limit(10)
            ->get();

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
        $topModels = AITokenUsage::selectRaw('model, SUM(tokens_used) as total_tokens')
            ->groupBy('model')
            ->orderByDesc('total_tokens')
            ->limit(5)
            ->get();

        // Haftalık karşılaştırma
        $currentWeekUsage = AITokenUsage::where('used_at', '>=', now()->startOfWeek())->sum('tokens_used');
        $previousWeekUsage = AITokenUsage::whereBetween('used_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tokens_used');
        $weeklyGrowth = $previousWeekUsage > 0 ? (($currentWeekUsage - $previousWeekUsage) / $previousWeekUsage * 100) : 0;

        return view('ai::admin.token-management.statistics-overview', compact(
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

        return view('ai::admin.token-management.tenant-statistics', compact(
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
                AITokenPackage::where('id', $packageData['id'])
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

        // Calculate real token balance for validation
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
                'message' => 'Yeterli token bakiyesi yok. Gerçek bakiye: ' . number_format($realBalance) . ' token'
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
                    'notes' => 'Admin tarafından ücretsiz token ekleme: ' . $request->input('adjustmentReason'),
                    'purchased_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token bakiyesi başarıyla güncellendi.',
                'new_balance' => $newBalance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token ayarlama sırasında hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
}