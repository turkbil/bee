<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Muzibu\App\Models\AbuseReport;
use Modules\Muzibu\App\Jobs\ScanUserForAbuseJob;
use Modules\Muzibu\App\Services\AbuseDetectionService;

/**
 * Suistimal Rapor Controller - Ping-Pong Sistemi v2
 *
 * Early Exit Optimizasyonu:
 * - Tek fingerprint'li kullanıcılar → Direkt CLEAN (Horizon'a gönderilmez)
 * - Birden fazla fingerprint → Horizon'da detaylı analiz
 *
 * @see AbuseDetectionService
 */
class AbuseReportController extends Controller
{
    protected AbuseDetectionService $service;

    public function __construct(AbuseDetectionService $service)
    {
        $this->service = $service;
    }

    /**
     * Abuse raporları listesi
     */
    public function index()
    {
        return view('muzibu::admin.abuse-reports.index');
    }

    /**
     * Tek bir raporun detayı (Timeline ile)
     */
    public function show(int $id)
    {
        $report = AbuseReport::with(['user', 'reviewer'])->findOrFail($id);

        // Kullanıcının timeline verilerini al (Vis.js için)
        $timelineData = $this->service->getUserTimelineData($report->user_id, 7);

        return view('muzibu::admin.abuse-reports.show', [
            'report' => $report,
            'timelineData' => $timelineData,
        ]);
    }

