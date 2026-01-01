<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, LogsActivity;

    protected $guarded = [];

    public $timestamps = true;
    protected $table = 'tenants';
    public $incrementing = true;

    /**
     * Serialize edilmemesi gereken büyük alanlar
     * Session'a yazılırken 8MB+ theme_settings problemi
     */
    protected $hidden = [
        'theme_settings',
    ];

    /**
     * Session serialize için büyük alanları hariç tut
     * PHP serialize (session) için __sleep() kullanılır
     */
    public function __sleep()
    {
        // Sadece temel alanları serialize et, theme_settings hariç
        // Hem attributes hem original'dan kaldır
        unset($this->attributes['theme_settings']);
        unset($this->original['theme_settings']);

        return array_keys(get_object_vars($this));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            // tenancy_db_name otomatik oluştur (yoksa)
            if (empty($tenant->tenancy_db_name)) {
                $slug = \Illuminate\Support\Str::slug($tenant->title ?? 'tenant', '_');
                $uniqueId = substr(md5(uniqid()), 0, 6);
                $tenant->tenancy_db_name = "tenant_{$slug}_{$uniqueId}";
            }
        });
    }
    
    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'central' => 'boolean',
        'data' => 'array',
        'theme_id' => 'integer',
        'theme_settings' => 'array',
        'ai_monthly_token_limit' => 'integer',
        'ai_last_used_at' => 'datetime',
        'tenant_ai_provider_id' => 'integer',
        'tenant_ai_provider_model_id' => 'integer',
        'ai_credits_balance' => 'float',
    ];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Tenant')  // "default" yerine "Tenant" olarak kaydedilsin
            ->logOnly(['title', 'is_active'])  // data çok büyük, loglamayalım
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Tenant {$this->title} {$eventName}");
    }
    
    public function domains()
    {
        return $this->hasMany(\Stancl\Tenancy\Database\Models\Domain::class, 'tenant_id', 'id');
    }

    /**
     * Theme folder_name accessor
     * tenant()->theme ile tema klasör adını döndürür
     */
    public function getThemeAttribute(): ?string
    {
        if (!$this->theme_id) {
            return null;
        }

        // Cache ile Theme model'den folder_name al
        static $themeCache = [];

        if (!isset($themeCache[$this->theme_id])) {
            $theme = \Modules\ThemeManagement\App\Models\Theme::find($this->theme_id);
            $themeCache[$this->theme_id] = $theme?->folder_name;
        }

        return $themeCache[$this->theme_id];
    }

    public function getDatabaseName()
    {
        return $this->tenancy_db_name;
    }
    
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'title',
            'tenancy_db_name',
            'is_active',
            'is_premium',
            'central',
            'theme_id',
            'theme_settings', // ← EKLENDI: data kolonuna yazılmasını önler
            'admin_default_locale',
            'tenant_default_locale',
            'fullname',
            'email',
            'phone',
            'ai_credits_balance',
            'ai_last_used_at',
            'tenant_ai_provider_id',
            'tenant_ai_provider_model_id',
            'data',
            'created_at',
            'updated_at',
        ];
    }

    public function modules()
    {
        return $this->belongsToMany(
            \Modules\ModuleManagement\App\Models\Module::class,
            'module_tenants',
            'tenant_id',
            'module_id'
        )->withPivot('is_active')
        ->withTimestamps();
    }

    /**
     * Tenant languages relationship - tenant context'te çalışır
     */
    public function tenantLanguages()
    {
        // Central tenant ise ana veritabanından, değilse tenant'ın kendi veritabanından
        if ($this->central) {
            // Central tenant - ana veritabanından al
            return \Modules\LanguageManagement\app\Models\TenantLanguage::on('mysql')->query();
        } else {
            // Normal tenant - tenant veritabanından al
            return \Modules\LanguageManagement\app\Models\TenantLanguage::query();
        }
    }

    /**
     * Module settings relationship
     */
    public function moduleSettings()
    {
        // Basit fallback - data field'ından module ayarlarını döndür
        $data = $this->data ?? [];
        return (object) ($data['module_settings'] ?? []);
    }

    /**
     * AI Token purchases relationship
     */
    public function aiTokenPurchases()
    {
        return $this->hasMany(\Modules\AI\App\Models\AITokenPurchase::class);
    }

    /**
     * AI Token usage relationship
     */
    public function aiTokenUsage()
    {
        return $this->hasMany(\Modules\AI\App\Models\AITokenUsage::class);
    }

    /**
     * AI Provider relationship
     */
    public function aiProvider()
    {
        return $this->belongsTo(\Modules\AI\App\Models\AIProvider::class, 'tenant_ai_provider_id');
    }

    /**
     * AI Provider Model relationship
     */
    public function aiProviderModel()
    {
        return $this->belongsTo(\Modules\AI\App\Models\AIProviderModel::class, 'tenant_ai_provider_model_id');
    }

    /**
     * Get effective AI provider (Hierarchy: Tenant -> Global Default)
     */
    public function getEffectiveAiProvider()
    {
        // 1. Tenant'ın kendi provider'ı varsa
        if ($this->tenant_ai_provider_id && $this->aiProvider && $this->aiProvider->is_active) {
            return $this->aiProvider;
        }
        
        // 2. Global varsayılan provider
        return \Modules\AI\App\Models\AIProvider::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get effective AI model (Hierarchy: Tenant Model -> Tenant Provider Default -> Global)
     */
    public function getEffectiveAiModel()
    {
        // 1. Tenant'ın belirttiği specific model
        if ($this->tenant_ai_provider_model_id && $this->aiProviderModel && $this->aiProviderModel->is_active) {
            return $this->aiProviderModel;
        }
        
        // 2. Tenant'ın provider'ının varsayılan modeli
        $provider = $this->getEffectiveAiProvider();
        if ($provider) {
            $model = \Modules\AI\App\Models\AIProviderModel::where('provider_id', $provider->id)
                ->where('is_default', true)
                ->where('is_active', true)
                ->orderBy('sort_order', 'desc')
                ->first();
                
            if ($model) {
                return $model;
            }
        }
        
        // 3. Global varsayılan model
        return \Modules\AI\App\Models\AIProviderModel::getDefaultModel();
    }

    /**
     * Backward compatibility - eski string provider method
     */
    public function getDefaultAiProvider()
    {
        $provider = $this->getEffectiveAiProvider();
        return $provider ? $provider->name : null;
    }

    /**
     * Backward compatibility - eski string model method
     */
    public function getDefaultAiModel()
    {
        $model = $this->getEffectiveAiModel();
        return $model ? $model->model_name : null;
    }

    /**
     * Set AI provider by name (for backward compatibility)
     */
    public function setDefaultAiProvider($providerName)
    {
        $provider = \Modules\AI\App\Models\AIProvider::where('name', $providerName)->first();
        if ($provider) {
            $this->tenant_ai_provider_id = $provider->id;
        }
        return $this;
    }

    /**
     * Set AI model by name (for backward compatibility)
     */
    public function setDefaultAiModel($modelName)
    {
        $provider = $this->getEffectiveAiProvider();
        if ($provider) {
            $model = \Modules\AI\App\Models\AIProviderModel::where('provider_id', $provider->id)
                ->where('model_name', $modelName)
                ->first();
            if ($model) {
                $this->tenant_ai_provider_model_id = $model->id;
            }
        }
        return $this;
    }

    /**
     * Check if tenant has enough AI credits
     * Premium tenants have unlimited credits
     */
    public function hasEnoughCredits(float $creditsNeeded): bool
    {
        // Premium tenant = sınırsız kredi
        if ($this->isPremium()) {
            return true;
        }

        return $this->ai_credits_balance >= $creditsNeeded;
    }

    /**
     * Use AI credits
     * Premium tenants don't consume credits
     */
    public function useCredits(float $creditsUsed): bool
    {
        // Premium tenant = kredi tüketimi yok
        if ($this->isPremium()) {
            $this->update(['ai_last_used_at' => now()]);
            return true;
        }

        if (!$this->hasEnoughCredits($creditsUsed)) {
            return false;
        }

        $this->decrement('ai_credits_balance', $creditsUsed);
        $this->update(['ai_last_used_at' => now()]);

        return true;
    }

    // ========================================
    // PREMIUM TENANT HELPERS
    // ========================================

    /**
     * Check if tenant is premium
     */
    public function isPremium(): bool
    {
        return $this->is_premium === true;
    }

    /**
     * Check if tenant has unlimited AI credits
     */
    public function hasUnlimitedAI(): bool
    {
        return $this->isPremium();
    }

    /**
     * Check if tenant has auto SEO fill enabled
     */
    public function hasAutoSeoFill(): bool
    {
        return $this->isPremium();
    }

    /**
     * Scope query to only premium tenants
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope query to only non-premium tenants
     */
    public function scopeNonPremium($query)
    {
        return $query->where('is_premium', false);
    }
}