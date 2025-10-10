# 🔐 CORE SYSTEM FILES - KORUNMALI DOSYALAR

## 🚨 UYARI: BU DOSYALAR YAPAY ZEKA TARAFINDAN DEĞİŞTİRİLMEMELİDİR
## 🚨 WARNING: THESE FILES SHOULD NOT BE MODIFIED BY AI

---

## 📋 Dosya Listesi / File List

### 1. JavaScript - `/public/js/core-system.js`
- **Version**: 1.0.0
- **İçerik / Content**:
  - Language Switcher System (Dil değiştirme sistemi)
  - Dark Mode Detection (Karanlık mod algılama)
  - Core Utilities (Temel yardımcı fonksiyonlar)
  - Event Listeners (Olay dinleyicileri)

### 2. CSS - `/public/css/core-system.css`
- **Version**: 1.0.0
- **İçerik / Content**:
  - Core Animations (Temel animasyonlar)
  - System Utilities (Sistem yardımcı sınıfları)
  - Critical Fixes (Kritik düzeltmeler)

---

## 🔧 Kullanım / Usage

### Frontend Themes
```html
<!-- Her tema bu dosyaları yüklemek ZORUNDADIR -->
<!-- Every theme MUST load these files -->

<!-- CSS - <head> içinde -->
<link rel="stylesheet" href="{{ asset('css/core-system.css') }}?v=1.0.0">

<!-- JS - </body> öncesinde -->
<script src="{{ asset('js/core-system.js') }}?v=1.0.0"></script>
```

### Admin Panel
```html
<!-- Admin layout'ta da aynı şekilde yüklenmeli -->
<!-- Must be loaded in admin layout as well -->

<!-- CSS -->
<link rel="stylesheet" href="{{ asset('css/core-system.css') }}?v=1.0.0">

<!-- JS -->
<script src="{{ asset('js/core-system.js') }}?v=1.0.0"></script>
```

---

## 📌 Özellikler / Features

### 1. Language Switcher (Dil Değiştirici)
- Otomatik overlay loading animasyonu
- Dark/Light mode uyumlu
- Cache temizleme özelliği
- Smooth transitions

### 2. Dark Mode Detection
- localStorage desteği
- CSS class kontrolü
- Media query desteği
- Otomatik tema algılama

### 3. Event Listeners
- Alpine.js uyumlu
- Livewire uyumlu
- Turbo uyumlu
- Dynamic content ready

---

## ⚠️ Kurallar / Rules

1. **ASLA DEĞİŞTİRME**: Bu dosyalar sistem çekirdeğidir
2. **HER ZAMAN YÜKLE**: Tüm temalarda ve admin'de zorunludur
3. **VERSİYON KONTROLÜ**: Güncellemeler sadece sistem yöneticisi tarafından
4. **TEMA BAĞIMSIZ**: Tema değişikliklerinden etkilenmez

---

## 🛡️ Koruma / Protection

Bu dosyalar:
- ✅ Tema değişikliklerinden bağımsızdır
- ✅ Tüm sayfalarda çalışır
- ✅ Kritik sistem fonksiyonları içerir
- ✅ Performance optimized
- ❌ AI tarafından değiştirilmemelidir
- ❌ Tema geliştiricileri tarafından override edilmemelidir

---

## 📞 İletişim / Contact

Sadece sistem yöneticisi bu dosyaları güncelleyebilir.
Only system administrator can update these files.

**Last Updated**: 2025-07-28
**Maintainer**: System Core Team