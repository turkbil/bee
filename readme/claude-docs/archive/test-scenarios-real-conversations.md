# 🎭 GERÇEK KONUŞMA SENARYOLARI - TEST DOKÜMANI

**Created:** 2025-10-16
**Purpose:** Sistem testleri için gerçek kullanıcı konuşma senaryoları

---

## 📋 TEST SENARYOLARI

### ✅ SENARYO 1: KİBAR KULLANICI - DETAYLI TALEP

**Kullanıcı Profili:**
- İsim: Ahmet Bey
- Şirket: ABC Lojistik
- Ton: Kibar, profesyonel
- İhtiyaç: 2 ton elektrikli transpalet

**Konuşma:**

```
👤 Kullanıcı: Merhaba, iyi günler. Lütfen 2 ton kapasiteli elektrikli transpalet önerir misiniz?

🤖 Bot (Beklenen):
Merhaba! Size 2 ton kapasiteli elektrikli transpaletlerimiz arasından öneri yapabilirim.

İşte popüler modellerimiz:

**Litef EPT20** [LINK:shop:litef-ept20]
- Kapasite: 2000 kg
- Elektrikli motorlu sistem
- Dar koridor kullanımına uygun

**Litef EPT20 Pro** [LINK:shop:litef-ept20-pro]
- Kapasite: 2000 kg
- Lithium batarya (hızlı şarj)

Size hangi özellikler daha önemli? (Batarya tipi, kullanım sıklığı vb.)

---

👤 Kullanıcı: Günlük 6-8 saat kullanacağız, hızlı şarj önemli.

🤖 Bot (Beklenen):
O zaman **Litef EPT20 Pro** tam sizin ihtiyacınıza uygun! 😊

**Litef EPT20 Pro** [LINK:shop:litef-ept20-pro]

**Avantajları:**
- Lithium batarya: 2 saat şarj, 8 saat çalışma
- Ara şarj mümkün (mola saatlerinde)
- Uzun ömür (5000+ şarj döngüsü)

Detaylı bilgi ve sipariş için:
📞 +90 XXX XXX XX XX
📧 satis@firma.com

Başka soru varsa sorabilirsiniz! 😊
```

**✅ Beklenen Sonuç:**
- Smart search: "2 ton elektrikli transpalet" → Layer 1 (Exact Match)
- Sentiment: `polite` → Kibar ton kullanıldı
- Prompt length: ~2500 token (eski: ~10000 token)
- Response time: 2-3 saniye (eski: 5-8 saniye)

---

### ❌ SENARYO 2: KABA KULLANICI - SABRSIZ

**Kullanıcı Profili:**
- İsim: Mehmet
- Şirket: Küçük atölye
- Ton: Kaba, sabırsız
- İhtiyaç: F4 201 forklift

**Konuşma:**

```
👤 Kullanıcı: Yav f4201 var mı yok mu hızlı söyle

🤖 Bot (Beklenen):
F4 201 Elektrikli Forklift bulunuyor.

**F4 201** [LINK:shop:f4-201]
- Kapasite: 2000 kg
- Kaldırma yüksekliği: 3000 mm
- Fiyat: Talep üzerine

Detaylı bilgi için linke tıklayabilir veya iletişime geçebilirsiniz.
📞 +90 XXX XXX XX XX

---

👤 Kullanıcı: Fiyatı ne lan acil lazım

🤖 Bot (Beklenen):
Bu ürünün fiyatı talep üzerine belirlenmektedir.

Hemen fiyat teklifi almak için:
📞 Telefon: +90 XXX XXX XX XX
📧 Email: satis@firma.com

Satış ekibimiz size özel fiyat teklifi hazırlayacaktır.
```

**✅ Beklenen Sonuç:**
- Smart search: "f4201" → Layer 2 (Fuzzy Search) → "F4 201" bulundu
- Sentiment: `rude` → Kısa, net yanıt (emoji yok)
- Bot sakin ve profesyonel kaldı (kabalaşmadı!)
- Response time: 1-2 saniye (çok kısa prompt)

