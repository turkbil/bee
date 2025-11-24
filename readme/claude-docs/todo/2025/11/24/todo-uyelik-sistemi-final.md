# Üyelik Sistemi - Final TODO

**Tarih:** 2025-11-24
**Durum:** Devam Ediyor

---

## KRİTİK KURALLAR

1. **Her adımda HTML taslak gösterilecek:**
   - Mevcut tasarım varsa → Mevcut + Yeni taslak
   - Yeni sayfa ise → Sadece yeni taslak
   - Onay alındıktan sonra kod yazılacak

2. **Sıralı ilerleme:**
   - Bir adım tamamlanmadan diğerine geçilmeyecek
   - Her adım için onay alınacak

3. **Bu TODO değişmeyecek:**
   - Yeni todo eklenmeyecek
   - Sadece tamamlananlar işaretlenecek
   - Gerekirse alt maddelere not eklenebilir

---

## BACKEND (Tamamlandı - %100)

### Kurumsal Hesap Sistemi (parent_id mimarisi)
- [x] Migration temizlendi (3 dosya → 1 dosya)
- [x] parent_id self-referencing eklendi
- [x] corporate_code nullable yapıldı (üyelerde NULL)
- [x] MuzibuCorporateAccount Model güncellendi
  - [x] isUserOwner(), isUserMember() static helpers
  - [x] getCorporateForUser(), findByUser() static helpers
  - [x] members() self-referencing relationship
- [x] CorporateService tamamen yeniden yazıldı
  - [x] joinWithCode() - kodla katılma
  - [x] getBillingAddress() - cart_addresses'ten adres
  - [x] getEffectiveSubscription() - kurum aboneliği
  - [x] leave() - kurumdan ayrılma

### Diğer Backend
- [x] Subscription Modülü (Livewire Admin)
- [x] Coupon Modülü (Livewire Admin)
- [x] Mail Modülü (8 mail + template)
- [x] Migration'lar (tüm tablolar hazır)
- [x] Middleware'ler (CheckDeviceLimit, CheckSubscription, CheckApproval)
- [x] Auth servisleri (6 adet)
- [x] Cron Jobs (4 command)
- [x] Settings (5 grup, 17 key)

---

## ADMIN PANEL

### 1. Menü Entegrasyonu
- [x] Subscription menüsü ekleme (zaten mevcut)
- [x] Coupon menüsü ekleme (zaten mevcut)
- [x] Kurumsal Hesaplar menüsü ekleme (UserManagement altına)

### 1.5 Kurumsal Hesap Yönetimi (Tamamlandı)
- [x] Liste sayfası (CorporateAccountComponent)
- [x] Ana firma oluşturma (shop products pattern)
- [x] Şube yönetimi (sol-sağ liste)
- [x] Alpine.js kod üretimi (6 karakter)
- [x] Database constraint düzeltme (tüm tenantlar)

### 2. Kullanıcı Listesi Güncelleme
- [ ] Mevcut liste taslağı göster
- [ ] Yeni kolonlar tasarımı (abonelik durumu, onay, cihaz sayısı)
- [ ] Filtreler tasarımı (abonelik tipi, onay durumu, kurumsal)
- [ ] Kod uygulama

### 3. Kullanıcı Detay Sayfası
- [ ] Mevcut detay taslağı göster
- [ ] Yeni sekmeler tasarımı:
  - [ ] Genel Bilgiler sekmesi
  - [ ] Abonelik sekmesi
  - [ ] Cihazlar sekmesi
  - [ ] Giriş Logları sekmesi
  - [ ] Kurumsal sekmesi (varsa)
- [ ] Kod uygulama

### 4. Kurumsal Hesap Yönetimi (Tamamlandı)
- [x] Liste sayfası tasarımı
- [x] Detay/Düzenleme sayfası tasarımı
- [x] Üye listesi tasarımı
- [x] Kod uygulama
- [x] Alpine.js kod üretimi
- [x] Database düzeltmeleri (tüm tenantlar)

### 5. Dashboard Widget'ları
- [ ] Abonelik istatistikleri widget tasarımı
- [ ] Yeni kayıtlar widget tasarımı
- [ ] Kod uygulama

---

## FRONTEND SAYFALARI

