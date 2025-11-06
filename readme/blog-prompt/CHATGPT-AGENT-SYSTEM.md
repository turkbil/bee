# 🤖 CHATGPT AGENT SİSTEMİ - BLOG OTOMASYON

> **Endüstriyel ürün satışı için tam otomatik blog üretim sistemi**

---

## 📋 İÇİNDEKİLER

1. [ChatGPT İçin Ana Prompt](#chatgpt-için-ana-prompt)
2. [Dosya Yükleme Workflow](#dosya-yükleme-workflow)
3. [SQL Yapısı ve Örnekler](#sql-yapısı-ve-örnekler)
4. [Agent Otomasyon Akışı](#agent-otomasyon-akışı)
5. [HTML/Tailwind Şablonları](#htmltailwind-şablonları)
6. [Kullanım Talimatları](#kullanım-talimatları)

---

## 🎯 CHATGPT İÇİN ANA PROMPT

### Ana Prompt (Kopyala-Yapıştır)

```markdown
Sen endüstriyel ürün satışı için SEO-optimizasyonlu blog yazıları üreten bir AI Agent'sın.

GÖREV: Sana verilen anahtar kelime ve yardımcı dosyalar ile tam otomatik blog üret.

ÇIKTILAR:
1. Blog taslağı (JSON format)
2. Blog içeriği (HTML + Tailwind)
3. SEO ayarları (JSON format)
4. SQL INSERT komutları

HEDEF:
- 2000-2500 kelime Türkçe içerik
- Schema.org yapılandırması
- FontAwesome ikonları ile görsel alanlar
- Tailwind CSS ile responsive tasarım
- SEO skoru 80+ hedefi

⚠️ KRİTİK SQL KURALI:
- JSON alanları için MUTLAKA JSON_OBJECT() fonksiyonu kullan
- Örnek: JSON_OBJECT('tr', 'Başlık metni')
- Manuel JSON string kullanma (validation hatası verir!)

ŞİMDİ BEKLENTİLER:
1. Anahtar kelimeyi sor
2. Dosyaları bekle (opsiyonel)
3. Çıktıları üret (JSON_OBJECT ile!)

BAŞLA.
```

---

## 📁 DOSYA YÜKLEME WORKFLOW

### Yükleme Sırası ve Açıklamaları

#### 1️⃣ **İlk Yükleme: Taslak Promptu**
**Dosya:** `1-blog-taslak-olusturma.md`
```
ChatGPT'ye de:
"Bu dosya blog taslağı oluşturma kurallarını içeriyor.
İnceleyip anahtar kelime için taslak oluştur."
```

#### 2️⃣ **İkinci Yükleme: İçerik Yazma Promptu**
**Dosya:** `2-blog-yazdirma.md`
```
ChatGPT'ye de:
"Bu dosya içerik yazma kurallarını içeriyor.
Taslağa göre detaylı içerik üret."
```

#### 3️⃣ **Üçüncü Yükleme: SEO Kontrol Listesi**
**Dosya:** `3-schema-seo-checklist.md`
```
ChatGPT'ye de:
"Bu dosya SEO kontrol listesi.
İçeriği bu kurallara göre optimize et."
```

#### 4️⃣ **Opsiyonel: Rakip İçerik Analizi**
```
ChatGPT'ye de:
"Rakip URL: [URL]
Bu içeriği analiz et ve daha iyisini yaz."
```

---

## 🗄️ SQL YAPISI VE ÖRNEKLER

### Blog Tablosu Yapısı

```sql
-- blogs tablosu
CREATE TABLE blogs (
    blog_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    blog_category_id BIGINT NULL,
    title JSON NOT NULL COMMENT '{"tr": "Başlık", "en": "Title"}',
    slug JSON NOT NULL COMMENT '{"tr": "baslik", "en": "title"}',
    body JSON NULL COMMENT '{"tr": "İçerik HTML", "en": "Content HTML"}',
    excerpt JSON NULL COMMENT '{"tr": "Özet", "en": "Excerpt"}',
    published_at TIMESTAMP NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('draft', 'published', 'scheduled') DEFAULT 'draft',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

### SEO Settings Tablosu Yapısı

```sql
-- seo_settings tablosu
CREATE TABLE seo_settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    seoable_type VARCHAR(255) NOT NULL,
    seoable_id BIGINT NOT NULL,
    titles JSON NULL COMMENT '{"tr": "SEO Title"}',
    descriptions JSON NULL COMMENT '{"tr": "SEO Description"}',
    og_titles JSON NULL COMMENT '{"tr": "OG Title"}',
    og_descriptions JSON NULL COMMENT '{"tr": "OG Description"}',
    og_images JSON NULL COMMENT '{"tr": "image-url.jpg"}',
    og_type VARCHAR(50) DEFAULT 'article',
    robots_meta JSON DEFAULT '{"index": true, "follow": true}',
    schema_type JSON NULL COMMENT '{"tr": "Article", "en": "BlogPosting"}',
    priority_score INT DEFAULT 5,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 📝 HAZIR SQL INSERT ÖRNEĞİ

### Transpalet Blog Örneği

```sql
-- ⚠️ ÖNEMLİ: JSON_OBJECT() fonksiyonu kullan (JSON validation hatası önlenir)

-- Blog kaydı ekle
INSERT INTO blogs (
    blog_category_id,
    title,
    slug,
    body,
    excerpt,
    published_at,
    is_featured,
    status,
    is_active
) VALUES (
    1, -- Kategori ID (Endüstriyel Ekipman)
    JSON_OBJECT('tr', 'Transpalet Nedir? Çeşitleri ve Kullanım Alanları [2025 Rehberi]'),
    JSON_OBJECT('tr', 'transpalet-nedir-cesitleri-kullanim-alanlari'),
    JSON_OBJECT('tr', '<!-- Blog HTML İçeriği Aşağıda -->'), -- HTML içerik
    JSON_OBJECT('tr', 'Transpalet, depo ve lojistik operasyonlarında palet taşıma işlemlerini kolaylaştıran endüstriyel ekipmandır. Manuel ve elektrikli modelleri ile 2-3 ton yük taşıma kapasitesine sahiptir.'),
    NOW(),
    1, -- Öne çıkan
    'published',
    1
);

-- Son eklenen blog ID'sini al
SET @last_blog_id = LAST_INSERT_ID();

-- SEO ayarlarını ekle
INSERT INTO seo_settings (
    seoable_type,
    seoable_id,
    titles,
    descriptions,
    og_titles,
    og_descriptions,
    og_images,
    og_type,
    robots_meta,
    schema_type,
    priority_score,
    status
) VALUES (
    'Modules\\Blog\\App\\Models\\Blog',
    @last_blog_id,
    JSON_OBJECT('tr', 'Transpalet Nedir? ⚡ Çeşitleri ve Fiyatları 2025'),
    JSON_OBJECT('tr', 'Transpalet nedir, nasıl kullanılır? ✅ Manuel ve elektrikli transpalet çeşitleri ✅ 2-3 ton kapasite ✅ En uygun fiyatlar ➤ Hemen inceleyin!'),
    JSON_OBJECT('tr', 'Transpalet Rehberi: Manuel ve Elektrikli Modeller'),
    JSON_OBJECT('tr', 'Depo ekipmanlarının vazgeçilmezi transpalet hakkında bilmeniz gereken her şey. Çeşitleri, özellikleri ve fiyat karşılaştırması.'),
    JSON_OBJECT('tr', '/uploads/blog/transpalet-rehber.jpg'),
    'article',
    JSON_OBJECT('index', true, 'follow', true, 'max-snippet', -1, 'max-image-preview', 'large'),
    JSON_OBJECT('tr', 'Article'),
    8,
    'active'
);
```

---

## 🎨 HTML/TAILWIND ŞABLONU

### Blog İçeriği HTML Yapısı

```html
<!-- Hero Section with Icon -->
<section class="py-8 md:py-12">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Sol İçerik -->
            <div>
                <h1 class="text-3xl md:text-5xl font-black mb-6 text-gray-900 dark:text-white">
                    Transpalet Nedir?
                    <span class="block text-2xl md:text-3xl text-blue-600 dark:text-blue-400 mt-2">
                        Endüstriyel Taşıma Çözümleri
                    </span>
                </h1>

                <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed mb-8">
                    Transpalet, depo ve lojistik operasyonlarında paletli yüklerin
                    taşınması için kullanılan temel endüstriyel ekipmandır.
                </p>

                <!-- Özellik Listesi -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center">
                            <i class="fa-light fa-weight text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white">2-3 Ton</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Kapasite</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center">
                            <i class="fa-light fa-ruler text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white">80-200cm</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Çatal Boyu</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ İkon Alanı -->
            <div class="flex justify-center lg:justify-end">
                <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-12 border-8 border-gray-200 dark:border-gray-700">
                    <div class="relative bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl p-16 aspect-square flex items-center justify-center">
                        <!-- Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-full blur-2xl"></div>
                        <!-- Ana İkon -->
                        <i class="fa-light fa-pallet relative text-blue-600 dark:text-blue-400" style="font-size: 8rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- İçerik Bölümleri -->
<section class="py-6 md:py-10">
    <div class="container mx-auto px-4">
        <article class="prose prose-lg max-w-none dark:prose-invert">

            <!-- Transpalet Tanımı -->
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                <i class="fa-light fa-circle-info text-blue-600 mr-2"></i>
                Transpalet Nedir?
            </h2>

            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-6">
                Transpalet, depolarda ve fabrikalarda paletli malzemelerin taşınması için kullanılan
                hidrolik veya elektrikli tahrikli endüstriyel ekipmandır. Manuel pompalama veya
                elektrik motoru ile çalışan çatal kaldırma mekanizması sayesinde, ağır yükleri
                minimum eforla taşımayı sağlar.
            </p>

            <!-- Özellikler Tablosu -->
            <div class="overflow-x-auto mb-8">
                <table class="w-full border-collapse bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-lg">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="p-4 text-left">Özellik</th>
                            <th class="p-4 text-left">Manuel</th>
                            <th class="p-4 text-left">Elektrikli</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b dark:border-gray-700">
                            <td class="p-4 font-semibold">Kapasite</td>
                            <td class="p-4">2000-3000 kg</td>
                            <td class="p-4">1500-3000 kg</td>
                        </tr>
                        <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <td class="p-4 font-semibold">Çatal Uzunluğu</td>
                            <td class="p-4">800-2000 mm</td>
                            <td class="p-4">1000-2400 mm</td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <td class="p-4 font-semibold">Kaldırma Yüksekliği</td>
                            <td class="p-4">85-200 mm</td>
                            <td class="p-4">85-200 mm</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Transpalet Çeşitleri -->
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6 mt-12">
                <i class="fa-light fa-layer-group text-blue-600 mr-2"></i>
                Transpalet Çeşitleri
            </h2>

            <!-- Çeşit Kartları -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Manuel Transpalet -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                        <i class="fa-light fa-hand text-blue-600 dark:text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Manuel Transpalet</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Hidrolik pompa sistemi ile çalışan, elektrik gerektirmeyen ekonomik model.
                    </p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 dark:text-green-400 mt-0.5"></i>
                            <span>Düşük başlangıç maliyeti</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 dark:text-green-400 mt-0.5"></i>
                            <span>Bakım gereksinimleri minimal</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 dark:text-green-400 mt-0.5"></i>
                            <span>Her zeminde kullanım</span>
                        </li>
                    </ul>
                </div>

                <!-- Elektrikli Transpalet -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                        <i class="fa-light fa-bolt text-blue-600 dark:text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Elektrikli Transpalet</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Elektrik motoru ile tahrik edilen, uzun mesafe taşımaya uygun model.
                    </p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 dark:text-green-400 mt-0.5"></i>
                            <span>Operatör yorgunluğu minimum</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 dark:text-green-400 mt-0.5"></i>
                            <span>Yüksek verimlilik</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 dark:text-green-400 mt-0.5"></i>
                            <span>Uzun mesafe taşıma</span>
                        </li>
                    </ul>
                </div>

                <!-- Paslanmaz Transpalet -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                        <i class="fa-light fa-shield text-blue-600 dark:text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Paslanmaz Transpalet</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Gıda ve ilaç sektörü için hijyenik, korozyona dayanıklı özel model.
                    </p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 dark:text-green-400 mt-0.5"></i>
                            <span>HACCP uyumlu</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 dark:text-green-400 mt-0.5"></i>
                            <span>Kolay temizlenebilir</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 dark:text-green-400 mt-0.5"></i>
                            <span>Uzun ömürlü</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Kullanım Alanları -->
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6 mt-12">
                <i class="fa-light fa-industry text-blue-600 mr-2"></i>
                Kullanım Alanları
            </h2>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3">Endüstriyel Tesisler</h4>
                    <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                        <li>• Üretim hatları arası taşıma</li>
                        <li>• Hammadde ve mamul transferi</li>
                        <li>• Sevkiyat alanı operasyonları</li>
                        <li>• Kalite kontrol bölgesi</li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3">Lojistik Merkezleri</h4>
                    <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                        <li>• Kamyon yükleme/boşaltma</li>
                        <li>• Cross-dock operasyonları</li>
                        <li>• Depo içi transfer</li>
                        <li>• Sipariş hazırlama</li>
                    </ul>
                </div>
            </div>

            <!-- SSS Bölümü -->
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6 mt-12">
                <i class="fa-light fa-circle-question text-blue-600 mr-2"></i>
                Sıkça Sorulan Sorular
            </h2>

            <div class="space-y-4 mb-8">
                <!-- Soru 1 -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">
                        Transpalet ne kadar yük kaldırır?
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300">
                        Standart manuel transpaletler genellikle 2000-2500 kg kapasitelidir. Özel üretim modellerde bu kapasite 5000 kg'a kadar çıkabilir. Elektrikli transpalet modelleri ise 1500-3000 kg arasında yük kaldırabilir.
                    </p>
                </div>

                <!-- Soru 2 -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">
                        Manuel mi elektrikli transpalet mi tercih edilmeli?
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300">
                        Günlük kullanım sıklığı düşük ve kısa mesafe taşımalarda manuel transpalet ekonomik çözümdür. Yoğun kullanım, uzun mesafe taşıma ve operatör konforu öncelikli ise elektrikli transpalet tercih edilmelidir.
                    </p>
                </div>

                <!-- Soru 3 -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">
                        Transpalet bakımı nasıl yapılır?
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300">
                        Günlük kontroller: Hidrolik yağ seviyesi, çatal hasarı, tekerlek durumu. Haftalık: Yağlama noktaları, cıvata sıkılıkları. Yıllık: Profesyonel servis bakımı, yağ değişimi, komple revizyon önerilir.
                    </p>
                </div>

                <!-- Soru 4 -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">
                        Transpalet çatal uzunluğu nasıl seçilir?
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300">
                        Standart Euro palet (1200x800mm) için 1150mm çatal uzunluğu idealdir. Amerikan paleti (1200x1000mm) için 1200mm, özel paletler için palet boyutundan 50mm kısa çatal uzunluğu seçilmelidir.
                    </p>
                </div>

                <!-- Soru 5 -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">
                        İkinci el transpalet alınır mı?
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300">
                        Düşük bütçe ve hafif kullanım için ikinci el değerlendirilebilir. Ancak garanti, yedek parça ve güvenlik açısından sıfır transpalet önerilir. İkinci elde hidrolik sistem, çatal durumu ve tekerlek aşınması mutlaka kontrol edilmelidir.
                    </p>
                </div>
            </div>

            <!-- CTA Bölümü -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-center mt-12">
                <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">
                    Transpalet İhtiyacınız mı Var?
                </h3>
                <p class="text-white/90 text-lg mb-6">
                    2 yıl garanti, ücretsiz kurulum ve operatör eğitimi ile yanınızdayız!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/iletisim" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                        <i class="fa-light fa-phone mr-2"></i>
                        Hemen Arayın
                    </a>
                    <a href="/urunler/transpalet" class="inline-block bg-blue-700 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-800 transition">
                        <i class="fa-light fa-shopping-cart mr-2"></i>
                        Ürünleri İncele
                    </a>
                </div>
            </div>

        </article>
    </div>
</section>
```

---

## 📊 JSON-LD SCHEMA MARKUP

### Article + FAQPage Schema Örneği

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Article",
      "headline": "Transpalet Nedir? Çeşitleri ve Kullanım Alanları [2025]",
      "description": "Transpalet hakkında merak edilen her şey. Manuel ve elektrikli transpalet çeşitleri, özellikleri, kullanım alanları ve fiyat karşılaştırması.",
      "image": "https://domain.com/uploads/blog/transpalet-rehber.jpg",
      "datePublished": "2025-11-06T08:00:00+03:00",
      "dateModified": "2025-11-06T10:00:00+03:00",
      "author": {
        "@type": "Organization",
        "name": "İxtif Endüstriyel",
        "url": "https://ixtif.com"
      },
      "publisher": {
        "@type": "Organization",
        "name": "İxtif Endüstriyel",
        "logo": {
          "@type": "ImageObject",
          "url": "https://ixtif.com/logo.png"
        }
      },
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "https://ixtif.com/blog/transpalet-nedir"
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Transpalet ne kadar yük kaldırır?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Standart manuel transpaletler genellikle 2000-2500 kg kapasitelidir. Özel üretim modellerde bu kapasite 5000 kg'a kadar çıkabilir."
          }
        },
        {
          "@type": "Question",
          "name": "Manuel mi elektrikli transpalet mi tercih edilmeli?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Günlük kullanım sıklığı düşük ve kısa mesafe taşımalarda manuel transpalet ekonomik çözümdür. Yoğun kullanım için elektrikli transpalet önerilir."
          }
        }
      ]
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Ana Sayfa",
          "item": "https://ixtif.com/"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Blog",
          "item": "https://ixtif.com/blog"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "Transpalet Nedir?",
          "item": "https://ixtif.com/blog/transpalet-nedir"
        }
      ]
    }
  ]
}
```

---

## 🚀 AGENT OTOMASYON AKIŞI

### 1. ChatGPT Agent Workflow

```yaml
ADIM 1: Anahtar Kelime Al
  - Kullanıcıdan anahtar kelime iste
  - Destek kelimeleri sor (opsiyonel)
  - Hedef kitle bilgisi al (B2B/B2C)

