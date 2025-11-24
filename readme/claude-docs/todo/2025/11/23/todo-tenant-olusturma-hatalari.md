# Tenant Oluşturma Hataları & Çözümleri - Teknik TODO

**Tarih:** 2025-11-23
**Durum:** ✅ Tamamlandı

---

## Düzeltilen Dosyalar

### 1. Plesk Komut Yolu Düzeltmesi

- [x] `app/Jobs/UnregisterDatabaseFromPlesk.php`
  - `plesk` → `/usr/sbin/plesk`

- [x] `app/Listeners/RegisterTenantDatabaseToPlesk.php`
  - Tüm `plesk` komutları → `/usr/sbin/plesk`
  - Domain: Her zaman `tuufi.com` kullan

### 2. Eksik Migration Dosyaları

- [x] `Modules/Shop/database/migrations/tenant/007_create_shop_payment_methods_table.php`
  - Arşivden tenant klasörüne kopyalandı

- [x] `Modules/Shop/database/migrations/tenant/023_create_shop_payments_table.php`
  - Arşivden tenant klasörüne kopyalandı

### 3. Duplicate Index Düzeltmeleri (morphs)

- [x] `Modules/Favorite/database/migrations/tenant/2024_11_10_000001_create_favorites_table.php`
  - `$table->index(['favoritable_type', 'favoritable_id'])` kaldırıldı

- [x] `Modules/ReviewSystem/database/migrations/tenant/2024_11_10_000001_create_ratings_table.php`
  - `$table->index(['ratable_type', 'ratable_id'])` kaldırıldı

- [x] `Modules/ReviewSystem/database/migrations/tenant/2024_11_10_000002_create_reviews_table.php`
  - `$table->index(['reviewable_type', 'reviewable_id'])` kaldırıldı
  - `$table->index('is_approved')` kaldırıldı (satır 31'de zaten var)

- [x] `Modules/Cart/database/migrations/tenant/2025_11_12_170813_create_cart_items_table.php`
  - morphs index kontrolü yapıldı

- [x] `Modules/Payment/database/migrations/tenant/2025_11_09_002_create_payments_table.php`
  - morphs index kontrolü yapıldı

- [x] `Modules/SeoManagement/database/migrations/tenant/2025_07_19_000001_create_seo_settings_table.php`
  - morphs index kontrolü yapıldı

---

## Test Sonuçları

```bash
# Test komutu
php artisan tinker --execute="
use App\Models\Tenant;
\$tenant = Tenant::create([
    'title' => 'Test Muzibu',
    'tenancy_db_name' => 'tenant_test_muzibu',
    'is_active' => true,
    'theme_id' => 1,
]);
"

# Sonuç
- 104 migration çalıştı
- 92 tablo oluşturuldu
- shop_payment_methods mevcut ✅
```

---

## Gelecekte Dikkat Edilecekler

### Migration Oluştururken
```bash
# İKİ YERE KOYULMALI:
database/migrations/YYYY_MM_DD_table.php           # Central
Modules/*/database/migrations/tenant/YYYY_...php  # Tenant
```

### morphs() Kullanırken
```php
// morphs() OTOMATİK INDEX OLUŞTURUR!
$table->morphs('favoritable');

// ❌ YANLIŞ - Tekrar tanımlama:
$table->index(['favoritable_type', 'favoritable_id']);

// ✅ DOĞRU - Sadece yorum:
// NOT: morphs() zaten index oluşturur
```

### Sütunda ->index() Varsa
```php
// Satırda index varsa:
$table->boolean('is_approved')->default(false)->index();

// ❌ YANLIŞ - Tekrar tanımlama:
$table->index('is_approved');

// ✅ DOĞRU - Composite index farklıysa kalabilir:
$table->index(['is_approved', 'created_at']);
```

---

## Plesk Entegrasyonu

### Komut Yolu
```bash
# Her zaman tam yol kullan
/usr/sbin/plesk db "SQL_QUERY"
```

### Domain
```php
// Tüm tenant'lar ana domain'e bağlı
$domain = 'tuufi.com';
```

---

## Komutlar

### Tenant Silme Sonrası Database Temizliği
```bash
# Plesk'te database kaldı mı kontrol
/usr/sbin/plesk db "SELECT name FROM databases WHERE dom_id = 1"

# Manuel silme gerekirse
/usr/sbin/plesk db "DELETE FROM db_users WHERE db_id IN (SELECT id FROM databases WHERE name = 'tenant_xxx')"
/usr/sbin/plesk db "DELETE FROM databases WHERE name = 'tenant_xxx'"
```

### Test Simülasyonu
```bash
php artisan tinker --execute="
use App\Models\Tenant;
\$t = Tenant::create(['title' => 'Test', 'tenancy_db_name' => 'tenant_test', 'is_active' => true, 'theme_id' => 1]);
"
```

---

**Notlar:**
- Tenant 2 (ixtif.com) referans alındı - 98 tablo
- Test sonucu 92 tablo (bazı modüller aktif değil, normal)
- Tüm kritik hatalar çözüldü
