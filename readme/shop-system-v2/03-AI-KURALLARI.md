# 🤖 AI KURALLARI - PDF'DEN JSON ÜRET İMİ

## 🎯 GENEL KURAL: %100 TÜRKÇE

**TÜM ALANLAR TÜRKÇE OLMALI!**
- ✅ `"tr"`: Türkçe içerik
- ✅ `"en"`: **Türkçe metnin birebir kopyası** (çeviri YOK!)
- ✅ `"vs."`: `"..."` (placeholder)

---

## 📝 DİL VE PAZARLAMA KURALLARI

### 1️⃣ **body** İKİ BÖLÜMDEN OLUŞMALI

```html
<section class="marketing-intro">
  <!-- ABARTILI, DUYGUSAL SATIŞ AÇILIŞI -->
  <p><strong>F4 201'i depoya soktuğunuz anda müşterileriniz "Bu transpaleti nereden aldınız?" diye soracak.</strong></p>
  <p>İXTİF mühendisleri bu modeli yalnızca yük taşımak için değil, <em>deponuzun prestijini parlatmak</em> için tasarladı.</p>
  <ul>
    <li><strong>Bir vardiyada iki kat iş</strong> – Lojistik maliyetleriniz %50'ye kadar düşsün.</li>
    <li><strong>Showroom etkisi</strong> – Ultra kompakt şasi dar koridorlarda bile vitrinde yürür gibi ilerler.</li>
  </ul>
</section>

<section class="marketing-body">
  <!-- TEKNİK FAYDALAR, GARANTİ, İLETİŞİM, SEO -->
  <p>Standart teslimat paketinde 2 adet 24V/20Ah Li-Ion modül bulunur...</p>
  <p>İXTİF'in <strong>ikinci el, kiralık, yedek parça ve teknik servis</strong> programları ile F4 201 yatırımınız tam koruma altında...</p>
  <p><strong>SEO Anahtar Kelimeleri:</strong> F4 201 transpalet, 48V Li-Ion transpalet, 2 ton akülü transpalet, İXTİF transpalet, dar koridor transpalet.</p>
  <p><strong>Şimdi İXTİF'i arayın:</strong> 0216 755 3 555 veya <strong>info@ixtif.com</strong></p>
</section>
```

**ZORUNLU:**
- ✅ `<section class="marketing-intro">` → Duygusal tetikleyici
- ✅ `<section class="marketing-body">` → Teknik + iletişim
- ✅ SEO anahtar kelimeleri marketing-body'de listelenecek
- ✅ İletişim: `0216 755 3 555` ve `info@ixtif.com`

---

### 2️⃣ **SEO ANAHTAR KELİMELERİ**

**ÜRÜN BAZLI (Her Ürün İçin Farklı):**
```
F4 201 transpalet
48V Li-Ion transpalet
2 ton akülü transpalet
İXTİF transpalet
dar koridor transpalet
```

**KATEGORİ BAZLI (Genel):**
```
akülü transpalet
Li-Ion transpalet çözümleri
elektrikli transpalet fiyatları
```

**NEREYE EKLENECEK?**
- ✅ `body` → marketing-body bölümünde liste olarak
- ✅ `short_description` → doğal cümleler içinde
- ✅ `features.list` → mümkün olduğunca

---

### 3️⃣ **İXTİF HİZMETLERİ (ZORUNLU)**

Her ürün için **mutlaka** şunlardan bahsedilecek:
- ✅ **İkinci el** seçenekleri
- ✅ **Kiralık** / leasing programları
- ✅ **Yedek parça** tedariki
- ✅ **Teknik servis** (7/24, Türkiye geneli mobil ekipler)

**NEREYE EKLENECEK?**
- ✅ `body` → marketing-body
- ✅ `features.list` → en az 1 madde
- ✅ `competitive_advantages` → en az 1 madde
- ✅ `faq_data` → en az 2 soru (ikinci el/kiralık + garanti/servis)

---

### 4️⃣ **İLETİŞİM BİLGİLERİ**

**STANDART:**
- Telefon: `0216 755 3 555`
- E-posta: `info@ixtif.com`
- Firma: `İXTİF İç ve Dış Ticaret A.Ş.`

**NEREYE EKLENECEK?**
- ✅ `body` → marketing-body sonunda
- ✅ `faq_data` → son sorularda ("Detaylı teklif için...")

---

### 5️⃣ **SON KULLANICI ODAKLI ANLAT**

**✅ YAPILACAK:**
- Son kullanıcı faydaları (hız, verimlilik, güvenlik)
- Operasyon kolaylığı
- Maliyet tasarrufu

