<?php

namespace Modules\Muzibu\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;

/**
 * CorporateSpot Model - Kurumsal anons/spot
 */
class CorporateSpot extends Model implements HasMedia
{
    use HasFactory, HasMediaManagement;

    protected $table = 'muzibu_corporate_spots';

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
        'corporate_account_id',
        'title',
        'slug',
        'duration',
        'starts_at',
        'ends_at',
        'position',
        'is_enabled',
        'is_archived',
    ];

    protected $casts = [
        'duration' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'position' => 'integer',
        'is_enabled' => 'boolean',
        'is_archived' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Media collections config - Audio ve hero görselleri için
     */
    protected function getMediaConfig(): array
    {
        return [
            'hero' => [
                'type' => 'image',
                'single_file' => true,
                'max_items' => 1,
                'max_size' => 10240, // 10 MB
                'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
                'sortable' => false,
            ],
            'audio' => [
                'type' => 'audio',
                'single_file' => true,
                'max_items' => 1,
                'max_size' => 30720, // 30 MB
                'allowed_types' => ['mp3', 'wav', 'flac', 'm4a', 'ogg', 'aac', 'wma'],
                'sortable' => false,
            ],
        ];
    }

    /**
     * Boot method - Model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($spot) {
            if (empty($spot->slug)) {
                $spot->slug = Str::slug($spot->title);
            }

            // Auto position (last in order)
            if (empty($spot->position)) {
                $maxPosition = self::where('corporate_account_id', $spot->corporate_account_id)->max('position') ?? 0;
                $spot->position = $maxPosition + 1;
            }
        });

        // Update slug on title change
        static::updating(function ($spot) {
            if ($spot->isDirty('title') && !$spot->isDirty('slug')) {
                $spot->slug = Str::slug($spot->title);
            }
        });
    }

    /**
     * Kurumsal hesap ilişkisi
     */
    public function corporateAccount(): BelongsTo
    {
        return $this->belongsTo(MuzibuCorporateAccount::class, 'corporate_account_id');
    }

    /**
     * Dinleme kayıtları
     */
    public function plays(): HasMany
    {
        return $this->hasMany(CorporateSpotPlay::class, 'spot_id');
    }

    /**
     * Aktif kayıtları getir (enabled ve archived değil)
     */
    public function scopeActive($query)
    {
        return $query->where('is_enabled', true)->where('is_archived', false);
    }

    /**
     * Arşivlenmişleri getir
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    /**
     * Sıralama bazında getir
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }

    /**
     * Şu an aktif olan spotları getir (tarih kontrolü)
     */
    public function scopeCurrentlyActive($query)
    {
        $now = now();

        return $query->active()
            ->where(function ($q) use ($now) {
                // starts_at null veya şu andan önce
                $q->where(function ($inner) use ($now) {
                    $inner->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', $now);
                });
            })
            ->where(function ($q) use ($now) {
                // ends_at null veya şu andan sonra
                $q->where(function ($inner) use ($now) {
                    $inner->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', $now);
                });
            });
    }

    /**
     * Ses dosyası URL'i al
     * Spatie Media Library'nin getUrl() metodu tenant-aware URL döndürür
     */
    public function getAudioUrl(): ?string
    {
        $media = $this->getFirstMedia('audio');
        return $media?->getUrl();
    }

    /**
     * Ses dosyası var mı?
     */
    public function hasAudio(): bool
    {
        return $this->getFirstMedia('audio') !== null;
    }

    /**
     * Formatlı süre (MM:SS)
     */
    public function getFormattedDuration(): string
    {
        if (!$this->duration) {
            return '00:00';
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Toplam dinlenme sayısı
     */
    public function getPlayCountAttribute(): int
    {
        return $this->plays()->count();
    }

    /**
     * Bugünkü dinlenme sayısı
     */
    public function getTodayPlayCountAttribute(): int
    {
        return $this->plays()->whereDate('created_at', today())->count();
    }

    /**
     * Atlama sayısı (skipped)
     */
    public function getSkipCountAttribute(): int
    {
        return $this->plays()->where('was_skipped', true)->count();
    }

    /**
     * Atlama oranı (%)
     */
    public function getSkipRateAttribute(): float
    {
        $total = $this->play_count;

        if ($total === 0) {
            return 0.0;
        }

        return round(($this->skip_count / $total) * 100, 2);
    }

    /**
     * Spot aktif mi? (tarih kontrolü dahil)
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_enabled || $this->is_archived) {
            return false;
        }

        $now = now();

        // starts_at kontrolü
        if ($this->starts_at && $this->starts_at > $now) {
            return false;
        }

        // ends_at kontrolü
        if ($this->ends_at && $this->ends_at < $now) {
            return false;
        }

        return true;
    }

    /**
     * Bir sonraki aktif spotu getir (rotation için)
     *
     * @param int $corporateAccountId
     * @param int|null $currentIndex Mevcut index
     * @return self|null
     */
    public static function getNextSpot(int $corporateAccountId, ?int $currentIndex = 0): ?self
    {
        $spots = self::where('corporate_account_id', $corporateAccountId)
            ->currentlyActive()
            ->ordered()
            ->get();

        if ($spots->isEmpty()) {
            return null;
        }

        // Bir sonraki index
        $nextIndex = ($currentIndex + 1) % $spots->count();

        return $spots[$nextIndex] ?? $spots->first();
    }
}
