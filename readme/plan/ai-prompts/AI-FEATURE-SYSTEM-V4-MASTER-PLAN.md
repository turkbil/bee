# 🎯 AI FEATURE SYSTEM V4 - MASTER PLAN
**Hedef**: Sıfırdan mükemmel, kontrollü AI feature sistemi

## 🏗️ SİSTEM MİMARİSİ

### 1. DOSYA YAPISI
```
/ai-prompts/features/
├── categories/
│   ├── seo/
│   │   ├── meta-tag-generator.md
│   │   ├── title-optimizer.md
│   │   └── content-seo-analyzer.md
│   ├── content/
│   │   ├── blog-writer.md
│   │   ├── article-writer.md
│   │   └── story-writer.md
│   ├── social-media/
│   │   ├── tweet-generator.md
│   │   ├── instagram-caption.md
│   │   └── linkedin-post.md
│   └── translation/
│       ├── multi-language-translator.md
│       └── localization-adapter.md
├── prompts/
│   ├── expert-prompts/
│   │   ├── EP001-writing-expert.md
│   │   ├── EP002-seo-expert.md
│   │   ├── EP003-social-media-expert.md
│   │   ├── EP004-translation-expert.md
│   │   └── EP005-creativity-booster.md
│   └── system-prompts/
│       ├── SP001-base-behavior.md
│       ├── SP002-security-rules.md
│       └── SP003-output-formatting.md
└── templates/
    ├── response-templates/
    │   ├── RT001-structured-content.json
    │   ├── RT002-seo-analysis.json
    │   └── RT003-social-media-post.json
    └── input-templates/
        ├── IT001-basic-text-input.json
        ├── IT002-advanced-content-form.json
        └── IT003-seo-optimization-form.json
```

## 📋 FEATURE DOSYASI TEMPLATE

### Feature MD Dosyası Formatı:
```markdown
# Feature: Blog Yazısı Oluşturucu

## METADATA
- **Feature ID**: BW001 (Blog Writer 001)
- **Slug**: blog-yazisi-olusturucu
- **Category ID**: 2 (İçerik Yazıcılığı)
- **Status**: active
- **Priority**: 100
- **Sort Order**: 1
- **Created**: 2025-08-10
- **Version**: 1.0

## FEATURE INFO
- **Name**: Blog Yazısı Oluşturucu
- **Description**: Profesyonel blog yazıları oluşturan AI asistanı
- **Icon**: fas fa-blog
- **Badge Color**: primary
- **Complexity**: intermediate

## QUICK PROMPT
```
Sen profesyonel bir blog yazarısın. Verilen konuyla ilgili engaging, SEO-friendly ve okuyucu odaklı blog yazıları oluşturursun.
```

## EXPERT PROMPTS (Priority Order)
- **EP002** (Priority: 100) - SEO Expert
- **EP001** (Priority: 90) - Writing Expert  
- **EP005** (Priority: 70) - Creativity Booster

## RESPONSE TEMPLATE
- **Template ID**: RT001
- **Format**: structured-content
- **Sections**: ["Başlık", "Giriş", "Ana İçerik", "Sonuç", "SEO Anahtar Kelimeler"]

## INPUT SYSTEM
- **Input Template**: IT002 (Advanced Content Form)
- **Primary Input**: 
  - Name: "Konu"
  - Type: textarea
  - Required: true
  - Placeholder: "Blog yazısı konusunu detaylarıyla açıklayın..."
- **Input Groups**:
  - **Yazı Ayarları**:
    - Ton (select): [Profesyonel, Samimi, Eğitici, Eğlenceli]
    - Uzunluk (range): 200-2000 kelime
    - Hedef Kitle (text): İsteğe bağlı
  - **SEO Ayarları**:
    - Ana Anahtar Kelime (text)
    - Hedef Dil (select): [Türkçe, İngilizce]

## USAGE EXAMPLES
```php
// Basic usage
ai_blog_yaz('Web tasarım trendleri');

// Advanced usage  
ai_blog_yaz('Web tasarım trendleri', [
    'ton' => 'profesyonel',
    'uzunluk' => 800,
    'hedef_kitle' => 'Tasarımcılar',
    'anahtar_kelime' => 'web tasarım 2025'
]);

// Generic feature call
ai_feature('blog-yazisi-olusturucu', 'Web tasarım trendleri', [
    'ton' => 'samimi'
]);
```

## TOKEN COST
- **Estimated**: 500-800 tokens
- **Input**: ~100-200 tokens
- **Output**: ~400-600 tokens

## VALIDATION RULES
```json
{
    "konu": ["required", "min:10", "max:500"],
    "ton": ["in:profesyonel,samimi,egitici,eglenceli"],
    "uzunluk": ["numeric", "min:200", "max:2000"],
    "anahtar_kelime": ["max:100"]
}
```
```

## 🎯 EXPERT PROMPT SİSTEMİ

