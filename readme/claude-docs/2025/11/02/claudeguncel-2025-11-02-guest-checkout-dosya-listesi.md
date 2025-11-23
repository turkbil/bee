# 📂 GUEST CHECKOUT - OLUŞTURULACAK/DEĞİŞTİRİLECEK DOSYALAR

**Tarih:** 2025-11-02
**Tenant:** ixtif.com (ID: 2)
**Proje:** Guest Checkout + Sipariş Sonrası Opsiyonel Hesap Oluşturma

---

## 🎯 PROJE KAPSAMI

**Hedef:**
- ✅ Guest checkout (zorunlu login YOK)
- ✅ Guest için inline adres formu
- ✅ Sipariş onay sayfası
- ✅ Sipariş sonrası opsiyonel hesap oluşturma
- ✅ Email onay sistemi
- ✅ Guest sipariş takip sistemi

---

## 📋 DOSYA LİSTESİ

### **PHASE 1: Guest Checkout Core (Öncelik Yüksek)**

#### 1. **BACKEND - Controller'lar**

##### ✅ `Modules/Shop/app/Http/Controllers/Front/OrderController.php` (YENİ)
**Amaç:** Sipariş onay sayfası, sipariş detay, sipariş takip

**Metodlar:**
```php
<?php

namespace Modules\Shop\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Modules\Shop\App\Models\ShopOrder;

class OrderController
{
    // Sipariş onay sayfası (sipariş sonrası)
    public function success(string $orderNumber)
    {
        $order = ShopOrder::where('order_number', $orderNumber)
            ->with(['items.product', 'customer'])
            ->firstOrFail();

        return view('shop::front.order-success', compact('order'));
    }

    // Guest sipariş takip sayfası (form)
    public function trackForm()
    {
        return view('shop::front.order-track');
    }

    // Guest sipariş takip (sorgu)
    public function track(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'order_number' => 'required|string',
        ]);

        $order = ShopOrder::where('order_number', $request->order_number)
            ->where('customer_email', $request->email)
            ->with(['items.product'])
            ->first();

        if (!$order) {
            return back()->withErrors(['error' => 'Sipariş bulunamadı. Lütfen bilgilerinizi kontrol edin.']);
        }

        return view('shop::front.order-detail', compact('order'));
    }
}
```

**Neden gerekli:** Sipariş onay, takip sayfaları için controller

---

#### 2. **BACKEND - Livewire Component'ler**

##### ✅ `Modules/Shop/app/Http/Livewire/Front/CheckoutPageNew.php` (DEĞİŞİKLİK)
**Değişiklikler:**

**a) Guest için inline adres formu ekle (Property'ler):**
```php
// Guest inline adres formu (Teslimat)
public $shipping_address_line_1 = '';
public $shipping_address_line_2 = '';
public $shipping_city = '';
public $shipping_district = '';
public $shipping_postal_code = '';
public $shipping_delivery_notes = '';

// Guest inline adres formu (Fatura - eğer "Fatura = Teslimat" değilse)
public $billing_address_line_1 = '';
public $billing_address_line_2 = '';
public $billing_city = '';
public $billing_district = '';
public $billing_postal_code = '';
```

**b) submitOrder() metodunu güncelle:**
```php
public function submitOrder()
{
    // ... mevcut validation ...

    DB::beginTransaction();

    try {
        // Müşteri oluştur veya güncelle
        $customer = $this->createOrUpdateCustomer();

        // ❗ YENİ: Guest için adres oluştur
        if (!$this->customerId) {
            // Teslimat adresi oluştur
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

            // Fatura adresi (eğer teslimat ile aynıysa)
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

        // ... mevcut sipariş oluşturma kodu ...

        DB::commit();

        // ❗ YENİ: Sipariş onay sayfasına redirect
        return redirect()->route('shop.order.success', $order->order_number);

    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error', 'Sipariş oluşturulurken bir hata oluştu: ' . $e->getMessage());
    }
}
```

**Neden gerekli:** Guest için adres formu eklemek, sipariş sonrası redirect

---

