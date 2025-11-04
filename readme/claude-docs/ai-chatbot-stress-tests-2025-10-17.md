# 🔥 AI Chatbot STRES TESTLERİ - Zor Senaryolar

**Tarih:** 2025-10-17
**Test Tipi:** Agresif Stres Testleri + Link Kontrolü
**Amaç:** Bot'u gerçekten zorla, tüm edge case'leri bul!

---

## 🎯 Test Stratejisi

Bu testler **normal kullanıcılardan çok daha zor**. Amaç:
1. Bot'u köşeye sıkıştırmak
2. Link formatını her senaryoda kontrol etmek
3. Firma adının her yanıtta geçtiğini doğrulamak
4. Kapasite hesabını en karmaşık senaryolarda test etmek
5. Edge case'leri bulmak

---

## 🔥 STRES TEST SENARYOLARI

### TEST 1: Karışık Teknik Detaylar + Typo + Belirsizlik

**Girdi:**
```
200kg mı 2000kg mı tam emin değilim ama elektirkli transplet lazım AGM batarya olcak
soguk depo içn kullanılcak paslanmz olmalı 1150 mm catal uzunlugu fiyat ne kadar link at
```

**Zorlu Noktalar:**
- ❌ Typo'lar: "elektirkli", "transplet", "olcak", "içn", "kullanılcak", "paslanmz", "catal"
- ❌ Belirsizlik: "200kg mı 2000kg mı"
- ❌ Çok fazla parametre: kapasite + batarya + kullanım alanı + çatal uzunluğu
- ✅ "link at" komutu - LINK formatı zorunlu!

**Beklenen AI Yanıtı:**
```markdown
İxtif olarak, soğuk depo için paslanmaz elektrikli transpalet aramanıza yardımcı oluyorum! 😊

Kapasite konusunda netleştirelim:
- **200 kg** (0.2 ton) → Hafif yük taşıma
- **2000 kg** (2 ton) → Orta/ağır yük taşıma

Soğuk depo + Paslanmaz + AGM batarya + 1150mm çatal uzunluğu kombinasyonunda:

**Soğuk Depo Tipi Paslanmaz Transpalet 2 Ton** [LINK:shop:soguk-depo-transpalet-2ton]
  - Kapasite: 2000 kg (2 ton)
  - Batarya: AGM batarya (soğuk dayanımlı)
  - Çatal uzunluğu: 1150 mm
  - Özellik: Paslanmaz çelik gövde (gıda uyumlu)
  - Çalışma sıcaklığı: -25°C / +40°C
  - Fiyat: 78.000 TL

**Manuel Soğuk Depo Transpalet 2 Ton** [LINK:shop:soguk-depo-manuel-2ton]
  - Kapasite: 2000 kg
  - Paslanmaz çelik yapı
  - Çatal uzunluğu: 1150 mm
  - Fiyat: 42.000 TL

Hangi kapasiteyi tercih edersiniz? Size özel teklif hazırlayabiliriz! 😊
```

**Kontrol Kriterleri:**
- ✅ Firma adı: "İxtif olarak" açıkça geçmeli
- ✅ Link formatı: `[LINK:shop:slug]` her üründe olmalı
- ✅ Kapasite: 200 kg vs 2000 kg farkını açıklamalı
- ✅ Typo'ları anlayıp doğru ürünleri göstermeli
- ✅ Tüm parametreleri (batarya, soğuk depo, paslanmaz, çatal uzunluğu) dikkate almalı

