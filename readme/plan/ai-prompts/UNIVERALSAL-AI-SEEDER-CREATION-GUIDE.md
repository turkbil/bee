# 🚀 AI FEATURE SEEDER OLUŞTURMA KILAVUZU

**Versiyon:** 3.0 - Universal Input System V3  
**Tarih:** 10.08.2025  
**Amaç:** Yeni AI feature seeder'larını nasıl oluşturacağını adım adım gösterir

---

## 📋 İÇİNDEKİLER
1. [Seeder Organizasyon Yapısı](#seeder-organizasyon-yapısı)
2. [Tablo İlişkileri ve Fonksiyonları](#tablo-ilişkileri-ve-fonksiyonları)
3. [Blog Writer Örneği - Adım Adım](#blog-writer-örneği---adım-adım)
4. [Seeder Template'i](#seeder-templatei)
5. [ID Yönetim Sistemi](#id-yönetim-sistemi)
6. [Dosya Organizasyonu](#dosya-organizasyonu)
7. [Kontrol Listesi](#kontrol-listesi)

---

## 📁 SEEDER ORGANİZASYON YAPISI

### **DOSYA HİYERARŞİSİ:**
```
Modules/AI/database/seeders/
├── 🔧 CORE (Herkesi İlgilendiren)
│   ├── AIFeatureCategoriesSeeder.php     # 18 kategori (tek seederda)
│   ├── ExpertPromptsSeeder.php           # Expert prompt library
│   └── AIModuleIntegrationsSeeder.php    # Modül entegrasyonları
│
├── 📝 FEATURES (Kategori Bazlı)
│   ├── ContentCategoryFeaturesSeeder.php    # İçerik kategorisi features
│   ├── SEOCategoryFeaturesSeeder.php        # SEO kategorisi features  
│   ├── TranslationCategoryFeaturesSeeder.php # Çeviri kategorisi features
│   └── ...
│
└── 🔗 RELATIONS (Her Feature Kendi Dosyasında)
    ├── BlogWriterRelationsSeeder.php       # Blog writer prompt ilişkileri
    ├── SEOAnalyzerRelationsSeeder.php      # SEO analyzer prompt ilişkileri
    └── ...
```

### **KURALLAR:**
- ✅ **Kategoriler**: Tek seederda (18 kategori)
- ✅ **Expert Prompt'lar**: Ayrı seeder, kategori altında
- ✅ **Feature'lar**: Kategori bazlı seeder'larda
- ✅ **Relations**: Her feature'ın kendi dosyası
- ✅ **Type Prefix'ler**: EP001 (Expert), FT201 (Feature), vs.

---

## 🗂️ TABLO İLİŞKİLERİ VE FONKSİYONLARI

### **1. AI_FEATURE_CATEGORIES** (Master Tablo)
```sql
-- 18 sabit kategori (ID: 1-18)
-- Her kategorinin kendine özel icon, slug, description
```
**Amaç:** Feature'ları gruplandırır  
**Kolonlar:**
- `ai_feature_category_id` (1-18) - **SABİT ID'LER**
- `title` - Kategori adı ("SEO Araçları", "İçerik Üretimi")
- `slug` - URL friendly ("seo-tools", "content-creation")
- `icon` - FontAwesome ("fas fa-search", "fas fa-edit")
- `order` - Sıralama (1,2,3...)

### **2. AI_PROMPTS** (System Prompt'lar)
```sql
-- Sistem geneli prompt'lar
-- Feature'lardan bağımsız, genel kullanım
```
**Amaç:** Genel sistem prompt'larını saklar  
**Kolonlar:**
- `prompt_id` - **SABİT ID** (SP001-SP999: System, EP1000+: Expert)
- `title` - Prompt başlığı
- `prompt_text` - Gerçek prompt içeriği
- `category` - Prompt kategorisi
- `priority` - Kullanım önceliği

### **3. AI_FEATURE_PROMPTS** (Expert Prompt'lar)
```sql
-- Feature'lara özel expert prompt'lar
-- Reusable, birden fazla feature'da kullanılabilir
```
**Amaç:** Expert-level prompt'ları saklar  
**Kolonlar:**
- `id` - **AUTO INCREMENT** (1000'den başlar)
- `name` - Expert adı ("İçerik Üretim Uzmanı")
- `expert_prompt` - Uzman prompt metni
- `supported_categories` - JSON [1,2,6] hangi kategorilerde
- `expert_persona` - Uzman kişiliği ("seo_expert", "content_creator")
- `priority` - Öncelik puanı (1-100)

### **4. AI_FEATURES** (Ana Feature Tablosu)
```sql
-- Her AI feature'ı
-- Quick prompt + Response template içerir
```
**Amaç:** Her AI özelliğini saklar  
**Kolonlar:**
- `id` - **SABİT ID** (200-299: Content, 300-399: SEO, vs.)
- `ai_feature_category_id` - Kategori ilişkisi (1-18)
- `name` - Feature adı ("Blog Yazısı Oluşturucu")
- `slug` - Unique slug ("blog-yazisi-olusturucu")
- `quick_prompt` - Kısa, hızlı prompt (NE yapacağı)
- `response_template` - JSON yanıt şablonu
- `helper_function` - PHP helper adı ("ai_blog_yaz")

### **5. AI_FEATURE_PROMPT_RELATIONS** (İlişki Tablosu)
```sql
-- Feature'lar ile Expert Prompt'ları birbirine bağlar
-- Priority sistemi ile sıralama
```
**Amaç:** Feature ↔ Expert Prompt eşleştirmeleri  
**Kolonlar:**
- `feature_id` - ai_features.id'ye referans
- `prompt_id` - ai_feature_prompts.id'ye referans  
- `priority` - Kullanım sırası (1=en önemli)
- `role` - primary, secondary, supportive

---

## 📝 BLOG WRİTER ÖRNEĞİ - ADIM ADIM

### **ADIM 1: Expert Prompt'ları Hazırla**
```php
// ExpertPromptsSeeder.php içinde
private function seedContentExpertPrompts(): void
{
    // EP1001 - İçerik Üretim Uzmanı
    AIFeaturePrompt::create([
        'id' => 1001,
        'name' => 'İçerik Üretim Uzmanı',
        'slug' => 'content-creation-expert',
        'expert_prompt' => 'Sen profesyonel bir içerik yazarısın. SEO uyumlu, okunabilir ve etkili içerikler üretirsin.',
        'supported_categories' => json_encode([2, 12]), // İçerik + Yaratıcı
        'expert_persona' => 'content_creator',
        'priority' => 95,
        'prompt_type' => 'expert'
    ]);

    // EP1002 - SEO İçerik Uzmanı  
    AIFeaturePrompt::create([
        'id' => 1002,
        'name' => 'SEO İçerik Uzmanı',
        'slug' => 'seo-content-expert',
        'expert_prompt' => 'Sen SEO uzmanısın. İçerikleri arama motorları için optimize edersin.',
        'supported_categories' => json_encode([1, 2]), // SEO + İçerik
        'expert_persona' => 'seo_expert',
        'priority' => 90,
        'prompt_type' => 'expert'
    ]);
}
```

### **ADIM 2: Feature'ı Oluştur**
```php
// ContentCategoryFeaturesSeeder.php içinde
private function seedBlogWriter(): void
{
    $feature = AIFeature::create([
        'id' => 201, // Content kategorisi: 200-299
        'ai_feature_category_id' => 2, // İçerik Üretimi kategorisi
        'name' => 'Blog Yazısı Oluşturucu',
        'slug' => 'blog-yazisi-olusturucu',
        'description' => 'Profesyonel blog yazıları oluşturan AI asistanı',
        'quick_prompt' => 'Sen profesyonel bir blog yazarısın. Verilen konuyla ilgili engaging, SEO-friendly ve okuyucu odaklı blog yazıları oluştururun.',
        'response_template' => json_encode([
            'sections' => ['Başlık', 'Giriş', 'Ana İçerik', 'Sonuç', 'SEO Anahtar Kelimeler'],
            'format' => 'structured_content',
            'word_count_range' => [400, 1200]
        ]),
        'helper_function' => 'ai_blog_yaz',
        'helper_examples' => json_encode([
            "ai_blog_yaz('Web tasarım trendleri')",
            "ai_blog_yaz('SEO ipuçları', ['uzunluk' => 800, 'ton' => 'profesyonel'])"
        ]),
        'icon' => 'fas fa-blog',
        'emoji' => '📝',
        'badge_color' => 'primary',
        'complexity_level' => 'intermediate',
        'requires_input' => true,
        'input_placeholder' => 'Blog yazısı konusunu detaylarıyla açıklayın...',
        'status' => 'active',
        'is_featured' => true,
        'show_in_examples' => true,
        'sort_order' => 1
    ]);
}
```

### **ADIM 3: Feature-Prompt İlişkilerini Oluştur**
```php  
// BlogWriterRelationsSeeder.php (ayrı dosya)
class BlogWriterRelationsSeeder extends Seeder
{
    public function run(): void
    {
        $blogFeatureId = 201; // Blog Writer feature ID'si
        
        // Primary Expert: İçerik Üretim Uzmanı
        AIFeaturePromptRelation::create([
            'feature_id' => $blogFeatureId,
            'prompt_id' => 1001, // İçerik Üretim Uzmanı
            'priority' => 1,
            'role' => 'primary',
            'is_active' => true
        ]);
        
        // Secondary Expert: SEO İçerik Uzmanı
        AIFeaturePromptRelation::create([
            'feature_id' => $blogFeatureId, 
            'prompt_id' => 1002, // SEO İçerik Uzmanı
            'priority' => 2,
            'role' => 'supportive',
            'is_active' => true
        ]);
    }
}
```

---

## 📋 SEEDER TEMPLATE'İ

### **YENİ FEATURE SEEDER TEMPLATE:**
```php
<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeaturePrompt;

/**
 * 🎯 [CATEGORY_NAME] CATEGORY FEATURES SEEDER
 * 
 * Bu seeder [CATEGORY_NAME] kategorisindeki AI feature'ları oluşturur.
 * 
 * FEATURES LİSTESİ:
 * - [FEATURE_NAME_1] (ID: [ID_1]) - [DESCRIPTION_1]
 * - [FEATURE_NAME_2] (ID: [ID_2]) - [DESCRIPTION_2]
 * 
 * ID ARALIĞI: [START_ID]-[END_ID] ([CATEGORY_NAME] kategorisi)
 * KATEGORI ID: [CATEGORY_ID]
 * 
 * BAĞIMLILIKLAR:
 * - AIFeatureCategoriesSeeder (kategori mevcut olmalı)
 * - ExpertPromptsSeeder (expert prompt'lar hazır olmalı)
 * 
 * SONRA ÇALIŞTIRILMASI GEREKEN:
 * - [Feature1]RelationsSeeder
 * - [Feature2]RelationsSeeder
 */
class [CategoryName]CategoryFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎯 [CATEGORY_NAME] kategorisi feature\'ları ekleniyor...');
        
        // Feature'ları oluştur
        $this->seed[Feature1Name]();
        $this->seed[Feature2Name]();
        
        $this->command->info('✅ [CATEGORY_NAME] kategorisi feature\'ları başarıyla eklendi!');
    }
    
    /**
     * [FEATURE_1_NAME] Feature
     * 
     * PROMPT HIERARCHY:
     * 1. Quick Prompt: "[QUICK_PROMPT_DESCRIPTION]"  
     * 2. Expert Prompts (Relations ile bağlanacak):
     *    - EP[ID1] ([EXPERT_1_NAME]) - Primary, Priority: 1
     *    - EP[ID2] ([EXPERT_2_NAME]) - Supportive, Priority: 2
     * 
     * RESPONSE TEMPLATE:
     * - Sections: [SECTION_LIST]
     * - Format: [FORMAT]
     * - Features: [FEATURES_LIST]
     * 
     * HELPER FUNCTION: 
     * - Name: [HELPER_NAME]
     * - Usage: [HELPER_USAGE_EXAMPLE]
     */
    private function seed[Feature1Name](): void
    {
        AIFeature::create([
            'id' => [FEATURE_ID], // [CATEGORY_NAME] kategorisi: [ID_RANGE]
            'ai_feature_category_id' => [CATEGORY_ID], // [CATEGORY_NAME] kategorisi
            'name' => '[FEATURE_DISPLAY_NAME]',
            'slug' => '[feature-slug]',
            'description' => '[FEATURE_DESCRIPTION]',
            
            // PROMPT SİSTEMİ
            'quick_prompt' => '[QUICK_PROMPT_TEXT]',
            'response_template' => json_encode([
                'sections' => ['[SECTION1]', '[SECTION2]', '[SECTION3]'],
                'format' => '[FORMAT]',
                'features' => ['[FEATURE1]', '[FEATURE2]']
            ]),
            
            // HELPER SİSTEMİ
            'helper_function' => '[helper_function_name]',
            'helper_examples' => json_encode([
                "[helper_function_name]('[example_input]')",
                "[helper_function_name]('[example_input]', ['option' => 'value'])"
            ]),
            'helper_parameters' => json_encode([
                'input' => 'string - [INPUT_DESCRIPTION]',
                'options' => 'array - [OPTIONS_DESCRIPTION]'
            ]),
            'helper_description' => '[HELPER_DESCRIPTION]',
            
            // UI AYARLARI
            'icon' => '[FONTAWESOME_ICON]',
            'emoji' => '[EMOJI]',
            'badge_color' => '[BOOTSTRAP_COLOR]',
            'complexity_level' => '[beginner|intermediate|advanced]',
            'requires_input' => true,
            'input_placeholder' => '[INPUT_PLACEHOLDER_TEXT]',
            
            // DURUM
            'status' => 'active',
            'is_featured' => [true|false],
            'show_in_examples' => true,
            'sort_order' => [ORDER_NUMBER]
        ]);
        
        $this->command->info('  ✓ [FEATURE_DISPLAY_NAME] oluşturuldu (ID: [FEATURE_ID])');
    }
}
```

---

## 🔢 ID YÖNETİM SİSTEMİ

### **SABİT ID ARALIĞI:**
```php
// KATEGORI ID'LERİ (SABİT - DEĞİŞMEZ)
1  => 'SEO Araçları'           // Feature ID: 100-199
2  => 'İçerik Üretimi'         // Feature ID: 200-299  
3  => 'Çeviri Hizmetleri'      // Feature ID: 300-399
4  => 'Pazarlama Araçları'     // Feature ID: 400-499
5  => 'E-ticaret'              // Feature ID: 500-599
6  => 'Sosyal Medya'           // Feature ID: 600-699
7  => 'Email Pazarlama'        // Feature ID: 700-799
8  => 'Analiz Araçları'        // Feature ID: 800-899
9  => 'Müşteri Hizmetleri'     // Feature ID: 900-999
10 => 'İş Geliştirme'          // Feature ID: 1000-1099
11 => 'Araştırma'              // Feature ID: 1100-1199
12 => 'Yaratıcı İçerik'        // Feature ID: 1200-1299
13 => 'Teknik Dokümantasyon'   // Feature ID: 1300-1399
14 => 'Kod ve Geliştirme'      // Feature ID: 1400-1499
15 => 'Tasarım'                // Feature ID: 1500-1599
16 => 'Eğitim'                 // Feature ID: 1600-1699
17 => 'Finans'                 // Feature ID: 1700-1799
18 => 'Hukuki'                 // Feature ID: 1800-1899

// EXPERT PROMPT ID'LERİ
EP1001-EP1999 => Expert Prompt'lar
SP2001-SP2999 => System Prompt'lar
```

### **ÖRNEK ID KULLANIMI:**
```php
// Blog Yazısı (İçerik kategorisi - ID: 2)
'id' => 201, // 200-299 aralığında

// SEO Analiz (SEO kategorisi - ID: 1)  
'id' => 101, // 100-199 aralığında

// Çeviri (Çeviri kategorisi - ID: 3)
'id' => 301, // 300-399 aralığında
```

---

## 📁 DOSYA ORGANİZASYONU

### **SEEDER ÇALIŞTIRMA SIRASI:**
```php
// AIDatabaseSeeder.php içinde sıralama:

// 1. TEMEL ALTYAPI
$this->call(AIFeatureCategoriesSeeder::class);      // 18 kategori
$this->call(ExpertPromptsSeeder::class);            // Expert prompt library

// 2. FEATURE'LAR (Kategori bazlı)
$this->call(ContentCategoryFeaturesSeeder::class);  // İçerik features
$this->call(SEOCategoryFeaturesSeeder::class);      // SEO features
$this->call(TranslationCategoryFeaturesSeeder::class); // Çeviri features
// ... diğer kategori seeder'ları

// 3. İLİŞKİLER (Feature bazlı - EN SON)
$this->call(BlogWriterRelationsSeeder::class);      // Blog writer ilişkileri
$this->call(SEOAnalyzerRelationsSeeder::class);     // SEO analyzer ilişkileri
// ... diğer relation seeder'ları
```

### **DOSYA ADLANDIRMA KURALLARI:**
```
✅ DOĞRU:
- ContentCategoryFeaturesSeeder.php
- BlogWriterRelationsSeeder.php  
- SEOAnalyzerRelationsSeeder.php

❌ YANLIŞ:
- AllFeaturesSeeder.php (çok genel)
- BlogSeeder.php (eksik bilgi)
- Relations.php (çok genel)
```

---

## ✅ KONTROL LİSTESİ

### **YENİ FEATURE SEEDER ÖNCESİ:**
- [ ] Kategori ID'sini belirle (1-18)
- [ ] Feature ID aralığını belirle (kategori*100 + sıra)  
- [ ] İhtiyaç duyulan expert prompt'ları tanımla
- [ ] Helper function adını belirle
- [ ] Response template tasarımını yap

### **SEEDER OLUŞTURURKEN:**
- [ ] Template'i kopyala ve doldur
- [ ] ID'lerin benzersiz olduğunu kontrol et
- [ ] Quick prompt'u yaz
- [ ] Response template JSON'unu hazırla
- [ ] Helper examples'ları ekle
- [ ] UI ayarlarını yapılandır

### **SEEDER SONRASI:**
- [ ] Relations seeder'ını oluştur
- [ ] AIDatabaseSeeder'a ekle  
- [ ] Test et: `php artisan db:seed --class="NewFeatureSeeder"`
- [ ] Admin panelinde kontrol et
- [ ] Helper function'ı test et

### **PRODUCTION ÖNCESİ:**
- [ ] Tüm ID'ler unique mi?
- [ ] Relations doğru kurulmuş mu?
- [ ] Helper function çalışıyor mu?
- [ ] Admin panelde görünüyor mu?
- [ ] Prowess sayfasında test edildi mi?

---

## 🔧 ÖNEMLİ NOTLAR

### **❗ KRITIK KURALLAR:**
1. **ID'ler asla değişmez** - Production'da ID değiştirmek felaket!
2. **Relations ayrı dosya** - Feature seeder'ından ayrı tut
3. **Template consistency** - Aynı formatta yanıt ver
4. **Helper naming** - `ai_[category]_[action]` formatı

### **🚨 SIYAH DIKKAT LİSTESİ:**
- ❌ Duplicate ID kullanma
- ❌ Foreign key eksik bırakma  
- ❌ Response template'i boş bırakma
- ❌ Expert prompt ilişkisini unutma
- ❌ Helper function test etmeme

### **💡 İPUÇLARI:**
- ✅ Önce expert prompt'ları hazırla
- ✅ Feature'ı oluştur, sonra relations'ı kur
- ✅ Her seeder'ı tek başına test et
- ✅ Template'leri tutarlı tut
- ✅ Documentation'ı eksik bırakma

---

**Bu kılavuz sayesinde profesyonel AI feature seeder'larını hızla oluşturabilirsin!**

**Sonraki Adım:** Bu template'i kullanarak yeni feature seeder'larını geliştir ve sistem dokümantasyonunu güncel tut.