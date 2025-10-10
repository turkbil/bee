# 📦 KATEGORİ SPECS - HER KATEGORİNİN SABİT ÖZELLİKLERİ

## 🎯 AMAÇ

Her kategori için **4 ana özellik kartı** standartlaştırılır. Bu kartlar:
- ✅ Landing page'de vitrin olarak gösterilir
- ✅ Kategori bazlı tutarlılık sağlar
- ✅ AI'nın her ürün için aynı yapıda JSON üretmesini garanti eder

---

## 📋 KATEGORİ BAZLI PRIMARY SPECS

### 1️⃣ **TRANSPALET** (category_id: dinamik)

```json
{
  "primary_specs_template": {
    "card_1": {
      "label": "Denge Tekeri",
      "field_path": "options.stabilizing_wheels",
      "icon": "fa-solid fa-circle-dot",
      "format": "boolean_to_text",
      "mapping": {
        "true": "Var",
        "false": "Yok"
      }
    },
    "card_2": {
      "label": "Li-Ion Akü",
      "field_path": "electrical.battery_system.configuration",
      "icon": "fa-solid fa-battery-full",
      "format": "text"
    },
    "card_3": {
      "label": "Şarj Cihazı",
      "field_path": "electrical.charger_options.standard",
      "icon": "fa-solid fa-plug",
      "format": "text"
    },
    "card_4": {
      "label": "Standart Çatal",
      "field_path": "dimensions.fork_dimensions",
      "icon": "fa-solid fa-ruler",
      "format": "fork_dimensions"
    }
  }
}
```

**ÖRNEK ÇIKTI (F4 201):**
```
┌─────────────────┬─────────────────┬─────────────────┬─────────────────┐
│ Denge Tekeri    │ Li-Ion Akü      │ Şarj Cihazı     │ Standart Çatal  │
│ ● Yok           │ 🔋 24V/20Ah     │ 🔌 24V/5A       │ 📏 1150x560mm   │
│                 │ çıkarılabilir   │ harici hızlı    │                 │
└─────────────────┴─────────────────┴─────────────────┴─────────────────┘
```

---

### 2️⃣ **FORKLIFT** (category_id: dinamik)

```json
{
  "primary_specs_template": {
    "card_1": {
      "label": "Asansör",
      "field_path": "dimensions.lift_height.value",
      "icon": "fa-solid fa-up-down",
      "format": "value_with_unit",
      "unit": "mm"
    },
    "card_2": {
      "label": "Li-Ion Akü",
      "field_path": "electrical.battery_system.configuration",
      "icon": "fa-solid fa-battery-full",
      "format": "text"
    },
    "card_3": {
      "label": "Şarj Cihazı",
      "field_path": "electrical.charger_options.standard",
      "icon": "fa-solid fa-plug",
      "format": "text"
    },
    "card_4": {
      "label": "Raf Aralığı",
      "field_path": "dimensions.aisle_width_1000x1200.value",
      "icon": "fa-solid fa-arrows-left-right",
      "format": "value_with_unit",
      "unit": "mm"
    }
  }
}
```

**ÖRNEK ÇIKTI:**
```
┌─────────────────┬─────────────────┬─────────────────┬─────────────────┐
│ Asansör         │ Li-Ion Akü      │ Şarj Cihazı     │ Raf Aralığı     │
│ ⬆️ 3000 mm      │ 🔋 48V/150Ah    │ 🔌 48V/10A      │ ↔️ 2800 mm      │
└─────────────────┴─────────────────┴─────────────────┴─────────────────┘
```

---

### 3️⃣ **İSTİF MAKİNESİ** (category_id: dinamik)

```json
{
  "primary_specs_template": {
    "card_1": {
      "label": "Asansör",
      "field_path": "dimensions.lift_height.value",
      "icon": "fa-solid fa-up-down",
      "format": "value_with_unit",
      "unit": "mm"
    },
    "card_2": {
      "label": "Akü",
      "field_path": "electrical.battery_system.configuration",
      "icon": "fa-solid fa-battery-full",
      "format": "text"
    },
    "card_3": {
      "label": "Şarj Cihazı",
      "field_path": "electrical.charger_options.standard",
      "icon": "fa-solid fa-plug",
      "format": "text"
    },
    "card_4": {
      "label": "Çatal",
      "field_path": "dimensions.fork_dimensions",
      "icon": "fa-solid fa-ruler",
      "format": "fork_dimensions"
    }
  }
}
```

---

### 4️⃣ **SİPARİŞ TOPLAMA MAKİNESİ** (category_id: dinamik)

```json
{
  "primary_specs_template": {
    "card_1": {
      "label": "Platform Yüksekliği",
      "field_path": "dimensions.platform_height.value",
      "icon": "fa-solid fa-up-down",
      "format": "value_with_unit",
      "unit": "mm"
    },
    "card_2": {
      "label": "Akü",
      "field_path": "electrical.battery_system.configuration",
      "icon": "fa-solid fa-battery-full",
      "format": "text"
    },
    "card_3": {
      "label": "Şarj Cihazı",
      "field_path": "electrical.charger_options.standard",
      "icon": "fa-solid fa-plug",
      "format": "text"
    },
    "card_4": {
      "label": "Güvenlik Sistemi",
      "field_path": "safety_features.system",
      "icon": "fa-solid fa-shield",
      "format": "text"
    }
  }
}
```

