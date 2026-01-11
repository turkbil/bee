<?php

namespace Modules\Payment\App\Contracts;

/**
 * Payable Interface
 *
 * Bu interface'i implement eden modeller Payment sistemi ile ödeme alabilir.
 *
 * Kullanım Örnekleri:
 * - ShopOrder (Shop modülü)
 * - Membership (UserManagement modülü)
 * - Subscription (gelecekte)
 * - Invoice (gelecekte)
 */
interface Payable
{
    /**
     * Ödenecek tutarı döndür (float olarak)
     *
     * @return float Ödeme tutarı (örn: 199.99)
     */
    public function getPayableAmount(): float;

    /**
     * Ödeme açıklamasını döndür
     * (PayTR user_basket ve merchant_oid için kullanılır)
     *
     * @return string Açıklama (örn: "Sipariş #ORD-20251112-ABC123")
     */
    public function getPayableDescription(): string;

    /**
     * Ödemeyi yapan müşteri bilgilerini döndür
     * (PayTR user_name, user_address, user_phone, email için kullanılır)
     *
     * @return array Müşteri bilgileri
     * [
     *     'name' => 'Ahmet Yılmaz',
     *     'email' => 'ahmet@example.com',
     *     'phone' => '+905551234567',
     *     'address' => 'İstanbul, Türkiye'
     * ]
     */
    public function getPayableCustomer(): array;

    /**
     * Ödeme detaylarını döndür (opsiyonel, sepet içeriği için)
     * (PayTR user_basket içeriği için kullanılır)
     *
     * @return array|null Sepet içeriği
     * [
     *     'items' => [
     *         [
     *             'name' => 'Ürün Adı',
     *             'price' => 99.99,
     *             'quantity' => 2
     *         ]
     *     ]
     * ]
     */
    public function getPayableDetails(): ?array;
}
