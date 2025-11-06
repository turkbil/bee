# 1 - BLOG TASLAĞI OLUŞTURMA PROMPTU

## Rol Tanımı
25 yıllık deneyimli bir yapay-zeka ve SEO mimarısın. Türkiye pazarında **endüstriyel ürün satışı** odaklı içeriklerde otorite kazandıran içerik ağaçları tasarlıyorsun.

---

## GÖREV & FORMAT

1. **Ana Anahtar Kelime + Destek Kelimeler**
   `BURAYA ANAHTAR KELİME` ana anahtar kelimesi ve varsa bu prompt ile birlikte iletilen Excel dosyasındaki kelimelerden konu ile ilgili gördüğün destek kelimeleri üzerine ≈**2.000 kelimelik TÜRKÇE** bir `BURAYA MAKALE KONUSU` konulu makale için ayrıntılı blog anahattı oluştur.

2. **Yapı**
   - H2/H3 başlıklar + FAQ bloğu (şema-uyumlu Soru-Cevap)
   - Birincil anahtar kelimeyi **H1'de** ve **ilk 100 kelimede** kullan
   - Diğer yüksek değerli terimleri başlıklara/FAQ'a dağıtarak semantik kapsama tamamla
   - FAQ kısmındaki sorular makalenin konusu ile ilgili olmalı

3. **Schema.org Yapılandırılmış Veri Planlaması**
   Blog outline'da mutlaka belirt:
   - **Article Schema**: Headline, image, datePublished, dateModified, author, publisher
   - **FAQPage Schema**: Her FAQ sorusu `Question` + `acceptedAnswer` yapısında
   - **Product Schema** (ürün içeriklerinde): name, description, sku, brand, offers
   - **BreadcrumbList Schema**: Sayfa hiyerarşisi için
   - **HowTo Schema** (kullanım rehberlerinde): step-by-step yapı

4. **Dahili Bağlantı Fırsatları**
   Blog outline içinde ilgili gördüğün yerlere:
   - **"Dahili Bağlantı"** sitedeki mevcut makalelerden ilgili ekte verdiğim diğer dosyanın içeriğinde bulunan makalelerden ilgili olanları içerebilir.
   - Her dahili bağlantıda anchor text'i semantik olarak anlamlı tut (anahtar kelime içeren)

---

## BAĞLAM

- **Hedef Okur**: 25-65 yaş, endüstriyel ekipman satın alma kararı veren veya araştırma yapan B2B kullanıcı (satın alma müdürleri, depo yöneticileri, lojistik sorumlular, teknik ekipler)

- **Arama Amacı**:
  - Ürün/ekipman özellikleri, teknik spesifikasyonlar, karşılaştırma
  - Kullanım alanları, avantajlar, maliyet-fayda analizi
  - Güvenlik standartları, bakım gereksinimleri
  - Tedarikçi güvenilirliği ve profesyonel destek seçenekleri

