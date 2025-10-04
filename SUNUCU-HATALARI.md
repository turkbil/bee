# 🚨 PLESK SUNUCU DEPLOYMENT HATALARI

**Tarih**: 2025-10-04
**Sunucu**: tuufi.com (Plesk)
**Durum**: Composer install başarısız

---

## ❌ ANA HATA

### HATA 1: IdeHelper ServiceProvider Bulunamıyor

**Hata Mesajı**:
```
In ProviderRepository.php line 205:
Class "Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" not found
```

**Sebep**:
- Composer `--no-dev` ile çalıştırıldığında `laravel-ide-helper` paketi kurulmuyor
- Ancak ServiceProvider hala yüklenmeye çalışılıyor

**Konum**:
- `config/app.php` veya `bootstrap/providers.php`

**Çözüm Önerileri**:

#### Option 1: Conditional Loading (Önerilen)
```php
// bootstrap/providers.php veya config/app.php
if (app()->environment('local')) {
    $app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
}
```

#### Option 2: Composer.json Düzenleme
```json
// composer.json - require'dan require-dev'e taşı
"require-dev": {
    "barryvdh/laravel-ide-helper": "^3.2"
}
```

#### Option 3: ServiceProvider Kaydını Kaldır
Production'da gereksiz olduğu için tamamen kaldır.

---

## ⚠️ İKİNCİL SORUNLAR

### SORUN 1: Studio Module Route Hatası

**Dosya**: `Modules/Studio/routes/admin.php`
**Satırlar**: 16, 20

**Mevcut Kod**:
```php
Route::get('/', StudioIndexComponent::class)  // HATA!
Route::get('/editor/{module}/{id}/{locale?}', EditorComponent::class)  // HATA!
```

**Sorun**: Livewire Component class'ları direkt route olarak kullanılmış

**Çözüm**:
```php
// Option 1: Controller method kullan
Route::get('/', [StudioController::class, 'index'])
Route::get('/editor/{module}/{id}/{locale?}', [StudioController::class, 'editor'])

// Option 2: StudioController'a methodları ekle
public function index() {
    return view('studio::admin.index');
}

public function editor($module, $id, $locale = null) {
    return view('studio::admin.editor', compact('module', 'id', 'locale'));
}
```

---

### SORUN 2: UserManagement Module Route Hatası

**Dosya**: `Modules/UserManagement/routes/admin.php`
**Satır**: 23

**Mevcut Kod**:
```php
Route::get('/', UserComponent::class)  // HATA!
```

**Sorun**: Livewire Component class'ı direkt route olarak kullanılmış

**Çözüm**:
```php
// Controller method kullan
Route::get('/', [UserManagementController::class, 'index'])

// UserManagementController'a method ekle
public function index() {
    return view('usermanagement::admin.index');
}
```

---

## 📊 COMPOSER ÇIKTI DETAYI

### Package Discover Hatası
```bash
> @php artisan package:discover --ansi

In ProviderRepository.php line 205:
  Class "Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" not found

Script @php artisan package:discover --ansi handling the post-autoload-dump event returned with error code 1
```

### PSR-4 Autoloading Uyarıları (Önemsiz)
Çok sayıda "does not comply with psr-4 autoloading standard" uyarısı var ancak bunlar kritik değil.

---

## ✅ ÇÖZÜM ADIMLARI

### Adım 1: IdeHelper Sorununu Çöz
1. `bootstrap/providers.php` dosyasını aç
2. IdeHelper ServiceProvider kaydını bul
3. Conditional loading ekle veya tamamen kaldır

### Adım 2: Studio Route'larını Düzelt
1. `Modules/Studio/routes/admin.php` dosyasını aç
2. Livewire Component route'larını controller method'a çevir
3. `StudioController` içine `index()` ve `editor()` methodları ekle

### Adım 3: UserManagement Route'unu Düzelt
1. `Modules/UserManagement/routes/admin.php` dosyasını aç
2. Livewire Component route'unu controller method'a çevir
3. `UserManagementController` içine `index()` methodu ekle

### Adım 4: Composer Tekrar Çalıştır
```bash
COMPOSER_ALLOW_SUPERUSER=1 /opt/plesk/php/8.3/bin/php /usr/lib64/plesk-9.0/composer.phar install --optimize-autoloader --no-dev --no-interaction
```

---

## 🔍 KONTROL KOMUTU

Hatalar düzeltildikten sonra test komutu:
```bash
export COMPOSER_ALLOW_SUPERUSER=1 && /opt/plesk/php/8.3/bin/php /usr/lib64/plesk-9.0/composer.phar dump-autoload --optimize --no-interaction
```

**Beklenen**: "Generating optimized autoload files" ve başarılı tamamlanma

---

## 📝 NOTLAR

- PHP 8.3 kullanılıyor: `/opt/plesk/php/8.3/bin/php`
- Composer yolu: `/usr/lib64/plesk-9.0/composer.phar`
- Production mode: `--no-dev` flag'i aktif
- COMPOSER_ALLOW_SUPERUSER=1 gerekli (root kullanıcısı)

---

**Son Güncelleme**: 2025-10-04
**Hazırlayan**: Claude AI
**Durum**: Nurullah'ın müdahalesi bekleniyor