---

### ⚡ SENARYO 3: ACİL KULLANICI - HEMEN İSTEYEN

**Kullanıcı Profili:**
- İsim: Fatma Hanım
- Şirket: Gıda deposu
- Ton: Acil, panik
- İhtiyaç: İstif makinesi, bugün teslim

**Konuşma:**

```
👤 Kullanıcı: ACIL! Bugün teslim istif makinesi lazım stokta var mı???

🤖 Bot (Beklenen):
Hemen yardımcı oluyorum!

İstif makinesi modellerimiz:

**Litef IS15** [LINK:shop:litef-is15]
- Kapasite: 1500 kg
- Kaldırma: 3000 mm

**Litef IS20** [LINK:shop:litef-is20]
- Kapasite: 2000 kg
- Kaldırma: 3500 mm

**Acil stok durumu ve bugün teslimat için:**
📞 HEMEN ARAYIN: +90 XXX XXX XX XX
📧 Email: satis@firma.com
💬 WhatsApp: +90 XXX XXX XX XX

Satış ekibimiz stok durumunu kontrol edip hemen size geri dönecektir!

---

👤 Kullanıcı: 1.5 ton yeterli hemen fiyat lazım

🤖 Bot (Beklenen):
**Litef IS15 - 1.5 Ton İstif Makinesi** [LINK:shop:litef-is15]

**Özellikler:**
- Kapasite: 1500 kg
- Kaldırma yüksekliği: 3000 mm
- Fiyat: 38.000 TL (KDV Dahil)

**HEMEN SİPARİŞ İÇİN:**
📞 +90 XXX XXX XX XX (Şimdi arayın!)
📧 satis@firma.com

Stok durumunu onaylayıp bugün teslimat ayarlayabilirsiniz!
```

**✅ Beklenen Sonuç:**
- Smart search: "istif makinesi" → Layer 1 (Exact Match)
- Sentiment: `urgent` → "Hemen", "ACIL" kelimeleri kullanıldı
- İletişim bilgileri önce verildi
- Response time: 2 saniye

---

### 🤔 SENARYO 4: KARARSIZ KULLANICI - KAFASI KARIŞIK

**Kullanıcı Profili:**
- İsim: Ali
- Şirket: Yeni açılan depo
- Ton: Kararsız, bilgisiz
- İhtiyaç: Transpalet mi forklift mi bilmiyor

**Konuşma:**

```
👤 Kullanıcı: Merhaba, bilmiyorum işte transpalet mi forklift mi alsam emin değilim

🤖 Bot (Beklenen):
Merhaba! Size doğru ürünü seçmenizde yardımcı olmak isterim 😊

Önce şunu belirleyelim:

**Transpalet vs Forklift farkı:**

**Transpalet:**
- Zemin seviyesinde palet taşıma
- Yükü sadece 10-20 cm kaldırır
- Daha ekonomik

**Forklift:**
- Yükü yukarı kaldırıp raflara yerleştirir
- 3-6 metre kaldırma kapasitesi
- Daha çok yönlü kullanım

**Sizin için sorum:**
- Sadece zemin seviyesinde palet taşıyacak mısınız?
- Yoksa yükü raflara istifleyecek misiniz?

Bu bilgi ile size en uygun ürünü önerebilirim!

---

👤 Kullanıcı: Sadece zemin seviyesi, rafımız yok

🤖 Bot (Beklenen):
O zaman **Transpalet** tam sizin için! 😊

Rafınız olmadığına göre forklift'e gerek yok. Transpalet hem daha ekonomik hem de sizin ihtiyacınıza tam uygun.

**Şimdi kapasite belirleyelim:**
- Genelde ne kadar ağırlıkta paletler taşıyacaksınız?
- Manuel mi (elle çekme) yoksa elektrikli mi (motorlu) tercih edersiniz?

Bu bilgilerle size tam ihtiyacınıza uygun transpalet'i önerebilirim!

---

👤 Kullanıcı: Genelde 1 ton ağırlıkta, elektrikli olsun

🤖 Bot (Beklenen):
Mükemmel! 1 ton (1000 kg) için size uygun elektrikli transpaletler:

**Litef EPT12** [LINK:shop:litef-ept12]
- Kapasite: 1200 kg (1 ton üstü güvenlik payı)
- Elektrikli motorlu
- Fiyat: 32.000 TL

**Litef EPT15** [LINK:shop:litef-ept15]
- Kapasite: 1500 kg (daha rahat kullanım)
- Elektrikli motorlu
- Fiyat: 35.000 TL

Ben EPT15'i öneriyorum çünkü 1.5 ton kapasitesi size güvenlik payı sağlar. 😊

Detaylı bilgi için linklere tıklayabilirsiniz!
```