---

### 5️⃣ **OTONOM SİSTEMLER** (category_id: dinamik)

```json
{
  "primary_specs_template": {
    "card_1": {
      "label": "Navigasyon",
      "field_path": "autonomous_features.navigation",
      "icon": "fa-solid fa-map-location-dot",
      "format": "text"
    },
    "card_2": {
      "label": "Akü",
      "field_path": "electrical.battery_system.configuration",
      "icon": "fa-solid fa-battery-full",
      "format": "text"
    },
    "card_3": {
      "label": "Şarj Sistemi",
      "field_path": "electrical.charger_options.standard",
      "icon": "fa-solid fa-plug",
      "format": "text"
    },
    "card_4": {
      "label": "Sensör Paketi",
      "field_path": "autonomous_features.sensors",
      "icon": "fa-solid fa-sensor",
      "format": "text"
    }
  }
}
```

---

### 6️⃣ **REACH TRUCK** (category_id: dinamik)

```json
{
  "primary_specs_template": {
    "card_1": {
      "label": "Asansör",
      "field_path": "dimensions.lift_height.value",
      "icon": "fa-solid fa-up-down",
      "format": "value_with_unit",
      "unit": "mm"
    },
    "card_2": {
      "label": "Akü",
      "field_path": "electrical.battery_system.configuration",
      "icon": "fa-solid fa-battery-full",
      "format": "text"
    },
    "card_3": {
      "label": "Şarj Cihazı",
      "field_path": "electrical.charger_options.standard",
      "icon": "fa-solid fa-plug",
      "format": "text"
    },
    "card_4": {
      "label": "Dar Koridor",
      "field_path": "dimensions.aisle_width_1000x1200.value",
      "icon": "fa-solid fa-arrows-left-right",
      "format": "value_with_unit",
      "unit": "mm"
    }
  }
}
```

---

## 🔧 FORMAT TİPLERİ

### 1. **text**
Direkt metin gösterir.
```php
"2x 24V/20Ah harici şarj ünitesi"
```

### 2. **value_with_unit**
Değer + birim birleştirir.
```php
Input: {"value": 3000, "unit": "mm"}
Output: "3000 mm"
```

### 3. **boolean_to_text**
Boolean → Türkçe metin.
```php
true → "Var"
false → "Yok"
```

### 4. **fork_dimensions**
Çatal boyutlarını birleştirir.
```php
Input: {"thickness": 50, "width": 150, "length": 1150, "unit": "mm"}
Output: "1150 x 150 mm"
```

---

## 📊 DATABASE YAPISINA EKLEME

### **shop_categories Tablosuna Yeni Kolon**

```php
// Migration
Schema::table('shop_categories', function (Blueprint $table) {
    $table->json('primary_specs_template')->nullable()->comment('Kategori bazlı sabit 4 kart yapısı');
});
```

### **Seeder Örneği**

```php
DB::table('shop_categories')->where('slug->tr', 'transpalet')->update([
    'primary_specs_template' => json_encode([
        'card_1' => [
            'label' => 'Denge Tekeri',
            'field_path' => 'options.stabilizing_wheels',
            'icon' => 'fa-solid fa-circle-dot',
            'format' => 'boolean_to_text',
            'mapping' => ['true' => 'Var', 'false' => 'Yok']
        ],
        'card_2' => [
            'label' => 'Li-Ion Akü',
            'field_path' => 'electrical.battery_system.configuration',
            'icon' => 'fa-solid fa-battery-full',
            'format' => 'text'
        ],
        'card_3' => [
            'label' => 'Şarj Cihazı',
            'field_path' => 'electrical.charger_options.standard',
            'icon' => 'fa-solid fa-plug',
            'format' => 'text'
        ],
        'card_4' => [
            'label' => 'Standart Çatal',
            'field_path' => 'dimensions.fork_dimensions',
            'icon' => 'fa-solid fa-ruler',
            'format' => 'fork_dimensions'
        ]
    ], JSON_UNESCAPED_UNICODE)
]);
```

---

## 🎯 AI'YA TALIMAT

AI'ya JSON üretirken:

1. **Kategoriyi belirle** (Transpalet, Forklift, vs.)
2. **primary_specs_template'i al** (yukarıdaki listeden)
3. **technical_specs'ten değerleri çıkar**
4. **primary_specs array'ine doldur**

**Örnek:**
```json
{
  "primary_specs": [
    {"label": "Denge Tekeri", "value": "Yok"},
    {"label": "Li-Ion Akü", "value": "24V/20Ah çıkarılabilir paket"},
    {"label": "Şarj Cihazı", "value": "24V/5A harici hızlı şarj"},
    {"label": "Standart Çatal", "value": "1150 x 560 mm"}
  ]
}
```

---

**ŞİMDİ FAQ SİSTEMİNİ DETAYLANDIRIYORUM...**
