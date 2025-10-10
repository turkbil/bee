# 🤖 İXTİF E-Ticaret Ürün İçe Aktarma Agent Sistemi

**Tarih**: 2025-10-09
**Proje**: Shop Modülü - PDF'den Direkt SQL INSERT
**Marka**: İXTİF (Son kullanıcı satış - B2C)

---

## 🎯 AMAÇ

EP PDF klasöründeki tüm ürün broşürlerini **direkt SQL INSERT** olarak oluşturmak.

### ❌ JSON'a GEREK YOK!
PDF → SQL (tek adım, hızlı, basit)

---

## 🔧 SİSTEM MİMARİSİ

### Sadece 2 Agent

```
1. MASTER ORCHESTRATOR
   ├── Klasör tara (recursive)
   └── Her PDF için:
       │
       └── 2. PDF-TO-SQL AGENT
           ├── PDF oku (Claude multimodal)
           ├── Prompt ile analiz
           ├── Direkt SQL INSERT üret
           └── SQL dosyası kaydet
```

**Basit = Hızlı = Az hata!**

---

## 📋 ÖNEMLİ NOKTALAR

### 1️⃣ Marka: İXTİF
```sql
-- EP Equipment değil!
INSERT INTO shop_brands (brand_id, title, ...)
VALUES (1, JSON_OBJECT('tr', 'İXTİF', 'en', 'iXTİF'), ...);
```

### 2️⃣ Terminoloji: TRANSPALET
- ❌ "Pallet truck"
- ❌ "Pallet kamyon"
- ✅ **"Transpalet"**

```sql
-- Kategori
INSERT INTO shop_categories (category_id, title, ...)
VALUES (2, JSON_OBJECT('tr', 'Transpalet', 'en', 'Pallet Truck'), ...);
```

### 3️⃣ Son Kullanıcı Dili

| Teknik Terim | Son Kullanıcı Dili |
|--------------|-------------------|
| AC traction motor | Güçlü elektrikli motor |
| Load capacity | Taşıma kapasitesi |
| Turning radius | Dönüş çapı (dar alanlarda kolay hareket) |
| Operator comfort | Rahat kullanım |
| Gradeability | Rampa çıkma yeteneği |

**Örnek**:
```sql
-- ❌ Teknik
'features', JSON_ARRAY('Dual AC traction motors 2x5.0kW', '80V Li-Ion battery')

-- ✅ Son kullanıcı
'features', JSON_ARRAY(
    'Güçlü çift motorlu sistem - Ağır yükleri kolayca taşır',
    'Lityum batarya - Şarj başına 6 saat çalışma'
)
```

---

## 🔄 İŞ AKIŞI

### Master Orchestrator

```python
def process_all_pdfs():
    pdf_folder = "/Users/nurullah/Desktop/cms/EP PDF"
    output_folder = "/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/sql-inserts"

    for pdf_path in scan_recursive(pdf_folder):
        try:
            sql_content = pdf_to_sql_agent(pdf_path)
            save_sql(sql_content, output_folder)
            log_success(pdf_path)
        except Exception as e:
            log_error(pdf_path, e)

    generate_report()
```

### PDF to SQL Agent

```python
def pdf_to_sql_agent(pdf_path):
    # 1. PDF oku
    pdf_content = read_pdf_with_claude(pdf_path)

    # 2. Prompt ile analiz
    prompt = load_prompt("02-json-to-sql-insert.md")
    prompt += "\n\n ÖNEMLI:\n"
    prompt += "- Marka: İXTİF (EP değil!)\n"
    prompt += "- Transpalet kullan (pallet truck değil)\n"
    prompt += "- Son kullanıcı dili (teknik jargon yok)\n"

    # 3. SQL üret
    sql_insert = claude_generate(prompt, pdf_content)

    # 4. Validasyon
    validate_sql_syntax(sql_insert)
    validate_phase1_structure(sql_insert)

    return sql_insert
```

---

## 📁 KLASÖR YAPISI

### Giriş (EP PDF)
```
/Users/nurullah/Desktop/cms/EP PDF/
├── 1-Forklift/
│   ├── CPD 15-18-20 TVL/
│   │   └── 02_CPD15-18-20TVL-EN-Brochure.pdf → 3 ürün
│   └── ...
├── 2-Transpalet/  ← ÖNEMLİ: Transpalet!
├── 3-İstif Makineleri/
├── 4-Sipariş Toplama/
├── 5-Otonom/
└── 6-Reach Truck/
```

