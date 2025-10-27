# 🎯 Manuel AI Kredi Ekleme Kılavuzu

**Tarih:** 27 Ekim 2025
**Sistem:** Tuufi Multi-Tenant AI Credit System
**Sorun:** Tenant'a manuel kredi ekleme işlemi

---

## 📋 İÇİNDEKİLER

1. [Sistem Mimarisi](#sistem-mimarisi)
2. [Sorun Analizi](#sorun-analizi)
3. [Manuel Kredi Ekleme Adımları](#manuel-kredi-ekleme-adımları)
4. [Otomatik Kredi Ekleme (Önerilen)](#otomatik-kredi-ekleme-önerilen)
5. [Sorun Giderme](#sorun-giderme)
6. [Gelecek Geliştirmeler](#gelecek-geliştirmeler)

---

## 🏗️ SİSTEM MİMARİSİ

### Kredi Saklama Yapısı

Sistemde AI kredileri **3 farklı yerde** tutulmaktadır:

```
┌─────────────────────────────────────────────────────────┐
│  AI KREDİ SİSTEMİ - ÜÇ KATMANLI YAPI                   │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  1️⃣  ai_credit_purchases Tablosu                       │
│     ├─ Satın alma kayıtları (purchase history)         │
│     ├─ Her satın alma ayrı bir kayıt                   │
│     └─ Status: 'completed' olanlar geçerli             │
│                                                         │
│  2️⃣  tenants.ai_credits_balance Kolonu (INTEGER)       │
│     ├─ Tenant'ın gerçek kullanılabilir kredisi         │
│     ├─ Direkt veritabanı kolonu                        │
│     └─ ai_get_credit_balance() bu kolonu okur          │
│                                                         │
│  3️⃣  tenants.data JSON (Virtual Column - Stancl)       │
│     ├─ Stancl Tenancy Virtual Column sistemi           │
│     ├─ Eloquent Model bu değeri öncelikli kullanır     │
│     └─ CRITICAL: Bu güncellenmezse kredi görünmez!     │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Neden 3 Yer?

1. **`ai_credit_purchases`**: Satın alma geçmişi, raporlama, fatura
2. **`tenants.ai_credits_balance`**: Hızlı balance query için
3. **`tenants.data` JSON**: Stancl Tenancy paketi extra kolonları JSON'da saklar (Virtual Column sistemi)

---

## 🐛 SORUN ANALİZİ

### Karşılaşılan Sorun

**Kullanıcı bildirimi:**
> "💳 AI Kredi Düşük seviye - Mevcut: 8.00 kredi"

**Beklenen:** 100,300 kredi
**Görünen:** 8.00 kredi

### Kök Sebep

Manuel kredi ekleme yapılırken **sadece 2 yer güncellendi:**

```sql
-- ✅ YAPILAN:
INSERT INTO ai_credit_purchases (tenant_id, credit_amount, ...)
VALUES (2, 100000, 'completed', ...);

UPDATE tenants SET ai_credits_balance = ai_credits_balance + 100000
WHERE id = 2;

-- ❌ YAPILMAYAN (KRİTİK!):
UPDATE tenants SET data = JSON_SET(data, '$.ai_credits_balance', [YENİ_DEĞER])
WHERE id = 2;
```

### Neden `data` JSON Güncellemesi Kritik?

**Stancl Tenancy Virtual Column Trait'i** Eloquent Model'de `data` JSON içindeki değeri **override** ediyor:

```php
// Stancl\VirtualColumn\VirtualColumn trait'i
// tenants.data JSON içindeki değerler -> Model attribute'lara map ediliyor

// Sonuç:
$tenant->ai_credits_balance
// ↓ İlk olarak data JSON'a bakıyor
// ↓ Orada bulamazsa veya farklıysa, JSON değerini kullanıyor
// ✅ Dolayısıyla JSON güncellemesi ZORUNLU!
```

---

## 📝 MANUEL KREDİ EKLEME ADIMLARI

### Senaryo: Tenant'a 100,000 Kredi Ekleme

**Tenant ID:** 2 (ixtif.com)
**Eklenecek Kredi:** 100,000

### Adım 1: Mevcut Durumu Kontrol Et

```bash
# Veritabanı kontrolü
mariadb -u kullanici -p'sifre' veritabani -e "
SELECT
    id,
    title,
    ai_credits_balance as kolon_kredisi,
    JSON_EXTRACT(data, '\$.ai_credits_balance') as json_kredisi
FROM tenants
WHERE id = 2;
"

# Purchases kontrolü
mariadb -u kullanici -p'sifre' veritabani -e "
SELECT
    tenant_id,
    SUM(credit_amount) as toplam_purchase,
    COUNT(*) as kayit_sayisi
FROM ai_credit_purchases
WHERE tenant_id = 2 AND status = 'completed'
GROUP BY tenant_id;
"
```

**Örnek Çıktı:**
```
id  title       kolon_kredisi  json_kredisi
2   ixtif.com   75000          8
```

### Adım 2: Purchase Kaydı Oluştur

```sql
INSERT INTO ai_credit_purchases (
    tenant_id,
    user_id,
    package_id,
    credit_amount,
    price_paid,
    amount,
    currency,
    status,
    payment_method,
    notes,
    purchased_at,
    created_at,
    updated_at
) VALUES (
    2,                              -- tenant_id
    NULL,                           -- user_id (admin grant)
    1,                              -- package_id (referans için)
    100000,                         -- credit_amount
    0.00,                           -- price_paid (ücretsiz)
    0.00,                           -- amount
    'TRY',                          -- currency
    'completed',                    -- status
    'admin_grant',                  -- payment_method
    'Manuel kredi ekleme - [SEBEP YAZIN]',  -- notes
    NOW(),                          -- purchased_at
    NOW(),                          -- created_at
    NOW()                           -- updated_at
);
```

### Adım 3: Tenant Kolonunu Güncelle

```sql
UPDATE tenants
SET ai_credits_balance = ai_credits_balance + 100000
WHERE id = 2;
```

### Adım 4: Data JSON Kolonunu Güncelle (KRİTİK!)

```sql
-- Önce yeni toplam değeri hesaplayın
SET @yeni_toplam = (
    SELECT ai_credits_balance + 100000
    FROM tenants
    WHERE id = 2
);

-- Sonra JSON'u güncelleyin
UPDATE tenants
SET data = JSON_SET(data, '$.ai_credits_balance', @yeni_toplam)
WHERE id = 2;
```

**VEYA** (tek komutla):

```sql
UPDATE tenants
SET data = JSON_SET(data, '$.ai_credits_balance', ai_credits_balance)
WHERE id = 2;
```

### Adım 5: Cache Temizle

```bash
php artisan cache:clear
php artisan tinker --execute="Cache::forget('tenant_credits_2');"
```

### Adım 6: Doğrulama

```bash
# Laravel Tinker ile kontrol
php artisan tinker --execute="
use Modules\AI\App\Services\AICreditService;
\$service = app(AICreditService::class);
echo '✅ Tenant 2 Bakiye: ' . format_credit(\$service->getTenantCredits(2));
"

# Veritabanı kontrolü
mariadb -u kullanici -p'sifre' veritabani -e "
SELECT
    'Purchases' as kaynak,
    SUM(credit_amount) as toplam
FROM ai_credit_purchases
WHERE tenant_id=2 AND status='completed'
UNION ALL
SELECT 'Tenant Kolon', ai_credits_balance
FROM tenants WHERE id=2
UNION ALL
SELECT 'Data JSON', JSON_EXTRACT(data, '\$.ai_credits_balance')
FROM tenants WHERE id=2;
"
```

**Başarılı Çıktı:**
```
✅ Tenant 2 Bakiye: 175.000,00 Kredi

kaynak          toplam
Purchases       100300
Tenant Kolon    175000
Data JSON       175000
```

---

## 🤖 OTOMATİK KREDİ EKLEME (ÖNERİLEN)

### Laravel Tinker ile Tek Komut

En güvenli ve önerilen yöntem:

```bash
php artisan tinker
```

```php
use App\Models\Tenant;
use Modules\AI\App\Models\AICreditPurchase;

// Tenant'ı bul
$tenant = Tenant::find(2);

// Eklenecek kredi miktarı
$creditAmount = 100000;

// 1. Purchase kaydı oluştur
$purchase = AICreditPurchase::create([
    'tenant_id' => $tenant->id,
    'user_id' => null,
    'package_id' => 1,
    'credit_amount' => $creditAmount,
    'price_paid' => 0.00,
    'amount' => 0.00,
    'currency' => 'TRY',
    'status' => 'completed',
    'payment_method' => 'admin_grant',
    'notes' => 'Manuel kredi ekleme - ' . now()->format('Y-m-d H:i'),
    'purchased_at' => now(),
]);

// 2. Tenant kolonunu ve JSON'u güncelle
$tenant->increment('ai_credits_balance', $creditAmount);
$tenant->ai_credits_balance = $tenant->ai_credits_balance; // Virtual column sync
$tenant->save();

// 3. Cache temizle
Cache::forget("tenant_credits_{$tenant->id}");

// 4. Doğrulama
echo "✅ Kredi Eklendi!\n";
echo "Purchase ID: {$purchase->id}\n";
echo "Yeni Bakiye: " . format_credit($tenant->fresh()->ai_credits_balance) . "\n";
```

### Artisan Command Oluşturma (Gelecek için)

**Dosya:** `app/Console/Commands/AddAICreditsCommand.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Modules\AI\App\Models\AICreditPurchase;
use Illuminate\Support\Facades\Cache;

class AddAICreditsCommand extends Command
{
    protected $signature = 'ai:add-credits
                            {tenant : Tenant ID}
                            {amount : Kredi miktarı}
                            {--reason= : Ekleme sebebi}';

    protected $description = 'Tenant\'a manuel AI kredi ekler';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $amount = (int) $this->argument('amount');
        $reason = $this->option('reason') ?? 'Manuel ekleme';

        // Tenant kontrolü
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("❌ Tenant bulunamadı: {$tenantId}");
            return 1;
        }

        $this->info("📦 Tenant: {$tenant->title}");
        $this->info("💰 Eklenecek: " . format_credit($amount));

        if (!$this->confirm('Devam edilsin mi?')) {
            $this->warn('İptal edildi.');
            return 0;
        }

        // 1. Purchase kaydı
        $purchase = AICreditPurchase::create([
            'tenant_id' => $tenant->id,
            'user_id' => null,
            'package_id' => 1,
            'credit_amount' => $amount,
            'price_paid' => 0.00,
            'amount' => 0.00,
            'currency' => 'TRY',
            'status' => 'completed',
            'payment_method' => 'admin_grant',
            'notes' => $reason,
            'purchased_at' => now(),
        ]);

        // 2. Tenant güncelleme
        $oldBalance = $tenant->ai_credits_balance;
        $tenant->increment('ai_credits_balance', $amount);
        $tenant->ai_credits_balance = $tenant->fresh()->ai_credits_balance;
        $tenant->save();

        // 3. Cache temizle
        Cache::forget("tenant_credits_{$tenant->id}");

        // Sonuç
        $this->info("✅ Başarılı!");
        $this->table(
            ['Özellik', 'Değer'],
            [
                ['Purchase ID', $purchase->id],
                ['Eski Bakiye', format_credit($oldBalance)],
                ['Eklenen', format_credit($amount)],
                ['Yeni Bakiye', format_credit($tenant->fresh()->ai_credits_balance)],
            ]
        );

        return 0;
    }
}
```

**Kullanım:**

```bash
# Tenant 2'ye 100,000 kredi ekle
php artisan ai:add-credits 2 100000 --reason="Promosyon kredisi"

# Tenant 3'e 50,000 kredi ekle
php artisan ai:add-credits 3 50000 --reason="Test kredisi"
```

---

## 🔧 SORUN GİDERME

### Problem 1: "Kredi Görünmüyor"

**Belirti:** Veritabanında kredi var ama panelde 0 görünüyor

**Çözüm:**

```bash
# 1. Cache temizle
php artisan cache:clear
php artisan tinker --execute="Cache::forget('tenant_credits_2');"

# 2. Data JSON kontrolü
mariadb -u kullanici -p'sifre' veritabani -e "
SELECT
    id,
    ai_credits_balance as kolon,
    JSON_EXTRACT(data, '\$.ai_credits_balance') as json_data
FROM tenants WHERE id = 2;
"

# 3. JSON farklıysa düzelt
mariadb -u kullanici -p'sifre' veritabani -e "
UPDATE tenants
SET data = JSON_SET(data, '\$.ai_credits_balance', ai_credits_balance)
WHERE id = 2;
"
```

### Problem 2: "Eloquent Farklı Değer Döndürüyor"

**Belirti:** SQL query 175,000 ama `$tenant->ai_credits_balance` 8 döndürüyor

**Sebep:** Stancl Virtual Column trait'i data JSON'u override ediyor

**Çözüm:** Yukarıdaki JSON güncelleme SQL'ini çalıştır

### Problem 3: "getTenantCredits() vs ai_get_credit_balance() Farkı"

**Fark:**

```php
// 1. getTenantCredits() - PURCHASES BAZLI
$balance = app(AICreditService::class)->getTenantCredits(2);
// Hesaplama: SUM(purchases.credit_amount) - SUM(usage.credits_used)

// 2. ai_get_credit_balance() - TENANT KOLONU BAZLI
$balance = ai_get_credit_balance(2);
// Direkt: tenants.ai_credits_balance kolonunu okur
```

**Hangi Kullanılmalı?**

- **Frontend/UI:** `ai_get_credit_balance()` (daha hızlı, tenant kolonundan direkt)
- **Raporlama:** `getTenantCredits()` (purchases geçmişine göre)

### Problem 4: "Cache Temizlenmiyor"

```bash
# Tüm cache'i temizle
php artisan cache:clear

# Redis kullanıyorsanız
redis-cli FLUSHALL

# Tenant-specific cache
php artisan tinker --execute="
Cache::forget('tenant_credits_2');
Cache::forget('user_credits_' . auth()->id());
Cache::forget('credit_breakdown_' . auth()->id());
"
```

---

## 🚀 GELECEK GELİŞTİRMELER

### 1. Otomatik Sync Sistemi

**Observer oluştur:** `app/Observers/TenantObserver.php`

```php
<?php

namespace App\Observers;

use App\Models\Tenant;

class TenantObserver
{
    public function saving(Tenant $tenant)
    {
        // ai_credits_balance değiştiğinde data JSON'u otomatik sync et
        if ($tenant->isDirty('ai_credits_balance')) {
            $data = $tenant->data ?? [];
            $data['ai_credits_balance'] = $tenant->ai_credits_balance;
            $tenant->data = $data;
        }
    }
}
```

**Register et:** `app/Providers/EventServiceProvider.php`

```php
use App\Models\Tenant;
use App\Observers\TenantObserver;

public function boot()
{
    Tenant::observe(TenantObserver::class);
}
```

### 2. Admin Panel UI

**Route:** `routes/admin.php`

```php
Route::post('/tenants/{tenant}/add-credits', [TenantController::class, 'addCredits'])
    ->middleware('role:root')
    ->name('tenants.add-credits');
```

**Controller:** `app/Http/Controllers/Admin/TenantController.php`

```php
public function addCredits(Request $request, Tenant $tenant)
{
    $request->validate([
        'amount' => 'required|integer|min:1',
        'reason' => 'required|string|max:500',
    ]);

    $amount = $request->amount;

    // Purchase kaydı
    $purchase = AICreditPurchase::create([
        'tenant_id' => $tenant->id,
        'user_id' => auth()->id(),
        'package_id' => 1,
        'credit_amount' => $amount,
        'price_paid' => 0.00,
        'amount' => 0.00,
        'currency' => 'TRY',
        'status' => 'completed',
        'payment_method' => 'admin_grant',
        'notes' => $request->reason,
        'purchased_at' => now(),
    ]);

    // Tenant güncelleme
    $tenant->increment('ai_credits_balance', $amount);
    $tenant->ai_credits_balance = $tenant->fresh()->ai_credits_balance;
    $tenant->save();

    // Cache temizle
    Cache::forget("tenant_credits_{$tenant->id}");

    return redirect()->back()->with('success',
        format_credit($amount) . ' kredi başarıyla eklendi!'
    );
}
```

### 3. Webhook/API Endpoint

Ödeme gateway'lerinden otomatik kredi ekleme:

```php
// routes/api.php
Route::post('/webhooks/payment/success', [PaymentWebhookController::class, 'success'])
    ->middleware('verify-webhook-signature');

// Controller
public function success(Request $request)
{
    $tenantId = $request->tenant_id;
    $amount = $request->credit_amount;

    // Kredi ekleme işlemi...

    return response()->json(['status' => 'success']);
}
```

---

## 📚 İLGİLİ DOSYALAR

### Core Files

- **Helper:** `/app/Helpers/CreditHelpers.php` (satır 203-254: `ai_get_credit_balance()`)
- **Service:** `/Modules/AI/app/Services/AICreditService.php` (satır 276-301: `getTenantCredits()`)
- **Model:** `/app/Models/Tenant.php` (satır 32: Virtual Column cast)
- **Migration:** `/Modules/AI/database/migrations/2025_07_01_000002_create_ai_credit_purchases_table.php`

### Routes

- **Admin Routes:** `/Modules/AI/routes/admin.php` (satır 465-468: `/admin/ai/credits/purchases`)
- **Credit Stats API:** `/Modules/AI/routes/admin.php` (satır 270-298: `/admin/ai/credit-stats`)

### Views

- **Purchases List:** `/Modules/AI/resources/views/admin/credits/purchases.blade.php` (demo veri - güncellenmeli!)

---

## ⚠️ ÖNEMLİ NOTLAR

### 1. Stancl Tenancy Virtual Column Sistemi

Stancl Tenancy paketi, `tenants` tablosuna eklenen **custom kolonları** otomatik olarak `data` JSON field'ında da saklar. Bu sistem:

- **장점:** Esnek, migration'sız yeni alan ekleme
- **Dezavantaj:** JSON ve kolon senkronizasyonu manuel yapılmalı

**Trait:** `Stancl\VirtualColumn\VirtualColumn`

### 2. Data JSON Yapısı

```json
{
  "is_premium": 1,
  "fullname": "Ahmet Kırmızı",
  "email": "ahmet@kirmizi.test",
  "phone": "+90 532 111 11 11",
  "ai_credits_balance": 175000,  // ← KRİTİK!
  "ai_last_used_at": "2025-10-15 01:57:05",
  "tenant_ai_provider_id": null,
  "tenant_ai_provider_model_id": null,
  "created_at": "2025-10-13 21:13:09",
  "updated_at": "2025-10-13 21:13:09"
}
```

### 3. Premium Tenant Kontrolü

Premium tenant'lar **sınırsız kredi**ye sahiptir:

```php
// Tenant.php - satır 231-239
public function hasEnoughCredits(float $creditsNeeded): bool
{
    if ($this->isPremium()) {
        return true; // Sınırsız!
    }
    return $this->ai_credits_balance >= $creditsNeeded;
}
```

Tenant 2 (ixtif.com) **is_premium = 1** durumunda!

---

## 📞 DESTEK

**Sorun yaşarsanız:**

1. Bu dokümandaki [Sorun Giderme](#sorun-giderme) bölümünü kontrol edin
2. Log dosyalarını inceleyin: `storage/logs/laravel.log`
3. Debug mode açın: `.env` dosyasında `APP_DEBUG=true`
4. Cache temizliği yapın: `php artisan cache:clear`

**Log kontrol:**

```bash
# Son 50 satır
tail -n 50 storage/logs/laravel.log

# Kredi ile ilgili loglar
grep -i "credit" storage/logs/laravel.log | tail -20

# Real-time izleme
tail -f storage/logs/laravel.log
```

---

## ✅ CHECKLIST: Manuel Kredi Ekleme

**İşlem öncesi:**
- [ ] Tenant ID'yi doğrula
- [ ] Mevcut kredi bakiyesini kaydet
- [ ] Ekleme sebebini belirle (notes)

**İşlem sırası:**
- [ ] 1. Purchase kaydı oluştur (`ai_credit_purchases`)
- [ ] 2. Tenant kolonunu güncelle (`tenants.ai_credits_balance`)
- [ ] 3. Data JSON'u güncelle (`tenants.data`)
- [ ] 4. Cache temizle
- [ ] 5. Doğrulama yap

**İşlem sonrası:**
- [ ] Admin panelden bakiye kontrol et
- [ ] Purchases listesinde kayıt görünüyor mu?
- [ ] Eloquent model doğru değer döndürüyor mu?
- [ ] Log kayıtlarını kontrol et

---

**Son Güncelleme:** 27 Ekim 2025
**Versiyon:** 1.0
**Yazar:** Claude AI + Nurullah

---

**💡 İPUCU:** Gelecekte manuel ekleme yapmak yerine yukarıdaki **Otomatik Kredi Ekleme** bölümündeki `AddAICreditsCommand` Artisan komutunu kullanmanızı öneririm!