##### ✅ `Modules/Shop/app/Http/Livewire/Front/CreateAccountFromOrder.php` (YENİ)
**Amaç:** Sipariş sonrası opsiyonel hesap oluşturma

```php
<?php

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Shop\App\Models\ShopOrder;
use Modules\Shop\App\Models\ShopCustomer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CreateAccountFromOrder extends Component
{
    public $order;
    public $password = '';
    public $password_confirmation = '';

    public function mount(ShopOrder $order)
    {
        $this->order = $order;
    }

    public function createAccount()
    {
        // Zaten login ise işlem yapma
        if (Auth::check()) {
            session()->flash('info', 'Zaten giriş yapmışsınız.');
            return redirect()->route('account.orders');
        }

        $this->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Şifre zorunludur',
            'password.min' => 'Şifre en az 8 karakter olmalıdır',
            'password.confirmed' => 'Şifreler eşleşmiyor',
        ]);

        // Email zaten kayıtlı mı kontrol et
        if (User::where('email', $this->order->customer_email)->exists()) {
            session()->flash('error', 'Bu email adresi zaten kayıtlı. Lütfen giriş yapın.');
            return redirect()->route('login');
        }

        try {
            // User oluştur
            $user = User::create([
                'name' => $this->order->customer_name,
                'email' => $this->order->customer_email,
                'password' => Hash::make($this->password),
            ]);

            // Guest customer'ı user'a bağla
            $customer = ShopCustomer::where('customer_id', $this->order->customer_id)->first();

            if ($customer) {
                $customer->update(['user_id' => $user->id]);
            }

            // Otomatik login
            Auth::login($user);

            session()->flash('success', 'Hesabınız oluşturuldu! Artık siparişlerinizi takip edebilirsiniz.');

            return redirect()->route('account.orders');

        } catch (\Exception $e) {
            session()->flash('error', 'Hesap oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('shop::livewire.front.create-account-from-order');
    }
}
```

**Neden gerekli:** Sipariş sonrası opsiyonel hesap oluşturma için

---

#### 3. **BACKEND - Models (Mevcut - Değişiklik Gerekebilir)**

##### ⚠️ `Modules/Shop/app/Models/ShopOrder.php` (KONTROL)
**Kontrol edilecek:**
- `customer_name`, `customer_email`, `customer_phone` field'ları var mı?
- `order_number` unique mi?
- Relationship'ler doğru mu? (`customer`, `items`)

**Eklenecek accessor (eğer yoksa):**
```php
public function getStatusLabelAttribute(): string
{
    return match($this->status) {
        'pending' => 'Beklemede',
        'processing' => 'Hazırlanıyor',
        'shipped' => 'Kargoda',
        'delivered' => 'Teslim Edildi',
        'cancelled' => 'İptal Edildi',
        default => 'Bilinmiyor',
    };
}

public function getPaymentStatusLabelAttribute(): string
{
    return match($this->payment_status) {
        'pending' => 'Ödeme Bekleniyor',
        'paid' => 'Ödendi',
        'failed' => 'Ödeme Başarısız',
        'refunded' => 'İade Edildi',
        default => 'Bilinmiyor',
    };
}
```

---

#### 4. **FRONTEND - Views (Blade Template'ler)**

##### ✅ `Modules/Shop/resources/views/livewire/front/checkout-page-new.blade.php` (DEĞİŞİKLİK)
**Değişiklikler:**

**a) Guest için inline adres formu ekle:**

