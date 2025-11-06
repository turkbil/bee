# 🚀 AGENT WORKFLOW GUIDE - ADIM ADIM KULLANIM

> **ChatGPT Agent ile Tam Otomatik Blog Üretim Süreci**

---

## 📌 HIZLI BAŞLANGIÇ

### 30 Saniyede Blog Üretimi

```bash
1. ChatGPT-4'e gir
2. Ana promptu yapıştır
3. Anahtar kelime ver: "transpalet nedir"
4. SQL çıktısını al
5. Veritabanına ekle
✅ Blog yayında!
```

---

## 🎯 DETAYLI WORKFLOW

### AŞAMA 1: ChatGPT Hazırlık

#### 1.1 Model Seçimi
```
✅ GPT-4 veya GPT-4 Turbo
❌ GPT-3.5 (Yetersiz)
```

#### 1.2 Ana Prompt Yükleme
```markdown
# Bu promptu ChatGPT'ye yapıştır:

Sen endüstriyel ürün satışı için SEO-optimizasyonlu blog yazıları üreten bir AI Agent'sın.

GÖREV:
- 2000-2500 kelime Türkçe blog
- SQL INSERT komutları üret
- FontAwesome ikonları kullan
- Tailwind CSS ile kodla

ŞİMDİ: Anahtar kelimeyi sor ve başla.
```

#### 1.3 Dosya Yükleme (Opsiyonel)
```
1. Dosya ikonuna tıkla
2. Şu sırayla yükle:
   - 1-blog-taslak-olusturma.md
   - 2-blog-yazdirma.md
   - 3-schema-seo-checklist.md
```

---

### AŞAMA 2: İçerik Üretimi

#### 2.1 Anahtar Kelime Girişi
```yaml
ChatGPT: "Anahtar kelimeyi girin:"
Sen:
  Ana kelime: "transpalet nedir"
  Destek: "manuel transpalet, elektrikli transpalet"
  Hedef: "B2B depo yöneticileri"
  Sektör: "Endüstriyel ekipman"
```

#### 2.2 ChatGPT Çıktıları
```
ChatGPT şunları verecek:
1. Blog taslağı (H1/H2/H3 yapısı)
2. HTML içerik (2000+ kelime)
3. SQL komutları (blog + seo_settings)
4. Schema.org JSON-LD
```

---

### AŞAMA 3: Veritabanı İşlemleri

#### 3.1 SQL Dosyasını Kaydet
```bash
# ChatGPT'den aldığın SQL'i kaydet
nano /tmp/blog-insert.sql
# SQL'i yapıştır
# CTRL+X, Y, Enter
```

#### 3.2 MySQL'e Ekle
```bash
# Direkt MySQL ile
mysql -u root tenant_ixtif < /tmp/blog-insert.sql

# Veya manuel
mysql -u root
USE tenant_ixtif;
# SQL komutlarını yapıştır
```

#### 3.3 Laravel Tinker Alternatifi
```php
php artisan tinker

// Blog ekle
$blog = new \Modules\Blog\App\Models\Blog;
$blog->title = ['tr' => 'Transpalet Nedir?'];
$blog->slug = ['tr' => 'transpalet-nedir'];
$blog->body = ['tr' => '<!-- HTML içerik -->'];
$blog->blog_category_id = 1;
$blog->published_at = now();
$blog->is_featured = true;
$blog->status = 'published';
$blog->is_active = true;
$blog->save();

// SEO ekle
$seo = new \Modules\SeoManagement\App\Models\SeoSetting;
$seo->seoable_type = 'Modules\\Blog\\App\\Models\\Blog';
$seo->seoable_id = $blog->blog_id;
$seo->titles = ['tr' => 'SEO Title'];
$seo->descriptions = ['tr' => 'SEO Description'];
$seo->schema_type = ['tr' => 'Article'];
$seo->priority_score = 8;
$seo->save();
```

---

### AŞAMA 4: Kontrol ve Yayınlama

#### 4.1 Cache Temizleme
```bash
php artisan cache:clear
php artisan view:clear
php artisan responsecache:clear
```

#### 4.2 Blog Kontrolü
```bash
# Blog listesini kontrol et
curl -s https://ixtif.com/blog | grep "transpalet"

# Direkt blog sayfasını kontrol et
curl -I https://ixtif.com/blog/transpalet-nedir

# HTTP 200 dönmeli
```

