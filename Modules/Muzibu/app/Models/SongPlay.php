<?php

namespace Modules\Muzibu\App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SongPlay extends BaseModel
{
    use HasFactory;

    protected $table = 'muzibu_song_plays';
    protected $primaryKey = 'id';

    protected $fillable = [
        'song_id',
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Şarkı ilişkisi
     */
    public function song()
    {
        return $this->belongsTo(Song::class, 'song_id', 'song_id');
    }

    /**
     * Kullanıcı ilişkisi
     */
    public function user()
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
     * Benzersiz kullanıcı sayısı (unique listeners)
     */
    public function scopeUniqueListeners($query)
    {
        return $query->distinct('user_id')->whereNotNull('user_id');
    }

    /**
     * Benzersiz IP sayısı
     */
    public function scopeUniqueIPs($query)
    {
        return $query->distinct('ip_address');
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
     * En çok dinlenen şarkıları getir (Top Charts)
     */
    public static function getTopSongs(int $limit = 10, ?string $period = null)
    {
        $query = self::query();

        // Period filtering
        if ($period === 'today') {
            $query->today();
        } elseif ($period === 'week') {
            $query->thisWeek();
        } elseif ($period === 'month') {
            $query->thisMonth();
        }

        return $query->select('song_id', \DB::raw('COUNT(*) as play_count'))
            ->groupBy('song_id')
            ->orderBy('play_count', 'desc')
            ->limit($limit)
            ->with('song')
            ->get();
    }

    /**
     * Cihaz dağılımı istatistiği
     */
    public static function getDeviceDistribution(?string $period = null)
    {
        $query = self::query();

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
    public static function getHourlyDistribution(?string $date = null)
    {
        $query = self::query();

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
     * Benzersiz dinleyici sayısı (unique users + IPs)
     */
    public static function getUniqueListenersCount(?string $period = null): int
    {
        $query = self::query();

        if ($period === 'today') {
            $query->today();
        } elseif ($period === 'week') {
            $query->thisWeek();
        } elseif ($period === 'month') {
            $query->thisMonth();
        }

        $uniqueUsers = $query->whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $uniqueIPs = $query->whereNull('user_id')->distinct('ip_address')->count('ip_address');

        return $uniqueUsers + $uniqueIPs;
    }
}
