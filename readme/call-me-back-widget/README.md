# 📞 Sizi Arayalım Widget - Kullanım Kılavuzu

## 🎯 Genel Bakış

**CallMeBackForm** Livewire component'i, kullanıcıların iletişim bilgilerini göndererek geri arama talebinde bulunmalarını sağlayan modern bir formdur.

### ✨ Özellikler

- ✅ **Multi-channel Notifications**: Telegram + WhatsApp + Email entegrasyonu
- ✅ **Preferred Time Selection**: Kullanıcı tercih edilen zaman dilimini seçebilir
- ✅ **Modern UI/UX**: Tailwind CSS + Alpine.js + Font Awesome
- ✅ **Dark Mode**: Tam dark mode desteği
- ✅ **Real-time Validation**: Livewire ile anlık form validasyonu
- ✅ **Success/Error Modals**: Kullanıcı dostu geri bildirim
- ✅ **KVKK Compliance**: Kişisel veri işleme onayı

---

## 📦 Dosyalar

### Backend (Livewire Component)
```
/app/Livewire/Page/CallMeBackForm.php
```

### Frontend (View)
```
/resources/views/livewire/page/call-me-back-form.blade.php
```

---

## 🚀 Kullanım

### 1. Blade Dosyasında (Page Modülü)

```blade
{{-- Örnek: Page show.blade.php --}}
<section class="py-12 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                📞 Sizi Arayalım
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-center mb-8">
                Formu doldurun, sizi en kısa sürede arayalım!
            </p>

            @livewire('page.call-me-back-form')
        </div>
    </div>
</section>
```

### 2. Anasayfa Widget Olarak

```blade
{{-- Örnek: homepage.blade.php --}}
<section class="py-20 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            {{-- Sol: Bilgi Kartı --}}
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Size Yardımcı Olalım!
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">
                        Uzman ekibimiz sizinle iletişime geçsin, tüm sorularınızı yanıtlayalım.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-check-circle text-green-600"></i>
                            <span>Hızlı ve profesyonel hizmet</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-check-circle text-green-600"></i>
                            <span>Ücretsiz danışmanlık</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-check-circle text-green-600"></i>
                            <span>24/7 destek</span>
                        </li>
                    </ul>
                </div>

                {{-- Sağ: Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8">
                    @livewire('page.call-me-back-form')
                </div>
            </div>
        </div>
    </div>
</section>
```

### 3. Modal İçinde Kullanım

```blade
{{-- Örnek: Modal tetikleyici button --}}
<button @click="$refs.callMeModal.classList.remove('hidden')"
    class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700">
    <i class="fa-solid fa-phone mr-2"></i>Sizi Arayalım
</button>

{{-- Modal --}}
<div x-ref="callMeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-2xl w-full p-8 relative"
         @click.away="$refs.callMeModal.classList.add('hidden')">

        {{-- Close Button --}}
        <button @click="$refs.callMeModal.classList.add('hidden')"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-times text-2xl"></i>
        </button>

        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 text-center">
            📞 Sizi Arayalım
        </h2>

        @livewire('page.call-me-back-form')
    </div>
</div>
```

---

## 📋 Form Alanları

| Alan | Tip | Zorunlu | Açıklama |
|------|-----|---------|----------|
| **name** | Text | ✅ Evet | Ad Soyad |
| **phone** | Tel | ✅ Evet | Telefon numarası |
| **email** | Email | ✅ Evet | E-posta adresi |
| **company** | Text | ❌ Hayır | Şirket adı (opsiyonel) |
| **message** | Textarea | ❌ Hayır | Kullanıcı mesajı (opsiyonel) |
| **preferred_time** | Radio | ✅ Evet | Tercih edilen zaman (anytime/morning/afternoon/evening) |
| **terms_accepted** | Checkbox | ✅ Evet | KVKK onayı |

---

## 🔔 Notification Sistemi

Form gönderildiğinde **3 kanal** üzerinden bildirim gönderilir:

### 1. Telegram
- ✅ Müşteri bilgileri (ad, telefon, email, şirket)
- ✅ Tercih edilen zaman dilimi
- ✅ Kullanıcı mesajı
- ✅ Sayfa URL'i, cihaz bilgisi

**Ayarlar**: `/admin/settingmanagement/settings` → Notifications → Telegram

### 2. WhatsApp (Twilio)
- ✅ Aynı format ile WhatsApp mesajı
- ✅ Twilio üzerinden gönderim

**Ayarlar**: `/admin/settingmanagement/settings` → Notifications → WhatsApp

### 3. Email
- ⏳ Şu anda placeholder (geliştirilebilir)
- ✅ Altyapı hazır (NotificationHub)

---

## ⚙️ Backend Yapısı

### NotificationHub Servisi

```php
use App\Services\NotificationHub;

$notificationHub = new NotificationHub();

$results = $notificationHub->sendCustomerLead(
    [
        'name' => 'Ali Veli',
        'phone' => '0532 123 45 67',
        'email' => 'ali@example.com',
        'company' => 'Örnek A.Ş.',
    ],
    '📞 Sizi Arayalım Talebi\n\nTercih Edilen Zaman: Sabah (09:00-12:00)',
    [], // Suggested products (boş)
    [
        'site' => 'ixtif.com',
        'page_url' => url()->current(),
        'device' => request()->userAgent(),
        'form_type' => 'Sizi Arayalım',
    ]
);

// $results = [
//     'telegram' => true,
//     'whatsapp' => true,
//     'email' => false,
//     'sent_count' => 2
// ]
```

### Preferred Time Options

```php
'anytime' => 'Farketmez',
'morning' => 'Sabah (09:00-12:00)',
'afternoon' => 'Öğleden Sonra (12:00-17:00)',
'evening' => 'Akşam (17:00-20:00)',
```

