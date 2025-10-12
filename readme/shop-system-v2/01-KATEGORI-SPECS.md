# 📦 KATEGORİ SPECS - HER KATEGORİNİN SABİT 4 ÖZELLİĞİ

## 🎯 AMAÇ

Her kategori için **4 ana özellik kartı** standartlaştırılır. Bu kartlar:
- ✅ Landing page'de vitrin olarak gösterilir
- ✅ Kategori bazlı tutarlılık sağlar
- ✅ Kullanıcının en çok aradığı bilgileri gösterir

---

## 📋 KATEGORİ BAZLI PRIMARY SPECS

### 1️⃣ **TRANSPALET** (2-Transpalet/)

**4 Ana Özellik:**
```json
"primary_specs": [
  {"label": "Yük Kapasitesi", "value": "[X] Ton"},
  {"label": "Akü Sistemi", "value": "Li-Ion [X]V"},
  {"label": "Çatal Uzunluğu", "value": "[X] mm"},
  {"label": "Denge Tekeri", "value": "Var/Yok"}
]
```

**Örnek (F4 201):**
```json
"primary_specs": [
  {"label": "Yük Kapasitesi", "value": "2 Ton"},
  {"label": "Akü Sistemi", "value": "Li-Ion 48V"},
  {"label": "Çatal Uzunluğu", "value": "1150 mm"},
  {"label": "Denge Tekeri", "value": "Yok"}
]
```

---

### 2️⃣ **FORKLIFT** (1-Forklift/)

**4 Ana Özellik:**
```json
"primary_specs": [
  {"label": "Yük Kapasitesi", "value": "[X] Ton"},
  {"label": "Mast Yüksekliği", "value": "[X] mm"},
  {"label": "Yakıt Tipi", "value": "Elektrik/Dizel/LPG"},
  {"label": "Kabin Tipi", "value": "Kapalı/Açık"}
]
```

**Örnek:**
```json
"primary_specs": [
  {"label": "Yük Kapasitesi", "value": "3.5 Ton"},
  {"label": "Mast Yüksekliği", "value": "6000 mm"},
  {"label": "Yakıt Tipi", "value": "Elektrik"},
  {"label": "Kabin Tipi", "value": "Kapalı"}
]
```

---

### 3️⃣ **İSTİF MAKİNESİ** (3-İstif Makineleri/)

**4 Ana Özellik:**
```json
"primary_specs": [
  {"label": "Yük Kapasitesi", "value": "[X] Ton"},
  {"label": "Kaldırma Yüksekliği", "value": "[X] mm"},
  {"label": "Kullanım Tipi", "value": "Yürüyüşlü/Sürücülü"},
  {"label": "Akü Kapasitesi", "value": "[X]V/[X]Ah"}
]
```

**Örnek:**
```json
"primary_specs": [
  {"label": "Yük Kapasitesi", "value": "1.6 Ton"},
  {"label": "Kaldırma Yüksekliği", "value": "5500 mm"},
  {"label": "Kullanım Tipi", "value": "Sürücülü"},
  {"label": "Akü Kapasitesi", "value": "48V/240Ah"}
]
```

---

### 4️⃣ **ORDER PICKER** (4-Order Picker - Dikey Sipariş/)

**4 Ana Özellik:**
```json
"primary_specs": [
  {"label": "Çalışma Yüksekliği", "value": "[X] mm"},
  {"label": "Yük Kapasitesi", "value": "[X] kg"},
  {"label": "Platform Tipi", "value": "Sabit/Hareketli"},
  {"label": "Akü Voltajı", "value": "[X]V"}
]
```

**Örnek:**
```json
"primary_specs": [
  {"label": "Çalışma Yüksekliği", "value": "8000 mm"},
  {"label": "Yük Kapasitesi", "value": "1200 kg"},
  {"label": "Platform Tipi", "value": "Hareketli"},
  {"label": "Akü Voltajı", "value": "80V"}
]
```

---

### 5️⃣ **OTONOM** (5-Otonom/)

**4 Ana Özellik:**
```json
"primary_specs": [
  {"label": "Otomasyon Seviyesi", "value": "Tam/Yarı Otonom"},
  {"label": "Yük Kapasitesi", "value": "[X] Ton"},
  {"label": "Navigasyon", "value": "Lazer/Kamera/QR"},
  {"label": "Güvenlik Sistemi", "value": "Lidar/3D Kamera"}
]
```

