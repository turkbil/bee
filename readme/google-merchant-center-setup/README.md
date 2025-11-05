# 🛒 Google Merchant Center Setup - İçindekiler

**Tenant:** ixtif.com
**Tarih:** 2025-11-05
**Durum:** Hazır - Kurulum için dokümantasyon tamamlandı

---

## 📁 DOSYALAR

### 1. HIZLI-BASLANGIC.md
**⚡ Buradan başla!**

En önemli adımları içeren 70 dakikalık hızlı kurulum kılavuzu:
- Merchant Center hesap oluşturma
- Web sitesi doğrulama (meta tag)
- Feed ekleme
- Politika sayfaları (özet)
- Google Ads bağlantısı
- Maksimum Performans kampanyası

**Kime göre:** Hızlıca başlamak isteyenler

---

### 2. KURULUM-REHBERI.md
**📚 Detaylı tam kılavuz**

Adım adım tüm kurulum süreci:
- Merchant Center onboarding (detaylı)
- Web sitesi doğrulama yöntemleri
- Feed konfigürasyonu (XML detayları)
- Politika sayfaları gereksinimleri
- Teslimat/Kargo ayarları
- Vergi konfigürasyonu
- Google Ads entegrasyonu (detaylı)
- Maksimum Performans kampanyası kurulumu
- Sorun giderme
- Performans takibi
- Optimizasyon önerileri

**Kime göre:** Teknik detayları öğrenmek isteyenler

---

### 3. POLITIKA-SAYFALARI.md
**📄 Gerekli sayfa içerikleri**

Google Merchant Center için zorunlu politika sayfalarının taslakları:
- İade ve Değişim Politikası (içerik taslağı)
- Gizlilik Politikası (KVKK uyumlu taslak)
- Kullanım Koşulları (hizmet şartları)
- Page modülü ile oluşturma adımları
- Footer'a link ekleme örnekleri

**Kime göre:** Politika sayfalarını oluşturacaklar

---

## 🎯 MEVCUT SİSTEM

### ✅ Hazır Bileşenler

Sistemde zaten mevcut olan özellikler:

**1. Google Shopping Feed**
- **URL:** `https://ixtif.com/productfeed`
- **Format:** Google Shopping RSS/XML
- **Ürün sayısı:** 500 (artırılabilir)
- **Güncelleme:** Real-time (veritabanından direkt)
- **Tenant-aware:** Her domain kendi feed'ini üretir

**2. Feed Controller**
- **Dosya:** `Modules/Shop/app/Http/Controllers/GoogleShoppingFeedController.php`
- **Route:** `/productfeed` (tenant middleware ile korumalı)
- **Dil desteği:** TR/EN (JSON çoklu dil)

**3. Public Feed Script**
- **Dosya:** `public/productfeed.php`
- **Özellik:** Tenant otomatik algılama
- **Brand JOIN:** Marka bilgisi dahil
- **Price handling:** "Price on request" desteği

**4. Otomatik Alanlar**
- ID (product_id)
- Başlık (title - JSON çoklu dil)
- Açıklama (body → short_description fallback)
- Link (slug - JSON çoklu dil)
- Fiyat (base_price + currency)
- Stok (her zaman "in stock")
- Durum (condition - new/used)
- Marka (brand_title)

---

## ❌ OLUŞTURULMASI GEREKENLER

### 1. Politika Sayfaları (Zorunlu)
- [ ] İade ve Değişim Politikası (`/iade-ve-degisim-politikasi`)
- [ ] Gizlilik Politikası (`/gizlilik-politikasi`)
- [ ] Kullanım Koşulları (`/kullanim-kosullari`)

**Nasıl:** `POLITIKA-SAYFALARI.md` dosyasındaki taslakları kullan

### 2. Footer Linkleri
- [ ] Politika sayfalarına footer'dan bağlantı ekle
- **Dosya:** `Modules/Shop/resources/views/layouts/app.blade.php`

### 3. Google Meta Tag (Doğrulama)
- [ ] Merchant Center'dan meta tag al
- [ ] Layout dosyasına `<head>` içine ekle
- [ ] Cache temizle, build yap

---

## 🚀 KURULUM ADIMLARI (Özet)

1. **Merchant Center Hesabı Oluştur** → https://merchants.google.com
2. **Web Sitesi Doğrula** → Meta tag ekle
3. **Feed Ekle** → `https://ixtif.com/productfeed`
4. **Politika Sayfaları Oluştur** → Page modülü
5. **Teslimat/Vergi Ayarla** → Merchant Center
6. **Google Ads'e Bağla** → Merchant Center + Google Ads
7. **Maksimum Performans Kampanyası** → Google Ads

