<?php

namespace App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\AITokenUsage;

#[Layout('admin.layout')]
class AITokenUsageStatsComponent extends Component
{
    public function render()
    {
        $tenant = tenant();
        
        if (!$tenant) {
            // Central domain için demo veri göster
            $dailyUsage = collect();
            $monthlyUsage = collect();
            $usageByType = collect();
            
            $usageStats = [
                'total_used_all_time' => 0,
                'total_used_this_month' => 0,
                'current_balance' => 1000,
                'monthly_limit' => 500,
                'average_daily_usage' => 0,
                'last_usage' => null
            ];
        } else {
            // Daily usage for last 30 days
            $dailyUsage = AITokenUsage::where('tenant_id', $tenant->id)
                ->selectRaw('DATE(used_at) as date, SUM(tokens_used) as total_tokens, COUNT(*) as usage_count')
                ->where('used_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Monthly usage stats
            $monthlyUsage = AITokenUsage::where('tenant_id', $tenant->id)
                ->selectRaw('YEAR(used_at) as year, MONTH(used_at) as month, SUM(tokens_used) as total_tokens, COUNT(*) as usage_count')
                ->where('used_at', '>=', now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            // Usage by type
            $usageByType = AITokenUsage::where('tenant_id', $tenant->id)
                ->selectRaw('usage_type, SUM(tokens_used) as total_tokens, COUNT(*) as usage_count')
                ->where('used_at', '>=', now()->subDays(30))
                ->groupBy('usage_type')
                ->orderByDesc('total_tokens')
                ->get();

            $usageStats = [
                'total_used_all_time' => AITokenUsage::where('tenant_id', $tenant->id)->sum('tokens_used'),
                'total_used_this_month' => $tenant->ai_tokens_used_this_month,
                'current_balance' => $tenant->ai_tokens_balance,
                'monthly_limit' => $tenant->ai_monthly_token_limit,
                'average_daily_usage' => $dailyUsage->avg('total_tokens'),
                'last_usage' => $tenant->ai_last_used_at
            ];
        }

        return view('livewire.admin.ai-token-usage-stats-component', compact('dailyUsage', 'monthlyUsage', 'usageByType', 'usageStats'));
    }
}