**Örnek:**
```json
"primary_specs": [
  {"label": "Otomasyon Seviyesi", "value": "Tam Otonom"},
  {"label": "Yük Kapasitesi", "value": "2 Ton"},
  {"label": "Navigasyon", "value": "Lazer + QR Kod"},
  {"label": "Güvenlik Sistemi", "value": "3D Kamera + Lidar"}
]
```

---

### 6️⃣ **REACH TRUCK** (6-Reach Truck/)

**4 Ana Özellik:**
```json
"primary_specs": [
  {"label": "Erişim Yüksekliği", "value": "[X] mm"},
  {"label": "Yük Kapasitesi", "value": "[X] Ton"},
  {"label": "Çatal Uzunluğu", "value": "[X] mm"},
  {"label": "Akü Kapasitesi", "value": "[X]V/[X]Ah"}
]
```

**Örnek:**
```json
"primary_specs": [
  {"label": "Erişim Yüksekliği", "value": "10000 mm"},
  {"label": "Yük Kapasitesi", "value": "2 Ton"},
  {"label": "Çatal Uzunluğu", "value": "1200 mm"},
  {"label": "Akü Kapasitesi", "value": "80V/500Ah"}
]
```

---

## 🎯 AI İÇİN TALİMATLAR

### PDF İşleme Adımları:

1. **PDF klasörünü belirle**
   ```
   Örnek: "/Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F4 201/..."
   Klasör: "2-Transpalet" → Kategori: "transpalet"
   ```

2. **Kategoriye göre 4 kartı seç**
   ```
   Transpalet → [Yük Kapasitesi, Akü Sistemi, Çatal Uzunluğu, Denge Tekeri]
   ```

3. **PDF'den değerleri çıkar ve doldur**
   ```json
   "primary_specs": [
     {"label": "Yük Kapasitesi", "value": "2 Ton"},     // PDF'den oku
     {"label": "Akü Sistemi", "value": "Li-Ion 48V"},   // PDF'den oku
     {"label": "Çatal Uzunluğu", "value": "1150 mm"},   // PDF'den oku
     {"label": "Denge Tekeri", "value": "Yok"}          // PDF'den oku (option)
   ]
   ```

4. **Her kategoride AYNI 4 kart kullanılır!**
   ```
   ✅ Transpalet → Her transpalet ürünü aynı 4 kartı kullanır (sadece değerler farklı)
   ✅ Forklift → Her forklift ürünü aynı 4 kartı kullanır (sadece değerler farklı)
   ```

---

## 📊 KATEGORİ → KLASÖR EŞLEŞTIRME

| PDF Klasörü | Kategori Slug | primary_specs Template |
|------------|---------------|------------------------|
| `1-Forklift/` | forklift | Yük Kapasitesi, Mast Yüksekliği, Yakıt Tipi, Kabin Tipi |
| `2-Transpalet/` | transpalet | Yük Kapasitesi, Akü Sistemi, Çatal Uzunluğu, Denge Tekeri |
| `3-İstif Makineleri/` | istif-makinesi | Yük Kapasitesi, Kaldırma Yüksekliği, Kullanım Tipi, Akü Kapasitesi |
| `4-Order Picker - Dikey Sipariş/` | order-picker | Çalışma Yüksekliği, Yük Kapasitesi, Platform Tipi, Akü Voltajı |
| `5-Otonom/` | otonom | Otomasyon Seviyesi, Yük Kapasitesi, Navigasyon, Güvenlik Sistemi |
| `6-Reach Truck/` | reach-truck | Erişim Yüksekliği, Yük Kapasitesi, Çatal Uzunluğu, Akü Kapasitesi |

---

## ✅ KONTROL LİSTESİ

AI ile JSON üretirken:

- [ ] PDF klasörünü tespit et
- [ ] Kategoriyi belirle (klasör adından)
- [ ] O kategorinin 4 kartını al (yukarıdaki tablodan)
- [ ] PDF'den değerleri oku
- [ ] primary_specs array'ine doldur
- [ ] Her kategoride AYNI 4 kart kullanıldığından emin ol

---

**🎉 Artık tutarlı ve standart primary_specs üretebilirsin!**
