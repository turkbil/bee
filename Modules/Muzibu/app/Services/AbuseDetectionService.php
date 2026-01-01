<?php

namespace Modules\Muzibu\App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Muzibu\App\Models\AbuseReport;

/**
 * Suistimal Tespit Servisi - Ping-Pong Sistemi v2
 *
 * B2B Müzik Platformu için hesap paylaşımı tespiti.
 * 1 abonelik = 1 cihaz = 1 aktif stream kuralını denetler.
 *
 * 3 TEMEL PATTERN:
 * 1. Ping-Pong: A→B→A döngüsü (IP, browser, platform, device)
 * 2. Concurrent Different Source: Aynı anda farklı fingerprint
 * 3. Split Stream: Aynı fingerprint + overlap (1 PC → 2 hoparlör)
 *
 * B2B KURALLARI (Normal sayılan):
 * - 15 saat, 7/24 dinleme = NORMAL (işletme)
 * - Yüksek hacim = NORMAL (restoran)
 * - Skip yok = NORMAL (arka plan müzik)
 * - Gece dinleme = NORMAL (24 saat açık mekan)
 *
 * @version 2.0 - Ping-Pong Detection System
 * @see https://muzibu.com.tr/readme/2026/01/01/suistimal-tespit-gelistirme/
 */
class AbuseDetectionService
{
    /**
     * Fingerprint oluşturmak için kullanılan alanlar
     */
    protected array $fingerprintFields = ['ip_address', 'browser', 'platform'];

    /**
     * Ping-Pong tespiti için kontrol edilecek alanlar
     */
    protected array $pingPongFields = ['ip_address', 'browser', 'platform', 'device_key'];

    /**
     * Tenant-aware database bağlantısı al
     */
    protected function getTenantConnection(): \Illuminate\Database\Connection
    {
        $tenant = tenant();
        if (!$tenant) {
            throw new \RuntimeException('Tenant context required for AbuseDetectionService');
        }
        return DB::connection('tenant');
    }

    /**
     * ⚡ EARLY EXIT: Hızlı kontrol - Horizon'a gerek var mı?
     *
     * Kullanıcı hep aynı kıstaslarla girmişse (tek fingerprint),
     * ping-pong OLAMAZ → direkt CLEAN işaretle, Horizon'a gönderme.
     *
     * @param int $userId
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return array ['skip' => bool, 'reason' => string, 'fingerprint_count' => int]
     */
    public function quickCheck(int $userId, Carbon $periodStart, Carbon $periodEnd): array
    {
        // Kullanıcının unique fingerprint sayısını kontrol et
        $fingerprints = $this->getTenantConnection()
            ->table('muzibu_song_plays')
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->select([
                DB::raw("CONCAT(COALESCE(ip_address,''), '|', COALESCE(browser,''), '|', COALESCE(platform,'')) as fingerprint")
            ])
            ->distinct()
            ->pluck('fingerprint');

        $uniqueCount = $fingerprints->filter(fn($f) => $f !== '||')->count();

        // Tek fingerprint = Ping-Pong OLAMAZ
        if ($uniqueCount <= 1) {
            return [
                'skip' => true,
                'reason' => 'single_fingerprint',
                'fingerprint_count' => $uniqueCount,
                'status' => AbuseReport::STATUS_CLEAN,
            ];
        }

        // Birden fazla fingerprint var, Horizon'da detaylı analiz gerekli
        return [
            'skip' => false,
            'reason' => 'multiple_fingerprints',
            'fingerprint_count' => $uniqueCount,
            'status' => null,
        ];
    }

