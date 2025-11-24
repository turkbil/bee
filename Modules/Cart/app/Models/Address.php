<?php

declare(strict_types=1);

namespace Modules\Cart\App\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends BaseModel
{
    use HasFactory;

    protected $table = 'cart_addresses';
    protected $primaryKey = 'address_id';

    protected $fillable = [
        'user_id',
        'address_type',
        'title',
        'first_name',
        'last_name',
        'company_name',
        'tax_office',
        'tax_number',
        'phone',
        'email',
        'address_line_1',
        'address_line_2',
        'neighborhood',
        'district',
        'city',
        'postal_code',
        'country_code',
        'is_default_billing',
        'is_default_shipping',
        'delivery_notes',
        'metadata',
    ];

    protected $casts = [
        'is_default_billing' => 'boolean',
        'is_default_shipping' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * User relation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Full name
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->neighborhood,
            $this->district,
            $this->city,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Convert to array for order snapshot
     */
    public function toSnapshot(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'company_name' => $this->company_name,
            'tax_office' => $this->tax_office,
            'tax_number' => $this->tax_number,
            'phone' => $this->phone,
            'email' => $this->email,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'neighborhood' => $this->neighborhood,
            'district' => $this->district,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country_code' => $this->country_code,
            'delivery_notes' => $this->delivery_notes,
        ];
    }

    /**
     * Set as default billing
     */
    public function setAsDefaultBilling(): void
    {
        // Önce diğer adreslerin default'unu kaldır
        self::where('user_id', $this->user_id)
            ->where('address_id', '!=', $this->address_id)
            ->update(['is_default_billing' => false]);

        $this->is_default_billing = true;
        $this->save();
    }

    /**
     * Set as default shipping
     */
    public function setAsDefaultShipping(): void
    {
        // Önce diğer adreslerin default'unu kaldır
        self::where('user_id', $this->user_id)
            ->where('address_id', '!=', $this->address_id)
            ->update(['is_default_shipping' => false]);

        $this->is_default_shipping = true;
        $this->save();
    }

    /**
     * Scopes
     */
    public function scopeBilling($query)
    {
        return $query->whereIn('address_type', ['billing', 'both']);
    }

    public function scopeShipping($query)
    {
        return $query->whereIn('address_type', ['shipping', 'both']);
    }

    public function scopeDefaultBilling($query)
    {
        return $query->where('is_default_billing', true);
    }

    public function scopeDefaultShipping($query)
    {
        return $query->where('is_default_shipping', true);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
