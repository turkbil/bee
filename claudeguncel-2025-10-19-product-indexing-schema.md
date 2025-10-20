# 🗄️ SHOP_PRODUCTS TABLO YAPISI VE İNDEXLEME

**Tarih:** 2025-10-19
**Veritabanı:** tenant_ixtif
**Tablo:** shop_products
**Toplam Ürün:** 1,019 aktif ürün

---

## 📋 TABLO YAPISI (63 Kolon)

### ✅ İÇERİK KOLONLARI (Indexlenmesi Gerekenler)

#### 1. Temel Bilgiler
```sql
product_id          bigint(20) unsigned   PRIMARY KEY, AUTO_INCREMENT
sku                 varchar(255)          UNIQUE - Ürün kodu
model_number        varchar(255)          Model numarası
barcode             varchar(255)          Barkod
```

#### 2. Kategori ve İlişkiler
```sql
category_id         bigint(20) unsigned   Kategori ID
brand_id            bigint(20) unsigned   Marka ID
parent_product_id   bigint(20) unsigned   Ana ürün ID (varyantlar için)
is_master_product   tinyint(1)            Ana ürün mü?
variant_type        varchar(100)          Varyant tipi
```

#### 3. İçerik (JSON formatında - çok dilli)
```sql
title               longtext              {"tr": "Başlık", "en": "Title"}
slug                longtext              {"tr": "slug-tr", "en": "slug-en"}
short_description   longtext              Kısa açıklama (JSON)
body                longtext              Detaylı açıklama (HTML + JSON)
```

#### 4. AI ve Arama
```sql
embedding           longtext              AI semantic search embedding
embedding_model     varchar(50)           Default: text-embedding-3-small
```

#### 5. Ürün Tipi ve Durum
```sql
product_type        enum                  physical, digital, service, membership, bundle
condition           enum                  new, used, refurbished
is_active           tinyint(1)            Aktif mi?
is_featured         tinyint(1)            Öne çıkan mı?
is_bestseller       tinyint(1)            En çok satan mı?
```

#### 6. Fiyatlandırma
```sql
price_on_request    tinyint(1)            Fiyat talep üzerine mi?
base_price          decimal(12,2)         Temel fiyat
compare_at_price    decimal(12,2)         Karşılaştırma fiyatı
cost_price          decimal(12,2)         Maliyet fiyatı
currency            varchar(3)            Default: TRY
```

#### 7. Taksit ve Depozito
```sql
deposit_required    tinyint(1)            Depozito gerekli mi?
deposit_amount      decimal(12,2)         Depozito tutarı
deposit_percentage  int(11)               Depozito yüzdesi
installment_available tinyint(1)          Taksit var mı?
max_installments    int(11)               Maksimum taksit sayısı
```

#### 8. Stok Yönetimi
```sql
stock_tracking      tinyint(1)            Stok takibi var mı?
current_stock       int(11)               Mevcut stok
low_stock_threshold int(11)               Düşük stok eşiği (default: 5)
allow_backorder     tinyint(1)            Ön sipariş kabul edilir mi?
lead_time_days      int(11)               Tedarik süresi (gün)
```

#### 9. Fiziksel Özellikler
```sql
weight              decimal(10,2)         Ağırlık
dimensions          longtext              Boyutlar (JSON)
```

#### 10. Teknik Özellikler (JSON)
```sql
technical_specs     longtext              Teknik özellikler
features            longtext              Özellikler listesi
highlighted_features longtext             Öne çıkan özellikler
primary_specs       longtext              Ana özellikler
```

#### 11. Ek Bilgiler (JSON)
```sql
accessories         longtext              Aksesuarlar
certifications      longtext              Sertifikalar
use_cases           longtext              Kullanım alanları
faq_data            longtext              SSS
competitive_advantages longtext           Rekabet avantajları
target_industries   longtext              Hedef sektörler
warranty_info       longtext              Garanti bilgileri
shipping_info       longtext              Kargo bilgileri
```

#### 12. Medya
```sql
media_gallery       longtext              Medya galerisi (JSON)
video_url           varchar(255)          Video URL
manual_pdf_url      varchar(255)          Kullanım kılavuzu PDF URL
```

#### 13. Etiketler ve Özel Alanlar
```sql
tags                longtext              Etiketler (JSON array)
custom_json_fields  longtext              Özel JSON alanları
```

#### 14. İstatistikler
```sql
view_count          int(11)               Görüntülenme sayısı
sales_count         int(11)               Satış sayısı
sort_order          int(11)               Sıralama
```

---

### ❌ TIMESTAMP KOLONLARI (Indexlenmeyecek)

```sql
published_at        timestamp             Yayın tarihi
created_at          timestamp             Oluşturma tarihi
updated_at          timestamp             Güncelleme tarihi
deleted_at          timestamp             Silinme tarihi (soft delete)
embedding_generated_at timestamp          Embedding oluşturma tarihi
```

---

## 🔍 AI INDEXLEME STRATEJİSİ

### 1. Öncelikli Kolonlar (Arama için kritik)

**Yüksek Öncelik:**
- `title` - Ürün adı
- `slug` - URL-friendly isim
- `sku` - Ürün kodu
- `short_description` - Kısa açıklama
- `tags` - Etiketler

**Orta Öncelik:**
- `body` - Detaylı açıklama
- `technical_specs` - Teknik özellikler
- `features` - Özellikler
- `highlighted_features` - Öne çıkan özellikler
- `use_cases` - Kullanım alanları

