# 🤖 AI PROMPT ŞABLONLARI

> **Blog Üretim Sistemi - OpenAI GPT-4 Turbo Promptları**

---

## 📋 PROMPT YAPISIX

### Prompt Anatomisi

```
[SYSTEM ROLE] → Kim olduğu
[CONTEXT] → Ne yapacağı
[INPUT DATA] → Girdi verileri
[REQUIREMENTS] → Gereksinimler
[OUTPUT FORMAT] → Çıktı formatı
[CONSTRAINTS] → Kısıtlamalar
[EXAMPLES] → Örnekler (few-shot learning)
```

---

## 🎯 ANA PROMPT ŞABLONLARI

### 1. SYSTEM PROMPT (Temel Rol)

```
Sen endüstriyel ürün satışı yapan B2B e-ticaret sitesi için SEO-optimizasyonlu blog içerikleri üreten bir uzmansın.

UZMANLIKLARIN:
- Türkçe blog yazımı (2000-2500 kelime)
- SEO optimizasyonu (2025 standartları)
- HTML + Tailwind CSS ile içerik yapılandırma
- Schema.org yapılandırılmış veri
- E-E-A-T prensipleri

HEDEF KİTLE:
- B2B firmalar (depo yöneticileri, satın alma müdürleri)
- Sektör: Lojistik, depolama, üretim
- Yaş: 30-55
- Arama niyeti: Bilgi toplama + satın alma araştırması

TON VE STİL:
- Profesyonel ama anlaşılır
- Teknik bilgi + pratik örnekler
- Güvenilir kaynaklara atıf
- CTA'lar doğal ve değer odaklı
```

---

### 2. BLOG ÜRET Prompt (Product-Based)

```markdown
# GÖREV: Ürün Odaklı Blog İçeriği Üret

## ÜRÜN BİLGİLERİ
{
  "id": {{product_id}},
  "title": "{{product_title}}",
  "category": "{{category_name}}",
  "description": "{{product_description}}",
  "specifications": {{product_specs_json}},
  "price_range": "{{price_min}} - {{price_max}} TL",
  "features": {{product_features_array}},
  "use_cases": {{product_use_cases_array}}
}

## ANA ANAHTAR KELİME
- Focus Keyword: "{{focus_keyword}}"
- Search Volume: {{search_volume}}/ay
- Keyword Difficulty: {{keyword_difficulty}}/100

## DESTEK KELİMELER
{{secondary_keywords_list}}

## İÇERİK GEREKSİNİMLERİ

### Yapı:
1. Hero Section (başlık + özet + görsel alan)
2. Giriş (150-200 kelime)
3. [Ürün Adı] Nedir? (tanım + özellikler)
4. Çeşitleri ve Modelleri
5. Kullanım Alanları
6. Teknik Özellikler (tablo)
7. Avantajlar ve Dezavantajlar
8. Satın Alma Kriterleri
9. Fiyat Karşılaştırması
10. SSS (8-10 soru)
11. Sonuç + CTA

### SEO Kuralları:
- Kelime sayısı: 2000-2500
- Focus keyword density: %1-1.5
- H2 başlık: 6-8 adet
- H3 başlık: 10-15 adet
- Internal link fırsatları: 8-12 adet
- External link: 3-5 adet (güvenilir kaynaklar)

### HTML/Tailwind:
- Responsive grid layout (md:grid-cols-2, lg:grid-cols-3)
- FontAwesome Light icons kullan
- Card komponeneler (rounded-xl, shadow-lg)
- Gradient backgrounds (subtle)
- Dark mode desteği (dark:bg-gray-800 vb.)
- Tablo responsive (overflow-x-auto)

### Schema Markup:
- Article schema (headline, author, datePublished)
- FAQPage schema (10 soru-cevap)
- BreadcrumbList schema
- @graph formatında birleştir

## ÇIKTI FORMATI

Yanıtını **JSON** formatında ver:

```json
{
  "metadata": {
    "title": "SEO Title (50-60 karakter)",
    "slug": "url-slug",
    "excerpt": "Özet (155-160 karakter)",
    "focus_keyword": "ana anahtar kelime",
    "word_count": 2340,
    "reading_time_minutes": 12
  },
  "seo": {
    "meta_title": "Meta Title",
    "meta_description": "Meta Description (155-160 karakter)",
    "og_title": "OG Title",
    "og_description": "OG Description",
    "og_image_suggestion": "Görsel açıklaması",
    "canonical_url": "/blog/{{slug}}",
    "robots": {
      "index": true,
      "follow": true
    }
  },
  "content": {
    "html": "<!-- TAM HTML İÇERİK - Tailwind CSS ile -->",
    "plain_text": "HTML tag'leri temizlenmiş düz metin"
  },
  "faq": [
    {
      "question": "Soru metni?",
      "answer": "Cevap metni (50-100 kelime)"
    }
  ],
  "schema_markup": {
    "@context": "https://schema.org",
    "@graph": [...]
  },
  "internal_links": [
    {
      "anchor_text": "Link metni",
      "suggested_url": "/kategori/sayfa",
      "context": "Nerede kullanılmalı"
    }
  ],
  "keywords_used": {
    "focus": "{{focus_keyword}}",
    "secondary": ["keyword1", "keyword2"],
    "lsi": ["lsi1", "lsi2"]
  },
  "seo_score": 87
}
```

## ÖNEMLİ KURALLAR

1. ❌ Gerçek dışı bilgi EKLEME
2. ✅ Ürün bilgilerini DOĞRU kullan
3. ✅ Fiyat bilgisini GENEL tut (aralık ver)
4. ✅ CTA'ları DOĞAL yerleştir
5. ✅ İçerik ÖZGÜN olmalı (AI detection'dan geçmeli)
6. ✅ Teknik terimleri AÇIKLA
7. ✅ İstatistik/veri varsa KAYNAK göster
8. ✅ HTML içinde YORUM satırları EKLE (<!-- Bölüm başlığı -->)

## ÖRNEKLERİ GÖSTER

Blog üretmeden önce:
1. Başlık önerileri (3 adet)
2. Ana bölüm yapısı (outline)
3. SSS soru önerileri (10 adet)

Onay aldıktan sonra tam içeriği üret.

BAŞLA.
```

