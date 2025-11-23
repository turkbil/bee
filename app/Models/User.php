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
        'is_corporate',
        'corporate_code',
        'parent_user_id',
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
            'is_corporate' => 'boolean',
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
     * Get parent user (for corporate sub-accounts)
     */
    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    /**
     * Get sub-users (for corporate accounts)
     */
    public function subUsers()
    {
        return $this->hasMany(User::class, 'parent_user_id');
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
     * Check if this is a corporate account
     */
    public function isCorporate(): bool
    {
        return $this->is_corporate ?? false;
    }

    /**
     * Check if this is a sub-account
     */
    public function isSubAccount(): bool
    {
        return $this->parent_user_id !== null;
    }

    /**
     * Get corporate parent's subscription (for sub-accounts)
     */
    public function getCorporateSubscription()
    {
        if ($this->isSubAccount() && $this->parentUser) {
            return $this->parentUser->subscription;
        }
        return $this->subscription;
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
}