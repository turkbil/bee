# ❓ FAQ SİSTEMİ

## 🎯 KARAR: FAQ'LAR AYRI TABLO DEĞİL, ÜRÜN İÇİNDE JSON!

### ✅ **NEDEN shop_products tablosunda?**

1. **Performans**
   - Landing page tek query ile yüklenir
   - JOIN sorgusu gereksiz

2. **Veri Bütünlüğü**
   - FAQ ürünün bir parçası
   - Ürün silindi mi? FAQ'ı da sil (otomatik)

3. **Çoklu Dil**
   - JSON içinde zaten çoklu dil desteği var
   - Ekstra translation tablosu gereksiz

4. **AI Entegrasyonu**
   - PDF'den ürünle birlikte FAQ üretiliyor
   - Tek JSON'da tüm veri

5. **Bakım Kolaylığı**
   - FAQ güncellemesi = ürün güncellemesi
   - Versiyonlama kolay

---

## ❌ **AYRI TABLO KULLANILMAZ**

### **shop_faqs Tablosu OLMAYACAK:**
```sql
-- ❌ BU YAPI KULLANILMAYACAK:
CREATE TABLE shop_faqs (
    faq_id,
    product_id,
    question,
    answer,
    ...
)
```

**NEDEN?**
- Müşteri yorumları değil, ürün bilgisi
- Her ürünün FAQ'ı kendine özel
- Ortak FAQ'lar yok (her ürün farklı)

---

## 📊 VERİ YAPISI

### **shop_products.faq_data Kolonu (JSON)**

```json
{
  "faq_data": [
    {
      "question": {
        "tr": "F4 201 bir vardiyada kaç saate kadar çalışabilir?",
        "en": "F4 201 bir vardiyada kaç saate kadar çalışabilir?",
        "vs.": "..."
      },
      "answer": {
        "tr": "Standart pakette gelen 2 adet 24V/20Ah Li-Ion modül ile tek şarjda 6 saate kadar kesintisiz çalışır. Yedek modüller ile batarya değişim süresi 60 saniyeden kısa olduğu için vardiya boyunca enerji kaybı yaşamazsınız.",
        "en": "Standart pakette gelen 2 adet 24V/20Ah Li-Ion modül ile tek şarjda 6 saate kadar kesintisiz çalışır. Yedek modüller ile batarya değişim süresi 60 saniyeden kısa olduğu için vardiya boyunca enerji kaybı yaşamazsınız.",
        "vs.": "..."
      },
      "sort_order": 1,
      "category": "usage",
      "is_highlighted": true
    },
    {
      "question": {
        "tr": "Dar koridorlarda manevra kabiliyeti nasıldır?",
        "en": "Dar koridorlarda manevra kabiliyeti nasıldır?",
        "vs.": "..."
      },
      "answer": {
        "tr": "400 mm gövde uzunluğu ve 1360 mm dönüş yarıçapı sayesinde 3,2 metreye kadar dar koridorlarda bile rahatlıkla döner. Özellikle yoğun raflı depolarda palet değişimini hızlandırır.",
        "en": "400 mm gövde uzunluğu ve 1360 mm dönüş yarıçapı sayesinde 3,2 metreye kadar dar koridorlarda bile rahatlıkla döner. Özellikle yoğun raflı depolarda palet değişimini hızlandırır.",
        "vs.": "..."
      },
      "sort_order": 2,
      "category": "technical",
      "is_highlighted": false
    }
  ]
}
```

---

## 📋 ALAN AÇIKLAMALARI

### **question** (JSON object)
- **tr**: Türkçe soru
- **en**: İngilizce soru (şimdilik Türkçe kopya)
- **vs.**: Dinamik dil desteği placeholder

### **answer** (JSON object)
- **tr**: Türkçe cevap (detaylı, ikna edici)
- **en**: İngilizce cevap (şimdilik Türkçe kopya)
- **vs.**: Dinamik dil desteği placeholder

### **sort_order** (integer)
Gösterim sırası (1, 2, 3, ...)

### **category** (string, optional)
FAQ kategorizasyonu:
- `usage` → Kullanım
- `technical` → Teknik
- `warranty` → Garanti
- `pricing` → Fiyatlandırma
- `service` → Servis

### **is_highlighted** (boolean, optional)
Öne çıkan FAQ'lar landing page'de vurgulanır.

---

## ✅ AI KURALLARI (FAQ İçin)

### **MİNİMUM 10 SORU-CEVAP**

Her ürün için **en az 10 FAQ** olmalı:
1. Kullanım süresi / vardiya performansı
2. Manevra kabiliyeti / dar koridor
3. Stabilizasyon / güvenlik özellikleri
4. Batarya / şarj sistemi
5. Garanti ve servis desteği
6. İkinci el / kiralık / finansman seçenekleri
7. Standart aksesuar / opsiyonel ekipmanlar
8. Kiralama / yedek parça paketleri
9. Saha kurulumu / operatör eğitimi
10. Teknik slogan ve motto

---

## 📝 CEVAP KURALLARI

