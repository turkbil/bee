# 📊 Kategori Mapping - EP PDF → Veritabanı

**Tarih**: 2025-10-09
**Kaynak**: Veritabanı (shop_categories.sql)

---

## 🎯 ANA KATEGORİLER (Level 1)

Veritabanındaki ana kategoriler (parent_id = NULL):

| category_id | Title (TR) | Title (EN) | sort_order | Durum |
|-------------|------------|------------|------------|-------|
| **163** | FORKLİFTLER | FORKLIFTS | 1 | ✅ Aktif |
| **165** | TRANSPALETLER | PALLET TRUCKS | 9 | ✅ Aktif |
| **45** | İSTİF MAKİNELERİ | STACKERS | 13 | ✅ Aktif |
| **183** | REACH TRUCK | REACH TRUCK | 14 | ✅ Aktif |
| **184** | ORDER PICKER | ORDER PICKER | 15 | ✅ Aktif |
| **185** | TOW TRUCK | TOW TRUCK | 16 | ✅ Aktif |
| **164** | İKİNCİ (2.) EL | SECOND HAND | 17 | ✅ Aktif |
| **50** | YEDEK PARÇA | SPARE PARTS | 28 | ✅ Aktif |
| **167** | ARŞİV | ARCHIVE | 128 | ❌ Pasif |

---

## 🗂️ EP PDF → KATEGORİ EŞLEŞMESİ

### PDF Klasörleri:
```
/EP PDF/
├── 1-Forklift
├── 2-Transpalet
├── 3-İstif Makineleri
├── 4-Order Picker - Dikey Sipariş Toplayıcılar
├── 5-Otonom
└── 6-Reach Truck
```

### Eşleşme Tablosu:

| PDF Klasör | Veritabanı Kategori | category_id | Notlar |
|------------|---------------------|-------------|--------|
| **1-Forklift** | FORKLİFTLER | **163** | ✅ Direkt eşleşme |
| **2-Transpalet** | TRANSPALETLER | **165** | ✅ Direkt eşleşme |
| **3-İstif Makineleri** | İSTİF MAKİNELERİ | **45** | ✅ Direkt eşleşme |
| **4-Order Picker** | ORDER PICKER | **184** | ✅ Direkt eşleşme |
| **5-Otonom** | - | - | ❌ Veritabanında yok! |
| **6-Reach Truck** | REACH TRUCK | **183** | ✅ Direkt eşleşme |

---

## ⚠️ SORUN: OTONOM KATEGORİSİ

**Problem**: "5-Otonom" klasörü var ama veritabanında karşılığı yok!

**Çözüm Seçenekleri**:

### Seçenek 1: Yeni Ana Kategori Oluştur ✅ (ÖNERİLEN)
```sql
INSERT INTO shop_categories (
    category_id, parent_id, title, slug, description,
    level, path, sort_order, is_active, show_in_menu, show_in_homepage,
    created_at, updated_at
) VALUES (
    186, -- category_id (sonraki sıra numarası)
    NULL, -- parent_id (ana kategori)
    JSON_OBJECT('tr', 'OTONOM SİSTEMLER', 'en', 'AUTONOMOUS SYSTEMS'),
    JSON_OBJECT('tr', 'otonom-sistemler', 'en', 'autonomous-systems'),
    JSON_OBJECT(
        'tr', 'AGV ve AMR otonom sistemleri',
        'en', 'AGV and AMR autonomous systems'
    ),
    1, -- level (ana kategori)
    '186', -- path
    16, -- sort_order (Order Picker'dan sonra, Tow Truck'tan önce)
    1, -- is_active
    1, -- show_in_menu
    1, -- show_in_homepage
    NOW(),
    NOW()
);
```

### Seçenek 2: Mevcut AMR Kategorisini Kullan
- Var olan AMR kategorisi var (category_id = 186) ama parent_id = 165 (Transpalet alt kategorisi)
- Uygun değil çünkü Otonom Sistemler ana kategori olmalı

---

## 🔧 ALT KATEGORİLER (Level 2-4)

### FORKLİFTLER (163) Alt Kategorileri:

| category_id | Title (TR) | Level | parent_id |
|-------------|------------|-------|-----------|
| **154** | ELEKTRİKLİ FORKLİFTLER | 2 | 163 |
| **41** | DİZEL FORKLİFTLER | 2 | 163 |

### ELEKTRİKLİ FORKLİFTLER (154) Alt Kategorileri:

| category_id | Title (TR) | Level | parent_id |
|-------------|------------|-------|-----------|
| **178** | LİTYUM AKÜLÜ FORKLİFTLER | 3 | 154 |
| **179** | TRAKSİYONER AKÜLÜ FORKLİFTLER | 3 | 154 |

