# 📦 LITEF PROJESİNDEN VERİ AKTARIMI - ANALİZ VE PLAN

**Tarih**: 2025-10-13 22:30
**ID**: litef-import-analysis

---

## 🎯 HEDEF

Litef projesindeki (eski PHP sistemi) ürün ve kategori verilerini Laravel CMS sistemimize aktarmak.

---

## 📊 LİTEF PROJESİ ANALİZ SONUÇLARI

### Database Bilgileri
- **Host**: localhost
- **Database**: litef_ekim23
- **User**: litef_ekim23
- **SQL Dump**: `/Users/nurullah/Desktop/cms/litef/litef_ekim23.sql` (75 MB)

### Ürün Verileri
- **Tablo**: `mod_digishop`
- **Toplam Ürün**: ~108+ INSERT statement (her satırda birden fazla ürün olabilir)
- **Kategori Tablosu**: `mod_digishop_categories`
- **İlişki Tablosu**: `mod_digishop_related_categories` (çoka-çok)

### Fotoğraflar
- **Konum**: `/Users/nurullah/Desktop/cms/litef/modules/digishop/dataimages/`
- **Toplam Dosya**: 3,985 adet
- **Format**: JPG + WebP (her ürün için 2 format)
- **Boyut**: ~1 GB (dataimages.zip mevcut)

### Litef Tablo Yapısı

#### mod_digishop (Ürünler)
```sql
- id
- cid (kategori ID - tek kategori ilişkisi)
- product_code
- marka
- seri
- model
- title_tr
- slug
- price, dprice
- price_type
- short_desc_tr
- body_tr, body2_tr
- gallery (int)
- thumb, thumb_1, thumb_2, thumb_3, thumb_4, thumb_5, thumb_6, l_thumb
- catalog (PDF URL)
- catalog_en
- metakey_tr, metadesc_tr
- active
- showcase
- sorting
- created
- all_hits, daily_hits, last_visit
- k_all_hits, k_daily_hits, k_last_visit
- i_all_hits, i_daily_hits, i_last_visit
```

#### mod_digishop_categories (Kategoriler)
```sql
- id
- parent_id (hiyerarşi)
- name_tr
- slug
- link
- thumb
- body_tr
- metakey_tr, metadesc_tr
- sorting
- active
- ikinciel, kiralik, teklif (özel flaglar)
- garanti
- telefon, whatsapp, email
- blog_linkle
```

#### mod_digishop_related_categories (İlişki)
```sql
- pid (product id)
- cid (category id)
```

---

## 🏗️ LARAVEL CMS SİSTEMİMİZ

### Shop Tabloları

#### shop_products
- **JSON Çoklu Dil**: title, slug, short_description, body
- **SKU Sistemi**: Benzersiz ürün kodu
- **Varyant Sistemi**: parent_product_id, is_master_product, variant_type
- **Zengin İçerik**: technical_specs, features, highlighted_features, accessories, certifications
- **Medya**: media_gallery (JSON), video_url, manual_pdf_url
- **SEO**: Universal SEO sistemi ile entegre
- **Fotoğraflar**: Ayrı tablo (shop_product_images)

#### shop_categories
- **JSON Çoklu Dil**: title, slug, description
- **Hiyerarşi**: parent_id, level, path
- **SEO**: Universal SEO sistemi ile entegre

#### shop_product_images (Ayrı tablo)
- **Alan Yapısı**: product_id, image_url, alt_text, sort_order, is_primary, type

