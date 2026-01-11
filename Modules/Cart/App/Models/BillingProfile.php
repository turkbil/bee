<?php

declare(strict_types=1);

namespace Modules\Cart\App\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingProfile extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'billing_profiles';
    protected $primaryKey = 'billing_profile_id';

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'identity_number',
        'company_name',
        'tax_number',
        'tax_office',
        'contact_name',
        'contact_phone',
        'contact_email',
        'is_default',
    ];

    /**
     * BaseModel'den gelen gereksiz alanları engelle
     */
    protected $guarded = [
        'is_active',  // billing_profiles tablosunda bu column yok
        'slug',       // billing_profiles slug kullanmaz
    ];

    /**
     * BaseModel'den gelen varsayılan is_active'i kaldır
     */
    protected $attributes = [
        // is_active yok - billing_profiles bu column'u kullanmaz
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Sluggable - DEVRE DIŞI (BillingProfile slug kullanmaz)
     */
    public function sluggable(): array
    {
        return []; // Slug generation disabled
    }

    /**
     * User relation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Is individual?
     */
    public function isIndividual(): bool
    {
        return $this->type === 'individual';
    }

    /**
     * Is corporate?
     */
    public function isCorporate(): bool
    {
        return $this->type === 'corporate';
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->isCorporate() && $this->company_name) {
            return $this->company_name;
        }

        return $this->title;
    }

    /**
     * Get tax info for display
     */
    public function getTaxInfoAttribute(): string
    {
        if ($this->isCorporate()) {
            return "VKN: {$this->tax_number} - {$this->tax_office}";
        }

        return $this->identity_number ? "TC: {$this->identity_number}" : '';
    }

    /**
     * Convert to array for order snapshot
     */
    public function toSnapshot(): array
    {
        return [
            'billing_profile_id' => $this->billing_profile_id,
            'title' => $this->title,
            'type' => $this->type,
            'identity_number' => $this->identity_number,
            'company_name' => $this->company_name,
            'tax_number' => $this->tax_number,
            'tax_office' => $this->tax_office,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
        ];
    }

    /**
     * Set as default
     */
    public function setAsDefault(): void
    {
        // Önce diğer profillerin default'unu kaldır
        self::where('user_id', $this->user_id)
            ->where('billing_profile_id', '!=', $this->billing_profile_id)
            ->update(['is_default' => false]);

        $this->is_default = true;
        $this->save();
    }

    /**
     * Scopes
     */
    public function scopeIndividual($query)
    {
        return $query->where('type', 'individual');
    }

    public function scopeCorporate($query)
    {
        return $query->where('type', 'corporate');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
