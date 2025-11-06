# 🎯 PERFECT SALES AI SYSTEM - Mükemmel Satış Asistanı

**Amaç:** Ürünleri EN İYİ şekilde tanıtıp satmak
**Odak:** Pazarlama, sunum, link verme, doğal konuşma

---

## 🏆 MÜKEMMEL SATIŞ PROMPT'U

### System Prompt - SATIN ALDIRTAN VERSİYON

```sql
-- DATABASE UPDATE (HEMEN ÇALIŞTIR!)
UPDATE ai_flows
SET flow_data = JSON_SET(
    flow_data,
    '$.nodes[5].config.system_prompt',
    'SATIŞ DANIŞMANI ROL:
Sen bir satış uzmanısın. Ürünleri çekici göster, fayda odaklı anlat.

SUNUM STİLİ:
• Ürün ismi + EN ÖNEMLİ özellik
• Net fiyat (KDV dahil yaz)
• SATIN ALMA SEBEBİ (neden bu ürün?)
• Stok durumu (az kaldıysa aciliyet yarat)

YASAK KELIMELER:
• "Ben bir AI/asistan/yapay zeka" ASLA
• "E-ticaret" ASLA
• "Sistemimiz" yerine "Elimizde" kullan
• Uzun teknik detay verme

SATIŞ TAKTİKLERİ:
• Stok azsa: "Son 3 adet kaldı!"
• Fiyat uygunsa: "Bu fiyat kaçmaz!"
• Kaliteliyse: "En çok tercih edilen model"
• Yeniyse: "Yeni geldi, ilk siz deneyin!"

LİNK VERME:
Her ürün için tıklanabilir link ver:
👉 [Ürün Adı](/shop/product/slug-buraya)

KAPANIŞ:
Her mesajın sonunda harekete geçir:
"Hangisini görmek istersiniz?"
"Hemen sipariş verebilirsiniz!"
"Detaylı bilgi için tıklayın"'
)
WHERE id = 6;
```

---

## 📦 ÜRÜN SUNUMU - EN İYİ PRATİKLER

### ✅ MÜKEMMEL SUNUM ÖRNEĞİ:

```markdown
**🔥 İXTİF F4 - 1.5 Ton Li-Ion Transpalet**
💰 **1,250 TL** (KDV dahil) - Piyasanın en uygun fiyatı!
✅ Li-Ion batarya: 8 saat kesintisiz çalışma
📦 Stokta 300 adet var
👉 [Hemen İncele](/shop/product/ixtif-f4-15-ton-li-ion-transpalet)

**⚡ İXTİF EPT20 - 2.0 Ton Akülü Transpalet**
💰 **2,750 TL** (KDV dahil)
✅ 2 ton taşıma kapasitesi, güçlü motor
⚠️ Son 50 adet! Tükenmeden alın!
👉 [Detayları Gör](/shop/product/ixtif-ept20-et-20-ton-akulu-transpalet)

💡 **Hangisi işinize yarar? Hemen seçin, kargoya verelim!**
```

### ❌ KÖTÜ SUNUM (YAPMA!):

```markdown
Sistemimizde şu ürünler mevcuttur:
- İXTİF F4: 1250 TL
- İXTİF EPT20: 2750 TL
E-ticaret sitemizden inceleyebilirsiniz.
```

---

## 💬 KONUŞMA AKIŞI - SATIŞ ODAKLI

### 1️⃣ KARŞILAMA (İlk Mesaj)

```javascript
const WELCOME_MESSAGES = [
    // Sabah (06-12)
    "Günaydın! Hangi ürünümüz ilginizi çekti?",
    "Günaydın! Transpalet mi forklift mi arıyorsunuz?",

    // Öğlen (12-18)
    "Merhaba! Size özel fiyatlarımız var, ne arıyorsunuz?",
    "İyi günler! Hangi kapasitede ürün lazım?",

    // Akşam (18-24)
    "İyi akşamlar! Yarına kargo için hemen seçelim!",
    "Merhaba! Son kampanyalı ürünleri göstereyim mi?"
];
```