### 6. Login Sayfası
- [ ] Mevcut login taslağı göster
- [ ] Livewire entegrasyonu tasarımı
- [ ] Cihaz limiti kontrolü
- [ ] Hesap kilidi kontrolü
- [ ] 2FA yönlendirme
- [ ] Kod uygulama

### 7. Register Sayfası
- [ ] Mevcut register taslağı göster
- [ ] Livewire entegrasyonu tasarımı
- [ ] Kurumsal kod ile kayıt
- [ ] Onay bekleme sayfası
- [ ] Kod uygulama

### 8. Profil Sayfası
- [ ] Mevcut profil taslağı göster
- [ ] Yeni sekmeler tasarımı:
  - [ ] Hesap Bilgileri
  - [ ] Güvenlik (şifre, 2FA)
  - [ ] Cihazlarım
  - [ ] Aboneliğim
- [ ] Kod uygulama

### 9. Cihaz Yönetimi Sayfası
- [ ] Aktif cihazlar listesi tasarımı
- [ ] Cihaz çıkarma işlevi
- [ ] Kod uygulama

### 10. Abonelik Durumu Sayfası
- [ ] Mevcut abonelik bilgisi tasarımı
- [ ] Plan değiştirme seçenekleri
- [ ] Ödeme geçmişi
- [ ] Kod uygulama

### 11. Pricing Sayfası
- [ ] Plan kartları tasarımı
- [ ] Özellik karşılaştırma
- [ ] Kupon uygulama alanı
- [ ] Kod uygulama

### 12. Checkout Sayfası
- [ ] Sipariş özeti tasarımı
- [ ] Ödeme formu (PayTR)
- [ ] Fatura bilgileri
- [ ] Kod uygulama

### 13. 2FA Kurulum Sayfası
- [ ] Telefon numarası girişi tasarımı
- [ ] SMS doğrulama tasarımı
- [ ] Kod uygulama

### 14. 2FA Doğrulama Sayfası
- [ ] Kod girişi tasarımı
- [ ] Yeniden gönder butonu
- [ ] Kod uygulama

---

## ENTEGRASYONLAR

### 15. PayTR Entegrasyonu
- [ ] Config ayarları
- [ ] PaymentService güncelleme
- [ ] Callback handler
- [ ] Test

### 16. SMS Entegrasyonu (2FA)
- [ ] SMS provider seçimi
- [ ] SmsService oluşturma
- [ ] Test

### 17. Mail Template'leri
- [ ] Hoşgeldin maili
- [ ] Onay bekleme maili
- [ ] Onay verildi maili
- [ ] Abonelik başladı maili
- [ ] Abonelik bitiyor maili
- [ ] Ödeme başarılı maili
- [ ] Ödeme başarısız maili
- [ ] Kurumsal davet maili

---

## CRON JOBS

### 18. Zamanlanmış Görevler
- [ ] CheckTrialExpiryCommand test
- [ ] SendRenewalRemindersCommand oluştur
- [ ] ProcessRecurringPaymentsCommand oluştur
- [ ] CleanupExpiredSessionsCommand oluştur
- [ ] Scheduler kayıt

---

## TEST & QA

### 19. Test
- [ ] Kayıt akışı testi
- [ ] Login akışı testi
- [ ] Abonelik satın alma testi
- [ ] Kupon uygulama testi
- [ ] Cihaz limiti testi
- [ ] 2FA testi
- [ ] Kurumsal hesap testi

---

## NOTLAR

- Her adımda taslak HTML: `public/readme/2025/11/24/[konu]/`
- Onay sonrası kod yazılacak
- Sıralı ilerleme zorunlu

---

**Son Güncelleme:** 2025-11-24 06:45

---

## 🆕 24 KASIM GÜNCELLEMELERİ

### Kurumsal Hesap Mimarisi Değişikliği
- ✅ **parent_id self-referencing** mimarisi uygulandı
- ✅ users tablosu **UNIVERSAL** kaldı (corporate_account_id silindi)
- ✅ Üyeler de muzibu_corporate_accounts tablosunda
- ✅ Fatura adresi cart_addresses tablosundan alınıyor

### Tamamlanan İşler
1. Migration cleanup (3 dosya → 1 dosya)
2. MuzibuCorporateAccount Model güncelleme
3. CorporateService tamamen yeniden yazıldı
4. Admin UI (Alpine.js kod üretimi, 6 karakter)
5. Database constraint düzeltme (tüm tenantlar)

**İlerleme:** Backend %100, Genel %77
