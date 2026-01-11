<?php

namespace Modules\Subscription\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Subscription\App\Models\Subscription;
use Modules\Cart\App\Models\Order;
use Modules\Cart\App\Models\OrderItem;
use Modules\Cart\App\Models\Address;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Models\PaymentMethod;

class SubscriptionCheckoutController extends Controller
{
    /**
     * Subscription için ödeme sayfasına yönlendir
     * Eğer order yoksa otomatik oluştur
     */
    public function show($subscriptionId)
    {
        // 🔥 AGGRESSIVE DEBUG - Write to file directly
        $debugLog = storage_path('logs/subscription-checkout-debug.log');
        file_put_contents($debugLog, "\n\n=== " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
        file_put_contents($debugLog, "Controller called: subscription_id={$subscriptionId}\n", FILE_APPEND);
        file_put_contents($debugLog, "Auth check: " . (auth()->check() ? 'YES' : 'NO') . "\n", FILE_APPEND);
        file_put_contents($debugLog, "Auth user ID: " . (auth()->id() ?: 'NULL') . "\n", FILE_APPEND);

        \Log::info('🔍 SubscriptionCheckoutController::show called', [
            'subscription_id' => $subscriptionId,
            'user_id' => auth()->id(),
            'auth_check' => auth()->check(),
            'request_url' => request()->fullUrl(),
        ]);

        $subscription = Subscription::find($subscriptionId);

        file_put_contents($debugLog, "Subscription found: " . ($subscription ? 'YES' : 'NO') . "\n", FILE_APPEND);
        if ($subscription) {
            file_put_contents($debugLog, "Subscription user_id: {$subscription->user_id}\n", FILE_APPEND);
        }

        \Log::info('🔍 Subscription lookup result', [
            'subscription_id' => $subscriptionId,
            'found' => $subscription ? 'yes' : 'no',
            'subscription_user_id' => $subscription ? $subscription->user_id : null,
            'auth_user_id' => auth()->id(),
        ]);

        if (!$subscription) {
            \Log::warning('❌ Subscription not found, redirecting to dashboard', [
                'subscription_id' => $subscriptionId,
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'Abonelik bulunamadı.');
        }

        // Kullanıcı kontrolü
        if ($subscription->user_id !== auth()->id()) {
            \Log::warning('❌ User mismatch, redirecting to dashboard', [
                'subscription_user_id' => $subscription->user_id,
                'auth_user_id' => auth()->id(),
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'Bu aboneliğe erişim yetkiniz yok.');
        }

        // Subscription için mevcut pending order var mı kontrol et
        $order = Order::where('user_id', auth()->id())
            ->where(function($q) use ($subscription) {
                $q->whereJsonContains('metadata->subscription_id', $subscription->subscription_id)
                  ->orWhereJsonContains('metadata->subscription_id', (string)$subscription->subscription_id);
            })
            ->whereIn('payment_status', ['pending', 'processing'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Order varsa payment sayfasına yönlendir
        if ($order) {
            \Log::info('✅ Existing order found', ['order_number' => $order->order_number]);
            return redirect()->route('payment.page', ['orderNumber' => $order->order_number]);
        }

        // Order yoksa oluştur
        try {
            DB::beginTransaction();

            $user = auth()->user();
            $plan = $subscription->plan;

            // Kullanıcının default billing address'ini al
            $billingAddress = Address::where('user_id', $user->id)
                ->where('is_default_billing', 1)
                ->first();

            if (!$billingAddress) {
                // Default yoksa ilk adresi al
                $billingAddress = Address::where('user_id', $user->id)
                    ->whereNotNull('address_line_1')
                    ->where('address_line_1', '!=', '')
                    ->first();
            }

            // Order oluştur
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),

                // İletişim bilgileri
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?? '',

                // Adres snapshot (JSON) - varsa
                'billing_address' => $billingAddress ? $billingAddress->toSnapshot() : json_encode([
                    'full_name' => $user->name,
                    'email' => $user->email,
                    'city' => 'Türkiye',
                ]),
                'shipping_address' => null, // Dijital ürün

                'subtotal' => $subscription->price_per_cycle,
                'tax_amount' => 0,
                'shipping_cost' => 0,
                'discount_amount' => 0,
                'total_amount' => $subscription->price_per_cycle,
                'currency' => $subscription->currency ?? 'TRY',
                'status' => 'pending',
                'payment_status' => 'pending',
                'requires_shipping' => false, // Dijital ürün (abonelik)

                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),

                // Metadata - subscription ID kaydet
                'metadata' => json_encode([
                    'subscription_id' => $subscription->subscription_id,
                    'subscription_number' => $subscription->subscription_number,
                    'billing_cycle' => $subscription->billing_cycle,
                ]),
            ]);

            // Order item oluştur
            OrderItem::create([
                'order_id' => $order->order_id,
                'item_type' => get_class($subscription),
                'item_id' => $subscription->subscription_id,
                'item_title' => ($plan ? $plan->title_text : 'Premium Abonelik') . ' - ' . ucfirst($subscription->billing_cycle ?? 'monthly'),
                'item_sku' => 'SUB-' . $subscription->subscription_id,
                'quantity' => 1,
                'unit_price' => $subscription->price_per_cycle,
                'subtotal' => $subscription->price_per_cycle,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total' => $subscription->price_per_cycle,
            ]);

            // PayTR payment method'unu al
            $paymentMethod = PaymentMethod::where('gateway', 'paytr')
                ->where('is_active', 1)
                ->first();

            if (!$paymentMethod) {
                throw new \Exception('PayTR ödeme yöntemi bulunamadı.');
            }

            // Payment kaydı oluştur
            $payment = Payment::create([
                'payment_method_id' => $paymentMethod->payment_method_id,
                'payable_type' => Order::class,
                'payable_id' => $order->order_id,
                'amount' => $subscription->price_per_cycle,
                'currency' => $subscription->currency ?? 'TRY',
                'exchange_rate' => 1,
                'amount_in_base_currency' => $subscription->price_per_cycle,
                'status' => 'pending',
                'gateway' => 'paytr',
                'payment_type' => 'subscription',
            ]);

            DB::commit();

            \Log::info('✅ Order created for subscription', [
                'order_number' => $order->order_number,
                'subscription_id' => $subscription->subscription_id,
                'amount' => $subscription->price_per_cycle,
            ]);

            // Payment sayfasına yönlendir
            return redirect()->route('payment.page', ['orderNumber' => $order->order_number]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('❌ Subscription order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Ödeme hazırlanırken hata oluştu: ' . $e->getMessage());
        }
    }
}
