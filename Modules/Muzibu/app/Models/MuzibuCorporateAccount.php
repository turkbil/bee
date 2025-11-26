<?php

namespace Modules\Muzibu\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MuzibuCorporateAccount extends Model
{
    protected $table = 'muzibu_corporate_accounts';

    protected $fillable = [
        'user_id',
        'parent_id',
        'corporate_code',
        'company_name',
        'branch_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
