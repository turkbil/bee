# 📞 Settings Dinamik Kullanım Kılavuzu

## ✅ EVET! Blade dosyalarında dinamik kullanabilirsin!

### 🎯 Kullanım Örnekleri

#### **Blade Dosyasında Kullanım:**

```blade
{{-- İletişim Sayfası Body --}}
<div class="contact-info">
    <h2>İletişim Bilgileri</h2>

    {{-- Telefon --}}
    <p>
        <i class="fa fa-phone"></i>
        <a href="tel:{{ settings('contact_phone_1') }}">
            {{ settings('contact_phone_1', '0216 755 3 555') }}
        </a>
    </p>

    {{-- Mobil --}}
    <p>
        <i class="fa fa-mobile"></i>
        <a href="tel:{{ settings('contact_phone_2') }}">
            {{ settings('contact_phone_2', '0501 005 67 58') }}
        </a>
    </p>

    {{-- WhatsApp --}}
    <p>
        <i class="fa-brands fa-whatsapp"></i>
        <a href="https://wa.me/{{ str_replace([' ', '0'], ['', '9'], settings('contact_whatsapp_1')) }}"
           target="_blank">
            {{ settings('contact_whatsapp_1', '0532 216 07 54') }}
        </a>
    </p>

    {{-- E-posta --}}
    <p>
        <i class="fa fa-envelope"></i>
        <a href="mailto:{{ settings('contact_email_1') }}">
            {{ settings('contact_email_1', 'info@ixtif.com') }}
        </a>
    </p>

    {{-- Site E-posta --}}
    <p>
        Site: {{ settings('site_email', 'info@ixtif.com') }}
    </p>
</div>
```

### 🔑 Mevcut Setting Keys

**İXTİF Tenant için Kullanılabilir Keys:**

```php
// Telefonlar
settings('contact_phone_1')     // 0216 755 3 555
settings('contact_phone_2')     // 0501 005 67 58
settings('contact_phone_3')     // Ekstra telefon (varsa)

// WhatsApp
settings('contact_whatsapp_1')  // 0532 216 07 54
settings('contact_whatsapp_2')  // Ekstra WhatsApp (varsa)
settings('contact_whatsapp_3')  // Ekstra WhatsApp (varsa)

// E-postalar
settings('site_email')          // info@ixtif.com
settings('contact_email_1')     // info@ixtif.com
settings('contact_email_2')     // Ekstra e-posta (varsa)
settings('contact_email_3')     // Ekstra e-posta (varsa)
settings('notification_email')  // Bildirim e-postası
```

### 📱 WhatsApp Link Helper

**WhatsApp numarasını wa.me formatına çevirme:**

```blade
{{-- Türkiye formatından uluslararası formata --}}
@php
    $whatsapp = settings('contact_whatsapp_1', '0532 216 07 54');
    // "0532 216 07 54" → "905322160754"
    $waLink = '9' . str_replace([' ', '0'], '', $whatsapp);
@endphp

<a href="https://wa.me/{{ $waLink }}" target="_blank">
    <i class="fa-brands fa-whatsapp"></i> WhatsApp ile İletişim
</a>

{{-- Departman özel mesaj --}}
<a href="https://wa.me/{{ $waLink }}?text=Satış" target="_blank">
    Satış Departmanı
</a>
```

### 🎨 Component Örneği

**resources/views/components/contact-buttons.blade.php:**

```blade
<div class="contact-buttons flex gap-4">
    {{-- Telefon Butonu --}}
    <a href="tel:{{ settings('contact_phone_1') }}"
       class="btn btn-primary">
        <i class="fa fa-phone"></i>
        Ara: {{ settings('contact_phone_1') }}
    </a>

    {{-- WhatsApp Butonu --}}
    @php
        $whatsapp = settings('contact_whatsapp_1', '0532 216 07 54');
        $waLink = '9' . str_replace([' ', '0'], '', $whatsapp);
    @endphp
    <a href="https://wa.me/{{ $waLink }}"
       target="_blank"
       class="btn btn-success">
        <i class="fa-brands fa-whatsapp"></i>
        WhatsApp: {{ $whatsapp }}
    </a>

    {{-- E-posta Butonu --}}
    <a href="mailto:{{ settings('contact_email_1') }}"
       class="btn btn-secondary">
        <i class="fa fa-envelope"></i>
        {{ settings('contact_email_1') }}
    </a>
</div>
```

**Kullanımı:**

```blade
{{-- Herhangi bir sayfada --}}
<x-contact-buttons />
```

### 🏢 İletişim Sayfası Full Örnek