**❌ YAPILMAYACAK:**
- Konteyner dizilimi (164 adet/40' konteyner)
- Toplu sevkiyat detayları
- Wholesale packaging
- B2B lojistik terimleri

---

## 📊 ALAN KURALLARI

### 6️⃣ **features** YAPISI

```json
{
  "features": {
    "tr": {
      "list": [
        "F4 201 transpalet 48V Li-Ion güç platformu ile 2 ton akülü taşıma kapasitesini dar koridor operasyonlarına taşır.",
        "Tak-çıkar 24V/20Ah Li-Ion bataryalarla vardiya ortasında şarj molasına son verin.",
        "140 kg servis ağırlığı ve 400 mm şasi uzunluğu sayesinde dar koridorlarda benzersiz çeviklik sağlar.",
        "İXTİF ikinci el, kiralık, yedek parça ve teknik servis ekosistemi ile yatırımınıza 360° koruma sağlar."
      ],
      "branding": {
        "slogan": "Depoda hız, sahada prestij: F4 201 ile dar koridorlara hükmedin.",
        "motto": "İXTİF farkı ile 2 tonluk yükler bile hafifler.",
        "technical_summary": "F4 201, 48V Li-Ion güç paketi, 0.9 kW BLDC sürüş motoru ve 400 mm ultra kompakt şasi kombinasyonuyla dar koridor ortamlarında yüksek tork, düşük bakım ve sürekli çalışma sunar."
      }
    },
    "en": {
      "list": [...],  // Türkçe kopya
      "branding": {...}  // Türkçe kopya
    }
  }
}
```

**ZORUNLU:**
- ✅ `list`: En az 4 madde
- ✅ `branding.slogan`: Satışa teşvik eden slogan
- ✅ `branding.motto`: Kısa, akılda kalıcı motto
- ✅ `branding.technical_summary`: Teknik özet (100-150 karakter)

---

### 7️⃣ **primary_specs** (4 KART)

**KATEGORİ BAZLI:**

#### **TRANSPALET:**
```json
[
  {"label": "Denge Tekeri", "value": "Yok"},
  {"label": "Li-Ion Akü", "value": "24V/20Ah çıkarılabilir paket"},
  {"label": "Şarj Cihazı", "value": "24V/5A harici hızlı şarj"},
  {"label": "Standart Çatal", "value": "1150 x 560 mm"}
]
```

#### **FORKLIFT:**
```json
[
  {"label": "Asansör", "value": "3000 mm"},
  {"label": "Li-Ion Akü", "value": "48V/150Ah paket"},
  {"label": "Şarj Cihazı", "value": "48V/10A hızlı şarj"},
  {"label": "Raf Aralığı", "value": "2800 mm"}
]
```

#### **İSTİF MAKİNESİ:**
```json
[
  {"label": "Asansör", "value": "4500 mm"},
  {"label": "Akü", "value": "24V/200Ah"},
  {"label": "Şarj Cihazı", "value": "24V/15A"},
  {"label": "Çatal", "value": "1200 x 560 mm"}
]
```

**DEĞERLER:**
- `technical_specs`'ten otomatik doldurulacak
- Değer yoksa `"Standart"` veya `"Opsiyonel"` yazılacak

---

### 8️⃣ **use_cases** (Minimum 6)

```json
{
  "use_cases": {
    "tr": [
      "E-ticaret depolarında hızlı sipariş hazırlama ve sevkiyat operasyonları",
      "Dar koridorlu perakende depolarında gece vardiyası yükleme boşaltma",
      "Soğuk zincir lojistiğinde düşük sıcaklıklarda kesintisiz malzeme taşıma",
      "İçecek ve FMCG dağıtım merkezlerinde yoğun palet trafiği yönetimi",
      "Dış saha rampalarda stabilizasyon tekerleği ile güvenli taşıma",
      "Kiralama filolarında yüksek kârlılık sağlayan Li-Ion platform çözümleri"
    ],
    "en": [...]  // Türkçe kopya
  }
}
```

**ZORUNLU:**
- ✅ Minimum 6 senaryo
- ✅ Sektör bazlı (e-ticaret, perakende, gıda, vs.)
- ✅ Gerçekçi kullanım alanları

---

### 9️⃣ **competitive_advantages** (Minimum 5)

```json
{
  "competitive_advantages": {
    "tr": [
      "48V Li-Ion güç platformu ile segmentindeki en agresif hızlanma ve rampa performansı",
      "140 kg'lık ultra hafif servis ağırlığı sayesinde lojistik maliyetlerinde dramatik düşüş",
      "Tak-çıkar batarya konsepti ile 7/24 operasyonda sıfır bekleme, sıfır bakım maliyeti",
      "Stabilizasyon tekerleği opsiyonu sayesinde bozuk zeminlerde bile devrilme riskini sıfırlar",
      "İXTİF stoktan hızlı teslimat ve yerinde devreye alma ile son kullanıcıyı bekletmez"
    ],
    "en": [...]  // Türkçe kopya
  }
}
```

**ZORUNLU:**
- ✅ Minimum 5 avantaj
- ✅ Ölçülebilir fayda + duygusal tetikleyici
- ✅ Rakiplerden farkınızı vurgulayın

---

### 🔟 **target_industries** (Minimum 20)

```json
{
  "target_industries": {
    "tr": [
      "E-ticaret & fulfillment merkezleri",
      "Perakende zincir depoları",
      "Soğuk zincir ve gıda lojistiği",
      "İçecek ve FMCG dağıtım şirketleri",
      "Endüstriyel üretim tesisleri",
      "3PL lojistik firmaları",
      "İlaç ve sağlık depoları",
      "Elektronik dağıtım merkezleri",
      "Mobilya & beyaz eşya depolama",
      "Otomotiv yedek parça depoları",
      "... (toplam 20 sektör)"
    ],
    "en": [...]  // Türkçe kopya
  }
}
```

---

### 1️⃣1️⃣ **faq_data** (Minimum 10)

Detaylar için **02-FAQ-SISTEMI.md** dosyasına bak.

**ZORUNLU KONULAR:**
- ✅ Kullanım süresi / vardiya performansı
- ✅ Manevra kabiliyeti
- ✅ Stabilizasyon / güvenlik
- ✅ Batarya / şarj sistemi
- ✅ **Garanti ve servis** (İXTİF 7/24 servis vurgulanacak)
- ✅ **İkinci el, kiralık, finansman** (0216 755 3 555, info@ixtif.com)
- ✅ Standart aksesuar / opsiyonlar
- ✅ Yedek parça paketleri
- ✅ Saha kurulumu / eğitim
- ✅ **Teknik slogan ve motto**

---

## 📐 TEKNİK SPESİFİKASYONLAR

### 1️⃣2️⃣ **technical_specs** YAPISI

```json
{
  "technical_specs": {
    "capacity": {
      "load_capacity": {"value": 2000, "unit": "kg"},
      "load_center_distance": {"value": 600, "unit": "mm"},
      "service_weight": {"value": 140, "unit": "kg"}
    },
    "dimensions": {
      "overall_length": {"value": 1550, "unit": "mm"},
      "turning_radius": {"value": 1360, "unit": "mm"},
      "fork_dimensions": {
        "thickness": 50,
        "width": 150,
        "length": 1150,
        "unit": "mm"
      }
    },
    "electrical": {
      "voltage": {"value": 48, "unit": "V"},
      "capacity": {"value": 20, "unit": "Ah"},
      "type": "Li-Ion",
      "battery_system": {
        "configuration": "2x 24V/20Ah değiştirilebilir Li-Ion modül (4 adede kadar genişletilebilir)"
      },
      "charger_options": {
        "standard": "2x 24V-5A harici şarj ünitesi",
        "optional": ["2x 24V-10A hızlı şarj ünitesi"]
      }
    },
    "performance": {
      "travel_speed": {"laden": 4.5, "unladen": 5.0, "unit": "km/h"},
      "max_gradeability": {"laden": 8, "unladen": 16, "unit": "%"}
    },
    "tyres": {
      "type": "Poliüretan",
      "drive_wheel": "210 × 70 mm Poliüretan",
      "load_wheel": "80 × 60 mm Poliüretan (çift sıra standart)"
    },
    "options": {
      "stabilizing_wheels": {"standard": false, "optional": true},
      "fork_lengths_mm": [900, 1000, 1150, 1220, 1350, 1500]
    }
  }
}
```

**KURALLAR:**
- ✅ Tablo verileri PDF'deki rakamlara **birebir uymalı**
- ✅ Birimler korunur (mm, kg, kW, V, Ah, km/h, %)
- ✅ `charger_options`, `battery_system` gibi alanlar **Türkçe açıklama** içermeli
- ✅ `note` alanları varsa **Türkçe** yazılacak

---

## 🎨 PAZARLAMA TONU

### DUYGUSAL TETİKLEYİCİLER (Kullanılacak Kelimeler)

**✅ KULLAN:**
- Prestij, şampiyon, hız rekoru, yatırımınızın vitrini
- Benzersiz, inanılmaz, devrim niteliğinde
- Sıfır bekleme, sıfır bakım, sıfır risk
- Showroom etkisi, vitrinde yürür gibi
- Depoda hız, sahada prestij

**❌ KULLANMA:**
- B2B jargon (wholesale, bulk, FOB, CIF)
- Aşırı teknik terimler (son kullanıcı anlamaz)
- İngilizce kelimeler (hepsi Türkçe olacak)

---

## ✅ KONTROL LİSTESİ

AI'dan dönen JSON'u kontrol et:

- [ ] Tüm `en` alanları `tr` ile aynı mı?
- [ ] `body` iki `<section>` içeriyor mu?
- [ ] SEO anahtar kelimeleri marketing-body'de listelenmiş mi?
- [ ] İXTİF hizmetleri (ikinci el, kiralık, yedek parça, servis) geçiyor mu?
- [ ] İletişim bilgileri (`0216 755 3 555`, `info@ixtif.com`) var mı?
- [ ] `features.branding` (slogan, motto, technical_summary) dolu mu?
- [ ] `primary_specs` 4 kart ve kategori bazlı doğru mu?
- [ ] `use_cases` ≥ 6 mı?
- [ ] `competitive_advantages` ≥ 5 mi?
- [ ] `target_industries` ≥ 20 mi?
- [ ] `faq_data` ≥ 10 ve zorunlu konular var mı?
- [ ] Teknik değerler PDF ile uyumlu mu?

---

**ŞİMDİ JSON ŞABLONUNU HAZIRLIYORUM...**