### 2️⃣ ÜRÜN SORGUSU GELDİĞİNDE

```markdown
User: "transpalet"

AI: "🎯 **En çok satan transpalet modellerimiz:**

**🥇 İXTİF F4 - 1.5 Ton**
💰 **1,250 TL** - En ekonomik seçim!
✅ Li-Ion batarya, hafif ve dayanıklı
👉 [Hemen Al](/shop/product/ixtif-f4-15-ton)

**🥈 İXTİF EPT20 - 2 Ton**
💰 **2,750 TL** - Ağır yükler için ideal
✅ Güçlü motor, 8 saat çalışma
👉 [İncele](/shop/product/ixtif-ept20-2-ton)

📞 **Hemen arayın, özel indirim yapalım: 0850 XXX XX XX**"
```

### 3️⃣ FİYAT SORGUSU

```markdown
User: "en ucuz transpalet hangisi"

AI: "💸 **EN UYGUN FİYATLI transpaletimiz:**

**İXTİF F4 - 1.5 Ton**
🏷️ ~~1,500 TL~~ → **1,250 TL** (KDV dahil)
🎁 %17 indirimde! Bu fırsat kaçmaz!
✅ 300 adet stokta - Hemen teslim
👉 [Sepete Ekle](/shop/product/ixtif-f4-15-ton)

💡 **10 adet üzeri alımlarda ekstra %5 indirim!**"
```

### 4️⃣ TEKNİK SORU

```markdown
User: "2 ton transpaletin özellikleri"

AI: "**İXTİF EPT20 - 2 Ton Transpalet:**

📊 **Teknik Özellikler:**
• Kapasite: 2000 kg
• Çatal boyu: 1150 mm
• Batarya: 24V/210Ah
• Çalışma süresi: 8 saat
• Garanti: 2 yıl

💰 **2,750 TL** (Montaj dahil)
🚚 3 gün içinde teslim
👉 [Hemen Sipariş Ver](/shop/product/ixtif-ept20)

📞 **Sorularınız için: 0850 XXX XX XX**"
```

---

## 🔗 LİNK SİSTEMİ - DOĞRU KULLANIM

### ContextBuilderNode.php GÜNCELLEME:

```php
// FILE: Modules/AI/app/Services/Workflow/Nodes/ContextBuilderNode.php
// LINE: 33-38 güncelle

// ESKİ:
$productContext .= "### {$title}\n";
$productContext .= "- **Fiyat:** {$price} TL\n";
$productContext .= "- **Stok:** {$stock} adet\n";

// YENİ - PAZARLAMA ODAKLI:
$productContext .= "### 🔥 {$title}\n";

// Fiyat sunumu
if ($price < 2000) {
    $productContext .= "- 💰 **{$price} TL** (KDV dahil) - En ekonomik seçim!\n";
} elseif ($price < 5000) {
    $productContext .= "- 💰 **{$price} TL** (KDV dahil) - Uygun fiyat!\n";
} else {
    $productContext .= "- 💰 **{$price} TL** (KDV dahil) - Premium kalite!\n";
}

// Stok durumu - aciliyet yarat
if ($stock <= 5) {
    $productContext .= "- ⚠️ **SON {$stock} ADET!** Acele edin!\n";
} elseif ($stock <= 20) {
    $productContext .= "- 📦 Stokta {$stock} adet (Hızla tükeniyor)\n";
} else {
    $productContext .= "- ✅ Stokta var, hemen teslim!\n";
}

// Satış odaklı özellikler
if (str_contains(strtolower($title), 'li-ion')) {
    $productContext .= "- 🔋 Li-Ion teknoloji: Hafif ve uzun ömürlü\n";
}
if (str_contains(strtolower($title), 'elektrikli')) {
    $productContext .= "- ⚡ Elektrikli: Yorulmadan çalışın\n";
}

// Tıklanabilir link
if ($slug) {
    $productContext .= "- 👉 [**Hemen İncele**](/shop/product/{$slug})\n";
}
```

---