ADIM 2: Taslak Oluştur
  - H1/H2/H3 başlık yapısı
  - 10 adet SSS sorusu
  - İçerik bölümleri planla
  - SEO meta bilgileri

ADIM 3: İçerik Üret
  - 2000-2500 kelime HTML içerik
  - Tailwind CSS sınıfları
  - FontAwesome ikonları
  - Responsive tablolar
  - Görsel alan yerleşimleri

ADIM 4: SEO Optimize Et
  - Title: 50-60 karakter
  - Description: 155-160 karakter
  - Schema markup ekle
  - Internal linking önerileri
  - Keyword density: %1-2

ADIM 5: SQL Çıktısı Ver
  - Blog INSERT komutu
  - SEO settings INSERT komutu
  - Kategori ilişkilendirmesi
  - Tag atamaları
```

### 2. Veritabanı İşlemleri

```php
// Laravel Tinker komutları

// Blog ekleme
$blog = new \Modules\Blog\App\Models\Blog;
$blog->blog_category_id = 1;
$blog->title = ['tr' => 'Transpalet Nedir? Çeşitleri ve Kullanım Alanları'];
$blog->slug = ['tr' => 'transpalet-nedir-cesitleri-kullanim-alanlari'];
$blog->body = ['tr' => '<!-- HTML içerik -->'];
$blog->excerpt = ['tr' => 'Özet metin...'];
$blog->published_at = now();
$blog->is_featured = true;
$blog->status = 'published';
$blog->is_active = true;
$blog->save();

