# 🤖 AI PROMPT: PDF'den SHOP Product JSON'a Dönüştürme

## 🎯 AMAÇ

EP Equipment broşür PDF'lerini analiz edip, yeni SHOP modülümüz için uygun JSON formatına dönüştürmek.

**ÖNEMLİ:** Bir PDF'de birden fazla ürün olabilir (örn: CPD15TVL, CPD18TVL, CPD20TVL)

---

## 📋 INPUT

**PDF Dosyası:**
- EP Equipment ürün broşürleri
- Teknik özellikler tabloları
- Ürün görselleri ve açıklamaları
- Özellik listeleri

**PDF Türleri:**
1. **Tek Ürün PDF:** Tek bir ürün modeli (örn: EST122)
2. **Çoklu Ürün PDF:** Aynı seride birden fazla model (örn: CPD15/18/20TVL)

---

## 📤 OUTPUT FORMAT

### **Her Ürün İçin Ayrı JSON**

```json
{
  "product_info": {
    "sku": "CPD15TVL",
    "model_number": "CPD15TVL",
    "series_name": "CPD TVL Series",
    "product_type": "physical",
    "condition": "new"
  },

  "basic_data": {
    "name": {
      "tr": "CPD15TVL Elektrikli Forklift",
      "en": "CPD15TVL Electric Forklift"
    },
    "short_description": {
      "tr": "80V Li-Ion teknolojili kompakt 3 tekerlekli elektrikli forklift",
      "en": "Compact 3-wheel dual-drive counterbalance forklift with 80V Li-Ion battery"
    },
    "long_description": {
      "tr": "CPD15TVL, 80 voltluk Li-Ion batarya teknolojisi etrafında tasarlanmış kompakt 3 tekerlekli elektrikli forklift. Güçlü çift sürüş AC çekiş motorları, geniş bacak alanı (394mm) ve ayarlanabilir direksiyon simidi ile üstün operatör konforu sunar.",
      "en": "CPD15TVL is a compact 3-wheel electric forklift designed around 80V Li-Ion battery technology. Features powerful dual drive AC traction motors, big legroom (394mm) and adjustable steering wheel for superior operator comfort."
    }
  },

  "category_brand": {
    "category_path": "Forklift > CPD Serisi > Elektrikli",
    "brand_name": "EP Equipment",
    "manufacturer": "EP EQUIPMENT CO.,LTD"
  },

  "pricing": {
    "base_price": null,
    "currency": "TRY",
    "price_on_request": true,
    "installment_available": true,
    "deposit_required": true,
    "deposit_percentage": 30
  },

  "stock_info": {
    "stock_tracking": true,
    "stock_quantity": 0,
    "lead_time_days": 60,
    "availability": "on_order",
    "warranty_months": 24
  },

  "physical_properties": {
    "weight": 2950,
    "dimensions": {
      "length": 2733,
      "width": 1070,
      "height": 2075,
      "unit": "mm"
    },
    "service_weight": 2950
  },

  "technical_specs": {
    "capacity": {
      "load_capacity": {"value": 1500, "unit": "kg"},
      "load_center_distance": {"value": 500, "unit": "mm"}
    },
    "performance": {
      "travel_speed_laden": {"value": 13, "unit": "km/h"},
      "travel_speed_unladen": {"value": 14, "unit": "km/h"},
      "lifting_speed_laden": {"value": 0.33, "unit": "m/s"},
      "lifting_speed_unladen": {"value": 0.45, "unit": "m/s"},
      "max_gradeability_laden": {"value": 10, "unit": "%"},
      "max_gradeability_unladen": {"value": 15, "unit": "%"}
    },
    "electrical": {
      "battery_voltage": {"value": 80, "unit": "V"},
      "battery_capacity": {"value": 150, "unit": "Ah"},
      "battery_type": "Li-Ion",
      "drive_motor_rating": {"value": 5.0, "unit": "kW", "note": "2x5.0kW dual motors"},
      "charger": "80V-35A single-phase integrated"
    },
    "mast": {
      "retracted_height": {"value": 2075, "unit": "mm"},
      "lift_height": {"value": 3000, "unit": "mm"},
      "extended_height": {"value": 4055, "unit": "mm"}
    },
    "dimensions_detail": {
      "length_to_forks": {"value": 1813, "unit": "mm"},
      "overall_width": {"value": 1070, "unit": "mm"},
      "fork_dimensions": "40x100x920mm",
      "turning_radius": {"value": 1450, "unit": "mm"}
    }
  },

  "features": {
    "tr": [
      "80V Li-Ion batarya teknolojisi ile güçlü performans",
      "Çift sürüş AC çekiş motorları (2x5.0kW)",
      "Geniş bacak alanı (394mm) ile yüksek operatör konforu",
      "Ayarlanabilir direksiyon simidi ve konforlu kova koltuk",
      "Uzun özerklik ve ara şarj imkanı",
      "16A fişli tek fazlı entegre şarj cihazı",
      "Kompakt 3 tekerlek tasarımı",
      "Dar koridorlarda çalışma (1450mm dönüş yarıçapı)",
      "Yüksek mukavemetli mast yapısı",
      "Optimize görüş alanı ve stabilite"
    ],
    "en": [
      "Powerful performance with 80V Li-Ion battery technology",
      "Dual drive AC traction motors (2x5.0kW)",
      "Big legroom (394mm) for higher operator comfort",
      "Adjustable steering wheel and comfortable bucket seat",
      "Long autonomy and occasional charge capability",
      "Single phase integrated charger with 16A plug",
      "Compact 3-wheel design",
      "Narrow aisle operation (1450mm turning radius)",
      "High-strengthened mast structure",
      "Optimal visibility and stability"
    ]
  },

  "highlighted_features": [
    {
      "icon": "battery-charging",
      "priority": 1,
      "category": "power",
      "title": {"tr": "80V Li-Ion Teknoloji", "en": "80V Li-Ion Technology"},
      "description": {"tr": "6 saat çalışma süresi ve ara şarj imkanı", "en": "6 hours working time with occasional charge"}
    },
    {
      "icon": "zap",
      "priority": 2,
      "category": "performance",
      "title": {"tr": "Güçlü Dual Motor", "en": "Powerful Dual Motors"},
      "description": {"tr": "2x5.0kW AC çekiş motorları", "en": "2x5.0kW AC traction motors"}
    },
    {
      "icon": "user",
      "priority": 3,
      "category": "comfort",
      "title": {"tr": "Geniş Çalışma Alanı", "en": "Large Workspace"},
      "description": {"tr": "394mm bacak alanı ile üstün konfor", "en": "394mm legroom for superior comfort"}
    },
    {
      "icon": "minimize-2",
      "priority": 4,
      "category": "maneuverability",
      "title": {"tr": "Kompakt Tasarım", "en": "Compact Design"},
      "description": {"tr": "1450mm dönüş yarıçapı, dar koridorlarda ideal", "en": "1450mm turning radius, ideal for narrow aisles"}
    }
  },

  "use_cases": {
    "tr": [
      "Depo ve lojistik operasyonları",
      "Dar koridorlu depolar (3.5m koridor genişliği)",
      "İç mekan malzeme taşıma",
      "Palet yükleme ve boşaltma",
      "Raf yükleme işlemleri (3m yüksekliğe kadar)",
      "Küçük-orta ölçekli depolar",
      "E-ticaret depoları"
    ],
    "en": [
      "Warehouse and logistics operations",
      "Narrow aisle warehouses (3.5m aisle width)",
      "Indoor material handling",
      "Pallet loading and unloading",
      "Rack loading operations (up to 3m height)",
      "Small to medium-sized warehouses",
      "E-commerce warehouses"
    ]
  },

  "competitive_advantages": {
    "tr": [
      "48V sistemlere göre %20 daha yüksek güç verimliliği",
      "Şarj başına 6 saat çalışma süresi",
      "Herhangi bir prizden şarj edilebilme",
      "Kiralama işleri için ideal (Li-Ion + yerleşik şarj)",
      "Düşük bakım maliyeti",
      "Sessiz çalışma",
      "Sıfır emisyon"
    ],
    "en": [
      "20% higher power efficiency than 48V systems",
      "6 hours working time per charge",
      "Can be charged at any power outlet",
      "Ideal for rental business (Li-Ion + onboard charging)",
      "Low maintenance cost",
      "Silent operation",
      "Zero emission"
    ]
  },

  "target_industries": {
    "tr": [
      "Lojistik ve Depolama",
      "E-ticaret",
      "Perakende",
      "Üretim Tesisleri",
      "Soğuk Hava Depoları",
      "Gıda Dağıtım"
    ],
    "en": [
      "Logistics and Warehousing",
      "E-commerce",
      "Retail",
      "Manufacturing Facilities",
      "Cold Storage",
      "Food Distribution"
    ]
  },

  "faq_data": [
    {
      "question": {"tr": "Şarj süresi ne kadar?", "en": "What is the charging time?"},
      "answer": {"tr": "35A entegre şarj cihazı ile yaklaşık 4-5 saat.", "en": "Approximately 4-5 hours with 35A integrated charger."}
    },
    {
      "question": {"tr": "Hangi mast seçenekleri mevcut?", "en": "What mast options are available?"},
      "answer": {"tr": "2-Standard (3.0m-4.0m), 2-Free (3.0m-4.0m), 3-Free (4.0m-6.0m) mast seçenekleri.", "en": "2-Standard (3.0m-4.0m), 2-Free (3.0m-4.0m), 3-Free (4.0m-6.0m) mast options."}
    },
    {
      "question": {"tr": "Kabin opsiyonu var mı?", "en": "Is cabin option available?"},
      "answer": {"tr": "Evet, tam kapalı kabin opsiyonu mevcuttur.", "en": "Yes, fully closed cabin option is available."}
    }
  ],

  "variants": [
    {
      "sku": "CPD15TVL-3000-150",
      "name": {"tr": "3000mm Mast + 150Ah Batarya", "en": "3000mm Mast + 150Ah Battery"},
      "option_values": {
        "mast_height": "3000mm",
        "battery_capacity": "150Ah"
      },
      "price_modifier": 0,
      "stock_quantity": 5,
      "is_default": true
    },
    {
      "sku": "CPD15TVL-4500-150",
      "name": {"tr": "4500mm Mast + 150Ah Batarya", "en": "4500mm Mast + 150Ah Battery"},
      "option_values": {
        "mast_height": "4500mm",
        "battery_capacity": "150Ah"
      },
      "price_modifier": 15000,
      "stock_quantity": 2,
      "is_default": false
    }
  ],

  "options": [
    {
      "category": "mast",
      "name": {"tr": "Mast Yüksekliği", "en": "Mast Height"},
      "values": ["3000mm", "3600mm", "4000mm", "4500mm", "5000mm", "5500mm", "6000mm"]
    },
    {
      "category": "cabin",
      "name": {"tr": "Kabin", "en": "Cabin"},
      "values": [
        {"tr": "Kabin Yok", "en": "No Cabin"},
        {"tr": "Temel Yarı Kapalı", "en": "Basic Semi-enclosed"},
        {"tr": "Gelişmiş Yarı Kapalı", "en": "Upgraded Semi-enclosed"},
        {"tr": "Tam Kapalı", "en": "Full Cabin"}
      ]
    },
    {
      "category": "wheels",
      "name": {"tr": "Tekerlek Tipi", "en": "Wheel Type"},
      "values": [
        {"tr": "Solid", "en": "Solid"},
        {"tr": "İzsiz Solid", "en": "Non-marking Solid"}
      ]
    },
    {
      "category": "seat",
      "name": {"tr": "Koltuk", "en": "Seat"},
      "values": [
        {"tr": "Standart", "en": "Regular"},
        {"tr": "Premium", "en": "Premium"},
        {"tr": "Süspansiyonlu", "en": "Suspension"}
      ]
    }
  ],

  "media_gallery": [
    {
      "type": "image",
      "url": "cpd15tvl-main.jpg",
      "alt": {"tr": "CPD15TVL Elektrikli Forklift", "en": "CPD15TVL Electric Forklift"},
      "is_primary": true,
      "sort_order": 1
    },
    {
      "type": "image",
      "url": "cpd15tvl-side.jpg",
      "alt": {"tr": "Yan Görünüm", "en": "Side View"},
      "is_primary": false,
      "sort_order": 2
    },
    {
      "type": "image",
      "url": "cpd15tvl-cabin.jpg",
      "alt": {"tr": "Kabin Detayı", "en": "Cabin Detail"},
      "is_primary": false,
      "sort_order": 3
    },
    {
      "type": "pdf",
      "url": "cpd15tvl-brochure.pdf",
      "alt": {"tr": "Teknik Broşür", "en": "Technical Brochure"},
      "sort_order": 10
    }
  ],

  "seo_data": {
    "tr": {
      "title": "CPD15TVL Elektrikli Forklift - 1500kg Kapasite | EP Equipment",
      "description": "CPD15TVL 80V Li-Ion elektrikli forklift. 1500kg kapasite, 3000mm kaldırma, kompakt tasarım. Dar koridorlar için ideal. ✓ Güçlü dual motor ✓ 6 saat çalışma",
      "keywords": ["elektrikli forklift", "CPD15TVL", "Li-Ion forklift", "1500kg forklift", "3 tekerlekli forklift", "EP Equipment"]
    },
    "en": {
      "title": "CPD15TVL Electric Forklift - 1500kg Capacity | EP Equipment",
      "description": "CPD15TVL 80V Li-Ion electric forklift. 1500kg capacity, 3000mm lift, compact design. Ideal for narrow aisles. ✓ Powerful dual motors ✓ 6 hours operation",
      "keywords": ["electric forklift", "CPD15TVL", "Li-Ion forklift", "1500kg forklift", "3-wheel forklift", "EP Equipment"]
    }
  },

  "related_products": ["CPD18TVL", "CPD20TVL", "EFL302"],
  "cross_sell_products": ["Battery Charger 80V", "Fork Extensions", "Safety Lights"],
  "up_sell_products": ["CPD20TVL", "CPD25L2"],

  "certifications": ["CE", "ISO 9001"],

  "tags": ["electric", "forklift", "li-ion", "compact", "3-wheel", "narrow-aisle", "80v"],

  "metadata": {
    "pdf_source": "02_CPD15-18-20TVL-EN-Brochure.pdf",
    "extraction_date": "2025-10-09",
    "product_family": "CPD TVL Series",
    "voltage_system": "80V",
    "drive_type": "Electric",
    "wheel_configuration": "3-wheel"
  }
}
```

