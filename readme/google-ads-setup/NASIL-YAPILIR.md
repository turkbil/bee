# Google Ads Kampanya Kurulum HTML Sayfası - Nasıl Yapılır?

## 📋 AMAÇ

Kullanıcı Google Ads kampanya kurulumu yaparken karmaşık açıklamaları anlayamıyor.

**Çözüm:** Sol tarafta ne yapacağı, sağ tarafta neden yapacağı olan basit bir HTML sayfası oluşturuldu.

---

## 🎯 TEMEL PRENSİP

### ❌ YANLIŞ YAKLAŞIM:
```
"Görüntülü Reklam Ağı'nı kapatmalısın çünkü telefon için kötü..."
"Müşteri edinme ayarını şöyle yap..."
"ROAS stratejisi yerine..."
```
→ Kullanıcı kaybolur, karışır, bulamaz

### ✅ DOĞRU YAKLAŞIM:
```
SOL: ŞU AN EKRANDA NE VAR?
     ☐ Google Arama Agi

     OLACAK HAL:
     ☑ Google Arama Agi

SAĞ: NEDEN?
     Google ortaklarında göster
     Daha çok müşteri
```
→ Kullanıcı checkbox görür, tıklar, biter

---

## 🔧 NASIL YAPILIR?

### 1. Kullanıcıdan a-html.txt İste

```bash
# Kullanıcı Google Ads ekranını a-html.txt'ye kopyalar
cat a-html.txt
```

### 2. HTML'i Analiz Et

a-html.txt içinde ara:
- `aria-checked="true"` → Seçili checkbox
- `aria-checked="false"` → Seçili olmayan checkbox
- `radio_button_checked` → Seçili radio button
- `radio_button_unchecked` → Seçili olmayan radio button

### 3. Sadece Ekrandakini Yaz

**ÖNEMLİ:** Ekranda olmayan hiçbir şey yazma!

```
❌ YANLIŞ:
"Müşteri Edinme ayarını aç/kapat"
→ Ekranda "Müşteri Edinme" yok!

✅ DOĞRU:
"1. AGLAR
 ☐ Google Arama Agi"
→ Ekranda var, görüyor!
```

### 4. HTML Formatı

```html
<div class="item">
    <div class="now">
        <strong>1. AGLAR</strong><br><br>
        SU AN:<br>
        <span class="checkbox">☐</span> Google Arama Agi
    </div>
    <div class="todo">
        OLACAK HAL:<br>
        <span class="checkbox">☑</span> Google Arama Agi
    </div>
</div>
```

**Özellikler:**
- `☐` = Boş checkbox (şu an)
- `☑` = Dolu checkbox (olacak hal)
- `⦿` = Radio button seçili
- `○` = Radio button boş

### 5. Renk Kodları

```css
.item {
    border: 3px solid #ea4335; /* Kırmızı = Değiştirilecek */
}

.item.ok {
    border: 3px solid #34a853; /* Yeşil = Doğru, dokunma */
    opacity: 0.6; /* Soluk = Önemli değil */
}
```

---

## 📝 ÖRNEK WORKFLOW

### Kullanıcı Dedi:
> "Claude, Google Ads kampanya ayarlarında ne yapmalıyım?"

### Sen Yap:
1. **İste:** `a-html.txt dosyasını gönder`
2. **Oku:** HTML içeriğini analiz et
3. **Bul:** Hangi checkbox'lar var? Seçili mi?
4. **Yaz:** Basit HTML oluştur
   - Sol: ŞU AN + OLACAK HAL
   - Sağ: NEDEN?

### Sonuç:
```
https://tuufi.com/google-ads-setup/kampanya-kurulum.html
```

Kullanıcı açar, checkbox'ları görür, tıklar, biter.

---

## 🚨 KRİTİK KURALLAR

### 1. ❌ KARMAŞIK YAPMA!

```
❌ YANLIŞ:
"Hedef ROAS yerine Maksimum Dönüşüm kullan çünkü..."

✅ DOĞRU:
"☐ Maksimum Dönüşüm → ☑ Tıkla"
```

### 2. ❌ OLMAYAN ŞEY YAZMA!

```
❌ YANLIŞ:
"5. MUSTERI EDINME
 TIKLAMA (herkese)"

Kullanıcı: "Nerde bu? Bulamıyorum!"
```

**Sadece ekranda gördüklerini yaz!**

### 3. ✅ CHECKBOX KULLAN!

Kullanıcı görsel düşünüyor:
- ☐ → "Ah, boş checkbox var!"
- ☑ → "Dolu olacak!"
- ⦿ → "Radio button seçili!"

### 4. ✅ İKİ KOLON!

```
SOL (650px):              SAĞ:
- ŞU AN                   - NEDEN?
- OLACAK HAL              - Kısa açıklama
```

---

## 🔄 GÜNCELLEME NASIL YAPILIR?

### Kullanıcı "Sonraki" Tıkladı:

1. **Yeni a-html.txt iste**
2. **Yeni HTML analiz et**
3. **Aynı dosyayı güncelle:**

```php
/var/www/vhosts/tuufi.com/httpdocs/public/google-ads-setup/kampanya-kurulum.html
```

4. **Kullanıcı yeniler (F5)** → Yeni adımı görür

---

## 📂 DOSYA YAPISI

```
/var/www/vhosts/tuufi.com/httpdocs/
├── public/
│   └── google-ads-setup/
│       └── kampanya-kurulum.html       # Kullanıcının açtığı sayfa
│
└── readme/
    └── google-ads-setup/
        └── NASIL-YAPILIR.md            # Bu dosya (AI için)
```

