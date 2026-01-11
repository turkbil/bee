<?php

namespace Modules\Muzibu\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CorporateSpotPlay Model - Spot dinleme geçmişi
 * NOT: SongPlay yapısının kopyası
 */
class CorporateSpotPlay extends Model
{
    use HasFactory;

    protected $table = 'muzibu_corporate_spot_plays';

    /**
     * Dinamik connection resolver
     * Central tenant ise mysql (default), değilse tenant connection
     */
    public function getConnectionName()
    {
        if (function_exists('tenant') && tenant() && !tenant()->central) {
            return 'tenant';
        }
        return config('database.default');
    }

    protected $fillable = [
        'spot_id',
        'corporate_account_id',
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'listened_duration',
        'was_skipped',
        'source_type',
        'source_id',
        'ended_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'ended_at' => 'datetime',
        'listened_duration' => 'integer',
        'was_skipped' => 'boolean',
        'source_id' => 'integer',
    ];

    /**
     * Spot ilişkisi
     */
    public function spot(): BelongsTo
    {
        return $this->belongsTo(CorporateSpot::class, 'spot_id');
    }

    /**
     * Kurumsal hesap ilişkisi
     */
    public function corporateAccount(): BelongsTo
    {
        return $this->belongsTo(MuzibuCorporateAccount::class, 'corporate_account_id');
    }

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Belirli bir tarih aralığındaki dinlemeleri getir
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Belirli bir cihaz tipine göre filtrele
     */
    public function scopeByDevice($query, string $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Bugünün dinlemelerini getir
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Bu haftanın dinlemelerini getir
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Bu ayın dinlemelerini getir
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    /**
     * Atlanan spotları getir
     */
    public function scopeSkipped($query)
    {
        return $query->where('was_skipped', true);
    }

    /**
     * Tamamlanan spotları getir (atlanmayan)
     */
    public function scopeCompleted($query)
    {
        return $query->where('was_skipped', false)->whereNotNull('ended_at');
    }

    /**
     * Belirli bir kaynaktan gelen dinlemeleri getir
     */
    public function scopeFromSource($query, string $sourceType, ?int $sourceId = null)
    {
        $query->where('source_type', $sourceType);

        if ($sourceId !== null) {
            $query->where('source_id', $sourceId);
        }

        return $query;
    }

    /**
     * Tamamlanmış dinlemeleri getir (ended_at dolu olanlar)
     */
    public function scopeEnded($query)
    {
        return $query->whereNotNull('ended_at');
    }

    /**
     * Devam eden dinlemeleri getir (ended_at boş olanlar)
     */
    public function scopeOngoing($query)
    {
        return $query->whereNull('ended_at');
    }

    /**
     * Cihaz dağılımı istatistiği
     */
    public static function getDeviceDistribution(?int $corporateAccountId = null, ?string $period = null)
    {
        $query = self::query();

        if ($corporateAccountId) {
            $query->where('corporate_account_id', $corporateAccountId);
        }

        if ($period === 'today') {
            $query->today();
        } elseif ($period === 'week') {
            $query->thisWeek();
        } elseif ($period === 'month') {
            $query->thisMonth();
        }

        return $query->select('device_type', \DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->get();
    }

    /**
     * Saatlik dinleme dağılımı
     */
    public static function getHourlyDistribution(?int $corporateAccountId = null, ?string $date = null)
    {
        $query = self::query();

        if ($corporateAccountId) {
            $query->where('corporate_account_id', $corporateAccountId);
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        } else {
            $query->today();
        }

        return $query->select(
            \DB::raw('HOUR(created_at) as hour'),
            \DB::raw('COUNT(*) as count')
        )
            ->groupBy(\DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get();
    }

    /**
     * En çok dinlenen spotları getir
     */
    public static function getTopSpots(int $corporateAccountId, int $limit = 10, ?string $period = null)
    {
        $query = self::where('corporate_account_id', $corporateAccountId);

        if ($period === 'today') {
            $query->today();
        } elseif ($period === 'week') {
            $query->thisWeek();
        } elseif ($period === 'month') {
            $query->thisMonth();
        }

        return $query->select('spot_id', \DB::raw('COUNT(*) as play_count'))
            ->groupBy('spot_id')
            ->orderBy('play_count', 'desc')
            ->limit($limit)
            ->with('spot')
            ->get();
    }

    /**
     * Skip oranını hesapla
     */
    public static function getSkipRate(?int $corporateAccountId = null, ?string $period = null): float
    {
        $query = self::whereNotNull('ended_at');

        if ($corporateAccountId) {
            $query->where('corporate_account_id', $corporateAccountId);
        }

        if ($period === 'today') {
            $query->today();
        } elseif ($period === 'week') {
            $query->thisWeek();
        } elseif ($period === 'month') {
            $query->thisMonth();
        }

        $total = $query->count();
        if ($total === 0) {
            return 0.0;
        }

        $skipped = (clone $query)->where('was_skipped', true)->count();

        return round(($skipped / $total) * 100, 2);
    }

    /**
     * Ortalama dinleme süresini hesapla
     */
    public static function getAverageListenDuration(?int $corporateAccountId = null, ?string $period = null): float
    {
        $query = self::whereNotNull('listened_duration');

        if ($corporateAccountId) {
            $query->where('corporate_account_id', $corporateAccountId);
        }

        if ($period === 'today') {
            $query->today();
        } elseif ($period === 'week') {
            $query->thisWeek();
        } elseif ($period === 'month') {
            $query->thisMonth();
        }

        return round($query->avg('listened_duration') ?? 0, 2);
    }

    /**
     * Yeni spot play kaydı oluştur (helper)
     */
    public static function logPlay(
        int $spotId,
        int $corporateAccountId,
        ?int $userId = null,
        ?string $sourceType = null,
        ?int $sourceId = null
    ): self {
        $request = request();

        return self::create([
            'spot_id' => $spotId,
            'corporate_account_id' => $corporateAccountId,
            'user_id' => $userId,
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'device_type' => self::detectDeviceType($request->userAgent()),
            'browser' => self::detectBrowser($request->userAgent()),
            'platform' => self::detectPlatform($request->userAgent()),
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);
    }

    /**
     * Cihaz tipini tespit et
     */
    protected static function detectDeviceType(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        if (preg_match('/mobile/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Tarayıcı tespit et
     */
    protected static function detectBrowser(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        $browsers = [
            'Chrome' => '/Chrome\/[\d.]+/i',
            'Firefox' => '/Firefox\/[\d.]+/i',
            'Safari' => '/Safari\/[\d.]+/i',
            'Edge' => '/Edg(e)?\/[\d.]+/i',
            'Opera' => '/OPR\/[\d.]+/i',
        ];

        foreach ($browsers as $name => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $name;
            }
        }

        return 'Other';
    }

    /**
     * Platform tespit et
     */
    protected static function detectPlatform(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        $platforms = [
            'Windows' => '/Windows/i',
            'macOS' => '/Macintosh|Mac OS/i',
            'Linux' => '/Linux/i',
            'iOS' => '/iPhone|iPad|iPod/i',
            'Android' => '/Android/i',
        ];

        foreach ($platforms as $name => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $name;
            }
        }

        return 'Other';
    }
}
