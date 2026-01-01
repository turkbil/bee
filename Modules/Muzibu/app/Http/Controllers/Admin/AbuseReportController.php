<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Muzibu\App\Models\AbuseReport;
use Modules\Muzibu\App\Jobs\ScanUserForAbuseJob;
use Modules\Muzibu\App\Services\AbuseDetectionService;

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
     * Toplu tarama başlat (tüm aktif kullanıcılar)
     * İki mod destekler:
     * 1. period_days: Son X gün (preset)
     * 2. date_start + date_end: Belirli tarih aralığı (custom)
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

        // Her kullanıcı için job dispatch et
        $dispatched = 0;
        foreach ($userIds as $userId) {
            ScanUserForAbuseJob::dispatch($userId, $periodStart, $periodEnd);
            $dispatched++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$dispatched} kullanıcı için tarama başlatıldı. ({$periodLabel})",
            'count' => $dispatched,
        ]);
    }

    /**
     * Tek bir kullanıcıyı tara
     */
    public function scanUser(Request $request, int $userId)
    {
        $periodDays = $request->input('period_days', 7);
        $periodEnd = now();
        $periodStart = now()->subDays($periodDays);

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
     * API: İstatistikler
     */
    public function apiStats()
    {
        $today = now()->toDateString();

        // Pattern sayılarını hesapla
        $reportsWithPatterns = AbuseReport::whereDate('scan_date', $today)
            ->whereNotNull('patterns_json')
            ->get();

        $patternCounts = [
            'rapid_skips' => 0,
            'high_volume' => 0,
            'repeat_songs' => 0,
            'multi_device' => 0,
            'suspicious_ip' => 0,
            'no_sleep' => 0,
            'bot_like' => 0,
        ];

        foreach ($reportsWithPatterns as $report) {
            $patterns = $report->patterns_json ?? [];
            foreach (array_keys($patternCounts) as $key) {
                if (isset($patterns[$key])) {
                    $patternCounts[$key]++;
                }
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