```blade
{{-- 2. Teslimat Adresi --}}
<div class="bg-white/20 dark:bg-gray-800/20 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
        <i class="fa-solid fa-truck mr-2 text-blue-500 dark:text-blue-400"></i>
        Teslimat Adresi
    </h2>

    @if($customerId)
        {{-- LOGIN USER: Modal ile adres seç --}}
        @if($shipping_address_id)
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                {{-- Seçili adres göster --}}
            </div>
        @else
            <button wire:click="openShippingModal" class="...">
                Teslimat Adresi Seç
            </button>
        @endif
    @else
        {{-- GUEST USER: Inline form --}}
        <div class="space-y-4">
            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                    Adres <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model="shipping_address_line_1"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    placeholder="Mahalle, Sokak, No">
                @error('shipping_address_line_1') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                    Adres Satır 2 (Opsiyonel)
                </label>
                <input type="text" wire:model="shipping_address_line_2"
                    class="w-full px-4 py-2.5 rounded-lg border"
                    placeholder="Daire, Kat, vb.">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        İl <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="shipping_city"
                        class="w-full px-4 py-2.5 rounded-lg border"
                        placeholder="Örn: İstanbul">
                    @error('shipping_city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        İlçe <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="shipping_district"
                        class="w-full px-4 py-2.5 rounded-lg border"
                        placeholder="Örn: Kadıköy">
                    @error('shipping_district') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Posta Kodu (Opsiyonel)
                    </label>
                    <input type="text" wire:model="shipping_postal_code"
                        class="w-full px-4 py-2.5 rounded-lg border"
                        placeholder="34000">
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                    Teslimat Notları (Opsiyonel)
                </label>
                <textarea wire:model="shipping_delivery_notes"
                    class="w-full px-4 py-2.5 rounded-lg border"
                    rows="2"
                    placeholder="Kapıcıya bırakabilirsiniz, vb."></textarea>
            </div>
        </div>
    @endif
</div>
```

**Neden gerekli:** Guest kullanıcı adres girebilsin

---

##### ✅ `Modules/Shop/resources/views/front/order-success.blade.php` (YENİ)
**Amaç:** Sipariş onay sayfası (sipariş sonrası)

```blade
@extends('themes.ixtif.layouts.app')

@section('title', 'Sipariş Onaylandı - ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">

        {{-- Başarı Mesajı --}}
        <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-700 rounded-xl p-8 mb-6 text-center">
            <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-check text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Siparişiniz Alındı!
            </h1>
            <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">
                Sipariş numaranız: <strong class="text-blue-600 dark:text-blue-400">{{ $order->order_number }}</strong>
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                📧 <strong>{{ $order->customer_email }}</strong> adresinize sipariş onayı gönderildi.
            </p>
        </div>

        {{-- Sipariş Özeti --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                Sipariş Detayları
            </h2>

            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{ $item->product_title }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Adet: {{ $item->quantity }} × {{ number_format($item->unit_price, 2, ',', '.') }} ₺
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900 dark:text-white">
                            {{ number_format($item->subtotal, 2, ',', '.') }} ₺
                        </p>
                    </div>
                </div>
                @endforeach

                {{-- Toplam --}}
                <div class="flex justify-between items-center text-lg font-bold text-gray-900 dark:text-white pt-4">
                    <span>TOPLAM:</span>
                    <span class="text-blue-600 dark:text-blue-400">
                        {{ number_format($order->total_amount, 2, ',', '.') }} ₺
                    </span>
                </div>
            </div>
        </div>

        {{-- Ödeme Bilgileri (Manuel Ödeme) --}}
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-200 dark:border-yellow-700 rounded-xl p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fa-solid fa-credit-card mr-2 text-yellow-600 dark:text-yellow-400"></i>
                Ödeme Bilgileri
            </h2>

            <p class="text-gray-700 dark:text-gray-300 mb-4">
                Siparişinizi tamamlamak için aşağıdaki banka hesabımıza ödeme yapabilirsiniz:
            </p>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 space-y-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <strong>Banka:</strong> Türkiye İş Bankası
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <strong>Hesap Adı:</strong> TUUFI Endüstriyel Ekipman
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <strong>IBAN:</strong> <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">TR XX XXXX XXXX XXXX XXXX XXXX XX</code>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <strong>Açıklama:</strong> {{ $order->order_number }}
                </p>
            </div>

            <div class="mt-4 flex gap-4">
                <a href="{{ whatsapp_link(null, 'Sipariş No: ' . $order->order_number . ' - Ödeme yaptım') }}"
                   target="_blank"
                   class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg text-center transition-all flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                    <span>WhatsApp ile Bildir</span>
                </a>

                <a href="tel:02167553555"
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-phone"></i>
                    <span>Bizi Arayın</span>
                </a>
            </div>
        </div>

        {{-- Hesap Oluşturma (Guest için) --}}
        @if(!Auth::check())
        @livewire('shop::create-account-from-order', ['order' => $order])
        @endif

        {{-- Anasayfaya Dön --}}
        <div class="text-center mt-8">
            <a href="{{ route('shop.index') }}"
               class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Alışverişe Devam Et</span>
            </a>
        </div>

    </div>
</div>
@endsection
```