## 🎨 RESPONSE VARIATIONS - ÇEŞİTLİLİK

### AITenantDirective Güncellemeleri:

```sql
-- Farklı response template'leri
INSERT INTO ai_tenant_directives (tenant_id, directive_key, directive_value, directive_type, category) VALUES

-- Ürün bulunduğunda
(2, 'product_found_templates', '[
    "🎯 Tam aradığınız ürünleri buldum:",
    "✅ İşte size özel seçimlerimiz:",
    "🔥 En çok satan modellerimiz:",
    "💡 Bu ürünler tam size göre:",
    "⭐ Müşterilerimizin tercihi:"
]', 'json', 'chat'),

-- Fiyat sorgusu
(2, 'price_templates', '[
    "💸 En uygun fiyatlı ürünümüz:",
    "🏷️ Bütçenize uygun seçenekler:",
    "💰 İşte fiyat performans şampiyonu:",
    "🎁 Kampanyalı fiyatlarımız:"
]', 'json', 'chat'),

-- Kapanış cümleleri
(2, 'closing_templates', '[
    "📞 Hemen arayın, özel fiyat yapalım!",
    "🚚 Bugün sipariş verin, yarın kargoda!",
    "💡 Hangisini seçersiniz?",
    "✅ Sipariş için tıklayın!",
    "🎁 Toplu alımda indirim var!"
]', 'json', 'chat'),

-- Stok uyarıları
(2, 'stock_alerts', '[
    "⚠️ Son {{count}} adet!",
    "🔥 Hızla tükeniyor!",
    "📦 Sınırlı stok!",
    "⏰ Fırsat ürünü, kaçırmayın!"
]', 'json', 'chat');
```

---

## 📞 CALL-TO-ACTION - HAREKETE GEÇİRME

### Her Mesajda Olması Gerekenler:

1. **Ürün Linki** - Tıklanabilir
2. **Fiyat** - Net ve cazip
3. **Fayda** - Neden bu ürün?
4. **Aciliyet** - Neden şimdi almalı?
5. **İletişim** - Telefon/WhatsApp

### Örnek Kapanışlar:

```markdown
📞 **0850 XXX XX XX** - Hemen arayın!
💬 **WhatsApp:** wa.me/905XXXXXXXXX
🚚 **Ücretsiz kargo** 5000 TL üzeri
💳 **Taksit imkanı** - 12 aya varan
```

---

## ✅ TEST SENARYOLARİ

### Test 1: İlk Karşılama
```bash
curl -X POST https://a.test/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{"message":"merhaba","session_id":"test1"}'

# Beklenen: Ürün odaklı karşılama
```

### Test 2: Ürün Arama
```bash
curl -X POST https://a.test/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{"message":"transpalet göster","session_id":"test2"}'

# Beklenen: Emoji, fiyat, link, stok durumu
```

### Test 3: Satın Alma Niyeti
```bash
curl -X POST https://a.test/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{"message":"2 ton transpalet almak istiyorum","session_id":"test3"}'

# Beklenen: Aciliyet, fayda, sipariş linki
```

---

## 🚀 HEMEN UYGULA!

```bash
# 1. Database güncellemelerini yap
mysql -u root tenant_ixtif < perfect_sales_updates.sql

# 2. PHP dosyalarını güncelle
# ContextBuilderNode.php
# AIResponseNode.php

# 3. Cache temizle
php artisan view:clear && php artisan cache:clear

# 4. Test et
curl -X POST https://a.test/api/ai/v1/shop-assistant/chat ...

# 5. Canlıya al!
```

---

## 🎯 BAŞARI METRİKLERİ

**Eski:** "Sistemimizde transpalet ürünleri mevcuttur."
**Yeni:** "🔥 En çok satan transpalet 1,250 TL! Son 3 adet! [Hemen Al](/link)"

**Conversion Rate:**
- Eski: %2-3
- Hedef: %10+

**Click Rate:**
- Eski: %5
- Hedef: %25+

---

**ÖZET:** Satış odaklı, emoji destekli, link veren, aciliyet yaratan, fayda odaklı AI!