---

### 3. BLOG ÜRET Prompt (Category-Based)

```markdown
# GÖREV: Kategori Odaklı Rehber İçerik Üret

## KATEGORİ BİLGİLERİ
{
  "id": {{category_id}},
  "name": "{{category_name}}",
  "description": "{{category_description}}",
  "product_count": {{product_count}},
  "subcategories": {{subcategories_array}},
  "top_products": [
    {
      "id": {{product_id}},
      "title": "{{product_title}}",
      "price": {{price}},
      "view_count": {{view_count}}
    }
  ]
}

## ANA ANAHTAR KELİME
"{{focus_keyword}}"

## İÇERİK TİPİ
{{content_type}} (guide | comparison | tutorial | faq)

## HEDEF
- Kullanıcıya {{category_name}} kategorisinde doğru ürün seçimi için rehberlik et
- Kategorideki ürün çeşitlerini açıkla
- Seçim kriterlerini listele
- Kullanım alanlarını detaylandır
- Fiyat-performans karşılaştırması yap

## YAPILANDIRMA

### 1. Giriş: Kategori Tanıtımı
- {{category_name}} nedir?
- Hangi sektörlerde kullanılır?
- Neden önemlidir?

### 2. Kategoride Ürün Çeşitleri
Her ürün tipi için:
- Tanım
- Özellikler
- Avantajlar/Dezavantajlar
- Kullanım senaryoları
- Fiyat aralığı

### 3. Seçim Kriterleri
- Kapasite/Boyut
- Güç kaynağı (manuel/elektrikli)
- Kullanım ortamı
- Bütçe
- Bakım gereksinimleri

### 4. Karşılaştırma Tablosu
| Özellik | Model A | Model B | Model C |
|---------|---------|---------|---------|
| ...     | ...     | ...     | ...     |

### 5. Top 5 Ürün Önerileri
(Sistemdeki en popüler ürünleri kullan)

### 6. Uygulama Örnekleri (Case Studies)
Sektör bazlı kullanım senaryoları

### 7. SSS (10 adet)

### 8. Sonuç + CTA

## ÇIKTI FORMATI

Aynı JSON formatında (yukarıdaki gibi)

BAŞLA.
```

---

### 4. BLOG ÜRET Prompt (Keyword-Based/SEO-Focused)