// SEO ayarları
$seo = new \Modules\SeoManagement\App\Models\SeoSetting;
$seo->seoable_type = 'Modules\\Blog\\App\\Models\\Blog';
$seo->seoable_id = $blog->blog_id;
$seo->titles = ['tr' => 'SEO Title'];
$seo->descriptions = ['tr' => 'SEO Description'];
$seo->og_titles = ['tr' => 'OG Title'];
$seo->og_descriptions = ['tr' => 'OG Description'];
$seo->schema_type = ['tr' => 'Article'];
$seo->priority_score = 8;
$seo->save();

// Tag ekleme
$blog->syncTagsByName(['transpalet', 'manuel-transpalet', 'elektrikli-transpalet']);
```

---

## 📚 KULLANIM TALİMATLARI

### Adım Adım Kullanım

#### 1️⃣ **ChatGPT'ye Giriş**
1. ChatGPT-4 veya üzeri modeli seç
2. Ana promptu yapıştır
3. Dosyaları yükle (sırasıyla)

#### 2️⃣ **Anahtar Kelime Girişi**
```
Örnek:
- Ana kelime: "transpalet nedir"
- Destek: "manuel transpalet, elektrikli transpalet, transpalet fiyatları"
- Hedef: B2B endüstriyel firmalar
```

#### 3️⃣ **Çıktıları Alma**
ChatGPT şu formatta verecek:
1. **blog_output.json** - Blog verisi
2. **seo_output.json** - SEO ayarları
3. **sql_commands.sql** - Veritabanı komutları
4. **html_content.html** - Tam HTML içerik

#### 4️⃣ **Veritabanına Ekleme**
```bash
# SQL dosyasını çalıştır
mysql -u root tenant_ixtif < sql_commands.sql

