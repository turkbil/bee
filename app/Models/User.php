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
     * 🔥 FIXED: Doğru Subscription modülü kullanılıyor
     */
    public function subscription()
    {
        return $this->hasOne(\Modules\Subscription\App\Models\Subscription::class, 'user_id')
            ->whereIn('status', ['active', 'trial']);
    }

    /**
     * Get all subscriptions
     * 🔥 FIXED: Doğru Subscription modülü kullanılıyor
     */
    public function subscriptions()
    {
        return $this->hasMany(\Modules\Subscription\App\Models\Subscription::class, 'user_id');
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
     * Check if current tenant has subscription/premium features enabled
     * Artık tüm tenant'lar için çalışır (dinamik)
     */
    protected function isMuzibuTenant(): bool
    {
        // Tenant varsa ve subscription özelliği aktifse true
        return (bool) tenant();
    }

    // ==========================================
    // MEMBERSHIP METHODS
    // ==========================================

    /**
     * Get device limit - Delegate to DeviceService (Muzibu)
     */
    public function getDeviceLimit(): int
    {
        // Tenant 1001 (Muzibu) için DeviceService kullan
        if ($this->isMuzibuTenant()) {
            $deviceService = app(\Modules\Muzibu\App\Services\DeviceService::class);
            return $deviceService->getDeviceLimit($this);
        }

        // Diğer tenant'lar için basit fallback
        return $this->device_limit ?: (int) setting('auth_device_limit', 1);
    }

    /**
     * Check if user has active subscription
     * Statüsü 'active' veya 'trial' olan subscription var mı?
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->where(function($q) {
                $q->whereNull('current_period_end')
                  ->orWhere('current_period_end', '>', now());
            })
            ->exists();
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
            return $this->subscriptions()
                ->whereIn('status', ['active', 'trial'])
                ->where(function($q) {
                    $q->whereNull('current_period_end')
                      ->orWhere('current_period_end', '>', now());
                })
                ->first();
        }

        if ($this->isCorporateMember() && $this->corporateAccount) {
            return $this->corporateAccount->owner->subscriptions()
                ->whereIn('status', ['active', 'trial'])
                ->where(function($q) {
                    $q->whereNull('current_period_end')
                      ->orWhere('current_period_end', '>', now());
                })
                ->first();
        }

        return $this->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->where(function($q) {
                $q->whereNull('current_period_end')
                  ->orWhere('current_period_end', '>', now());
            })
            ->first();
    }

    // ==========================================
    // PREMIUM MUSIC LISTENING METHODS (Tenant 1001)
    // ==========================================

    /**
     * Premium üye mi? (aktif subscription veya trial)
     *
     * ⚠️ SADECE TENANT 1001 (muzibu.com.tr) İÇİN!
     * Diğer tenant'lar için direkt false döner, cache kullanılmaz
     *
     * ⚡ PERFORMANCE: 1 saatlik cache ile optimize edildi (sadece Muzibu için)
     * Her Muzibu stream request'inde çağrılıyor - cache kritik!
     */
    public function isPremium(): bool
    {
        // Tenant yoksa false
        if (!$this->isMuzibuTenant()) {
            return false;
        }

        // 🚀 5 dakikalık cache (güvenlik vs performans balance)
        // Event-based invalidation: Login/Register/Subscription change → cache flush
        $cacheKey = 'user_' . $this->id . '_is_premium_tenant_' . tenant()->id;

        return \Cache::remember($cacheKey, 300, function () {
            // Yeni subscription sistemi: subscriptions tablosundan kontrol et
            // ✅ FIXED: whereNull kaldırıldı (NULL = sonsuz premium önlendi)
            $activeSubscription = $this->subscriptions()
                ->where('status', 'active')
                ->where('current_period_end', '>', now()) // 🔥 Sadece gelecek tarihli subscription'lar
                ->first();

            if ($activeSubscription) {
                return true;
            }

            // Fallback: Eski sistem (is_premium kolonu)
            return $this->is_premium ?? false;
        });
    }

    /**
     * Aktif trial var mı?
     * Tenant 1001 (muzibu.com) için
     * 🔥 FIX: has_trial=true VE trial_ends_at gelecekte ise trial aktif
     */
    public function isTrialActive(): bool
    {
        if (!$this->isMuzibuTenant()) {
            return false;
        }

        // Yeni subscription sistemi: has_trial=true VE trial_ends_at gelecekte
        // NOT: status 'active' veya 'trial' olabilir, önemli olan has_trial ve trial_ends_at
        $trialSubscription = $this->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->where('has_trial', true)
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->first();

        if ($trialSubscription) {
            return true;
        }

        // Fallback: Eski sistem (trial_ends_at kolonu)
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Premium veya Trial üye mi?
     * 🔥 Helper: Tek çağrı ile hem premium hem trial kontrolü
     * Tenant 1001 (muzibu.com) için
     */
    public function isPremiumOrTrial(): bool
    {
        return $this->isPremium() || $this->isTrialActive();
    }

    /**
     * Get active subscription (for device limit hierarchy)
     * Returns hasOne relation for eager loading support
     */
    public function activeSubscription()
    {
        return $this->hasOne(\Modules\Subscription\App\Models\Subscription::class, 'user_id')
            ->where('status', 'active')
            ->where('current_period_end', '>', now());
    }
}