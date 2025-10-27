# 🤖 Claude Task Agent Sistemi - PDF to SQL

**Tarih**: 2025-10-09
**Sistem**: Claude Code built-in Task agents
**Avantaj**: PHP kod yazmaya gerek yok!

---

## 🎯 NEDEN CLAUDE AGENT?

### ✅ Avantajları

1. **Kod Yazmaya Gerek Yok**
   - Claude zaten PDF okuyabiliyor (multimodal)
   - Built-in agent sistemi var
   - Prompt ile yönlendirme yeterli

2. **Daha Hızlı**
   - PHP kod yazma yok
   - Debug yok
   - Direkt kullanıma hazır

3. **Daha Akıllı**
   - AI-powered decision making
   - Hata durumlarını anlıyor
   - Otomatik düzeltme yapabiliyor

### ❌ PHP Agent Dezavantajları

- Kod yazmak gerekir
- Test etmek gerekir
- Bakım gerektirir
- Claude API integration gerekir

---

## 🔄 CLAUDE AGENT WORKFLOW

### Basit Kullanım

```
Sen → Claude → Task Agent (general-purpose)
```

**Tek komut yeterli:**
```
"EP PDF klasöründeki tüm PDF'leri oku, her biri için SQL INSERT oluştur.
02-json-to-sql-insert.md prompt'unu kullan."
```

Claude otomatik olarak:
1. Klasörü tarar
2. Her PDF'i okur
3. Prompt'u uygular
4. SQL dosyalarını oluşturur

---

## 📋 KULLANIM ÖRNEKLERİ

### Örnek 1: Tek PDF İşle

```
"Şu PDF'i oku ve SQL INSERT oluştur:
/Users/nurullah/Desktop/cms/EP PDF/1-Forklift/CPD 15-18-20 TVL/02_CPD15-18-20TVL-EN-Brochure.pdf

Prompt kullan: readme/ecommerce/ai-prompts/02-json-to-sql-insert.md
SQL kaydet: readme/ecommerce/sql-inserts/

Önemli:
- Marka: İXTİF (EP değil!)
- Transpalet terminolojisi kullan
- İkna edici Türkçe dil (turkish-copywriting-guide.md'ye göre)"
```

### Örnek 2: Klasör İşle

```
"EP PDF/1-Forklift klasöründeki TÜM PDF'leri işle.
Her PDF için ayrı SQL dosyası oluştur.

Prompt: readme/ecommerce/ai-prompts/02-json-to-sql-insert.md
Çıktı: readme/ecommerce/sql-inserts/

Bu PDF'de 3 ürün varsa (CPD15, CPD18, CPD20), 3 ayrı SQL dosyası oluştur:
- CPD15TVL-insert.sql
- CPD18TVL-insert.sql
- CPD20TVL-insert.sql"
```

### Örnek 3: Tüm Kataloğu İşle

```
"EP PDF klasöründeki TÜM PDF'leri işle (recursive).

Klasörler:
- 1-Forklift
- 2-Transpalet (ÖNEMLİ: Terminoloji!)
- 3-İstif Makineleri
- 4-Sipariş Toplama
- 5-Otonom
- 6-Reach Truck

Her PDF için SQL oluştur.
İlerlemeyi raporla.
Hataları logla."
```

---

## 🎨 PROMPT STRATEJİSİ

### Master Prompt Yapısı

```
GÖREV: PDF'den SQL INSERT oluştur

INPUT: [PDF path]

ÇIKTI: SQL dosyası (readme/ecommerce/sql-inserts/)

PROMPT DOSYASI:
- Ana: readme/ecommerce/ai-prompts/02-json-to-sql-insert.md
- Dil Rehberi: readme/ecommerce/turkish-copywriting-guide.md

KURALLAR:
1. Marka = İXTİF (EP Equipment değil!)
2. Transpalet (Pallet truck/kamyon değil!)
3. İkna edici Türkçe (son kullanıcı odaklı)
4. İletişim bilgilerini doğal yerleştir:
   - Tel: 0216 755 4 555
   - Email: info@ixtif.com
   - Slogan: "İXTİF - Türkiye'nin İstif Pazarı"

ÇOKLU ÜRÜN:
- Eğer PDF'de 3 ürün varsa, 3 ayrı SQL dosyası oluştur
- Her ürün için benzersiz product_id kullan
- SKU unique olmalı

VALIDATION:
- SQL syntax kontrolü
- JSON syntax kontrolü
- Foreign key kontrolü
- Marka kontrolü (İXTİF mi?)
- Terminoloji kontrolü (Transpalet mi?)
```

---

## 🚀 ADıM ADıM KULLANIM

### 1. İlk Test (Tek PDF)

**Komut**:
```
"Şu PDF'i test et:
/Users/nurullah/Desktop/cms/EP PDF/1-Forklift/CPD 15-18-20 TVL/02_CPD15-18-20TVL-EN-Brochure.pdf

Bu PDF'de 3 ürün var (CPD15TVL, CPD18TVL, CPD20TVL).
Her biri için ayrı SQL dosyası oluştur.

Prompt: readme/ecommerce/ai-prompts/02-json-to-sql-insert.md

SQL dosyalarını kaydet:
readme/ecommerce/sql-inserts/

Bana şunları göster:
1. Kaç ürün buldu?
2. SQL dosyaları oluşturuldu mu?
3. Marka İXTİF mi kontrol et
4. Terminoloji doğru mu (Transpalet?)
5. İkna edici Türkçe kullanıldı mı?"
```