### Çıktı (SQL)
```
/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/sql-inserts/
├── CPD15TVL-insert.sql
├── CPD18TVL-insert.sql
├── CPD20TVL-insert.sql
└── ALL-PRODUCTS-insert.sql (hepsini birleştir)
```

---

## 📊 SQL YAPISI

### Her SQL Dosyası İçinde

```sql
-- =====================================================
-- İXTİF ÜRÜN: CPD15TVL
-- Kategori: Forklift > Akülü Forklift
-- PDF: 02_CPD15-18-20TVL-EN-Brochure.pdf
-- Tarih: 2025-10-09
-- =====================================================

SET FOREIGN_KEY_CHECKS=0;

-- 1. BRAND (Tek seferlik)
INSERT INTO shop_brands (...) VALUES (...)
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- 2. CATEGORY
INSERT INTO shop_categories (...) VALUES (...)
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- 3. PRODUCT
INSERT INTO shop_products (
    product_id, category_id, brand_id,
    sku, model_number,
    title, slug, short_description, long_description,
    technical_specs, features, highlighted_features,
    base_price, currency, price_on_request,
    weight, dimensions,
    stock_tracking, current_stock,
    warranty_info, tags,
    is_active, is_featured,
    created_at, updated_at
) VALUES (
    1, -- product_id
    11, -- category_id (Akülü Forklift)
    1, -- brand_id (İXTİF)
    'CPD15TVL',
    'CPD15TVL',
    JSON_OBJECT('tr', 'CPD15TVL Elektrikli Forklift', 'en', 'CPD15TVL Electric Forklift'),
    JSON_OBJECT('tr', 'cpd15tvl-elektrikli-forklift', 'en', 'cpd15tvl-electric-forklift'),
    JSON_OBJECT(
        'tr', '80V lityum bataryalı kompakt forklift. Dar alanlarda üstün performans.',
        'en', 'Compact forklift with 80V lithium battery. Superior performance in narrow spaces.'
    ),
    -- ... devamı
    NOW(),
    NOW()
);

-- 4. VARIANTS
INSERT INTO shop_product_variants (...) VALUES (...);
INSERT INTO shop_product_variants (...) VALUES (...);

SET FOREIGN_KEY_CHECKS=1;
```

---

## 🏷️ KATEGORİ YAPISI

```sql
-- Ana Kategoriler
1. Forklift
   └── 11. Akülü Forklift
   └── 12. Dizel Forklift
   └── 13. LPG Forklift

2. Transpalet  ← ÖNEMLİ!
   └── 21. Akülü Transpalet
   └── 22. Manuel Transpalet
   └── 23. Tartılı Transpalet

3. İstif Makinesi
   └── 31. Akülü İstif
   └── 32. Manuel İstif

4. Sipariş Toplama
   └── 41. Order Picker
   └── 42. Reach Truck

5. Otonom Sistemler
   └── 51. AGV
   └── 52. AMR
```

---

## 🎨 SON KULLANICI ODAKLI DİL

### Features (Özellikler)

**❌ Teknik Jargon**:
```json
"80V Li-Ion battery technology"
"Dual AC traction motors 2x5.0kW"
"394mm legroom for operator comfort"
```

**✅ Son Kullanıcı Dili**:
```json
"Şarj başına 6 saat kesintisiz çalışma"
"Güçlü çift motorlu sistem - Ağır yükleri kolayca taşır"
"Geniş ayak alanı - Rahat kullanım"
```

### FAQ (Sık Sorulan Sorular)

Son kullanıcının soracağı sorular:
- "Kaç saat çalışır?"
- "Ne kadar ağırlık taşır?"
- "Dar koridorlarda kullanılır mı?"
- "Garanti süresi ne kadar?"
- "Bakımı zor mu?"

```sql
'faq_data', JSON_ARRAY(
    JSON_OBJECT(
        'question', JSON_OBJECT('tr', 'Tam şarj süresi ne kadar?'),
        'answer', JSON_OBJECT('tr', '35A şarj cihazı ile 4-5 saat. Hızlı şarj opsiyonu mevcuttur.')
    ),
    JSON_OBJECT(
        'question', JSON_OBJECT('tr', 'Garanti süresi ne kadar?'),
        'answer', JSON_OBJECT('tr', '24 ay standart garanti. Batarya için ayrıca 24 ay.')
    )
)
```

---

## 🚀 KULLANIM

### Komutlar

