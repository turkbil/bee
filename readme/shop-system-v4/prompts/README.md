# 📚 SHOP SYSTEM V4 - AI PROMPT SİSTEMİ

## 🎯 AMAÇ

Bu sistem AI (ChatGPT/Claude) kullanarak PDF kataloglardan **Shop System V4** seeder dosyalarını üretir.

**V4 Farkı:** 8 content variation + AI-focused keywords + Category-based comparison + SEO optimization

---

## 📁 KLASÖR YAPISI

```
prompts/
├── README.md                    ← ŞU AN BURADASINIZ
├── 00-GENEL-TALIMATLAR.md      ← Tüm kategoriler için geçerli
├── 01-SISTEM-MIMARI-V4.md      ← V4 database yapısı
├── 02-8-VARIATION-SYSTEM.md    ← Content variation sistemi
├── 03-AI-KEYWORDS-SYSTEM.md    ← AI-focused keyword stratejisi
├──EOF
echo "" >> /var/www/vhosts/tuufi.com/httpdocs/readme/shop-system-v4/prompts/README.md
cat >> /var/www/vhosts/tuufi.com/httpdocs/readme/shop-system-v4/prompts/README.md << 'EOF'
 04-PDF-CONTENT-ANALYSIS.md     ← PDF içerik skoru sistemi
├── 05-NORMALIZATION-RULES.md   ← Alan normalizasyonu kuralları
│
├── 1-transpalet/
│   ├── README.md               ← Transpalet özellikleri
│   ├── PROMPT.md               ← ChatGPT/Claude prompt
│   ├── ORNEK-SEEDER.php        ← Örnek seeder
│   └── FIELD-MAPPING.json      ← Alan eşleştirme
│
├── 2-forklift/
│   ├── README.md
│   ├── PROMPT.md
│   ├── ORNEK-SEEDER.php
│   └── FIELD-MAPPING.json
│
├── 3-istif-makineleri/
│   └── ... (aynı yapı)
│
├── 4-siparis-toplama/
│   └── ... (aynı yapı)
│
├── 5-otonom/
│   └── ... (aynı yapı)
│
├── 6-reach-truck/
│   └── ... (aynı yapı)
│
└── karsilastirma/
    ├── README.md               ← Karşılaştırma sistemi
    ├── COMPARISON-DESIGN.md    ← 3 katmanlı sistem
    ├── RISK-MATRIX.md          ← Risk analizi
    └── HYBRID-SYSTEM.md        ← Hybrid karşılaştırma
```

---

## 🚀 HIZLI BAŞLANGIÇ

### 1. PDF İçerik Skoru Hesapla

```bash
AI'ya gönder:
"Bu PDF'i analiz et ve içerik skoru hesapla:
- Teknik tablo var mı? (+20 puan)
- Why Series bölümü var mı? (+15 puan)
- Kullanım senaryoları var mı? (+15 puan)
- Maliyet tasarrufu açıklaması var mı? (+10 puan)
- Platform tasarım açıklaması var mı? (+10 puan)
- Opsiyon listesi detaylı mı? (+10 puan)
- Feature açıklamaları detaylı mı? (+20 puan)
TOPLAM: /100"
```

**Skor < 70:** Eksik veri uyarısı ver, hangi bölümlerin eksik olduğunu belirt
**Skor 70-85:** Standart parse, eksik alanları `null` olarak belirt
**Skor > 85:** Tam detay parse, tüm 8 variation üret

### 2. Kategori-Specific Prompt Kullan

```bash
Transpalet için:
"1-transpalet/PROMPT.md" dosyasındaki talimatları uygula.
PDF'i analiz et ve seeder oluştur.
```

### 3. Seeder Dosyasını Kaydet

```bash
/Modules/Shop/Database/Seeders/V4/
```

### 4. Test Et

```bash
php artisan migrate:fresh --seed
```

---

## ✅ V4 SİSTEMİNDE YENİ OLAN ÖZELLİKLER

### 1. 8 Content Variation (Her Özellik İçin)

```json
{
  "content_variations": {
    "li-ion-battery": {
      "technical": "24V 20Ah Li-Ion batarya, 2000+ şarj döngüsü",
      "benefit": "Tam gün çalış, şarj bekleme. Kurşun-aside göre %40 daha hafif",
      "slogan": "Bir Şarj, Tam Gün İş!",
      "motto": "Güç Hiç Bitmesin",
      "short": "24V Li-Ion, 2000+ döngü",
      "long": "24V 20Ah kapasiteli lityum iyon batarya sistemi, geleneksel kurşun-asit bataryalara göre...",
      "comparison": "Li-Ion vs Kurşun-Asit: %40 daha hafif, 3x daha uzun ömür, sıfır bakım",
      "keywords": "li-ion, lityum iyon, şarj süresi, batarya ömrü, hafif batarya"
    }
  }
}
```

### 2. AI-Focused Keywords (3 Kategori)

```json
{
  "keywords": {
    "primary": ["transpalet", "elektrikli transpalet", "li-ion transpalet", "1.5 ton transpalet"],
    "synonyms": ["palet taşıyıcı", "elektrikli palet kaldırıcı", "akülü transpalet"],
    "usage_jargon": ["palet jack", "transpalet makinesi", "depo transpaleti", "lojistik transpaleti"]
  }
}
```

### 3. Category-Based Primary Specs (5 Sabit Alan)

**✅ KULLANICI BELİRLEDİ - Her kategori için 5 özellik:**

**1. Transpalet:**
- Kapasite, Denge Tekeri, Lityum Akü, Şarj Cihazı, Dönüş Yarıçapı

