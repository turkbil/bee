<?php

namespace Modules\Payment\App\Contracts;

/**
 * Payable Interface
 *
 * Ödeme alabilen her model bu interface'i implement etmelidir.
 * ShopOrder, Subscription, Booking, Invoice vb.
 */
interface Payable
{
    /**
     * Ödeme tutarını döndür
     */
    public function getPaymentAmount(): float;

    /**
     * Para birimini döndür (TRY, USD, EUR vb.)
     */
    public function getPaymentCurrency(): string;

    /**
     * Ödeme yapan müşteriyi döndür (ad, email, telefon)
     */
    public function getPaymentCustomer(): array;

    /**
     * Sepet/Ürün bilgilerini döndür (PayTR için basket parametresi)
     *
     * @return array [
     *   ['name' => 'Ürün 1', 'price' => 1500, 'quantity' => 1],
     *   ['name' => 'Ürün 2', 'price' => 2500, 'quantity' => 2],
     * ]
     */
    public function getPaymentBasket(): array;

    /**
     * Ödeme açıklamasını döndür
     */
    public function getPaymentDescription(): string;

    /**
     * Başarılı ödeme callback'i
     */
    public function onPaymentCompleted($payment): void;

    /**
     * Başarısız ödeme callback'i
     */
    public function onPaymentFailed($payment): void;

    /**
     * İptal edilen ödeme callback'i
     */
    public function onPaymentCancelled($payment): void;
}
