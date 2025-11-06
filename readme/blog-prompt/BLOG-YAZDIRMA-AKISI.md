# 🚀 BLOG YAZDIRMA AKIŞI - TEK DOSYA

> **2 ANA AŞAMA: Önce TASLAK → Sonra GERÇEK İÇERİK**

---

## 📌 SADECE BU DOSYAYI KULLANIN!

**Basit Akış:**
1. **TASLAK OLUŞTUR** (Prompt 1 kullan) → Yapıyı kur
2. **GERÇEK İÇERİK YAZ** (Prompt 2 kullan) → Taslağı doldur

Bu kadar! Diğer dosyaları unutun.

---

## 🔵 AŞAMA 1: TASLAK OLUŞTUR

### 🎯 Önce Anahtar Kelimeleri Belirle
```
✅ Ana anahtar kelime: [örn: transpalet nedir]
✅ 3-5 destek kelime: [manuel transpalet, elektrikli transpalet, transpalet fiyatları]
✅ Hedef kitle: [B2B / B2C]
```

### 📝 TASLAK PROMPT'U - Kopyala & Yapıştır:

```markdown
Sen 25 yıllık SEO uzmanısın. Türkiye'de endüstriyel ürün satışı yapan bir site için blog taslağı oluşturacaksın.

ANAHTAR KELİME: [BURAYA ANA KELİMEYİ YAZ]
DESTEK KELİMELER: [BURAYA DESTEK KELİMELERİ YAZ]

İSTENENLER:
1. Title (55 karakter) + Meta Description (155 karakter)
2. H1 ve H2/H3 başlıklar (anahtar kelime dağılımıyla)
3. Her başlık için kelime sayısı hedefi
4. Her bölüm için not (görsel, tablo, liste vb.)
5. 10 adet SSS sorusu
6. Dahili link önerileri (5 adet)
7. Schema planı (Article, FAQ, HowTo)

Blog 2000-2500 kelime olacak. B2B odaklı, teknik detaylı.

Çıktıyı markdown formatında, net başlıklarla ver.
```

### Örnek Kullanım:
```
ANAHTAR KELİME: transpalet nedir
DESTEK KELİMELER: manuel transpalet, elektrikli transpalet, transpalet fiyatları, 2 ton transpalet
```

---

## 🟢 AŞAMA 2: TASLAKTAN GERÇEK İÇERİK OLUŞTUR

### ✍️ İÇERİK PROMPT'U - Taslağı Gerçeğe Dönüştür:

```markdown
Sen endüstriyel ürünler konusunda uzman içerik yazarısın.

BÖLÜM: [TASLAKTAN BÖLÜM BAŞLIĞINI YAPIŞTIIR]
HEDEF KELİME SAYISI: [TASLAKTAN AL]
ANAHTAR KELİMELER: [İLGİLİ KELİMELERİ YAZ]

YAZIM KURALLARI:
- Cümle max 20 kelime
- Paragraf 50-150 kelime
- %1-2 anahtar kelime yoğunluğu
- LSI ve eş anlamlı kelimeler kullan
- Profesyonel B2B tonu

EKLE:
- Madde listesi VEYA tablo (uygunsa)
- 1 dahili link önerisi
- 1 otorite kaynak
- Görsel önerisi + alt text

Markdown formatında yaz. SEO notlarını sonuna ekle.
```

### İpucu:
Her bölümü ayrı ayrı yazdırın. Sonra birleştirin.

---

## ❓ ADIM 4: SSS (FAQ) BÖLÜMÜ OLUŞTUR

### SSS Prompt'u:

```markdown
Aşağıdaki konu için 10 adet SSS oluştur.

KONU: [ANA ANAHTAR KELİME]
DESTEK KELİMELER: [LİSTE]

HER SORU İÇİN:
- Soru: Long-tail anahtar kelime içermeli (3-5 kelime)
- Cevap: 50-100 kelime, net ve özlü
- Cevaba bir anahtar kelime doğal şekilde yerleştir

SORU TİPLERİ DAHİL ET:
- Nedir/Nasıl soruları (3 adet)
- Karşılaştırma soruları (2 adet)
- Fiyat/Maliyet soruları (2 adet)
- Teknik özellik soruları (2 adet)
- Problem çözüm sorusu (1 adet)

FAQPage Schema'ya uygun format kullan.
```

---

## 🔧 ADIM 5: SEO KONTROLÜ & SCHEMA EKLE

### A. Hızlı SEO Kontrol Listesi

```markdown
## ✅ BİTİRMEDEN KONTROL ET:

### Meta Bilgiler
□ Title: 50-60 karakter, anahtar kelime başta
□ Description: 155-160 karakter, CTA var
□ URL: kisa-ve-aciklayici
□ H1: Tek ve optimize

### İçerik
□ İlk 100 kelimede ana anahtar kelime
□ Kelime sayısı: 2000-2500
□ H2 başlık sayısı: 4-6
□ Görsel sayısı: 5+
□ Dahili link: 5-10
□ Dış link: 3-5 otorite

### Teknik
□ Tüm görsellerde alt text
□ Mobile responsive kontrol
□ Sayfa hızı (<3 saniye)
□ Schema markup eklendi
```

