# 🤖 YAPAY ZEKA İÇİN PDF → JSON DÖNÜŞÜM REHBERİ

## 📋 GENEL BAKIŞ

Bu rehber, **herhangi bir AI programına** (ChatGPT, Claude, Gemini, vb.) PDF kataloglarını Shop System v2 JSON formatına dönüştürme işini nasıl yaptıracağınızı açıklar.

---

## 🎯 AMAÇ

EP Equipment PDF kataloglarını (Transpalet, Forklift, İstif Makinesi vb.) okuyup, e-ticaret sistemi için yapılandırılmış JSON dosyaları üretmek.

---

## 📂 DOSYA YAPISI VE ÖNCELIK SIRASI

AI programı aşağıdaki dosyaları **TAM BU SIRADA** okumalıdır:

### 1️⃣ **AI-PROMPT.md** (En Önemli - İLK OKUNACAK)
**Konum:** `/readme/shop-system-v2/AI-PROMPT.md`

**İçeriği:**
- JSON oluşturma kuralları
- Minimum içerik gereksinimleri (FAQ ≥10, use_cases ≥6, vb.)
- Türkçe dil zorunluluğu
- İletişim bilgileri (0216 755 3 555, info@ixtif.com)
- Variant sistemi kuralları

**Neden Önemli:**
Bu dosya AI'ın "beyin haritası"dır. Tüm kurallar burada.

---

### 2️⃣ **01-KATEGORI-SPECS.md** (Kategori Şablonları)
**Konum:** `/readme/shop-system-v2/01-KATEGORI-SPECS.md`

**İçeriği:**
- Her kategori için `primary_specs` template (4 kart)
- Transpalet için: Denge Tekeri, Li-Ion Akü, Şarj Cihazı, Standart Çatal
- Forklift için: Mast Tipi, Motor Gücü, Yük Merkezi, Kabin
- İstif Makinesi için: Yürüyüşlü/Sürücülü, Akü Kapasitesi, Mast Yüksekliği, Çatal Genişliği

**Neden Önemli:**
Her ürün JSON'unda `primary_specs` alanı bu şablona göre doldurulur.

---

### 3️⃣ **04-JSON-SABLONU.md** (JSON Template)
**Konum:** `/readme/shop-system-v2/04-JSON-SABLONU.md`

**İçeriği:**
- Standart JSON yapısı (tüm alanlar)
- Alan açıklamaları
- Örnek değerler

**Neden Önemli:**
JSON çıktısının formatı ve zorunlu alanları burada tanımlı.

---

### 4️⃣ **08-VARIANT-SYSTEM.md** (Varyant Sistemi)
**Konum:** `/readme/shop-system-v2/08-VARIANT-SYSTEM.md`

**İçeriği:**
- Product-based variants (her varyant = ayrı ürün)
- Simple variants (sadece fiyat/stok farkı)
- Hibrit sistem kuralları

**Neden Önemli:**
Eğer üründe varyantlar varsa (farklı çatal boyutları, batarya seçenekleri vb.) bu sisteme göre işlenir.

---

### 5️⃣ **03-AI-KURALLARI.md** (Ek Kurallar)
**Konum:** `/readme/shop-system-v2/03-AI-KURALLARI.md`

**İçeriği:**
- İçerik üretim standartları
- SEO kuralları
- Marketing copy standartları

---

## 🚀 BAŞKA BİR AI'A PROMPT VERİRKEN SIRA

### ADIM 1: Dosyaları Yükle (veya İçeriği Kopyala)

```
Önce şu dosyaları oku ve öğren:

1. /readme/shop-system-v2/AI-PROMPT.md
2. /readme/shop-system-v2/01-KATEGORI-SPECS.md
3. /readme/shop-system-v2/04-JSON-SABLONU.md
4. /readme/shop-system-v2/08-VARIANT-SYSTEM.md
```

---

### ADIM 2: PDF'i Yükle

```
Şimdi bu PDF'i oku:
/Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F1/F1-EN-Brochure-2.pdf
```

---

### ADIM 3: JSON Üretim Komutu

```
Yukarıda öğrendiğin kurallara göre bu PDF için JSON üret.

ZORUNLU KURALLAR:
✅ %100 Türkçe içerik
✅ primary_specs: Transpalet kategorisi şablonunu kullan (01-KATEGORI-SPECS.md)
✅ FAQ: Minimum 10 soru-cevap
✅ use_cases: Minimum 6 senaryo
✅ competitive_advantages: Minimum 5 avantaj
✅ target_industries: Minimum 20 sektör
✅ İletişim: 0216 755 3 555 | info@ixtif.com
✅ İXTİF servisleri: İkinci el, kiralık, yedek parça, teknik servis belirt

JSON'u şu formatta kaydet:
/readme/shop-system-v2/json-extracts/F1-transpalet.json
```

