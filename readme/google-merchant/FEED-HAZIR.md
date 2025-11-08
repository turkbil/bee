# ✅ GOOGLE MERCHANT FEED HAZIR!

## 🎯 FEED BİLGİLERİ

**Feed URL:** `https://ixtif.com/googlemerchant`

**Ürün Sayısı:** 22 aktif ürün (fiyatlı + görselli)

**Format:** Google Shopping XML (RSS 2.0)

---

## ✅ EKLENMİŞ ÖZELLİKLER

### Zorunlu Alanlar
- ✅ Product ID
- ✅ Title
- ✅ Description
- ✅ Link
- ✅ **Image** (featured_image)
- ✅ Price (otomatik %20 indirim)
- ✅ Sale Price (gerçek fiyat)
- ✅ Availability (her zaman "in stock")
- ✅ Condition (her zaman "new")
- ✅ Brand

### Önerilen Alanlar
- ✅ **Additional Images** (gallery - max 10)
- ✅ GTIN (varsa barcode)
- ✅ MPN (varsa model_number)
- ✅ identifier_exists (GTIN/MPN yoksa "no")
- ✅ **Google Product Category** (auto-detection)
- ✅ Product Type (kendi kategoriniz)
- ✅ Shipping info

### Custom Labels (Performans için)
- ✅ **Label 0:** CE Sertifikalı
- ✅ **Label 1:** Hızlı Teslimat
- ✅ **Label 2:** B2B Özel
- ✅ **Label 3:** Stokta
- ✅ **Label 4:** Garanti bilgisi
  - Forklift: "2 Yıl Garanti + 5 Yıl Akü"
  - Diğer: "1 Yıl Garanti"

---

## 🎨 OTOMATİK ÖZELLİKLER

### 1. Otomatik %20 İndirim
**Nasıl Çalışıyor:**
- Ürünün `compare_at_price` varsa → Kullan
- Yoksa → `base_price × 1.20` hesapla
- **Google'da:** Normal fiyat + İndirimli fiyat gösterilir

**Örnek:**
```
Base Price: 25,500 USD
Compare At Price: Yok
↓
price: 30,600 USD (otomatik %20 ekle)
sale_price: 25,500 USD (gerçek fiyat)
```

### 2. Google Kategori Auto-Detection
**Keyword-based mapping:**
- `forklift` → Business & Industrial > Material Handling > Forklifts
- `transpalet` → Business & Industrial > Material Handling > Pallet Jacks & Stackers
- `akü` → Forklift & Lift Truck Parts & Accessories
- `yedek parça` → Parts & Accessories
- **Default:** Business & Industrial > Material Handling

### 3. Garanti Auto-Detection
**Kategori/başlık kontrolü:**
- "Forklift" içeriyorsa → 2 Yıl Garanti + 5 Yıl Akü
- Diğer ürünler → 1 Yıl Garanti

---

## 📊 FEED PERFORMANSI

**Filtreleme:**
- Sadece aktif ürünler (`is_active = 1`)
- Fiyatı olan ürünler (`base_price > 0`)
- Görseli olan ürünler (featured_image zorunlu)
- Fiyat gösterimi gizli olmayanlar (`price_display_mode != 'hide'`)

**Toplam Ürün:** 1020
**Feed'deki Ürün:** 22
**Filtreleme Oranı:** %2.1 (diğerleri fiyatsız/görselsiz)

---

## 🚀 SONRAKİ ADIMLAR

### 1. Google Merchant Center Hesap Aç
- https://merchants.google.com
- İşletme bilgileri gir
- Domain doğrulama yap

### 2. Feed'i Merchant Center'a Ekle
**Feed Ayarları:**
- Country: Turkey
- Language: Turkish
- Feed URL: `https://ixtif.com/googlemerchant`
- Fetch Frequency: Daily, 03:00

### 3. Domain Doğrulama
**HTML Tag yöntemi (önerilen):**
```html
<meta name="google-site-verification" content="XXXXXXXXXXXXXXX">
```
Ben bu kodu `<head>` tag'ine eklerim.

### 4. Feed Doğrulama
- Google otomatik kontrol yapacak
- Hata varsa bildireceğim
- Düzeltip tekrar test edeceğiz

---

## 🔧 TEKNİK DETAYLAR

**Controller:** `/Modules/Shop/app/Http/Controllers/GoogleShoppingFeedController.php`

**Service:** `/Modules/Shop/app/Services/GoogleProductCategoryMapper.php`

**Route:** `/routes/web.php`
```php
Route::middleware(['web', 'tenant'])->group(function () {
    Route::get('googlemerchant', [GoogleShoppingFeedController::class, 'index'])
        ->name('google.merchant.feed');
});
```

**Tenant Aware:** Evet (multi-tenant sistem)

**Cache:** Hayır (her istekte fresh data)

---

## ⚠️ DİKKAT EDİLECEKLER

### Ürün Ekleme/Güncelleme
Feed otomatik güncellenir:
- Yeni ürün eklendiğinde
- Fiyat değiştiğinde
- Görsel eklendiğinde

**Google fetch:** Günlük (03:00)

### GTIN/MPN Ekleme
Ürün düzenlerken:
- **Barcode** alanı → GTIN
- **Model Number** alanı → MPN
- Yoksa sorun değil (`identifier_exists: no`)

### Kategori Mapping
**Manuel mapping eklemek için:**
`GoogleProductCategoryMapper.php` dosyasında `$categoryMap` array'ini güncelle:
```php
private static array $categoryMap = [
    1 => 'Business & Industrial > Material Handling > Forklifts',
    2 => 'Business & Industrial > Material Handling > Pallet Jacks',
    // ...
];
```

---

## 📝 ÖNEMLİ NOTLAR

1. **Feed URL değiştirme!** Google Merchant Center'da bu URL'i kullanacaksın.

2. **Ürün fiyatları:** Otomatik %20 indirim ekleniyor (Google'da indirimli gösterilir).

3. **Stok:** Her zaman "in stock" (B2B için uygun).

4. **Kategori:** Auto-detection çalışıyor ama manuel mapping daha iyi sonuç verir.

5. **Görseller:** Görseli olmayan ürünler feed'e dahil edilmiyor.

---

## ✅ HAZIR!

Feed hazır ve çalışıyor. Merchant Center hesabı aç, feed'i ekle, başla! 🚀

**Sorular için:** `/readme/google-merchant/` klasöründeki dökümanları oku.
