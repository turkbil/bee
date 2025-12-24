<?php

declare(strict_types=1);

namespace Modules\Cart\App\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Order extends BaseModel
{
    use HasFactory;

    protected $table = 'cart_orders';
    protected $primaryKey = 'order_id';

    // BaseModel'deki is_active default'unu devre dışı bırak (cart_orders tablosunda yok)
    protected $attributes = [];

    protected $fillable = [
        'order_number',
        'user_id',
        'order_type',
        'order_source',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_cost',
        'total_amount',
        'currency',
        'payment_status',
        'paid_amount',
        'requires_shipping',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'coupon_code',
        'coupon_discount',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_company',
        'customer_tax_office',
        'customer_tax_number',
        'billing_address',
        'shipping_address',
        'agreed_terms',
        'agreed_privacy',
        'agreed_marketing',
        'customer_notes',
        'admin_notes',
        'metadata',
        'ip_address',
        'user_agent',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'requires_shipping' => 'boolean',
        'agreed_terms' => 'boolean',
        'agreed_privacy' => 'boolean',
        'agreed_marketing' => 'boolean',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'metadata' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * User relation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order items
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Payments (polymorphic)
     */
    public function payments(): MorphMany
    {
        if (class_exists(\Modules\Payment\App\Models\Payment::class)) {
            return $this->morphMany(\Modules\Payment\App\Models\Payment::class, 'payable');
        }
        return $this->morphMany(self::class, 'payable'); // Fallback
    }

    /**
     * Generate order number (PayTR: sadece alfanumerik, özel karakter yok)
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));

        return "{$prefix}{$date}{$random}"; // Tire yok - PayTR uyumlu
    }

    /**
     * Recalculate totals
     */
    public function recalculateTotals(): void
    {
        $items = $this->items()->get();

        $this->subtotal = $items->sum('subtotal');
        $this->tax_amount = $items->sum('tax_amount');
        $this->total_amount = $this->subtotal + $this->tax_amount + $this->shipping_cost - $this->discount_amount - $this->coupon_discount;

        $this->save();
    }

    /**
     * Check if order requires shipping
     */
    public function checkRequiresShipping(): bool
    {
        // Tüm itemlar dijitalse kargo gerekmez
        $hasPhysical = $this->items()->where('is_digital', false)->exists();
        return $hasPhysical;
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(float $amount = null): void
    {
        $this->paid_amount = $amount ?? $this->total_amount;
        $this->payment_status = 'paid';
        $this->save();

        // Subscription item varsa aktifleştir
        $this->activateSubscriptionItems();
    }

    /**
     * Siparişteki subscription item'larını aktifleştir
     * Manuel ödeme onayı veya PayTR callback sonrası çağrılır
     *
     * 🔗 ZİNCİR SİSTEMİ:
     * - Her satın alma ayrı subscription kaydı oluşturur
     * - Yeni subscription, öncekinin bittiği yerden başlar
     * - Mevcut aktif varsa yeni subscription "pending" olur
     * - users.subscription_expires_at otomatik güncellenir
     */
    protected function activateSubscriptionItems(): void
    {
        \Log::channel('daily')->info('🔵 activateSubscriptionItems START (Chain System)', [
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'items_count' => $this->items->count(),
        ]);

        if (!class_exists(\Modules\Subscription\App\Models\Subscription::class)) {
            \Log::channel('daily')->warning('⚠️ Subscription class not found');
            return;
        }

        $user = \App\Models\User::find($this->user_id);
        if (!$user) {
            \Log::channel('daily')->error('❌ User bulunamadı', ['user_id' => $this->user_id]);
            return;
        }

        foreach ($this->items as $item) {
            if ($item->orderable_type !== 'Modules\\Subscription\\App\\Models\\SubscriptionPlan') {
                continue;
            }

            try {
                $plan = $item->orderable;
                if (!$plan) {
                    \Log::channel('daily')->warning('⚠️ SubscriptionPlan bulunamadı', ['item_id' => $item->order_item_id]);
                    continue;
                }

                // Cycle bilgilerini al
                $cycleKey = $item->metadata['cycle_key'] ?? null;
                $cycleMetadata = $item->metadata['cycle_metadata'] ?? null;

                if (!$cycleKey) {
                    $cycles = $plan->getSortedCycles();
                    $cycleKey = array_key_first($cycles) ?? 'monthly';
                    $cycleMetadata = $cycles[$cycleKey] ?? null;
                }

                $durationDays = $cycleMetadata['duration_days'] ?? 30;

                // 1️⃣ Pending payment subscription var mı? (Checkout'ta oluşturulan)
                $pendingPaymentSub = \Modules\Subscription\App\Models\Subscription::where('user_id', $this->user_id)
                    ->where('subscription_plan_id', $plan->subscription_plan_id)
                    ->where('status', 'pending_payment')
                    ->orderBy('created_at', 'desc')
                    ->first();

                // 2️⃣ Zincirdeki son subscription'ın bitiş tarihini bul
                $lastEndDate = $user->getLastSubscriptionEndDate();
                $hasActiveOrPending = $lastEndDate && $lastEndDate->isFuture();

                // 3️⃣ Yeni subscription tarihleri
                $startDate = $hasActiveOrPending ? $lastEndDate : now();
                $endDate = $startDate->copy()->addDays($durationDays);

                // 4️⃣ Status belirleme: mevcut aktif/pending varsa "pending", yoksa "active"
                $newStatus = $hasActiveOrPending ? 'pending' : 'active';

                if ($pendingPaymentSub) {
                    // Pending payment subscription'ı güncelle
                    $pendingPaymentSub->update([
                        'status' => $newStatus,
                        'current_period_start' => $startDate,
                        'current_period_end' => $endDate,
                        'next_billing_date' => $endDate,
                        'started_at' => $newStatus === 'active' ? now() : $startDate,
                        'total_paid' => $item->total_price,
                        'billing_cycles_completed' => 1,
                        'metadata' => array_merge($pendingPaymentSub->metadata ?? [], [
                            'order_id' => $this->order_id,
                            'order_number' => $this->order_number,
                            'activated_at' => now()->toDateTimeString(),
                            'chain_position' => $hasActiveOrPending ? 'queued' : 'first',
                        ]),
                    ]);

                    \Log::channel('daily')->info('✅ Subscription zincire eklendi (pending_payment → ' . $newStatus . ')', [
                        'subscription_id' => $pendingPaymentSub->subscription_id,
                        'user_id' => $this->user_id,
                        'status' => $newStatus,
                        'start_date' => $startDate->toDateTimeString(),
                        'end_date' => $endDate->toDateTimeString(),
                        'duration_days' => $durationDays,
                    ]);
                } else {
                    // Yeni subscription oluştur (zincir mantığıyla)
                    $subscription = \Modules\Subscription\App\Models\Subscription::create([
                        'user_id' => $this->user_id,
                        'subscription_plan_id' => $plan->subscription_plan_id,
                        'subscription_number' => \Modules\Subscription\App\Models\Subscription::generateSubscriptionNumber(),
                        'status' => $newStatus,
                        'cycle_key' => $cycleKey,
                        'cycle_metadata' => $cycleMetadata,
                        'price_per_cycle' => $item->unit_price,
                        'currency' => $this->currency ?? 'TRY',
                        'has_trial' => false,
                        'trial_days' => 0,
                        'started_at' => $newStatus === 'active' ? now() : $startDate,
                        'current_period_start' => $startDate,
                        'current_period_end' => $endDate,
                        'next_billing_date' => $endDate,
                        'auto_renew' => true,
                        'billing_cycles_completed' => 1,
                        'total_paid' => $item->total_price,
                        'metadata' => [
                            'order_id' => $this->order_id,
                            'order_number' => $this->order_number,
                            'activated_at' => now()->toDateTimeString(),
                            'chain_position' => $hasActiveOrPending ? 'queued' : 'first',
                        ],
                    ]);

                    \Log::channel('daily')->info('✅ Yeni subscription zincire eklendi', [
                        'subscription_id' => $subscription->subscription_id,
                        'user_id' => $this->user_id,
                        'status' => $newStatus,
                        'start_date' => $startDate->toDateTimeString(),
                        'end_date' => $endDate->toDateTimeString(),
                        'duration_days' => $durationDays,
                    ]);
                }

                // 5️⃣ users.subscription_expires_at güncelle
                $user->recalculateSubscriptionExpiry();

                \Log::channel('daily')->info('📅 User subscription_expires_at güncellendi', [
                    'user_id' => $this->user_id,
                    'expires_at' => $user->fresh()->subscription_expires_at?->toDateTimeString(),
                ]);

            } catch (\Exception $e) {
                \Log::channel('daily')->error('❌ Subscription aktivasyon hatası', [
                    'order_id' => $this->order_id,
                    'item_id' => $item->order_item_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    /**
     * Mark as shipped
     */
    public function markAsShipped(string $trackingNumber = null): void
    {
        $this->status = 'shipped';
        $this->tracking_number = $trackingNumber;
        $this->shipped_at = now();
        $this->save();
    }

    /**
     * Mark as delivered
     */
    public function markAsDelivered(): void
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Cancel order
     */
    public function cancel(string $reason = null): void
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        if ($reason) {
            $this->admin_notes = ($this->admin_notes ? $this->admin_notes . "\n" : '') . "İptal nedeni: {$reason}";
        }
        $this->save();
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessors
     */
    public function getRemainingAmountAttribute(): float
    {
        return (float) $this->total_amount - (float) $this->paid_amount;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getIsCompletedAttribute(): bool
    {
        return in_array($this->status, ['delivered', 'completed']);
    }

    /**
     * Ödeme tamamlandığında çağrılır (PayTR callback)
     * - Sipariş durumunu güncelle
     * - Sepeti temizle
     * - Bildirim e-postaları gönder
     */
    public function onPaymentCompleted(\Modules\Payment\App\Models\Payment $payment): void
    {
        // 🔥 DEBUG: Başlangıç
        \Log::channel('daily')->info('🔵 Order::onPaymentCompleted START', [
            'order_id' => $this->order_id,
            'payment_id' => $payment->payment_id,
        ]);

        // 1. Sipariş durumunu güncelle
        $this->status = 'processing'; // pending -> processing
        $this->payment_status = 'paid';
        $this->paid_amount = $payment->amount;
        $this->confirmed_at = now();
        $this->save();

        \Log::channel('daily')->info('🔵 Order status updated', ['status' => $this->status]);

        // 2. Kullanıcının sepetini temizle (Cart tablosunda customer_id kullanılıyor!)
        if ($this->user_id) {
            try {
                $cart = \Modules\Cart\App\Models\Cart::where('customer_id', $this->user_id)
                    ->where('status', 'active')
                    ->first();

                if ($cart) {
                    $cartService = app(\Modules\Cart\App\Services\CartService::class);
                    $cartService->clearCart($cart);
                    \Log::channel('daily')->info('🛒 Sepet temizlendi', ['customer_id' => $this->user_id, 'cart_id' => $cart->cart_id, 'order_id' => $this->order_id]);
                }
            } catch (\Exception $e) {
                \Log::channel('daily')->error('❌ Sepet temizleme hatası: ' . $e->getMessage());
            }
        }

        // 3. Admin'e bildirim e-postası gönder
        $this->sendAdminNotification($payment);

        // 4. Müşteriye onay e-postası gönder
        $this->sendCustomerConfirmation($payment);

        // 5. Subscription item varsa aktifleştir
        $this->activateSubscriptionItems();

        \Log::channel('daily')->info('✅ Sipariş tamamlandı', [
            'order_id' => $this->order_id,
            'order_number' => $this->order_number,
            'amount' => $payment->amount
        ]);
    }

    /**
     * Ödeme başarısız olduğunda çağrılır
     */
    public function onPaymentFailed(\Modules\Payment\App\Models\Payment $payment): void
    {
        $this->status = 'payment_failed';
        $this->payment_status = 'failed';
        $this->save();

        \Log::channel('daily')->warning('⚠️ Ödeme başarısız', [
            'order_id' => $this->order_id,
            'order_number' => $this->order_number
        ]);
    }

    /**
     * Admin'e yeni sipariş bildirimi gönder
     */
    protected function sendAdminNotification(\Modules\Payment\App\Models\Payment $payment): void
    {
        $adminEmail = setting('order_notification_email', setting('contact_email'));

        if (!$adminEmail) {
            return;
        }

        try {
            $items = $this->items->map(function ($item) {
                return "- {$item->item_title} x{$item->quantity} = " . number_format((float) $item->total_price, 2, ',', '.') . " ₺";
            })->implode("\n");

            \Mail::raw(
                "🛒 YENİ SİPARİŞ ALINDI!\n\n" .
                "Sipariş No: {$this->order_number}\n" .
                "Tarih: " . now()->format('d.m.Y H:i') . "\n" .
                "Tutar: " . number_format((float) $payment->amount, 2, ',', '.') . " ₺\n\n" .
                "MÜŞTERİ BİLGİLERİ:\n" .
                "Ad Soyad: {$this->customer_name}\n" .
                "E-posta: {$this->customer_email}\n" .
                "Telefon: {$this->customer_phone}\n\n" .
                "ÜRÜNLER:\n{$items}\n\n" .
                "Sipariş detayları için admin paneli ziyaret edin.",
                function ($message) use ($adminEmail) {
                    $message->to($adminEmail)
                        ->subject("🛒 Yeni Sipariş #{$this->order_number} - " . number_format((float) $this->total_amount, 0, ',', '.') . " ₺");
                }
            );

            \Log::channel('daily')->info('📧 Admin bildirim e-postası gönderildi', ['email' => $adminEmail]);
        } catch (\Exception $e) {
            \Log::channel('daily')->error('❌ Admin bildirim e-postası gönderilemedi: ' . $e->getMessage());
        }
    }

    /**
     * Müşteriye sipariş onay e-postası gönder
     */
    protected function sendCustomerConfirmation(\Modules\Payment\App\Models\Payment $payment): void
    {
        if (!$this->customer_email) {
            return;
        }

        try {
            $items = $this->items->map(function ($item) {
                return "- {$item->item_title} x{$item->quantity} = " . number_format((float) $item->total_price, 2, ',', '.') . " ₺";
            })->implode("\n");

            $siteName = setting('site_name', config('app.name'));

            \Mail::raw(
                "Merhaba {$this->customer_name},\n\n" .
                "Siparişiniz başarıyla alındı! Teşekkür ederiz.\n\n" .
                "SİPARİŞ BİLGİLERİ:\n" .
                "Sipariş No: {$this->order_number}\n" .
                "Tarih: " . now()->format('d.m.Y H:i') . "\n" .
                "Toplam: " . number_format((float) $payment->amount, 2, ',', '.') . " ₺\n\n" .
                "ÜRÜNLER:\n{$items}\n\n" .
                "Siparişiniz en kısa sürede hazırlanacaktır.\n\n" .
                "Teşekkürler,\n{$siteName}",
                function ($message) use ($siteName) {
                    $message->to($this->customer_email)
                        ->subject("✅ Sipariş Onayı #{$this->order_number} - {$siteName}");
                }
            );

            \Log::channel('daily')->info('📧 Müşteri onay e-postası gönderildi', ['email' => $this->customer_email]);
        } catch (\Exception $e) {
            \Log::channel('daily')->error('❌ Müşteri onay e-postası gönderilemedi: ' . $e->getMessage());
        }
    }
}