---

## 🔍 ÇEVİRİ KURALLARI

### Teknik Terimler
- **Forklift** → Forklift (aynen kullan)
- **Stacker** → İstif Makinesi
- **Pallet Truck** → Transpalet / Palet Arabası
- **Load Capacity** → Yük Kapasitesi
- **Lift Height** → Kaldırma Yüksekliği
- **Turning Radius** → Dönüş Yarıçapı
- **Mast** → Mast / Direk
- **Li-Ion** → Li-Ion (aynen kullan)
- **Battery** → Batarya
- **Charger** → Şarj Cihazı

### Özellik Açıklamaları
- Teknik değerleri olduğu gibi koru
- Birimlerini çevir: mm → mm, kg → kg, kW → kW (değiştirme)
- Sadece açıklama metinlerini çevir

---

## 📊 ÇOKLU ÜRÜN PDF İŞLEME

**Örnek: CPD15/18/20TVL PDF'i**

Bu PDF'de 3 farklı model var. Her biri için AYRI JSON üret:

1. **CPD15TVL.json**
2. **CPD18TVL.json**
3. **CPD20TVL.json**

**Ortak Özellikler:**
- Aynı series_name kullan
- Aynı features listesini paylaşabilirler
- Farklı olan: capacity, weight, dimensions, sku, model_number