    /**
     * 🔥 Toplu tarama başlat (Early Exit optimizasyonu ile)
     *
     * Akış:
     * 1. Aktif kullanıcıları bul
     * 2. Her kullanıcı için quickCheck yap
     * 3. Tek fingerprint → Direkt CLEAN rapor oluştur
     * 4. Birden fazla fingerprint → Horizon'a gönder
     */
    public function startScan(Request $request)
    {
        $request->validate([
            'period_days' => 'nullable|integer|min:1|max:365',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
        ]);

        // Tarih aralığını belirle
        if ($request->has('date_start') && $request->has('date_end')) {
            // Custom tarih aralığı
            $periodStart = \Carbon\Carbon::parse($request->date_start)->startOfDay();
            $periodEnd = \Carbon\Carbon::parse($request->date_end)->endOfDay();
            $periodDays = $periodStart->diffInDays($periodEnd) + 1;
            $periodLabel = $periodStart->format('d.m.Y') . ' - ' . $periodEnd->format('d.m.Y');
        } else {
            // Preset: Son X gün
            $periodDays = $request->input('period_days', 7);
            $periodEnd = now();
            $periodStart = now()->subDays($periodDays);
            $periodLabel = "Son {$periodDays} gün";
        }

        // Seçilen dönemde play kaydı olan aboneleri bul
        $userIds = $this->service->getActiveUserIdsInRange($periodStart, $periodEnd);

        if ($userIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "Taranacak aktif kullanıcı bulunamadı. ({$periodLabel})",
            ]);
        }

        // ⚡ Early Exit ile akıllı tarama
        $earlyExitCount = 0;  // Tek fingerprint, direkt CLEAN
        $horizonCount = 0;    // Birden fazla fingerprint, Horizon'a gönderildi

        foreach ($userIds as $userId) {
            // 🔥 Quick Check: Tek fingerprint mi?
            $quickResult = $this->service->quickCheck($userId, $periodStart, $periodEnd);

            if ($quickResult['skip']) {
                // Tek fingerprint → Direkt CLEAN rapor oluştur
                $this->createCleanReport($userId, $periodStart, $periodEnd, $quickResult);
                $earlyExitCount++;
            } else {
                // Birden fazla fingerprint → Horizon'da detaylı analiz
                ScanUserForAbuseJob::dispatch($userId, $periodStart, $periodEnd);
                $horizonCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => sprintf(
                "Tarama başlatıldı. (%s)\n⚡ %d kullanıcı Early Exit (tek fingerprint)\n🔍 %d kullanıcı Horizon'da analiz ediliyor",
                $periodLabel,
                $earlyExitCount,
                $horizonCount
            ),
            'total' => $userIds->count(),
            'early_exit' => $earlyExitCount,
            'horizon' => $horizonCount,
        ]);
    }

    /**
     * ⚡ Early Exit için CLEAN rapor oluştur
     */
    protected function createCleanReport(int $userId, $periodStart, $periodEnd, array $quickResult): void
    {
        // Play sayısını al
        $playCount = \Illuminate\Support\Facades\DB::connection('tenant')
            ->table('muzibu_song_plays')
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->count();

        AbuseReport::updateOrCreate(
            [
                'user_id' => $userId,
                'scan_date' => now()->toDateString(),
            ],
            [
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_plays' => $playCount,
                'overlap_count' => 0,
                'abuse_score' => 0,
                'status' => AbuseReport::STATUS_CLEAN,
                'overlaps_json' => [],
                'daily_stats' => [],
                'patterns_json' => [
                    'early_exit' => true,
                    'reason' => $quickResult['reason'],
                    'fingerprint_count' => $quickResult['fingerprint_count'],
                    'ping_pong' => ['detected' => false, 'fields' => [], 'cycles' => []],
                    'concurrent_different' => ['detected' => false, 'count' => 0, 'samples' => []],
                    'split_stream' => ['detected' => false, 'count' => 0, 'samples' => []],
                ],
            ]
        );
    }

    /**
     * Tek bir kullanıcıyı tara (Early Exit ile)
     */
    public function scanUser(Request $request, int $userId)
    {
        $periodDays = $request->input('period_days', 7);
        $periodEnd = now();
        $periodStart = now()->subDays($periodDays);

        // Quick Check
        $quickResult = $this->service->quickCheck($userId, $periodStart, $periodEnd);

        if ($quickResult['skip']) {
            // Tek fingerprint → Direkt CLEAN
            $this->createCleanReport($userId, $periodStart, $periodEnd, $quickResult);
            return response()->json([
                'success' => true,
                'message' => "Kullanıcı #{$userId} ⚡ Early Exit (tek fingerprint) - CLEAN",
            ]);
        }

        // Birden fazla fingerprint → Horizon'a gönder
        ScanUserForAbuseJob::dispatch($userId, $periodStart, $periodEnd);

        return response()->json([
            'success' => true,
            'message' => "Kullanıcı #{$userId} için tarama başlatıldı. (Son {$periodDays} gün)",
        ]);
    }

    /**
     * Raporu incele ve aksiyon al
     */
    public function review(Request $request, int $id)
    {
        $request->validate([
            'action' => 'required|in:none,warned,suspended',
            'notes' => 'nullable|string|max:1000',
        ]);

        $report = AbuseReport::findOrFail($id);

        $report->markAsReviewed(
            Auth::id(),
            $request->input('action'),
            $request->input('notes')
        );

        return response()->json([
            'success' => true,
            'message' => 'Rapor güncellendi.',
        ]);
    }

    /**
     * API: Rapor listesi (AJAX için)
     */
    public function apiList(Request $request)
    {
        $query = AbuseReport::with('user')
            ->orderByDesc('scan_date')
            ->orderByDesc('abuse_score');

        // Filtreleme
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('scan_date', $request->date);
        }

        // Pagination
        $reports = $query->paginate(20);

        return response()->json($reports);
    }

    /**
     * API: İstatistikler (Ping-Pong sistemi için güncellendi)
     */
    public function apiStats()
    {
        $today = now()->toDateString();

        // Pattern sayılarını hesapla (Yeni 3 pattern sistemi)
        $reportsWithPatterns = AbuseReport::whereDate('scan_date', $today)
            ->whereNotNull('patterns_json')
            ->get();

        $patternCounts = [
            'ping_pong' => 0,
            'concurrent_different' => 0,
            'split_stream' => 0,
            'early_exit' => 0,
        ];

        foreach ($reportsWithPatterns as $report) {
            $patterns = $report->patterns_json ?? [];

            if ($patterns['early_exit'] ?? false) {
                $patternCounts['early_exit']++;
            }
            if ($patterns['ping_pong']['detected'] ?? false) {
                $patternCounts['ping_pong']++;
            }
            if ($patterns['concurrent_different']['detected'] ?? false) {
                $patternCounts['concurrent_different']++;
            }
            if ($patterns['split_stream']['detected'] ?? false) {
                $patternCounts['split_stream']++;
            }
        }

        $stats = [
            'total_scanned' => AbuseReport::whereDate('scan_date', $today)->count(),
            'clean' => AbuseReport::whereDate('scan_date', $today)->clean()->count(),
            'suspicious' => AbuseReport::whereDate('scan_date', $today)->suspicious()->count(),
            'abuse' => AbuseReport::whereDate('scan_date', $today)->abuse()->count(),
            'unreviewed' => AbuseReport::unreviewed()->count(),
            'last_scan' => AbuseReport::latest('created_at')->first()?->created_at?->diffForHumans(),
            'with_patterns' => $reportsWithPatterns->count(),
            'pattern_counts' => $patternCounts,
        ];

        return response()->json($stats);
    }

    /**
     * API: Kullanıcının timeline verilerini al
     */
    public function apiTimeline(int $userId, Request $request)
    {
        $periodDays = $request->input('period_days', 7);
        $timelineData = $this->service->getUserTimelineData($userId, $periodDays);

        return response()->json($timelineData);
    }

    /**
     * API: Kullanıcı arama (tek kullanıcı tarama için)
     */
    public function apiUsers(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Central database'den kullanıcıları ara
        $users = \App\Models\User::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}