    /**
     * 🎯 ANA TARAMA: Tek bir kullanıcıyı tara ve rapor oluştur
     *
     * @param int $userId
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @param bool $skipQuickCheck Early exit kontrolünü atla (job'dan geliyorsa)
     * @return AbuseReport|null
     */
    public function scanUser(int $userId, Carbon $periodStart, Carbon $periodEnd, bool $skipQuickCheck = false): ?AbuseReport
    {
        // Kullanıcının play verilerini çek
        $plays = $this->getUserPlays($userId, $periodStart, $periodEnd);

        if ($plays->isEmpty()) {
            return null;
        }

        // 🔥 YENİ: Tüm pattern'leri tespit et (3 pattern)
        $patterns = $this->detectAllPatterns($plays);

        // Status belirle (herhangi bir pattern detected ise abuse)
        $hasAbuse = $this->hasAnyPatternDetected($patterns);
        $status = $hasAbuse ? AbuseReport::STATUS_ABUSE : AbuseReport::STATUS_CLEAN;

        // Günlük istatistikleri hesapla
        $dailyStats = $this->calculateDailyStats($plays, $patterns);

        // Abuse score: Tespit edilen pattern sayısı + örnek sayıları
        $abuseScore = $this->calculatePatternScore($patterns);

        // Mevcut raporu güncelle veya yeni oluştur
        return AbuseReport::updateOrCreate(
            [
                'user_id' => $userId,
                'scan_date' => now()->toDateString(),
            ],
            [
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_plays' => $plays->count(),
                'overlap_count' => $patterns['split_stream']['count'] ?? 0,
                'abuse_score' => $abuseScore,
                'status' => $status,
                'overlaps_json' => $patterns['split_stream']['samples'] ?? [],
                'daily_stats' => $dailyStats,
                'patterns_json' => $patterns,
            ]
        );
    }

    /**
     * 🔥 TÜM PATTERN'LERİ TESPİT ET
     *
     * 3 Pattern:
     * 1. Ping-Pong: A→B→A döngüsü (her field için ayrı kontrol)
     * 2. Concurrent Different Source: Aynı anda farklı fingerprint
     * 3. Split Stream: Aynı fingerprint + overlap (1 PC → 2 hoparlör)
     */
    public function detectAllPatterns(Collection $plays): array
    {
        $patterns = [
            'ping_pong' => [
                'detected' => false,
                'fields' => [],
                'cycles' => [],
            ],
            'concurrent_different' => [
                'detected' => false,
                'count' => 0,
                'samples' => [],
            ],
            'split_stream' => [
                'detected' => false,
                'count' => 0,
                'samples' => [],
            ],
        ];

        // 1. Ping-Pong tespiti (her field için)
        foreach ($this->pingPongFields as $field) {
            $pingPongResult = $this->detectPingPong($plays, $field);
            if ($pingPongResult['detected']) {
                $patterns['ping_pong']['detected'] = true;
                $patterns['ping_pong']['fields'][] = $field;
                $patterns['ping_pong']['cycles'] = array_merge(
                    $patterns['ping_pong']['cycles'],
                    $pingPongResult['cycles']
                );
            }
        }

        // 2. Concurrent Different Source tespiti
        $concurrentResult = $this->detectConcurrentDifferentSource($plays);
        $patterns['concurrent_different'] = $concurrentResult;

        // 3. Split Stream tespiti
        $splitResult = $this->detectSplitStream($plays);
        $patterns['split_stream'] = $splitResult;

        return $patterns;
    }

    /**
     * 🔄 PING-PONG TESPİTİ
     *
     * A→B→A döngüsünü tespit eder.
     * Kalıcı geçiş (A→B kalır) = NORMAL
     * Döngü (A→B→A) = ABUSE
     *
     * @param Collection $plays Zaman sıralı play listesi
     * @param string $field Kontrol edilecek alan (ip_address, browser, platform, device_key)
     * @return array ['detected' => bool, 'cycles' => [...]]
     */
    protected function detectPingPong(Collection $plays, string $field): array
    {
        $result = [
            'detected' => false,
            'cycles' => [],
        ];

        // Field değerlerini zaman sırasına göre al
        $values = $plays->pluck($field)->filter()->values()->all();

        if (count($values) < 3) {
            return $result;
        }

        // Ardışık farklı değerleri bul ve döngü ara
        $cycles = [];
        $i = 0;
        while ($i < count($values) - 2) {
            $a = $values[$i];
            $b = $values[$i + 1] ?? null;
            $c = $values[$i + 2] ?? null;

            // A→B→A döngüsü var mı?
            if ($a && $b && $c && $a !== $b && $a === $c) {
                $cycles[] = [
                    'field' => $field,
                    'sequence' => [$a, $b, $a],
                    'position' => $i,
                ];
                $i += 2; // Döngüyü atla
            } else {
                $i++;
            }
        }

        if (!empty($cycles)) {
            $result['detected'] = true;
            $result['cycles'] = array_slice($cycles, 0, 10); // Max 10 örnek
        }

        return $result;
    }