### B. Schema Markup Ekle (Article + FAQ)

```html
<!-- Bu kodu <head> içine veya içeriğin sonuna ekle -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Article",
      "headline": "[BAŞLIK]",
      "description": "[META DESCRIPTION]",
      "image": "[GÖRSEL URL]",
      "datePublished": "[TARİH]",
      "author": {
        "@type": "Organization",
        "name": "[ŞİRKET ADI]"
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "[SSS SORU 1]",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "[CEVAP 1]"
          }
        }
        // Diğer sorular için tekrarla
      ]
    }
  ]
}
</script>
```

---

## 💡 HAZIR ŞABLON: DİREKT KULLAN

### Komple Blog Yazdırma - Tek Prompt (Kısa Bloglar İçin)

```markdown
Endüstriyel ürün satışı yapan Türkiye'deki bir e-ticaret sitesi için "[ANAHTAR KELİME]" konusunda 2000 kelimelik SEO-optimize blog yaz.

İÇERİK YAPISI:
1. Giriş (100-150 kelime) - Ana anahtar kelime ilk cümlede
2. [KONU] Nedir? (300-400 kelime)
3. Çeşitleri/Türleri (400-500 kelime) - Tablo ekle
4. Nasıl Çalışır/Kullanılır? (300-400 kelime)
5. Seçim Kriterleri (400-500 kelime) - Liste formatı
6. SSS (10 soru-cevap, her cevap 50-100 kelime)
7. Sonuç (100-150 kelime) - CTA ile bitir

EKLE:
- Her bölümde 1-2 anahtar kelime
- 5 dahili link önerisi
- 3 dış kaynak (otorite site)
- Her bölüm için görsel önerisi + alt text
- Title (55 kar) + Meta description (155 kar)

YAZIM: B2B odaklı, teknik, güvenilir ton. Cümle ≤20 kelime.
```

---

## 🎯 ÖRNEK: TRANSPALET BLOG AKIŞI

### 1️⃣ Anahtar Kelimeler
- Ana: transpalet nedir
- Destek: manuel transpalet, elektrikli transpalet, transpalet fiyatları

### 2️⃣ Taslak Çıktısı (Özet)
```
Title: Transpalet Nedir? Çeşitleri ve Fiyatları [2025]
H1: Transpalet Nedir? Çeşitleri, Özellikleri ve Kullanım Alanları
H2: Transpalet Nedir ve Ne İşe Yarar? (250 kelime)
H2: Transpalet Çeşitleri (500 kelime)
  H3: Manuel Transpalet
  H3: Elektrikli Transpalet
H2: Transpalet Nasıl Çalışır? (400 kelime)
H2: Transpalet Seçim Kriterleri (400 kelime)
H2: SSS (10 soru)
```

### 3️⃣ İçerik Yazdır (Bölüm bölüm)
### 4️⃣ SSS Ekle
### 5️⃣ Schema Ekle & Kontrol Et

---

## 🚨 EN SIK YAPILAN HATALAR

❌ **YAPMAYIN:**
- Tüm içeriği tek seferde yazdırmaya çalışmak
- Anahtar kelime dolgusu (keyword stuffing)
- Schema markup'ı atlama
- Alt text yazmama
- Çok uzun cümleler (>20 kelime)

✅ **YAPIN:**
- Bölüm bölüm yazdırın
- Doğal anahtar kelime kullanımı
- Her zaman schema ekleyin
- Her görsele alt text
- Kısa, net cümleler

---

## 📊 HIZLI KONTROL

### Blog Hazır mı? Son Kontrol:
```
□ 2000+ kelime
□ Title & meta var
□ H1 tek ve optimize
□ 4-6 H2 başlık
□ 10 SSS sorusu
□ 5+ dahili link
□ 3+ dış kaynak
□ Schema eklendi
□ Görsellerde alt text
□ Mobile test yapıldı
```

**Hepsi ✓ ise → YAYINLA! 🚀**

---

## 💬 YARDIM

**Soru:** Hangi prompt'u ne zaman kullanacağım?
**Cevap:** Sırayla:
1. Taslak prompt (Adım 2)
2. İçerik prompt (Adım 3) - her bölüm için
3. SSS prompt (Adım 4)
4. Schema ekle (Adım 5)

**Soru:** Tek prompt'la tüm blogu yazdırabilir miyim?
**Cevap:** 1500 kelimeye kadar evet. Daha uzun bloglar için bölüm bölüm daha kaliteli.

---

## 📁 DİĞER DOSYALAR NE İŞE YARAR?

İhtiyacınız olursa:
- **MASTER-GUIDE.md** → Detaylı öğrenmek isteyenler için (30+ sayfa)
- **1,2,3-...md** → Spesifik konular için
- **Bu dosya** → Hızlıca blog yazdırmak için (EN PRATİK)

---

**✨ İPUCU:** Bu dosyayı bookmark yapın. Her blog için bu akışı takip edin. 5 adımda profesyonel, SEO-optimize blog hazır!

---

*Güncelleme: 6 Kasım 2025 | Endüstriyel Ürün Satışı Odaklı | B2B & B2C*