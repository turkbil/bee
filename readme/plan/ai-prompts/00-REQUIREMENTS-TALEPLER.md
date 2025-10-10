# 🎯 NURULLAH'IN AI TALEPLERİ - NET VE AÇIK

## 🚨 KRİTİK SORUNLAR (ACİL ÇÖZÜLECEK)

### 1. ❌ **"UZUN YAZI" SORUNU**
**SORUN:** "Uzun yazı yaz" diyorum → 3 paragraf yazıyor → "daha uzun" diyorum → "uzun yazı nedir" diye saçmalıyor

**İSTEDİĞİM:**
- "uzun" = MİNİMUM 800-1200 kelime
- "çok uzun" = MİNİMUM 1500-2500 kelime  
- "kısa" = 200-400 kelime
- "detaylı" = 1000-1500 kelime
- "500 kelimelik" = TAM 500 kelime (±%20)

### 2. ❌ **PARAGRAF YAPISI YOK**
**SORUN:** Tek blok halinde yazı → Okunamıyor → Profesyonel görünmüyor

**İSTEDİĞİM:**
- MİNİMUM 4 paragraf zorunlu
- Her paragraf 3-6 cümle
- Paragraflar arası boş satır
- Başlık ve alt başlıklar olsun
- Liste ve madde kullanımı

### 3. ❌ **APTAL YANITLAR**
**SORUN:** 
- "bilişim hakkında yaz" → "hangi konuda?" diyor
- Konu veriyorum → 3 satır yazıyor
- "Bu konuda yardımcı olamam" diyor

**İSTEDİĞİM:**
- Konu verilince DİREKT yazmaya başlasın
- Belirsizse bile TAHMİN edip yazsın
- "Yardımcı olamam" DEMESİN, her zaman birşey üretsin

---

## 🎯 FONKSİYONEL İSTEKLER

### 1. 👤 **KİŞİSEL TANIMA (Chat Panel)**
**Senaryo:** Chat panelinde konuşurken

**İSTEDİĞİM:**
```
AI: "Merhaba Nurullah! Bugün ne yapmak istiyorsun?"
Ben: "Nasılsın?"
AI: "İyiyim, teşekkürler Nurullah! TechCorp için hangi konuda yardım edebilirim?"
```

**KURALLAR:**
- users tablosundan benim adımı bilsin
- Samimi ve kişisel konuşsun
- Geçmiş konuşmaları hatırlasın

### 2. 🏢 **ŞİRKET TANIMA (Feature/Prowess)**
**Senaryo:** Feature kullanırken

**İSTEDİĞİM:**
```
Ben: "Blog yazısı yaz"
AI: [TechCorp'un teknoloji şirketi olduğunu bilerek, o sektöre uygun yazar]
```

**KURALLAR:**
- AITenantProfile'dan şirket bilgilerini alsın
- Sektöre uygun içerik üretsin
- Marka tonunu korusun
- AMA sürekli "teknoloji şirketiyiz" demesin

### 3. 🔄 **ÇEVİRİ SİSTEMİ (2 Aşamalı)**

#### A) **Prowess'te Çeviri:**
```
1. "Çeviri" feature'ına tıkla
2. Metin kutusuna yapıştır
3. Dil seç (dropdown)
4. Çevir
```

#### B) **Pages Modülünde Çeviri:**
```
1. Edit sayfasında "Çevir" butonu
2. Mevcut dil: TR, Hedef diller: EN, DE
3. Otomatik JSON field'lara yaz
4. Veritabanına kaydet
```

**KURAL:** Çeviride ASLA yorum katma, kelimesi kelimesine çevir

### 4. 📊 **VERİTABANI ERİŞİMİ**

**İSTEDİĞİM:**
- Ürünler tablosuna erişsin
- "En çok satan ürün nedir?" sorusuna cevap versin
- Stok durumu söylesin
- Sipariş geçmişini bilsin

**YETKİ SİSTEMİ:**
- ROOT: Her şeye erişir
- ADMIN: Kendi tenant'ına erişir
- EDITOR: Sadece yetkili modüllere
- USER: Çok sınırlı

### 5. 🎨 **FEATURE TİPLERİ**

#### **TİP 1: BASİT (Static)**
```
Input: "Blog yaz"
Output: Direkt yazar
```

#### **TİP 2: SEÇİMLİ (Selection)**
```
Input: "Çeviri yap"
Sistem: "Hangi dile?" [Dropdown]
User: "İngilizce"
Output: Çevirir
```

#### **TİP 3: SAYFA BAĞIMLI (Context)**
```
Pages/Edit sayfasında:
Button: "Bu sayfayı optimize et"
Sistem: [Mevcut içeriği alır, optimize eder]
```

#### **TİP 4: ENTEGRE (Integration)**
```
Input: "Ürün ekle"
Sistem: [Form gösterir]
User: [Doldurur]
Sistem: [Veritabanına kaydeder]
```

---

## 💡 AKILLI ÖZELLİKLER

