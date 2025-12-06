<?php

namespace App\Models;

use Modules\Payment\App\Contracts\Payable;

/**
 * Subscription Model - Alias/Bridge Class
 *
 * Bu sınıf Modules\Subscription\App\Models\Subscription'ı extend eder.
 * Payable interface geriye dönük uyumluluk için burada implement edilir.
 *
 * 🔥 ANA MODEL: Modules\Subscription\App\Models\Subscription
 * Bu dosya sadece backward compatibility için tutulmuştur.
 */
class Subscription extends \Modules\Subscription\App\Models\Subscription implements Payable
{
    // =========================================
    // Payable Interface Implementation
    // =========================================

    public function getPayableAmount(): float
    {
        return (float) $this->price_per_cycle;
    }

    public function getPayableDescription(): string
    {
        $planName = $this->plan ? $this->plan->title : 'Abonelik';
        return "{$planName} - {$this->subscription_number}";
    }

    public function getPayableCustomer(): array
    {
        return [
            'name' => $this->user->name ?? 'Misafir',
            'email' => $this->user->email ?? '',
            'phone' => $this->user->phone ?? '',
            'address' => 'Türkiye',
        ];
    }

    public function getPayableDetails(): ?array
    {
        return [
            'items' => [[
                'name' => $this->plan ? $this->plan->title : 'Abonelik',
                'price' => $this->price_per_cycle,
                'quantity' => 1,
            ]]
        ];
    }
}
