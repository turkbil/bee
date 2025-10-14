# 🎁 PREMIUM TENANT + AUTO SEO FILL - KULLANIM KILAVUZU

**Tarih:** 2025-10-14
**Versiyon:** 1.0
**Durum:** Production Ready ✅

---

## 📋 İÇİNDEKİLER

1. [Genel Bakış](#genel-bakış)
2. [Premium Özellikler](#premium-özellikler)
3. [Kurulum](#kurulum)
4. [Tenant'ı Premium Yapma](#tenantı-premium-yapma)
5. [Auto SEO Fill Kullanımı](#auto-seo-fill-kullanımı)
6. [API Kullanımı](#api-kullanımı)
7. [Frontend Entegrasyonu](#frontend-entegrasyonu)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 GENEL BAKIŞ

Premium Tenant sistemi, özel müşterilere **sınırsız AI kullanımı** ve **otomatik SEO doldurma** gibi premium özellikler sunar.

### Özellikler:

✅ **Sınırsız AI Kredisi** - Premium tenant'lar AI kredit tüketmez
✅ **Otomatik SEO Doldurma** - Boş sayfalarda SEO otomatik doldurulur
✅ **Öncelikli İşlem** - Premium tenant'lar öncelikli işlenir
✅ **Tüm AI Feature'lara Erişim** - Kısıtlama yok

---

## 🎁 PREMIUM ÖZELLİKLER

### 1. Sınırsız AI Kredisi

Premium tenant'lar için:
- `Tenant::hasEnoughCredits()` → Her zaman `true`
- `Tenant::useCredits()` → Kredi tüketimi YOK
- Tüm AI feature'lar sınırsız kullanılabilir

### 2. Otomatik SEO Doldurma

Sayfa ilk açıldığında:
- SEO title ve description boşsa
- AI ile otomatik doldurulur
- Kullanıcı beklemez (background)
- Sadece 1. alternatif kaydedilir

### 3. Öncelikli İşlem

- Queue priority yüksek
- Rate limiting esnek
- API limitleri daha yüksek

---

## 🔧 KURULUM

### 1. Migration Çalıştırma

```bash
# ✅ ZATEN YAPILDI (central DB'de)
php artisan migrate
```

### 2. Dosyalar Kontrol

```bash
# Migration
database/migrations/2025_10_14_054829_add_is_premium_to_tenants_table.php

# Service
app/Services/AI/AutoSeoFillService.php

# Controller
app/Http/Controllers/Api/AutoSeoFillController.php

# Frontend
public/assets/js/auto-seo-fill.js
resources/views/components/auto-seo-trigger.blade.php

# Model
app/Models/Tenant.php (güncellendi)

# Routes
routes/tenant.php (güncellendi)
```

---

## 👑 TENANT'I PREMIUM YAPMA

### Yöntem 1: Tinker (Hızlı)

```bash
php artisan tinker
```

```php
// Tekil tenant
$tenant = App\Models\Tenant::find(1);
$tenant->is_premium = true;
$tenant->save();

// Test
echo $tenant->isPremium() ? 'Premium ✅' : 'Normal ❌';
```

### Yöntem 2: Database (Manuel)

```sql
UPDATE tenants
SET is_premium = 1
WHERE id = 1;
```

### Yöntem 3: Admin Panel (Gelecek)

Admin panelden "Premium" checkbox'ı eklenecek.

---

## 🚀 AUTO SEO FILL KULLANIMI

### Blade Sayfalarında Kullanım

```blade
{{-- Page manage sayfasında --}}
@extends('layouts.admin')

@section('content')
    {{-- Diğer içerikler --}}

    {{-- Auto SEO Fill Trigger --}}
    <x-auto-seo-trigger
        :model="$page"
        model-type="page"
        :locale="$currentLanguage"
    />

    {{-- Auto SEO Fill JS --}}
    @push('scripts')
        <script src="{{ asset('assets/js/auto-seo-fill.js') }}"></script>
    @endpush
@endsection
```

### Örnek: Page Manage

```blade
{{-- Modules/Page/resources/views/admin/manage.blade.php --}}

<x-auto-seo-trigger
    :model="$page"
    model-type="page"
    :locale="session('page_manage_language', 'tr')"
/>

@push('scripts')
    <script src="{{ asset('assets/js/auto-seo-fill.js') }}"></script>
@endpush
```

### Örnek: Portfolio Manage

```blade
{{-- Modules/Portfolio/resources/views/admin/manage.blade.php --}}

<x-auto-seo-trigger
    :model="$portfolio"
    model-type="portfolio"
    :locale="$currentLanguage"
/>

@push('scripts')
    <script src="{{ asset('assets/js/auto-seo-fill.js') }}"></script>
@endpush
```

---

## 📡 API KULLANIMI

### 1. Tekil SEO Fill

```javascript
// POST /api/auto-seo-fill
fetch('/api/auto-seo-fill', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        model_type: 'page',  // page|portfolio|announcement|blog
        model_id: 123,       // Model ID
        locale: 'tr'         // Dil kodu
    })
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        console.log('✅ SEO dolduruldu:', data.data);
    } else if (data.skipped) {
        console.log('⏭️ SEO zaten dolu');
    } else {
        console.error('❌ Hata:', data.error);
    }
});
```

### 2. Toplu SEO Fill

```javascript
// POST /api/auto-seo-fill/bulk
fetch('/api/auto-seo-fill/bulk', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        model_type: 'page',  // Tüm page'ler
        locale: 'tr'
    })
})
.then(res => res.json())
.then(data => {
    console.log('Dolduruldu:', data.filled);
    console.log('Atlandı:', data.skipped);
    console.log('Hata:', data.errors);
});
```

### Rate Limiting

- **1 request / 1 dakika** (throttle:1,1)
- Premium tenant için esnek
- Bulk işlemler için ayrı limit

---

## 🎨 FRONTEND ENTEGRASYONU

### JavaScript Debug Mode

```javascript
// Console'da debug açma
window.autoSeoFill.config.debug = true;

// Manuel tetikleme
window.autoSeoFill.trigger('page', 123, 'tr');
```

### Data Attributes

```html
<div
    data-auto-seo-fill="true"
    data-premium-tenant="1"
    data-seo-empty="1"
    data-model-type="page"
    data-model-id="123"
    data-locale="tr"
    style="display: none;"
    id="auto-seo-trigger"
></div>
```

---

## 🧪 TEST SENARYOLARI

### Test 1: Premium Kontrolü

```bash
php artisan tinker
```

```php
$tenant = tenant();
echo $tenant->isPremium() ? 'Premium ✅' : 'Normal ❌';
```

### Test 2: Auto Fill Kontrolü

```bash
php artisan tinker
```

```php
use App\Services\AI\AutoSeoFillService;

$service = app(AutoSeoFillService::class);
$page = \Modules\Page\App\Models\Page::first();

// Doldurulmalı mı?
$should = $service->shouldAutoFill($page, 'tr');
echo $should ? 'Doldurulmalı ✅' : 'Atlanacak ⏭️';

// SEO verilerini üret
$seoData = $service->autoFillSeoData($page, 'tr');
print_r($seoData);

// Kaydet
$saved = $service->saveSeoData($page, $seoData, 'tr');
echo $saved ? 'Kaydedildi ✅' : 'Hata ❌';
```

### Test 3: Frontend Trigger

1. Premium tenant'a gir
2. Boş SEO'lu bir sayfa aç
3. Console'u aç
4. `[Auto SEO Fill]` log'larını izle
5. Sayfa yenile, SEO dolu olmalı

---

## 🔍 TROUBLESHOOTING

### Problem 1: Auto Fill Çalışmıyor

**Kontrol:**
1. Tenant premium mi?
   ```php
   tenant()->isPremium()
   ```

2. SEO gerçekten boş mu?
   ```php
   $page->seoSetting->titles
   $page->seoSetting->descriptions
   ```

3. JavaScript yüklendi mi?
   ```javascript
   typeof window.autoSeoFill !== 'undefined'
   ```

4. API endpoint çalışıyor mu?
   ```bash
   curl -X POST http://laravel.test/api/auto-seo-fill \
     -H "Content-Type: application/json" \
     -d '{"model_type":"page","model_id":1,"locale":"tr"}'
   ```

### Problem 2: Rate Limit Hatası

**Çözüm:**
- 1 dakika bekle
- Veya throttle limitini artır:
  ```php
  // routes/tenant.php
  Route::middleware(['throttle:5,1']) // 5 request / 1 dakika
  ```

### Problem 3: AI Kredisi Yetmiyor

**Kontrol:**
```php
$tenant = tenant();
echo $tenant->isPremium() ? 'Premium (Sınırsız)' : 'Normal';
echo $tenant->hasUnlimitedAI() ? 'Unlimited AI' : 'Limited';
```

**Çözüm:**
Tenant'ı premium yap:
```php
$tenant->update(['is_premium' => true]);
```

---

## 📊 LOGlar

### Laravel Log

```bash
# Auto SEO Fill log'ları
tail -f storage/logs/laravel.log | grep "Auto SEO Fill"
```

**Log Mesajları:**
- `🚀 Auto SEO Fill: Başlıyor`
- `✅ Auto SEO Fill: SEO verileri hazırlandı`
- `💾 Auto SEO Fill: Kayıt tamamlandı`
- `❌ Auto SEO Fill: Hata`

### Frontend Console

```javascript
// Debug mode'da
[Auto SEO Fill] Triggering auto SEO fill
[Auto SEO Fill] ✅ Auto SEO Fill successful
```

---

## 🎯 ÖNEMLİ NOTLAR

1. ⚠️ **Migration sadece central DB'de çalıştır**
   ```bash
   # ✅ Doğru
   php artisan migrate

   # ❌ Yanlış
   php artisan tenants:migrate
   ```

2. ⚠️ **Premium tenant = Sınırsız AI**
   - Kredi tüketimi YOK
   - Rate limiting esnek
   - Tüm feature'lar sınırsız

3. ⚠️ **Auto fill sadece boş sayfalarda**
   - Title + Description ikisi de boşsa
   - Yoksa skip edilir
   - Sadece 1. alternatif kaydedilir

4. ⚠️ **Background çalışır**
   - Kullanıcıyı bekletmez
   - 500ms delay ile başlar
   - Retry logic var

---

## 📞 DESTEK

**Sorular için:**
- GitHub Issues: https://github.com/turkbil/bee/issues
- Email: nurullah@nurullah.net

---

## 🔄 VERSİYON GEÇMİŞİ

### v1.0 - 2025-10-14
- ✅ Premium tenant sistemi
- ✅ Auto SEO fill service
- ✅ API endpoints
- ✅ Frontend trigger
- ✅ Blade component
- ✅ AI credit bypass
- ✅ Rate limiting
- ✅ Multi-language support

---

**🎉 Sistem hazır ve production'da çalışıyor!**