### 1. **HATA ANLAMA**
```
Ben: "bloğ yazısı yaz" (yanlış yazım)
AI: "Blog yazısı" olarak anlayıp devam eder
```

### 2. **CONTEXT MEMORY**
```
Ben: "Dün konuştuğumuz konu hakkında"
AI: [Önceki konuşmayı hatırlar ve devam eder]
```

### 3. **TAHMİN YETENEĞİ**
```
Ben: "bunu daha iyi yap"
AI: [Son yapılan işlemi anlar ve iyileştirir]
```

### 4. **ÖĞRENME**
```
- Hangi uzunlukta yazı sevdiğimi öğrensin
- Hangi tonda konuşmayı tercih ettiğimi bilsin
- Sık kullandığım feature'ları öncelikli göstersin
```

---

## 📝 PROMPT HİYERARŞİSİ

### **İstediğim Sıralama:**

```
1. GİZLİ SİSTEM KURALLARI (kullanıcı görmez)
   - Uzunluk kuralları
   - Paragraf zorunluluğu
   - Kalite standartları

2. CONTEXT BİLGİSİ
   - Chat'te: Kullanıcı bilgisi
   - Feature'da: Şirket bilgisi
   - Sayfa bilgisi varsa

3. QUICK PROMPT (Feature'ın ne yapacağı)
   "Sen bir blog yazarısın..."

4. EXPERT PROMPTS (Detaylı bilgiler)
   - Priority 1: En önemli
   - Priority 2: Önemli
   - Priority 3: Normal

5. RESPONSE TEMPLATE (Yanıt formatı)
   - Paragraf sayısı
   - Kelime sayısı
   - Markdown formatı
```

---

## ✅ BAŞARI KRİTERLERİ

### **KISA VADELİ (1 Hafta)**
1. ✅ "Uzun yazı" = 1000+ kelime ZORUNLU
2. ✅ Minimum 4 paragraf ZORUNLU
3. ✅ "Bilişim" deyince direkt yazması
4. ✅ Kullanıcıyı tanıması (Merhaba Nurullah!)
5. ✅ Şirketi tanıması (TechCorp context)

### **ORTA VADELİ (1 Ay)**
1. ✅ 150+ hazır feature
2. ✅ Çeviri sistemi çalışıyor
3. ✅ Database entegrasyonu
4. ✅ Yetki sistemi aktif
5. ✅ Öğrenen AI

### **UZUN VADELİ (3 Ay)**
1. ✅ Tahmin eden AI
2. ✅ Otomatik optimizasyon
3. ✅ Multi-language
4. ✅ Voice integration
5. ✅ Mobile app

---

## 🚫 YAPMAMASI GEREKENLER

1. ❌ "Bu konuda yardımcı olamam" DEMESİN
2. ❌ "Hangi konuda?" diye SORMASIN (tahmin etsin)
3. ❌ Kısa yazı YAZMASIN (minimum 400 kelime)
4. ❌ Tek paragraf YAZMASIN (minimum 4)
5. ❌ HTML kartlar içinde düz metin GÖSTERMESİN
6. ❌ Sürekli "teknoloji şirketiyiz" DEMESİN
7. ❌ Çeviride yorum KATMASIN
8. ❌ "Üzgünüm" ile BAŞLAMASIN

---

## 🎯 ÖZET: NE İSTİYORUM?

**BASİT:**
1. UZUN YAZSIN (1000+ kelime)
2. PARAGRAFLARA BÖLSÜN (4+)
3. BENİ TANISIN (Nurullah)
4. ŞİRKETİ BİLSİN (TechCorp)
5. APTALLIK YAPMASIN

**İLERİ SEVİYE:**
1. VERİTABANINA ERİŞSİN
2. ÇEVİRİ YAPSIN (kelimesi kelimesine)
3. SAYFA İÇERİĞİNİ BİLSİN
4. ÖĞRENSİN VE GELİŞSİN
5. TAHMİN ETSİN

---

## 📌 EN ÖNEMLİ 3 ŞEY

### 1. 🔴 **UZUN YAZSIN**
```php
if (contains($input, "uzun")) {
    $minWords = 1000;
    $minParagraphs = 4;
}
```

### 2. 🔴 **CONTEXT KULLANSIN**
```php
if ($mode === 'chat') {
    useUserContext(); // "Merhaba Nurullah"
} else {
    useCompanyContext(); // "TechCorp için..."
}
```

### 3. 🔴 **APTALLIK YAPMASIN**
```php
// YANLIŞ:
"Bu konuda yardımcı olamam"
"Hangi konuda yazayım?"
"Üzgünüm ama..."

// DOĞRU:
"Hemen yazıyorum..."
[1000+ kelime içerik]
[4+ paragraf]
```

---

**NOT:** Bu belge, tüm isteklerimi NET ve AÇIK şekilde özetliyor. Bunları yapan bir AI istiyorum. "Salak AI" değil, "ULTRA AKILLI AI" olsun!