**✅ Beklenen Sonuç:**
- Smart search: "transpalet" → Layer 1
- Sentiment: `confused` → Yönlendirici ve eğitici ton
- Adım adım karar vermesine yardım edildi
- Response time: 2-3 saniye

---

### 🎯 SENARYO 5: UZMAN KULLANICI - TEKNİK DETAY İSTEYEN

**Kullanıcı Profili:**
- İsim: Mühendis Canan
- Şirket: Büyük lojistik firması
- Ton: Profesyonel, teknik
- İhtiyaç: Özel spec'li reach truck

**Konuşma:**

```
👤 Kullanıcı: 3 fazlı 48V sistem, min 7m kaldırma, dar koridor reach truck var mı?

🤖 Bot (Beklenen):
Evet, dar koridor reach truck'larımız mevcut.

**Litef DRRT20 - Dar Koridor Reach Truck** [LINK:shop:litef-drrt20]

**Teknik Özellikler:**
- Kapasite: 2000 kg
- Kaldırma yüksekliği: 7000 mm (Triple mast)
- Motor: 48V / 3-fazlı AC motor
- Dar koridor genişliği: Min 2.3m
- Fork uzunluğu: 1150 mm (ayarlanabilir)
- Batarya: 500 Ah (8 saat çalışma)
- Hız: 10 km/h (yüklü), 12 km/h (boş)
- Kaldırma hızı: 0.4 m/s
- İniş hızı: 0.5 m/s

**Katalog ve teknik döküman:**
[LINK:shop:litef-drrt20] → "Teknik Özellikler" sekmesi

Başka teknik soru varsa sorabilirsiniz!

---

👤 Kullanıcı: Batarya değiştirme süresi ne kadar? Ara şarj destekliyor mu?

🤖 Bot (Beklenen):
**Litef DRRT20 Batarya Detayları:**

**Değiştirme Süresi:**
- Standart sistem: 10-15 dakika (yan batarya kaseti)
- Hızlı değiştirme opsiyonu mevcut

**Ara Şarj:**
- ✅ Opportunity charging destekli
- Mola saatlerinde (15-30 dk) ara şarj mümkün
- Bataryaya zarar vermez (modern şarj kontrol sistemi)

**Şarj Süreleri:**
- Tam şarj: 8 saat (0%→100%)
- Ara şarj (80% bataryadan): 30 dakika → +20% kapasite

Detaylı teknik döküman ve datasheet için:
📧 teknik@firma.com (PDF gönderebiliriz)
📞 +90 XXX XXX XX XX
```

**✅ Beklenen Sonuç:**
- Smart search: "reach truck" → Layer 1
- Sentiment: `neutral` (teknik kullanıcı)
- Teknik detaylar verildi
- Response time: 2-3 saniye

---

### 💬 SENARYO 6: SOHBET EDEN - BİLGİ TOPLAYAN