```blade
@extends('layouts.app')

@section('content')
<div class="container py-12">
    <h1 class="text-4xl font-bold mb-8">İletişim</h1>

    <div class="grid md:grid-cols-2 gap-8">
        {{-- İletişim Bilgileri --}}
        <div class="space-y-6">
            <h2 class="text-2xl font-semibold mb-4">İletişim Bilgileri</h2>

            {{-- Telefon (Sabit) --}}
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fa fa-phone text-blue-600"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Telefon (Sabit)</div>
                    <a href="tel:{{ settings('contact_phone_1') }}"
                       class="text-lg font-semibold text-blue-600 hover:underline">
                        {{ settings('contact_phone_1', '0216 755 3 555') }}
                    </a>
                </div>
            </div>

            {{-- Telefon (Mobil) --}}
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fa fa-mobile text-purple-600"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Telefon (Mobil)</div>
                    <a href="tel:{{ settings('contact_phone_2') }}"
                       class="text-lg font-semibold text-purple-600 hover:underline">
                        {{ settings('contact_phone_2', '0501 005 67 58') }}
                    </a>
                </div>
            </div>

            {{-- WhatsApp --}}
            @php
                $whatsapp = settings('contact_whatsapp_1', '0532 216 07 54');
                $waLink = '9' . str_replace([' ', '0'], '', $whatsapp);
            @endphp
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fa-brands fa-whatsapp text-green-600"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">WhatsApp (7/24)</div>
                    <a href="https://wa.me/{{ $waLink }}"
                       target="_blank"
                       class="text-lg font-semibold text-green-600 hover:underline">
                        {{ $whatsapp }}
                    </a>
                </div>
            </div>

            {{-- E-posta --}}
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fa fa-envelope text-red-600"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">E-posta</div>
                    <a href="mailto:{{ settings('contact_email_1') }}"
                       class="text-lg font-semibold text-red-600 hover:underline">
                        {{ settings('contact_email_1', 'info@ixtif.com') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- İletişim Formu --}}
        <div>
            <h2 class="text-2xl font-semibold mb-4">Mesaj Gönderin</h2>
            <form action="/iletisim" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="name" placeholder="Ad Soyad" class="w-full px-4 py-2 border rounded" required>
                <input type="email" name="email" placeholder="E-posta" class="w-full px-4 py-2 border rounded" required>
                <input type="tel" name="phone" placeholder="Telefon" class="w-full px-4 py-2 border rounded">
                <select name="subject" class="w-full px-4 py-2 border rounded" required>
                    <option value="">Konu Seçin</option>
                    <option value="sales">Satış</option>
                    <option value="rental">Kiralama</option>
                    <option value="service">Servis</option>
                    <option value="parts">Yedek Parça</option>
                    <option value="other">Diğer</option>
                </select>
                <textarea name="message" rows="5" placeholder="Mesajınız" class="w-full px-4 py-2 border rounded" required></textarea>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700">
                    Gönder
                </button>
            </form>
        </div>
    </div>

    {{-- WhatsApp Hızlı Butonlar --}}
    <div class="mt-12 grid md:grid-cols-4 gap-4">
        <a href="https://wa.me/{{ $waLink }}?text=Satış"
           target="_blank"
           class="bg-green-500 text-white p-4 rounded-lg text-center hover:bg-green-600">
            <i class="fa-brands fa-whatsapp text-3xl mb-2"></i>
            <div class="font-semibold">Satış</div>
        </a>
        <a href="https://wa.me/{{ $waLink }}?text=Kiralama"
           target="_blank"
           class="bg-green-500 text-white p-4 rounded-lg text-center hover:bg-green-600">
            <i class="fa-brands fa-whatsapp text-3xl mb-2"></i>
            <div class="font-semibold">Kiralama</div>
        </a>
        <a href="https://wa.me/{{ $waLink }}?text=Servis"
           target="_blank"
           class="bg-green-500 text-white p-4 rounded-lg text-center hover:bg-green-600">
            <i class="fa-brands fa-whatsapp text-3xl mb-2"></i>
            <div class="font-semibold">Servis</div>
        </a>
        <a href="https://wa.me/{{ $waLink }}?text=YedekParca"
           target="_blank"
           class="bg-green-500 text-white p-4 rounded-lg text-center hover:bg-green-600">
            <i class="fa-brands fa-whatsapp text-3xl mb-2"></i>
            <div class="font-semibold">Yedek Parça</div>
        </a>
    </div>
</div>
@endsection
```

### 💡 Pro Tips

**1. Default Value Her Zaman Ver:**
```blade
{{-- ❌ Kötü --}}
{{ settings('contact_phone_1') }}

{{-- ✅ İyi --}}
{{ settings('contact_phone_1', '0216 755 3 555') }}
```

**2. Cache Kontrolü:**
```php
// Settings cache'lenir (24 saat)
// Değişiklik sonrası cache temizle:
php artisan cache:clear
```

**3. Blade Directive Oluştur (İsteğe Bağlı):**

**app/Providers/AppServiceProvider.php:**
```php
use Illuminate\Support\Facades\Blade;

public function boot()
{
    Blade::directive('phone', function ($key) {
        return "<?php echo settings($key, ''); ?>";
    });

    Blade::directive('whatsapp', function ($key) {
        return "<?php
            \$wa = settings($key, '');
            \$waLink = '9' . str_replace([' ', '0'], '', \$wa);
            echo 'https://wa.me/' . \$waLink;
        ?>";
    });
}
```

**Kullanım:**
```blade
<a href="tel:@phone('contact_phone_1')">Ara</a>
<a href="@whatsapp('contact_whatsapp_1')">WhatsApp</a>
```

---

## 🎯 Özet

**EVET**, Pages tablosunun `body` kolonuna şunları yazabilirsin:

```blade
Telefonumuz: {{ settings('contact_phone_1', '0216 755 3 555') }}
WhatsApp: {{ settings('contact_whatsapp_1', '0532 216 07 54') }}
E-posta: {{ settings('contact_email_1', 'info@ixtif.com') }}
```

**Dinamik olarak** veritabanından çekilir ve gösterilir! 🚀

**Not:** Page body'de Blade directive çalışır, çünkü Laravel Pages modülü render ederken Blade compile eder.