### ✅ **DETAYLI CEVAPLAR**
Kısa "evet/hayır" cevapları yok. Her cevap:
- ✅ Minimum 2-3 cümle
- ✅ Teknik detay içermeli
- ✅ Faydaya odaklanmalı
- ✅ İletişim bilgisi eklenebilir (son sorularda)

**Örnek:**
```
❌ KISA: "Evet, garantisi var."

✅ UZUN: "F4 201 için 24 ay tam kapsamlı garanti sunuyoruz; şasi, motor, elektronik ve Li-Ion bataryalar bu kapsamda. İXTİF Türkiye genelinde mobil servis araçları ile 7/24 destek sağlar."
```

---

## 📌 ZORUNLU FAQ KONULARI

Her ürün için **mutlaka** şunlar sorulmalı:

### 1️⃣ **İKİNCİ EL / KİRALIK SEÇENEKLERİ**
```json
{
  "question": {
    "tr": "İkinci el, kiralık veya finansman seçenekleri mevcut mu?",
    "en": "..."
  },
  "answer": {
    "tr": "Evet, İXTİF olarak sıfır satışın yanı sıra ikinci el, kiralık ve operasyonel leasing çözümleri sunuyoruz. Filonuzun büyüklüğüne göre 12-36 ay arası ödeme planları hazırlıyor, yedek parça ve teknik servis paketlerini birlikte planlıyoruz. Detaylı teklif için 0216 755 3 555 numarasını arayabilir veya info@ixtif.com adresine yazabilirsiniz.",
    "en": "..."
  }
}
```

### 2️⃣ **GARANTİ VE SERVİS**
```json
{
  "question": {
    "tr": "Garantisi ve servis desteği nasıl işliyor?",
    "en": "..."
  },
  "answer": {
    "tr": "[ÜRÜN] için 24 ay tam kapsamlı garanti sunuyoruz; şasi, motor, elektronik ve Li-Ion bataryalar bu kapsamda. İXTİF Türkiye genelinde mobil servis araçları ile 7/24 destek sağlar, Türkiye genelinde uzman teknik servis ağımızla hızlı destek sağlanır.",
    "en": "..."
  }
}
```

### 3️⃣ **YEDEK PARÇA**
İkinci el FAQ'ının içinde mutlaka "yedek parça" kelimesi geçmeli.

### 4️⃣ **TEKNİK SLOGAN VE MOTTO**
```json
{
  "question": {
    "tr": "Teknik slogan ve motto nedir?",
    "en": "..."
  },
  "answer": {
    "tr": "Slogan: \"[ÜRÜNE ÖZEL SLOGAN]\". Motto: \"[ÜRÜNE ÖZEL MOTTO]\". Bu mesajlar satış sayfasında ayrı kartlarda öne çıkar.",
    "en": "..."
  }
}
```

---

## 🎨 LANDING PAGE GÖSTERİMİ

### **HTML Şablonu**

```html
<section class="faq-section">
  <div class="container">
    <h2 class="section-title">Sık Sorulan Sorular</h2>

    <div class="faq-accordion">
      @foreach($product->faq_data as $faq)
        <div class="faq-item {{ $faq['is_highlighted'] ? 'highlighted' : '' }}">
          <button class="faq-question" data-category="{{ $faq['category'] ?? '' }}">
            <span class="icon">❓</span>
            {{ $faq['question'][app()->getLocale()] }}
            <span class="toggle-icon">+</span>
          </button>
          <div class="faq-answer">
            {{ $faq['answer'][app()->getLocale()] }}
          </div>
        </div>
      @endforeach
    </div>

    <div class="faq-cta">
      <p><strong>Başka sorunuz mu var?</strong></p>
      <p>0216 755 3 555 numarasını arayın veya <a href="mailto:info@ixtif.com">info@ixtif.com</a> adresine yazın.</p>
    </div>
  </div>
</section>
```

---

## 🔍 FRONTEND FİLTRELEME (Opsiyonel)

```javascript
// FAQ kategori filtresi
const faqCategories = ['all', 'usage', 'technical', 'warranty', 'pricing', 'service'];

function filterFAQs(category) {
  document.querySelectorAll('.faq-item').forEach(item => {
    const itemCategory = item.querySelector('[data-category]').dataset.category;
    if (category === 'all' || itemCategory === category) {
      item.style.display = 'block';
    } else {
      item.style.display = 'none';
    }
  });
}
```

---

## ✅ ÖZET

| Özellik | Detay |
|---------|-------|
| **Konum** | `shop_products.faq_data` (JSON kolon) |
| **Minimum Sayı** | 10 soru-cevap |
| **Dil Desteği** | JSON içinde (`tr`, `en`, `vs.`) |
| **Zorunlu Konular** | İkinci el, kiralık, yedek parça, garanti, servis |
| **Cevap Uzunluğu** | Minimum 2-3 cümle, detaylı |
| **İletişim** | Son sorularda `0216 755 3 555` ve `info@ixtif.com` |

---

**ŞİMDİ AI KURALLARI DOSYASINI HAZIRLIYORUM (ESKİ KURALLARIN ENTEGRASYONU)...**