---

## 📝 TAM PROMPT ÖRNEĞİ (KOPYALA-YAPIŞTIR)

Aşağıdaki promptu **aynen** kopyalayıp ChatGPT/Gemini'ye yapıştırabilirsiniz:

```
# Görev: EP Equipment PDF'inden E-ticaret JSON Oluştur

## 1. Öğrenme Aşaması

Önce şu dosyaları oku ve kuralları öğren:

📄 **AI-PROMPT.md**: Ana kural seti
📄 **01-KATEGORI-SPECS.md**: Kategori şablonları (primary_specs için)
📄 **04-JSON-SABLONU.md**: JSON yapısı
📄 **08-VARIANT-SYSTEM.md**: Varyant sistemi

## 2. PDF Analizi

PDF: F1-EN-Brochure-2.pdf (1.5 ton elektrikli transpalet)

Teknik bilgileri, özellikleri ve spesifikasyonları çıkar.

## 3. JSON Üretimi

ZORUNLU KURALLAR:
- ✅ %100 Türkçe içerik (İngilizce yok)
- ✅ primary_specs: Transpalet şablonu kullan (4 kart)
  - Yük Kapasitesi
  - Akü Sistemi
  - Çatal Uzunluğu
  - Denge Tekeri
- ✅ FAQ ≥ 10 soru-cevap
- ✅ use_cases ≥ 6 senaryo
- ✅ competitive_advantages ≥ 5 avantaj
- ✅ target_industries ≥ 20 sektör
- ✅ İletişim: 0216 755 3 555 | info@ixtif.com
- ✅ body: <section class="marketing-intro"> + <section class="marketing-body">
- ✅ İXTİF servisleri ekle: İkinci el satış, kiralama, yedek parça, teknik servis

## 4. Çıktı Formatı

JSON dosyası olarak kaydet: F1-transpalet.json

category_slug: "transpalet"
model_code: "F1"
sku: "F1-EPT"
...
```

---

## 🔄 SÜREÇ AKIŞI

```
┌─────────────────┐
│  PDF Dosyası    │
│  (EP Equipment) │
└────────┬────────┘
         │
         ▼
┌─────────────────────────────┐
│  ADIM 1: Kuralları Öğren    │
│  - AI-PROMPT.md             │
│  - 01-KATEGORI-SPECS.md     │
│  - 04-JSON-SABLONU.md       │
│  - 08-VARIANT-SYSTEM.md     │
└─────────┬───────────────────┘
          │
          ▼
┌─────────────────────────────┐
│  ADIM 2: PDF'i Analiz Et    │
│  - Teknik specs çıkar       │
│  - Özellikler listele       │
│  - Görselleri not al        │
└─────────┬───────────────────┘
          │
          ▼
┌─────────────────────────────┐
│  ADIM 3: İçerik Üret        │
│  - Türkçe marketing copy    │
│  - FAQ oluştur (min 10)     │
│  - Use cases yaz (min 6)    │
│  - Hedef sektörler (min 20) │
└─────────┬───────────────────┘
          │
          ▼
┌─────────────────────────────┐
│  ADIM 4: JSON Oluştur       │
│  - primary_specs şablonu    │
│  - Tüm alanları doldur      │
│  - Validasyon kontrol       │
└─────────┬───────────────────┘
          │
          ▼
┌─────────────────────────────┐
│  ÇIKTI: JSON Dosyası        │
│  json-extracts/F1.json      │
└─────────────────────────────┘
```

---

## ⚠️ SIRA ÖNEMLİ!

AI programı dosyaları **TAM BU SIRADA** okumalıdır:

1. **AI-PROMPT.md** → Temel kurallar
2. **01-KATEGORI-SPECS.md** → primary_specs şablonu
3. **04-JSON-SABLONU.md** → JSON yapısı
4. **08-VARIANT-SYSTEM.md** → Varyant kuralları
5. **PDF Dosyası** → Ürün bilgileri

**Neden Sıra Önemli?**
- AI-PROMPT.md olmadan AI neyi nasıl yapacağını bilmez
- 01-KATEGORI-SPECS.md olmadan primary_specs kartlarını yanlış oluşturur
- 04-JSON-SABLONU.md olmadan JSON formatı hatalı olur
- 08-VARIANT-SYSTEM.md olmadan varyantları yanlış işler

