# 🏢 SHOP MARKA YAPISI

## 📊 MARKA LİSTESİ

### **1. EP EQUIPMENT**

```json
{
  "id": 1,
  "parent_brand_id": null,
  "name": {"tr": "EP Equipment", "en": "EP Equipment"},
  "slug": "ep-equipment",
  "description": {
    "tr": "Lider malzeme taşıma ekipmanları üreticisi. Forklift, transpalet ve istif makineleri konusunda uzman.",
    "en": "Leading material handling equipment manufacturer. Expert in forklifts, pallet trucks and stackers."
  },
  "short_description": {
    "tr": "Profesyonel Malzeme Taşıma Çözümleri",
    "en": "Professional Material Handling Solutions"
  },
  "logo_url": "brands/ep-equipment-logo.png",
  "banner_url": "brands/ep-equipment-banner.jpg",
  "website_url": "https://www.ep-equipment.com",
  "country_code": "CN",
  "founded_year": 1997,
  "sort_order": 1,
  "is_active": true,
  "is_featured": true,
  "seo_data": {
    "tr": {
      "title": "EP Equipment - Elektrikli Forklift ve Malzeme Taşıma",
      "description": "EP Equipment forklift, transpalet ve istif makinesi modelleri. Elektrikli ve Li-Ion teknoloji. Profesyonel çözümler.",
      "keywords": ["EP Equipment", "forklift", "transpalet", "istif makinesi", "elektrikli forklift"]
    },
    "en": {
      "title": "EP Equipment - Electric Forklift and Material Handling",
      "description": "EP Equipment forklift, pallet truck and stacker models. Electric and Li-Ion technology. Professional solutions.",
      "keywords": ["EP Equipment", "forklift", "pallet truck", "stacker", "electric forklift"]
    }
  },
  "contact_info": {
    "email": "info@ep-equipment.com",
    "phone": "+86-571-87176888",
    "address": {
      "tr": "Hangzhou, Zhejiang, Çin",
      "en": "Hangzhou, Zhejiang, China"
    }
  },
  "social_media": {
    "facebook": "https://facebook.com/ep-equipment",
    "linkedin": "https://linkedin.com/company/ep-equipment",
    "youtube": "https://youtube.com/@ep-equipment"
  },
  "certifications": [
    {
      "name": "CE",
      "year": 2005,
      "description": {"tr": "CE Sertifikası", "en": "CE Certification"}
    },
    {
      "name": "ISO 9001",
      "year": 2000,
      "description": {"tr": "Kalite Yönetim Sistemi", "en": "Quality Management System"}
    },
    {
      "name": "ISO 14001",
      "year": 2010,
      "description": {"tr": "Çevre Yönetim Sistemi", "en": "Environmental Management System"}
    }
  ],
  "metadata": {
    "product_categories": ["forklift", "pallet_truck", "stacker", "reach_truck", "order_picker"],
    "technology": ["Li-Ion", "AGM", "Lead-acid"],
    "capacity_range": "1000-5000kg",
    "global_presence": ["Europe", "Asia", "Americas", "Middle East"],
    "year_established": 1997,
    "employees": "5000+",
    "production_capacity": "50,000+ units/year"
  }
}
```

---

### **2. İXTİF (Bayii/Distribütör)**

```json
{
  "id": 2,
  "parent_brand_id": 1,
  "name": {"tr": "iXtif", "en": "iXtif"},
  "slug": "ixtif",
  "description": {
    "tr": "Türkiye'nin önde gelen malzeme taşıma ekipmanları distribütörü. EP Equipment yetkili bayii.",
    "en": "Turkey's leading material handling equipment distributor. Authorized EP Equipment dealer."
  },
  "short_description": {
    "tr": "EP Equipment Türkiye Distribütörü",
    "en": "EP Equipment Turkey Distributor"
  },
  "logo_url": "brands/ixtif-logo.png",
  "banner_url": "brands/ixtif-banner.jpg",
  "website_url": "https://www.ixtif.com",
  "country_code": "TR",
  "founded_year": 2015,
  "sort_order": 2,
  "is_active": true,
  "is_featured": true,
  "seo_data": {
    "tr": {
      "title": "iXtif - EP Equipment Türkiye Bayii | Forklift ve İstif Makineleri",
      "description": "iXtif, EP Equipment ürünlerinin Türkiye distribütörü. Forklift, transpalet, istif makinesi satış ve servisi.",
      "keywords": ["iXtif", "forklift Türkiye", "EP Equipment bayi", "istif makinesi", "transpalet"]
    },
    "en": {
      "title": "iXtif - EP Equipment Turkey Dealer | Forklifts and Stackers",
      "description": "iXtif, EP Equipment distributor in Turkey. Forklift, pallet truck, stacker sales and service.",
      "keywords": ["iXtif", "forklift Turkey", "EP Equipment dealer", "stacker", "pallet truck"]
    }
  },
  "contact_info": {
    "email": "info@ixtif.com",
    "phone": "+90 (212) XXX XX XX",
    "address": {
      "tr": "İstanbul, Türkiye",
      "en": "Istanbul, Turkey"
    }
  },
  "social_media": {
    "facebook": "https://facebook.com/ixtif",
    "instagram": "https://instagram.com/ixtif",
    "linkedin": "https://linkedin.com/company/ixtif"
  },
  "certifications": [
    {
      "name": "Yetkili Bayi",
      "year": 2015,
      "description": {
        "tr": "EP Equipment Yetkili Bayii",
        "en": "Authorized EP Equipment Dealer"
      }
    }
  ],
  "metadata": {
    "dealer_type": "distributor",
    "parent_brand": "EP Equipment",
    "service_areas": ["Istanbul", "Ankara", "Izmir", "Bursa"],
    "services": ["sales", "service", "spare_parts", "rental"]
  }
}
```

---

## 📋 MARKA-ÜRÜN EŞLEŞMESİ

| Ürün | Marka | Brand ID |
|------|-------|----------|
| **CPD15TVL** | EP Equipment | 1 |
| **CPD18TVL** | EP Equipment | 1 |
| **CPD20TVL** | EP Equipment | 1 |
| **F4** | EP Equipment | 1 |
| **EST122** | EP Equipment | 1 |

---

## 📝 SEEDER ÖRNEK KOD

```php
// BrandSeeder.php
public function run()
{
    // Ana Marka
    $epEquipment = Brand::create([
        'name' => ['tr' => 'EP Equipment', 'en' => 'EP Equipment'],
        'slug' => 'ep-equipment',
        'description' => [
            'tr' => 'Lider malzeme taşıma ekipmanları üreticisi',
            'en' => 'Leading material handling equipment manufacturer'
        ],
        'logo_url' => 'brands/ep-equipment-logo.png',
        'website_url' => 'https://www.ep-equipment.com',
        'country_code' => 'CN',
        'founded_year' => 1997,
        'is_active' => true,
        'is_featured' => true,
        'certifications' => [
            ['name' => 'CE', 'year' => 2005],
            ['name' => 'ISO 9001', 'year' => 2000]
        ]
    ]);

    // Bayi
    Brand::create([
        'parent_brand_id' => $epEquipment->id,
        'name' => ['tr' => 'iXtif', 'en' => 'iXtif'],
        'slug' => 'ixtif',
        'description' => [
            'tr' => 'EP Equipment Türkiye Distribütörü',
            'en' => 'EP Equipment Turkey Distributor'
        ],
        'logo_url' => 'brands/ixtif-logo.png',
        'website_url' => 'https://www.ixtif.com',
        'country_code' => 'TR',
        'founded_year' => 2015,
        'is_active' => true
    ]);
}
```