# Veya Laravel Tinker ile
php artisan tinker
# Ardından PHP kodlarını yapıştır
```

#### 5️⃣ **Kontrol ve Yayınlama**
```bash
# Cache temizle
php artisan cache:clear
php artisan view:clear

# Blog kontrolü
curl -s https://ixtif.com/blog/[slug]
```

---

## 🎯 BAŞARI KRİTERLERİ

### SEO Skor Hedefleri

| Metrik | Hedef | Kontrol |
|--------|-------|---------|
| İçerik Uzunluğu | 2000-2500 kelime | ✅ |
| Keyword Density | %1-2 | ✅ |
| Title Uzunluğu | 50-60 karakter | ✅ |
| Description | 155-160 karakter | ✅ |
| Schema Markup | Article + FAQ | ✅ |
| Internal Links | 5-10 adet | ✅ |
| Görsel/İkon | 10+ adet | ✅ |
| H2/H3 Başlık | 8-12 adet | ✅ |
| SSS | 5-10 soru | ✅ |
| CTA | 2-3 adet | ✅ |

---

## 🔧 SORUN GİDERME

### Sık Karşılaşılan Sorunlar

**1. ChatGPT çıktı vermiyor**
- Model GPT-4 olmalı
- Token limiti aşılmış olabilir
- Promptu parçalara böl

**2. SQL hata veriyor**
- JSON escape karakterlerine dikkat
- blog_category_id kontrolü
- Tenant database doğru mu?

**3. HTML düzgün görünmüyor**
- Tailwind CSS yüklü mü?
- FontAwesome CDN ekli mi?
- Dark mode classları kontrol et

---

## 📞 DESTEK

**Dosya Konumu:** `/Users/nurullah/Desktop/cms/laravel/readme/blog-prompt/`
**Son Güncelleme:** 6 Kasım 2025
**Platform:** Laravel Multi-tenant E-commerce
**Target Tenant:** ixtif.com (ID: 2)

---

*Bu döküman ChatGPT agent sisteminin eksiksiz kullanım kılavuzudur.*