---

## 💡 SONRAKI AI'LAR İÇİN NOTLAR

### Kullanıcı Şunu Derse:
> "Google Ads kampanya kurulum rehberi göster"

### Sen:
1. `a-html.txt` iste
2. HTML'i oku → Checkbox'ları bul
3. Basit HTML oluştur (şu an + olacak hal)
4. Yenile dedikçe güncelle

### Kullanıcı Şunu Derse:
> "Anlamıyorum, karışık!"

### Sen:
1. Daha basit yap
2. Checkbox ekle (☐ ☑)
3. "ŞU AN" ve "OLACAK HAL" yaz
4. Olmayan şey yazma!

### Kullanıcı Şunu Derse:
> "Bulamıyorum!"

### Sen:
1. ❌ Ekranda olmayan şey yazmışsın
2. ✅ Tekrar a-html.txt'yi oku
3. ✅ Sadece gördüklerini yaz

---

## 🎨 HTML TEMPLATE

```html
<!DOCTYPE html>
<html>
<head>
    <style>
        .item { border: 3px solid #ea4335; } /* Kırmızı = Yap */
        .item.ok { border: 3px solid #34a853; opacity: 0.6; } /* Yeşil = OK */
        .checkbox { font-size: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="left">
                <!-- ŞU AN + OLACAK HAL -->
            </div>
            <div class="right">
                <!-- NEDEN? -->
            </div>
        </div>
    </div>
</body>
</html>
```

---

## ✅ CHECKLIST

Yeni bir rehber sayfası oluştururken:

- [ ] a-html.txt aldın mı?
- [ ] HTML'i analiz ettin mi?
- [ ] Checkbox/Radio button durumlarını tespit ettin mi?
- [ ] Sadece ekrandaki şeyleri yazdın mı?
- [ ] Checkbox (☐ ☑) kullandın mı?
- [ ] İki kolon (SOL: ne yapmalı, SAĞ: neden) yaptın mı?
- [ ] Basit, karmaşık değil mi?
- [ ] Kullanıcı test etti mi?

---

## 💰 ÖZEL DURUM: PARA TASARRUFU STRATEJİSİ

### ⚠️ AĞLAR AYARI - ÖNEMLİ!

**Kullanıcı Şunu Derse:**
> "Çok para yiyor! Sadece Google'da arama yapanları görelim."

### ✅ YAPILACAK: AĞLARI KAPAT

**Varsayılan (Google önerir):**
- ☑ Google Arama Ağı İş Ortakları (AÇIK)
- ☑ Google Görüntülü Reklam Ağı (AÇIK)

**Para Tasarrufu Modu:**
- ☐ Google Arama Ağı İş Ortakları (KAPALI)
- ☐ Google Görüntülü Reklam Ağı (KAPALI)

**Sonuç:** Sadece Google.com arama sonuçlarında gösterilir!

---

### 💡 NEDEN KAPATMALIYIZ?

#### İş Ortakları Ağı:
- Park edilmiş alanlar
- Düşük kalite trafik
- Telefon aramaz, para yer

#### Görüntülü Reklam Ağı:
- YouTube banner
- Web sitesi banner
- Telefon aramaz, para yer

#### Sadece Google Arama:
- Google.com'da arama yapan
- Niyet YÜKSEK
- Telefon arama FAZLA
- Para verimli kullanılır

**ÖZET:** Az para, çok müşteri! 🎯

---

### 📋 HTML Örneği (Ağlar - Kapalı Mod)

```html
<div class="item">
    <div class="now">
        <strong>AGLAR</strong><br><br>
        SU AN:<br>
        <span class="checkbox">☑</span> Google Arama Agi Is Ortaklari<br>
        <span class="checkbox">☑</span> Google Goruntulu Reklam Agi
    </div>
    <div class="todo">
        OLACAK HAL:<br>
        <span class="checkbox">☐</span> Google Arama Agi Is Ortaklari<br>
        <span class="checkbox">☐</span> Google Goruntulu Reklam Agi
    </div>
</div>
```

**Açıklama:**
```
NEDEN KAPATMALIYIM?

💰 PARA TASARRUFU:
- Is Ortaklari = Park edilmis alanlar
- Goruntulu = YouTube, banner
- Dusuk kalite trafik
- Telefon aramasi az
- Para boşa gidiyor

🎯 SADECE GOOGLE ARAMA:
- Google.com'da arama yapan
- Niyet YUKSEK
- Telefon arama FAZLA
- Para verimli kullanilir

✅ SONUC: Az para, cok musteri!
```

---

## 🚀 ÖZET

**1 Cümle:** Kullanıcı ekranında ne görüyorsa, onu basit checkbox'larla göster.

**Prensip:**
- SOL: ☐ → ☑ (Görsel)
- SAĞ: NEDEN? (Kısa)

**Altın Kural:** Ekranda olmayan hiçbir şey yazma!

**Para Tasarrufu:** Kullanıcı "çok para yiyor" derse → Ağları kapat!

---

**Oluşturulma Tarihi:** 2025-10-26
**Son Güncelleme:** 2025-10-26 (Para Tasarrufu Stratejisi eklendi)
**Dosya Konumu:** `/var/www/vhosts/tuufi.com/httpdocs/readme/google-ads-setup/NASIL-YAPILIR.md`
**İlgili Dosya:** `/var/www/vhosts/tuufi.com/httpdocs/public/google-ads-setup/kampanya-kurulum.html`
