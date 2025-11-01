# 🤖 Shop System V4 - AI PDF Parser Prompt

## 🎯 AMAÇ

Bu prompt, üretici PDF kataloglarından **tam otomatik PHP seeder kodu** üretmek için kullanılır.

---

## 📋 KULLANIM

```bash
# AI'a şu komutu ver:
"readme/shop-system-v4/V4-SYSTEM-RULES.md ve readme/shop-system-v4/AI-PARSER-PROMPT.md dosyalarını oku.
EP PDF/2-Transpalet/F4 201/ klasöründeki PDF'i analiz et.
Modules/Shop/database/seeders/Transpalet_F4_201_Seeder.php dosyasını oluştur."
```

---

## 🤖 AI İÇİN TALİMATLAR

### 1️⃣ OKUMA AŞAMASI

**Önce oku:**
1. `/var/www/vhosts/tuufi.com/httpdocs/readme/shop-system-v4/V4-SYSTEM-RULES.md` - Tüm sistem kuralları
2. PDF dosyasını analiz et (OCR/text extraction)

### 2️⃣ ANALİZ AŞAMASI

**PDF'den çıkaracağın bilgiler:**

#### A. Temel Bilgiler:
- **Model**: Ürün model kodu (F4, CPD20, CBD15)
- **Kapasite**: Kaldırma kapasitesi (kg → Ton'a çevir)
- **Enerji Tipi**: Li-Ion, Elektrikli, Dizel, LPG, Hibrit
- **Kategori**: 7 ana kategoriden hangisi (Transpalet, Forklift, İstif Makinesi, Reach Truck, Order Picker, Tow Truck, Otonom)

#### B. Başlık Oluştur:
```
[Model] [Kapasite] [Enerji Tipi] [Kategori] [- Özel Özellik (opsiyonel)]
```

**Örnek:**
- PDF'de: "F4" model, "1500 kg" capacity, "Li-Ion battery"
- Başlık: `F4 1.5 Ton Lityum Akülü Transpalet`

#### C. Teknik Özellikler Çıkar:

**Transpalet için standart kategoriler:**
1. Genel Özellikler (Model, kapasite, enerji)
2. Batarya Sistemi (Tip, voltaj, kapasite, operasyon süresi, şarj süresi)
3. Boyutlar (Uzunluk, genişlik, yükseklik, ağırlık)
4. Çatal Özellikleri (Uzunluk, genişlik, kaldırma yüksekliği)
5. Performans (Hız, kaldırma hızı, menzil)
6. Tekerlekler (Tip, malzeme, çap)
7. Fren Sistemi (Tip, özellikler)
8. Güvenlik (Koruma sistemleri, BMS)
9. Ergonomi (Kumanda tipi, tutamaç)
10. Çevresel (Sıcaklık aralığı, gürültü seviyesi, IP koruma)
11. Sertifikalar (CE, ISO, TÜV)
12. Opsiyonlar (Fork uzunlukları, ekstra batarya, aksesuarlar)

**JSON Formatı:**
```json
{
  "general": {
    "category_name": "Genel Özellikler",
    "icon": "fa-info-circle",
    "properties": [
      {"key": "Model", "value": "F4", "unit": ""},
      {"key": "Kapasite", "value": "1500", "unit": "kg"},
      {"key": "Enerji Tipi", "value": "Li-Ion Batarya", "unit": ""}
    ]
  },
  "battery": {
    "category_name": "Batarya Sistemi",
    "icon": "fa-battery-full",
    "properties": [
      {"key": "Tip", "value": "Li-Ion", "unit": ""},
      {"key": "Voltaj", "value": "24", "unit": "V"},
      {"key": "Kapasite", "value": "20", "unit": "Ah"}
    ]
  }
}
```

#### D. Özellik Bullet'ları Çıkar:

**PDF'deki özellik açıklamalarından (Features, Highlights):**
- Kompakt tasarım
- Li-Ion batarya
- Çıkarılabilir batarya
- Stabilizasyon tekerlekleri (opsiyonel)
- Hafif yapı (120kg)
- BMS korumalı

**Her özellik için 8 varyasyon üret!**

### 3️⃣ İÇERİK ÜRETME AŞAMASI

#### A. 8 Varyasyon Sistemi:

**Her özellik için şunları yaz:**

**Örnek Özellik: "24V/20Ah Li-Ion Batarya"**

```json
{
  "li-ion-battery": {
    "technical": "24V 20Ah Li-Ion batarya sistemi, BMS korumalı, 4-6 saat operasyon kapasitesi, 2-3 saat şarj süresi, 1500+ şarj döngüsü ömrü",

    "benefit": "Tam gün çalışın, şarj beklemeyin - tek şarjda 6 saate kadar kesintisiz operasyon",

    "slogan": "Bir Şarj, Tam Gün İş!",

    "motto": "Li-Ion teknoloji ile sınırsız verimlilik",

    "short_bullet": "4-6 saat kesintisiz, sıfır bakım, uzun ömür",

    "long_description": "F4'ün 24V/20Ah Li-Ion batarya sistemi, tek şarjda 4-6 saat kesintisiz operasyon kapasitesi sunar. Geleneksel kurşun asit bataryalara göre 3 kat daha uzun ömürlü, %50 daha hafif ve tamamen bakım gerektirmez. Entegre BMS (Battery Management System) aşırı şarj, derin deşarj ve kısa devre koruması sağlar. Çıkarılabilir tasarımı sayesinde ikinci bir batarya ile 7/24 çalışma imkanı sunar. Sadece 2-3 saat şarj süresinde tam dolum sağlar.",

    "comparison": "Kurşun aside göre 3x uzun ömür (1500 vs 500 döngü), %50 daha hafif (20kg vs 40kg), sıfır bakım maliyeti, 2-3 saat hızlı şarj (vs 8-10 saat)",

    "keywords": "lityum, li-ion, lithium, akü, batarya, şarj, 24 volt, enerji, pil, battery, şarjlı, elektrikli akü",

    "icon": "fa-battery-full",
    "icon_color": "success"
  }
}
```

**Varyasyon Yazma Kuralları:**

| # | Tip | Nasıl Yazılır? | Uzunluk |
|---|-----|----------------|---------|
| 1 | **Teknik** | Mühendislik dili, sayısal veri, birimler | 1-2 cümle |
| 2 | **Fayda** | Müşteriye ne kazandırır? Pratik yarar | 1 cümle |
| 3 | **Slogan** | Akılda kalıcı, vurucu, markalama | 3-6 kelime |
| 4 | **Motto** | Marka değeri, felsefe | 4-8 kelime |
| 5 | **Kısa Bullet** | Hızlı tarama, öz bilgi | 3-6 kelime |
| 6 | **Uzun Açıklama** | Detaylı anlatım, bağlam, ek bilgi | 3-5 cümle |
| 7 | **Karşılaştırma** | Rakip/eski teknoloji ile kıyaslama | 1-2 cümle |
| 8 | **Anahtar Kelime** | Arama/AI için tetikleyiciler | 5-10 kelime |

#### B. FAQ Üret (Minimum 10):

**Kategori Dağılımı:**
- Kullanım: 30% (3 soru)
- Teknik: 25% (2-3 soru)
- Seçenekler: 20% (2 soru)
- Bakım: 15% (1-2 soru)
- Satın Alma: 10% (1 soru)

**F4 için örnek FAQ:**

```json
[
  {
    "category": "usage",
    "question": "F4 transpalet hangi sektörlerde kullanılır?",
    "answer": "F4, kompakt yapısı sayesinde market, e-ticaret deposu, soğuk hava deposu, eczane, küçük üretim tesisleri gibi dar alan gerektiren sektörlerde idealdir. Özellikle dar koridorlu depolarda ve iç mekan operasyonlarında yüksek verimlilik sağlar.",
    "icon": "fa-industry"
  },
  {
    "category": "usage",
    "question": "Dar koridorlarda kullanılabilir mi?",
    "answer": "Evet, F4'ün en büyük avantajlarından biri 400mm'lik kompakt çatal mesafesidir. Bu sayede standart transpaletlerin giremediği dar koridorlarda ve sıkışık alanlarda rahatlıkla çalışabilir.",
    "icon": "fa-arrows-alt-h"
  },
  {
    "category": "usage",
    "question": "Soğuk hava depolarında çalışır mı?",
    "answer": "Evet, F4 -25°C'ye kadar soğuk ortamlarda test edilmiş ve onaylanmıştır. Li-Ion batarya teknolojisi sayesinde soğuk hava depolarında bile yüksek performans gösterir.",
    "icon": "fa-snowflake"
  },
  {
    "category": "technical",
    "question": "Li-Ion batarya ne kadar dayanır?",
    "answer": "24V/20Ah Li-Ion batarya, tek şarjda 4-6 saat kesintisiz operasyon sağlar. Batarya ömrü 1500+ şarj döngüsüdür, bu da günde 1 şarj ile yaklaşık 4-5 yıl kullanım anlamına gelir.",
    "icon": "fa-battery-three-quarters"
  },
  {
    "category": "technical",
    "question": "Şarj süresi ne kadar?",
    "answer": "%0'dan %100'e şarj süresi 2-3 saattir. Hızlı şarj özelliği sayesinde öğle molasında veya vardiya arasında kolayca şarj edilebilir.",
    "icon": "fa-plug"
  },
  {
    "category": "technical",
    "question": "Maksimum kaldırma kapasitesi nedir?",
    "answer": "F4'ün maksimum kaldırma kapasitesi 1500 kg (1.5 ton)'dur. Standart Euro palet (800 kg ortalama yük) için ideal kapasitedir.",
    "icon": "fa-weight-hanging"
  },
  {
    "category": "options",
    "question": "Hangi fork uzunlukları mevcut?",
    "answer": "F4 için 6 farklı fork uzunluğu seçeneği bulunmaktadır: 900mm, 1000mm, 1150mm, 1220mm, 1370mm ve 1500mm. Ayrıca 2 farklı genişlik seçeneği vardır: 560mm (standart) ve 685mm (geniş).",
    "icon": "fa-ruler-horizontal"
  },
  {
    "category": "options",
    "question": "Ekstra batarya alınabilir mi?",
    "answer": "Evet, F4'ün bataryası çıkarılabilir tasarıma sahiptir. İkinci bir batarya alarak 7/24 kesintisiz operasyon sağlayabilirsiniz. Bir batarya kullanılırken diğeri şarj olur.",
    "icon": "fa-battery-half"
  },
  {
    "category": "maintenance",
    "question": "Bakım gereksinimleri nelerdir?",
    "answer": "Li-Ion batarya sistemi tamamen bakım gerektirmez (su ilavesi, asit seviye kontrolü yok). Sadece periyodik genel kontroller (fren, tekerlekler, hidrolik) yeterlidir. Yıllık servis önerilir.",
    "icon": "fa-tools"
  },
  {
    "category": "maintenance",
    "question": "Garanti süresi kaç yıl?",
    "answer": "F4 için standart 2 yıl garanti sağlanır. Li-Ion batarya için ayrıca 2 yıl veya 1000 şarj döngüsü garantisi vardır (hangisi önce dolarsa).",
    "icon": "fa-shield-alt"
  },
  {
    "category": "purchase",
    "question": "Fiyat teklifi nasıl alınır?",
    "answer": "Ürün sayfasındaki iletişim formunu doldurarak veya +90 (XXX) XXX XX XX numaralı telefondan bizimle iletişime geçebilirsiniz. Detaylı teknik danışmanlık ve özel fiyat teklifi için uzman ekibimiz size yardımcı olacaktır.",
    "icon": "fa-phone-alt"
  },
  {
    "category": "usage",
    "question": "E-ticaret deposu için uygun mu?",
    "answer": "Kesinlikle! F4, e-ticaret depolarındaki hızlı sipariş hazırlama süreçleri için idealdir. Kompakt yapısı raf araları geçişi kolaylaştırır, Li-Ion batarya 8 saatlik vardiya boyunca kesintisiz çalışma sağlar.",
    "icon": "fa-box-open"
  }
]
```

**FAQ Yazma Kuralları:**
1. **Gerçek sorular yaz**: Müşterilerin Google'da arayacağı sorular
2. **Uzun kuyruk**: "Transpalet nedir?" yerine "F4 transpalet soğuk hava deposunda kullanılabilir mi?"
3. **Detaylı cevap**: Minimum 2 cümle, maksimum 1 paragraf
4. **Sayısal veri**: Mümkün olduğunca somut bilgi (1500 kg, 4-6 saat, -25°C)
5. **İkon ata**: Her soruya uygun FontAwesome ikonu

#### C. Anahtar Kelimeler Üret:

**3 Kategori:**

```json
{
  "keywords": {
    "primary": [
      "F4 transpalet",
      "1.5 ton transpalet",
      "lityum transpalet",
      "kompakt transpalet",
      "hafif transpalet"
    ],
    "synonyms": [
      "palet taşıyıcı",
      "palet kaldırıcı",
      "el transpaleti",
      "akülü palet",
      "lityum akülü transpalet",
      "lithium pallet truck",
      "li-ion pallet truck",
      "elektrikli palet taşıyıcı",
      "elektrikli transpalet",
      "bataryalı palet",
      "şarjlı transpalet",
      "palet arabası elektrikli"
    ],
    "usage_jargon": [
      "soğuk hava deposu transpalet",
      "frigo transpaleti",
      "dar koridor transpalet",
      "market transpaleti",
      "depo transpaleti",
      "lojistik transpalet",
      "kargo transpaleti",
      "e-ticaret deposu",
      "portif palet",
      "hafif yük taşıma",
      "kısa mesafe taşıma",
      "iç mekan transpalet",
      "gıda deposu",
      "soğuk zincir ekipmanı",
      "eczane deposu"
    ]
  }
}
```

**Anahtar Kelime Kuralları:**
1. **Primary (5-8)**: Ana tanımlayıcılar (model, kapasite, özellik)
2. **Synonyms (10-15)**: Müşterilerin farklı ifadeleri, İngilizce karşılıklar
3. **Usage/Jargon (10-15)**: Kullanım senaryoları, sektör jargonu, müşteri dili
4. **7 Kategori AYRI**: Forklift ≠ Transpalet ≠ İstif Makinesi

#### D. Sektör Listesi Oluştur (15-30):

**Ürün özelliklerine göre belirle:**

**F4 için (Kompakt, Hafif, İç Mekan, Li-Ion):**

```json
{
  "industries": [
    {"name": "Market/Süpermarket", "icon": "fa-shopping-cart", "relevance": "high"},
    {"name": "E-ticaret Deposu", "icon": "fa-box", "relevance": "high"},
    {"name": "Soğuk Hava Deposu", "icon": "fa-snowflake", "relevance": "high"},
    {"name": "Gıda Lojistiği", "icon": "fa-apple-alt", "relevance": "high"},
    {"name": "Eczane/İlaç Deposu", "icon": "fa-pills", "relevance": "medium"},
    {"name": "Hastane Lojistiği", "icon": "fa-hospital", "relevance": "medium"},
    {"name": "Tekstil Deposu", "icon": "fa-tshirt", "relevance": "medium"},
    {"name": "Elektronik Depo", "icon": "fa-microchip", "relevance": "medium"},
    {"name": "Küçük Üretim Tesisi", "icon": "fa-cogs", "relevance": "medium"},
    {"name": "Mobilya Mağazası", "icon": "fa-couch", "relevance": "low"},
    {"name": "Yedek Parça Deposu", "icon": "fa-wrench", "relevance": "low"},
    {"name": "Kitap/Kırtasiye Deposu", "icon": "fa-book", "relevance": "low"},
    {"name": "Hırdavat Mağazası", "icon": "fa-tools", "relevance": "low"},
    {"name": "Kozmetik Deposu", "icon": "fa-spray-can", "relevance": "low"},
    {"name": "Ayakkabı Mağazası", "icon": "fa-shoe-prints", "relevance": "low"},
    {"name": "Giyim Mağazası", "icon": "fa-tshirt", "relevance": "low"},
    {"name": "Oyuncak Mağazası", "icon": "fa-gamepad", "relevance": "low"},
    {"name": "Pet Shop", "icon": "fa-paw", "relevance": "low"}
  ]
}
```

**Sektör Seçim Mantığı:**
- **Kompakt (400mm) → Dar koridor**: Market, e-ticaret, soğuk hava
- **Hafif (120kg) → İç mekan**: Hastane, eczane, ofis
- **Li-Ion (-25°C) → Soğuk ortam**: Gıda, frigo, soğuk zincir
- **1.5 ton → Hafif/orta yük**: Palet, karton kutu (ağır sanayi DEĞİL)

#### E. Ürün Açıklaması Yaz (400-600 kelime):

**3 Katmanlı Yapı:**

**1. Hikayeci Giriş (100-150 kelime):**
```
Deponuzda yer daraldı mı? Dar koridorlarda manevra yaparken zorlanıyor musunuz? İşte F4, tam da bu sorunlar için tasarlandı. Sadece 400mm'lik çatal mesafesi ile şimdiye kadar erişemediğiniz alanlara kolayca ulaşın. 120 kg ağırlığıyla piyasadaki en hafif transpalet olmasına rağmen 1.5 ton yükü güvenle taşır.

Li-Ion batarya teknolojisi sayesinde sabah şarj edin, akşama kadar çalışın. Artık batarya değiştirme, bakım yapma veya kurşun asit'in ağırlığıyla uğraşma yok. F4, küçük işletmelerin büyük dostu! Soğuk hava deposundan e-ticaret merkezine, marketten küçük üretim tesisine - her ortamda maksimum verimlilik.
```

**Ton:** Samimi, dikkat çeken, sorun-çözüm odaklı, ürünü öven

**2. Profesyonel Teknik (200-300 kelime):**
```
F4, EP Equipment'ın modüler platform teknolojisi ile geliştirilmiş, endüstriyel sınıf bir elektrikli transpalettir. 24V/20Ah Li-Ion batarya sistemi, tek şarjda 4-6 saat kesintisiz operasyon kapasitesi sunar. Çıkarılabilir batarya tasarımı sayesinde ikinci bir batarya ile 7/24 çalışma mümkündür. Geleneksel kurşun asit bataryalara göre 3 kat daha uzun ömür (1500+ döngü), %50 daha hafif ve tamamen bakım gerektirmez.

Kompakt geometri (400mm çatal mesafesi) dar koridorlarda ve sıkışık alanlarda üstün manevra kabiliyeti sağlar. 120 kg ağırlığıyla sınıfının en hafif modeli olmasına rağmen 1500 kg kaldırma kapasitesine sahiptir. 6 farklı fork uzunluğu (900-1500mm) ve 2 farklı genişlik seçeneği (560/685mm) ile her uygulamaya özelleştirilebilir.

Entegre BMS (Battery Management System) aşırı şarj, derin deşarj ve kısa devre koruması sağlar. IP54 koruma sınıfı ile toz ve su sıçramasına karşı dayanıklıdır. -25°C ile +45°C arasında sorunsuz çalışma kabiliyeti, soğuk hava deposu uygulamaları için idealdir. CE sertifikalı, Avrupa güvenlik standartlarına tam uyumludur.

Opsiyonel stabilizasyon tekerlekleri sistemi, yük taşırken ekstra denge sağlar ve devrilme riskini minimize eder. Ergonomik tutamaç tasarımı operatör yorgunluğunu azaltır. Sessiz çalışma (< 60 dB) sayesinde hastane, market gibi sessizlik gereken ortamlarda rahatlıkla kullanılabilir.
```

**Ton:** Ciddi, mühendislik dili, sayısal veri, standartlar

**3. Detay/Nüans (100-150 kelime):**
```
F4'ü günlük kullanımda öne çıkaran detaylar: Li-Ion batarya sayesinde molalarda kısa şarj yapılabilir (fırsat şarjı), bu da uzun vardiyalarda büyük avantaj sağlar. Küçük operasyonlar için tek batarya yeterlidir, büyüyen işletmeler ikinci batarya ekleyerek kapasite artırabilir.

Soğuk hava deposu kullanıcıları için önemli not: -25°C'de bile batarya performansı %85+ seviyesindedir. Market uygulamalarında müşteri alanına çıkılması gerektiğinde sessiz çalışma ve kompakt yapı büyük kolaylık sağlar.

Servis ve yedek parça desteği Türkiye genelinde mevcuttur. EP Equipment'ın global distribütör ağı sayesinde orijinal parça temininde sorun yaşanmaz. İlk 2 yıl garanti kapsamındadır, opsiyonel garanti uzatma paketleri mevcuttur.
```

**Ton:** Bilgilendirici, pratik ipuçları, kullanım senaryoları

### 4️⃣ SEEDER KODU ÜRET

**Tam PHP seeder kodu oluştur:**

```php
<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Shop\Models\ShopProduct;
use Modules\Shop\Models\ShopCategory;
use Modules\Shop\Models\ShopBrand;

class Transpalet_F4_201_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kategori ve marka bul
        $category = ShopCategory::where('slug', 'transpalet')->first();
        $brand = ShopBrand::where('slug', 'ep-equipment')->first();

        // Ürünü oluştur
        $product = ShopProduct::updateOrCreate(
            ['sku' => 'F4-201'],  // Benzersiz tanımlayıcı
            [
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'is_active' => true,
                'is_featured' => false,

                // BAŞLIK (Çoklu dil - JSON)
                'title' => json_encode([
                    'tr' => 'F4 1.5 Ton Lityum Akülü Transpalet',
                    'en' => 'F4 1.5 Ton Li-Ion Pallet Truck'
                ]),

                // 8 VARYASYONLU İÇERİK
                'content_variations' => json_encode([
                    // Feature 1: Li-Ion Battery
                    'li-ion-battery' => [
                        'technical' => '24V 20Ah Li-Ion batarya sistemi, BMS korumalı, 4-6 saat operasyon kapasitesi, 2-3 saat şarj süresi, 1500+ şarj döngüsü ömrü',
                        'benefit' => 'Tam gün çalışın, şarj beklemeyin - tek şarjda 6 saate kadar kesintisiz operasyon',
                        'slogan' => 'Bir Şarj, Tam Gün İş!',
                        'motto' => 'Li-Ion teknoloji ile sınırsız verimlilik',
                        'short_bullet' => '4-6 saat kesintisiz, sıfır bakım, uzun ömür',
                        'long_description' => 'F4\'ün 24V/20Ah Li-Ion batarya sistemi, tek şarjda 4-6 saat kesintisiz operasyon kapasitesi sunar. Geleneksel kurşun asit bataryalara göre 3 kat daha uzun ömürlü, %50 daha hafif ve tamamen bakım gerektirmez. Entegre BMS (Battery Management System) aşırı şarj, derin deşarj ve kısa devre koruması sağlar. Çıkarılabilir tasarımı sayesinde ikinci bir batarya ile 7/24 çalışma imkanı sunar.',
                        'comparison' => 'Kurşun aside göre 3x uzun ömür (1500 vs 500 döngü), %50 daha hafif (20kg vs 40kg), sıfır bakım maliyeti',
                        'keywords' => 'lityum, li-ion, lithium, akü, batarya, şarj, 24 volt, enerji, pil, battery',
                        'icon' => 'fa-battery-full',
                        'icon_color' => 'success'
                    ],

                    // Feature 2: Compact Design
                    'compact-design' => [
                        'technical' => '400mm çatal mesafesi, 1150mm toplam uzunluk, 120kg ağırlık, dar koridor uyumlu',
                        'benefit' => 'Standart transpaletlerin giremediği alanlara erişin',
                        'slogan' => 'Küçük Yapı, Büyük İşler!',
                        'motto' => 'Kompakt tasarım, sınırsız erişim',
                        'short_bullet' => '400mm çatal, dar koridor, hafif yapı',
                        'long_description' => 'F4\'ün 400mm\'lik çatal mesafesi, piyasadaki en kompakt transpalet tasarımlarından biridir. Bu sayede market rafları, dar depo koridorları ve sıkışık üretim alanlarında rahatlıkla manevra yapabilir. Sadece 120kg ağırlığıyla operatör için kolay kontrol sağlar, zemin yükünü minimize eder.',
                        'comparison' => 'Standart 560mm çatala göre %28 daha kompakt, 180kg modellere göre %33 daha hafif',
                        'keywords' => 'kompakt, küçük, dar koridor, hafif, taşınabilir, manevra',
                        'icon' => 'fa-compress-alt',
                        'icon_color' => 'primary'
                    ],

                    // Feature 3: Removable Battery
                    'removable-battery' => [
                        'technical' => 'Çıkarılabilir Li-Ion batarya, 2-3 saat şarj, ikinci batarya desteği, sıcak takas sistemi',
                        'benefit' => '7/24 kesintisiz çalışma - bir batarya kullanılırken diğeri şarj olur',
                        'slogan' => 'Dur-Kalk Yok, Sürekli İş!',
                        'motto' => 'Çift batarya sistemi ile sonsuz çalışma',
                        'short_bullet' => 'Çıkarılabilir, ikinci batarya, 7/24 operasyon',
                        'long_description' => 'F4\'ün çıkarılabilir batarya tasarımı, ikinci bir batarya ile 24 saat kesintisiz operasyon imkanı sağlar. Bir batarya bittiğinde sadece 30 saniyede değiştirin ve çalışmaya devam edin. Şarj istasyonunda yedek batarya her zaman hazır. Vardiya değişimlerinde zaman kaybı olmaz.',
                        'comparison' => 'Sabit bataryalı modellere göre %100 daha uzun operasyon süresi (12 saat vs 6 saat)',
                        'keywords' => 'çıkarılabilir, değiştirilebilir, yedek batarya, sıcak takas, kesintisiz',
                        'icon' => 'fa-exchange-alt',
                        'icon_color' => 'warning'
                    ],

                    // Feature 4: Modular Platform
                    'modular-platform' => [
                        'technical' => 'EP Equipment modüler platform, 6 fork uzunluğu (900-1500mm), 2 genişlik (560/685mm), opsiyonel stabilizasyon',
                        'benefit' => 'İhtiyacınıza özel konfigürasyon - standart palet, Euro palet, özel boyutlar',
                        'slogan' => 'Senin İşin, Senin Transpalettin!',
                        'motto' => 'Modüler yapı, sınırsız konfigürasyon',
                        'short_bullet' => '6 fork seçeneği, özelleştirilebilir, modüler',
                        'long_description' => 'F4, EP Equipment\'ın kanıtlanmış modüler platform teknolojisi üzerine inşa edilmiştir. 6 farklı fork uzunluğu (900, 1000, 1150, 1220, 1370, 1500mm) ve 2 farklı genişlik seçeneği (560mm standart, 685mm geniş) ile her uygulamaya uyarlanabilir. Opsiyonel stabilizasyon tekerlekleri sistemi ekleyerek yüksek yüklerde ekstra güvenlik sağlayabilirsiniz.',
                        'comparison' => 'Standart tek boyut modellere göre 12 farklı konfigürasyon seçeneği',
                        'keywords' => 'modüler, özelleştirilebilir, konfigüre, opsiyonel, seçenekler',
                        'icon' => 'fa-puzzle-piece',
                        'icon_color' => 'info'
                    ],

                    // Feature 5: Cold Storage Capability
                    'cold-storage' => [
                        'technical' => '-25°C ile +45°C çalışma aralığı, Li-Ion düşük sıcaklık performansı, IP54 koruma, anti-kondensasyon',
                        'benefit' => 'Soğuk hava deposunda bile tam performans - performans kaybı yok',
                        'slogan' => 'Soğukta da Güçlü!',
                        'motto' => 'Her sıcaklıkta aynı performans',
                        'short_bullet' => '-25°C dayanıklı, frigo uyumlu, donmaz',
                        'long_description' => 'F4, Li-Ion batarya teknolojisi sayesinde -25°C\'ye kadar soğuk ortamlarda test edilmiş ve onaylanmıştır. Geleneksel kurşun asit bataryaların performans kaybettiği soğuk koşullarda %85+ verimlilik sağlar. Gıda lojistiği, soğuk hava deposu, dondurulmuş ürün depoları için ideal çözümdür. IP54 koruma sınıfı ile nem ve yoğuşmaya karşı korunmuştur.',
                        'comparison' => 'Kurşun asit -10°C\'de %40 performans kaybeder, Li-Ion -25°C\'de %15',
                        'keywords' => 'soğuk hava, frigo, dondurucu, düşük sıcaklık, gıda deposu',
                        'icon' => 'fa-snowflake',
                        'icon_color' => 'info'
                    ],

                    // Feature 6: Zero Maintenance
                    'zero-maintenance' => [
                        'technical' => 'Li-Ion sıfır bakım, su ilavesi yok, asit kontrolü yok, self-diagnosis sistem, BMS izleme',
                        'benefit' => 'Bakım maliyeti sıfır - sadece çalıştırın',
                        'slogan' => 'Al Kullan, Unut!',
                        'motto' => 'Bakım yok, sadece verimlilik var',
                        'short_bullet' => 'Sıfır bakım, su yok, asit yok',
                        'long_description' => 'F4\'ün Li-Ion batarya sistemi tamamen bakım gerektirmez. Kurşun asit bataryalarda zorunlu olan haftalık su ilavesi, aylık asit seviye kontrolü, terminal temizliği gibi işlemler tamamen ortadan kalkar. BMS sistemi batarya sağlığını otomatik izler ve sorun durumunda uyarı verir. Bu sayede yıllık bakım maliyetleri %80 azalır.',
                        'comparison' => 'Kurşun asit: haftalık bakım, yıllık 50 saat zaman; Li-Ion: sıfır bakım',
                        'keywords' => 'bakım yok, bakımsız, kolay kullan, self-service, otomatik',
                        'icon' => 'fa-check-circle',
                        'icon_color' => 'success'
                    ]
                ]),

                // ANAHTAR KELİMELER (AI asistan için)
                'keywords' => json_encode([
                    'primary' => [
                        'F4 transpalet',
                        '1.5 ton transpalet',
                        'lityum transpalet',
                        'kompakt transpalet',
                        'hafif transpalet'
                    ],
                    'synonyms' => [
                        'palet taşıyıcı',
                        'palet kaldırıcı',
                        'el transpaleti',
                        'akülü palet',
                        'lityum akülü transpalet',
                        'lithium pallet truck',
                        'li-ion pallet truck',
                        'elektrikli palet taşıyıcı',
                        'elektrikli transpalet',
                        'bataryalı palet',
                        'şarjlı transpalet',
                        'palet arabası elektrikli'
                    ],
                    'usage_jargon' => [
                        'soğuk hava deposu transpalet',
                        'frigo transpaleti',
                        'dar koridor transpalet',
                        'market transpaleti',
                        'depo transpaleti',
                        'lojistik transpalet',
                        'kargo transpaleti',
                        'e-ticaret deposu',
                        'portif palet',
                        'hafif yük taşıma',
                        'kısa mesafe taşıma',
                        'iç mekan transpalet',
                        'gıda deposu',
                        'soğuk zincir ekipmanı',
                        'eczane deposu'
                    ]
                ]),

                // FAQ (Minimum 10 soru)
                'faq' => json_encode([
                    [
                        'category' => 'usage',
                        'question' => 'F4 transpalet hangi sektörlerde kullanılır?',
                        'answer' => 'F4, kompakt yapısı sayesinde market, e-ticaret deposu, soğuk hava deposu, eczane, küçük üretim tesisleri gibi dar alan gerektiren sektörlerde idealdir.',
                        'icon' => 'fa-industry'
                    ],
                    [
                        'category' => 'usage',
                        'question' => 'Dar koridorlarda kullanılabilir mi?',
                        'answer' => 'Evet, 400mm çatal mesafesi sayesinde standart transpaletlerin giremediği dar koridorlarda rahatlıkla çalışabilir.',
                        'icon' => 'fa-arrows-alt-h'
                    ],
                    [
                        'category' => 'usage',
                        'question' => 'Soğuk hava depolarında çalışır mı?',
                        'answer' => 'Evet, -25°C\'ye kadar test edilmiş ve onaylanmıştır. Li-Ion teknoloji soğukta bile yüksek performans gösterir.',
                        'icon' => 'fa-snowflake'
                    ],
                    [
                        'category' => 'technical',
                        'question' => 'Li-Ion batarya ne kadar dayanır?',
                        'answer' => 'Tek şarjda 4-6 saat kesintisiz operasyon. Batarya ömrü 1500+ şarj döngüsü (yaklaşık 4-5 yıl günlük kullanımda).',
                        'icon' => 'fa-battery-three-quarters'
                    ],
                    [
                        'category' => 'technical',
                        'question' => 'Şarj süresi ne kadar?',
                        'answer' => '%0\'dan %100\'e 2-3 saat. Hızlı şarj özelliği sayesinde öğle molasında şarj edilebilir.',
                        'icon' => 'fa-plug'
                    ],
                    [
                        'category' => 'technical',
                        'question' => 'Maksimum kaldırma kapasitesi nedir?',
                        'answer' => '1500 kg (1.5 ton). Standart Euro palet (800 kg ortalama yük) için ideal kapasitedir.',
                        'icon' => 'fa-weight-hanging'
                    ],
                    [
                        'category' => 'options',
                        'question' => 'Hangi fork uzunlukları mevcut?',
                        'answer' => '6 farklı uzunluk: 900mm, 1000mm, 1150mm, 1220mm, 1370mm, 1500mm. İki genişlik: 560mm (standart), 685mm (geniş).',
                        'icon' => 'fa-ruler-horizontal'
                    ],
                    [
                        'category' => 'options',
                        'question' => 'Ekstra batarya alınabilir mi?',
                        'answer' => 'Evet, çıkarılabilir tasarım sayesinde ikinci batarya ile 7/24 kesintisiz operasyon sağlanabilir.',
                        'icon' => 'fa-battery-half'
                    ],
                    [
                        'category' => 'maintenance',
                        'question' => 'Bakım gereksinimleri nelerdir?',
                        'answer' => 'Li-Ion batarya sıfır bakım gerektirir (su/asit yok). Sadece periyodik genel kontroller (fren, tekerlek) yeterlidir.',
                        'icon' => 'fa-tools'
                    ],
                    [
                        'category' => 'maintenance',
                        'question' => 'Garanti süresi kaç yıl?',
                        'answer' => 'Standart 2 yıl garanti. Li-Ion batarya için 2 yıl veya 1000 şarj döngüsü garantisi.',
                        'icon' => 'fa-shield-alt'
                    ],
                    [
                        'category' => 'purchase',
                        'question' => 'Fiyat teklifi nasıl alınır?',
                        'answer' => 'İletişim formunu doldurarak veya telefon ile uzman ekibimizden detaylı teklif alabilirsiniz.',
                        'icon' => 'fa-phone-alt'
                    ],
                    [
                        'category' => 'usage',
                        'question' => 'E-ticaret deposu için uygun mu?',
                        'answer' => 'Kesinlikle! Hızlı sipariş hazırlama, raf arası geçiş ve 8 saatlik kesintisiz çalışma için idealdir.',
                        'icon' => 'fa-box-open'
                    ]
                ]),

                // SEKTÖRLER (15-30 adet)
                'industries' => json_encode([
                    ['name' => 'Market/Süpermarket', 'icon' => 'fa-shopping-cart', 'relevance' => 'high'],
                    ['name' => 'E-ticaret Deposu', 'icon' => 'fa-box', 'relevance' => 'high'],
                    ['name' => 'Soğuk Hava Deposu', 'icon' => 'fa-snowflake', 'relevance' => 'high'],
                    ['name' => 'Gıda Lojistiği', 'icon' => 'fa-apple-alt', 'relevance' => 'high'],
                    ['name' => 'Eczane/İlaç Deposu', 'icon' => 'fa-pills', 'relevance' => 'medium'],
                    ['name' => 'Hastane Lojistiği', 'icon' => 'fa-hospital', 'relevance' => 'medium'],
                    ['name' => 'Tekstil Deposu', 'icon' => 'fa-tshirt', 'relevance' => 'medium'],
                    ['name' => 'Elektronik Depo', 'icon' => 'fa-microchip', 'relevance' => 'medium'],
                    ['name' => 'Küçük Üretim Tesisi', 'icon' => 'fa-cogs', 'relevance' => 'medium'],
                    ['name' => 'Mobilya Mağazası', 'icon' => 'fa-couch', 'relevance' => 'low'],
                    ['name' => 'Yedek Parça Deposu', 'icon' => 'fa-wrench', 'relevance' => 'low'],
                    ['name' => 'Kitap/Kırtasiye', 'icon' => 'fa-book', 'relevance' => 'low'],
                    ['name' => 'Hırdavat', 'icon' => 'fa-tools', 'relevance' => 'low'],
                    ['name' => 'Kozmetik Deposu', 'icon' => 'fa-spray-can', 'relevance' => 'low'],
                    ['name' => 'Ayakkabı Mağazası', 'icon' => 'fa-shoe-prints', 'relevance' => 'low'],
                    ['name' => 'Giyim Mağazası', 'icon' => 'fa-tshirt', 'relevance' => 'low'],
                    ['name' => 'Oyuncak Mağazası', 'icon' => 'fa-gamepad', 'relevance' => 'low'],
                    ['name' => 'Pet Shop', 'icon' => 'fa-paw', 'relevance' => 'low']
                ]),

                // TEKNİK ÖZELLİKLER (Accordion)
                'technical_specs' => json_encode([
                    'general' => [
                        'category_name' => 'Genel Özellikler',
                        'icon' => 'fa-info-circle',
                        'properties' => [
                            ['key' => 'Model', 'value' => 'F4', 'unit' => ''],
                            ['key' => 'SKU', 'value' => 'F4-201', 'unit' => ''],
                            ['key' => 'Kapasite', 'value' => '1500', 'unit' => 'kg'],
                            ['key' => 'Kategori', 'value' => 'Transpalet', 'unit' => ''],
                            ['key' => 'Enerji Tipi', 'value' => 'Li-Ion Batarya', 'unit' => ''],
                            ['key' => 'Marka', 'value' => 'EP Equipment', 'unit' => '']
                        ]
                    ],
                    'battery' => [
                        'category_name' => 'Batarya Sistemi',
                        'icon' => 'fa-battery-full',
                        'properties' => [
                            ['key' => 'Tip', 'value' => 'Li-Ion', 'unit' => ''],
                            ['key' => 'Voltaj', 'value' => '24', 'unit' => 'V'],
                            ['key' => 'Kapasite', 'value' => '20', 'unit' => 'Ah'],
                            ['key' => 'Operasyon Süresi', 'value' => '4-6', 'unit' => 'saat'],
                            ['key' => 'Şarj Süresi', 'value' => '2-3', 'unit' => 'saat'],
                            ['key' => 'Batarya Ömrü', 'value' => '1500+', 'unit' => 'döngü'],
                            ['key' => 'Çıkarılabilir', 'value' => 'Evet', 'unit' => ''],
                            ['key' => 'BMS Korumalı', 'value' => 'Evet', 'unit' => '']
                        ]
                    ],
                    'dimensions' => [
                        'category_name' => 'Boyutlar ve Ağırlık',
                        'icon' => 'fa-ruler-combined',
                        'properties' => [
                            ['key' => 'Toplam Uzunluk', 'value' => '1150', 'unit' => 'mm'],
                            ['key' => 'Çatal Mesafesi', 'value' => '400', 'unit' => 'mm'],
                            ['key' => 'Toplam Genişlik (560)', 'value' => '560', 'unit' => 'mm'],
                            ['key' => 'Toplam Genişlik (685)', 'value' => '685', 'unit' => 'mm'],
                            ['key' => 'Toplam Yükseklik', 'value' => '1200', 'unit' => 'mm'],
                            ['key' => 'Ağırlık', 'value' => '120', 'unit' => 'kg']
                        ]
                    ],
                    'forks' => [
                        'category_name' => 'Çatal Özellikleri',
                        'icon' => 'fa-grip-horizontal',
                        'properties' => [
                            ['key' => 'Fork Uzunluk Seçenekleri', 'value' => '900/1000/1150/1220/1370/1500', 'unit' => 'mm'],
                            ['key' => 'Fork Genişlik Seçenekleri', 'value' => '560/685', 'unit' => 'mm'],
                            ['key' => 'Kaldırma Yüksekliği', 'value' => '200', 'unit' => 'mm'],
                            ['key' => 'Fork Kalınlığı', 'value' => '50', 'unit' => 'mm'],
                            ['key' => 'Fork Malzemesi', 'value' => 'Çelik', 'unit' => '']
                        ]
                    ],
                    'performance' => [
                        'category_name' => 'Performans',
                        'icon' => 'fa-tachometer-alt',
                        'properties' => [
                            ['key' => 'Hız (Yüksüz)', 'value' => '5.5', 'unit' => 'km/h'],
                            ['key' => 'Hız (Yüklü)', 'value' => '4.5', 'unit' => 'km/h'],
                            ['key' => 'Kaldırma Hızı', 'value' => '20', 'unit' => 'mm/s'],
                            ['key' => 'İndirme Hızı', 'value' => '25', 'unit' => 'mm/s'],
                            ['key' => 'Menzil (Tam Şarj)', 'value' => '15-20', 'unit' => 'km']
                        ]
                    ],
                    'wheels' => [
                        'category_name' => 'Tekerlekler',
                        'icon' => 'fa-dot-circle',
                        'properties' => [
                            ['key' => 'Yük Tekerleği Tipi', 'value' => 'Poliüretan', 'unit' => ''],
                            ['key' => 'Yük Tekerleği Çapı', 'value' => '80', 'unit' => 'mm'],
                            ['key' => 'Direksiyon Tekerleği', 'value' => 'Çift Poliüretan', 'unit' => ''],
                            ['key' => 'Direksiyon Çapı', 'value' => '230', 'unit' => 'mm'],
                            ['key' => 'Stabilizasyon Tekerleği', 'value' => 'Opsiyonel', 'unit' => '']
                        ]
                    ],
                    'brake' => [
                        'category_name' => 'Fren Sistemi',
                        'icon' => 'fa-hand-paper',
                        'properties' => [
                            ['key' => 'Fren Tipi', 'value' => 'Elektromanyetik', 'unit' => ''],
                            ['key' => 'Acil Fren', 'value' => 'Evet', 'unit' => ''],
                            ['key' => 'Park Freni', 'value' => 'Otomatik', 'unit' => ''],
                            ['key' => 'Geri Hareket Freni', 'value' => 'Evet', 'unit' => '']
                        ]
                    ],
                    'safety' => [
                        'category_name' => 'Güvenlik',
                        'icon' => 'fa-shield-alt',
                        'properties' => [
                            ['key' => 'BMS Sistemi', 'value' => 'Evet', 'unit' => ''],
                            ['key' => 'Aşırı Şarj Koruması', 'value' => 'Evet', 'unit' => ''],
                            ['key' => 'Kısa Devre Koruması', 'value' => 'Evet', 'unit' => ''],
                            ['key' => 'Termal Koruma', 'value' => 'Evet', 'unit' => ''],
                            ['key' => 'Acil Durdurma Butonu', 'value' => 'Evet', 'unit' => ''],
                            ['key' => 'Operatör Sensörü', 'value' => 'Evet', 'unit' => '']
                        ]
                    ],
                    'ergonomics' => [
                        'category_name' => 'Ergonomi',
                        'icon' => 'fa-user',
                        'properties' => [
                            ['key' => 'Kumanda Tipi', 'value' => 'Ergonomik Tutamaç', 'unit' => ''],
                            ['key' => 'Tutamaç Malzemesi', 'value' => 'Kaymaz Plastik', 'unit' => ''],
                            ['key' => 'Yükseklik Ayarı', 'value' => 'Sabit', 'unit' => ''],
                            ['key' => 'Gösterge Paneli', 'value' => 'LED Batarya Seviyesi', 'unit' => '']
                        ]
                    ],
                    'environmental' => [
                        'category_name' => 'Çevresel Özellikler',
                        'icon' => 'fa-leaf',
                        'properties' => [
                            ['key' => 'Çalışma Sıcaklığı', 'value' => '-25 ~ +45', 'unit' => '°C'],
                            ['key' => 'Depolama Sıcaklığı', 'value' => '-30 ~ +60', 'unit' => '°C'],
                            ['key' => 'IP Koruma Sınıfı', 'value' => 'IP54', 'unit' => ''],
                            ['key' => 'Gürültü Seviyesi', 'value' => '<60', 'unit' => 'dB'],
                            ['key' => 'Emisyon', 'value' => 'Sıfır', 'unit' => '']
                        ]
                    ],
                    'certifications' => [
                        'category_name' => 'Sertifikalar',
                        'icon' => 'fa-certificate',
                        'properties' => [
                            ['key' => 'CE', 'value' => 'Evet', 'unit' => ''],
                            ['key' => 'ISO 9001', 'value' => 'Evet', 'unit' => ''],
                            ['key' => 'ISO 14001', 'value' => 'Evet', 'unit' => ''],
                            ['key' => 'UL Certified', 'value' => 'Evet', 'unit' => '']
                        ]
                    ],
                    'options' => [
                        'category_name' => 'Opsiyonlar/Aksesuarlar',
                        'icon' => 'fa-plus-circle',
                        'properties' => [
                            ['key' => 'İkinci Li-Ion Batarya', 'value' => 'Opsiyonel', 'unit' => ''],
                            ['key' => 'Stabilizasyon Tekerlekleri', 'value' => 'Opsiyonel', 'unit' => ''],
                            ['key' => '1500mm Uzun Fork', 'value' => 'Opsiyonel', 'unit' => ''],
                            ['key' => 'Geniş Fork (685mm)', 'value' => 'Opsiyonel', 'unit' => ''],
                            ['key' => 'Hızlı Şarj Cihazı', 'value' => 'Opsiyonel', 'unit' => '']
                        ]
                    ]
                ]),

                // ÜRÜN AÇIKLAMASI (3 katmanlı, 400-600 kelime)
                'description' => json_encode([
                    'tr' => "Deponuzda yer daraldı mı? Dar koridorlarda manevra yaparken zorlanıyor musunuz? İşte F4, tam da bu sorunlar için tasarlandı. Sadece 400mm'lik çatal mesafesi ile şimdiye kadar erişemediğiniz alanlara kolayca ulaşın. 120 kg ağırlığıyla piyasadaki en hafif transpalet olmasına rağmen 1.5 ton yükü güvenle taşır. Li-Ion batarya teknolojisi sayesinde sabah şarj edin, akşama kadar çalışın. Artık batarya değiştirme, bakım yapma veya kurşun asit'in ağırlığıyla uğraşma yok. F4, küçük işletmelerin büyük dostu!\n\nF4, EP Equipment'ın modüler platform teknolojisi ile geliştirilmiş, endüstriyel sınıf bir elektrikli transpalettir. 24V/20Ah Li-Ion batarya sistemi, tek şarjda 4-6 saat kesintisiz operasyon kapasitesi sunar. Çıkarılabilir batarya tasarımı sayesinde ikinci bir batarya ile 7/24 çalışma mümkündür. Geleneksel kurşun asit bataryalara göre 3 kat daha uzun ömür (1500+ döngü), %50 daha hafif ve tamamen bakım gerektirmez. Kompakt geometri (400mm çatal mesafesi) dar koridorlarda ve sıkışık alanlarda üstün manevra kabiliyeti sağlar. 120 kg ağırlığıyla sınıfının en hafif modeli olmasına rağmen 1500 kg kaldırma kapasitesine sahiptir. 6 farklı fork uzunluğu (900-1500mm) ve 2 farklı genişlik seçeneği (560/685mm) ile her uygulamaya özelleştirilebilir. Entegre BMS (Battery Management System) aşırı şarj, derin deşarj ve kısa devre koruması sağlar. IP54 koruma sınıfı ile toz ve su sıçramasına karşı dayanıklıdır. -25°C ile +45°C arasında sorunsuz çalışma kabiliyeti, soğuk hava deposu uygulamaları için idealdir.\n\nF4'ü günlük kullanımda öne çıkaran detaylar: Li-Ion batarya sayesinde molalarda kısa şarj yapılabilir (fırsat şarjı), bu da uzun vardiyalarda büyük avantaj sağlar. Küçük operasyonlar için tek batarya yeterlidir, büyüyen işletmeler ikinci batarya ekleyerek kapasite artırabilir. Soğuk hava deposu kullanıcıları için önemli not: -25°C'de bile batarya performansı %85+ seviyesindedir. Market uygulamalarında müşteri alanına çıkılması gerektiğinde sessiz çalışma ve kompakt yapı büyük kolaylık sağlar.",
                    'en' => 'F4 1.5 Ton Li-Ion Pallet Truck - Compact, lightweight, powerful. Ideal for narrow aisles, cold storage, and e-commerce warehouses.'
                ])
            ]
        );

        $this->command->info("✅ F4 Transpalet seeder çalıştırıldı (ID: {$product->id})");
    }
}
```

---

## ✅ KONTROL LİSTESİ

Seeder oluştururken şunları doğrula:

- [ ] Başlık standardizasyona uygun
- [ ] En az 5-6 özellik için 8 varyasyon üretildi
- [ ] FAQ minimum 10 soru (5 kategoride)
- [ ] Anahtar kelimeler 3 kategoride (Primary, Synonyms, Usage)
- [ ] Sektör listesi 15-30 adet
- [ ] Teknik özellikler 8-12 kategori
- [ ] Her içerik FontAwesome 6 ikonu aldı
- [ ] Ürün açıklaması 400-600 kelime (3 katmanlı)
- [ ] CTA/Kampanya/Sosyal kanıt YOK
- [ ] JSON syntax hatasız

---

## 🚀 SEEDER KULLANIMI

```bash
# Seeder çalıştır
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\Transpalet_F4_201_Seeder

# Tüm shop seeder'ları çalıştır
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\DatabaseSeeder
```

---

**Versiyon**: V4.0
**Son Güncelleme**: 2025-01-01