    /**
     * 🔀 CONCURRENT DIFFERENT SOURCE TESPİTİ
     *
     * Aynı anda farklı fingerprint = 2 farklı lokasyon/cihaz
     * Örnek: 14:00'da hem Ankara IP hem İstanbul IP → 2 kişi kullanıyor
     *
     * @param Collection $plays
     * @return array ['detected' => bool, 'count' => int, 'samples' => [...]]
     */
    protected function detectConcurrentDifferentSource(Collection $plays): array
    {
        $result = [
            'detected' => false,
            'count' => 0,
            'samples' => [],
        ];

        $playsArray = $plays->values()->all();
        $count = count($playsArray);
        $samples = [];

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $p1 = $playsArray[$i];
                $p2 = $playsArray[$j];

                // Fingerprint'ler farklı mı?
                $fp1 = $this->getFingerprint($p1);
                $fp2 = $this->getFingerprint($p2);

                if ($fp1 === $fp2) {
                    continue; // Aynı kaynak, bu split stream olabilir
                }

                // Zaman çakışması var mı?
                if ($this->hasTimeOverlap($p1, $p2)) {
                    $samples[] = [
                        'play1' => [
                            'id' => $p1['id'],
                            'song' => $p1['title'],
                            'fingerprint' => $fp1,
                            'ip' => $p1['ip'] ?? '',
                            'browser' => $p1['browser'] ?? '',
                            'time' => $p1['time'],
                        ],
                        'play2' => [
                            'id' => $p2['id'],
                            'song' => $p2['title'],
                            'fingerprint' => $fp2,
                            'ip' => $p2['ip'] ?? '',
                            'browser' => $p2['browser'] ?? '',
                            'time' => $p2['time'],
                        ],
                        'date' => Carbon::parse($p1['time'])->toDateString(),
                    ];
                }
            }
        }

        if (!empty($samples)) {
            $result['detected'] = true;
            $result['count'] = count($samples);
            $result['samples'] = array_slice($samples, 0, 20); // Max 20 örnek
        }

        return $result;
    }

    /**
     * 📺 SPLIT STREAM TESPİTİ
     *
     * Aynı fingerprint + overlap = 1 PC'den 2 hoparlöre yönlendirme
     * Örnek: Aynı Chrome'dan 14:00'da 2 farklı şarkı aynı anda
     *
     * @param Collection $plays
     * @return array ['detected' => bool, 'count' => int, 'samples' => [...]]
     */
    protected function detectSplitStream(Collection $plays): array
    {
        $result = [
            'detected' => false,
            'count' => 0,
            'samples' => [],
        ];

        $playsArray = $plays->values()->all();
        $count = count($playsArray);
        $samples = [];

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $p1 = $playsArray[$i];
                $p2 = $playsArray[$j];

                // Fingerprint'ler aynı mı?
                $fp1 = $this->getFingerprint($p1);
                $fp2 = $this->getFingerprint($p2);

                if ($fp1 !== $fp2) {
                    continue; // Farklı kaynak, bu concurrent different olabilir
                }

                // Zaman çakışması var mı?
                if ($this->hasTimeOverlap($p1, $p2)) {
                    $overlapSeconds = $this->calculateOverlapSeconds($p1, $p2);

                    // Minimum 5 saniye çakışma (gürültü filtresi)
                    if ($overlapSeconds < 5) {
                        continue;
                    }

                    $samples[] = [
                        'play1' => [
                            'id' => $p1['id'],
                            'song' => $p1['title'],
                            'start' => $p1['time'],
                            'end' => $p1['end']->toDateTimeString(),
                        ],
                        'play2' => [
                            'id' => $p2['id'],
                            'song' => $p2['title'],
                            'start' => $p2['time'],
                            'end' => $p2['end']->toDateTimeString(),
                        ],
                        'fingerprint' => $fp1,
                        'overlap_seconds' => $overlapSeconds,
                        'date' => Carbon::parse($p1['time'])->toDateString(),
                    ];
                }
            }
        }

        if (!empty($samples)) {
            $result['detected'] = true;
            $result['count'] = count($samples);
            $result['samples'] = array_slice($samples, 0, 50); // Max 50 örnek
        }

        return $result;
    }

    /**
     * Fingerprint oluştur (IP + Browser + Platform)
     */
    protected function getFingerprint(array $play): string
    {
        return implode('|', [
            $play['ip'] ?? '',
            $play['browser'] ?? '',
            $play['platform'] ?? '',
        ]);
    }

    /**
     * İki play arasında zaman çakışması var mı?
     */
    protected function hasTimeOverlap(array $p1, array $p2): bool
    {
        // p1 daha önce başlamış olmalı
        $first = $p1['start']->lte($p2['start']) ? $p1 : $p2;
        $second = $p1['start']->lte($p2['start']) ? $p2 : $p1;

        // first biterken second başlamış mı?
        return $first['end']->gt($second['start']);
    }

    /**
     * İki play arasındaki çakışma süresini hesapla
     */
    protected function calculateOverlapSeconds(array $p1, array $p2): int
    {
        $first = $p1['start']->lte($p2['start']) ? $p1 : $p2;
        $second = $p1['start']->lte($p2['start']) ? $p2 : $p1;

        $overlapEnd = $first['end']->lt($second['end']) ? $first['end'] : $second['end'];
        return abs($overlapEnd->diffInSeconds($second['start']));
    }

    /**
     * Herhangi bir pattern tespit edildi mi?
     */
    protected function hasAnyPatternDetected(array $patterns): bool
    {
        return ($patterns['ping_pong']['detected'] ?? false)
            || ($patterns['concurrent_different']['detected'] ?? false)
            || ($patterns['split_stream']['detected'] ?? false);
    }

    /**
     * Pattern'lere göre abuse score hesapla
     */
    protected function calculatePatternScore(array $patterns): int
    {
        $score = 0;

        // Ping-Pong: Her döngü 100 puan
        if ($patterns['ping_pong']['detected'] ?? false) {
            $cycleCount = count($patterns['ping_pong']['cycles'] ?? []);
            $score += min($cycleCount * 100, 500);
        }

        // Concurrent Different: Her örnek 50 puan
        if ($patterns['concurrent_different']['detected'] ?? false) {
            $count = $patterns['concurrent_different']['count'] ?? 0;
            $score += min($count * 50, 500);
        }

        // Split Stream: Her örnek 30 puan
        if ($patterns['split_stream']['detected'] ?? false) {
            $count = $patterns['split_stream']['count'] ?? 0;
            $score += min($count * 30, 300);
        }

        return $score;
    }

    /**
     * Kullanıcının play verilerini çek
     */
    public function getUserPlays(int $userId, Carbon $start, Carbon $end): Collection
    {
        return $this->getTenantConnection()
            ->table('muzibu_song_plays as sp')
            ->join('muzibu_songs as s', 'sp.song_id', '=', 's.song_id')
            ->where('sp.user_id', $userId)
            ->whereBetween('sp.created_at', [$start, $end])
            ->orderBy('sp.created_at')
            ->select([
                'sp.id',
                'sp.song_id',
                's.title',
                's.duration',
                'sp.device_type',
                'sp.browser',
                'sp.platform',
                'sp.ip_address',
                'sp.user_agent',
                'sp.created_at',
            ])
            ->get()
            ->map(function ($play) {
                // Title JSON ise parse et
                $title = $play->title;
                if (is_string($title) && str_starts_with($title, '{')) {
                    $decoded = json_decode($title, true);
                    $title = $decoded['tr'] ?? $decoded['en'] ?? $title;
                }

                // Browser tespiti
                $browser = $play->browser;
                if (empty($browser) || $browser === 'Unknown') {
                    $browser = $this->detectBrowser($play->user_agent ?? '');
                }

                $platform = $play->platform ?? 'Unknown';
                $device = $play->device_type ?? 'desktop';

                // Device key: platform + browser + IP son 2 oktet
                $ipShort = '';
                if ($play->ip_address) {
                    $parts = explode('.', $play->ip_address);
                    $ipShort = count($parts) >= 4 ? $parts[2] . '.' . $parts[3] : '';
                }
                $deviceKey = $platform . '-' . $browser . '-' . $ipShort;

                return [
                    'id' => $play->id,
                    'song_id' => $play->song_id,
                    'title' => $title,
                    'duration' => (int) ($play->duration ?? 180),
                    'device' => $device,
                    'browser' => $browser,
                    'platform' => $platform,
                    'device_key' => $deviceKey,
                    'ip' => $play->ip_address,
                    'ip_address' => $play->ip_address, // Ping-pong için alias
                    'time' => $play->created_at,
                    'start' => Carbon::parse($play->created_at),
                    'end' => Carbon::parse($play->created_at)->addSeconds($play->duration ?? 180),
                ];
            });
    }

    /**
     * Browser tespiti (User-Agent'tan)
     */
    protected function detectBrowser(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        // Öncelik sırasına göre kontrol
        if (str_contains($ua, 'edg/') || str_contains($ua, 'edge/')) return 'edge';
        if (str_contains($ua, 'opr/') || str_contains($ua, 'opera')) return 'opera';
        if (str_contains($ua, 'brave')) return 'brave';
        if (str_contains($ua, 'vivaldi')) return 'vivaldi';
        if (str_contains($ua, 'samsungbrowser')) return 'samsung';
        if (str_contains($ua, 'yabrowser') || str_contains($ua, 'yowser')) return 'yandex';
        if (str_contains($ua, 'ucbrowser') || str_contains($ua, 'ubrowser')) return 'ucbrowser';
        if (str_contains($ua, 'firefox') || str_contains($ua, 'fxios')) return 'firefox';
        if (str_contains($ua, 'safari') && !str_contains($ua, 'chrome') && !str_contains($ua, 'chromium')) return 'safari';
        if (str_contains($ua, 'chrome') || str_contains($ua, 'chromium') || str_contains($ua, 'crios')) return 'chrome';
        if (str_contains($ua, 'msie') || str_contains($ua, 'trident')) return 'ie';

        return 'other';
    }

    /**
     * Günlük istatistikleri hesapla
     */
    public function calculateDailyStats(Collection $plays, array $patterns): array
    {
        $dailyStats = [];

        $playsByDate = $plays->groupBy(function ($play) {
            return Carbon::parse($play['time'])->toDateString();
        });

        foreach ($playsByDate as $date => $datePlays) {
            // O güne ait pattern örneklerini say
            $pingPongCount = collect($patterns['ping_pong']['cycles'] ?? [])
                ->filter(fn($c) => true) // Tüm döngüler sayılır
                ->count();

            $concurrentCount = collect($patterns['concurrent_different']['samples'] ?? [])
                ->filter(fn($s) => ($s['date'] ?? '') === $date)
                ->count();

            $splitCount = collect($patterns['split_stream']['samples'] ?? [])
                ->filter(fn($s) => ($s['date'] ?? '') === $date)
                ->count();

            $dailyStats[$date] = [
                'plays' => $datePlays->count(),
                'desktop' => $datePlays->where('device', 'desktop')->count(),
                'mobile' => $datePlays->where('device', 'mobile')->count(),
                'ping_pong' => $pingPongCount,
                'concurrent' => $concurrentCount,
                'split_stream' => $splitCount,
            ];
        }

        return $dailyStats;
    }

    /**
     * Belirli tarih aralığında aktif abonelik sahibi kullanıcıları bul
     */
    public function getActiveUserIdsInRange(Carbon $start, Carbon $end): Collection
    {
        $activeUserIds = $this->getTenantConnection()
            ->table('muzibu_song_plays')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        if ($activeUserIds->isEmpty()) {
            return collect();
        }

        return $this->getTenantConnection()
            ->table('users')
            ->whereIn('id', $activeUserIds)
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '>', now())
            ->pluck('id');
    }

    /**
     * Son X günde aktif abonelik sahibi kullanıcıları bul
     */
    public function getActiveUserIds(int $days = 7): Collection
    {
        return $this->getActiveUserIdsInRange(now()->subDays($days), now());
    }

    /**
     * Kullanıcının timeline verilerini al (UI için)
     */
    public function getUserTimelineData(int $userId, int $periodDays = 7): array
    {
        $periodEnd = now();
        $periodStart = now()->subDays($periodDays);

        $plays = $this->getUserPlays($userId, $periodStart, $periodEnd);
        $patterns = $this->detectAllPatterns($plays);

        // Timeline formatına çevir
        $items = [];
        foreach ($plays as $play) {
            // Bu play herhangi bir pattern'de var mı?
            $isAbuse = $this->isPlayInPatterns($play, $patterns);

            $items[] = [
                'id' => $play['id'],
                'group' => $play['device'],
                'browser' => $play['browser'] ?? 'other',
                'platform' => $play['platform'] ?? 'Unknown',
                'device_key' => $play['device_key'] ?? $play['device'],
                'ip' => $play['ip'] ?? '',
                'content' => $play['title'],
                'start' => $play['time'],
                'end' => $play['end']->toIso8601String(),
                'className' => $play['device'] . ' ' . ($play['browser'] ?? 'other') . ($isAbuse ? ' overlap' : ''),
                'title' => sprintf(
                    "%s\n%s - %s\n%s / %s (%s)",
                    $play['title'],
                    Carbon::parse($play['time'])->format('H:i:s'),
                    $play['end']->format('H:i:s'),
                    ucfirst($play['device']),
                    ucfirst($play['browser'] ?? 'other'),
                    $play['platform'] ?? 'Unknown'
                ),
            ];
        }

        return [
            'items' => $items,
            'patterns' => $patterns,
            'stats' => [
                'total_plays' => $plays->count(),
                'desktop_plays' => $plays->where('device', 'desktop')->count(),
                'mobile_plays' => $plays->where('device', 'mobile')->count(),
                'ping_pong_detected' => $patterns['ping_pong']['detected'],
                'concurrent_detected' => $patterns['concurrent_different']['detected'],
                'split_stream_detected' => $patterns['split_stream']['detected'],
                'abuse_score' => $this->calculatePatternScore($patterns),
            ],
        ];
    }

    /**
     * Bir play herhangi bir pattern'de yer alıyor mu?
     */
    protected function isPlayInPatterns(array $play, array $patterns): bool
    {
        $playId = $play['id'];

        // Concurrent different'da mı?
        foreach ($patterns['concurrent_different']['samples'] ?? [] as $sample) {
            if (($sample['play1']['id'] ?? 0) === $playId || ($sample['play2']['id'] ?? 0) === $playId) {
                return true;
            }
        }

        // Split stream'de mi?
        foreach ($patterns['split_stream']['samples'] ?? [] as $sample) {
            if (($sample['play1']['id'] ?? 0) === $playId || ($sample['play2']['id'] ?? 0) === $playId) {
                return true;
            }
        }

        return false;
    }
}