### LİTYUM AKÜLÜ FORKLİFTLER (178) Alt Kategorileri:

| category_id | Title (TR) | Level | parent_id |
|-------------|------------|-------|-----------|
| **180** | 3 TEKERLİ LİTYUM AKÜLÜ | 4 | 178 |
| **181** | 4 TEKERLİ LİTYUM AKÜLÜ | 4 | 178 |
| **182** | YÜKSEK TONAJLI LİTYUM AKÜLÜ | 4 | 178 |

### TRANSPALETLER (165) Alt Kategorileri:

| category_id | Title (TR) | Level | parent_id |
|-------------|------------|-------|-----------|
| **44** | ELEKTRİKLİ TRANSPALETLER | 2 | 165 |
| **166** | MANUEL TRANSPALETLER | 2 | 165 |
| **186** | AMR | 2 | 165 |

---

## 📝 PROMPT İÇİN KATEGORİ MAPPING

### Basit Mapping (Ana Kategoriler):

```javascript
const pdfFolderToCategoryId = {
    "1-Forklift": 163,           // FORKLİFTLER
    "2-Transpalet": 165,         // TRANSPALETLER
    "3-İstif Makineleri": 45,    // İSTİF MAKİNELERİ
    "4-Order Picker": 184,       // ORDER PICKER
    "5-Otonom": 186,             // OTONOM SİSTEMLER (OLUŞTURULACAK!)
    "6-Reach Truck": 183         // REACH TRUCK
};
```

### Akıllı Mapping (Ürün Özelliklerine Göre):

**Forklift Alt Kategorileri**:
```javascript
// PDF'den tespit edilen özelliklere göre alt kategori seç
if (productType === "Forklift") {
    if (batteryType === "Li-Ion") {
        if (wheelCount === 3) {
            categoryId = 180; // 3 TEKERLİ LİTYUM AKÜLÜ
        } else if (wheelCount === 4) {
            categoryId = 181; // 4 TEKERLİ LİTYUM AKÜLÜ
        } else if (capacity >= 5000) {
            categoryId = 182; // YÜKSEK TONAJLI LİTYUM AKÜLÜ
        } else {
            categoryId = 178; // LİTYUM AKÜLÜ FORKLİFTLER (genel)
        }
    } else if (batteryType === "Traction") {
        categoryId = 179; // TRAKSİYONER AKÜLÜ FORKLİFTLER
    } else if (fuelType === "Diesel") {
        categoryId = 41; // DİZEL FORKLİFTLER
    } else {
        categoryId = 154; // ELEKTRİKLİ FORKLİFTLER (genel)
    }
}
```

**Transpalet Alt Kategorileri**:
```javascript
if (productType === "Transpalet") {
    if (isElectric) {
        categoryId = 44; // ELEKTRİKLİ TRANSPALETLER
    } else {
        categoryId = 166; // MANUEL TRANSPALETLER
    }
}
```

---

## 🎯 ÖNERİLEN YAPILANDIRMA

### 1. Otonom Kategori Oluştur
```sql
-- Yeni ana kategori ekle
INSERT INTO shop_categories (...) VALUES (...); -- category_id = 186
```

### 2. Basit Mapping Kullan (İlk Aşama)
- PDF klasör adından ana kategori belirle
- Detaylı sınıflandırma manuel yapılabilir

### 3. Gelecekte: Akıllı Mapping
- AI ile ürün özelliklerini analiz et
- Otomatik alt kategori seçimi

---

## 📋 SQL PROMPT İÇİN GÜNCELLENMİŞ MAPPING

```sql
-- PDF Path'e göre category_id belirleme

-- 1-Forklift klasörü
category_id = 163 -- FORKLİFTLER (ana)
-- Detay: Li-Ion + 3 Tekerli → 180
-- Detay: Li-Ion + 4 Tekerli → 181
-- Detay: Dizel → 41

-- 2-Transpalet klasörü
category_id = 165 -- TRANSPALETLER (ana)
-- Detay: Elektrikli → 44
-- Detay: Manuel → 166

-- 3-İstif Makineleri klasörü
category_id = 45 -- İSTİF MAKİNELERİ

-- 4-Order Picker klasörü
category_id = 184 -- ORDER PICKER

-- 5-Otonom klasörü
category_id = 186 -- OTONOM SİSTEMLER (yeni oluşturulacak)

-- 6-Reach Truck klasörü
category_id = 183 -- REACH TRUCK
```

---

**Son Güncelleme**: 2025-10-09
**Durum**: ⚠️ Otonom kategorisi oluşturulmalı
