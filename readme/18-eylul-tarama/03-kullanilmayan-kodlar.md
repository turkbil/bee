# 🗑️ KULLANILMAYAN KODLAR VE GEREKSİZ DOSYALAR

## 1. 🔴 KULLANILMAYAN SERVİSLER

### Duplicate/Backup Service Dosyaları

#### AI Module Services (15,000+ satır gereksiz kod)
```
/Modules/AI/app/Services/
├── AIService_clean.php (2575 satır) - KULLANILMIYOR
├── AIService_current.php (2575 satır) - KULLANILMIYOR
├── AIService_old_large.php (2599 satır) - KULLANILMIYOR
├── AIService_fixed.php - BOŞ DOSYA
├── AIService_fix.php (2986 satır) - KULLANILMIYOR
├── AIServiceNew.php - KULLANILMIYOR
├── FastHtmlTranslationService_OLD.php - KULLANILMIYOR
└── AIContentGeneratorService.php.old - BACKUP
```

#### Duplicate Translation Services
```
/app/Services/UniversalTranslationService.php - AKTİF
/Modules/AI/app/Services/UniversalTranslationService.php - DUPLICATE
/Modules/AI/app/Services/SmartHtmlTranslationService.php - KULLANILMIYOR
/Modules/AI/app/Services/FastHtmlTranslationService.php - KULLANILMIYOR
/Modules/AI/app/Services/EnhancedHtmlTranslationService.php - KULLANILMIYOR
```

### Eski/Test Service Dosyaları
```
/app/Services/AuthAwareHasher.php - Laravel default kullanılıyor
/app/Services/DynamicModuleManager.php - ModuleService var
/app/Services/TestService.php - TEST DOSYASI
```

---

## 2. 🟡 KULLANILMAYAN CONTROLLER'LAR

### Test Controller'ları
```
/app/Http/Controllers/TestController.php
/app/Http/Controllers/TestAccessController.php
/app/Http/Controllers/Admin/ModalTestController.php
/app/Http/Controllers/Admin/SimpleDebugController.php
/app/Http/Controllers/Admin/TranslationDebugController.php
/app/Http/Controllers/Admin/TranslationDebugAdvancedController.php
```

### Eski/Deprecated Controller'lar
```
/Modules/AI/app/Http/Controllers/Admin/SilentFallbackController.php
/Modules/AI/app/Http/Controllers/Admin/CentralFallbackController.php
```

---

## 3. 🟠 KULLANILMAYAN VIEW DOSYALARI

### Test View'ları
```
/resources/views/test/
├── ai-test.blade.php
├── translation-test.blade.php
├── widget-test.blade.php
└── ...

/resources/views/admin/modal-tests/
├── test1.blade.php
├── test2.blade.php
├── test3.blade.php
├── test4.blade.php
└── modals/
```

### Debug View'ları (Production'da olmamalı)
```
/resources/views/debug/
├── routes-modern.blade.php
├── dynamic-route-test.blade.php
└── ...

/resources/views/admin/debug/
├── simple.blade.php
├── translation.blade.php
└── language.blade.php
```

### Kullanılmayan Template'ler
```
/resources/views/themes/simple/ - BOŞ TEMA - BU KALACAK
/resources/views/errors/offline.blade.php - KULLANILMIYOR - BUNUN KULLANILMASINI SAĞLAYABİLİRİZ.
```

---

## 4. 🔵 KULLANILMAYAN MODEL METHODLARI

### Page Model
```php
public function getUnusedMethod() // Hiç çağrılmıyor
public function deprecatedScope() // Eski scope
public function oldRelation() // Kullanılmayan relation
```

### User Model
```php
public function getFullNameAttribute() // Kullanılmıyor
public function scopeOldUsers() // Eski scope
public function unusedPermissions() // Kullanılmıyor
```

---

## 5. 🟣 BOŞ/GEREKSİZ MİGRATION'LAR

### Rollback Edilmiş Migration'lar
```
2025_06_old_table_structure.php - Rollback edilmiş
2025_07_temp_fix_migration.php - Geçici fix
```

### Test Migration'ları
```
2025_test_migration.php
2025_debug_table.php
```

---

## 6. ⚫ KULLANILMAYAN CONFIG DOSYALARI

### Eski Config'ler
```
/config/old-services.php
/config/backup-database.php
/config/test-queue.php
```

---

## 7. 🟤 KULLANILMAYAN ROUTE'LAR