### 2. Validasyon

**Kontrol Et**:
```
"Oluşturulan SQL dosyalarını kontrol et:
readme/ecommerce/sql-inserts/CPD15TVL-insert.sql

Şunları doğrula:
1. ✅ Marka: İXTİF (brand_id = 1)
2. ✅ SQL Syntax geçerli mi?
3. ✅ JSON_OBJECT() doğru kullanılmış mı?
4. ✅ İkna edici Türkçe var mı?
5. ✅ İletişim bilgileri eklenmiş mi?
6. ✅ Foreign key'ler doğru mu?

Hata varsa düzelt ve yeni SQL oluştur."
```

### 3. Toplu İşlem

**Tüm Klasörü İşle**:
```
"EP PDF/1-Forklift klasöründeki TÜM PDF'leri işle.

Her PDF için:
1. Oku
2. Ürünleri tespit et
3. Her ürün için SQL oluştur
4. Validation yap
5. SQL kaydet

İlerleme raporu ver:
- Toplam PDF: ?
- Toplam Ürün: ?
- Başarılı: ?
- Hatalı: ?

Hataları ayrı logla."
```

---

## 📊 ÇIKTI YAPISI

### Başarılı İşlem

```
✅ CPD15-18-20TVL-EN-Brochure.pdf İşlendi

Bulunan Ürünler: 3
├── CPD15TVL
├── CPD18TVL
└── CPD20TVL

Oluşturulan Dosyalar:
├── CPD15TVL-insert.sql ✅
├── CPD18TVL-insert.sql ✅
└── CPD20TVL-insert.sql ✅

Validasyon:
├── Marka: İXTİF ✅
├── Terminoloji: Forklift ✅
├── SQL Syntax: Geçerli ✅
├── İkna Edici Türkçe: Var ✅
└── İletişim Bilgileri: Eklendi ✅
```

### Hata Durumu

```
❌ corrupted-file.pdf İşlem Hatası

Hata: PDF okunamadı
Sebep: Dosya bozuk veya şifreli

Çözüm: Manuel kontrol gerekli
```

---

## 🎯 ÖZEL DURUMLAR

### Çoklu Ürün PDF

**Tespit Stratejisi**:
```
PDF'de birden fazla ürün varsa:
1. Model numaralarını tespit et (CPD15, CPD18, CPD20)
2. Her modelin özelliklerini ayır
3. Ortak bilgileri paylaş (features, brand, category)
4. Farklı bilgileri ayır (capacity, dimensions, price)
5. Her ürün için ayrı SQL oluştur
```

**Örnek**:
```
CPD 15-18-20 TVL Brochure.pdf
↓
3 ayrı ürün tespit edildi
↓
Ortak: Brand (İXTİF), Category (Forklift), Features
Farklı: Capacity (1500kg, 1800kg, 2000kg)
↓
CPD15TVL-insert.sql
CPD18TVL-insert.sql
CPD20TVL-insert.sql
```

### Terminoloji Düzeltme

**Otomatik Replace**:
```
PDF'den okunan → SQL'de yazılan
─────────────────────────────────
"Pallet Truck" → "Transpalet"
"Pallet Kamyon" → "Transpalet"
"EP Equipment" → "İXTİF"
"Stacker" → "İstif Makinesi"
"Order Picker" → "Sipariş Toplama Makinesi"
```

---

## 💡 İPUÇLARI

### 1. İlk Test Küçük Başla

❌ Kötü:
```
"Tüm 150 PDF'i işle"
```

✅ İyi:
```
"Önce 1 PDF test et → Sonra 5 PDF → Sonra tüm klasör"
```

### 2. Validation Her Adımda

```
PDF İşlendi
    ↓
SQL Oluşturuldu
    ↓
Validation ✅
    ↓
Sonraki PDF
```

### 3. Hata Logları Tut

```
readme/ecommerce/logs/
├── success.log (Başarılı işlemler)
├── errors.log (Hatalar)
└── validation.log (Validasyon hataları)
```

---

## 🔧 TROUBLESHOOTING

### Problem: PDF Okunamıyor

```
Çözüm:
1. Dosya bozuk mu kontrol et
2. Şifreli mi kontrol et
3. PDF versiyonu uygun mu?
4. Manuel olarak aç ve kontrol et
```

### Problem: SQL Syntax Hatası

```
Çözüm:
1. JSON_OBJECT() syntax'ı doğru mu?
2. Tek tırnak escape edilmiş mi? (')
3. UTF-8 karakterler doğru mu?
4. Foreign key'ler var mı?
```

### Problem: Marka EP Equipment Çıkıyor

```
Çözüm:
Prompt'a ekle:
"ÇOK ÖNEMLİ: Marka mutlaka İXTİF olmalı!
EP Equipment gördüğünde otomatik İXTİF'e çevir!"
```

---

## ✅ SONUÇ

### Claude Agent Sistemi ile:

✅ **Kod yazmaya gerek yok**
✅ **Hızlı prototipleme**
✅ **Kolay test**
✅ **Otomatik hata düzeltme**
✅ **AI-powered decision making**

### Kullanım:

```
1. PDF ver
2. Prompt ver (02-json-to-sql-insert.md)
3. Claude otomatik işler
4. SQL dosyaları hazır!
```

**Basit = Güçlü!** 🚀

---

**Son Güncelleme**: 2025-10-09
**Durum**: ✅ HAZIR - Test için bekleniyor
