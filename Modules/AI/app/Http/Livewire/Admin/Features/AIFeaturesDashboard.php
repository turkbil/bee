<?php

namespace Modules\AI\App\Http\Livewire\Admin\Features;

use Livewire\Component;
use App\Services\AITokenService;
use App\Helpers\TokenHelper;
use Modules\AI\App\Models\AITokenUsage;
use Modules\AI\App\Models\Setting;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AIFeaturesDashboard extends Component
{
    public $refreshInterval = 30; // seconds - 30 saniye
    public $autoRefresh = false; // Varsayılan olarak kapalı
    
    // Real-time data
    public $currentTenant;
    public $tokenStats = [];
    public $dailyUsage = 0;
    public $monthlyUsage = 0;
    public $remainingTokens = 0;
    public $remainingLimit = 0;
    public $lastActivities = [];
    
    // Chart data
    public $chartData = [];
    public $usageByType = [];
    public $weeklyTrend = [];
    
    // Settings
    public $settings;
    
    protected $listeners = [
        'refreshDashboard' => 'loadData',
        'tokenUsed' => 'loadData'
    ];

    public function mount()
    {
        $this->currentTenant = tenancy()->tenant ?? Tenant::first();
        $this->loadData();
    }

    public function render()
    {
        return view('ai::admin.features.ai-features-dashboard');
    }

    public function loadData()
    {
        try {
            if (!$this->currentTenant) {
                $this->currentTenant = Tenant::first();
            }

            $this->loadTokenStats();
            $this->loadUsageStats();
            $this->loadChartData();
            $this->loadLastActivities();
            $this->loadSettings();
            
            // Auto-refresh logic
            if ($this->autoRefresh) {
                $this->dispatch('refreshTimer');
            }
            
        } catch (\Exception $e) {
            \Log::error('AI Dashboard data loading error', [
                'error' => $e->getMessage(),
                'tenant_id' => $this->currentTenant?->id
            ]);
        }
    }

    private function loadTokenStats()
    {
        $tenant = $this->currentTenant;
        
        if (!$tenant) {
            $this->tokenStats = [
                'current_balance' => 0,
                'total_tokens' => 0,
                'used_this_month' => 0,
                'monthly_limit' => 0,
                'remaining_monthly' => 0,
                'percentage_used' => 0,
                'ai_enabled' => false
            ];
            return;
        }

        try {
            // Basitleştirilmiş - sadece TokenHelper kullan
            $this->tokenStats = [
                'current_balance' => TokenHelper::remaining($tenant),
                'real_balance' => TokenHelper::remaining($tenant),
                'total_purchased' => TokenHelper::totalPurchased($tenant),
                'total_used' => TokenHelper::totalUsed($tenant),
                'used_this_month' => TokenHelper::monthlyUsage($tenant),
                'monthly_limit' => TokenHelper::monthlyLimit($tenant),
                'remaining_monthly' => TokenHelper::monthlyLimit($tenant) - TokenHelper::monthlyUsage($tenant),
                'percentage_used' => TokenHelper::monthlyLimit($tenant) > 0 
                    ? round((TokenHelper::monthlyUsage($tenant) / TokenHelper::monthlyLimit($tenant)) * 100, 1) 
                    : 0,
                'ai_enabled' => $tenant->ai_enabled ?? false
            ];

            $this->remainingTokens = $this->tokenStats['real_balance'];
            $this->monthlyUsage = $this->tokenStats['used_this_month'];
            $this->remainingLimit = $this->tokenStats['remaining_monthly'];
            
            \Log::info('AI Dashboard - Token Stats Loaded via TokenHelper', [
                'tenant_id' => $tenant->id,
                'real_balance' => $this->tokenStats['real_balance'],
                'total_purchased' => $this->tokenStats['total_purchased'],
                'total_used' => $this->tokenStats['total_used'],
                'used_this_month' => $this->tokenStats['used_this_month']
            ]);
            
        } catch (\Exception $e) {
            \Log::error('AI Dashboard - Token Stats Loading Error', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            
            // Default values on error
            $this->tokenStats = [
                'current_balance' => 0,
                'real_balance' => 0,
                'total_purchased' => 0,
                'total_used' => 0,
                'used_this_month' => 0,
                'monthly_limit' => 0,
                'remaining_monthly' => 0,
                'percentage_used' => 0,
                'ai_enabled' => false
            ];
            
            $this->remainingTokens = 0;
            $this->monthlyUsage = 0;
            $this->remainingLimit = 0;
        }
    }

    private function loadUsageStats()
    {
        if (!$this->currentTenant) {
            $this->dailyUsage = 0;
            return;
        }

        try {
            // Today's usage
            $this->dailyUsage = TokenHelper::todayUsage($this->currentTenant);
                
            \Log::info('AI Dashboard - Usage Stats Loaded', [
                'tenant_id' => $this->currentTenant->id,
                'daily_usage' => $this->dailyUsage
            ]);
            
        } catch (\Exception $e) {
            \Log::error('AI Dashboard - Usage Stats Loading Error', [
                'tenant_id' => $this->currentTenant->id,
                'error' => $e->getMessage()
            ]);
            
            $this->dailyUsage = 0;
        }
    }

    private function loadChartData()
    {
        if (!$this->currentTenant) {
            $this->chartData = [];
            $this->usageByType = [];
            $this->weeklyTrend = [];
            return;
        }

        try {
            // Usage by type (last 30 days)
            $usageByTypeRaw = AITokenUsage::forTenant($this->currentTenant->id)
                ->where('used_at', '>=', Carbon::now()->subDays(30))
                ->selectRaw('usage_type, SUM(tokens_used) as total')
                ->groupBy('usage_type')
                ->get();
                
            $this->usageByType = $usageByTypeRaw->map(function ($item) {
                return [
                    'type' => $item->usage_type_label ?? ucfirst($item->usage_type),
                    'value' => $item->total,
                    'color' => $this->getTypeColor($item->usage_type)
                ];
            })->toArray();

            // Weekly trend (last 7 days)
            $weekData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $usage = AITokenUsage::forTenant($this->currentTenant->id)
                    ->whereDate('used_at', $date)
                    ->sum('tokens_used');
                    
                $weekData[] = [
                    'date' => $date->format('M d'),
                    'value' => $usage
                ];
            }
            $this->weeklyTrend = $weekData;

            // Chart data for main chart
            $this->chartData = [
                'labels' => collect($this->weeklyTrend)->pluck('date')->toArray(),
                'data' => collect($this->weeklyTrend)->pluck('value')->toArray()
            ];
            
            \Log::info('AI Dashboard - Chart Data Loaded', [
                'tenant_id' => $this->currentTenant->id,
                'usage_by_type_count' => count($this->usageByType),
                'weekly_trend_count' => count($this->weeklyTrend)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('AI Dashboard - Chart Data Loading Error', [
                'tenant_id' => $this->currentTenant->id,
                'error' => $e->getMessage()
            ]);
            
            $this->chartData = [];
            $this->usageByType = [];
            $this->weeklyTrend = [];
        }
    }

    private function loadLastActivities()
    {
        if (!$this->currentTenant) {
            $this->lastActivities = [];
            return;
        }

        try {
            $activities = AITokenUsage::forTenant($this->currentTenant->id)
                ->with('user')
                ->orderBy('used_at', 'desc')
                ->limit(5)
                ->get();
                
            $this->lastActivities = $activities->map(function ($usage) {
                // Türkçe zaman farkı hesaplama
                $timeAgo = 'Bilinmeyen';
                if ($usage->used_at) {
                    $now = now();
                    $usedAt = $usage->used_at;
                    
                    if ($usedAt->gt($now)) {
                        // Gelecek tarih ise "şimdi" göster
                        $timeAgo = 'şimdi';
                    } else {
                        $diffInSeconds = $now->diffInSeconds($usedAt);
                        
                        if ($diffInSeconds < 60) {
                            $timeAgo = 'şimdi';
                        } elseif ($diffInSeconds < 3600) { // 1 saat
                            $minutes = floor($diffInSeconds / 60);
                            $timeAgo = $minutes . ' dakika önce';
                        } elseif ($diffInSeconds < 86400) { // 24 saat
                            $hours = floor($diffInSeconds / 3600);
                            $timeAgo = $hours . ' saat önce';
                        } else {
                            $days = floor($diffInSeconds / 86400);
                            $timeAgo = $days . ' gün önce';
                        }
                    }
                }
                
                return [
                    'id' => $usage->id,
                    'type' => $usage->usage_type_label ?? ucfirst($usage->usage_type),
                    'tokens' => TokenHelper::format($usage->tokens_used),
                    'description' => $usage->description ?? 'AI İşlemi',
                    'user' => $usage->user?->name ?? 'Bilinmeyen',
                    'time' => $timeAgo,
                    'time_exact' => $usage->used_at?->format('H:i:s') ?? ''
                ];
            })->toArray();
            
            \Log::info('AI Dashboard - Last Activities Loaded', [
                'tenant_id' => $this->currentTenant->id,
                'activities_count' => count($this->lastActivities)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('AI Dashboard - Last Activities Loading Error', [
                'tenant_id' => $this->currentTenant->id,
                'error' => $e->getMessage()
            ]);
            
            $this->lastActivities = [];
        }
    }

    private function loadSettings()
    {
        $this->settings = Setting::first();
    }

    private function getTypeColor($type)
    {
        return match($type) {
            'chat' => '#0d6efd',
            'image' => '#20c997',
            'text' => '#fd7e14',
            'translation' => '#6f42c1',
            default => '#6c757d'
        };
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
        
        if ($this->autoRefresh) {
            $this->loadData();
        }
    }

    public function refreshNow()
    {
        $this->loadData();
        $this->dispatch('dataRefreshed');
    }

    public function setRefreshInterval($seconds)
    {
        $this->refreshInterval = max(1, min(60, (int)$seconds));
    }

    // Helper methods for view
    public function getBalancePercentage()
    {
        $purchased = TokenHelper::totalPurchased($this->currentTenant);
        $remaining = TokenHelper::remaining($this->currentTenant);
        
        if ($purchased <= 0) {
            return 0;
        }
        
        return round(($remaining / $purchased) * 100, 1);
    }

    public function getUsagePercentage()
    {
        return TokenHelper::usagePercentage($this->currentTenant);
    }
    
    public function getDailyProgressPercentage()
    {
        return TokenHelper::todayUsagePercentage($this->currentTenant);
    }
    
    public function getAverageProgressPercentage()
    {
        return TokenHelper::dailyAveragePercentage($this->currentTenant);
    }

    public function getStatusColor()
    {
        if (!$this->tokenStats['ai_enabled']) {
            return 'danger';
        }
        
        $percentage = $this->getUsagePercentage();
        if ($percentage >= 90) return 'danger';
        if ($percentage >= 75) return 'warning';
        return 'success';
    }

    public function getBalanceColor()
    {
        $balance = $this->tokenStats['real_balance'];
        if ($balance <= 100) return 'danger';
        if ($balance <= 500) return 'warning';
        return 'success';
    }
}