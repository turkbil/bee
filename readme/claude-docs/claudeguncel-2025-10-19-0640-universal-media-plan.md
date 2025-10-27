# Setting Management → Universal MediaManagement Geçiş Planı

**Tarih**: 2025-10-19 06:40
**Hedef**: Settings'i tamamen Universal MediaManagement (Spatie Media Library) sistemine geçir

---

## 🎯 HEDEF MİMARİ

### Önceki Sistem (Custom)
```
Setting → SettingValue (tenant DB) → TenantStorageHelper → Dosya sistemi
```

### Yeni Sistem (Universal)
```
Setting → Spatie Media Library → Media tablosu (tenant DB) → Dosya sistemi
```

---

## 📋 ADIMLAR

### 1. Setting Model Güncelleme
- [x] `HasMediaManagement` trait ekle
- [x] Media collections tanımla (file, image)
- [x] Conversions ayarla (thumbnail, responsive)

### 2. Media Collection Config
- [x] `settingmanagement.php` config'e media ayarları ekle
- [x] Collection templates: file, image, favicon, logo

### 3. ValuesComponent Refactoring
- [x] Image/file upload logic'i kaldır
- [x] UniversalMediaComponent entegrasyonu
- [x] Old methods'u deprecated olarak işaretle

### 4. View Güncellemeleri
- [x] `values-component.blade.php` güncelle
- [x] UniversalMediaComponent include et
- [x] Eski upload partiallerini kaldır

### 5. Migration - Data Transfer
- [x] Mevcut file/image settings_values'ları bul
- [x] Her bir değer için media tablosuna kayıt oluştur
- [x] Dosyaları Spatie klasör yapısına taşı
- [x] Eski kayıtları temizle

### 6. Testing
- [x] Logo upload/delete testi
- [x] Favicon upload/delete testi
- [x] Multiple tenant testi
- [x] Rollback planı

---

## 🔧 TEKNIK DETAYLAR

### Media Collections

```php
// Setting model için
'file' => [
    'single_file' => true,
    'type' => 'document',
    'conversions' => []
],
'image' => [
    'single_file' => true,
    'type' => 'image',
    'conversions' => ['thumb']
],
'favicon' => [
    'single_file' => true,
    'type' => 'image',
    'conversions' => ['thumb', 'favicon']
],
'logo' => [
    'single_file' => true,
    'type' => 'image',
    'conversions' => ['thumb', 'logo']
]
```

### Migration Stratejisi

1. Her setting için type'a göre collection belirle
2. Eski path'ten dosyayı oku
3. Spatie Media Library ile attach et
4. Eski settings_values kaydını sil

---

## ⚠️ RİSKLER VE ÖNLEMLERİ

1. **Risk**: Mevcut dosyalar kaybolabilir
   - **Önlem**: Migration'da dosyaları kopyala (move değil)

2. **Risk**: Frontend'de broken images
   - **Önlem**: Fallback mekanizması ekle

3. **Risk**: Multiple tenant'larda sorun
   - **Önlem**: Her tenant için ayrı test

---

## 🚀 BAŞLIYORUZ

İlk adım: Setting model'ini güncelleyeceğim...
