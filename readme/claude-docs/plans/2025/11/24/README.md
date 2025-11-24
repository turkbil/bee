# Üyelik Sistemi - Bekleyen İşler Plan Dökümanları

**Tarih:** 2025-11-24 19:53
**Durum:** Planlama Tamamlandı
**Backend İlerleme:** %100
**Genel İlerleme:** %77

---

## 📋 Plan Listesi

### 1. Kullanıcı Listesi Güncelleme
**Dosya:** `plan-01-user-list-update.md`
**Öncelik:** Orta
**Tahmini Süre:** 1 gün

**Özellikler:**
- 4 yeni kolon (Abonelik, Onay, Cihaz, Kurumsal)
- 3 yeni filtre
- 2 yeni toplu işlem (Bulk Approve, Bulk Reject)

**Dosyalar:**
- `Modules/UserManagement/app/Http/Livewire/UserComponent.php`
- `Modules/UserManagement/resources/views/livewire/user-component.blade.php`

---

### 2. Frontend Auth Sayfaları
**Dosya:** `plan-02-frontend-auth-pages.md`
**Öncelik:** Orta
**Tahmini Süre:** 2-3 gün

**Sayfalar:**
- Login (Cihaz limiti, 2FA, onay kontrolü)
- Register (Kurumsal kod, trial, onay sistemi)
- Profile (4 sekmeli: Hesap, Güvenlik, Cihazlar, Abonelik)

**Yeni Dosyalar:**
- `app/Http/Livewire/Auth/LoginComponent.php`
- `app/Http/Livewire/Auth/RegisterComponent.php`
- `app/Http/Livewire/Profile/ProfileComponent.php`
- `app/Http/Livewire/Profile/SecurityComponent.php`
- `app/Http/Livewire/Profile/DevicesComponent.php`
- `app/Http/Livewire/Profile/SubscriptionComponent.php`

---

### 3. Cihaz Yönetimi Sayfası
**Dosya:** `plan-03-device-management.md`
**Öncelik:** Orta
**Tahmini Süre:** 1 gün

**Özellikler:**
- Aktif cihazlar listesi
- User-Agent parsing
- Cihaz çıkarma
- Toplu cihaz çıkarma
- Limit bilgisi gösterimi

**Yeni Dosyalar:**
- `app/Http/Livewire/Profile/DevicesComponent.php`
- `resources/views/livewire/profile/devices-component.blade.php`

---

### 4. Abonelik Durumu Sayfası
**Dosya:** `plan-04-subscription-status.md`
**Öncelik:** Orta
**Tahmini Süre:** 1 gün

**Özellikler:**
- Mevcut plan bilgisi
- Kalan gün hesaplama
- Kurumsal üye bilgilendirmesi
- Plan değiştirme linki
- Ödeme geçmişi
- İptal/Yenileme işlemleri

**Yeni Dosyalar:**
- `app/Http/Livewire/Profile/SubscriptionComponent.php`
- `resources/views/livewire/profile/subscription-component.blade.php`

---

### 5. Pricing & Checkout
**Dosya:** `plan-05-pricing-checkout.md`
**Öncelik:** Düşük
**Tahmini Süre:** 2 gün

**Sayfalar:**
- Pricing (Plan kartları, özellik karşılaştırma, kupon)
- Checkout (Sipariş özeti, fatura bilgileri, PayTR)

**Yeni Dosyalar:**
- `app/Http/Livewire/Subscription/PricingComponent.php`
- `app/Http/Livewire/Subscription/CheckoutComponent.php`
- `resources/views/livewire/subscription/pricing-component.blade.php`
- `resources/views/livewire/subscription/checkout-component.blade.php`

---

## 📊 Genel Özet

### Tamamlanan İşler (17)
- ✅ Migration (parent_id mimarisi)
- ✅ MuzibuCorporateAccount Model
- ✅ CorporateService (yeni mimari)
- ✅ Admin Kurumsal Yönetim
- ✅ Subscription Modülü
- ✅ Coupon Modülü
- ✅ Mail Modülü
- ✅ Auth Servisleri
- ✅ Middleware
- ✅ Cron Jobs
- ✅ Settings
- ✅ Auth Tema Tasarımları

### Bekleyen İşler (5)
1. ⏳ Kullanıcı Listesi Güncelleme
2. ⏳ Frontend Auth Sayfaları
3. ⏳ Cihaz Yönetimi Sayfası
4. ⏳ Abonelik Durumu Sayfası
5. ⏳ Pricing & Checkout

---

## 🎯 Önerilen Sıralama

### Öncelik 1 (Hemen)
1. **Kullanıcı Listesi Güncelleme** - Admin ihtiyacı
2. **Frontend Auth Sayfaları** - Kullanıcı kayıt/giriş

### Öncelik 2 (Sonra)
3. **Cihaz Yönetimi** - Güvenlik özelliği
4. **Abonelik Durumu** - Kullanıcı bilgilendirmesi

### Öncelik 3 (En Son)
5. **Pricing & Checkout** - Satış akışı

---

## 🚀 Çalışma Yöntemi

Her plan için:
1. **HTML Taslak Hazırla** → Onay bekle
2. **Kod Yaz** → Backend + Frontend
3. **Test Et** → Fonksiyonel test
4. **Deploy** → Production'a al

---

## 📁 Dosya Yapısı

```
readme/claude-docs/plans/2025/11/24/
├── README.md (bu dosya)
├── plan-01-user-list-update.md
├── plan-02-frontend-auth-pages.md
├── plan-03-device-management.md
├── plan-04-subscription-status.md
└── plan-05-pricing-checkout.md
```

---

## 💡 Teknik Notlar

### Ortak Servisler (Hazır)
- `app/Services/Auth/CorporateService.php`
- `app/Services/Auth/DeviceService.php`
- `app/Services/Auth/TwoFactorService.php`
- `app/Services/Auth/LoginLogService.php`
- `Modules/Subscription/app/Services/SubscriptionService.php`
- `Modules/Coupon/app/Services/CouponService.php`

### Middleware (Hazır)
- `CheckDeviceLimit`
- `CheckSubscription`
- `CheckApproval`

### Models (Hazır)
- `User` (with relationships)
- `MuzibuCorporateAccount`
- `Subscription`
- `SubscriptionPlan`
- `Coupon`

---

## 🎨 Tasarım Standartları

### Frontend
- **Framework:** Alpine.js + Tailwind CSS
- **Icons:** FontAwesome
- **Responsive:** Mobile-first

### Admin
- **Framework:** Livewire + Bootstrap + Tabler.io
- **Icons:** FontAwesome
- **Pattern:** index.blade.php + manage.blade.php

---

## ✅ Checklist

Her plan için:
- [ ] Plan MD okundu
- [ ] Mevcut kod analiz edildi
- [ ] HTML taslak hazırlandı
- [ ] Taslak onaylandı
- [ ] Backend kodu yazıldı
- [ ] Frontend kodu yazıldı
- [ ] Test edildi
- [ ] Permission kontrol edildi
- [ ] Cache temizlendi
- [ ] Production deploy edildi

---

**HAZIR! Hangi plandan başlamak istersin?**