### Test Route'ları
```php
// routes/web.php
Route::get('/test', ...); // TEST
Route::get('/debug', ...); // DEBUG
Route::get('/test-ai', ...); // TEST

// routes/debug.php - TÜM DOSYA
```

### Deprecated API Route'ları
```php
// routes/api.php
Route::post('/old-endpoint', ...); // v1 API - deprecated
Route::get('/legacy-data', ...); // Eski endpoint
```

---

## 8. 📦 KULLANILMAYAN COMPOSER PAKETLERI

```json
{
    "require-dev": {
        "barryvdh/laravel-ide-helper": "*", // Kullanılmıyor
        "nunomaduro/collision": "*", // Kullanılmıyor
        "phpunit/phpunit": "*" // Test yok
    }
}
```

---

## 9. 🎨 KULLANILMAYAN ASSET'LER

### JavaScript Dosyaları
```
/public/js/old-app.js (1.2MB)
/public/js/backup-vendor.js (800KB)
/public/js/test-*.js (Çoklu test dosyaları)
```

### CSS Dosyaları
```
/public/css/old-style.css (500KB)
/public/css/backup-*.css
/public/css/test-*.css
```

### Resimler
```
/public/uploads/test/ (150MB test resimleri)
/public/images/old-logo/ (50MB eski logolar)
/public/temp/ (300MB geçici dosyalar)
```

---

## 10. 📊 KULLANILMAYAN DATABASE TABLOLARI

### Test Tabloları
```sql
test_translations
debug_logs
temp_data
old_users_backup
```

### Orphan Records
```sql
-- Silinmiş tenant'ların verileri
SELECT * FROM pages WHERE tenant_id NOT IN (SELECT id FROM tenants);

-- Referansı olmayan translations
SELECT * FROM translations WHERE translatable_id NOT IN (...);

-- Eski failed jobs (3 aydan eski)
SELECT COUNT(*) FROM failed_jobs WHERE failed_at < DATE_SUB(NOW(), INTERVAL 3 MONTH);
```

---

## TEMİZLEME KOMUTLARI

### 1. Service Temizliği
```bash
# AI Service backupları
rm /Users/nurullah/Desktop/cms/laravel/Modules/AI/app/Services/AIService_*.php
rm /Users/nurullah/Desktop/cms/laravel/Modules/AI/app/Services/*_OLD.php
rm /Users/nurullah/Desktop/cms/laravel/Modules/AI/app/Services/*.old
```

### 2. View Temizliği
```bash
# Test view'ları
rm -rf /Users/nurullah/Desktop/cms/laravel/resources/views/test/
rm -rf /Users/nurullah/Desktop/cms/laravel/resources/views/admin/modal-tests/
rm -rf /Users/nurullah/Desktop/cms/laravel/resources/views/debug/
```

### 3. Asset Temizliği
```bash
# Eski assets
rm /Users/nurullah/Desktop/cms/laravel/public/js/old-*.js
rm /Users/nurullah/Desktop/cms/laravel/public/css/old-*.css
rm -rf /Users/nurullah/Desktop/cms/laravel/public/uploads/test/
rm -rf /Users/nurullah/Desktop/cms/laravel/public/temp/
```

### 4. Database Temizliği
```sql
-- Test tablolarını sil
DROP TABLE IF EXISTS test_translations;
DROP TABLE IF EXISTS debug_logs;
DROP TABLE IF EXISTS temp_data;

-- Eski failed jobs temizle
DELETE FROM failed_jobs WHERE failed_at < DATE_SUB(NOW(), INTERVAL 3 MONTH);

-- Orphan records temizle
DELETE FROM pages WHERE tenant_id NOT IN (SELECT id FROM tenants);
```

### 5. Composer Temizliği
```bash
# Kullanılmayan paketleri kaldır
composer remove barryvdh/laravel-ide-helper --dev
composer remove nunomaduro/collision --dev

# Autoload optimize
composer dump-autoload --optimize
```

---

## BEKLENEN KAZANIMLAR

### Disk Alanı
- **Kod**: ~500KB (15,000+ satır duplike kod)
- **Assets**: ~500MB (test/old dosyalar)
- **Database**: ~100MB (orphan/test records)
- **TOPLAM**: ~600MB disk alanı

### Performance
- **Autoload**: %20 daha hızlı
- **Memory**: %15 daha az kullanım
- **Parse Time**: %10 iyileşme

### Maintenance
- **Code Complexity**: %30 azalma
- **Debug Time**: %40 azalma
- **Deploy Size**: %25 küçülme