---

## ⚠️ ÖNEMLİ NOTLAR

### 1. YORUM SİSTEMLERİ
**3 Farklı Yorum Tipi Var:**

#### a) Ürün Açıklamaları (Bizim Yazdıklarımız)
```json
"long_description": {
  "tr": "CPD15TVL, 80 voltluk Li-Ion batarya...",
  "en": "CPD15TVL is a compact 3-wheel..."
}
```

#### b) Özellik Notları (Technical Notes)
```json
"technical_specs": {
  "electrical": {
    "drive_motor_rating": {
      "value": 5.0,
      "unit": "kW",
      "note": "2x5.0kW dual motors"  // ← Bu bizim notumuz
    }
  }
}
```

#### c) Müşteri Yorumları (Reviews) - FARKLI TABLO!
```json
// Bu JSON'da YOK!
// shop_product_reviews tablosunda tutulacak
```

### 2. Fiyatlandırma
- PDF'lerde fiyat yoksa: `"price_on_request": true`
- B2B ürünlerse: `"deposit_required": true`
- Taksit varsa: `"installment_available": true`

### 3. Stok Bilgisi
- Yeni ürün: `"stock_quantity": 0, "availability": "on_order"`
- Lead time: Üretim/tedarik süresi (genelde 30-90 gün)

### 4. Varyantlar
- Ana farklılıklar (mast, batarya, kapasite) → Ayrı ürün
- Küçük opsiyonlar (renk, tekerlek, koltuk) → `options` array'inde

### 5. Media Gallery
- PDF'den görsel çıkarmaya çalış
- Placeholder URL'ler kullan
- `is_primary: true` ilk görsele

---

## 🎯 KULLANIM

```bash
# Claude'a gönder:
"Şu PDF'i analiz et ve yukarıdaki formata göre JSON çıkar:
[PDF path]

Dikkat:
- Çoklu ürün varsa her biri için ayrı JSON
- Teknik özellikleri tam çıkar
- Türkçe çevirileri ekle
- Yorumları karıştırma (açıklama ≠ review)"
```

---

## 📝 ÇIKTI DOSYA ADLARI

**Tek Ürün:**
- `EST122-product.json`
- `F4-product.json`

**Çoklu Ürün:**
- `CPD15TVL-product.json`
- `CPD18TVL-product.json`
- `CPD20TVL-product.json`

Tüm JSON'lar: `/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/json-extracts/`