**Kullanıcı Profili:**
- İsim: Öğrenci Zeynep
- Şirket: Okul projesi
- Ton: Meraklı, öğrenmek isteyen
- İhtiyaç: Genel bilgi

**Konuşma:**

```
👤 Kullanıcı: Merhaba, transpalet nedir tam olarak? Yeni duydum bu kelimeyi

🤖 Bot (Beklenen):
Merhaba! Size transpalet hakkında bilgi vermekten memnuniyet duyarım 😊

**Transpalet Nedir?**
Transpalet, paletli yükleri zemin seviyesinde taşımak için kullanılan endüstriyel ekipmandır.

**Nasıl Çalışır?**
1. Forklarını (çatalları) paletin altına sokarsınız
2. Kolu yukarı kaldırarak paleti hafifçe yerden kaldırırsınız (10-20 cm)
3. Çekerek veya iterek yükü taşırsınız

**Ne İşe Yarar?**
- Depolarda palet taşıma
- Kamyon yükleme/boşaltma
- Raf arası palet transfer

**Elektrikli vs Manuel:**
- **Manuel:** İnsan gücüyle çekilir (daha ucuz, basit)
- **Elektrikli:** Motorlu hareket (ağır yükler, yorulmadan çalışma)

Eğer satın almayı düşünüyorsanız veya daha fazla bilgi isterseniz, sorabilirsiniz! 😊

---

👤 Kullanıcı: Teşekkür ederim! Elektrikli ile manuel arasındaki fiyat farkı çok mu?

🤖 Bot (Beklenen):
Evet, fiyat farkı var:

**Manuel Transpalet:**
- Fiyat aralığı: 5.000 - 10.000 TL
- Basit mekanik sistem
- İnsan gücü gerekir

**Elektrikli Transpalet:**
- Fiyat aralığı: 30.000 - 60.000 TL
- Motorlu sistem + batarya
- Otomatik hareket

**Fark neden?**
Elektrikli transpalet motor, batarya, elektronik kontrol sistemi içerir. Ama ağır yükler için çok daha verimli!

**Hangi durumda hangisi:**
- Günde 5-10 palet → Manuel yeterli
- Günde 50+ palet → Elektrikli şart!

Başka soru varsa sorabilirsiniz! 😊
```

**✅ Beklenen Sonuç:**
- Sentiment: `neutral` (bilgi toplama)
- Eğitici ton kullanıldı
- Ürün satışı yapılmadı (istemediği için)
- Response time: 2 saniye

---

## 📊 BEKLENEN TEST SONUÇLARI

| Metrik | Öncesi | Sonrası | İyileşme |
|--------|--------|---------|----------|
| **Prompt Length** | 2000+ satır | ~400 satır | %80 azalma |
| **Token Usage** | ~10000 token | ~2500 token | %75 azalma |
| **Response Time** | 5-10 saniye | 2-4 saniye | %60 hızlanma |
| **Ürün Bulma Başarısı** | %30 (F4 201 gibi) | %95+ | %65 artış |
| **Kullanıcı Tipi Desteği** | Yok | 6 tip | ✅ Yeni |
| **Fuzzy Search** | Yok | 3-layer | ✅ Yeni |
| **Sentiment Analysis** | Yok | 5 ton | ✅ Yeni |

---

## 🚀 TEST ADIMALRI

### 1. Manuel Test (Browser)

```bash
# 1. Laravel çalıştır
php artisan serve

# 2. Tarayıcıda aç
http://laravel.test/shop/litef-ept20

# 3. Sağ altta AI chatbot aç

# 4. Senaryoları test et:
- "Merhaba" (Selamlaşma)
- "f4201 var mı?" (Fuzzy search)
- "Yav hızlı söyle lan!" (Kaba kullanıcı)
- "ACIL bugün lazım!" (Acil kullanıcı)
```

### 2. API Test (Postman/cURL)

