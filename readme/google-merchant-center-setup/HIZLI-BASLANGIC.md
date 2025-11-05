# ⚡ Google Merchant Center - Hızlı Başlangıç

**5 Dakikada Merchant Center'a Başla!**

---

## ✅ HAZIR OLAN SİSTEM BİLEŞENLERİ

Sistemde zaten mevcut olanlar:
- ✅ **Google Shopping Feed:** `https://ixtif.com/productfeed`
- ✅ **Otomatik ürün senkronizasyonu** (500 ürün)
- ✅ **Çoklu dil desteği** (TR/EN)
- ✅ **SSL sertifikası**
- ✅ **Tenant-aware sistem** (her domain kendi feed'ini üretir)

**FEED DETAYLARI:**
- Format: Google Shopping RSS/XML
- Güncelleme: Real-time (veritabanından direkt)
- Ürün sayısı: 500 (artırılabilir)
- Otomatik alanlar: ID, başlık, açıklama, link, fiyat, stok, marka

---

## 🚀 YAPILACAKLAR (Sırayla)

### 1️⃣ Merchant Center Hesabı Oluştur (10 dk)

**URL:** https://merchants.google.com

**Adımlar:**
1. Google hesabı ile giriş yap
2. İşletme adı: "IXTIF Endüstriyel Ekipman" (veya firma adınız)
3. Ülke: Türkiye
4. Web sitesi: `ixtif.com`

**Doğrulama Talebi Gelecek → Adım 2'ye geç**

---

### 2️⃣ Web Sitesi Doğrulama (5 dk)

Google bir **meta tag** verecek:
```html
<meta name="google-site-verification" content="XXXXXXX" />
```

**Ekleme Yeri:**
`Modules/Shop/resources/views/layouts/app.blade.php`

**Nasıl:**
```blade
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Merchant Center Verification -->
    <meta name="google-site-verification" content="BURAYA-GOOGLE-TAGINI-YAPISTIR" />

    <title>{{ $title ?? config('app.name') }}</title>
    ...
```

**Kaydet → Cache Temizle → Google'da Doğrula Butonuna Tıkla**

```bash
php artisan view:clear
php artisan responsecache:clear
npm run prod
```

---

### 3️⃣ Ürün Feed'i Ekle (5 dk)

**Merchant Center → Ürünler → Feedler → Feed Ekle**

**Ayarlar:**
- Feed türü: **Planlı getirme** (Scheduled fetch)
- Ülke: **Türkiye**
- Dil: **Türkçe**
- Feed adı: **"ixtif.com Google Shopping Feed"**
- Feed URL: **`https://ixtif.com/productfeed`**
- Fetch sıklığı: **Günlük** (her gün 03:00)

**"Şimdi getir" butonuna tıkla → 5 dakika bekle → Ürünler yüklenecek**

---

### 4️⃣ Politika Sayfaları Oluştur (20 dk)

**⚠️ KRİTİK: Bu sayfalar olmadan onay alamazsınız!**

**Gerekli Sayfalar:**
1. İade ve Değişim Politikası
2. Gizlilik Politikası
3. Kullanım Koşulları

**Nasıl Oluşturulur:**
- Admin → Page Management → Create New Page
- İçerik taslakları: `POLITIKA-SAYFALARI.md` dosyasında mevcut
- Footer'a linkler ekle

**URL'ler:**
```
https://ixtif.com/iade-ve-degisim-politikasi
https://ixtif.com/gizlilik-politikasi
https://ixtif.com/kullanim-kosullari
```

**Merchant Center'a Ekle:**
- Merchant Center → Ayarlar → Web sitesi → Politikalar
- Her 3 URL'yi ekle

---

### 5️⃣ Teslimat/Vergi Ayarları (10 dk)

**Merchant Center → Ayarlar → Gönderim ve İadeler**

**Teslimat:**
- Hizmet adı: "Standart Kargo"
- Süre: 3-7 iş günü
- Kargo ücreti: 50 TL (veya gerçek ücretiniz)
- Ücretsiz kargo eşiği: 1000 TL (opsiyonel)

**Vergi:**
- Merchant Center → Ayarlar → Vergi
- Türkiye KDV: %20
- Fiyatlara dahil: ✅

---

### 6️⃣ Google Ads'e Bağla (5 dk)

**Merchant Center → Ayarlar → Bağlantılı hesaplar → Google Ads**

1. Google Ads Müşteri Kimliği gir (123-456-7890 formatında)
2. İstek gönder
3. **Google Ads hesabına geç → Bağlantı isteğini onayla**

**Bağlantı durumu "Aktif" olmalı!**

---

### 7️⃣ Maksimum Performans Kampanyası (15 dk)

**Google Ads → Kampanyalar → Yeni Kampanya**

**Kampanya Türü:**
- Hedef: Satış
- Tür: **Maksimum Performans** (Performance Max)

**Ayarlar:**
- Kampanya adı: "ixtif.com - Maksimum Performans"
- Günlük bütçe: 100 TL (ayarlanabilir)

**Varlık Grubu:**
- Başlıklar: "Kaliteli Transpalet", "Endüstriyel Ekipman" (5-15 başlık)
- Açıklamalar: Firma tanıtımı (4-5 açıklama, 90 karakter)
- Görseller: Logo, banner, ürün görselleri

**Merchant Center Feed:**
- "ixtif.com Google Shopping Feed" seçin

**Hedef Kitle:**
- Coğrafi konum: Türkiye
- Dil: Türkçe

**Kampanyayı Yayınla!**

---

## ⏱️ TOPLAM SÜRE: ~70 Dakika

| Adım | Süre | Zorluk |
|------|------|--------|
| Merchant Center hesap | 10 dk | Kolay |
| Web doğrulama | 5 dk | Kolay |
| Feed ekleme | 5 dk | Kolay |
| Politika sayfaları | 20 dk | Orta |
| Teslimat/Vergi | 10 dk | Kolay |
| Google Ads bağlama | 5 dk | Kolay |
| Kampanya oluşturma | 15 dk | Orta |

---

## 📋 KONTROL LİSTESİ

**Merchant Center:**
- [ ] Hesap oluşturuldu
- [ ] Web sitesi doğrulandı (meta tag)
- [ ] Feed eklendi ve çalışıyor
- [ ] Ürünler "Aktif" durumda
- [ ] İade politikası sayfası oluşturuldu
- [ ] Gizlilik politikası sayfası oluşturuldu
- [ ] Kullanım koşulları sayfası oluşturuldu
- [ ] Footer'da linkler var
- [ ] Teslimat ayarları yapıldı
- [ ] Vergi ayarları yapıldı

**Google Ads:**
- [ ] Google Ads hesabı mevcut
- [ ] Merchant Center bağlantısı kuruldu
- [ ] Bağlantı onaylandı
- [ ] Maksimum Performans kampanyası oluşturuldu
- [ ] Varlık grubu tamamlandı
- [ ] Feed kampanyaya eklendi
- [ ] Kampanya yayında

---

## 🔍 HIZLI TEST

### Feed Çalışıyor mu?
```bash
curl -s https://ixtif.com/productfeed | head -50
```
**Beklenen:** XML formatında ürün listesi

### Ürün Sayısı
```bash
curl -s https://ixtif.com/productfeed | grep -o '<item>' | wc -l
```
**Beklenen:** ~500 ürün

### Politika Sayfaları
```bash
curl -I https://ixtif.com/iade-ve-degisim-politikasi
curl -I https://ixtif.com/gizlilik-politikasi
curl -I https://ixtif.com/kullanim-kosullari
```
**Beklenen:** HTTP/2 200 (her 3 sayfa da)

---

## ⚠️ SIK SORULAN SORULAR

### "Feed'de hiç ürün yok?"
**Sebep:** `base_price` NULL veya 0 olan ürünler feed'e eklenmez.
**Çözüm:** Ürün fiyatlarını kontrol et, en az 1 ürünün fiyatı olmalı.

### "Google meta tag'i bulamıyor?"
**Sebep:** Cache eski dosyayı gösteriyor.
**Çözüm:**
```bash
php artisan view:clear
php artisan responsecache:clear
npm run prod
```
Browser'da CTRL+F5 ile hard refresh yap.

### "Merchant Center onay vermiyor?"
**Sebep:** Politika sayfaları eksik veya footer'da link yok.
**Çözüm:** Her 3 sayfa da oluşturulmalı ve footer'da görünür link olmalı.

### "Google Ads bağlantısı beklemede?"
**Sebep:** Google Ads tarafında onay verilmemiş.
**Çözüm:** Google Ads → Araçlar → Bağlantılı hesaplar → Merchant Center → Onayla

---

## 📞 YARDIM

**Detaylı Dökümanlar:**
- `KURULUM-REHBERI.md` - Tam kurulum kılavuzu
- `POLITIKA-SAYFALARI.md` - Politika sayfası içerikleri

**Google Destek:**
- https://support.google.com/merchants
- https://support.google.com/google-ads

---

**Başarılar! 🚀**

**Hazırlayan:** Claude AI
**Tenant:** ixtif.com
**Tarih:** 2025-11-05