#### 4.3 SEO Kontrolü
```bash
# Meta tagları kontrol et
curl -s https://ixtif.com/blog/transpalet-nedir | grep -E "<title>|<meta"

# Schema markup kontrolü
curl -s https://ixtif.com/blog/transpalet-nedir | grep "@context"
```

---

## 🎨 İÇERİK ÖZELLEŞTİRME

### İkon Kullanımı

#### FontAwesome İkon Örnekleri
```html
<!-- Başlıklarda -->
<h2><i class="fa-light fa-pallet mr-2"></i>Transpalet Çeşitleri</h2>

<!-- Özelliklerde -->
<div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
  <i class="fa-light fa-weight text-blue-600"></i>
</div>

<!-- Listlerde -->
<i class="fa-solid fa-check text-green-600"></i> Avantaj

<!-- Hero İkon -->
<i class="fa-light fa-pallet text-blue-600" style="font-size: 8rem;"></i>
```

#### Sık Kullanılan İkonlar
```
Transpalet: fa-pallet
Forklift: fa-forklift
Ağırlık: fa-weight
Yükseklik: fa-ruler
Elektrik: fa-bolt
Yakıt: fa-gas-pump
Güvenlik: fa-shield
Eğitim: fa-graduation-cap
Bakım: fa-tools
Sertifika: fa-certificate
```

### Renk Paleti

#### Tailwind Renkleri
```css
/* Light Mode */
Başlıklar: text-gray-900
Metin: text-gray-700
Alt metin: text-gray-600
Arka plan: bg-white
Vurgu: bg-blue-50

/* Dark Mode */
Başlıklar: dark:text-white
Metin: dark:text-gray-300
Alt metin: dark:text-gray-400
Arka plan: dark:bg-gray-800
Vurgu: dark:bg-slate-700/50
```

### Responsive Kurallar

```html
<!-- Mobil öncelikli -->
<h1 class="text-2xl md:text-4xl lg:text-5xl">Başlık</h1>
<p class="text-base md:text-lg lg:text-xl">Metin</p>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
```

---

## 📊 PERFORMANS İPUÇLARI

### ChatGPT Optimizasyonu

#### Token Tasarrufu
```
✅ Kısa ve net promptlar
✅ Gereksiz açıklama isteme
✅ Direkt SQL çıktısı iste
❌ Uzun açıklamalar
❌ Adım adım anlatım
```

#### Hızlı Üretim
```
1. Hazır prompt şablonları kullan
2. Anahtar kelimeleri önceden belirle
3. Kategori ID'lerini bil
4. SQL şablonunu hazır tut
```

### Veritabanı Optimizasyonu

#### Index Kullanımı
```sql
-- Blog aramaları için
INDEX idx_blog_slug (slug);
INDEX idx_blog_status (status, is_active);
INDEX idx_blog_published (published_at);

-- SEO için
INDEX idx_seo_seoable (seoable_type, seoable_id);
INDEX idx_seo_priority (priority_score);
```

#### Cache Stratejisi
```php
// Blog cache
Cache::remember("blog_{$slug}", 3600, function() {
    return Blog::where('slug', $slug)->first();
});

// SEO cache
Cache::tags(['seo'])->remember("seo_{$id}", 3600, function() {
    return SeoSetting::find($id);
});
```

---

## 🔧 SORUN GİDERME

### Sık Karşılaşılan Hatalar

#### 1. JSON Validation Hatası (blogs_chk_3)
```sql
-- ❌ YANLIŞ (Manuel JSON string - validation hatası verir!)
'{"tr": "Transpalet Nedir?"}'

-- ✅ DOĞRU (JSON_OBJECT fonksiyonu kullan)
JSON_OBJECT('tr', 'Transpalet Nedir?')

-- Neden? Blog tablosunda json_valid() constraint var
-- Manuel JSON yazarken escape karakterleri constraint'i bozar
```

#### 2. JSON Escape Hatası
```sql
-- ❌ YANLIŞ
'{"tr": "Blog'un başlığı"}'

-- ✅ DOĞRU (JSON_OBJECT kullan - en güvenli)
JSON_OBJECT('tr', 'Blog''un başlığı')

-- veya manuel düzeltme (önerilmez)
'{"tr": "Blog\\'un başlığı"}'
```

#### 3. Kategori ID Hatası
```bash
# Önce kategorileri kontrol et
mysql -u root -e "USE tenant_ixtif; SELECT * FROM blog_categories;"

# Yoksa kategori ekle
mysql -u root -e "USE tenant_ixtif;
INSERT INTO blog_categories (name, slug, is_active)
VALUES ('{\"tr\": \"Endüstriyel\"}', '{\"tr\": \"endustriyel\"}', 1);"
```

