# 💱 Multi-Currency System - TCMB Auto Update

**Tarih:** 2025-10-30 20:20
**Durum:** ✅ Tamamlandı
**Tenant:** ixtif.com (ID: 2)

---

## 📋 YAPILAN İŞLEMLER

### 1. ✅ Manuel/Otomatik Mod Sistemi

**Migration:** `2025_10_30_200601_add_is_auto_update_to_shop_currencies_table.php`

```sql
ALTER TABLE shop_currencies
ADD COLUMN is_auto_update BOOLEAN DEFAULT FALSE
COMMENT 'TCMB\'den otomatik güncelleme yapılsın mı?';
```

**Özellikler:**
- `is_auto_update = true` → TCMB'den günlük otomatik güncellenir
- `is_auto_update = false` → Manuel kur girişi (korunur)

---

### 2. ✅ Model Güncellemesi

**Dosya:** `Modules/Shop/app/Models/ShopCurrency.php`

**Eklenenler:**
- `is_auto_update` fillable ve cast
- `scopeAutoUpdate()` query scope

---

### 3. ✅ Admin Manage Component

**Dosya:** `Modules/Shop/app/Http/Livewire/Admin/ShopCurrencyManageComponent.php`

**Eklenenler:**
- `public bool $isAutoUpdate = false;` property
- Validation rule eklendi
- Save işleminde is_auto_update kaydediliyor

**View:** `currency-manage-component.blade.php`
- 3 kolonlu toggle sistemi (Active, Default, Auto Update)
- Preview card'da auto update badge
- Kullanıcı dostu açıklamalar

---

### 4. ✅ Admin Listeleme Component

**Dosya:** `Modules/Shop/app/Http/Livewire/Admin/ShopCurrencyComponent.php`

**updateFromTCMB() Metodu Güncellemesi:**
```php
// SADECE is_auto_update=true olanları güncelle
$currencies = ShopCurrency::whereIn('code', array_keys($tcmbRates))
    ->where('is_auto_update', true)
    ->get();

// Manuel kurları say ve bilgilendir
$skippedCount = $manualCurrencies->count();
```

**View:** `currency-component.blade.php`
- Exchange Rate kolonunda badge sistemi:
  - ✅ Auto Update (yeşil) → Otomatik güncellenen
  - ❌ Manuel (mavi) → Manuel girilen

---

### 5. ✅ Artisan Command

**Dosya:** `app/Console/Commands/UpdateCurrencyRatesCommand.php`

**Komut:**
```bash
php artisan currency:update-rates           # Sadece auto_update=true olanları günceller
php artisan currency:update-rates --force   # Tüm currency'leri zorla günceller
```

**Çıktı Örneği:**
```
🔄 Fetching exchange rates from TCMB...
✅ Fetched 21 exchange rates from TCMB
🔧 Updating 1 currencies...
  📈 USD: 30.0000 → 41.9755 (+11.9755 / +39.92%)

✅ Successfully updated 1 currencies!
💰 USD: ₺41.9755
💶 EUR: ₺48.7458
```

**Özellikler:**
- TCMB API'den güncel kurları çeker
- Sadece `is_auto_update=true` olanları günceller
- Değişim oranını ve yüzdesini gösterir
- Log dosyasına kaydeder

---

### 6. ✅ Cron Job / Task Scheduler

**Dosya:** `app/Console/Kernel.php`

```php
// Her gün 15:30'da TCMB'den kurları çek (TCMB genelde 15:00'da yayınlar)
$schedule->command('currency:update-rates')
         ->dailyAt('15:30')
         ->withoutOverlapping()
         ->runInBackground()
         ->appendOutputTo(storage_path('logs/currency-updates.log'));
```

**Log Dosyası:** `storage/logs/currency-updates.log`

---

## 🎯 KULLANIM SENARYOLARI

### Senaryo 1: Otomatik Güncelleme (USD, EUR)

1. Admin panelden USD düzenle
2. "TCMB Auto Update" toggle'ı aktif et
3. Kaydet

**Sonuç:**
- Her gün 15:30'da USD otomatik TCMB'den güncellenir
- Listeleme sayfasında "Auto Update ✅" badge görünür
- Manuel değiştirmek istersem yine düzenlenebilir (ama ertesi gün otomatik güncellenir)

---

### Senaryo 2: Manuel Kur (GBP, JPY)

1. Admin panelden GBP ekle/düzenle
2. "TCMB Auto Update" toggle'ı kapalı tut
3. Exchange Rate'i manuel gir (örn: 55.0000)
4. Kaydet

