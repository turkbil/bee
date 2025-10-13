<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\AI\App\Models\AIConversation;
use Modules\AI\App\Models\AIMessage;
use Carbon\Carbon;

/**
 * 📊 Conversation Analytics Controller
 *
 * Metadata-based conversation analytics:
 * - Device breakdown (mobile, tablet, desktop)
 * - Browser stats (Chrome, Safari, Firefox...)
 * - OS distribution (Windows, Mac, iOS, Android...)
 * - Time-based patterns
 * - Product/Category engagement
 */
class ConversationAnalyticsController extends Controller
{
    /**
     * 📊 Ana Analytics Dashboard
     */
    public function index(Request $request): View
    {
        $tenantId = tenant('id');
        $days = $request->input('days', 30);

        $startDate = Carbon::now()->subDays($days);

        // 📱 Cihaz Dağılımı
        $deviceStats = AIConversation::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->pluck('context_data.device_type')
            ->filter()
            ->countBy()
            ->toArray();

        // 🌐 Tarayıcı Dağılımı
        $browserStats = AIConversation::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->pluck('context_data.browser')
            ->filter()
            ->countBy()
            ->toArray();

        // 💻 İşletim Sistemi Dağılımı
        $osStats = AIConversation::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->pluck('context_data.os')
            ->filter()
            ->countBy()
            ->toArray();

        // 🕐 Saatlik Dağılım
        $hourlyStats = AIConversation::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function($conv) {
                return $conv->created_at->format('H:00');
            })
            ->map->count()
            ->toArray();

        // 📊 Genel İstatistikler
        $totalConversations = AIConversation::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $startDate)
            ->count();

        $totalMessages = AIMessage::whereHas('conversation', function($q) use ($tenantId, $startDate) {
            $q->where('tenant_id', $tenantId)
              ->where('created_at', '>=', $startDate);
        })->count();

        $avgMessagesPerConv = $totalConversations > 0
            ? round($totalMessages / $totalConversations, 1)
            : 0;

        // 🛒 En Popüler Ürünler/Kategoriler
        $productEngagement = AIConversation::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('context_data->product_id')
            ->get()
            ->pluck('context_data.product_id')
            ->countBy()
            ->sortDesc()
            ->take(10)
            ->toArray();

        return view('ai::admin.analytics.conversations', [
            'deviceStats' => $deviceStats,
            'browserStats' => $browserStats,
            'osStats' => $osStats,
            'hourlyStats' => $hourlyStats,
            'totalConversations' => $totalConversations,
            'totalMessages' => $totalMessages,
            'avgMessagesPerConv' => $avgMessagesPerConv,
            'productEngagement' => $productEngagement,
            'days' => $days,
        ]);
    }
}