**Neden gerekli:** Sipariş onay sayfası + Banka bilgileri + Hesap oluşturma

---

##### ✅ `Modules/Shop/resources/views/livewire/front/create-account-from-order.blade.php` (YENİ)
**Amaç:** Opsiyonel hesap oluşturma component'i

```blade
<div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-xl p-6">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 flex items-center">
        <i class="fa-solid fa-user-plus mr-2 text-blue-600 dark:text-blue-400"></i>
        Hesap Oluşturarak Avantaj Kazanın
    </h3>

    <ul class="text-sm text-gray-700 dark:text-gray-300 mb-4 space-y-1">
        <li class="flex items-center gap-2">
            <i class="fa-solid fa-check text-green-600"></i>
            <span>Siparişlerinizi takip edin</span>
        </li>
        <li class="flex items-center gap-2">
            <i class="fa-solid fa-check text-green-600"></i>
            <span>Adreslerinizi kaydedin (hızlı checkout)</span>
        </li>
        <li class="flex items-center gap-2">
            <i class="fa-solid fa-check text-green-600"></i>
            <span>Sipariş geçmişinizi görün</span>
        </li>
    </ul>

    <form wire:submit="createAccount" class="space-y-4">
        <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                Email (değiştirilemez)
            </label>
            <input type="text" value="{{ $order->customer_email }}" disabled
                class="w-full px-4 py-2.5 rounded-lg border bg-gray-100 dark:bg-gray-700 text-gray-500">
        </div>

        <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                Şifre <span class="text-red-500">*</span>
            </label>
            <input type="password" wire:model="password"
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                placeholder="En az 8 karakter">
            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                Şifre Tekrar <span class="text-red-500">*</span>
            </label>
            <input type="password" wire:model="password_confirmation"
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                placeholder="Şifreyi tekrar girin">
        </div>

        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-user-plus"></i>
            <span>Ücretsiz Hesap Oluştur</span>
        </button>
    </form>

    <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 text-center">
        Hesap oluşturmak tamamen ücretsizdir ve zorunlu değildir.
    </p>
</div>
```

**Neden gerekli:** Sipariş sonrası hesap oluşturma UI

---

##### ✅ `Modules/Shop/resources/views/front/order-track.blade.php` (YENİ)
**Amaç:** Guest sipariş takip formu

```blade
@extends('themes.ixtif.layouts.app')

@section('title', 'Siparişimi Takip Et')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-box-open text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Siparişimi Takip Et
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Email adresiniz ve sipariş numaranızla siparişinizi sorgulayabilirsiniz.
                </p>
            </div>

            @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4">
                {{ $errors->first('error') }}
            </div>
            @endif

            <form action="{{ route('shop.order.track.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Email Adresi <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="ornek@email.com">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Sipariş Numarası <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="order_number" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="ORD-20251102-ABCDEF">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all">
                    <i class="fa-solid fa-search mr-2"></i>
                    Sipariş Sorgula
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Yardıma mı ihtiyacınız var?
                </p>
                <a href="{{ whatsapp_link(null, 'Sipariş takibi hakkında bilgi almak istiyorum') }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 text-green-600 dark:text-green-400 hover:underline">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                    <span>WhatsApp ile İletişime Geç</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
```

**Neden gerekli:** Guest sipariş takip formu

---

##### ✅ `Modules/Shop/resources/views/front/order-detail.blade.php` (YENİ)
**Amaç:** Sipariş detay sayfası (guest + login)