**Sonuç:**
- GBP kuru manuel girilen değerde kalır
- Cron job çalışsa bile GBP değişmez
- Listeleme sayfasında "Manuel ❌" badge görünür

---

### Senaryo 3: Anlık Manuel Güncelleme

**Admin Panel:**
1. `/admin/shop/currencies` sayfasına git
2. "TCMB'den Güncelle" butonuna tık

**Sonuç:**
- SADECE `is_auto_update=true` olanlar güncellenir
- Toast mesajı: "2 para birimi güncellendi (1 manuel kur korundu)"

---

### Senaryo 4: Toplu Zorunlu Güncelleme

**Terminal:**
```bash
php artisan currency:update-rates --force
```

**Sonuç:**
- TÜM currency'ler TCMB'den güncellenir
- `is_auto_update=false` olanlar bile güncellenir
- Acil durumlarda veya ilk kurulum için kullanılır

---

## 🧪 TEST SONUÇLARI

### Test 1: TCMB API Bağlantısı
```bash
curl -s -k https://www.tcmb.gov.tr/kurlar/today.xml
```
✅ Başarılı - 21 döviz kuru çekildi

---

### Test 2: Command Çalıştırma
```bash
php artisan currency:update-rates
```
**Before:**
- USD: 30.0000 (is_auto_update=true)
- EUR: 48.7458 (is_auto_update=false)

**After:**
- USD: 41.9755 ✅ Güncellendi
- EUR: 48.7458 ✅ Korundu (manuel)

---

### Test 3: Force Update
```bash
php artisan currency:update-rates --force
```
**Sonuç:** Tüm currency'ler güncellendi (manuel olanlar dahil)

---

## 📊 VERİTABANI DEĞİŞİKLİKLERİ

### Migration Çalıştırıldı
```bash
php artisan migrate
php artisan tenants:migrate
```

**Sonuç:**
- Central DB: ✅ is_auto_update eklendi
- Tenant 2 (ixtif.com): ✅ is_auto_update eklendi
- Tenant 3 (ixtif.com.tr): ✅ is_auto_update eklendi

---

## 🔐 GÜVENLİK & PERFORMANS

### TCMB API
- SSL verification kapalı (`curl -k`)
- 10 saniyelik timeout
- Error handling mevcut
- Log'lara kaydediliyor

### Command
- `withoutOverlapping()` → Aynı anda 2 kez çalışmaz
- `runInBackground()` → Sistem bloklamaz
- Log dosyasına output yazılır

### Cron Job
- Günde 1 kez (15:30)
- TCMB yayın saatinden sonra (15:00 + 30dk buffer)
- Laravel Scheduler üzerinden kontrollü

---

## 📝 DOKÜMANTASYON LİNKLERİ

**Admin Panel:**
- Currency List: `/admin/shop/currencies`
- Currency Create/Edit: `/admin/shop/currencies/manage/{id?}`

**Command:**
- `php artisan currency:update-rates --help`

**Log Dosyaları:**
- `storage/logs/currency-updates.log` → Cron job çıktıları
- `storage/logs/laravel.log` → TCMB API hataları

---

## ✅ TODO LİSTESİ - TAMAMLANDI

- [x] TCMB API test et
- [x] `is_auto_update` field ekle (migration)
- [x] ShopCurrency modeline ekle
- [x] ManageComponent'e toggle ekle
- [x] CurrencyComponent'te auto_update filtrele
- [x] Listeleme view'a badge ekle
- [x] Artisan Command oluştur
- [x] Task Scheduler ekle
- [x] Command test et
- [x] Cache temizle + Build

---

## 🚀 SONRAKİ ADIMLAR (OPSİYONEL)

### 1. Email Bildirimi
Kur değişimi %10'dan fazlaysa admin'e email gönder

### 2. Ürün Fiyatları Otomatik Güncelleme
Currency değiştiğinde, o currency'yi kullanan ürünlerin fiyatlarını güncelle

### 3. Kur Geçmişi
`shop_currency_history` tablosu oluştur, her değişimi kaydet

### 4. Frontend Currency Switcher
Sitede ziyaretçi para birimi seçebilsin (session'da tut)

---

## 📌 NOTLAR

1. **TCMB Yayın Saati:** Her gün 15:00 (hafta içi)
2. **Cron Schedule:** 15:30 (30 dakika buffer)
3. **Manuel Override:** Admin istediği zaman manuel değiştirebilir
4. **Force Update:** Acil durumlarda tüm kurları zorla güncelle

---

**✅ Sistem başarıyla test edildi ve production'a hazır!**