#### 4. Tenant Database Hatası
```bash
# Doğru tenant'ta mısın?
mysql -u root -e "SHOW DATABASES LIKE 'tenant_%';"

# Doğru database'i seç
USE tenant_ixtif;  # ixtif.com için
USE tenant_tuufi;  # tuufi.com için
```

#### 5. Cache Sorunu
```bash
# Nuclear cache clear
php artisan cache:clear && \
php artisan config:clear && \
php artisan route:clear && \
php artisan view:clear && \
find storage/framework/views -name "*.php" -delete
```

---

## 📈 KALİTE KONTROL

### SEO Skor Kontrolü (Hedef: 80+)

```yaml
✅ Title: 50-60 karakter
✅ Description: 155-160 karakter
✅ H1: 1 adet, anahtar kelime içermeli
✅ H2: 4-6 adet
✅ İçerik: 2000+ kelime
✅ Keyword Density: %1-2
✅ Schema Markup: Article + FAQ
✅ Internal Links: 5-10 adet
✅ Görsel Alt Text: Tüm görsellerde
✅ Mobile Friendly: Responsive
```

### İçerik Kalite Kontrolü

```yaml
✅ Giriş paragrafı: 100-150 kelime
✅ Tanım bölümü: Net ve özlü
✅ Özellikler: Tablo veya liste
✅ Karşılaştırma: Avantaj/Dezavantaj
✅ SSS: 5-10 soru
✅ CTA: 2-3 adet
✅ Kaynak: 2-3 otorite link
```

---

## 🎯 BAŞARI METRİKLERİ

### Hedefler

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Üretim Süresi | <10 dakika | ChatGPT + SQL |
| SEO Skoru | 80+ | Yoast/RankMath |
| İçerik Uzunluğu | 2000+ kelime | Word count |
| Schema Markup | %100 | Rich Results Test |
| Mobile Uyum | %100 | PageSpeed |
| Yayınlama | <15 dakika | Toplam süre |

---

## 💡 PRO İPUÇLARI

### Toplu Üretim

```bash
# 10 anahtar kelime listesi hazırla
nano keywords.txt
transpalet nedir
forklift kiralama
reach truck özellikleri
...

# ChatGPT'ye toplu ver
"Bu 10 anahtar kelime için SQL üret"

# Tek seferde ekle
mysql -u root tenant_ixtif < all-blogs.sql
```

### Otomatik Yayınlama

```php
// Cron job ile zamanla
$blogs = Blog::where('status', 'scheduled')
             ->where('published_at', '<=', now())
             ->update(['status' => 'published']);
```

### A/B Testing

```php
// Farklı başlıkları test et
$variants = [
    'a' => 'Transpalet Nedir?',
    'b' => 'Transpalet Rehberi 2025'
];

// CTR ölç ve optimize et
```

---

## 📞 DESTEK & KAYNAKLAR

### Dosya Konumları
```
/Users/nurullah/Desktop/cms/laravel/readme/blog-prompt/
├── CHATGPT-AGENT-SYSTEM.md    # Ana sistem dökümanı
├── AGENT-WORKFLOW-GUIDE.md    # Bu dosya
├── SQL-EXAMPLES.sql           # Hazır SQL örnekleri
├── BLOG-YAZDIRMA-AKISI.md    # Hızlı workflow
├── 1-blog-taslak-olusturma.md # Taslak promptu
├── 2-blog-yazdirma.md        # İçerik promptu
└── 3-schema-seo-checklist.md  # SEO kontrol
```

### Hızlı Komutlar
```bash
# Blog listesi
mysql -u root -e "USE tenant_ixtif; SELECT blog_id, title FROM blogs ORDER BY blog_id DESC LIMIT 10;"

# SEO kontrol
mysql -u root -e "USE tenant_ixtif; SELECT * FROM seo_settings WHERE seoable_type LIKE '%Blog%' ORDER BY id DESC LIMIT 5;"

# Cache temizle
php artisan cache:clear && php artisan view:clear

# Blog URL test
curl -I https://ixtif.com/blog/[slug]
```

---

**✨ İpucu:** Bu workflow'u takip ederek 10 dakikada profesyonel, SEO-optimize blog yayınlayabilirsiniz!

---

*Son Güncelleme: 6 Kasım 2025*
*Platform: Laravel Multi-tenant*
*Hedef: ixtif.com*