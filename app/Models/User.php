<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Modules\UserManagement\App\Traits\HasModulePermissions;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, InteractsWithMedia, HasModulePermissions, SoftDeletes;

    /**
     * The guard name for Spatie Permission
     *
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * Get the name of the guard to be used during authentication.
     *
     * @return string
     */
    public function guardName(): string
    {
        return 'web';
    }

    /**
     * A model may have multiple roles.
     * Override to fix guard name issue.
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            'role_id'
        )->where(config('permission.table_names.roles') . '.guard_name', 'web')
        ->withoutGlobalScopes(); // 🔴 KRİTİK: Global scope'ları bypass et
    }

    /**
     * 🔴 KRİTİK: Spatie Permission cache'ini tamamen temizle
     * Component'te rol değiştirme sonrası çağrılır
     */
    public function flushRoleCache(): void
    {
        // 1. Spatie global cache
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Model relationships
        $this->unsetRelation('roles');
        $this->unsetRelation('permissions');

        // 3. Laravel cache
        \Cache::forget("user_{$this->id}_roles");
        \Cache::forget("user_{$this->id}_permissions");
    }

    /**
     * A model may have multiple direct permissions.
     * Override to fix guard name issue.
     */
    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            'permission_id'
        )->where(config('permission.table_names.permissions') . '.guard_name', 'web');
    }

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
        'subscription_expires_at', // Toplam subscription bitiş tarihi (zincir sistemi)
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
            'subscription_expires_at' => 'datetime', // Zincir subscription bitiş tarihi
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
    // MUZIBU RELATIONSHIPS (Tenant 1001)
    // ==========================================

    /**
     * Get user's playlists (Muzibu)
     */
    public function playlists()
    {
        if (!$this->isMuzibuTenant()) {
            return $this->hasMany(self::class, 'id')->whereRaw('1=0'); // Empty relation
        }

        return $this->hasMany(\Modules\Muzibu\App\Models\Playlist::class, 'user_id');
    }

    /**
     * Get user's song plays (Muzibu)
     */
    public function songPlays()
    {
        if (!$this->isMuzibuTenant()) {
            return $this->hasMany(self::class, 'id')->whereRaw('1=0'); // Empty relation
        }

        return $this->hasMany(\Modules\Muzibu\App\Models\SongPlay::class, 'user_id');
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
     * Premium üye mi?
     *
     * 🔴 TEK KAYNAK: users.subscription_expires_at
     * - Gelecekte ise = Premium
     * - NULL veya geçmişte ise = Ücretsiz
     * - Trial ayrımı YOK (trial da premium sayılır)
     *
     * ⚠️ SADECE TENANT CONTEXT'İNDE ÇALIŞIR
     */
    public function isPremium(): bool
    {
        // Tenant yoksa false
        if (!$this->isMuzibuTenant()) {
            return false;
        }

        // 1. Model değeri (hızlı)
        if ($this->subscription_expires_at && $this->subscription_expires_at->isFuture()) {
            return true;
        }

        // 2. Fresh DB kontrolü (model stale olabilir)
        $freshExpiry = \DB::table('users')
            ->where('id', $this->id)
            ->value('subscription_expires_at');

        return $freshExpiry && \Carbon\Carbon::parse($freshExpiry)->isFuture();
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

    // ==========================================
    // SUBSCRIPTION CHAIN SYSTEM METHODS
    // ==========================================

    /**
     * Kullanıcının subscription_expires_at değerini yeniden hesapla
     * Tüm active ve pending subscription'ların en son bitiş tarihini bul
     *
     * 🎯 SADECE TENANT DB GÜNCELLENİR:
     * - subscription_expires_at tenant'a özel veridir (Muzibu)
     * - Central DB'de bu sütun kullanılmaz
     */
    public function recalculateSubscriptionExpiry(): void
    {
        // 🔧 FIX: Tenant context check removed - User model already exists in tenant DB
        // Connection check ensures we're working with correct database
        $connection = $this->getConnectionName();

        // Only process if we have a connection (prevents running on non-existent models)
        if (!$connection) {
            \Log::warning('recalculateSubscriptionExpiry: No database connection', ['user_id' => $this->id]);
            return;
        }

        // Sadece ÖDENMİŞ veya MANUEL abonelikleri dahil et
        // - active/pending status
        // - order varsa: payment_status = paid/completed
        // - order yoksa: manuel oluşturulmuş (admin onaylı)
        $validSubscriptions = $this->subscriptions()
            ->whereIn('status', ['active', 'pending'])
            ->get()
            ->filter(function ($sub) {
                $orderId = $sub->metadata['order_id'] ?? null;

                // Order yoksa = manuel oluşturulmuş, dahil et
                if (!$orderId) {
                    return true;
                }

                // Order varsa ödeme durumunu kontrol et
                if (class_exists(\Modules\Cart\App\Models\Order::class)) {
                    $order = \Modules\Cart\App\Models\Order::find($orderId);
                    if ($order && in_array($order->payment_status, ['paid', 'completed'])) {
                        return true;
                    }
                }

                return false;
            });

        $lastSubscription = $validSubscriptions->sortByDesc('current_period_end')->first();
        $expiresAt = $lastSubscription?->current_period_end;

        \Log::channel('daily')->info('🔄 recalculateSubscriptionExpiry', [
            'user_id' => $this->id,
            'connection' => $connection,
            'valid_subscriptions_count' => $validSubscriptions->count(),
            'calculated_expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : 'NULL',
        ]);

        // Update users table using the same connection as the model
        try {
            \DB::connection($connection)
                ->table('users')
                ->where('id', $this->id)
                ->update(['subscription_expires_at' => $expiresAt]);
        } catch (\Exception $e) {
            \Log::error('recalculateSubscriptionExpiry: DB update failed', [
                'user_id' => $this->id,
                'connection' => $connection,
                'error' => $e->getMessage(),
            ]);
        }

        // Model'i refresh et
        $this->subscription_expires_at = $expiresAt;

        // Premium cache'i temizle
        \Cache::forget('user_' . $this->id . '_is_premium_tenant_' . (tenant()?->id ?? 0));
    }

    /**
     * Kullanıcının zincirdeki son subscription'ının bitiş tarihini al
     * Yeni subscription eklerken başlangıç tarihi olarak kullanılır
     */
    public function getLastSubscriptionEndDate(): ?\Carbon\Carbon
    {
        $lastSubscription = $this->subscriptions()
            ->whereIn('status', ['active', 'pending'])
            ->orderBy('current_period_end', 'desc')
            ->first();

        return $lastSubscription?->current_period_end;
    }

    /**
     * Kullanıcının bekleyen (pending) subscription'ları var mı?
     */
    public function hasPendingSubscriptions(): bool
    {
        return $this->subscriptions()
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Kullanıcının aktif subscription'ını getir
     */
    public function getActiveSubscription(): ?\Modules\Subscription\App\Models\Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('current_period_end', '>', now())
            ->first();
    }

    /**
     * Kullanıcının bekleyen subscription'larını getir (sıralı)
     */
    public function getPendingSubscriptions()
    {
        return $this->subscriptions()
            ->where('status', 'pending')
            ->orderBy('current_period_start', 'asc')
            ->get();
    }

    /**
     * Email doğrulama notification'ını gönder (Custom template ile)
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }
}