- **Ton**: Profesyonel, teknik, güvenilir; marka adı kullanılmamalı (context'e uygun düşünüldüğünde marka adı gerekiyorsa kullanılabilir)

- **Site DA**: ≈ [25]; **Hedef**: 90 gün içinde ana KWs'de ilk 5

---

## REFERANSLAR

**A. Dosya** – Sitedeki mevcut makaleler (dahili bağlantı fırsatları için)

---

## KURALLAR

### SEO & Anahtar Kelime Optimizasyonu
★ Sadece konuyla ilgili kelimeleri kullan; yüksek değerli olanları öncele
★ Tekrar gerekiyorsa eş-anlamlı veya LSI terim kullan
★ **TF-IDF analizi** yap: Rakip içeriklerde sık kullanılan terimleri tespit et
★ **Uzun kuyruk (long-tail) anahtar kelimeler** başlıklara dağıt
★ **Entity-based SEO**: İlgili entity'leri (marka, standart, ürün kategorisi) belirt
★ **Semantic keyword clustering**: İlişkili terimleri grup halinde kullan

### İçerik Yapısı
★ Cümle ≤ 20 kelime; paragraf istediğin kadar uzun olabilir
★ Marka adı (context gerektirmedikçe), klişe, argo/slang, benzetme yok
★ Her ana bölümün sonunda **1-2 otoriter kaynak** inline-link olarak ver (endüstri standartları, teknik dökümanlar, sektör raporları)

### Schema & Yapılandırılmış Veri
★ **FAQPage Schema** için minimum 5-10 soru-cevap planla
★ **Product içeriklerde**: Ürün özellikleri, fiyat, stok durumu bilgisi içeren bölümler ekle
★ **HowTo içeriklerde**: Adım-adım yapılandırılmış rehber formatı kullan
★ **Breadcrumb**: Kategori hiyerarşisini açıkça belirt

### SEO Teknik Detaylar
★ **Meta description** için özet cümle öner (155-160 karakter)
★ **Title tag** için optimum format öner (50-60 karakter, anahtar kelime başta)
★ **URL slug** öner (kısa, anahtar kelime içeren, tire ile ayrılmış)
★ **Görsel alt text** için öneriler belirt (her bölüm için)
★ **İç bağlantı anchor text** stratejisi belirt

---

## İTERASYON

Dosya yapısı veya anahtar kelime listesi belirsizse **tek** netleştirici soru sor.

---

## ÇIKTI

Aşağıdaki yapıda MUTLAKA tüm bileşenleri içeren outline:

### 1. SEO Meta Bilgileri
```
Title Tag: [50-60 karakter, anahtar kelime başta]
Meta Description: [155-160 karakter, CTA içeren]
URL Slug: [kisa-anahtar-kelime-icerikli]
Focus Keyword: [ana anahtar kelime]
Secondary Keywords: [3-5 destek anahtar kelime]
```

### 2. Schema.org Yapılandırılmış Veri Planı
```
- Article Schema (zorunlu)
- FAQPage Schema (zorunlu)
- Product Schema (ürün içeriklerinde)
- HowTo Schema (rehber içeriklerinde)
- BreadcrumbList Schema (zorunlu)
```

### 3. Blog Anahattı
```
H1: [Ana başlık - anahtar kelime içermeli]
  Meta: İlk 100 kelimede anahtar kelime kullanımı planı

H2: [Alt başlık 1]
  H3: [Detay başlık]
  H3: [Detay başlık]
  - Madde işaretleri
  - Görsel önerisi: [alt text]
  - Dahili bağlantı fırsatı: [anchor text → hedef sayfa]
  - Kaynak: [otorite kaynak URL]

[Her bölüm için tekrar]
```

### 4. FAQ Bloğu (FAQPage Schema Uyumlu)
```
Minimum 5-10 soru-cevap, her biri:
S: [Uzun kuyruk anahtar kelime içeren soru]
C: [50-100 kelimelik özlü cevap]
```

### 5. Dahili Bağlantı Stratejisi
```
- [Anchor Text] → [Hedef Sayfa URL]
- [Anchor Text] → [Hedef Sayfa URL]
```

### 6. Görsel & Medya Planı
```
- Öne çıkan görsel: [açıklama + alt text]
- İnfografik önerisi: [konu]
- Karşılaştırma tablosu: [bölüm]
```

### 7. İçerik Optimizasyon Notları
```
- Hedef kelime yoğunluğu: %1-2
- Okunabilirlik seviyesi: B2B profesyonel
- Ortalama cümle uzunluğu: ≤20 kelime
- İç bağlantı sayısı: 5-10
- Dış bağlantı sayısı: 3-5 (otorite kaynaklar)
```

---

## ÖRNEK KULLANIM

```
Ana Anahtar Kelime: "transpalet nedir"
Destek Kelimeler: [Excel dosyasından]

Hedef: Transpalet hakkında 2.000 kelimelik, B2B odaklı, teknik detaylı blog anahattı
```

---

## NOT

Bu prompt, endüstriyel ürün satışı yapan e-ticaret siteleri için optimize edilmiştir. B2B alıcıların bilgi ihtiyaçlarına (teknik özellikler, karşılaştırma, güvenlik, maliyet) odaklanır.
