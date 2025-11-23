# 🤖 AI Chatbot Tester & Improver Skill - Ultra Kapsamlı Kılavuz

**Tarih:** 2025-10-17
**Proje:** İxtif - Laravel Multi-Tenant CMS
**Site:** laravel.test (Endüstriyel Ürün Satışı - Forklift, Transpalet, İstif Makinesi)

---

## 📚 İçindekiler

1. [Skill Özeti](#skill-özeti)
2. [Sorun Analizi](#sorun-analizi)
3. [Test Persona'ları (7 Tip)](#test-personaları)
4. [Test Senaryoları (50+ Test)](#test-senaryoları)
5. [Değerlendirme Kriterleri](#değerlendirme-kriterleri)
6. [Skill Yapısı](#skill-yapısı)
7. [Kullanım Kılavuzu](#kullanım-kılavuzu)
8. [İyileştirme Önerileri](#iyileştirme-önerileri)

---

## 🎯 Skill Özeti

### Ne Yapar?

**AI Chatbot Tester & Improver** skill'i, shop chatbot'u **gerçek kullanıcı simülasyonuyla** test eder ve iyileştirir.

### Ana Özellikler

| Özellik | Açıklama |
|---------|----------|
| **7 Persona Tipi** | Kaba, kibar, acil, cahil, okumuş, kararsız, yabancı |
| **50+ Test Senaryosu** | Forklift, transpalet, istif makinesi sorguları |
| **Otomatik Değerlendirme** | 10 kriter ile yanıt kalitesi ölçümü |
| **İyileştirme Önerileri** | Knowledge base + prompt + search iyileştirmeleri |
| **Detaylı Raporlama** | Test sonuçları, başarı oranı, hatalar |

### Ne Zaman Kullanılır?

```
✅ "Shop chatbot'u test et"
✅ "AI yanıtları saçmalıyor, test et"
✅ "Farklı kullanıcı tipleriile chatbot'u simule et"
✅ "Chatbot ürünleri tanımıyor mu kontrol et"
✅ "Kaba kullanıcılar karşısında chatbot nasıl davranıyor test et"
```

---

## 🔍 Sorun Analizi

### Kullanıcının Şikayetleri:

1. ❌ **Ürünleri tanımıyor**
   - "2 ton transpalet" sorulunca ilgisiz ürünler gösteriyor
   - Model numaralarını bilmiyor (F4-201, vs.)

2. ❌ **Hatalı paylaşımlar yapıyor**
   - Yanlış kategori ürünleri öneriyor
   - Link'ler çalışmıyor

3. ❌ **Sorulara doğru yanıt vermiyor**
   - "Kiralama var mı?" → Cevap vermiyor
   - "Firmayı tanıt" → Genel bilgi veriyor, firma adı yok

4. ❌ **Ürün/kategori tanıması lazım**
   - Forklift vs transpalet ayrımı yapamıyor
   - Kapasite (2 ton = 2000 kg) karıştırıyor

5. ❌ **Firmayı bilmesi lazım**
   - İxtif firması adını, hizmetlerini bilmiyor
   - Showroom, iletişim bilgilerini paylaşmıyor

### Sistem Analizi:

**✅ Güçlü Yönler:**
- ProductSearchService: 3-layer search (exact, fuzzy, phonetic)
- Kategori tespit (forklift, transpalet, istif, reach truck)
- Sentiment analysis (polite, rude, urgent, confused)
- Knowledge base: 48 adet bilgi

**❌ Zayıf Yönler:**
- ✅ Test yok → Gerçek kullanıcı simülasyonu eksik
- ✅ Persona çeşitliliği yok → Tek tip test
- ✅ Yanıt kalitesi ölçülememiyor → Metrik yok
- ✅ Hata tespiti manual → Otomatik kontrol yok
- ✅ Knowledge base güncelliği → Kontrol edilmiyor

---

## 🎭 Test Persona'ları (7 Tip)

### 1. 😤 KABA KULLANICI (Rude User)

**Özellikler:**
- Kaba dil kullanır (lan, yav, be)
- Sabırsız, agresif ton
- Kısa, kesik cümleler
- Emoji kullanmaz

**Örnek Mesajlar:**
```
"2 ton transpalet var mı lan hızlı ol"
"Forklift lazım bana yav ne duruyorsun"
"O şey var mı be palet taşıyan şey"
"Hemen fiyat söyle lan"
"Niye geç yanıt veriyon be"
```

**Beklenen Chatbot Davranışı:**
- ✅ Sakin ve profesyonel kalmalı
- ✅ Kaba dili görmezden gelmeli
- ✅ Kısa, net yanıt vermeli
- ✅ Emoji kullanmamalı
- ✅ Direkt ürün + fiyat göstermeli

**Değerlendirme:**
```javascript
{
  "profesyonel_ton": true/false,  // Kaba kullanıcıya sakin kaldı mı?
  "kısa_yanıt": true/false,       // Uzun açıklama yapmadı mı?
  "emoji_yok": true/false,        // Emoji kullanmadı mı?
  "direkt_bilgi": true/false      // Direkt ürün gösterdi mi?
}
```

---

### 2. 😊 KİBAR KULLANICI (Polite User)

**Özellikler:**
- Nazik dil (lütfen, rica ederim, teşekkürler)
- Detaylı soru sorar
- Sabırlı
- Emoji kullanabilir

**Örnek Mesajlar:**
```
"Merhaba, lütfen 2 ton kapasiteli transpalet önerir misiniz?"
"Rica ederim, elektrikli forklift modelleri hakkında bilgi alabilir miyim?"
"Teşekkürler, istif makinesi fiyatlarını öğrenebilir miyim?"
"Zahmet olmazsa reach truck için teklif alabilir miyim?"
"Çok teşekkür ederim, yardımcı oldunuz 😊"
```

**Beklenen Chatbot Davranışı:**
- ✅ Kibar ton kullanmalı
- ✅ Detaylı bilgi vermeli
- ✅ Emoji kullanabilir (😊)
- ✅ "Tabii ki!" ile başlamalı
- ✅ Yardımcı olduğunu belirtmeli

**Değerlendirme:**
```javascript
{
  "kibar_ton": true/false,        // "Tabii ki" ile başladı mı?
  "detaylı_bilgi": true/false,    // Yeterli detay verdi mi?
  "emoji_kullandı": true/false,   // 😊 emoji ekledi mi?
  "yardımcı_mesaj": true/false    // "Yardımcı oldum" dedi mi?
}
```

---

### 3. ⏰ ACİL KULLANICI (Urgent User)

**Özellikler:**
- Acele ifadeleri (hemen, şimdi, acil, çabuk)
- Çok fazla ünlem (!!! vs)
- Stres belirten ton
- Hızlı yanıt bekler

**Örnek Mesajlar:**
```
"ACİL forklift lazım!!! Hemen fiyat verin!"
"Şimdi stokta transpalet var mı? Hemen almam lazım!"
"Çabuk istif makinesi teklifi yapın! Yarın teslim olmalı!"
"Acil 3 ton forklift! Bugün karar vereceğim!"
"İvediyim! Hemen iletişime geçin!"
```

**Beklenen Chatbot Davranışı:**
- ✅ "Hemen yardımcı oluyorum" demeli
- ✅ Direkt ürün + fiyat vermeli
- ✅ İletişim bilgilerini eklemeli (telefon, WhatsApp)
- ✅ Stok durumu belirtmeli
- ✅ Teslimat süresi söylemeli

**Değerlendirme:**
```javascript
{
  "hızlı_yanıt": true/false,      // "Hemen yardımcı" dedi mi?
  "direkt_ürün": true/false,      // Direkt ürün gösterdi mi?
  "iletişim_bilgisi": true/false, // Telefon/WhatsApp verdi mi?
  "stok_bilgisi": true/false      // Stok durumu belirtti mi?
}
```

---

### 4. 🤷 CAHİL KULLANICI (Confused/Uninformed User)

**Özellikler:**
- Teknik bilgi yok
- Yanlış terimler kullanır
- "Bilmiyorum" der
- Yönlendirilmesi gerekir

**Örnek Mesajlar:**
```
"O palet taşıyan şey var mı? Adını bilmiyorum"
"Yük kaldıran makine istiyorum, hangisi olduğunu bilmiyorum"
"200 kilo taşıyan bir şey lazım, ne almalıyım?"
"Forklift mi transpalet mi almalıyım bilmiyorum"
"Elektrik mi dizel mi iyi bilmiyorum"
```

**Beklenen Chatbot Davranışı:**
- ✅ Sabırlı ve yönlendirici olmalı
- ✅ Doğru terimi öğretmeli ("Bu transpalet denir")
- ✅ Karşılaştırma yapmalı (forklift vs transpalet)
- ✅ Kullanım alanı sorgulamalı ("Ne için kullanacaksınız?")
- ✅ Alternatifler sunmalı

**Değerlendirme:**
```javascript
{
  "sabırlı_ton": true/false,      // Sabırlı mı?
  "terim_öğretti": true/false,    // Doğru terimi söyledi mi?
  "karşılaştırma": true/false,    // Ürünleri karşılaştırdı mı?
  "soru_sordu": true/false        // Kullanım alanı sordu mu?
}
```

---

### 5. 🎓 OKUMUŞ KULLANICI (Technical/Expert User)

**Özellikler:**
- Teknik terimler kullanır
- Spesifik model numaraları sorar
- Detaylı özellikler ister
- Karşılaştırma yapar

**Örnek Mesajlar:**
```
"F4-201 modeli stokta mı?"
"2 ton AGM bataryalı elektrikli transpalet arıyorum"
"4.5 metre kaldırma yüksekliği, triplex mast, Li-Ion batarya olan forklift?"
"Reach truck koridorgenişliği 2.5m olan modeliniz var mı?"
"Soğuk depo için ETC serisi transpalet lazım, paslanmaz çelik gövdeli"
```

**Beklenen Chatbot Davranışı:**
- ✅ Teknik terimleri anlamalı
- ✅ Model numaralarını bulmalı
- ✅ Spesifik özellikleri göstermeli
- ✅ Karşılaştırma sunmalı
- ✅ Teknik doküman/katalog önerMeli

**Değerlendirme:**
```javascript
{
  "terim_anladı": true/false,     // Teknik terimleri anladı mı?
  "model_buldu": true/false,      // Model numarasını buldu mu?
  "özellik_gösterdi": true/false, // Teknik özellikleri gösterdi mi?
  "katalog_önerdi": true/false    // Katalog/doküman önerdi mi?
}
```

---

### 6. 🤔 KARARSIZ KULLANICI (Indecisive User)

**Özellikler:**
- "Galiba", "sanırım", "emin değilim" der
- Alternatifler arasında gidip gelir
- Çok soru sorar
- Karar vermekte zorlanır

**Örnek Mesajlar:**
```
"2 ton mu 3 ton mu alsam emin değilim"
"Elektrikli mi dizel mi bilmiyorum, hangisi daha iyi acaba?"
"Transpalet almayı düşünüyorum ama istif makinesi de olabilir sanırım"
"Kiralama mı alsam satın mı alsam kararsızım"
"Galiba AGM batarya iyi ama Li-Ion da var, hangisi?"
```

**Beklenen Chatbot Davranışı:**
- ✅ Yönlendirici sorular sorMalı ("Ne için kullanacaksınız?")
- ✅ Karşılaştırma tablosu sunmalı
- ✅ Kullanım senaryoları açıklamalı
- ✅ Avantaj/dezavantaj listesi vermeli
- ✅ Tavsiye sunmalı

**Değerlendirme:**
```javascript
{
  "yönlendirici_soru": true/false, // Kullanım alanı sordu mu?
  "karşılaştırma": true/false,     // Karşılaştırma yaptı mı?
  "senaryo_anlattı": true/false,   // Kullanım senaryosu verdi mi?
  "tavsiye_verdi": true/false      // Tavsiye sundu mu?
}
```

---

### 7. 🌍 YABANCI KULLANICI (Foreign User)

**Özellikler:**
- İngilizce konuşur (bazen Türkçe karışır)
- Basit cümleler kullanır
- Teknik terimler İngilizce
- Yavaş iletişim

**Örnek Mesajlar:**
```
"Hello, do you have 2 ton forklift?"
"Transpalet price please"
"I need forklift for warehouse, electric or diesel?"
"Kiralama var mı? (rental available?)"
"Soğuk depo için ürün var mı? (cold storage products?)"
```

**Beklenen Chatbot Davranışı:**
- ✅ İngilizce yanıt vermeli (veya Türkçe-İngilizce karışık)
- ✅ Basit cümleler kullanmalı
- ✅ Teknik terimleri İngilizce çevirmeli
- ✅ Görsellerle desteklemeli
- ✅ İletişim kanallarını belirtmeli

**Değerlendirme:**
```javascript
{
  "ingilizce_yanıt": true/false,  // İngilizce yanıt verdi mi?
  "basit_cümle": true/false,      // Basit cümleler kullandı mı?
  "çeviri_yaptı": true/false,     // Teknik terimleri çevirdi mi?
  "görsel_önerdi": true/false     // Görsel/katalog önerdi mi?
}
```

---

## 🧪 Test Senaryoları (50+ Test)

### Kategori 1: GENEL SELAMLAŞMA

| # | Persona | Mesaj | Beklenen Yanıt |
|---|---------|-------|----------------|
| 1 | Kibar | "Merhaba" | "Merhaba! Size nasıl yardımcı olabilirim? 😊" |
| 2 | Kaba | "Alo" | "Merhaba! Size nasıl yardımcı olabilirim?" |
| 3 | Acil | "ACİL yardım lazım!!!" | "Hemen yardımcı oluyorum! Nasıl yardımcı olabilirim?" |
| 4 | Yabancı | "Hello" | "Hello! How can I help you? 😊" |

**Kontrol Kriterleri:**
- ✅ Selamlaşma yapıyor mu?
- ✅ Kaba kullanıcıya sakin kalıyor mu?
- ✅ İngilizce mesaja İngilizce yanıt veriyor mu?
- ❌ Ürün kategorisi söylememeli! (sadece genel yardım teklifi)

---

### Kategori 2: TRANSPALET SORGULARI

| # | Persona | Mesaj | Beklenen Yanıt |
|---|---------|-------|----------------|
| 5 | Kibar | "Lütfen transpalet önerisi yapabilir misiniz?" | Kategori tespit + ürün listesi + linkler |
| 6 | Kaba | "Transpalet var mı lan hızlı ol" | Kısa yanıt + ürün listesi (emoji yok) |
| 7 | Acil | "ACİL 2 ton transpalet!!!" | "Hemen yardımcı oluyorum" + ürün + fiyat + iletişim |
| 8 | Okumuş | "2 ton AGM bataryalı elektrikli transpalet" | Spesifik filtre + AGM bataryalı ürünler |
| 9 | Cahil | "O palet taşıyan şey var mı?" | "Transpalet denir" + ürün + kullanım açıklama |
| 10 | Kararsız | "Transpalet mı istif mi alsam bilmiyorum" | Karşılaştırma + kullanım senaryoları + tavsiye |

**Kontrol Kriterleri:**
- ✅ "transpalet" kategorisini tespit ediyor mu?
- ✅ Ürün listesi gösteriyor mu?
- ✅ Link formatı doğru mu? ([LINK:shop:slug])
- ✅ Kapasiteyi doğru filtreliyor mu? (2 ton = 2000 kg)

---

### Kategori 3: FORKLİFT SORGULARI

| # | Persona | Mesaj | Beklenen Yanıt |
|---|---------|-------|----------------|
| 11 | Kibar | "Forklift modelleri hakkında bilgi alabilir miyim?" | Kategori tespit + forklift türleri + ürün listesi |
| 12 | Kaba | "Forklift lazım bana yav ne duruyorsun" | Kısa yanıt + forklift listesi |
| 13 | Acil | "Hemen 3 ton forklift teklifi!!!" | "Hemen yardımcı" + 3 ton forkliftler + iletişim |
| 14 | Okumuş | "4.5 metre kaldırma, triplex mast, elektrikli forklift" | Spesifik filtre + triplex + 4.5m yükseklik |
| 15 | Cahil | "Yük kaldıran makine istiyorum" | "Forklift denir" + ürün + kullanım açıklama |
| 16 | Kararsız | "Elektrikli mi dizel mi forklift alsam?" | Karşılaştırma (elektrikli vs dizel) + tavsiye |

**Kontrol Kriterleri:**
- ✅ "forklift" kategorisini tespit ediyor mu?
- ✅ Kapasite filtresini doğru uyguluyor mu? (3 ton)
- ✅ Kaldırma yüksekliğini tespit ediyor mu? (4.5m = 4500mm)
- ✅ Mast tipini anlıyor mu? (duplex/triplex)

---

### Kategori 4: İSTİF MAKİNESİ SORGULARI

| # | Persona | Mesaj | Beklenen Yanıt |
|---|---------|-------|----------------|
| 17 | Kibar | "İstif makinesi önerisi rica ediyorum" | Kategori tespit + istif makineleri listesi |
| 18 | Kaba | "İstif makinesi var mı be" | Kısa yanıt + istif listesi |
| 19 | Acil | "ŞIMDI istif makinesi lazım!!!" | "Hemen yardımcı" + istif + stok bilgisi |
| 20 | Okumuş | "1.5 ton, 3m kaldırma yüksekliği, elektrikli istif" | Spesifik filtre + 1.5 ton + 3m yükseklik |
| 21 | Cahil | "Rafara koyma makinesi" | "İstif makinesi denir" + ürün + açıklama |

**Kontrol Kriterleri:**
- ✅ "istif" kategorisini tespit ediyor mu?
- ✅ Kaldırma yüksekliğini anlıyor mu? (3m = 3000mm)
- ✅ Kapasite filtresini doğru uyguluyor mu? (1.5 ton)

---

### Kategori 5: MODEL NUMARASI SORGULARI

| # | Persona | Mesaj | Beklenen Yanıt |
|---|---------|-------|----------------|
| 22 | Okumuş | "F4-201 modeli var mı?" | Model bulma + ürün detayı + link |
| 23 | Okumuş | "F4201 hakkında bilgi" | Model bulma (tire olmadan) + detay |
| 24 | Kaba | "F4-201 var mı lan" | Kısa yanıt + model + fiyat |
| 25 | Acil | "ACİL F4-201 stokta mı?" | "Hemen kontrol" + stok bilgisi + iletişim |

**Kontrol Kriterleri:**
- ✅ Model numarasını (F4-201) buluyor mu?
- ✅ Tire'li ve tiresiz varyasyonları tespit ediyor mu? (F4-201 = F4201)
- ✅ Ürün linkini veriyor mu?
- ✅ Stok bilgisini paylaşıyor mu?

---

### Kategori 6: ÖZEL ÖZELLİK SORGULARI

| # | Persona | Mesaj | Beklenen Yanıt |
|---|---------|-------|----------------|
| 26 | Okumuş | "AGM bataryalı transpalet" | AGM filtre + uygun ürünler |
| 27 | Okumuş | "Li-Ion batarya olan forklift" | Li-Ion filtre + uygun ürünler |
| 28 | Okumuş | "Soğuk depo için transpalet" | "Soğuk depo" filtre + ETC serisi ürünler |
| 29 | Okumuş | "Paslanmaz çelik transpalet" | "Paslanmaz" filtre + stainless steel ürünler |
| 30 | Okumuş | "Dar koridor için reach truck" | Reach truck kategori + dar koridor modeller |

**Kontrol Kriterleri:**
- ✅ AGM/Li-Ion batarya tipini tespit ediyor mu?
- ✅ "Soğuk depo" kullanım alanını anlıyor mu?
- ✅ ETC (Extreme Temperature Conditions) serisini öneriyor mu?
- ✅ "Paslanmaz" özelliğini filtreliyor mu?

---

### Kategori 7: FİRMA VE HİZMET SORGULARI

| # | Persona | Mesaj | Beklenen Yanıt |
|---|---------|-------|----------------|
| 31 | Kibar | "İxtif kimdir?" | Firma tanıtımı (Knowledge Base'den) + slogan |
| 32 | Kibar | "Hangi hizmetleri sunuyorsunuz?" | Kiralama, 2. el, servis, yedek parça (detaylı) |
| 33 | Kaba | "Kiralama yapıyor musunuz" | "Evet" + kiralama süreleri (günlük/haftalık/aylık) |
| 34 | Acil | "TEKNİK SERVİS VAR MI ACİL!" | "Evet, 7/24" + iletişim bilgileri |
| 35 | Kibar | "Yedek parça bulabilir miyim?" | "Evet" + stok bilgisi + hızlı teslimat |

**Kontrol Kriterleri:**
- ✅ Firma adını (İxtif) söylüyor mu?
- ✅ Firma sloganını ("Türkiye'nin İstif Pazarı") kullanıyor mu?
- ✅ Kiralama sürelerini (günlük/haftalık/aylık/yıllık) belirtiyor mu?
- ✅ 7/24 teknik destek bilgisini veriyor mu?

---

### Kategori 8: TEKNİK BİLGİ SORGULARI

| # | Persona | Mesaj | Beklenen Yanıt |
|---|---------|-------|----------------|
| 36 | Okumuş | "AGM batarya nedir?" | AGM açıklama (Knowledge Base'den) + avantajları |
| 37 | Kararsız | "AGM mı Li-Ion mu tercih etmeliyim?" | Karşılaştırma + kullanım senaryoları + tavsiye |
| 38 | Cahil | "Duplex triplex ne demek?" | Mast türleri açıklaması + kullanım alanları |
| 39 | Okumuş | "Soğuk depo için hangi ekipman kullanmalıyım?" | ETC serisi + özellikler + öneri |
| 40 | Kibar | "Forklift kapasitesi nasıl belirlenir?" | Kapasite açıklaması (Knowledge Base'den) |

**Kontrol Kriterleri:**
- ✅ Knowledge Base'deki bilgiyi kullanıyor mu?
- ✅ Teknik terimleri doğru açıklıyor mu?
- ✅ Karşılaştırma yapıyor mu? (AGM vs Li-Ion)
- ✅ Pratik tavsiye veriyor mu?

---

### Kategori 9: FİYAT VE ÖDEME SORGULARI

| # | Persona | Mesaj | Beklenen Yanıt |
|---|---------|-------|----------------|
| 41 | Kaba | "Fiyatı nedir lan" | Fiyat bilgisi veya "Talep üzerine" + iletişim |
| 42 | Kibar | "Transpalet fiyatları nedir?" | Ürün listesi + fiyatlar veya fiyat teklifi yönlendirme |
| 43 | Acil | "Hemen fiyat verin!!!" | "Hemen yardımcı" + fiyat + iletişim |
| 44 | Kibar | "Hangi ödeme seçenekleri var?" | Nakit, EFT, kredi kartı, vade, leasing |
| 45 | Okumuş | "Leasing ile alabilir miyim?" | Leasing açıklama + avantajlar + iş ortağı firmalar |

**Kontrol Kriterleri:**
- ✅ Fiyat bilgisini paylaşıyor mu?
- ✅ "Fiyat talep üzerine" ise iletişim bilgisi veriyor mu?
- ✅ Ödeme seçeneklerini açıklıyor mu?
- ✅ Leasing avantajlarını anlatıyor mu?

---

### Kategori 10: TESLİMAT VE GARANTİ SORGULARI

| # | Persona | Mesaj | Beklenen Yanıt |
|---|---------|-------|----------------|
| 46 | Acil | "Ne zaman teslim edersiniz?" | Teslimat süreleri (stok: 1-3 gün, özel: 2-4 hafta) |
| 47 | Kibar | "Tüm Türkiye'ye teslimat var mı?" | "Evet" + bölge ofisleri + teslimat ağı |
| 48 | Kibar | "Garanti süresi ne kadardır?" | Yeni: 12-24 ay, 2. el: 6 ay + kapsam |
| 49 | Cahil | "Garanti ne demek?" | Garanti açıklama + kapsam + avantajlar |
| 50 | Acil | "ACİL bugün teslim olur mu?" | "Aynı gün teslimat" (stokta varsa) + İstanbul/çevre |

**Kontrol Kriterleri:**
- ✅ Teslimat sürelerini belirtiyor mu?
- ✅ Tüm Türkiye teslimat bilgisini veriyor mu?
- ✅ Garanti sürelerini açıklıyor mu?
- ✅ Acil teslimat imkanlarını belirtiyor mu?

---

## 📊 Değerlendirme Kriterleri (10 Kriter)

Her test senaryosu için **10 kriter** ile değerlendirme yapılır:

### 1. ✅ Kategori Tespiti (Category Detection)

**Ne Kontrol Edilir:**
- Kullanıcının sorduğu kategoriyi doğru tespit etti mi?
- Forklift / Transpalet / İstif / Reach Truck ayrımını yapıyor mu?

**Örnekler:**
```
✅ "Transpalet lazım" → Transpalet kategorisi tespit edildi
✅ "Forklift arıyorum" → Forklift kategorisi tespit edildi
❌ "Transpalet lazım" → Forklift ürünleri önerdi (YANLIŞ!)
```

**Puanlama:**
- 1 puan: Kategori doğru tespit edildi
- 0 puan: Kategori tespit edilemedi veya yanlış kategori

---

### 2. ✅ Ürün Gösterimi (Product Display)

**Ne Kontrol Edilir:**
- Ürün listesi gösterdi mi?
- Link formatı doğru mu? ([LINK:shop:slug])
- Genel açıklama yerine SPESİFİK ürünler gösterdi mi?

**Örnekler:**
```
✅ **F4-201 Elektrikli Transpalet** [LINK:shop:f4-201-transpalet]
❌ "Transpaletler palet taşıma için kullanılır..." (genel bilgi, ürün yok!)
```

**Puanlama:**
- 1 puan: Ürün listesi + link gösterildi
- 0.5 puan: Ürün gösterildi ama link yok
- 0 puan: Genel bilgi verildi, ürün gösterilmedi

---

### 3. ✅ Link Formatı (Link Format)

**Ne Kontrol Edilir:**
- Link formatı doğru mu? ([LINK:shop:slug])
- Çalışan link mi yoksa kırık mı?

**Örnekler:**
```
✅ [LINK:shop:f4-201-transpalet]
✅ [LINK:shop:forklift-elektrikli-2ton]
❌ [https://laravel.test/shop/f4-201] (HTML link YANLIŞ!)
❌ Link yok
```

**Puanlama:**
- 1 puan: Link formatı doğru
- 0 puan: Link formatı yanlış veya yok

---

### 4. ✅ Kapasite Hesabı (Capacity Calculation)

**Ne Kontrol Edilir:**
- 1 ton = 1000 kg hesabını doğru yapıyor mu?
- "2 ton" sorulunca "2000 kg" olarak aratıyor mu?
- "200 kg" sorulunca "0.2 ton" olarak değerlendiriyor mu?

**Örnekler:**
```
✅ "2 ton transpalet" → 2000 kg filtre ✅
✅ "1500 kg forklift" → 1.5 ton ürünler ✅
❌ "200 kg transpalet" → 2 ton ürünler gösterdi (YANLIŞ! 200 kg = 0.2 ton)
```

**Puanlama:**
- 1 puan: Kapasite hesabı doğru
- 0 puan: Kapasite yanlış hesaplandı

---

### 5. ✅ Firma Bilgisi (Company Information)

**Ne Kontrol Edilir:**
- Firma adını (İxtif) söylüyor mu?
- Firma sloganını kullanıyor mu? ("Türkiye'nin İstif Pazarı")
- Firma hizmetlerini biliyor mu?

**Örnekler:**
```
✅ "İxtif olarak..."
✅ "Türkiye'nin İstif Pazarı sloganıyla..."
❌ Firma adını hiç söylemedi
```

**Puanlama:**
- 1 puan: Firma adı + slogan veya hizmet bilgisi
- 0.5 puan: Sadece firma adı
- 0 puan: Firma bilgisi yok

---

### 6. ✅ İletişim Bilgisi (Contact Information)

**Ne Kontrol Edilir:**
- Telefon/WhatsApp/Email/Telegram bilgisi veriyor mu?
- Özellikle acil durumlarda iletişim bilgisi paylaşıyor mu?

**Örnekler:**
```
✅ "📞 Telefon: +90 XXX XXX XX XX"
✅ "💬 WhatsApp: [link]"
❌ İletişim bilgisi paylaşılmadı (acil durumda!)
```

**Puanlama:**
- 1 puan: İletişim bilgisi verildi
- 0 puan: İletişim bilgisi verilmedi (gerektiği halde)

---

### 7. ✅ Sentiment Uyumu (Sentiment Match)

**Ne Kontrol Edilir:**
- Kaba kullanıcıya sakin kaldı mı?
- Kibar kullanıcıya kibar yanıt verdi mi?
- Acil kullanıcıya "Hemen yardımcı oluyorum" dedi mi?

**Örnekler:**
```
✅ Kaba → Sakin, profesyonel, emoji yok
✅ Kibar → "Tabii ki! 😊", detaylı bilgi
✅ Acil → "Hemen yardımcı oluyorum!"
❌ Kaba → Emoji kullandı ve uzun açıklama yaptı (YANLIŞ!)
```

**Puanlama:**
- 1 puan: Sentiment'e uygun yanıt
- 0 puan: Sentiment'e uygun olmayan yanıt

---

### 8. ✅ Knowledge Base Kullanımı (KB Usage)

**Ne Kontrol Edilir:**
- Firma/hizmet/teknik bilgileri Knowledge Base'den alıyor mu?
- "AGM batarya nedir?" sorusuna KB'deki yanıtı veriyor mu?

**Örnekler:**
```
✅ "İxtif, 'Türkiye'nin İstif Pazarı' sloganıyla..." (KB'den)
✅ "Kiralama: Günlük, haftalık, aylık, yıllık" (KB'den)
❌ "Firma hakkında bilgi yok" (KB'de var ama kullanmadı!)
```

**Puanlama:**
- 1 puan: KB bilgisi kullanıldı
- 0 puan: KB bilgisi kullanılmadı (gerektiği halde)

---

### 9. ✅ Yanıt Kalitesi (Response Quality)

**Ne Kontrol Edilir:**
- Yanıt açık ve anlaşılır mı?
- Markdown formatı doğru mu?
- Liste formatı doğru mu? (her madde ayrı satırda)
- HTML tag'leri yok mu? (<p>, <li> yasak!)

**Örnekler:**
```
✅ Markdown kullandı, liste doğru formatta
✅ Paragraflar arasında boş satır var
❌ HTML tag kullandı (<p>...</p>)
❌ Liste yan yana (- Ürün1 - Ürün2 - Ürün3) YANLIŞ!
```

**Puanlama:**
- 1 puan: Yanıt kaliteli, format doğru
- 0.5 puan: Yanıt iyi ama format hataları var
- 0 puan: Yanıt kötü, anlaşılmaz

---

### 10. ✅ Hata Yokluğu (Error-Free)

**Ne Kontrol Edilir:**
- Yanlış bilgi verdi mi?
- Kırık link paylaştı mı?
- Mantık hatası yaptı mı?

**Örnekler:**
```
✅ Tüm bilgiler doğru
❌ "200 kg = 2 ton" dedi (YANLIŞ!)
❌ Kırık link paylaştı
❌ "Ürün bulunamadı" dedi (ama KB'de ürün var!)
```

**Puanlama:**
- 1 puan: Hata yok
- 0 puan: Hata var

---

## 🛠️ Skill Yapısı

### Klasör Yapısı

```
ai-chatbot-tester-improver/
├── SKILL.md                         (Ana skill dosyası)
├── scripts/
│   ├── run_test.py                  (Test çalıştırıcı)
│   ├── evaluate_response.py         (Yanıt değerlendirici)
│   ├── generate_report.py           (Rapor oluşturucu)
│   └── suggest_improvements.py      (İyileştirme önerici)
├── references/
│   ├── personas.md                  (7 persona detayları)
│   ├── test_scenarios.md            (50+ test senaryosu)
│   ├── evaluation_criteria.md       (10 kriter detayları)
│   └── current_system_analysis.md   (Mevcut sistem analizi)
└── assets/
    ├── test_results_template.html   (Rapor template)
    └── improvement_checklist.md     (İyileştirme checklist)
```

---

## 📖 Kullanım Kılavuzu

### Yöntem 1: skill-creator ile Oluşturma (ÖNERİLEN)

```
"skill-creator ile AI chatbot tester skill'i oluştur.
Bu skill, shop chatbot'u farklı persona'larla test etsin,
yanıt kalitesini ölçsün ve iyileştirme önerileri sunsun."
```

### Yöntem 2: Manual Test Çalıştırma

```
"AI chatbot'u test et:
- 7 persona tipiyle (kaba, kibar, acil, cahil, okumuş, kararsız, yabancı)
- 50+ test senaryosuyla
- laravel.test sitesinde gerçek simülasyon yap"
```

### Yöntem 3: Spesifik Persona Testi

```
"Shop chatbot'u SADECE kaba kullanıcılar ile test et.
10 farklı kaba mesaj gönder ve yanıtları değerlendir."
```

### Yöntem 4: Kategori Bazlı Test

```
"Transpalet kategorisi için chatbot test et.
Tüm persona tipleriyle transpalet sorguları yap."
```

---

## 🔧 İyileştirme Önerileri

### 1. Knowledge Base İyileştirmeleri

**Sorun:** Firma bilgisi eksik veya güncel değil

**Çözüm:**
```sql
-- AIKnowledgeBase'i kontrol et
SELECT * FROM ai_knowledge_base WHERE question LIKE '%İxtif%';

-- Eksik bilgi ekle
INSERT INTO ai_knowledge_base (category, question, answer, is_active)
VALUES ('Firma Hakkında', 'İxtif showroom nerede?', 'İstanbul (Tuzla), Ankara, İzmir, Bursa', true);
```

---

### 2. Prompt İyileştirmeleri

**Sorun:** Ürün göstermiyor, genel bilgi veriyor

**Çözüm:**
```php
// OptimizedPromptService.php
// "MUTLAKA ÜRÜN GÖSTER" kuralını güçlendir

$prompts[] = "⚠️ KRİTİK: ASLA genel açıklama yapma!";
$prompts[] = "✅ MUTLAKA ürün listesi göster!";
$prompts[] = "✅ Her ürün için: **Başlık** [LINK:shop:slug]";
```

---

### 3. Search Algorithm İyileştirmeleri

**Sorun:** Kategori tespit ediyor ama ürün bulmuyor

**Çözüm:**
```php
// ProductSearchService.php
// Kategori filtresi çok katı, gevşet

// ÖNCE kategori bazlı ara
$results = $this->searchByCategory($categoryId, $keywords);

// Boşsa, tüm ürünlerde ara (kategori olmadan)
if (empty($results)) {
    $results = $this->exactMatch($keywords);
}
```

---

### 4. Kapasite Hesaplama İyileştirmesi

**Sorun:** "200 kg" → "2 ton" olarak algılıyor

**Çözüm:**
```php
// ProductSearchService.php - extractKeywords metodu

// CAPACITY EXTRACTION (düzeltme)
preg_match_all('/(\d+\.?\d*)\s*(ton|kg|kilo|kilogram)/i', $originalMessage, $capacityMatches);
if (!empty($capacityMatches[1])) {
    foreach ($capacityMatches[1] as $idx => $number) {
        $unit = $capacityMatches[2][$idx] ?? '';

        // ✅ Eğer "kg" ise ve 1000'den küçükse → KG olarak kalmalı!
        if (stripos($unit, 'kg') !== false && floatval($number) < 1000) {
            // 200 kg → "200kg" olarak ekle (ton'a çevirme!)
            $keywords[] = floatval($number) . 'kg';
        } elseif (stripos($unit, 'ton') !== false) {
            // 2 ton → "2000kg" olarak ekle
            $keywords[] = (floatval($number) * 1000) . 'kg';
        }
    }
}
```

---

### 5. Firma Bilgisi Zorunluluğu

**Sorun:** Chatbot firma adını söylemiyor

**Çözüm:**
```php
// OptimizedPromptService.php

$prompts[] = "## 🏢 FİRMA BİLGİSİ (ZORUNLU!)";
$prompts[] = "";
$prompts[] = "**Firma Adı:** İxtif";
$prompts[] = "**Slogan:** Türkiye'nin İstif Pazarı";
$prompts[] = "**⚠️ KRİTİK:** Her yanıtta 'İxtif olarak...' diye başla!";
```

---

### 6. İletişim Bilgisi Zorunluluğu

**Sorun:** Acil durumlarda iletişim bilgisi vermiyor

**Çözüm:**
```php
// OptimizedPromptService.php - buildSentimentGuidance

case 'urgent':
    $prompts[] = "**Kullanıcı acele ediyor → Hızlı yanıt ver**";
    $prompts[] = "- 'Hemen yardımcı oluyorum' de";
    $prompts[] = "- Direkt ürün + fiyat bilgisi ver";
    $prompts[] = "- **ZORUNLU:** İletişim bilgilerini ekle (telefon, WhatsApp)"; // EKLENDI
    $prompts[] = "- Stok durumu belirt";
    $prompts[] = "- Teslimat süresi söyle";
    break;
```

---

## 📈 Başarı Metrikleri

### Test Sonucu Örneği:

```
🧪 TEST RAPORU
═══════════════════════════════════════════

📊 GENEL İSTATİSTİKLER:
─────────────────────
✅ Başarılı: 42/50 (84%)
❌ Başarısız: 8/50 (16%)
⏱️ Ortalama yanıt süresi: 2.3 saniye

📋 PERSONA BAZLI BAŞARI:
─────────────────────
😊 Kibar Kullanıcı: 10/10 (100%) ✅
😤 Kaba Kullanıcı: 7/10 (70%) ⚠️
⏰ Acil Kullanıcı: 6/10 (60%) ⚠️
🤷 Cahil Kullanıcı: 9/10 (90%) ✅
🎓 Okumuş Kullanıcı: 8/10 (80%) ✅
🤔 Kararsız Kullanıcı: 9/10 (90%) ✅
🌍 Yabancı Kullanıcı: 3/10 (30%) ❌

🎯 KRİTER BAZLI BAŞARI:
─────────────────────
1. Kategori Tespiti: 90% ✅
2. Ürün Gösterimi: 70% ⚠️
3. Link Formatı: 95% ✅
4. Kapasite Hesabı: 60% ❌
5. Firma Bilgisi: 50% ❌
6. İletişim Bilgisi: 40% ❌
7. Sentiment Uyumu: 85% ✅
8. KB Kullanımı: 75% ✅
9. Yanıt Kalitesi: 80% ✅
10. Hata Yokluğu: 65% ⚠️

🚨 KRİTİK SORUNLAR:
─────────────────────
❌ Kapasite hesabı hatalı (200 kg = 2 ton diye algılıyor)
❌ Firma bilgisi eksik (İxtif adını söylemiyor)
❌ Yabancı kullanıcılara İngilizce yanıt vermiyor
❌ Acil durumlarda iletişim bilgisi paylaşmıyor

✅ İYİLEŞTİRME ÖNERİLERİ:
─────────────────────
1. ProductSearchService.php → extractKeywords metodunu düzelt (kg/ton ayrımı)
2. OptimizedPromptService.php → Firma bilgisini zorunlu yap
3. OptimizedPromptService.php → İngilizce yanıt desteği ekle
4. OptimizedPromptService.php → Acil durumda iletişim bilgisi zorunlu
```

---

## 🎯 Sonuç ve Tavsiyeler

### Öncelikli İyileştirmeler (1. Sprint):

1. **Kapasite Hesabı Düzeltme** (KRİTİK!)
   - `ProductSearchService.php` → `extractKeywords` metodu
   - 200 kg = 0.2 ton (2 ton DEĞİL!)

2. **Firma Bilgisi Zorunluluğu**
   - `OptimizedPromptService.php` → "İxtif olarak..." zorunlu

3. **İletişim Bilgisi Acil Durumlarda**
   - `OptimizedPromptService.php` → Urgent sentiment'te zorunlu

### Orta Öncelik (2. Sprint):

4. **İngilizce Destek**
   - Yabancı persona için İngilizce yanıt

5. **Ürün Gösterimi İyileştirme**
   - Genel açıklama yerine ürün listesi

6. **Knowledge Base Güncelleme**
   - Eksik bilgileri ekle (showroom, vs.)

### Düşük Öncelik (3. Sprint):

7. **Link Formatı İyileştirme**
   - Kırık linkleri tespit et

8. **Yanıt Kalitesi İyileştirme**
   - Markdown formatı kontrol

9. **Hata Tespit Sistemi**
   - Otomatik hata tespiti

---

## 📞 Skill Kullanımı - Hızlı Başlangıç

### Test 1: Genel Test (Tüm Persona + Tüm Senaryo)

```
"AI chatbot tester skill'ini kullan.
Shop chatbot'u 7 persona ile 50+ senaryo ile test et.
Detaylı rapor oluştur."
```

### Test 2: Kaba Kullanıcı Testi

```
"Shop chatbot'u SADECE kaba kullanıcı persona'sıyla test et.
10 kaba mesaj gönder, yanıtları değerlendir."
```

### Test 3: Transpalet Kategorisi Testi

```
"Shop chatbot'u transpalet kategorisi için test et.
Tüm persona tipleriile transpalet sorguları yap."
```

### Test 4: İyileştirme Önerileri

```
"Shop chatbot test sonuçlarına göre iyileştirme önerileri sun.
ProductSearchService ve OptimizedPromptService için kod örnekleri ver."
```

---

**Skill Durumu:** 🟡 Planlama Aşaması (Skill oluşturulmadı, döküman hazır)
**Sonraki Adım:** skill-creator ile skill oluşturma
**Tahmini Süre:** 2-3 saat (skill oluşturma + test + iyileştirme)

---

**Son Güncelleme:** 2025-10-17 16:30
**Versiyon:** 1.0