---

## 🎯 KRİTİK KONTROL LİSTESİ

AI'nın ürettiği JSON'u kontrol ederken:

- [ ] Tüm içerik %100 Türkçe mi?
- [ ] primary_specs 4 kart mı? (kategori şablonuna uygun)
- [ ] FAQ ≥ 10 soru-cevap mı?
- [ ] use_cases ≥ 6 senaryo mu?
- [ ] competitive_advantages ≥ 5 avantaj mı?
- [ ] target_industries ≥ 20 sektör mü?
- [ ] İletişim bilgileri var mı? (0216 755 3 555, info@ixtif.com)
- [ ] İXTİF servisleri belirtilmiş mi? (ikinci el, kiralık, yedek parça, teknik servis)
- [ ] body HTML section'ları var mı?
- [ ] technical_specs detaylı ve doğru mu?

---

## 💡 İPUÇLARI

### ChatGPT İçin:
```
1. Dosyaları sırayla yükle (Advanced Data Analysis kullan)
2. "Bu dosyayı oku ve öğren" komutunu ver
3. PDF'i yükle
4. JSON üret komutunu ver
5. Çıktıyı indir
```

### Claude İçin:
```
1. Dosyaları Projects'e ekle
2. PDF'i yükle
3. "Kuralları uygula ve JSON üret" de
4. Artifact olarak JSON oluşturur
```

### Gemini İçin:
```
1. Dosyaları Google Drive'a yükle
2. PDF'i yükle
3. Detaylı prompt ver
4. JSON'u kopyala
```

---

## 🔧 ÖRNEK KOMUTLAR

### Tek Ürün İçin:
```
Bu PDF'i oku ve Shop System v2 formatında JSON üret:
/EP PDF/2-Transpalet/F1/F1-EN-Brochure-2.pdf

Kurallar: readme/shop-system-v2/AI-PROMPT.md
Şablon: readme/shop-system-v2/01-KATEGORI-SPECS.md (Transpalet)
Format: readme/shop-system-v2/04-JSON-SABLONU.md
Çıktı: readme/shop-system-v2/json-extracts/F1-transpalet.json
```

### Toplu İşleme:
```
/EP PDF/2-Transpalet/ klasöründeki TÜM PDF'leri işle.
Her biri için ayrı JSON üret.
Kuralları readme/shop-system-v2/ klasöründen al.
```

---

## 📊 BAŞARI KRİTERLERİ

Üretilen JSON başarılı sayılır eğer:

1. ✅ Tüm zorunlu alanlar dolu
2. ✅ %100 Türkçe içerik
3. ✅ primary_specs kategori şablonuna uygun
4. ✅ Minimum sayılar karşılanmış (FAQ≥10, use_cases≥6, vb.)
5. ✅ İletişim bilgileri doğru
6. ✅ JSON syntax hatası yok
7. ✅ technical_specs detaylı ve doğru
8. ✅ Marketing copy profesyonel ve satış odaklı

---

## 🆘 SORUN GİDERME

### Problem: AI İngilizce içerik üretiyor
**Çözüm:** AI-PROMPT.md'deki "%100 Türkçe" kuralını vurgula

### Problem: primary_specs yanlış
**Çözüm:** 01-KATEGORI-SPECS.md'deki ilgili kategori şablonunu göster

### Problem: FAQ sayısı az
**Çözüm:** "FAQ sayısı 10'un altında. Lütfen minimum 10 soru-cevap üret" de

### Problem: technical_specs eksik
**Çözüm:** PDF'den tüm teknik tabloyu detaylı çıkar, hiçbir bilgiyi atlama

---

## 📞 İLETİŞİM

Her JSON'da mutlaka bulunması gereken iletişim bilgileri:

```json
"contact": {
  "phone": "0216 755 3 555",
  "email": "info@ixtif.com",
  "company": "İXTİF İç ve Dış Ticaret A.Ş."
}
```

---

## 🎓 SONUÇ

Bu rehberi takip ederek **herhangi bir AI programı** ile aynı kalitede JSON üretebilirsiniz.

**Anahtar:** Dosyaları doğru sırada okumak ve tüm kuralları uygulamak.

**Sıra:** AI-PROMPT.md → 01-KATEGORI-SPECS.md → 04-JSON-SABLONU.md → 08-VARIANT-SYSTEM.md → PDF

**Sonuç:** Profesyonel, SEO-uyumlu, satış odaklı e-ticaret JSON'ları.
