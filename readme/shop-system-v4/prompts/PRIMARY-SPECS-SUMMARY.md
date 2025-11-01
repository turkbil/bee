# 📊 PRIMARY SPECS ÖZET - TÜM KATEGORİLER

**Tarih:** 2025-01-11
**Kullanıcı Kararı:** Her kategori için 5 sabit özellik

---

## 1️⃣ TRANSPALET (Pallet Truck)

```json
{
  "capacity": "Kapasite (kg)",
  "stabilizing_wheel": "Denge Tekeri (Var/Yok/Opsiyonel)",
  "battery": "Lityum Akü (V/Ah Li-Ion)",
  "charger": "Şarj Cihazı (Entegre/Harici)",
  "turning_radius": "Dönüş Yarıçapı (mm)"
}
```

**Örnek:** 1500 kg, Opsiyonel, 24V/20Ah Li-Ion, Entegre, 1360 mm

**Neden bu 5?**
- Kapasite → Ana özellik
- Denge Tekeri → Yüksek yük dengesi
- Lityum Akü → Modern teknoloji
- Şarj Cihazı → Kullanım kolaylığı
- Dönüş Yarıçapı → Dar koridor performansı

---

## 2️⃣ FORKLIFT

```json
{
  "capacity": "Kapasite (kg)",
  "mast_type": "Asansör Tipi (Duplex/Triplex/vb.)",
  "battery": "Lityum Akü (V/Ah Li-Ion)",
  "charger": "Şarj Cihazı (Entegre/Harici)",
  "lift_height": "Kaldırma Yüksekliği (mm)"
}
```

**Örnek:** 2500 kg, Triplex, 48V/40Ah Li-Ion, Entegre, 4000 mm

**Neden bu 5?**
- Kapasite → Ana özellik
- Asansör → Dikey kaldırma kapasitesi
- Lityum Akü → Enerji sistemi
- Şarj Cihazı → Operasyon süresi
- Kaldırma Yüksekliği → Kritik performans

---

## 3️⃣ İSTİF MAKİNESİ (Stacker)

```json
{
  "capacity": "Kapasite (kg)",
  "mast_type": "Asansör Tipi (Duplex/Triplex/vb.)",
  "battery": "Akü (V/Ah)",
  "charger": "Şarj Cihazı (Entegre/Harici)",
  "lift_height": "Kaldırma Yüksekliği (mm)"
}
```

**Örnek:** 1600 kg, Duplex, 24V/30Ah, Harici, 3300 mm

**Neden bu 5?**
- Kapasite → Taşıma kapasitesi
- Asansör → İstif yüksekliği
- Akü → Güç kaynağı
- Şarj Cihazı → Operasyon esnekliği
- Kaldırma Yüksekliği → Ana fonksiyon

---

## 4️⃣ REACH TRUCK

```json
{
  "capacity": "Kapasite (kg)",
  "lift_height": "Kaldırma Yüksekliği (mm)",
  "battery": "Lityum Akü (V/Ah Li-Ion)",
  "charger": "Şarj Cihazı (Entegre/Harici)",
  "aisle_width": "Raf Mesafesi / Koridor Genişliği (mm)"
}
```

**Örnek:** 2000 kg, 8000 mm, 48V/50Ah Li-Ion, Entegre, 2700 mm

**Neden bu 5?**
- Kapasite → Taşıma kapasitesi
- Kaldırma Yüksekliği → Yüksek raf erişimi
- Lityum Akü → Güç sistemi
- Şarj Cihazı → Operasyon süresi
- Raf Mesafesi → Dar koridor avantajı

---

## 5️⃣ ORDER PICKER (Sipariş Toplama)

```json
{
  "capacity": "Kapasite (kg)",
  "platform_height": "Platform Yüksekliği (mm)",
  "battery": "Lityum Akü (V/Ah Li-Ion)",
  "charger": "Şarj Cihazı (Entegre/Harici)",
  "platform_width": "Platform Genişliği (mm)"
}
```

**Örnek:** 1000 kg, 6000 mm, 24V/40Ah Li-Ion, Entegre, 800 mm

**Neden bu 5?**
- Kapasite → Yük taşıma
- Platform Yüksekliği → Sipariş toplama yüksekliği
- Lityum Akü → Enerji sistemi
- Şarj Cihazı → Çalışma süresi
- Platform Genişliği → Operatör konforu

---

## 6️⃣ OTONOM ARAÇLAR (AGV/AMR)

```json
{
  "capacity": "Kapasite (kg)",
  "navigation_system": "Navigasyon Sistemi (Laser/SLAM/Magnetic/QR)",
  "battery": "Lityum Akü (V/Ah Li-Ion)",
  "charger": "Şarj Cihazı (Otomatik/Manuel)",
  "operating_hours": "Çalışma Süresi (saat/şarj)"
}
```

**Örnek:** 1200 kg, Laser SLAM, 48V/60Ah Li-Ion, Otomatik, 8-10 saat

**Neden bu 5?**
- Kapasite → Taşıma kapasitesi
- Navigasyon Sistemi → Otonom hareket kabiliyeti
- Lityum Akü → Enerji sistemi
- Şarj Cihazı → Otomatik şarj (24/7 operasyon)
- Çalışma Süresi → Kesintisiz operasyon süresi

**🤔 KULLANICI KARARI BEKLENİYOR:**
Yukarıdaki 5 özellik uygun mu? Yoksa şunlardan biri 5. sırada olsun mu?
- ⚡ Hareket Hızı (km/h) - Hız performansı
- 📡 Sensor Sayısı - Güvenlik sistemi
- 🔄 Yük Transfer Sistemi - Otomatik yükleme/indirme

---

## 📋 GENEL KURALLAR

**Tüm kategoriler için:**
1. **Kapasite** → Her zaman ilk özellik
2. **Lityum Akü** → Modern modellerde standart
3. **Şarj Cihazı** → Operasyonel verimlilik
4. **Kategori-specific 2 özellik** → Her kategorinin kendine özgü

**Dil:** TR + EN (2 dil zorunlu!)

**Format:**
```json
{
  "primary_specs": {
    "field_name": "Değer birim ile"
  }
}
```

---

## 🎯 KULLANIM

Her kategori için PROMPT.md dosyasında bu 5 alan tanımlı!

AI'ya PDF verdiğinde bu 5 alanı **mutlaka** çıkaracak ve vurgulayacak.