---

## 🎨 UI/UX Özellikleri

### Form Elementleri

- **Icon'lu Label'lar**: Her alan FontAwesome icon ile
- **Radio Buttons (Preferred Time)**: 4 şık kart tasarımı (Farketmez, Sabah, Öğleden Sonra, Akşam)
- **KVKK Checkbox**: Gradient background + KVKK link
- **Submit Button**: Gradient (blue → purple → pink) + loading state
- **Success/Error Modal**: Animasyonlu modal + gradient icon

### Responsive Design

- **Mobile**: Tek kolon
- **Tablet**: Telefon/Email yan yana
- **Desktop**: Tüm alanlar optimize

### Dark Mode

- Tam dark mode desteği
- Gradient'ler dark mode'da açık tonlar
- Border/background opacity ayarları

---

## 🔧 Geliştirme Önerileri

### 1. Email Notification Ekleme

```php
// NotificationHub.php içinde email gönderimi ekleyebilirsiniz
if ($this->emailEnabled) {
    Mail::to($this->settings['email'])
        ->send(new CallMeBackMail($data));
}
```

### 2. Admin Panel'de Talepler

```php
// Migration: call_me_back_requests tablosu
php artisan make:migration create_call_me_back_requests_table

// Model oluştur
php artisan make:model CallMeBackRequest

// Admin'de listeleme sayfası
```

### 3. SMS Notification

```php
// SMSNotificationService.php ekleyip NotificationHub'a entegre et
```

---

## 🧪 Test

### Manuel Test

1. Sayfayı aç: `/page/sizi-arayalim` (veya component'in bulunduğu sayfa)
2. Formu doldur
3. "Hemen Arayın" butonuna bas
4. Success modal'ı kontrol et
5. Telegram/WhatsApp'tan bildirimi kontrol et

### Validation Test

```php
// Boş form gönderme → Hata mesajları görülmeli
// Geçersiz email → Email formatı hatası
// KVKK checkbox işaretli değil → Checkbox hatası
```

---

## 📝 Log Kayıtları

### Success Log

```
[2025-10-31 10:30:15] local.INFO: Call Me Back Request Received
{
  "customer_name": "Ali Veli",
  "customer_phone": "0532 123 45 67",
  "customer_email": "ali@example.com",
  "preferred_time": "morning"
}
```

### Notification Log

```
[2025-10-31 10:30:16] local.INFO: Call Me Back Notifications Sent
{
  "telegram": true,
  "whatsapp": true,
  "email": false,
  "total_sent": 2
}
```

### Error Log

```
[2025-10-31 10:30:17] local.ERROR: Call Me Back Notification Failed
{
  "error": "Twilio credentials not configured"
}
```

---

## 🚨 Troubleshooting

### 1. Form gönderilmiyor

**Sorun**: Submit button çalışmıyor
**Çözüm**:
- Livewire script'lerin yüklü olduğunu kontrol et (`@livewireScripts`)
- Alpine.js yüklü mü kontrol et
- Console'da JavaScript hatası var mı bak

### 2. Telegram/WhatsApp bildirimi gitmiyor

**Sorun**: Notification gönderilmiyor
**Çözüm**:
- `/admin/settingmanagement/settings` → Telegram/WhatsApp ayarlarını kontrol et
- `enabled` checkbox'ı işaretli mi?
- Bot Token / Chat ID / Twilio credentials dolu mu?
- Log'larda hata var mı kontrol et: `storage/logs/laravel.log`

### 3. Permission hatası

**Sorun**: 500 Error - Permission denied
**Çözüm**:
```bash
# Owner/permission düzelt
sudo chown tuufi.com_:psaserv /var/www/vhosts/tuufi.com/httpdocs/app/Livewire/Page/CallMeBackForm.php
sudo chown tuufi.com_:psaserv /var/www/vhosts/tuufi.com/httpdocs/resources/views/livewire/page/call-me-back-form.blade.php
sudo chmod 644 /var/www/vhosts/tuufi.com/httpdocs/app/Livewire/Page/CallMeBackForm.php
sudo chmod 644 /var/www/vhosts/tuufi.com/httpdocs/resources/views/livewire/page/call-me-back-form.blade.php

# OPcache reset
curl -s -k https://ixtif.com/public/opcache-reset.php
```

---

## 🎁 Bonus: Sticky Widget (Sağ Alt Köşe)

```blade
{{-- Sticky "Sizi Arayalım" Button --}}
<div class="fixed bottom-6 right-6 z-40" x-data="{ showForm: false }">
    {{-- Trigger Button --}}
    <button @click="showForm = true"
        class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-4 rounded-full shadow-2xl hover:shadow-green-500/50 transition-all flex items-center gap-3 font-bold text-lg hover:scale-110 active:scale-95">
        <i class="fa-solid fa-phone-volume text-2xl animate-pulse"></i>
        <span class="hidden lg:block">Sizi Arayalım</span>
    </button>

    {{-- Popup Form --}}
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed inset-0 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm z-50">

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-2xl w-full p-8 relative"
             @click.away="showForm = false">

            <button @click="showForm = false"
                class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200">
                <i class="fa-solid fa-times text-lg"></i>
            </button>

            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                📞 Sizi Arayalım
            </h2>

            @livewire('page.call-me-back-form')
        </div>
    </div>
</div>
```

---

## 📞 Destek

Sorun yaşarsanız:
- Log dosyalarını kontrol edin: `storage/logs/laravel.log`
- NotificationHub ayarlarını gözden geçirin
- Bu dokümantasyondaki troubleshooting bölümüne bakın

---

**🚀 Başarılar!**

*Son güncelleme: 2025-10-31*