```blade
@extends('themes.ixtif.layouts.app')

@section('title', 'Sipariş Detayı - ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">

        {{-- Sipariş Başlığı --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Sipariş #{{ $order->order_number }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $order->created_at->format('d.m.Y H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-4 py-2 rounded-lg text-sm font-semibold
                        {{ $order->status === 'delivered' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $order->status_label }}
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $order->payment_status_label }}
                    </p>
                </div>
            </div>

            {{-- İletişim Bilgileri --}}
            <div class="grid md:grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">İletişim</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->customer_name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->customer_email }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->customer_phone }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Teslimat Adresi</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_district }}, {{ $order->shipping_city }}<br>
                        {{ $order->shipping_postal_code }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Sipariş Kalemleri --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Sipariş Detayları</h2>

            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{ $item->product_title }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            SKU: {{ $item->product_sku }} | Adet: {{ $item->quantity }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ number_format($item->unit_price, 2, ',', '.') }} ₺
                        </p>
                        <p class="font-bold text-gray-900 dark:text-white">
                            {{ number_format($item->subtotal, 2, ',', '.') }} ₺
                        </p>
                    </div>
                </div>
                @endforeach

                {{-- Toplam --}}
                <div class="space-y-2 pt-4">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Ara Toplam:</span>
                        <span>{{ number_format($order->subtotal, 2, ',', '.') }} ₺</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>KDV:</span>
                        <span>{{ number_format($order->tax_amount, 2, ',', '.') }} ₺</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white pt-2 border-t">
                        <span>TOPLAM:</span>
                        <span class="text-blue-600 dark:text-blue-400">{{ number_format($order->total_amount, 2, ',', '.') }} ₺</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Geri Dön --}}
        <div class="text-center">
            <a href="{{ route('shop.order.track') }}"
               class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Yeni Sipariş Sorgula</span>
            </a>
        </div>

    </div>
</div>
@endsection
```

**Neden gerekli:** Sipariş detay gösterimi (guest takip)

---

#### 5. **ROUTES**

##### ✅ `routes/web.php` (DEĞİŞİKLİK - EKLE)

```php
// ... mevcut route'lar ...

// SHOP ORDER ROUTES (Guest + Login)
Route::middleware(['tenant', 'locale.site'])
    ->prefix('shop/order')
    ->group(function () {
        // Sipariş onay sayfası (sipariş sonrası redirect)
        Route::get('/success/{order_number}', [\Modules\Shop\App\Http\Controllers\Front\OrderController::class, 'success'])
            ->name('shop.order.success');

        // Guest sipariş takip formu
        Route::get('/track', [\Modules\Shop\App\Http\Controllers\Front\OrderController::class, 'trackForm'])
            ->name('shop.order.track');

        // Guest sipariş takip sorgusu
        Route::post('/track', [\Modules\Shop\App\Http\Controllers\Front\OrderController::class, 'track'])
            ->name('shop.order.track.submit');
    });
```

**Neden gerekli:** Yeni route'lar eklemek

---

#### 6. **EMAIL TEMPLATE'LERİ**

