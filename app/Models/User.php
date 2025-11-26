<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Modules\UserManagement\App\Traits\HasModulePermissions;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, InteractsWithMedia, HasModulePermissions, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'is_active',
        'admin_locale',
        'tenant_locale',
        'dashboard_preferences',
        'phone',
        'bio',
        // Membership fields
        'device_limit',
        'is_approved',
        'failed_login_attempts',
        'locked_until',
        'two_factor_enabled',
        'two_factor_phone',
        'corporate_account_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'dashboard_preferences' => 'json',
            // Membership casts
            'is_approved' => 'boolean',
            'locked_until' => 'datetime',
            'two_factor_enabled' => 'boolean',
        ];
    }
    
    /**
     * Media koleksiyonları için tanımlamalar
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
             ->singleFile()
             ->useDisk('public');
    }
    
    /**
     * PERFORMANCE: Cached role check to reduce DB queries
     */
    public function hasCachedRole($role): bool
    {
        static $roleCache = [];
        $cacheKey = "user_{$this->id}_role_{$role}";

        if (!isset($roleCache[$cacheKey])) {
            $roleCache[$cacheKey] = cache()->remember("user_role_{$this->id}_{$role}", 300, function() use ($role) {
                return $this->hasRole($role);
            });
        }

        return $roleCache[$cacheKey];
    }

    // ==========================================
    // MEMBERSHIP RELATIONSHIPS
    // ==========================================

    /**
     * Get active subscription
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class)->active();
    }

    /**
     * Get all subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get customer addresses
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    /**
     * Get corporate account user belongs to
     * Only works for Muzibu tenant (1001)
     */
    public function corporateAccount()
    {
        // Tenant-aware: Only Muzibu uses corporate accounts
        if (!$this->isMuzibuTenant()) {
            return $this->belongsTo(self::class, 'id')->whereRaw('1=0'); // Empty relation
        }

        return $this->belongsTo(\Modules\Muzibu\App\Models\MuzibuCorporateAccount::class);
    }

    /**
     * Get corporate account owned by user
     * Only works for Muzibu tenant (1001)
     */
    public function ownedCorporateAccount()
    {
        // Tenant-aware: Only Muzibu uses corporate accounts
        if (!$this->isMuzibuTenant()) {
            return $this->hasOne(self::class, 'id')->whereRaw('1=0'); // Empty relation
        }

        return $this->hasOne(\Modules\Muzibu\App\Models\MuzibuCorporateAccount::class, 'user_id');
    }

    /**
     * Check if current tenant is Muzibu
     */
    protected function isMuzibuTenant(): bool
    {
        $tenant = tenant();
        return $tenant && $tenant->id == 1001;
    }

    // ==========================================
    // MEMBERSHIP METHODS
    // ==========================================

    /**
     * Get device limit (user-specific or default from settings)
     */
    public function getDeviceLimit(): int
    {
        return $this->device_limit ?? (int) setting('auth_session_device_limit', 1);
    }

    /**
     * Check if user has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription()->exists();
    }

    /**
     * Check if account is locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Check if user is approved
     */
    public function isApproved(): bool
    {
        return $this->is_approved ?? true;
    }

    /**
     * Check if 2FA is enabled
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled ?? false;
    }

    /**
     * Get 2FA phone (user-specific or default phone)
     */
    public function getTwoFactorPhone(): ?string
    {
        return $this->two_factor_phone ?? $this->phone;
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedLogins(): void
    {
        $this->increment('failed_login_attempts');

        $maxAttempts = (int) setting('auth_security_max_attempts', 5);
        $lockoutMinutes = (int) setting('auth_security_lockout', 30);

        if ($this->failed_login_attempts >= $maxAttempts) {
            $this->update([
                'locked_until' => now()->addMinutes($lockoutMinutes)
            ]);
        }
    }

    /**
     * Reset failed login attempts
     */
    public function resetFailedLogins(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Get default billing address
     */
    public function getDefaultBillingAddress()
    {
        return $this->addresses()->billing()->default()->first()
            ?? $this->addresses()->billing()->first();
    }

    // ==========================================
    // CORPORATE METHODS (Muzibu only)
    // ==========================================

    /**
     * Check if user is corporate owner
     * Only works for Muzibu tenant
     */
    public function isCorporateOwner(): bool
    {
        if (!$this->isMuzibuTenant()) {
            return false;
        }
        return $this->ownedCorporateAccount()->exists();
    }

    /**
     * Check if user is corporate member
     * Only works for Muzibu tenant
     */
    public function isCorporateMember(): bool
    {
        if (!$this->isMuzibuTenant()) {
            return false;
        }
        return $this->corporate_account_id !== null;
    }

    /**
     * Get effective subscription (own or corporate owner's)
     * Only works for Muzibu tenant
     */
    public function getEffectiveSubscription()
    {
        if (!$this->isMuzibuTenant()) {
            return $this->subscription ?? null;
        }

        if ($this->isCorporateMember() && $this->corporateAccount) {
            return $this->corporateAccount->owner->subscription ?? null;
        }
        return $this->subscription ?? null;
    }

    // ==========================================
    // PREMIUM MUSIC LISTENING METHODS (Tenant 1001)
    // ==========================================

    /**
     * Bugün kaç şarkı dinledi? (60+ saniye dinlenen)
     * Tenant 1001 (muzibu.com) için
     */
    public function getTodayPlayedCount(): int
    {
        if (!$this->isMuzibuTenant()) {
            return 0;
        }

        return \DB::table('muzibu_song_plays')
            ->where('user_id', $this->id)
            ->where('duration_listened', '>=', 60)
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Şarkı çalabilir mi?
     * Tenant 1001 (muzibu.com) için
     */
    public function canPlaySong(): bool
    {
        if (!$this->isMuzibuTenant()) {
            return true; // Diğer tenant'lar etkilenmez
        }

        // Premium/Trial → Sınırsız
        if ($this->isPremium() || $this->isTrialActive()) {
            return true;
        }

        // Normal üye → Günde 5 şarkı (60+ saniye dinlenen)
        return $this->getTodayPlayedCount() < 5;
    }

    /**
     * Kalan şarkı hakkı
     * Tenant 1001 (muzibu.com) için
     */
    public function getRemainingPlays(): int
    {
        if (!$this->isMuzibuTenant()) {
            return -1; // Diğer tenant'lar sınırsız
        }

        // Premium/Trial → Sınırsız
        if ($this->isPremium() || $this->isTrialActive()) {
            return -1;
        }

        // Normal üye → Kalan hak
        return max(0, 5 - $this->getTodayPlayedCount());
    }

    /**
     * Premium üye mi? (is_premium veya aktif trial)
     * Tenant 1001 (muzibu.com) için
     */
    public function isPremium(): bool
    {
        if (!$this->isMuzibuTenant()) {
            return false;
        }

        return $this->is_premium ?? false;
    }

    /**
     * Aktif trial var mı?
     * Tenant 1001 (muzibu.com) için
     */
    public function isTrialActive(): bool
    {
        if (!$this->isMuzibuTenant()) {
            return false;
        }

        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }
}