**2. Forklift:**
- Kapasite, Asansör, Lityum Akü, Şarj Cihazı, Kaldırma Yüksekliği

**3. İstif Makinesi:**
- Kapasite, Asansör, Akü, Şarj Cihazı, Kaldırma Yüksekliği

**4. Reach Truck:**
- Kapasite, Kaldırma Yüksekliği, Lityum Akü, Şarj Cihazı, Raf Mesafesi

**5. Order Picker:**
- Kapasite, Kaldırma Yüksekliği, Lityum Akü, Şarj Cihazı, Platform Genişliği

**Detay:** Her kategorinin kendi PROMPT.md dosyasında

### 4. PDF İçerik Skoru

```json
{
  "content_quality_score": {
    "total": 85,
    "breakdown": {
      "technical_table": 20,
      "why_series": 15,
      "use_cases": 15,
      "cost_saving": 10,
      "platform_design": 10,
      "options": 10,
      "features": 15
    },
    "missing_fields": ["competitive_advantages"],
    "warnings": ["Maliyet tasarrufu verisi eksik, üreticiye sorulmalı"]
  }
}
```

### 5. Normalizasyon Alanları

```json
{
  "stabilizing_wheel": {
    "value": true,
    "original_text": "Industrial floating stabilizing wheels for maximum stability",
    "_normalized_field": "has_stabilizing_wheel",
    "_aliases": ["castor wheels", "floating wheels", "stabilizing wheels"]
  }
}
```

---

## 🚨 KRİTİK FARKLAR: V3 vs V4

| Özellik | V3 | V4 |
|---------|----|----|
| **Content Variation** | ❌ Yok | ✅ 8 çeşit (technical, benefit, slogan, vb.) |
| **Keywords** | ⚠️ Basit liste | ✅ 3 kategori (primary, synonyms, usage) |
| **Primary Specs** | ❌ Yok | ✅ Kategori bazlı 4 sabit alan |
| **PDF Quality Check** | ❌ Yok | ✅ İçerik skoru hesaplama |
| **Normalization** | ❌ Yok | ✅ Alan normalizasyonu |
| **Comparison Support** | ⚠️ Kısıtlı | ✅ 3 katmanlı comparison sistemi |
| **FAQ Minimum** | ⚠️ 10-12 | ✅ 12-15 (kategorize) |
| **One-Line Description** | ❌ Yok | ✅ 120-150 karakter (kart için) |
| **Competitive Advantages** | ⚠️ Basit | ✅ Detaylı + eksik veri handling |

---

## 📋 ÇALIŞMA AKIŞI

```
1. PDF Upload
   ↓
2. İçerik Skoru Hesapla (04-PDF-CONTENT-ANALYSIS.md)
   ↓
3. Kategori Tespit Et
   ↓
4. Kategori-Specific Prompt Çalıştır (1-transpalet/PROMPT.md)
   ↓
5. 8 Content Variation Üret (02-8-VARIATION-SYSTEM.md)
   ↓
6. AI Keywords Üret (03-AI-KEYWORDS-SYSTEM.md)
   ↓
7. Normalizasyon Uygula (05-NORMALIZATION-RULES.md)
   ↓
8. Seeder Dosyası Oluştur
   ↓
9. Kalite Kontrol
   ↓
10. Database'e Kaydet
```

---

## 🎓 ÖNEMLİ NOTLAR

### İçerik Skoru < 70 İse

AI şunu yapmalı:
```
⚠️ UYARI: PDF içerik skoru düşük (65/100)

Eksik Alanlar:
- ❌ "Why Series?" bölümü yok
- ❌ Maliyet tasarrufu açıklaması yok
- ❌ Kullanım senaryoları kısıtlı

Yapabileceklerim:
- ✅ Teknik özellikler: TAM (20/20)
- ✅ Opsiyon listesi: TAM (10/10)
- ⚠️ Content variations: KISMEN (5/8 variation üretilebilir)

Önerim:
- Üreticiden ek bilgi talep edin
- Veya mevcut verilerle devam edip eksik alanları "null" bırakayım
```

### Normalizasyon Örneği

**Farklı PDF'lerde aynı özellik farklı isimlerle:**
```
F4 PDF: "Stabilizing wheels allow to handle big loads"
EPL185 PDF: "Industrial floating stabilizing wheels"
```

**Normalizasyon:**
```json
{
  "_normalized_field": "has_stabilizing_wheel",
  "_normalized_value": true,
  "_comparison_label": "Stabilize Tekerlek"
}
```

---

## 📞 DESTEK

Sorun yaşarsanız:
1. **İlgili kategori klasörünü** kontrol edin (1-transpalet/, 2-forklift/, vb.)
2. **04-PDF-CONTENT-ANALYSIS.md** ile içerik skorunu hesaplayın
3. **05-NORMALIZATION-RULES.md** ile alan eşleştirmesini kontrol edin
4. **karsilastirma/README.md** ile karşılaştırma sistemini anlayın

---

## 🎉 V4 SİSTEMİ HAZIR!

**V4 Avantajları:**
- ✅ AI matching için optimize edilmiş keyword sistemi
- ✅ Her özellik 8 farklı şekilde anlatılıyor
- ✅ Karşılaştırma sistemi için hazır veri yapısı
- ✅ PDF kalite kontrolü ile eksik veri tespiti
- ✅ Normalizasyon ile farklı PDF'lerden tutarlı veri

**Artık AI çok daha akıllı seeder üretebilir!** 🚀