```markdown
# GÖREV: Anahtar Kelime Odaklı SEO İçerik Üret

## ANAHTAR KELİME ANALİZİ
{
  "focus_keyword": "{{keyword}}",
  "search_intent": "{{intent}}", // informational, commercial, transactional
  "search_volume": {{volume}},
  "keyword_difficulty": {{difficulty}},
  "related_keywords": [{{related_list}}],
  "people_also_ask": [{{paa_questions}}],
  "serp_features": ["featured_snippet", "people_also_ask", "videos"]
}

## RAKİP ANALİZİ
Top 3 rakip URL'leri için:
- Kelime sayısı
- Başlık yapısı
- Eksik konular (content gap)

## HEDEF SERP FEATURESGarantili bir hedef belirleyemem, ancak en iyi şekilde optimize edebilirim:

1. **Featured Snippet:**
   - Soruya direkt cevap (40-60 kelime)
   - Tanım box formatında

2. **People Also Ask:**
   - İlgili 10 soruya detaylı cevaplar

3. **Top 3 Ranking:**
   - Rakiplerden daha kapsamlı
   - Daha iyi yapılandırılmış
   - Daha güncel bilgi

## İÇERİK STRATEJİSİ

### Search Intent'e Göre Yapı:

**Informational:**
- Tanım (What is)
- Nasıl çalışır? (How it works)
- Çeşitleri (Types)
- Kullanım alanları (Use cases)
- Avantajlar (Benefits)

**Commercial:**
- Ürün karşılaştırması
- Seçim kriterleri
- Fiyat aralıkları
- Marka önerileri
- İnceleme/Review

**Transactional:**
- Satın alma rehberi
- En iyi ürünler
- Fiyat karşılaştırması
- Nereden alınır?
- Teslimat/Garanti bilgileri

## ÖZELLİKLER

✅ Featured snippet için optimize edilmiş giriş
✅ People Also Ask sorularına cevaplar
✅ Long-tail keyword varyantları
✅ İlgili aramalar için alt başlıklar
✅ İç linkler (kategori/ürün sayfalarına)
✅ Dış linkler (sektör kaynakları)

## ÇIKTI FORMATI

JSON (aynı format)

## FEATURED SNIPPET OPTİMİZASYONU

Giriş paragrafında (ilk 100 kelime):
```html
<div class="featured-snippet-target">
  <p><strong>{{focus_keyword}}</strong>, {{kısa tanım (40-60 kelime)}}</p>
</div>
```

BAŞLA.
```

---

### 5. FAQ GENERATE Prompt (Özel)

```markdown
# GÖREV: Blog için SEO-Optimize SSS Üret

## GİRDİ
{
  "topic": "{{blog_topic}}",
  "focus_keyword": "{{keyword}}",
  "related_keywords": [{{keywords}}],
  "product_info": {{product_data}}, // opsiyonel
  "target_audience": "{{audience}}"
}

## HEDEF
Google'ın "People Also Ask" bölümünde çıkacak şekilde optimize edilmiş 10 adet soru-cevap üret.

## SORU TİPLERİ

1. **Tanım Soruları (2 adet)**
   - "{{keyword}} nedir?"
   - "{{keyword}} ne işe yarar?"

2. **Nasıl Soruları (2 adet)**
   - "{{keyword}} nasıl kullanılır?"
   - "{{keyword}} nasıl çalışır?"

3. **Karşılaştırma Soruları (2 adet)**
   - "A mı B mi daha iyi?"
   - "{{keyword}} ile {{alternatif}} arasındaki fark nedir?"

4. **Satın Alma Soruları (2 adet)**
   - "{{keyword}} fiyatları ne kadar?"
   - "En iyi {{keyword}} hangisi?"

5. **Teknik/Özellik Soruları (2 adet)**
   - "{{keyword}} özellikleri nelerdir?"
   - "{{keyword}} bakımı nasıl yapılır?"

## CEVAP KURALLARI

- Uzunluk: 50-100 kelime
- Direkt cevapla başla
- Detay ver ama kısa tut
- Anahtar kelimeyi cevaba dahil et
- Ek bilgi linklerinegönder (internal)

## ÇIKTI FORMATI

```json
{
  "faq": [
    {
      "question": "Transpalet ne kadar yük kaldırır?",
      "answer": "Standart manuel transpaletler genellikle 2000-2500 kg kapasitelidir. Özel üretim modellerde bu kapasite 5000 kg'a kadar çıkabilir. Elektrikli transpalet modelleri ise 1500-3000 kg arasında yük kaldırabilir. Kapasite seçimi, taşınacak paletin ağırlığına göre yapılmalıdır.",
      "category": "Teknik"
    }
  ],
  "schema_markup": {
    "@type": "FAQPage",
    "mainEntity": [...]
  }
}
```

BAŞLA.
```

