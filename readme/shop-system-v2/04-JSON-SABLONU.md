# 📄 STANDART JSON ŞABLONU

Bu şablon AI'ya verilecek. AI, PDF'den bu yapıya uygun JSON üretecek.

---

## 🎯 KULLANIM

1. Bu dosyayı kopyala
2. AI'ya gönder (Claude, GPT-4, vb.)
3. PDF path'ini ekle
4. JSON çıktısını `readme/shop-system-v2/json-extracts/` klasörüne kaydet

---

## 📋 ŞABLON

```json
{
  "product_info": {
    "sku": "F4-201",
    "model_number": "F4 201",
    "series_name": "F Serisi",
    "product_type": "physical",
    "condition": "new"
  },

  "basic_data": {
    "title": "F4 201 - 2 Ton 48V Li-Ion Transpalet",
    "slug": "f4-201-2-ton-48v-li-ion-transpalet",
    "short_description": "F4 201 transpalet; 48V Li-Ion güç paketi, 2 ton akülü taşıma kapasitesi ve 400 mm ultra kompakt şasi ile dar koridorlarda bile hız rekoru kıran İXTİF transpalet çözümüdür.",
    "body": "<section class=\"marketing-intro\">\n    <p><strong>F4 201'i depoya soktuğunuz anda müşterileriniz "Bu transpaleti nereden aldınız?" diye soracak.</strong> Gücünü 48V Li-Ion sistemden alan bu yıldız, 140 kg gibi inanılmaz hafif servis ağırlığıyla 2 tonluk yükleri çocuk oyuncağına çeviriyor.</p>\n    <p>İXTİF mühendisleri bu modeli yalnızca yük taşımak için değil, <em>deponuzun prestijini parlatmak</em> için tasarladı.</p>\n    <ul>\n        <li><strong>Bir vardiyada iki kat iş</strong> – Lojistik maliyetleriniz %50'ye kadar düşsün.</li>\n        <li><strong>Showroom etkisi</strong> – Ultra kompakt 400 mm şasi dar koridorlarda bile vitrinde yürür gibi ilerler.</li>\n    </ul>\n</section>\n<section class=\"marketing-body\">\n    <p>Standart teslimat paketinde 2 adet 24V/20Ah Li-Ion modül bulunur...</p>\n    <p>İXTİF'in ikinci el, kiralık, yedek parça ve teknik servis programları ile F4 201 yatırımınız tam koruma altında...</p>\n    <p><strong>SEO Anahtar Kelimeleri:</strong> F4 201 transpalet, 48V Li-Ion transpalet, 2 ton akülü transpalet, İXTİF transpalet, dar koridor transpalet.</p>\n    <p><strong>Şimdi İXTİF'i arayın:</strong> 0216 755 3 555 veya <strong>info@ixtif.com</strong></p>\n</section>"
  },

  "category_brand": {
    "category_name": "Transpalet",
    "brand_id": 1,
    "brand_name": "İXTİF",
    "manufacturer": "İXTİF İç ve Dış Ticaret A.Ş."
  },

  "pricing": {
    "price_on_request": true,
    "base_price": null,
    "currency": "TRY",
    "deposit_required": true,
    "deposit_percentage": 30,
    "installment_available": true,
    "max_installments": 12
  },

  "inventory": {
    "stock_tracking": true,
    "current_stock": 0,
    "low_stock_threshold": 1,
    "lead_time_days": 45
  },

  "physical_properties": {
    "weight": 140,
    "service_weight": 140,
    "dimensions": {
      "length": 1550,
      "width": 590,
      "height": 105,
      "unit": "mm"
    }
  },

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
        "voltage": 48,
        "capacity": 20,
        "unit": "V/Ah",
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
      "drive_wheel": "210 × 70 mm Poliüretan"
    },
    "options": {
      "stabilizing_wheels": {"standard": false, "optional": true},
      "fork_lengths_mm": [900, 1000, 1150, 1220, 1350, 1500]
    }
  },

  "features": {
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

  "highlighted_features": [
    {
      "icon": "bolt",
      "priority": 1,
      "title": "48V Güç Paketi",
      "description": "0.9 kW BLDC sürüş motoru ve elektromanyetik fren ile 2 tonluk yükte bile yüksek tork."
    },
    {
      "icon": "battery-full",
      "priority": 2,
      "title": "Tak-Çıkar Li-Ion",
      "description": "2x 24V/20Ah modül standart, 4 modüle kadar genişletilebilir hızlı şarj sistemi."
    },
    {
      "icon": "arrows-alt",
      "priority": 3,
      "title": "Ultra Kompakt Şasi",
      "description": "400 mm gövde uzunluğu ve 1360 mm dönüş yarıçapı ile dar koridor çözümü."
    }
  ],

  "primary_specs": [
    {"label": "Denge Tekeri", "value": "Yok"},
    {"label": "Li-Ion Akü", "value": "24V/20Ah çıkarılabilir paket"},
    {"label": "Şarj Cihazı", "value": "24V/5A harici hızlı şarj"},
    {"label": "Standart Çatal", "value": "1150 x 560 mm"}
  ],

  "use_cases": [
    "E-ticaret depolarında hızlı sipariş hazırlama ve sevkiyat operasyonları",
    "Dar koridorlu perakende depolarında gece vardiyası yükleme boşaltma",
    "Soğuk zincir lojistiğinde düşük sıcaklıklarda kesintisiz malzeme taşıma",
    "İçecek ve FMCG dağıtım merkezlerinde yoğun palet trafiği yönetimi",
    "Dış saha rampalarda stabilizasyon tekerleği ile güvenli taşıma",
    "Kiralama filolarında yüksek kârlılık sağlayan Li-Ion platform çözümleri"
  ],

  "competitive_advantages": [
    "48V Li-Ion güç platformu ile segmentindeki en agresif hızlanma ve rampa performansı",
    "140 kg'lık ultra hafif servis ağırlığı sayesinde lojistik maliyetlerinde dramatik düşüş",
    "Tak-çıkar batarya konsepti ile 7/24 operasyonda sıfır bekleme, sıfır bakım maliyeti",
    "Stabilizasyon tekerleği opsiyonu sayesinde bozuk zeminlerde bile devrilme riskini sıfırlar",
    "İXTİF stoktan hızlı teslimat ve yerinde devreye alma ile son kullanıcıyı bekletmez"
  ],

  "target_industries": [
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
    "Tarım ve tohum depolama tesisleri",
    "Yerel belediye depoları",
    "Enerji ve altyapı malzeme depoları",
    "Perakende hızlı tüketim zincirleri",
    "Liman içi malzeme taşıma operasyonları",
    "Havaalanı kargo terminalleri",
    "Küçük ve orta ölçekli üretim atölyeleri",
    "Lüks perakende backstore yönetimi",
    "Ev & yapı market stok sahaları",
    "Kargo ve kurye transfer merkezleri"
  ],

  "faq_data": [
    {
      "question": "F4 201 bir vardiyada kaç saate kadar çalışabilir?",
      "answer": "Standart pakette gelen 2 adet 24V/20Ah Li-Ion modül ile tek şarjda 6 saate kadar kesintisiz çalışır...",
      "sort_order": 1,
      "category": "usage",
      "is_highlighted": true
    },
    {
      "question": "Dar koridorlarda manevra kabiliyeti nasıldır?",
      "answer": "400 mm gövde uzunluğu ve 1360 mm dönüş yarıçapı sayesinde...",
      "sort_order": 2,
      "category": "technical"
    },
    {
      "question": "Garantisi ve servis desteği nasıl işliyor?",
      "answer": "F4 201 için 24 ay tam kapsamlı garanti sunuyoruz; İXTİF Türkiye genelinde mobil servis araçları ile 7/24 destek sağlar...",
      "sort_order": 5,
      "category": "warranty"
    },
    {
      "question": "İkinci el, kiralık veya finansman seçenekleri mevcut mu?",
      "answer": "Evet, İXTİF olarak sıfır satışın yanı sıra ikinci el, kiralık ve operasyonel leasing çözümleri sunuyoruz... Detaylı teklif için 0216 755 3 555 numarasını arayabilir veya info@ixtif.com adresine yazabilirsiniz.",
      "sort_order": 6,
      "category": "pricing"
    }
  ],

  "tags": [
    "transpalet",
    "li-ion",
    "48v",
    "2-ton",
    "kompakt",
    "ixtif",
    "f4-201-transpalet",
    "48v-li-ion-transpalet",
    "2-ton-akulu-transpalet",
    "dar-koridor-transpalet",
    "ikinci-el-transpalet",
    "kiralik-transpalet",
    "yedek-parca",
    "teknik-servis"
  ],

  "warranty_info": {
    "duration_months": 24,
    "coverage": "Şasi, elektrik, hidrolik ve Li-Ion batarya dahil tam garanti.",
    "support": "İXTİF Türkiye geneli mobil servis ağı ile 7/24 destek."
  },

  "media_gallery": [
    {
      "type": "image",
      "url": "products/f4-201/main.jpg",
      "is_primary": true,
      "sort_order": 1
    }
  ],

  "variants": [
    {
      "sku": "F4-201-STD",
      "title": "Standart Paket (1150mm çatal, 2x batarya)",
      "option_values": {"fork_length": "1150mm", "battery": "2x"},
      "price_modifier": 0,
      "stock_quantity": 5,
      "is_default": true
    },
    {
      "sku": "F4-201-LONG",
      "title": "Uzun Çatal (1500mm)",
      "option_values": {"fork_length": "1500mm", "battery": "2x"},
      "price_modifier": 10000,
      "stock_quantity": 2
    }
  ],

  "metadata": {
    "pdf_source": "/Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F4 201/02_F4-201-brochure-CE.pdf",
    "extraction_date": "2025-01-10",
    "product_family": "F Serisi Transpaletler"
  }
}
```

---

## 📝 DOSYA KAYDETME

```bash
# Dosya adı: sku-slug.json
f4-201-transpalet.json
es12-istif-makinesi.json
jx1-order-picker.json
```

**Konum:**
```
readme/shop-system-v2/json-extracts/
```

---

**SON İKİ DOSYAYI DA HAZIRLIYORUM (SEEDER + LANDING PAGE)...**
