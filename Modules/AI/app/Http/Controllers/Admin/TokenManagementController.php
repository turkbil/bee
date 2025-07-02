<?php

namespace Modules\AI\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\AITokenPackage;
use App\Models\AITokenPurchase;
use App\Models\AITokenUsage;
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

        return view('ai::admin.token-management.show', compact('tenant', 'recentUsage', 'monthlyUsage'));
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

        $oldBalance = $tenant->ai_tokens_balance;

        // Update basic settings
        $tenant->update([
            'ai_enabled' => $request->boolean('ai_enabled'),
            'ai_monthly_token_limit' => $request->integer('ai_monthly_token_limit', 0)
        ]);

        // Handle token adjustment
        if ($request->filled('token_adjustment') && $request->integer('token_adjustment') != 0) {
            $adjustment = $request->integer('token_adjustment');
            
            if ($adjustment > 0) {
                $tenant->addTokens($adjustment, $request->input('adjustment_reason', 'Admin tarafından eklendi'));
            } else {
                $newBalance = max(0, $oldBalance + $adjustment);
                $tenant->update(['ai_tokens_balance' => $newBalance]);
                
                // Log the adjustment as usage
                AITokenUsage::create([
                    'tenant_id' => $tenant->id,
                    'tokens_used' => abs($adjustment),
                    'usage_type' => 'admin_adjustment',
                    'description' => $request->input('adjustment_reason', 'Admin tarafından düzeltme'),
                    'used_at' => now()
                ]);
            }
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
            return redirect()->back()->with('error', 'Bu paketten satın alma yapılmış, silinemiyor.');
        }

        $package->delete();

        return redirect()->back()->with('success', 'Token paketi silindi.');
    }

    /**
     * All purchases history (Root admin)
     */
    public function allPurchases()
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $purchases = AITokenPurchase::with(['tenant', 'package'])
            ->latest()
            ->paginate(50);

        return view('ai::admin.token-management.purchases', compact('purchases'));
    }

    /**
     * All usage statistics (Root admin)
     */
    public function allUsageStats()
    {
        // Root admin check
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $dailyUsage = AITokenUsage::selectRaw('DATE(used_at) as date, SUM(tokens_used) as total_tokens, COUNT(*) as usage_count')
            ->where('used_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topTenants = Tenant::selectRaw('tenants.*, SUM(ai_token_usage.tokens_used) as total_usage')
            ->join('ai_token_usage', 'tenants.id', '=', 'ai_token_usage.tenant_id')
            ->where('ai_token_usage.used_at', '>=', now()->subDays(30))
            ->groupBy('tenants.id')
            ->orderByDesc('total_usage')
            ->limit(10)
            ->get();

        $usageByType = AITokenUsage::selectRaw('usage_type, SUM(tokens_used) as total_tokens, COUNT(*) as usage_count')
            ->where('used_at', '>=', now()->subDays(30))
            ->groupBy('usage_type')
            ->orderByDesc('total_tokens')
            ->get();

        return view('ai::admin.token-management.usage-stats', compact('dailyUsage', 'topTenants', 'usageByType'));
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

        $request->validate([
            'ai_enabled' => 'required|boolean'
        ]);

        $tenant->update([
            'ai_enabled' => $request->boolean('ai_enabled')
        ]);

        $statusText = $request->boolean('ai_enabled') ? 'aktif' : 'pasif';

        return response()->json([
            'success' => true,
            'message' => "AI kullanımı {$statusText} hale getirildi.",
            'ai_enabled' => $tenant->ai_enabled
        ]);
    }
}