---

### 6. SEO OPTIMIZE Prompt (Post-Processing)

```markdown
# GÖREV: Mevcut Blog İçeriğini SEO-Optimize Et

## GİRDİ
{
  "blog_content": "{{existing_html}}",
  "focus_keyword": "{{keyword}}",
  "target_word_count": 2500,
  "current_word_count": 1800,
  "seo_score": 65
}

## OPTİMİZASYON HEDEFLERİ

### 1. Kelime Sayısı Artırma
- Hedef: {{target_word_count}} kelime
- Mevcut: {{current_word_count}} kelime
- Eklenecek: {{target - current}} kelime

**Nereye ekle:**
- Mevcut bölümlere detay ekle
- Yeni alt başlıklar ekle
- Case study/örnek ekle
- İstatistik/veri ekle

### 2. Keyword Optimization
- Focus keyword density kontrol et (hedef: %1-1.5)
- LSI keywords ekle
- Long-tail variants ekle
- Başlıklarda keyword kullan

### 3. Yapı İyileştirme
- TOC (table of contents) ekle
- Jump links ekle
- Infographic placeholder ekle
- Video embed alanı ekle

### 4. Internal Linking
- 8-12 adet internal link ekle
- Anchor text optimize et
- İlgili sayfalara link

### 5. Schema Markup Zenginleştir
- HowTo schema (uygunsa)
- VideoObject schema (v2)
- Review schema (ürün varsa)

## ÇIKTI

Optimize edilmiş HTML + JSON metadata

BAŞLA.
```

---

## 🔧 PROMPT PARAMETRELERİ

### OpenAI API Ayarları

```json
{
  "model": "gpt-4-turbo",
  "temperature": 0.7,
  "max_tokens": 4096,
  "top_p": 0.9,
  "frequency_penalty": 0.3,
  "presence_penalty": 0.2,
  "response_format": { "type": "json_object" }
}
```

### Açıklamalar:

- **temperature: 0.7** → Dengeli yaratıcılık (0.5-0.8 arası ideal)
- **max_tokens: 4096** → Uzun içerik için yeterli
- **frequency_penalty: 0.3** → Tekrar eden kelime kullanımını azaltır
- **presence_penalty: 0.2** → Konu çeşitliliği artırır
- **response_format: json** → JSON çıktı zorunlu (GPT-4 Turbo özelliği)

---

## 📚 FEW-SHOT LEARNING ÖRNEKLERİ

Prompt'a eklenecek örnek blog snippeti:

```markdown
## ÖRNEK BAŞARILI BLOG (Referans)

### Başlık Örneği:
"Transpalet Nedir? ⚡ Çeşitleri, Özellikleri ve Fiyatları [2025 Rehberi]"

### Giriş Örneği:
"Transpalet, depolama ve lojistik operasyonlarının vazgeçilmez ekipmanıdır. Paletli yüklerin kısa mesafelerde taşınması için tasarlanan bu araçlar, işgücü verimliliğini artırır ve iş güvenliğini sağlar. Bu rehberde, transpaletin ne olduğunu, çeşitlerini, teknik özelliklerini ve satın alma kriterlerini detaylı bir şekilde inceleyeceğiz."

### H2 Başlık Yapısı:
1. Transpalet Nedir ve Nasıl Çalışır?
2. Transpalet Çeşitleri ve Modelleri
3. Manuel Transpalet Özellikleri ve Avantajları
4. Elektrikli Transpalet: Ne Zaman Tercih Edilmeli?
5. Transpalet Teknik Özellikleri ve Kapasite Seçimi
6. Transpalet Kullanım Alanları ve Uygulama Örnekleri
7. Transpalet Fiyatları ve Maliyet Analizi
8. Satın Alma Rehberi: Doğru Transpalet Nasıl Seçilir?

### SSS Örneği:
**Soru:** Transpalet ne kadar yük kaldırır?
**Cevap:** Standart manuel transpaletler 2000-2500 kg, özel modeller 5000 kg'a kadar yük kaldırabilir.
```

---

**Son Güncelleme:** 2025-11-14
**Versiyon:** 1.0-PROMPTS
