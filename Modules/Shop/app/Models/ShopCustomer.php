<?php

namespace Modules\Shop\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class ShopCustomer extends Model
{
    use SoftDeletes;

    protected $table = 'shop_customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'user_id',
        'customer_type',
        'billing_type', // individual (TC) / corporate (VKN)
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_name',
        'tax_office',
        'tax_number', // TC Kimlik veya VKN
        'customer_group_id',
        'password',
        'email_verified',
        'email_verified_at',
        'accepts_marketing',
        'accepts_sms',
        'total_orders',
        'total_spent',
        'last_order_date',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'accepts_marketing' => 'boolean',
        'accepts_sms' => 'boolean',
        'total_orders' => 'integer',
        'total_spent' => 'decimal:2',
        'email_verified_at' => 'datetime',
        'last_order_date' => 'datetime',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * İlişki: User (kayıtlı kullanıcı)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * İlişki: Adresler
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(ShopCustomerAddress::class, 'customer_id', 'customer_id');
    }

    /**
     * İlişki: Siparişler
     */
    public function orders(): HasMany
    {
        return $this->hasMany(ShopOrder::class, 'customer_id', 'customer_id');
    }

    /**
     * Varsayılan fatura adresi
     */
    public function defaultBillingAddress()
    {
        return $this->hasOne(ShopCustomerAddress::class, 'customer_id', 'customer_id')
            ->where('is_default_billing', true);
    }

    /**
     * Varsayılan teslimat adresi
     */
    public function defaultShippingAddress()
    {
        return $this->hasOne(ShopCustomerAddress::class, 'customer_id', 'customer_id')
            ->where('is_default_shipping', true);
    }

    /**
     * Tam isim
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Kurumsal müşteri mi?
     */
    public function isCorporate(): bool
    {
        return $this->customer_type === 'corporate';
    }

    /**
     * Kurumsal fatura mı?
     */
    public function hasCorporateBilling(): bool
    {
        return $this->billing_type === 'corporate';
    }

    /**
     * Bireysel fatura mı?
     */
    public function hasIndividualBilling(): bool
    {
        return $this->billing_type === 'individual';
    }

    /**
     * Fatura bilgilerini al
     */
    public function getBillingInfoAttribute(): array
    {
        if ($this->hasCorporateBilling()) {
            return [
                'type' => 'corporate',
                'company_name' => $this->company_name,
                'tax_office' => $this->tax_office,
                'tax_number' => $this->tax_number, // VKN
            ];
        }

        return [
            'type' => 'individual',
            'full_name' => $this->full_name,
            'tax_number' => $this->tax_number, // TC Kimlik
        ];
    }

    /**
     * Email veya telefon ile müşteri bul
     */
    public static function findByEmailOrPhone(string $email = null, string $phone = null)
    {
        $query = self::query();

        if ($email) {
            $query->where('email', $email);
        }

        if ($phone && !$email) {
            $query->orWhere('phone', $phone);
        }

        return $query->first();
    }

    /**
     * Yeni müşteri oluştur (misafir checkout için)
     */
    public static function createFromCheckout(array $data): self
    {
        return self::create([
            'user_id' => auth()->id(), // Null olabilir (misafir)
            'customer_type' => $data['customer_type'] ?? 'individual',
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'tax_office' => $data['tax_office'] ?? null,
            'tax_number' => $data['tax_number'] ?? null,
            'accepts_marketing' => $data['accepts_marketing'] ?? false,
            'accepts_sms' => $data['accepts_sms'] ?? false,
        ]);
    }
}