##### ✅ `app/Mail/OrderConfirmationMail.php` (YENİ)

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Shop\App\Models\ShopOrder;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(ShopOrder $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Sipariş Onayı - ' . $this->order->order_number)
            ->view('emails.orders.confirmation');
    }
}
```

**Neden gerekli:** Sipariş onay email'i göndermek için

---

##### ✅ `resources/views/emails/orders/confirmation.blade.php` (YENİ)

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sipariş Onayı</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">

        {{-- Header --}}
        <div style="background-color: #3B82F6; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">✅ Siparişiniz Alındı!</h1>
        </div>

        {{-- Content --}}
        <div style="background-color: white; padding: 30px; border-radius: 0 0 8px 8px;">

            <p>Merhaba <strong>{{ $order->customer_name }}</strong>,</p>

            <p>Siparişiniz başarıyla alındı. En kısa sürede hazırlayıp kargoya vereceğiz.</p>

            <div style="background-color: #EFF6FF; border-left: 4px solid #3B82F6; padding: 15px; margin: 20px 0;">
                <p style="margin: 0;"><strong>Sipariş Numarası:</strong> {{ $order->order_number }}</p>
                <p style="margin: 5px 0 0 0;"><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
            </div>

            {{-- Ürünler --}}
            <h2 style="margin-top: 30px; font-size: 18px; color: #333;">Sipariş Detayları</h2>

            <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
                <thead>
                    <tr style="background-color: #F3F4F6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #E5E7EB;">Ürün</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid #E5E7EB;">Adet</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid #E5E7EB;">Fiyat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #E5E7EB;">{{ $item->product_title }}</td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #E5E7EB;">{{ $item->quantity }}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #E5E7EB;">{{ number_format($item->subtotal, 2, ',', '.') }} ₺</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Ara Toplam:</td>
                        <td style="padding: 10px; text-align: right;">{{ number_format($order->subtotal, 2, ',', '.') }} ₺</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">KDV:</td>
                        <td style="padding: 10px; text-align: right;">{{ number_format($order->tax_amount, 2, ',', '.') }} ₺</td>
                    </tr>
                    <tr style="font-size: 18px; font-weight: bold; color: #3B82F6;">
                        <td colspan="2" style="padding: 10px; text-align: right;">TOPLAM:</td>
                        <td style="padding: 10px; text-align: right;">{{ number_format($order->total_amount, 2, ',', '.') }} ₺</td>
                    </tr>
                </tbody>
            </table>

            {{-- Ödeme Bilgileri --}}
            <div style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 15px; margin: 20px 0;">
                <h3 style="margin: 0 0 10px 0; font-size: 16px;">💳 Ödeme Bilgileri</h3>
                <p style="margin: 0;">Siparişinizi tamamlamak için aşağıdaki hesaba ödeme yapabilirsiniz:</p>
                <p style="margin: 10px 0 0 0; font-family: monospace; font-size: 14px;">
                    <strong>IBAN:</strong> TR XX XXXX XXXX XXXX XXXX XXXX XX<br>
                    <strong>Açıklama:</strong> {{ $order->order_number }}
                </p>
            </div>

            {{-- Teslimat Adresi --}}
            <h3 style="margin-top: 30px; font-size: 16px;">📍 Teslimat Adresi</h3>
            <p style="margin: 10px 0;">
                {{ $order->shipping_address }}<br>
                {{ $order->shipping_district }}, {{ $order->shipping_city }}<br>
                {{ $order->shipping_postal_code }}
            </p>

            {{-- İletişim --}}
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #E5E7EB; text-align: center;">
                <p>Sorularınız için bizimle iletişime geçebilirsiniz:</p>
                <p>
                    <a href="tel:02167553555" style="color: #3B82F6; text-decoration: none;">📞 0216 755 35 55</a> |
                    <a href="mailto:info@ixtif.com" style="color: #3B82F6; text-decoration: none;">✉️ info@ixtif.com</a>
                </p>
            </div>

        </div>

        {{-- Footer --}}
        <div style="text-align: center; padding: 20px; color: #6B7280; font-size: 12px;">
            <p>© {{ date('Y') }} TUUFI Endüstriyel Ekipman. Tüm hakları saklıdır.</p>
        </div>

    </div>
</body>
</html>
```

**Neden gerekli:** Email HTML template

---

#### 7. **CheckoutPageNew'e Email Gönderimi Ekle**

##### ⚠️ `Modules/Shop/app/Http/Livewire/Front/CheckoutPageNew.php` (DEĞİŞİKLİK)

**submitOrder() metodunun sonuna ekle:**

```php
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

public function submitOrder()
{
    // ... mevcut kod ...

    DB::commit();

    // ❗ Email gönder
    try {
        Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
    } catch (\Exception $e) {
        // Email gönderilemezse hata logla ama sipariş devam etsin
        \Log::error('Sipariş email gönderilemedi: ' . $e->getMessage());
    }

    // Sipariş onay sayfasına redirect
    return redirect()->route('shop.order.success', $order->order_number);
}
```

