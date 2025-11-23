<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    use SoftDeletes;

    protected $table = 'customer_addresses';

    protected $fillable = [
        'user_id',
        'title',
        'type', // billing, shipping
        'first_name',
        'last_name',
        'company_name',
        'tax_office',
        'tax_number',
        'phone',
        'email',
        'address_line_1',
        'address_line_2',
        'city',
        'district',
        'postal_code',
        'country',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Default addresses
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope: Billing addresses
     */
    public function scopeBilling($query)
    {
        return $query->where('type', 'billing');
    }

    /**
     * Scope: Shipping addresses
     */
    public function scopeShipping($query)
    {
        return $query->where('type', 'shipping');
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->district,
            $this->city,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}
