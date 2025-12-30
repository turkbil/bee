<?php

namespace Modules\Muzibu\App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Muzibu\App\Models\AbuseReport;

class AbuseDetectionService
{
    /**
     * Tenant-aware database bağlantısı al
     * Tenant context yoksa exception fırlat
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
     * Tek bir kullanıcıyı tara ve rapor oluştur
     *
     * @param int $userId
     * @param Carbon $periodStart Dönem başlangıcı
     * @param Carbon $periodEnd Dönem sonu
     * @return AbuseReport|null
     */
    public function scanUser(int $userId, Carbon $periodStart, Carbon $periodEnd): ?AbuseReport
    {
        // Kullanıcının play verilerini çek
        $plays = $this->getUserPlays($userId, $periodStart, $periodEnd);

        if ($plays->isEmpty()) {
            return null;
        }

        // Overlap'leri tespit et
        $overlaps = $this->detectOverlaps($plays);

        // 🔥 YENİ: Tüm pattern'leri tespit et
        $patterns = $this->detectAllPatterns($plays, $overlaps);

        // 🔥 YENİ: Toplam abuse score hesapla (overlap + patterns)
        $abuseScore = $this->calculateTotalAbuseScore($overlaps, $patterns);

        // Status belirle
        $status = AbuseReport::determineStatus($abuseScore);

        // Günlük istatistikleri hesapla
        $dailyStats = $this->calculateDailyStats($plays, $overlaps);

        // overlaps_json için max 100 kayıt sınırla (MySQL packet limit koruması)
        // overlap_count zaten toplam sayıyı tutuyor
        $overlapsForStorage = array_slice($overlaps, 0, 100);

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
                'overlap_count' => count($overlaps), // Gerçek toplam sayı
                'abuse_score' => $abuseScore,
                'status' => $status,
                'overlaps_json' => $overlapsForStorage, // Sadece ilk 100 örnek
                'daily_stats' => $dailyStats,
                'patterns_json' => $patterns,
            ]
        );
    }

    /**
     * B2B İşletme Müzik Platformu için Pattern Tespiti
     *
     * Kaldırılan pattern'ler (B2B için geçerli değil):
     * - Hızlı Skip: Personel şarkı değiştirebilir
     * - Tekrar Şarkı: Playlist loop normal
     * - Gece Dinleme: 24 saat açık işletmeler var
     * - Bot Davranışı: Para veren müşteriler bot olamaz
     */
    public function detectAllPatterns(Collection $plays, array $overlaps): array
    {
        $patterns = [];

        // 1. Yüksek Hacim - Günde 600+ şarkı (çoklu stream şüphesi)
        $highVolume = $this->detectHighVolume($plays);
        if (!empty($highVolume)) {
            $patterns['high_volume'] = $highVolume;
        }

        // 2. Çoklu Cihaz - 5+ farklı cihaz/gün (hesap paylaşımı)
        $multiDevice = $this->detectMultiDevice($plays);
        if (!empty($multiDevice)) {
            $patterns['multi_device'] = $multiDevice;
        }

        // 3. Şüpheli IP - 5+ farklı IP/gün (farklı lokasyonlardan erişim)
        $suspiciousIp = $this->detectSuspiciousIp($plays);
        if (!empty($suspiciousIp)) {
            $patterns['suspicious_ip'] = $suspiciousIp;
        }

        return $patterns;
    }

    /**
     * 🔥 HIZLI SKİP TESPİTİ
     * 30 saniyeden kısa dinlemeler = şüpheli
     */
    protected function detectRapidSkips(Collection $plays): array
    {
        $minListenSeconds = 30;
        $rapidSkips = [];
        $totalRapidSkips = 0;

        foreach ($plays as $play) {
            $listenDuration = $play['start']->diffInSeconds($play['end']);
            if ($listenDuration < $minListenSeconds) {
                $totalRapidSkips++;
                $rapidSkips[] = [
                    'play_id' => $play['id'],
                    'song' => $play['title'],
                    'duration' => $listenDuration,
                    'time' => $play['time'],
                ];
            }
        }

        // %30'dan fazla hızlı skip varsa şüpheli
        $skipRate = $plays->count() > 0 ? ($totalRapidSkips / $plays->count()) * 100 : 0;

        if ($skipRate > 30) {
            return [
                'count' => $totalRapidSkips,
                'rate' => round($skipRate, 1),
                'severity' => $skipRate > 60 ? 'high' : ($skipRate > 45 ? 'medium' : 'low'),
                'score' => min($totalRapidSkips * 5, 300), // Max 300 puan
                'samples' => array_slice($rapidSkips, 0, 5),
            ];
        }

        return [];
    }

    /**
     * Yüksek Hacim Pattern - İşletme bazlı eşikler
     *
     * İşletme kullanımı hesabı:
     * - Çalışma saatleri: 08:00 - 24:00 (16 saat)
     * - Ortalama şarkı: 3-4 dakika
     * - Normal: ~300 şarkı/gün (tek cihaz, kesintisiz)
     * - Şüpheli: 600+ şarkı/gün (çoklu stream veya otomasyon)
     */
    protected function detectHighVolume(Collection $plays): array
    {
        $threshold = 600; // İşletme için günde max normal dinleme (16 saat x ~38 şarkı)
        $playsByDay = $plays->groupBy(fn($p) => Carbon::parse($p['time'])->toDateString());

        $highVolumeDays = [];
        foreach ($playsByDay as $date => $dayPlays) {
            $count = $dayPlays->count();
            if ($count > $threshold) {
                $highVolumeDays[] = [
                    'date' => $date,
                    'count' => $count,
                    'excess' => $count - $threshold,
                ];
            }
        }

        if (!empty($highVolumeDays)) {
            $totalExcess = array_sum(array_column($highVolumeDays, 'excess'));
            return [
                'days' => $highVolumeDays,
                'total_excess' => $totalExcess,
                'severity' => $totalExcess > 400 ? 'high' : ($totalExcess > 200 ? 'medium' : 'low'),
                'score' => min($totalExcess, 400), // Max 400 puan
            ];
        }

        return [];
    }

    /**
     * 🔥 TEKRAR ŞARKI TESPİTİ
     * Aynı şarkı 10+ kez = şüpheli (loop abuse)
     */
    protected function detectRepeatSongs(Collection $plays): array
    {
        $threshold = 10;
        $songCounts = $plays->groupBy('song_id')->map->count();
        $repeatedSongs = $songCounts->filter(fn($count) => $count >= $threshold);

        if ($repeatedSongs->isNotEmpty()) {
            $songDetails = [];
            foreach ($repeatedSongs as $songId => $count) {
                $songPlay = $plays->firstWhere('song_id', $songId);
                $songDetails[] = [
                    'song_id' => $songId,
                    'title' => $songPlay['title'] ?? 'Bilinmiyor',
                    'count' => $count,
                ];
            }

            // En çok tekrar edene göre sırala
            usort($songDetails, fn($a, $b) => $b['count'] <=> $a['count']);

            $maxCount = $songDetails[0]['count'] ?? 0;
            return [
                'songs' => array_slice($songDetails, 0, 5),
                'total_repeats' => $repeatedSongs->sum(),
                'severity' => $maxCount > 30 ? 'high' : ($maxCount > 20 ? 'medium' : 'low'),
                'score' => min($repeatedSongs->sum() * 3, 300), // Max 300 puan
            ];
        }

        return [];
    }

    /**
     * 🔥 ÇOKLU CİHAZ TESPİTİ
     * 24 saatte 5+ farklı cihaz = hesap paylaşımı şüphesi
     */
    protected function detectMultiDevice(Collection $plays): array
    {
        $threshold = 5;
        $playsByDay = $plays->groupBy(fn($p) => Carbon::parse($p['time'])->toDateString());

        $multiDeviceDays = [];
        foreach ($playsByDay as $date => $dayPlays) {
            $uniqueDevices = $dayPlays->pluck('device_key')->unique();
            if ($uniqueDevices->count() >= $threshold) {
                $multiDeviceDays[] = [
                    'date' => $date,
                    'device_count' => $uniqueDevices->count(),
                    'devices' => $uniqueDevices->values()->all(),
                ];
            }
        }

        if (!empty($multiDeviceDays)) {
            $maxDevices = max(array_column($multiDeviceDays, 'device_count'));
            return [
                'days' => $multiDeviceDays,
                'max_devices' => $maxDevices,
                'severity' => $maxDevices > 10 ? 'high' : ($maxDevices > 7 ? 'medium' : 'low'),
                'score' => min($maxDevices * 20, 200), // Max 200 puan
            ];
        }

        return [];
    }

    /**
     * 🔥 ŞÜPHELİ IP TESPİTİ
     * Kısa sürede farklı IP'ler = VPN veya hesap paylaşımı
     */
    protected function detectSuspiciousIp(Collection $plays): array
    {
        $threshold = 5; // 24 saatte max normal IP değişimi
        $playsByDay = $plays->groupBy(fn($p) => Carbon::parse($p['time'])->toDateString());

        $suspiciousDays = [];
        foreach ($playsByDay as $date => $dayPlays) {
            $uniqueIps = $dayPlays->pluck('ip')->filter()->unique();
            if ($uniqueIps->count() >= $threshold) {
                $suspiciousDays[] = [
                    'date' => $date,
                    'ip_count' => $uniqueIps->count(),
                    'ips' => $uniqueIps->values()->all(),
                ];
            }
        }

        if (!empty($suspiciousDays)) {
            $maxIps = max(array_column($suspiciousDays, 'ip_count'));
            return [
                'days' => $suspiciousDays,
                'max_ips' => $maxIps,
                'severity' => $maxIps > 10 ? 'high' : ($maxIps > 7 ? 'medium' : 'low'),
                'score' => min($maxIps * 15, 150), // Max 150 puan
            ];
        }

        return [];
    }

    /**
     * 🔥 24/7 DİNLEME TESPİTİ
     * Gece 02:00-06:00 arası sürekli dinleme = bot şüphesi
     */
    protected function detectNoSleepPattern(Collection $plays): array
    {
        // Gece saatlerini kontrol et (02:00 - 06:00)
        $nightPlays = $plays->filter(function ($play) {
            $hour = Carbon::parse($play['time'])->hour;
            return $hour >= 2 && $hour < 6;
        });

        // Gece saatlerinde dinleme olan günleri grupla
        $nightDays = $nightPlays->groupBy(fn($p) => Carbon::parse($p['time'])->toDateString());

        $suspiciousNights = [];
        foreach ($nightDays as $date => $plays) {
            // Gece 5+ şarkı = şüpheli
            if ($plays->count() >= 5) {
                $suspiciousNights[] = [
                    'date' => $date,
                    'night_plays' => $plays->count(),
                    'hours' => $plays->map(fn($p) => Carbon::parse($p['time'])->format('H:i'))->unique()->values()->all(),
                ];
            }
        }

        if (!empty($suspiciousNights)) {
            $totalNightPlays = array_sum(array_column($suspiciousNights, 'night_plays'));
            return [
                'nights' => $suspiciousNights,
                'total_night_plays' => $totalNightPlays,
                'severity' => count($suspiciousNights) > 5 ? 'high' : (count($suspiciousNights) > 3 ? 'medium' : 'low'),
                'score' => min($totalNightPlays * 5, 250), // Max 250 puan
            ];
        }

        return [];
    }

    /**
     * 🔥 TOPLAM ABUSE SCORE HESAPLA
     * Overlap + Pattern skorlarını birleştir
     */
    public function calculateTotalAbuseScore(array $overlaps, array $patterns): int
    {
        // Overlap score
        $overlapScore = $this->calculateAbuseScore($overlaps);

        // Pattern scores
        $patternScore = 0;
        foreach ($patterns as $pattern) {
            $patternScore += $pattern['score'] ?? 0;
        }

        return $overlapScore + $patternScore;
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
                'sp.browser',       // 🔥 NEW: jenssegers/agent ile kaydedilen browser
                'sp.platform',      // 🔥 NEW: jenssegers/agent ile kaydedilen platform
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

                // 🔥 FIX: Önce DB'deki browser değerini kullan, yoksa user_agent'tan parse et
                $browser = $play->browser;
                if (empty($browser) || $browser === 'Unknown') {
                    $browser = $this->detectBrowser($play->user_agent ?? '');
                }

                // Platform bilgisi
                $platform = $play->platform ?? 'Unknown';

                $device = $play->device_type ?? 'desktop';

                // 🔥 Benzersiz cihaz kimliği: platform + browser + IP son 2 oktet
                // Örnek: "macOS-Chrome-192.168" veya "Windows-Firefox-10.0"
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
                    'duration' => (int) ($play->duration ?? 180), // Default 3 dakika
                    'device' => $device,
                    'browser' => $browser,
                    'platform' => $platform,
                    'device_key' => $deviceKey, // Benzersiz cihaz kimliği
                    'ip' => $play->ip_address,
                    'time' => $play->created_at,
                    'start' => Carbon::parse($play->created_at),
                    'end' => Carbon::parse($play->created_at)->addSeconds($play->duration ?? 180),
                ];
            });
    }

    /**
     * 🌐 Gelişmiş Browser Tespiti
     *
     * User-Agent string'inden tarayıcıyı doğru tespit eder.
     * Chromium tabanlı tarayıcıları (Edge, Opera, Brave, Vivaldi, Samsung, Yandex)
     * Chrome'dan ayırt eder.
     *
     * @param string $userAgent
     * @return string Browser adı (lowercase)
     */
    protected function detectBrowser(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        // 🔴 ÖNCELİK SIRASI ÖNEMLİ!
        // Chromium tabanlı tarayıcılar Chrome'dan ÖNCE kontrol edilmeli

        // Edge (Edg/ veya Edge/)
        if (str_contains($ua, 'edg/') || str_contains($ua, 'edge/')) {
            return 'edge';
        }

        // Opera (OPR/ veya Opera/)
        if (str_contains($ua, 'opr/') || str_contains($ua, 'opera')) {
            return 'opera';
        }

        // Brave
        if (str_contains($ua, 'brave')) {
            return 'brave';
        }

        // Vivaldi
        if (str_contains($ua, 'vivaldi')) {
            return 'vivaldi';
        }

        // Samsung Internet
        if (str_contains($ua, 'samsungbrowser')) {
            return 'samsung';
        }

        // Yandex Browser
        if (str_contains($ua, 'yabrowser') || str_contains($ua, 'yowser')) {
            return 'yandex';
        }

        // UC Browser
        if (str_contains($ua, 'ucbrowser') || str_contains($ua, 'ubrowser')) {
            return 'ucbrowser';
        }

        // Firefox
        if (str_contains($ua, 'firefox') || str_contains($ua, 'fxios')) {
            return 'firefox';
        }

        // Safari (Chrome içermemeli!)
        if (str_contains($ua, 'safari') && !str_contains($ua, 'chrome') && !str_contains($ua, 'chromium')) {
            return 'safari';
        }

        // Chrome (en son kontrol - diğerleri zaten return etti)
        if (str_contains($ua, 'chrome') || str_contains($ua, 'chromium') || str_contains($ua, 'crios')) {
            return 'chrome';
        }

        // Internet Explorer
        if (str_contains($ua, 'msie') || str_contains($ua, 'trident')) {
            return 'ie';
        }

        return 'other';
    }

    /**
     * Overlap'leri tespit et
     * TÜM şarkı çakışmalarını kontrol et (aynı tarayıcı dahil!)
     *
     * AMAÇ: Eş zamanlı şarkı dinlemeyi tespit etmek
     * - Chrome + Chrome = ABUSE (2 sekme açılmış)
     * - Chrome + Safari = ABUSE (2 farklı tarayıcı)
     * - Desktop + Mobile = ABUSE (2 farklı cihaz)
     *
     * Kullanıcı aynı tarayıcıda 2 sekme açıp farklı hoparlörlere
     * yönlendirerek dinleyebilir - bu da suistimaldir!
     */
    public function detectOverlaps(Collection $plays): array
    {
        $overlaps = [];
        $playsArray = $plays->values()->all();
        $count = count($playsArray);
        $checkedPairs = []; // Aynı çifti tekrar kontrol etmeyi önle

        // Tüm çiftleri kontrol et (O(n²) ama gerekli)
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $p1 = $playsArray[$i];
                $p2 = $playsArray[$j];

                // 🔥 KALDIRILDI: device_key kontrolü
                // Aynı tarayıcıda 2 sekme açılması da abuse olarak sayılmalı!
                // Amaç: Eş zamanlı şarkı dinlemeyi tespit etmek

                // Aynı çifti tekrar kontrol etme
                $pairKey = min($p1['id'], $p2['id']) . '-' . max($p1['id'], $p2['id']);
                if (isset($checkedPairs[$pairKey])) {
                    continue;
                }
                $checkedPairs[$pairKey] = true;

                // p1 daha önce başlamış olmalı (zaman sıralı)
                $first = $p1['start']->lte($p2['start']) ? $p1 : $p2;
                $second = $p1['start']->lte($p2['start']) ? $p2 : $p1;

                // Çakışma var mı? (first biterken second başlamış mı?)
                if ($first['end']->gt($second['start'])) {
                    // Çakışma süresi hesapla
                    // Gerçek çakışma = min(first_end, second_end) - second_start
                    $overlapEnd = $first['end']->lt($second['end']) ? $first['end'] : $second['end'];
                    $overlapSeconds = abs($overlapEnd->diffInSeconds($second['start']));

                    // Minimum 5 saniye çakışma olsun (gürültüyü filtrele)
                    if ($overlapSeconds < 5) {
                        continue;
                    }

                    // Aynı cihaz/tarayıcı mı kontrol et (UI'da göstermek için)
                    $sameDevice = ($p1['device_key'] ?? $p1['device']) === ($p2['device_key'] ?? $p2['device']);
                    $sameBrowser = strtolower($p1['browser'] ?? '') === strtolower($p2['browser'] ?? '');
                    $sameIp = ($p1['ip'] ?? '') === ($p2['ip'] ?? '');

                    $overlaps[] = [
                        'play1' => [
                            'id' => $first['id'],
                            'song' => $first['title'],
                            'device' => $first['device'],
                            'browser' => $first['browser'] ?? 'unknown',
                            'platform' => $first['platform'] ?? 'Unknown',
                            'device_key' => $first['device_key'] ?? $first['device'],
                            'ip' => $first['ip'] ?? '',
                            'start' => $first['time'],
                            'end' => $first['end']->toDateTimeString(),
                        ],
                        'play2' => [
                            'id' => $second['id'],
                            'song' => $second['title'],
                            'device' => $second['device'],
                            'browser' => $second['browser'] ?? 'unknown',
                            'platform' => $second['platform'] ?? 'Unknown',
                            'device_key' => $second['device_key'] ?? $second['device'],
                            'ip' => $second['ip'] ?? '',
                            'start' => $second['time'],
                            'end' => $second['end']->toDateTimeString(),
                        ],
                        'overlap_seconds' => $overlapSeconds,
                        'overlap_start' => $second['time'],
                        'overlap_end' => $overlapEnd->toDateTimeString(),
                        'date' => Carbon::parse($first['time'])->toDateString(),
                        // 🔥 Yeni flag'ler
                        'same_device' => $sameDevice,
                        'same_browser' => $sameBrowser,
                        'same_ip' => $sameIp,
                    ];
                }
            }
        }

        // Çakışmaları zaman sırasına göre sırala
        usort($overlaps, function ($a, $b) {
            return strcmp($a['overlap_start'], $b['overlap_start']);
        });

        return $overlaps;
    }

    /**
     * Abuse score hesapla (toplam çakışma saniyesi)
     */
    public function calculateAbuseScore(array $overlaps): int
    {
        return array_reduce($overlaps, function ($carry, $overlap) {
            return $carry + ($overlap['overlap_seconds'] ?? 0);
        }, 0);
    }

    /**
     * Günlük istatistikleri hesapla
     */
    public function calculateDailyStats(Collection $plays, array $overlaps): array
    {
        $dailyStats = [];

        // Play'leri günlere göre grupla
        $playsByDate = $plays->groupBy(function ($play) {
            return Carbon::parse($play['time'])->toDateString();
        });

        foreach ($playsByDate as $date => $datePlays) {
            // O güne ait overlap'leri bul
            $dateOverlaps = array_filter($overlaps, fn($o) => $o['date'] === $date);
            $dateAbuseScore = array_reduce($dateOverlaps, fn($c, $o) => $c + $o['overlap_seconds'], 0);

            $dailyStats[$date] = [
                'plays' => $datePlays->count(),
                'desktop' => $datePlays->where('device', 'desktop')->count(),
                'mobile' => $datePlays->where('device', 'mobile')->count(),
                'overlaps' => count($dateOverlaps),
                'abuse_score' => $dateAbuseScore,
            ];
        }

        return $dailyStats;
    }

    /**
     * AKILLI TARAMA: Belirli tarih aralığında şarkı dinleyen AKTİF ABONELİK SAHİBİ kullanıcıları bul
     * Aktif = users.subscription_expires_at > NOW()
     *
     * @param Carbon $start Başlangıç tarihi
     * @param Carbon $end Bitiş tarihi
     * @return Collection User ID listesi
     */
    public function getActiveUserIdsInRange(Carbon $start, Carbon $end): Collection
    {
        // 1. Belirtilen tarih aralığında şarkı dinleyen kullanıcıları bul
        $activeUserIds = $this->getTenantConnection()
            ->table('muzibu_song_plays')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        if ($activeUserIds->isEmpty()) {
            return collect();
        }

        // 2. Bu kullanıcılardan subscription_expires_at > NOW() olanları bul (tenant DB)
        return $this->getTenantConnection()
            ->table('users')
            ->whereIn('id', $activeUserIds)
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '>', now())
            ->pluck('id');
    }

    /**
     * AKILLI TARAMA: Son X günde şarkı dinleyen AKTİF ABONELİK SAHİBİ kullanıcıları bul
     * (Geriye dönük uyumluluk için)
     *
     * @param int $days Kaç günlük aktivite kontrol edilsin
     * @return Collection User ID listesi
     */
    public function getActiveUserIds(int $days = 7): Collection
    {
        return $this->getActiveUserIdsInRange(now()->subDays($days), now());
    }

    /**
     * Belirli bir kullanıcının timeline verilerini al
     */
    public function getUserTimelineData(int $userId, int $periodDays = 7): array
    {
        $periodEnd = now();
        $periodStart = now()->subDays($periodDays);

        $plays = $this->getUserPlays($userId, $periodStart, $periodEnd);
        $overlaps = $this->detectOverlaps($plays);

        // Timeline formatına çevir
        $items = [];
        foreach ($plays as $play) {
            $isOverlapping = collect($overlaps)->contains(function ($o) use ($play) {
                return $o['play1']['id'] === $play['id'] || $o['play2']['id'] === $play['id'];
            });

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
                'className' => $play['device'] . ' ' . ($play['browser'] ?? 'other') . ($isOverlapping ? ' overlap' : ''),
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
            'overlaps' => $overlaps,
            'stats' => [
                'total_plays' => $plays->count(),
                'desktop_plays' => $plays->where('device', 'desktop')->count(),
                'mobile_plays' => $plays->where('device', 'mobile')->count(),
                'overlap_count' => count($overlaps),
                'abuse_score' => $this->calculateAbuseScore($overlaps),
            ],
        ];
    }
}