### Expert Prompt Dosyası Formatı:
```markdown
# Expert Prompt: Writing Expert

## METADATA
- **Prompt ID**: EP001
- **Name**: İyi Edebiyat Yazarı
- **Category**: writing
- **Priority Range**: 80-100
- **Reusable**: true
- **Created**: 2025-08-10
- **Version**: 1.0

## PROMPT CONTENT
```
Sen yaratıcı yazarlık alanında uzman bir editörsün. Metinleri:
- Akıcı ve okunabilir hale getirirsin
- Doğru dilbilgisi ve yazım kuralları uygularsın  
- Hedef kitleye uygun dil kullanırsın
- İçeriği engaging ve ilgi çekici yaparsın
- Paragraf düzenini optimize edersin
```

## USAGE CONDITIONS
- **Content Types**: [blog, article, story, social-media]
- **Languages**: [tr, en]
- **Min Input Length**: 10
- **Max Input Length**: unlimited

## COMPATIBLE FEATURES
- blog-yazisi-olusturucu (Priority: 90)
- makale-yazici (Priority: 95)
- sosyal-medya-gonderisi (Priority: 85)
- email-icerik-yazici (Priority: 80)
```

## 💾 VERİTABANI ID SİSTEMİ

### ID Sabitleme Stratejisi:
```php
// Feature ID'leri kategori bazlı aralıklarda
SEO Category (ID: 1): Feature ID 100-199
Content Category (ID: 2): Feature ID 200-299  
Translation Category (ID: 3): Feature ID 300-399
Social Media Category (ID: 6): Feature ID 600-699

// Expert Prompt ID'leri
Expert Prompts: 1000-1999
System Prompts: 2000-2999
Category Specific: 3000+

// Response Template ID'leri
Basic Templates: 100-199
Advanced Templates: 200-299
Specialized Templates: 300-399
```

## 🔄 SEEDER SİSTEMİ

### Yeni Seeder Yapısı:
1. **CategorySeeder** - 18 kategori (mevcut)
2. **ExpertPromptsSeeder** - Reusable expert prompts
3. **SystemPromptsSeeder** - Base system prompts  
4. **ResponseTemplatesSeeder** - Yanıt şablonları
5. **InputTemplatesSeeder** - Form şablonları
6. **FeatureSeeder_Category** - Her kategori için ayrı seeder

### Feature Seeder Örneği:
```php
class ContentCategoryFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBlogWriter();
        $this->seedArticleWriter();  
        $this->seedStoryWriter();
    }
    
    private function seedBlogWriter(): void
    {
        // Feature creation
        $feature = AIFeature::create([
            'id' => 201, // Fixed ID
            'ai_feature_category_id' => 2,
            'name' => 'Blog Yazısı Oluşturucu',
            'slug' => 'blog-yazisi-olusturucu',
            // ... other fields from MD file
        ]);
        
        // Expert Prompt Relations
        $feature->prompts()->attach([
            1002 => ['role' => 'primary', 'priority' => 100],
            1001 => ['role' => 'secondary', 'priority' => 90],
            1005 => ['role' => 'enhancement', 'priority' => 70]
        ]);
        
        // Input System
        $this->createInputSystem($feature);
    }
}
```

## 🚀 UYGULAMA STRATEJİSİ

### Phase 1: Temelleri Hazırla (1 gün)
1. ✅ Eski seeder'ları temizle
2. ✅ Kategori seeder'ı kontrol et
3. ✅ Expert prompt library oluştur (5-10 temel prompt)
4. ✅ Response template'leri hazırla
5. ✅ Input template'leri hazırla

### Phase 2: İlk Perfect Feature (1 gün)  
1. ✅ Blog Yazısı Oluşturucu MD dosyası yaz
2. ✅ Seeder'ı oluştur ve test et
3. ✅ Universal Input System entegre et
4. ✅ Prowess'te test et - PERFECT çalışmalı!
5. ✅ Helper function test et

### Phase 3: Kademeli Genişletme (5 gün)
1. ✅ Kategori başına 2-3 feature ekle
2. ✅ Her feature'ı test et
3. ✅ Prompt conflicts kontrol et
4. ✅ Performance optimization

### Phase 4: Production Ready (2 gün)
1. ✅ Tüm feature'ları test et
2. ✅ Documentation tamamla  
3. ✅ Cache sistemi optimize et
4. ✅ Error handling güçlendir

## 💡 AVANTAJLAR

### ✅ Kalite Kontrol
- Her feature MD dosyasında planlanmış
- Test edilmeden production'a çıkmaz
- Prompt conflicts minimum

### ✅ Maintainability  
- Dosya bazlı organizasyon
- Version control friendly
- Clear dependencies

### ✅ Scalability
- ID aralıkları planlanmış
- Expert prompt'lar reusable
- Template sistemi esnek

### ✅ Developer Experience
- Clear documentation
- Consistent structure
- Easy debugging

## 🎪 ÖRNEK WORKFLOW

1. **Yeni Feature İhtiyacı**: "Twitter Thread Generator"
2. **MD Dosyası Oluştur**: `/features/social-media/twitter-thread-generator.md`
3. **Expert Prompt'ları Seç**: EP003 (Social Media) + EP001 (Writing)
4. **Input System Tasarla**: Thread adedi, konu, ton
5. **Response Template**: Numbered thread format
6. **Seeder Oluştur**: Fixed ID 601
7. **Test Et**: Prowess'te perfect çalışmalı
8. **Deploy Et**: Production'a gönder

Bu plan nasıl? Eksik gördüğün nokta var mı?