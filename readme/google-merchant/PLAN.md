# Google Merchant Center Kurulum Planı

## 📋 YAPILACAKLAR LİSTESİ

### 1. FEED İYİLEŞTİRMESİ (Kod - Claude yapacak)
- [ ] Ürün görselleri ekle (featured_image + gallery)
- [ ] GTIN (barkod) ekle
- [ ] MPN (model numarası) ekle
- [ ] Google kategori ekle
- [ ] Stok kontrolü düzelt (dinamik)
- [ ] İndirim fiyatı ekle
- [ ] Ek görseller ekle
- [ ] Product type (kendi kategoriniz) ekle

### 2. MERCHANT CENTER HESAP (Sen yapacaksın)
- [ ] Google Merchant Center'a gir
- [ ] Hesap oluştur
- [ ] İşletme bilgileri gir
- [ ] Web sitesi doğrulama kodu al

### 3. DOMAIN DOĞRULAMA (Birlikte)
- [ ] Doğrulama kodunu head'e ekle (Claude yapacak)
- [ ] Merchant Center'da "Verify" butonuna bas (Sen yapacaksın)

### 4. FEED EKLEME (Birlikte)
- [ ] Feed URL'ini Merchant Center'a ekle (Sen yapacaksın)
- [ ] Günlük otomatik fetch ayarla (Sen yapacaksın)

### 5. HATA DÜZELTİMİ (Birlikte)
- [ ] Merchant Center hata ekranını göster (Sen yapacaksın)
- [ ] Hataları düzelt (Claude yapacak)
- [ ] Test et ve tekrarla

---

## 🎯 SEN NE YAPACAKSIN (Basit Adımlar)

### ADIM 1: Merchant Center'a Git
1. https://merchants.google.com adresine git
2. Gmail hesabınla giriş yap
3. "Get Started" veya "Hesap Oluştur" tıkla

### ADIM 2: Ekran Görüntüsü Gönder
- Gördüğün HTML sayfasını bana gönder
- Ben sana ne yazacağını söyleyeceğim

### ADIM 3: Domain Doğrulama
- Merchant Center'da "Verify website" kısmına gel
- HTML tag yöntemini seç
- Çıkan kodu KOPYALAMA, bana göster
- Ben kodu ekleyeceğim
- Sen "Verify" butonuna basacaksın

### ADIM 4: Feed URL Ekle
- Products → Feeds → "+" tıkla
- Country: Turkey
- Language: Turkish
- Feed URL: `https://ixtif.com/productfeed`
- Fetch: Daily, 03:00
- Kaydet

### ADIM 5: Hata Kontrolü
- Feed yüklendikten sonra hata varsa bana göster
- Ben düzelteceğim

---

## 💻 BEN NE YAPACAĞIM (Teknik Kod)

### Yapacağım Değişiklikler:
1. `GoogleShoppingFeedController.php` dosyasını güncelleyeceğim
2. Eksik alanları ekleyeceğim:
   - Ürün görselleri (MediaManagement)
   - Barkod/Model numarası
   - Google kategori mapping
   - Dinamik stok durumu
   - İndirim fiyatı

### Oluşturacağım Dosyalar:
- `GoogleProductCategoryMapper.php` - Kategori eşleştirme servisi
- `google-categories.json` - Kategori taxonomy verisi
- Test için örnek feed

---

## 📝 ÖNEMLİ NOTLAR

### Feed URL'in:
```
https://ixtif.com/productfeed
```

### Merchant Center Ayarları:
- **Ülke:** Turkey (TR)
- **Dil:** Turkish (tr)
- **Para Birimi:** TRY
- **Fetch Sıklığı:** Günlük (Daily)

### Google Ads Bağlantısı:
- İsteğe bağlı
- Daha sonra yapılabilir
- Performance Max kampanyalar için gerekli

---

## 🚀 BAŞLAMAK İÇİN

**Şimdi ne yapmalısın:**
1. https://merchants.google.com adresine git
2. Gördüğün ekranı bana göster
3. Ben sana yol göstereceğim

**Ben ne yapmalıyım:**
- Onayını bekle
- Feed kodunu iyileştireyim
