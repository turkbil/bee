<?php

namespace Modules\Muzibu\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Muzibu\App\Traits\HasPlaylistDistribution;

class MuzibuCorporateAccount extends Model
{
    use HasPlaylistDistribution;
    protected $table = 'muzibu_corporate_accounts';

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
        'user_id',
        'parent_id',
        'corporate_code',
        'company_name',
        'branch_name',
        'is_active',
        // Spot sistemi
        'spot_enabled',
        'spot_songs_between',
        'spot_current_index',
        'spot_is_paused',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        // Spot sistemi
        'spot_enabled' => 'boolean',
        'spot_songs_between' => 'integer',
        'spot_current_index' => 'integer',
        'spot_is_paused' => 'boolean',
    ];

    /**
     * Kurum sahibi (ana hesap)
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Üst firma (parent)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Şubeler (children)
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Ana firma mı? (şube değil)
     */
    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Ana şube mi?
     */
    public function isMainBranch(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Alt şubeleri getir
     */
    public function getSubBranches()
    {
        return $this->children()->get();
    }

    /**
     * Şirket adını getir (ana şubeyse kendi, alt şubeyse parent'ın)
     */
    public function getCompanyName(): ?string
    {
        if ($this->isMainBranch()) {
            return $this->company_name;
        }

        return $this->parent ? $this->parent->company_name : null;
    }

    /**
     * Tüm şubeleri recursive getir
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Kuruma bağlı üyeler (parent_id ile bağlı kayıtlar)
     */
    public function members(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Kurumun spotları (anonslari)
     */
    public function spots(): HasMany
    {
        return $this->hasMany(CorporateSpot::class, 'corporate_account_id');
    }

    /**
     * Aktif ve şu an geçerli spotları getir
     */
    public function activeSpots()
    {
        return $this->spots()->currentlyActive()->ordered();
    }

    /**
     * Spot dinleme kayıtları
     */
    public function spotPlays(): HasMany
    {
        return $this->hasMany(CorporateSpotPlay::class, 'corporate_account_id');
    }

    /**
     * Bir sonraki spotu getir ve index'i güncelle
     */
    public function getNextSpot(): ?CorporateSpot
    {
        // Spot sistemi kapalıysa veya durdurulmuşsa null döndür
        if (!$this->spot_enabled || $this->spot_is_paused) {
            return null;
        }

        $spot = CorporateSpot::getNextSpot($this->id, $this->spot_current_index);

        if ($spot) {
            // Bir sonraki index'e geç
            $this->increment('spot_current_index');
        }

        return $spot;
    }

    /**
     * Spot rotation index'i sıfırla
     */
    public function resetSpotIndex(): void
    {
        $this->update(['spot_current_index' => 0]);
    }

    /**
     * Üye sayısı
     */
    public function getMembersCountAttribute(): int
    {
        return $this->members()->count();
    }

    /**
     * Benzersiz davet kodu oluştur
     */
    public static function generateCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('corporate_code', $code)->exists());

        return $code;
    }

    /**
     * Kullanıcının kurum kaydını bul
     */
    public static function findByUser(int $userId): ?self
    {
        return self::where('user_id', $userId)->first();
    }

    /**
     * Kullanıcı kurum sahibi mi?
     */
    public static function isUserOwner(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->whereNull('parent_id')
            ->exists();
    }

    /**
     * Kullanıcı üye mi?
     */
    public static function isUserMember(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->whereNotNull('parent_id')
            ->exists();
    }

    /**
     * Kullanıcının bağlı olduğu kurumu getir
     */
    public static function getCorporateForUser(int $userId): ?self
    {
        $record = self::where('user_id', $userId)->first();

        if (!$record) {
            return null;
        }

        // Üyeyse parent kurumu döndür
        if ($record->parent_id) {
            return $record->parent;
        }

        // Kurum sahibiyse kendisini döndür
        return $record;
    }
}