```bash
# Tek PDF işle
php artisan shop:import-pdf "/Users/nurullah/Desktop/cms/EP PDF/1-Forklift/CPD.../brochure.pdf"

# Klasör işle
php artisan shop:import-folder "/Users/nurullah/Desktop/cms/EP PDF/1-Forklift"

# TÜM kataloğu işle
php artisan shop:import-all "/Users/nurullah/Desktop/cms/EP PDF"

# Dry run (test - SQL oluştur ama DB'ye ekleme)
php artisan shop:import-all "/Users/nurullah/Desktop/cms/EP PDF" --dry-run

# Sadece SQL üret (DB'ye yükleme)
php artisan shop:import-all "/Users/nurullah/Desktop/cms/EP PDF" --sql-only

# SQL'leri DB'ye yükle
php artisan shop:run-sql-inserts "/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/sql-inserts"
```

---

## 📊 ÇIKTI RAPORU

```
🤖 İXTİF Ürün İçe Aktarma Raporu
═══════════════════════════════════════════

📁 Kaynak: /Users/nurullah/Desktop/cms/EP PDF
📅 Tarih: 2025-10-09 14:30:25
⏱️  Süre: 25 dakika

📊 İSTATİSTİKLER
├── Toplam PDF: 147
├── Toplam Ürün: 289
├── SQL oluşturuldu: 289 dosya
├── Başarılı: 285
├── Hatalı: 4
└── Başarı Oranı: %98.6

📦 KATEGORİ
├── Forklift: 65 ürün
├── Transpalet: 89 ürün  ← Terminoloji doğru!
├── İstif Makinesi: 54 ürün
├── Sipariş Toplama: 42 ürün
├── Otonom: 18 ürün
└── Reach Truck: 21 ürün

✅ BAŞARI
├── İXTİF marka: ✓ 289 üründe doğru
├── Transpalet terminoloji: ✓ 89 üründe doğru
├── Son kullanıcı dili: ✓ Kontrol edildi
└── Phase 1 uyumluluk: ✓ %100

❌ HATA
├── PDF okunamadı: 2 dosya
├── SQL syntax hatası: 1 dosya
└── Validation hatası: 1 dosya

📁 ÇIKTILAR
├── SQL: /readme/ecommerce/sql-inserts/ (289 dosya)
├── Toplu SQL: ALL-PRODUCTS-insert.sql
└── Log: /storage/logs/import-2025-10-09.log

═══════════════════════════════════════════
✨ İşlem tamamlandı!
```

---

## ✅ CHECKLIST

### Agent Geliştirme
- [ ] MasterOrchestratorAgent.php
- [ ] PdfToSqlAgent.php

### Prompts
- [ ] 02-json-to-sql-insert.md güncelle (İXTİF + Transpalet + Son kullanıcı)

### Validation Rules
- [ ] Marka = İXTİF kontrolü
- [ ] "Pallet" kelimesi varsa → "Transpalet" uyarısı
- [ ] Teknik terim kontrolü (AI ile)
- [ ] SQL syntax kontrolü
- [ ] Foreign key kontrolü

### Test
- [ ] Tek PDF (CPD15TVL)
- [ ] Çoklu ürün PDF (CPD15-18-20TVL → 3 ürün)
- [ ] Transpalet klasörü (terminoloji kontrolü)
- [ ] Full catalog (150 PDF)

---

## 🎯 NEDEN SADECE SQL?

### ❌ JSON Neden Gereksiz?

1. **Ekstra adım**: PDF → JSON → SQL (2 adım yerine 1)
2. **Daha yavaş**: Her ürün için 2x Claude çağrısı
3. **Daha fazla hata riski**: 2 conversion, 2 validation
4. **Gereksiz dosya**: JSON'ları ne yapacağız?

### ✅ Direkt SQL Avantajları

1. **Hızlı**: PDF → SQL (tek adım)
2. **Basit**: 1 agent, 1 conversion, 1 validation
3. **Az hata**: Tek conversion = az hata riski
4. **Direkt kullanım**: SQL oluştuktan sonra direkt import

### Sonuç

```
PDF → JSON → SQL = 2 adım, yavaş ❌
PDF → SQL = 1 adım, hızlı ✅
```

**KISS Prensibi**: Keep It Simple, Stupid! 🚀

---

## 🔧 SQL IMPORT

### Manuel Import

```bash
# MySQL
mysql -u root -p database_name < CPD15TVL-insert.sql

# Laravel
php artisan db:seed --class=ManualProductSeeder
```

### Otomatik Import (Agent)

```bash
# Tüm SQL'leri yükle
php artisan shop:run-sql-inserts

# Belirli klasör
php artisan shop:run-sql-inserts --dir=sql-inserts/forklift
```

---

**Son Güncelleme**: 2025-10-09
**Durum**: 🟡 Planlama Aşaması
**Sonraki**: Agent kodlama başlasın mı?