```bash
# Kibar kullanıcı testi
curl -X POST http://laravel.test/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{
    "message": "Merhaba, lütfen 2 ton elektrikli transpalet önerir misiniz?",
    "session_id": "test-session-1"
  }'

# Kaba kullanıcı testi
curl -X POST http://laravel.test/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{
    "message": "Yav f4201 var mı yok mu hızlı söyle",
    "session_id": "test-session-2"
  }'

# Acil kullanıcı testi
curl -X POST http://laravel.test/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{
    "message": "ACIL! Bugün teslim istif makinesi lazım!!!",
    "session_id": "test-session-3"
  }'
```

### 3. Log Kontrolleri

```bash
# Laravel log kontrol
tail -f storage/logs/laravel.log | grep "Smart Search"

# Beklenen çıktılar:
# ✅ Layer 1 (Exact Match) found products
# ✅ Layer 2 (Fuzzy Search) found products
# ✅ User sentiment: urgent/polite/rude
```

---

## ✅ BAŞARI KRİTERLERİ

### Zorunlu Gereksinimler:
- ✅ F4 201 gibi ürünler **ilk denemede** bulunmalı
- ✅ Kaba kullanıcıya **sakin ve profesyonel** yanıt verilmeli
- ✅ Acil kullanıcıya **hızlı ve direkt** yanıt verilmeli
- ✅ Prompt **2500 token altında** olmalı
- ✅ Response time **4 saniye altında** olmalı

### Opsiyonel İyileştirmeler:
- ⚡ Cache hit rate %80+
- 🎯 Fuzzy search accuracy %95+
- 💬 Conversation context memory 20 mesaj
- 🔍 3-layer search fallback sistemi

---

## 🐛 HATA SENARYOLARI

### Hata 1: Ürün Bulunamadı (False Negative)

```
👤 Kullanıcı: "F4 201 var mı?"
🤖 Bot: "Bu kriterlere uygun ürün bulamadım" ❌

🔍 Debug:
- Log kontrol: Smart search hangi layer'a kadar gitti?
- Database kontrol: Ürün SKU/title doğru mu?
- Cache kontrol: Cache expired mı?
```

### Hata 2: Yanlış Ürün Önerisi (False Positive)

```
👤 Kullanıcı: "2 ton transpalet"
🤖 Bot: "İşte 200 kg forkliftlerimiz..." ❌

🔍 Debug:
- Kapasite dönüşümü kontrol: 2 ton = 2000 kg?
- Smart search results kontrol: Hangi ürünler geldi?
```

### Hata 3: Kaba Ton (Bot Kabalaştı)

```
👤 Kullanıcı: "Yav hızlı ol lan!"
🤖 Bot: "Kendine gel!" ❌

🔍 Debug:
- Sentiment detection kontrol: `rude` tespit edildi mi?
- Optimized prompt kontrol: Kaba kullanıcı rehberi eklendi mi?
```

---

## 📝 TEST RAPORU ŞABLONu

```markdown
# AI Chatbot Test Raporu
**Tarih:** 2025-10-16
**Test Eden:** [İsim]

## Test Edilen Senaryolar:
- [x] Senaryo 1: Kibar kullanıcı
- [x] Senaryo 2: Kaba kullanıcı
- [x] Senaryo 3: Acil kullanıcı
- [x] Senaryo 4: Kararsız kullanıcı
- [x] Senaryo 5: Uzman kullanıcı
- [x] Senaryo 6: Sohbet eden kullanıcı

## Sonuçlar:

### Başarılı:
- ✅ F4 201 ilk denemede bulundu (Fuzzy Search Layer 2)
- ✅ Kaba kullanıcıya sakin yanıt verildi
- ✅ Prompt 2300 token (hedef: 2500)

### Başarısız:
- ❌ [Varsa hataları yaz]

### İyileştirme Önerileri:
- [Öneri 1]
- [Öneri 2]
```

---

🎉 **Test dökümanı tamamlandı!** Tüm senaryoları test edip sonuçları raporlayabilirsiniz.
