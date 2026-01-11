<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Services;

use App\Models\User;
use Modules\Cart\App\Models\OrderItem;
use Modules\Cart\App\Models\Order;
use Modules\Subscription\App\Models\Subscription;
use Modules\Subscription\App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Log;

/**
 * Kurumsal Subscription Service
 *
 * Tenant 1001 (Muzibu) için kurumsal subscription aktivasyonu
 * Cart/Order modülleri genel kalır, Muzibu-spesifik kod burada
 */
class CorporateSubscriptionService
{
    /**
     * Kurumsal subscription aktivasyonu
     * OrderItem'daki target_user_ids için subscription oluşturur
     */
    public function activateCorporateSubscriptions(OrderItem $item, Order $order): bool
    {
        $metadata = $item->metadata ?? [];
        $targetUserIds = $metadata['target_user_ids'] ?? [];

        if (empty($targetUserIds)) {
            Log::channel('daily')->warning('⚠️ CorporateSubscriptionService: target_user_ids boş', [
                'order_id' => $order->order_id,
                'item_id' => $item->order_item_id,
            ]);
            return false;
        }

        Log::channel('daily')->info('🏢 Kurumsal Subscription Aktivasyonu Başladı', [
            'order_id' => $order->order_id,
            'target_users' => $targetUserIds,
            'count' => count($targetUserIds),
        ]);

        // Plan bilgilerini al
        $plan = $item->orderable;
        if (!$plan instanceof SubscriptionPlan) {
            Log::channel('daily')->error('❌ Plan bulunamadı veya yanlış tip', [
                'orderable_type' => $item->orderable_type,
            ]);
            return false;
        }

        // Cycle bilgilerini al
        $cycleKey = $metadata['cycle_key'] ?? null;
        $cycleMetadata = $metadata['cycle_metadata'] ?? null;

        if (!$cycleKey) {
            $cycles = $plan->getSortedCycles();
            $cycleKey = array_key_first($cycles) ?? 'monthly';
            $cycleMetadata = $cycles[$cycleKey] ?? null;
        }

        $durationDays = $cycleMetadata['duration_days'] ?? 30;
        $successCount = 0;

        foreach ($targetUserIds as $userId) {
            try {
                $user = User::find($userId);
                if (!$user) {
                    Log::channel('daily')->warning('⚠️ User bulunamadı', ['user_id' => $userId]);
                    continue;
                }

                // Zincir sistemi: Son subscription'ın bitiş tarihini bul
                $lastEndDate = $user->getLastSubscriptionEndDate();
                $hasActiveOrPending = $lastEndDate && $lastEndDate->isFuture();

                // Tarihleri hesapla
                $startDate = $hasActiveOrPending ? $lastEndDate : now();
                $endDate = $startDate->copy()->addDays($durationDays);

                // Status: mevcut aktif/pending varsa "pending", yoksa "active"
                $newStatus = $hasActiveOrPending ? 'pending' : 'active';

                // Birim fiyat (toplam / kullanıcı sayısı)
                $unitPrice = $item->unit_price;

                // Subscription oluştur
                $subscription = Subscription::create([
                    'user_id' => $userId,
                    'subscription_plan_id' => $plan->subscription_plan_id,
                    'subscription_number' => Subscription::generateSubscriptionNumber(),
                    'status' => $newStatus,
                    'cycle_key' => $cycleKey,
                    'cycle_metadata' => $cycleMetadata,
                    'price_per_cycle' => $unitPrice,
                    'currency' => $order->currency ?? 'TRY',
                    'has_trial' => false,
                    'trial_days' => 0,
                    'started_at' => $newStatus === 'active' ? now() : $startDate,
                    'current_period_start' => $startDate,
                    'current_period_end' => $endDate,
                    'next_billing_date' => $endDate,
                    'auto_renew' => false, // Kurumsal için otomatik yenileme kapalı
                    'billing_cycles_completed' => 1,
                    'total_paid' => $unitPrice,
                    'metadata' => [
                        'order_id' => $order->order_id,
                        'order_number' => $order->order_number,
                        'activated_at' => now()->toDateTimeString(),
                        'chain_position' => $hasActiveOrPending ? 'queued' : 'first',
                        'corporate' => true,
                        'corporate_owner_id' => $order->user_id,
                        'corporate_order_item_id' => $item->order_item_id,
                    ],
                ]);

                // User subscription_expires_at güncelle
                $user->recalculateSubscriptionExpiry();

                Log::channel('daily')->info('✅ Kurumsal subscription oluşturuldu', [
                    'subscription_id' => $subscription->subscription_id,
                    'user_id' => $userId,
                    'user_name' => $user->name,
                    'status' => $newStatus,
                    'start_date' => $startDate->toDateTimeString(),
                    'end_date' => $endDate->toDateTimeString(),
                ]);

                $successCount++;

            } catch (\Exception $e) {
                Log::channel('daily')->error('❌ Kurumsal subscription hatası', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::channel('daily')->info('🏢 Kurumsal Subscription Aktivasyonu Tamamlandı', [
            'order_id' => $order->order_id,
            'total_users' => count($targetUserIds),
            'success_count' => $successCount,
        ]);

        return $successCount > 0;
    }

    /**
     * User ID'lerden isim listesi döner (admin için)
     */
    public function getCorporateMemberNames(array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }

        return User::whereIn('id', $userIds)
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * User ID'lerin geçerliliğini kontrol eder
     */
    public function validateCorporateUsers(array $userIds): array
    {
        $valid = [];
        $invalid = [];

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $valid[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            } else {
                $invalid[] = $userId;
            }
        }

        return [
            'valid' => $valid,
            'invalid' => $invalid,
            'all_valid' => empty($invalid),
        ];
    }

    /**
     * Subscription'ın kurumsal olup olmadığını kontrol eder
     */
    public static function isCorporateSubscription(Subscription $subscription): bool
    {
        return ($subscription->metadata['corporate'] ?? false) === true;
    }

    /**
     * Kurumsal subscription'ın sahibini döner
     */
    public static function getCorporateOwner(Subscription $subscription): ?User
    {
        $ownerId = $subscription->metadata['corporate_owner_id'] ?? null;
        return $ownerId ? User::find($ownerId) : null;
    }
}