### Mevcut Seeder Durumu
- **Konum**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/database/seeders/`
- **Toplam**: 283 seeder dosyası
- **Pattern**: `[SKU]_[Tip]_[Aşama]_[İsim].php`
  - Örnek: `CPD15TVL_Forklift_1_Master.php`
- **Durum**: Fotoğrafları YOK - Sadece placeholder data

---

## 🔄 ALAN KARŞILAŞTIRMASI

| Litef | Laravel | Dönüşüm Notu |
|-------|---------|--------------|
| `id` | `product_id` | Auto increment |
| `cid` | `category_id` | Foreign key |
| `product_code` | `sku` | **Unique constraint** |
| `marka` | `brand_id` | shop_brands tablosundan match |
| `model` | `model_number` | Direkt kopyala |
| `title_tr` | `title->tr` | JSON'a dönüştür |
| `slug` | `slug->tr` | JSON'a dönüştür |
| `short_desc_tr` | `short_description->tr` | JSON'a dönüştür |
| `body_tr` | `body->tr` | JSON'a dönüştür, HTML temizle |
| `body2_tr` | `body->tr` (append) | İkinci açıklama birleştir |
| `thumb, thumb_1-6` | shop_product_images | **Ayrı tablo + fiziksel kopyalama** |
| `catalog` | `manual_pdf_url` | PDF dosyası kopyala |
| `price` | `base_price` | Decimal dönüşümü |
| `active` | `is_active` | Boolean |
| `showcase` | `is_featured` | Boolean |
| `sorting` | Custom logic | Opsiyonel |
| `metakey_tr` | SEO Management | Universal SEO sistemine aktar |
| `metadesc_tr` | SEO Management | Universal SEO sistemine aktar |

---

## 🚀 UYGULAMA PLANI

### Adım 1: Veritabanı Bağlantısı Kurma
**Görev**: Litef veritabanına Laravel'den bağlanma
- [ ] `config/database.php` - Litef için yeni connection ekle
- [ ] Test bağlantısı

### Adım 2: Kategori Aktarımı
**Görev**: mod_digishop_categories → shop_categories
- [ ] Litef kategorileri çek
- [ ] Hiyerarşiyi koru (parent_id mapping)
- [ ] JSON formatına dönüştür (title, slug, description)
- [ ] Kategori mapping tablosu oluştur (litef_id => laravel_id)

### Adım 3: Marka Eşleştirme
**Görev**: Litef'teki marka alanını shop_brands ile eşleştir
- [ ] Unique marka listesi çıkar
- [ ] shop_brands'ta eksik markaları oluştur
- [ ] Marka mapping tablosu oluştur

### Adım 4: Ürün Aktarımı
**Görev**: mod_digishop → shop_products
- [ ] Litef ürünlerini çek
- [ ] Alan mapping'i uygula
- [ ] Tekrarlanan ürünleri kontrol et (mevcut seederlarda var mı?)
- [ ] **Sadece litef'te olup bizde olmayan ürünleri** aktar
- [ ] SKU çakışması kontrolü
- [ ] JSON dönüşümleri yap
- [ ] HTML içerik temizleme

### Adım 5: Fotoğraf Aktarımı
**Görev**: dataimages/ → storage/app/public/shop/products/
- [ ] Ürün bazında fotoğraf eşleştir
- [ ] Fiziksel dosyaları kopyala
- [ ] shop_product_images tablosuna kaydet
- [ ] Thumbnail oluştur (Laravel Image intervention)
- [ ] WebP dönüşümü (opsiyonel - zaten var)
- [ ] is_primary flag'i ayarla (thumb = primary)

### Adım 6: PDF Katalog Aktarımı
**Görev**: Katalog PDF'lerini kopyala
- [ ] catalog alanındaki PDF'leri bul
- [ ] `/Users/nurullah/Desktop/cms/litef/modules/digishop/datafiles/` kontrol et
- [ ] `storage/app/public/shop/catalogs/` klasörüne kopyala
- [ ] manual_pdf_url alanını güncelle

### Adım 7: Seeder Fotoğraf Ekleme
**Görev**: Mevcut seederlara fotoğraf ekle
- [ ] Seeder dosyalarını aç
- [ ] SKU ile litef ürünlerini eşleştir
- [ ] Fotoğrafları shop_product_images'a ekle
- [ ] media_gallery JSON'ını güncelle

### Adım 8: İlişki Tablosu (Opsiyonel)
**Görev**: mod_digishop_related_categories - Çoka-çok kategori
- [ ] Laravel'de çoka-çok ilişki gerekiyor mu?
- [ ] Gerekirse pivot tablo oluştur

### Adım 9: Doğrulama ve Test
- [ ] Aktarılan ürün sayısı kontrolü
- [ ] Fotoğraf eksik olan ürünler?
- [ ] Kategori ataması doğru mu?
- [ ] Frontend'de ürünler görünüyor mu?
- [ ] SEO verileri aktarıldı mı?

---

## 💡 ÖNEMLİ NOTLAR

1. **VERİTABANI GÜVENLİĞİ**:
   - ⚠️ `migrate:fresh --seed` YAPMA!
   - ⚠️ Manuel INSERT'ler önce test et
   - ✅ Transaction kullan (rollback için)

2. **FOTOĞRAF YÖNETİMİ**:
   - WebP formatları zaten var (boyut optimizasyonu)
   - JPG + WebP ikisini de koru
   - Thumbnail: 300x300
   - Medium: 800x800
   - Large: 1200x1200

3. **MEVCUT SEEDERLAR**:
   - 283 seeder'ın çoğu fotoğrafsız
   - SKU ile eşleştir
   - Litef'teki fotoğrafları ekle

4. **ÇİFT DİL SİSTEMİ**:
   - Litef: Sadece Türkçe (title_tr, body_tr)
   - Laravel: JSON çoklu dil
   - EN çevirileri daha sonra eklenebilir

5. **PERFORMANS**:
   - Chunk kullan (1000'er ürün)
   - Queue kullan (fotoğraf kopyalama için)
   - Progress bar ekle

---

## 🛠️ TEKNİK YAKLIŞIM

### Artisan Command Oluştur
```bash
php artisan make:command ImportLitefProducts
```

### Command Yapısı
```php
- connectToLitef()
- importCategories()
- importBrands()
- importProducts()
  - filterExistingProducts()
  - transformToLaravelFormat()
  - saveProducts()
- importImages()
  - copyPhysicalFiles()
  - createThumbnails()
  - saveToDatabase()
- importCatalogs()
- updateSeeders()
- validateImport()
```

---

## ❓ SORULAR VE KARARLAR

1. **Kategori Eşleştirme**:
   - Litef'teki kategorilerle bizim kategoriler aynı mı?
   - Farklıysa manual mapping gerekir mi?

2. **Marka Eşleştirme**:
   - Litef'teki "marka" alanı shop_brands'ta var mı?
   - Yoksa otomatik oluşturulsun mu?

3. **Fiyat Stratejisi**:
   - Litef'teki fiyatlar güncel mi?
   - "price_on_request" olarak mı aktarılsın?

4. **SEO Verileri**:
   - metakey_tr, metadesc_tr → Universal SEO'ya aktar
   - SEO Management modülü hazır mı?

5. **HTML Temizleme**:
   - body_tr'deki HTML'de special char sorunları var mı?
   - Purifier kullanılsın mı?

---

## 📝 SONRAKI ADIMLAR

1. ✅ Analiz tamamlandı
2. ⏳ Kullanıcıdan onay al
3. ⏳ Import Command'ı oluştur
4. ⏳ Test ortamında dene
5. ⏳ Canlıya aktar

---

**Hazırlayan**: Claude
**Durum**: Analiz tamamlandı - Kullanıcı onayı bekleniyor