**Neden gerekli:** Sipariş sonrası otomatik email

---

### **PHASE 2: ÖDEME SİSTEMİ (Opsiyonel - Sonra)**

Bu dosyalar iyzico/PayTR entegrasyonu için gerekli, ileride eklenebilir:

- `app/Services/IyzicoPaymentService.php`
- `Modules/Shop/app/Http/Controllers/Front/PaymentController.php`
- `config/iyzico.php`
- `Modules/Shop/resources/views/front/payment-pending.blade.php`
- `Modules/Shop/resources/views/front/payment-success.blade.php`
- `Modules/Shop/resources/views/front/payment-failed.blade.php`

**Şimdilik atla** (manuel ödeme yeterli).

---

## 📊 DOSYA ÖZETİ

### **YENİ OLUŞTURULACAK DOSYALAR (11 adet)**

1. ✅ `Modules/Shop/app/Http/Controllers/Front/OrderController.php` - Controller
2. ✅ `Modules/Shop/app/Http/Livewire/Front/CreateAccountFromOrder.php` - Livewire
3. ✅ `Modules/Shop/resources/views/front/order-success.blade.php` - View
4. ✅ `Modules/Shop/resources/views/front/order-track.blade.php` - View
5. ✅ `Modules/Shop/resources/views/front/order-detail.blade.php` - View
6. ✅ `Modules/Shop/resources/views/livewire/front/create-account-from-order.blade.php` - Livewire View
7. ✅ `app/Mail/OrderConfirmationMail.php` - Mail
8. ✅ `resources/views/emails/orders/confirmation.blade.php` - Email Template

### **DEĞİŞTİRİLECEK DOSYALAR (3 adet)**

9. ⚠️ `Modules/Shop/app/Http/Livewire/Front/CheckoutPageNew.php` - Guest adres formu + Email gönderimi
10. ⚠️ `Modules/Shop/resources/views/livewire/front/checkout-page-new.blade.php` - Inline adres formu UI
11. ⚠️ `routes/web.php` - Yeni route'lar ekle

---

## 🔍 KONTROL EDİLECEK DOSYALAR (1 adet)

12. 🔍 `Modules/Shop/app/Models/ShopOrder.php` - Status accessor'lar var mı?

---

## ⏱️ TAHMİNİ SÜRE

| İşlem | Süre |
|-------|------|
| Controller oluştur (OrderController) | 15dk |
| Livewire Component (CreateAccountFromOrder) | 20dk |
| Views (order-success, order-track, order-detail) | 45dk |
| CheckoutPageNew güncelle (inline adres formu) | 30dk |
| Email template oluştur | 20dk |
| Route'ları ekle | 5dk |
| Test et | 30dk |
| **TOPLAM** | **~3 saat** |

---

## 🎯 SIRALAMA (Hangi Dosyadan Başlayalım?)

### **1. ÖNCE BACKEND (Controller + Livewire)**
1. `OrderController.php` oluştur
2. `CreateAccountFromOrder.php` oluştur
3. `OrderConfirmationMail.php` oluştur

### **2. SONRA VIEWS**
4. `order-success.blade.php` oluştur
5. `order-track.blade.php` oluştur
6. `order-detail.blade.php` oluştur
7. `create-account-from-order.blade.php` oluştur
8. `confirmation.blade.php` (email) oluştur

### **3. EN SON MEVCUT DOSYALARI GÜNCELLE**
9. `CheckoutPageNew.php` güncelle (inline adres formu)
10. `checkout-page-new.blade.php` güncelle (UI)
11. `routes/web.php` güncelle

---

## ❓ SONRAKİ ADIM

**Soru:** Bu dosyaları şimdi oluşturmaya başlayalım mı?

**Seçenekler:**
- **A)** Evet, hemen başla (tek tek oluştur + permission düzelt)
- **B)** Önce banka bilgilerini/SMTP ayarlarını kontrol edelim
- **C)** Önce mevcut checkout sayfasını test edelim (çalışıyor mu?)

Hangi seçeneği tercih edersin?