**Toplam Süre:** ~70 dakika

---

## 📊 FEED DETAYLARI

### URL
```
https://ixtif.com/productfeed
```

### Format
```xml
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
  <channel>
    <title>IXTIF Endüstriyel Ekipman</title>
    <link>https://ixtif.com</link>
    <description>...</description>
    <item>
      <g:id>123</g:id>
      <g:title>1.5 Ton Transpalet</g:title>
      <g:description>...</g:description>
      <g:link>https://ixtif.com/shop/transpalet-1-5-ton</g:link>
      <g:price>15000.00 TRY</g:price>
      <g:availability>in stock</g:availability>
      <g:condition>new</g:condition>
      <g:brand>IXTIF</g:brand>
    </item>
    ...
  </channel>
</rss>
```

### Test Komutları
```bash
# Feed çıktısını görüntüle
curl -s https://ixtif.com/productfeed | head -100

# Ürün sayısını kontrol et
curl -s https://ixtif.com/productfeed | grep -o '<item>' | wc -l

# Feed'i dosyaya kaydet
curl -s https://ixtif.com/productfeed > google-feed.xml
```

---

## ⚠️ ÖNEMLİ NOTLAR

### 1. Tenant Context
- Feed otomatik olarak domain'den tenant'ı algılar
- Her tenant kendi ürünlerini feed'e ekler
- `ixtif.com` → Tenant ID: 2

### 2. Fiyat Kontrolü
- Sadece `base_price > 0` olan ürünler feed'e dahil edilir
- `price_on_request = true` olan ürünler fiyat göstermez
- Fiyatsız ürünler bazı kategorilerde kabul edilir

### 3. Dil Sistemi
- Tüm alanlar JSON çoklu dil formatında
- Feed Türkçe (`tr`) dilini önceliklendirir
- Fallback: `en` → `product_id` (slug için)

### 4. Cache
- Feed real-time (cache yok)
- Her istek direkt veritabanından çeker
- Performance: ~500 ürün < 1 saniye

---

## 🔧 TEKNİK DETAYLAR

### Controller Location
```
Modules/Shop/app/Http/Controllers/GoogleShoppingFeedController.php
```

### Route Definition
```php
Route::middleware(['web', 'tenant'])->group(function () {
    Route::get('productfeed', [GoogleShoppingFeedController::class, 'index']);
});
```

### Database Query
```php
DB::table('shop_products as p')
    ->leftJoin('shop_brands as b', 'p.brand_id', '=', 'b.brand_id')
    ->select('p.product_id', 'p.title', 'p.slug', 'p.base_price', 'b.title as brand_title')
    ->where('p.is_active', 1)
    ->whereNull('p.deleted_at')
    ->limit(500)
    ->get();
```

### JSON Parsing
```php
$titleData = json_decode($product->title, true);
$title = is_array($titleData) ? ($titleData['tr'] ?? $titleData['en'] ?? 'Product') : $product->title;
```

---

## 📞 DESTEK VE KAYNAKLAR

### Google Dökümanları
- **Merchant Center Help:** https://support.google.com/merchants
- **Google Ads Help:** https://support.google.com/google-ads
- **Feed Specification:** https://support.google.com/merchants/answer/7052112

### Sistem Dökümanları
- `readme/thumbmaker/README.md` - Görsel optimizasyonu
- `readme/tenant-olusturma.md` - Tenant yönetimi
- `CLAUDE.md` - Genel sistem talimatları

---

## ✅ BAŞARILI KURULUM SONRASI

### Merchant Center
- ✅ Hesap "Aktif" durumda
- ✅ Feed günlük güncelleniyor
- ✅ Ürünler "Onaylandı" durumunda
- ✅ Politika sayfaları doğrulandı

### Google Ads
- ✅ Merchant Center bağlantısı aktif
- ✅ Maksimum Performans kampanyası yayında
- ✅ Ürünler kampanyada görünüyor
- ✅ Gösterim/Tıklama alıyor

### Performans Takibi
- **Merchant Center Dashboard:** Gösterim, tıklama, CTR
- **Google Ads Dashboard:** Dönüşüm, harcama, ROAS
- **Google Analytics:** Detaylı kullanıcı davranışı

---

**Hazırlayan:** Claude AI
**Tenant:** ixtif.com
**Son Güncelleme:** 2025-11-05
**Durum:** ✅ Ready for deployment
