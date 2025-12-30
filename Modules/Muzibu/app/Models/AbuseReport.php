<?php

namespace Modules\Muzibu\App\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbuseReport extends BaseModel
{
    use HasFactory;

    protected $table = 'muzibu_abuse_reports';

    /**
     * Dinamik connection resolver
     * Muzibu modülü SADECE tenant 1001 için, ZORLA tenant connection kullan!
     */
    public function getConnectionName()
    {
        return 'tenant';
    }

    protected $fillable = [
        'user_id',
        'scan_date',
        'period_start',
        'period_end',
        'total_plays',
        'overlap_count',
        'abuse_score',
        'status',
        'overlaps_json',
        'daily_stats',
        'patterns_json', // 🔥 YENİ: Tespit edilen pattern'ler
        'reviewed_by',
        'reviewed_at',
        'action_taken',
        'notes',
    ];

    protected $casts = [
        'scan_date' => 'date',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'total_plays' => 'integer',
        'overlap_count' => 'integer',
        'abuse_score' => 'integer',
        'overlaps_json' => 'array',
        'daily_stats' => 'array',
        'patterns_json' => 'array', // 🔥 YENİ
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_CLEAN = 'clean';
    const STATUS_SUSPICIOUS = 'suspicious';
    const STATUS_ABUSE = 'abuse';

    /**
     * Action constants
     */
    const ACTION_NONE = 'none';
    const ACTION_WARNED = 'warned';
    const ACTION_SUSPENDED = 'suspended';

    /**
     * Threshold constants (saniye cinsinden)
     */
    const THRESHOLD_SUSPICIOUS = 300;  // 5 dakika
    const THRESHOLD_ABUSE = 600;       // 10 dakika

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Taranan kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * İnceleyen admin
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Sadece abuse olanlar
     */
    public function scopeAbuse($query)
    {
        return $query->where('status', self::STATUS_ABUSE);
    }

    /**
     * Sadece suspicious olanlar
     */
    public function scopeSuspicious($query)
    {
        return $query->where('status', self::STATUS_SUSPICIOUS);
    }

    /**
     * Sadece clean olanlar
     */
    public function scopeClean($query)
    {
        return $query->where('status', self::STATUS_CLEAN);
    }

    /**
     * İncelenmemiş olanlar
     */
    public function scopeUnreviewed($query)
    {
        return $query->whereNull('reviewed_at');
    }

    /**
     * Belirli tarihte tarama
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('scan_date', $date);
    }

    /**
     * Skor sıralaması (en yüksek önce)
     */
    public function scopeHighestScore($query)
    {
        return $query->orderByDesc('abuse_score');
    }

    // =============================================
    // ACCESSORS
    // =============================================

    /**
     * Status badge rengi
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ABUSE => 'danger',
            self::STATUS_SUSPICIOUS => 'warning',
            default => 'success',
        };
    }

    /**
     * Status label (Türkçe)
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ABUSE => 'Suistimal',
            self::STATUS_SUSPICIOUS => 'Şüpheli',
            default => 'Temiz',
        };
    }

    /**
     * Action label (Türkçe)
     */
    public function getActionLabelAttribute(): ?string
    {
        return match ($this->action_taken) {
            self::ACTION_WARNED => 'Uyarıldı',
            self::ACTION_SUSPENDED => 'Askıya Alındı',
            self::ACTION_NONE => 'İşlem Yapılmadı',
            default => null,
        };
    }

    /**
     * Abuse score formatted (dakika:saniye)
     */
    public function getAbuseScoreFormattedAttribute(): string
    {
        $minutes = floor($this->abuse_score / 60);
        $seconds = $this->abuse_score % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * İncelenmiş mi?
     */
    public function getIsReviewedAttribute(): bool
    {
        return !is_null($this->reviewed_at);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Score'a göre status belirle
     */
    public static function determineStatus(int $abuseScore): string
    {
        if ($abuseScore >= self::THRESHOLD_ABUSE) {
            return self::STATUS_ABUSE;
        }

        if ($abuseScore >= self::THRESHOLD_SUSPICIOUS) {
            return self::STATUS_SUSPICIOUS;
        }

        return self::STATUS_CLEAN;
    }

    /**
     * Admin tarafından incelendi olarak işaretle
     */
    public function markAsReviewed(int $adminId, ?string $action = null, ?string $notes = null): bool
    {
        return $this->update([
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'action_taken' => $action ?? self::ACTION_NONE,
            'notes' => $notes,
        ]);
    }
}