**Düşük Öncelik:**
- `faq_data` - SSS
- `competitive_advantages` - Avantajlar
- `warranty_info` - Garanti
- `shipping_info` - Kargo

---

## 🤖 CHATBOT İÇİN ÖNEMLİ NOTLAR

### JSON Formatı
Çoğu kolon JSON formatında:
```json
{
  "tr": "Türkçe değer",
  "en": "English value"
}
```

### Örnek Ürün Verisi
```json
{
  "product_id": 114,
  "sku": "EFXZ-251",
  "title": {
    "tr": "İXTİF EFXZ 251 - 2.5 Ton Li-Ion Denge Ağırlıklı Forklift"
  },
  "slug": {
    "tr": "ixtif-efxz-251-25-ton-li-ion-denge-agirlikli-forklift"
  },
  "short_description": {
    "tr": "EFXZ 251, içten yanmalı gövdeden dönüştürülen..."
  },
  "base_price": 350000.00,
  "currency": "TRY"
}
```

---

## 🚨 KRİTİK KELİME EŞLEŞTİRMELERİ

### Yanlış Eşleştirmeleri Önle

**❌ ASLA KARIŞTIRMA:**
```
'terazili' ≠ 'denge ağırlıklı'
  ├─ 'terazili' = weighing scale = tartı özelliği
  └─ 'denge ağırlıklı' = counterbalanced = forklift tipi

'platform' ≠ 'palet'
  ├─ 'platform' = yükseltme platformu
  └─ 'palet' = ahşap/plastik taşıma paleti

'manuel' ≠ 'yarı elektrikli'
  ├─ 'manuel' = tamamen elle çalışan
  └─ 'yarı elektrikli' = kaldırma elektrikli, hareket manuel
```

**✅ EŞ ANLAMLILAR:**
```
'elektrikli' = 'akülü' = 'battery operated'
'soğuk depo' = 'cold storage' = 'ETC' (Extreme Temperature Conditions)
'paslanmaz' = 'SS' = 'stainless steel'
```

---

## 📊 MEVCUT DURUM ANALİZİ

### Veritabanı: tenant_ixtif

**Toplam Ürün:** 1,019 aktif ürün

**Terazili Ürün Durumu:**
```sql
SELECT COUNT(*) FROM shop_products
WHERE deleted_at IS NULL
AND (
  title LIKE '%terazi%' OR
  body LIKE '%terazi%' OR
  tags LIKE '%terazi%' OR
  features LIKE '%terazi%'
);
```
**Sonuç:** 0 ürün ❌

**Chatbot Davranışı:**
- Kullanıcı "terazili model" dediğinde → Ürün bulunamadı
- ✅ YENİ: İletişim bilgilerini göster (pozitif ton)
- ❌ ESKİ: "Denge ağırlıklı" ürün öneriyordu (yanlış!)

---

## 🛠️ YAPILAN GÜNCELLEMELER

### 1. OptimizedPromptService.php (Satır 154-186)

**Eklenen Kurallar:**
```php
⚠️ KRİTİK: YANLIŞ KELİME EŞLEŞTİRMELERİ YAPMA!
❌ 'terazili' (weighing scale) ≠ 'denge ağırlıklı' (counterbalanced)

Eğer kullanıcı 'terazili' dedi ve ürün listesinde
'terazi/tartı/weighing' kelimesi YOKSA:
→ ÜRÜN ÖNERME!
→ 'Ürün bulunamadı' mantığına geç
→ İletişim bilgilerini ver!
```

### 2. Semantic Matching Geliştirmeleri

**Slug Kontrolü:**
```
Kullanıcı 'terazili' dedi →
  Slug'da 'terazi/weighing/scale' ara
  YOKSA → İletişim bilgisi göster
```

**Özel Kısaltmalar:**
```
'ETC' = Extreme Temperature Conditions = Soğuk depo
'SS' = Stainless Steel = Paslanmaz çelik
'Scale/Weighing' = Terazili/Tartı özelliği
```

---

## ✅ SONUÇ

**Chatbot Davranışı (YENİ):**

1. **"Terazili model" araması:**
   ```
   İxtif olarak, 'terazili' konusunda size yardımcı olabiliriz! 😊

   Bu konuda detaylı bilgi almak için müşteri temsilcimizle görüşebilirsiniz:

   💬 WhatsApp: [link]
   📱 Telegram: [link]
   📧 E-posta: [link]
   📞 Telefon: [link]

   Size özel çözümler sunabiliriz!
   ```

2. **Yanlış eşleştirme engellendi:**
   - ❌ "Denge ağırlıklı" önerme
   - ✅ İletişim bilgisi gösterme

3. **Semantic matching iyileştirildi:**
   - Slug/title/body'de keyword arama
   - Özel kısaltma tanıma (ETC, SS, vb.)
   - Typo tolerance

---

## 📝 NOT

**Eğer gerçekten terazili ürün eklenirse:**
1. `tags` kolonuna "terazi", "tartı", "weighing" ekle
2. `features` veya `technical_specs` kolonuna "tartı özelliği" ekle
3. `title` veya `short_description`'a "terazili" kelimesini ekle
4. Chatbot otomatik olarak bulup önerecek!

---

**🎯 Sistem hazır! Artık yanlış ürün eşleştirmesi yapmayacak.**
