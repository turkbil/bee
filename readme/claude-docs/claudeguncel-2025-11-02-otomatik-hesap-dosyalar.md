# 🚀 OTOMATİK HESAP OLUŞTURMA - DOSYA LİSTESİ

**Tarih:** 2025-11-02
**Karar:** Guest sipariş verdiğinde otomatik User hesabı oluştur

---

## 📋 SADECE YAPILACAK DEĞİŞİKLİKLER

### **1. CheckoutPageNew.php'ye Eklenecek Kod**

**Yer:** `submitOrder()` metodunun sonuna (DB::commit()'ten önce)

```php
// Sipariş oluşturulduktan sonra...

// ✅ OTOMATİK USER OLUŞTUR (Guest için)
if (!Auth::check() && !User::where('email', $customer->email)->exists()) {
    // Random şifre oluştur
    $randomPassword = Str::random(12);

    $user = User::create([
        'name' => $customer->first_name . ' ' . $customer->last_name,
        'email' => $customer->email,
        'password' => Hash::make($randomPassword),
    ]);

    // Customer'ı user'a bağla
    $customer->update(['user_id' => $user->id]);

    // Otomatik login
    Auth::login($user);

    // Email gönder (şifre ile)
    Mail::to($user->email)->send(new AccountCreatedMail($user, $randomPassword, $order));
}

DB::commit();

// Sipariş onay email'i
Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));

return redirect()->route('shop.order.success', $order->order_number);
```

---

### **2. Guest Adres Formu Ekle**

**CheckoutPageNew.php'ye property'ler ekle:**

```php
// Guest inline adres formu (Teslimat)
public $shipping_address_line_1 = '';
public $shipping_address_line_2 = '';
public $shipping_city = '';
public $shipping_district = '';
public $shipping_postal_code = '';
public $shipping_delivery_notes = '';
```

**submitOrder() içinde adres oluştur:**

```php
// Guest için adres oluştur
if (!$this->customerId || !$this->shipping_address_id) {
    $shippingAddress = ShopCustomerAddress::create([
        'customer_id' => $customer->customer_id,
        'address_type' => 'shipping',
        'address_line_1' => $this->shipping_address_line_1,
        'address_line_2' => $this->shipping_address_line_2,
        'city' => $this->shipping_city,
        'district' => $this->shipping_district,
        'postal_code' => $this->shipping_postal_code,
        'delivery_notes' => $this->shipping_delivery_notes,
        'is_default_shipping' => true,
    ]);

    $this->shipping_address_id = $shippingAddress->address_id;

    // Fatura adresi aynıysa kopyala
    if ($this->billing_same_as_shipping) {
        $billingAddress = ShopCustomerAddress::create([
            'customer_id' => $customer->customer_id,
            'address_type' => 'billing',
            'address_line_1' => $this->shipping_address_line_1,
            'address_line_2' => $this->shipping_address_line_2,
            'city' => $this->shipping_city,
            'district' => $this->shipping_district,
            'postal_code' => $this->shipping_postal_code,
            'is_default_billing' => true,
        ]);

        $this->billing_address_id = $billingAddress->address_id;
    }
}
```

---

## ✅ OLUŞTURULACAK YENİ DOSYALAR

Sadece şunları oluşturacağız (basit versiyon):

1. **OrderController.php** - Sipariş onay/takip
2. **order-success.blade.php** - Sipariş onay sayfası
3. **order-track.blade.php** - Sipariş takip formu
4. **order-detail.blade.php** - Sipariş detay
5. **AccountCreatedMail.php** - Hesap oluşturma email'i
6. **account-created.blade.php** - Hesap email template
7. **OrderConfirmationMail.php** - Sipariş onay email'i
8. **confirmation.blade.php** - Sipariş email template

**CreateAccountFromOrder SİLİNDİ!** (Artık gerek yok)

---

## 📧 EMAIL AKIŞI

**Sipariş sonrası 2 email gider:**

1. **Sipariş Onayı Email** (`OrderConfirmationMail`)
   - Sipariş detayları
   - Banka bilgileri
   - Teslimat adresi

2. **Hesap Oluşturma Email** (`AccountCreatedMail`)
   - Email: xxx@example.com
   - Şifre: ABC123XYZ456
   - "Hesabınız otomatik oluşturuldu, şifrenizi değiştirebilirsiniz"

---

## ⏱️ TAHMİNİ SÜRE: 2 SAAT

| İşlem | Süre |
|-------|------|
| OrderController oluştur | 10dk |
| Views oluştur (3 adet) | 30dk |
| Email template'ler (2 adet) | 20dk |
| CheckoutPageNew güncelle | 30dk |
| checkout-page-new.blade.php güncelle (adres formu) | 20dk |
| Route'lar ekle | 5dk |
| Test et | 15dk |
| **TOPLAM** | **~2 saat** |

---

## 🎯 SONRAKİ ADIM

Şimdi dosyaları tek tek oluşturalım mı?

**Sıralama:**
1. OrderController.php
2. AccountCreatedMail.php
3. OrderConfirmationMail.php
4. Email template'ler
5. Views
6. CheckoutPageNew.php güncelle
7. Routes ekle
8. Test

Başlayalım mı?
