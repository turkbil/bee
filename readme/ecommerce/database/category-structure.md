# 🗂️ SHOP KATEGORİ YAPISI

## 📊 HİYERARŞİ PLANI

### **LEVEL 1 - ANA KATEGORİLER**

```json
[
  {
    "id": 1,
    "parent_id": null,
    "name": {"tr": "Forklift", "en": "Forklift"},
    "slug": "forklift",
    "description": {
      "tr": "Elektrikli ve dizel forkliftler, çeşitli tonajlarda yük taşıma çözümleri",
      "en": "Electric and diesel forklifts, load handling solutions in various capacities"
    },
    "icon_class": "fa-solid fa-truck-pickup",
    "level": 1,
    "path": "1",
    "sort_order": 1,
    "is_active": true,
    "is_featured": true
  },
  {
    "id": 2,
    "parent_id": null,
    "name": {"tr": "Transpalet", "en": "Pallet Truck"},
    "slug": "transpalet",
    "description": {
      "tr": "Elektrikli ve manuel transpaletler, palet taşıma çözümleri",
      "en": "Electric and manual pallet trucks, pallet handling solutions"
    },
    "icon_class": "fa-solid fa-dolly",
    "level": 1,
    "path": "2",
    "sort_order": 2,
    "is_active": true,
    "is_featured": true
  },
  {
    "id": 3,
    "parent_id": null,
    "name": {"tr": "İstif Makineleri", "en": "Stacker"},
    "slug": "istif-makineleri",
    "description": {
      "tr": "Yüksek kaldırma kapasiteli istif makineleri ve platformları",
      "en": "High lifting capacity stacking machines and platforms"
    },
    "icon_class": "fa-solid fa-layer-group",
    "level": 1,
    "path": "3",
    "sort_order": 3,
    "is_active": true,
    "is_featured": false
  }
]
```

---

### **LEVEL 2 - ALT KATEGORİLER**

#### **Forklift Alt Kategorileri:**

```json
[
  {
    "id": 11,
    "parent_id": 1,
    "name": {"tr": "CPD Serisi", "en": "CPD Series"},
    "slug": "cpd-serisi",
    "description": {
      "tr": "Kompakt elektrikli 3 tekerlekli forkliftler - 80V Li-Ion teknoloji",
      "en": "Compact electric 3-wheel forklifts - 80V Li-Ion technology"
    },
    "level": 2,
    "path": "1/11",
    "sort_order": 1,
    "is_active": true,
    "is_featured": true
  },
  {
    "id": 12,
    "parent_id": 1,
    "name": {"tr": "EFL Serisi", "en": "EFL Series"},
    "slug": "efl-serisi",
    "description": {
      "tr": "Elektrikli 4 tekerlekli counterbalance forkliftler",
      "en": "Electric 4-wheel counterbalance forklifts"
    },
    "level": 2,
    "path": "1/12",
    "sort_order": 2,
    "is_active": true,
    "is_featured": false
  }
]
```

---

#### **Transpalet Alt Kategorileri:**

```json
[
  {
    "id": 21,
    "parent_id": 2,
    "name": {"tr": "Elektrikli Transpalet", "en": "Electric Pallet Truck"},
    "slug": "elektrikli-transpalet",
    "description": {
      "tr": "Li-Ion bataryalı elektrikli transpalet modelleri",
      "en": "Li-Ion battery electric pallet truck models"
    },
    "level": 2,
    "path": "2/21",
    "sort_order": 1,
    "is_active": true
  },
  {
    "id": 22,
    "parent_id": 2,
    "name": {"tr": "Manuel Transpalet", "en": "Manual Pallet Truck"},
    "slug": "manuel-transpalet",
    "description": {
      "tr": "Hidrolik manuel palet taşıma araçları",
      "en": "Hydraulic manual pallet handling trucks"
    },
    "level": 2,
    "path": "2/22",
    "sort_order": 2,
    "is_active": true
  }
]
```

---

#### **İstif Makineleri Alt Kategorileri:**

```json
[
  {
    "id": 31,
    "parent_id": 3,
    "name": {"tr": "Yürüyen Operatörlü İstif", "en": "Pedestrian Stacker"},
    "slug": "yuruyen-operatorlu-istif",
    "description": {
      "tr": "Yürüyen operatör tip istif makineleri",
      "en": "Pedestrian operator type stacking machines"
    },
    "level": 2,
    "path": "3/31",
    "sort_order": 1,
    "is_active": true
  }
]
```

---

## 📋 ÜRÜN-KATEGORİ EŞLEŞMESİ

| Ürün | Kategori Path | Category ID |
|------|---------------|-------------|
| **CPD15TVL** | Forklift > CPD Serisi | 11 |
| **CPD18TVL** | Forklift > CPD Serisi | 11 |
| **CPD20TVL** | Forklift > CPD Serisi | 11 |
| **F4** | Transpalet > Elektrikli Transpalet | 21 |
| **EST122** | İstif Makineleri > Yürüyen Operatörlü İstif | 31 |

---

## 🎯 SEO DATA PATTERN

```json
{
  "seo_data": {
    "tr": {
      "title": "CPD Serisi Forklift Modelleri | EP Equipment",
      "description": "CPD15, CPD18, CPD20 elektrikli forklift modelleri. 80V Li-Ion teknoloji, kompakt tasarım, güçlü performans.",
      "keywords": ["CPD serisi", "elektrikli forklift", "Li-Ion forklift", "kompakt forklift", "EP Equipment"]
    },
    "en": {
      "title": "CPD Series Forklift Models | EP Equipment",
      "description": "CPD15, CPD18, CPD20 electric forklift models. 80V Li-Ion technology, compact design, powerful performance.",
      "keywords": ["CPD series", "electric forklift", "Li-Ion forklift", "compact forklift", "EP Equipment"]
    }
  }
}
```

---

## 📝 SEEDER ÖRNEK KOD

```php
// CategorySeeder.php
public function run()
{
    // Ana Kategoriler
    $forklift = Category::create([
        'name' => ['tr' => 'Forklift', 'en' => 'Forklift'],
        'slug' => 'forklift',
        'description' => [
            'tr' => 'Elektrikli ve dizel forkliftler',
            'en' => 'Electric and diesel forklifts'
        ],
        'level' => 1,
        'path' => '1',
        'sort_order' => 1,
        'is_active' => true,
        'is_featured' => true
    ]);

    // Alt Kategori
    $cpdSeries = Category::create([
        'parent_id' => $forklift->id,
        'name' => ['tr' => 'CPD Serisi', 'en' => 'CPD Series'],
        'slug' => 'cpd-serisi',
        'description' => [
            'tr' => 'Kompakt elektrikli 3 tekerlekli forkliftler',
            'en' => 'Compact electric 3-wheel forklifts'
        ],
        'level' => 2,
        'path' => "1/{$forklift->id}",
        'sort_order' => 1,
        'is_active' => true,
        'is_featured' => true
    ]);
}
```