**Puan:** /10
1. Kategori tespiti: ___ (transpalet + soğuk depo + paslanmaz)
2. Ürün gösterimi: ___ (en az 2 ürün)
3. Link formatı: ___ (her ürün için doğru)
4. Kapasite hesabı: ___ (200 kg ≠ 2000 kg farkı açıklandı mı?)
5. Firma bilgisi: ___ ("İxtif olarak" geçiyor mu?)
6. İletişim bilgisi: ___ (N/A - acil değil)
7. Sentiment uyumu: ___ (belirsizlik → yönlendirici)
8. KB kullanımı: ___ (soğuk depo bilgisi kullanıldı mı?)
9. Yanıt kalitesi: ___ (typo'ları anladı mı? düzgün markdown?)
10. Hata yokluğu: ___ (tutarlı yanıt?)

---

### TEST 2: Birden Fazla Ürün + Farklı Birimler + Karşılaştırma

**Girdi:**
```
ACİL! 1.5 ton transpalet + 3000kg forklift + 4.5 metre kaldırma istif makinesi
hepsinin linklerini ver karşılaştırmalı tablo yap elektrikli olsun hepsi
toplam maliyeti söyle bugün teslimat olur mu HEMEN CEVAP VER!
```

**Zorlu Noktalar:**
- ❌ ACİL sentiment → İletişim bilgisi ZORUNLU
- ❌ 3 farklı ürün kategorisi aynı anda
- ❌ Farklı birimler: ton, kg, metre
- ❌ Karşılaştırmalı tablo isteniyor (markdown table)
- ❌ Toplam maliyet hesabı
- ❌ Teslimat sorusu (stok/teslimat bilgisi yok)
- ✅ "linklerini ver" komutu - LINK formatı zorunlu!

**Beklenen AI Yanıtı:**
```markdown
İxtif olarak hemen yardımcı oluyorum! 🚀

Size 3 ekipman için detaylı bilgi sunuyorum:

## 1️⃣ Elektrikli Transpalet 1.5 Ton

**Elektrikli Transpalet 1.5 Ton AGM** [LINK:shop:transpalet-1-5-ton-elektrikli]
  - Kapasite: 1500 kg (1.5 ton)
  - Batarya: AGM
  - Fiyat: 38.000 TL

## 2️⃣ Elektrikli Forklift 3 Ton

**Elektrikli Forklift 3 Ton** [LINK:shop:forklift-3-ton-elektrikli]
  - Kapasite: 3000 kg (3 ton)
  - Kaldırma: 3000 mm (varsayılan)
  - Fiyat: 185.000 TL

## 3️⃣ Elektrikli İstif Makinesi 4.5m Kaldırma

**İstif Makinesi Elektrikli 4.5m** [LINK:shop:istif-makinesi-4-5m]
  - Kaldırma yüksekliği: 4500 mm (4.5 metre)
  - Kapasite: 1500 kg
  - Fiyat: 95.000 TL

---

## 📊 Karşılaştırmalı Tablo

| Ekipman | Kapasite | Özellik | Fiyat |
|---------|----------|---------|-------|
| Transpalet | 1.5 ton (1500 kg) | Elektrikli, AGM | 38.000 TL |
| Forklift | 3 ton (3000 kg) | Elektrikli, 3m kaldırma | 185.000 TL |
| İstif Makinesi | 1.5 ton | Elektrikli, 4.5m kaldırma | 95.000 TL |
| **TOPLAM** | - | - | **318.000 TL** |

---

⚡ **ACİL DESTEK İÇİN:**
📞 Telefon: +90 XXX XXX XX XX
💬 WhatsApp: +90 XXX XXX XX XX
📧 E-posta: satis@ixtif.com

**Teslimat:** Stok durumu ve teslimat süresi için yukarıdaki numaralardan
hemen iletişime geçin! Acil talepler için özel çözümler sunuyoruz. 🚀

Toplu alım indirimi uygulayabiliriz! Hemen arayın!
```

**Kontrol Kriterleri:**
- ✅ Firma adı: "İxtif olarak" geçmeli
- ✅ Link formatı: 3 ürün için de `[LINK:shop:slug]` olmalı
- ✅ Kapasite: 1.5 ton, 3000 kg (3 ton), doğru dönüşümler
- ✅ Birim: 4.5 metre → 4500 mm dönüşümü
- ✅ ACİL sentiment: Telefon + WhatsApp + Email ZORUNLU!
- ✅ Markdown tablo formatı
- ✅ Toplam maliyet hesabı
- ✅ Teslimat sorusuna profesyonel yanıt

**Puan:** /10
1. Kategori tespiti: ___ (3 kategori de doğru tespit edildi mi?)
2. Ürün gösterimi: ___ (3 ürün + linkler)
3. Link formatı: ___ (3 link de doğru mu?)
4. Kapasite hesabı: ___ (1.5 ton, 3000kg→3ton doğru mu?)
5. Firma bilgisi: ___ ("İxtif olarak")
6. İletişim bilgisi: ___ (ACİL → Telefon + WhatsApp + Email var mı?)
7. Sentiment uyumu: ___ (Urgent → "Hemen", "🚀", hızlı yanıt)
8. KB kullanımı: ___ (3 ürün bilgisi doğru mu?)
9. Yanıt kalitesi: ___ (Tablo formatı, toplam hesap, düzgün markdown)
10. Hata yokluğu: ___ (tutarlı, eksiksiz?)

---

### TEST 3: Yanlış Terminoloji + Mantık Hataları + Tuzak Sorular

**Girdi:**
```
500 ton transpalet var mı elektriksiz manuel bataryalı soğuk hava
forkliftten daha ucuz olmalı 10 metre kaldırma yapsın ama
transpalet olsun link ver hızlı
```

**Zorlu Noktalar:**
- ❌ **Mantık hatası:** "500 ton transpalet" (gerçekte max 3-5 ton olur)
- ❌ **Çelişki:** "elektriksiz manuel bataryalı" (mantıksız kombinasyon)
- ❌ **Çelişki:** "10 metre kaldırma transpalet" (transpaletler max 200mm kaldırır!)
- ❌ **Tuzak:** Forklift ile transpalet karşılaştırması (farklı kategoriler)
- ❌ **Gerçekçi olmayan beklenti:** 10m kaldırma transpalette olmaz
- ✅ "link ver" komutu

**Beklenen AI Yanıtı:**
```markdown
İxtif olarak, talebinizde bazı teknik tutarsızlıklar var, size yardımcı olmak isterim! 😊

**🔍 Tespit Edilen Sorunlar:**

1. **500 ton transpalet:** Transpaletler genelde 1.5 - 3 ton kapasitededir.
   500 ton endüstriyel vinç kapasitesidir, transpalet değil. ❌

2. **"Elektriksiz manuel bataryalı":** Manuel transpaletler batarya kullanmaz (insan gücü).
   Bataryalı olanlar elektriklidir. ❌

3. **"10 metre kaldırma transpalet":** Transpaletler sadece 200mm (20cm) kaldırır (yer seviyesi taşıma).
   10 metre kaldırma için **istif makinesi** veya **forklift** gerekir. ❌

---

**✅ Size Önerim:**

Muhtemelen aradığınız şu ürünlerden biri:

### Seçenek 1: Yüksek Kapasiteli Transpalet (Gerçekçi)

**Manuel Transpalet 3 Ton** [LINK:shop:manuel-transpalet-3ton]
  - Kapasite: 3000 kg (3 ton) ← En yüksek transpalet kapasitesi
  - Kaldırma: 200 mm (yer seviyesi)
  - Fiyat: 12.000 TL

**Elektrikli Transpalet 3 Ton** [LINK:shop:elektrikli-transpalet-3ton]
  - Kapasite: 3000 kg (3 ton)
  - Batarya: AGM (şarj edilebilir)
  - Kaldırma: 200 mm
  - Fiyat: 52.000 TL

### Seçenek 2: 10 Metre Kaldırma İçin (Doğru Ekipman)

**İstif Makinesi 10m Kaldırma** [LINK:shop:istif-makinesi-10m]
  - Kapasite: 1.5 ton
  - Kaldırma: 10.000 mm (10 metre) ← İhtiyacınız olan bu!
  - Fiyat: 145.000 TL

**Reach Truck 10m Kaldırma** [LINK:shop:reach-truck-10m]
  - Kapasite: 2 ton
  - Kaldırma: 10.000 mm (10 metre)
  - Dar koridor uyumlu
  - Fiyat: 285.000 TL

---

**🤔 Hangi İhtiyacınız Var?**

- **Yer seviyesinde palet taşıma (200mm)** → Transpalet
- **Yüksek raflara istif (10m)** → İstif makinesi / Reach truck / Forklift

Hangi seçenek size uygun? Daha fazla detay verirseniz tam olarak doğru ürünü bulabilirim! 😊

📞 Telefon: +90 XXX XXX XX XX
```

**Kontrol Kriterleri:**
- ✅ Firma adı: "İxtif olarak" geçmeli
- ✅ Link formatı: Her ürün için doğru
- ✅ Mantık hatalarını tespit edip düzeltmeli
- ✅ Kullanıcıyı eğitmeli (transpalet vs istif makinesi farkı)
- ✅ Gerçekçi alternatifler sunmalı
- ✅ Profesyonel ve yardımsever ton

**Puan:** /10
1. Kategori tespiti: ___ (Transpalet + alternatifleri)
2. Ürün gösterimi: ___ (Gerçekçi ürünler + alternatifler)
3. Link formatı: ___ (4+ link doğru mu?)
4. Kapasite hesabı: ___ (500 ton mantıksızlığını yakaladı mı?)
5. Firma bilgisi: ___ ("İxtif olarak")
6. İletişim bilgisi: ___ (Opsiyonel ama varsa +)
7. Sentiment uyumu: ___ (Yardımsever, eğitici)
8. KB kullanımı: ___ (Transpalet vs istif makinesi bilgisi)
9. Yanıt kalitesi: ___ (Hataları açıkladı mı? Net alternatifler?)
10. Hata yokluğu: ___ (Tutarlı, mantıklı?)

---

### TEST 4: Çok Uzun Detaylı Soru + Müşteri Profili

**Girdi:**
```
Merhaba ben Ahmet İstanbul Avrupa yakası depo işletmecisiyim 5000 metrekare depom var
şu an 3 tane eski forkliftim var ama çok arızalanıyor yedek parça bulamıyorum
2 tane de manuel transpalet var onlar da kırık yeni ekipman almayı düşünüyorum
ama bütçem sınırlı 150bin TL civarı ne önerirsiniz bir de soğuk depo bölümümüz var
-18 derecede çalışacak ekipman lazım gıda sektörü olduğu için hijyenik olmalı
paslanmaz çelik tercih ediyorum 2 ton yeterli olur ayrıca kiralama seçeneğiniz var mı
aylık ne kadar olur servisi kendiniz mi yapıyorsunuz yoksa anlaşmalı servis mi
garantisi ne kadar linklerini atın lütfen acelem var yarın karar vereceğim
```

**Zorlu Noktalar:**
- ❌ Çok uzun, karmaşık hikaye
- ❌ Çok fazla soru (fiyat, kiralama, servis, garanti, link, soğuk depo, paslanmaz)
- ❌ Müşteri profili + ihtiyaç analizi gerekiyor
- ❌ Bütçe kısıtı (150k TL)
- ❌ "Acelem var yarın karar vereceğim" → Urgent sentiment
- ✅ "linklerini atın" komutu

**Beklenen AI Yanıtı:**
```markdown
Merhaba Ahmet Bey! İxtif olarak, depo ekipman ihtiyaçlarınızda size yardımcı olmaktan mutluluk duyarız! 😊

**🎯 İhtiyaç Analizi:**
- Depo: 5000 m² (Avrupa yakası, İstanbul)
- Mevcut ekipman: 3 eski forklift + 2 kırık transpalet
- Bütçe: ~150.000 TL
- Soğuk depo: -18°C, gıda sektörü, hijyenik, paslanmaz çelik
- Kapasite: 2 ton yeterli
- Servis ve garanti soruları
- Acele durum: Yarın karar

---

## 💡 150.000 TL Bütçe ile Önerilerimiz

### Seçenek 1: Soğuk Depo Özel + Manuel Transpalet (Bütçeye Uygun)

**Soğuk Depo Paslanmaz Transpalet 2 Ton** [LINK:shop:soguk-depo-transpalet-2ton-paslanmaz]
  - Kapasite: 2000 kg (2 ton)
  - Özellik: Paslanmaz çelik, -25°C / +40°C
  - Gıda sektörü uyumlu (hijyenik tasarım)
  - Batarya: AGM (soğuk dayanımlı)
  - Fiyat: **78.000 TL**

**Manuel Transpalet 2 Ton (Yedek/Genel Kullanım)** [LINK:shop:manuel-transpalet-2ton]
  - Kapasite: 2000 kg
  - Standart depo kullanımı
  - Fiyat: **9.500 TL**

**Toplam:** 87.500 TL ← Bütçe içinde! ✅

---

### Seçenek 2: Kiralama Seçeneği (Aylık Ödeme)

**Soğuk Depo Transpalet 2 Ton - Kiralama**
- Aylık kira: **3.200 TL/ay** (12 aylık anlaşma)
- Bakım ve servis dahil
- 2 yıl garanti (kiralama süresince)

**Avantajları:**
- ✅ İlk yatırım yok
- ✅ Servis + bakım bizde
- ✅ Arıza durumunda yedek ekipman garantisi

---

## 🔧 Servis ve Garanti Bilgileri

**Servis Hizmeti:**
- ✅ Kendi servis ekibimiz (7/24 destek)
- ✅ İstanbul Avrupa yakası servis noktası mevcut
- ✅ Periyodik bakım (yıllık 2 kez ücretsiz)

**Garanti:**
- ✅ 2 yıl kapsamlı garanti (satın almada)
- ✅ Yedek parça tedariki garantili (5 yıl)
- ✅ Orijinal + yan sanayi yedek parça seçenekleri

---

## 📊 Karşılaştırma: Satın Alma vs Kiralama

| Seçenek | İlk Maliyet | 12 Aylık Toplam | Avantaj |
|---------|-------------|-----------------|---------|
| **Satın Alma** | 87.500 TL | 87.500 TL | Siz sahipsiniz |
| **Kiralama** | 0 TL | 38.400 TL | Servis dahil, düşük risk |

---

⚡ **ACİL KARAR İÇİN HEMEN ARAYIN:**
📞 Telefon: +90 XXX XXX XX XX
💬 WhatsApp: +90 XXX XXX XX XX
📧 E-posta: ahmet.danismanlik@ixtif.com

**Yarına Özel Teklif:**
Bugün saat 18:00'e kadar kararınızı verirseniz %5 indirim + ilk yıl bakım ücretsiz! 🎁

Size özel detaylı fiyat teklifi ve ödeme planı hazırlayabiliriz.
Hemen iletişime geçin! 🚀

Başka soru veya ihtiyacınız var mı?
```

**Kontrol Kriterleri:**
- ✅ Firma adı: "İxtif olarak" açıkça geçmeli
- ✅ Link formatı: Her ürün için doğru
- ✅ Bütçe analizi: 150k TL'ye uygun öneriler
- ✅ Soğuk depo + paslanmaz + gıda uyumlu parametreler
- ✅ Kiralama seçeneği bilgisi
- ✅ Servis + garanti detayları
- ✅ ACİL sentiment: İletişim bilgileri zorunlu!
- ✅ Karşılaştırmalı tablo
- ✅ Kişiselleştirilmiş yanıt (Ahmet Bey, İstanbul)

**Puan:** /10
1. Kategori tespiti: ___ (Transpalet + soğuk depo)
2. Ürün gösterimi: ___ (2+ ürün + linkler)
3. Link formatı: ___ (Doğru formatlar)
4. Kapasite hesabı: ___ (2 ton = 2000 kg)
5. Firma bilgisi: ___ ("İxtif olarak" + kişiselleştirme)
6. İletişim bilgisi: ___ (ACİL → Telefon + WhatsApp + Email + special offer!)
7. Sentiment uyumu: ___ (Acele + profesyonel + detaylı)
8. KB kullanımı: ___ (Kiralama + servis + garanti bilgisi)
9. Yanıt kalitesi: ___ (Tablo, analiz, kişiselleştirme, özel teklifler)
10. Hata yokluğu: ___ (Eksiksiz, tutarlı, profesyonel?)

---

### TEST 5: Edge Cases - Sınır Değerler + Tutarsızlıklar

**Girdi:**
```
0.1 ton transpalet var mı yoksa 50000 kg forklift mi alsam
ikisi de aynı şey değil mi zaten link at hangisi daha iyi
15 metre kaldırma transpalet olsun ama ucuz olsun 5000 TL bütçem var
```

**Zorlu Noktalar:**
- ❌ **0.1 ton (100 kg):** Çok düşük kapasite, transpalette nadirdir
- ❌ **50000 kg (50 ton):** Çok yüksek kapasite, standart forklift değil (özel ekipman)
- ❌ **Mantık hatası:** "transpalet ve forklift aynı şey" (değil!)
- ❌ **Gerçekdışı:** "15 metre kaldırma transpalet" (transpaletler kaldırmaz!)
- ❌ **Gerçekdışı bütçe:** "5000 TL" (hiçbir yeni ekipman bu fiyata olmaz)
- ✅ "link at" komutu

**Beklenen AI Yanıtı:**
```markdown
İxtif olarak, sorularınızda bazı teknik yanlış anlamalar var, açıklayayım! 😊

---

## 🔍 Teknik Düzeltmeler

### 1️⃣ "Transpalet ve Forklift Aynı Şey mi?"

**Hayır, tamamen farklı ekipmanlardır!**

**Transpalet:**
- ✅ Sadece yer seviyesinde taşır (200mm kaldırma)
- ✅ Palet taşıma için
- ✅ Kapasite: 1.5 - 3 ton
- ❌ Rafa istif yapamaz!

**Forklift:**
- ✅ Yüksek kaldırma (3m, 4.5m, 6m+)
- ✅ Rafa istif yapar
- ✅ Kapasite: 1.5 - 5 ton (standart), 10-50 ton (özel endüstriyel)
- ✅ Operatör sürücülü

---

### 2️⃣ Kapasite Sorunu

**0.1 ton (100 kg):**
Transpaletler minimum 1.5 ton (1500 kg) kapasitededir.
100 kg için **el arabası** daha uygun olur.

**50000 kg (50 ton):**
Bu kapasite standart forklift değil, **konteyner forklift** veya **özel endüstriyel vinç** kapasitesidir.
Standart forkliftler 2-5 ton arasındadır.

---

### 3️⃣ "15 Metre Kaldırma Transpalet"

❌ **Transpaletler kaldırmaz!** (Sadece 200mm yer seviyesi)

15 metre kaldırma için:
- ✅ **Reach Truck** (dar koridor + yüksek kaldırma)
- ✅ **Yüksek Kaldırmalı Forklift**
- ✅ **İstif Makinesi**

---

### 4️⃣ Bütçe Realitesi: 5000 TL

**Üzgünüz, 5000 TL ile yeni ekipman almak mümkün değil.** ❌

**En ucuz seçenekler:**
- **Manuel Transpalet (en ucuz):** 8.500 TL
- **2. El Transpalet:** 12.000 - 15.000 TL
- **Kiralama:** Aylık 800 TL'den başlayan seçenekler

---

## 💡 Size Gerçekçi Öneriler

### İhtiyacınızı Netleştirelim:

**A) Yer Seviyesinde Palet Taşıma (200mm)**

**Manuel Transpalet 2 Ton** [LINK:shop:manuel-transpalet-2ton]
  - Kapasite: 2000 kg (2 ton)
  - Kaldırma: 200 mm
  - Fiyat: **8.500 TL** ← En ucuz seçenek

**Elektrikli Transpalet 2 Ton** [LINK:shop:elektrikli-transpalet-2ton]
  - Kapasite: 2000 kg
  - Fiyat: **42.000 TL**

---

**B) Yüksek Kaldırma (15 metre için)**

**İstif Makinesi 15m Kaldırma** [LINK:shop:istif-makinesi-15m]
  - Kaldırma: 15.000 mm (15 metre)
  - Kapasite: 1.5 ton
  - Fiyat: **225.000 TL**

**Reach Truck 12m Kaldırma** [LINK:shop:reach-truck-12m]
  - Kaldırma: 12.000 mm (12 metre)
  - Dar koridor uyumlu
  - Fiyat: **385.000 TL**

---

**C) 2. El / Kiralama Seçenekleri (Bütçe Dostu)**

- 📦 2. El Manuel Transpalet: ~12.000 TL
- 📦 Kiralama: Aylık 800 TL'den başlayan seçenekler

---

**🤔 Hangi İhtiyacınız Var Gerçekten?**

1. Sadece yer seviyesinde palet taşıma mı?
2. Yoksa raflara yüksek istif mi?

Lütfen ihtiyacınızı netleştirin, size en uygun ve bütçenize en yakın çözümü bulalım! 😊

📞 Telefon: +90 XXX XXX XX XX (2. el ve kiralama seçenekleri için)
```

**Kontrol Kriterleri:**
- ✅ Firma adı: "İxtif olarak" geçmeli
- ✅ Link formatı: Her ürün için doğru
- ✅ Tüm mantık hatalarını yakaladı mı?
- ✅ Gerçekçi bütçe bilgisi verdi mi?
- ✅ Kullanıcıyı eğitti mi? (transpalet vs forklift)
- ✅ Alternatif çözümler sundu mu? (2. el, kiralama)

**Puan:** /10
1. Kategori tespiti: ___ (Hataları tespit etti mi?)
2. Ürün gösterimi: ___ (Gerçekçi ürünler + alternatifler)
3. Link formatı: ___ (3+ link doğru mu?)
4. Kapasite hesabı: ___ (0.1 ton, 50 ton mantıksızlığı yakalandı mı?)
5. Firma bilgisi: ___ ("İxtif olarak")
6. İletişim bilgisi: ___ (Opsiyonel)
7. Sentiment uyumu: ___ (Eğitici, yardımsever, anlayışlı)
8. KB kullanımı: ___ (Transpalet vs forklift bilgisi)
9. Yanıt kalitesi: ___ (Net açıklamalar, düzeltmeler, gerçekçi öneriler)
10. Hata yokluğu: ___ (Tutarlı, mantıklı?)

---

## 📋 KRİTİK KONTROL LİSTESİ

Her test için bu 4 kritik noktayı kontrol et:

### ✅ 1. LINK FORMATI
```markdown
DOĞRU: **Ürün Adı** [LINK:shop:product-slug]
YANLIŞ: [Ürün Adı](https://ixtif.com/shop/product-slug)
YANLIŞ: Ürün Adı (link yok!)
```

### ✅ 2. FİRMA ADI
```markdown
DOĞRU: "İxtif olarak, size yardımcı olmaktan mutluluk duyarız..."
DOĞRU: "Firmamızda 2 ton kapasiteli transpaletler mevcut."
YANLIŞ: "Size yardımcı olabilirim..." (firma adı yok!)
```

### ✅ 3. KAPASİTE DÖNÜŞÜMÜ
```markdown
DOĞRU: 200 kg = 0.2 ton ✅
DOĞRU: 2000 kg = 2 ton ✅
DOĞRU: 2 ton = 2000 kg ✅
YANLIŞ: 200 kg = 2 ton ❌
```

### ✅ 4. ACİL İLETİŞİM
```markdown
Eğer "ACİL", "HEMEN", "ÇABUK" gibi kelimeler varsa:

ZORUNLU:
⚡ ACİL DESTEK İÇİN:
📞 Telefon: [NUMARA]
💬 WhatsApp: [LINK]
📧 E-posta: [EMAIL]
```

---

## 🎯 BEKLENEN SONUÇLAR

| Test | Zorluk | Link Sayısı | Firma Adı | Kapasite | İletişim |
|------|--------|-------------|-----------|----------|----------|
| TEST 1 | 🔥🔥🔥 | 2+ | Zorunlu | 200kg ≠ 2000kg | Opsiyonel |
| TEST 2 | 🔥🔥🔥🔥 | 3+ | Zorunlu | 3 farklı dönüşüm | **ZORUNLU** |
| TEST 3 | 🔥🔥🔥🔥🔥 | 4+ | Zorunlu | 500 ton mantıksız | Opsiyonel |
| TEST 4 | 🔥🔥🔥🔥 | 2+ | Zorunlu + kişiselleştirme | 2 ton = 2000kg | **ZORUNLU** |
| TEST 5 | 🔥🔥🔥🔥🔥 | 3+ | Zorunlu | 0.1 ton, 50 ton edge case | Opsiyonel |

---

## 📊 BAŞARI KRİTERLERİ

**Minimum Gereksinim:** %85 başarı (42.5/50 puan)

**İdeal Hedef:** %95+ başarı (47.5/50 puan)

---

**Test Hazırlığı Tamamlandı!**
Şimdi bu 5 zorlu senaryoyu simüle edelim ve gerçek sonuçları görelim! 🚀
