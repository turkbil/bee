# 📝 BLOG YAZISI OLUŞTURUCU - KOMPLE DOKÜMANTASYON

## 🎯 AMATÖR İÇİN BASIT ANLATIM

### Blog Yazısı Nasıl Oluşturulur?

1. **Admin Panele Giriş:**
   - `http://laravel.test/admin` adresine gidin
   - AI → Features → Blog Yazısı Oluşturucu sayfasını açın

2. **Form Doldurma:**
   - **Blog Konusu** kutusuna yazmak istediğiniz konuyu yazın (ör: "2025 Web Tasarım Trendleri")
   - **İçerik Uzunluğu** kaydırıcısından istediğiniz uzunluğu seçin (1-5 arası, 3 normal)
   - **Yazım Tonu** açılır menüsünden tonu seçin (Profesyonel, Samimi, Eğitici vs.)
   - **Hedef Kitle** alanına okuyucu kitlenizi yazın (ör: "25-35 yaş web tasarımcılar")
   - **Şirket Profilimi Kullan** kutusunu işaretlerseniz, AI şirket bilgilerinizi kullanır

3. **Blog Oluşturma:**
   - "Blog Yazısı Oluştur" butonuna tıklayın
   - AI size özel blog yazısı oluşturacak
   - Yanıt şu bölümleri içerecek:
     - ✅ SEO uyumlu başlık
     - ✅ Meta açıklama (Google arama sonuçları için)
     - ✅ Giriş paragrafı
     - ✅ Ana içerik (H2, H3 başlıklar, listeler)
     - ✅ Sonuç bölümü
     - ✅ SEO anahtar kelimeler
     - ✅ Sosyal medya paylaşım önerileri

4. **Kredi Sistemi:**
   - Her blog oluşturma işlemi kredi harcar
   - Kullanılan kredi miktarı, içerik uzunluğuna göre değişir
   - Kredi yetersizse sistem uyarı verir

### Arkada Ne Oluyor?

1. **Form gönderildiğinde:**
   - Sistem önce kredinizi kontrol eder
   - Yeterli kredininiz varsa işleme devam eder

2. **AI'ya gönderim:**
   - Sistem 20+ farklı prompt'u sırayla birleştirir
   - Önce sistem kuralları, sonra expert bilgileri eklenir
   - En son kullanıcı girdisi eklenir

3. **Yanıt işleme:**
   - AI'dan gelen yanıt formatlanır
   - Kredi düşürülür ve kayıt edilir
   - Yanıt ekranda gösterilir

## 🔧 TEKNİK DOKÜMANTASYON

### Sistem Mimarisi

Blog Yazısı Oluşturucu feature'ı **Universal Input System V3** üzerine inşa edilmiştir ve tamamen **database-driven** çalışır.

## 🎯 GENEL OVERVIEW - BLOG YAZISI OLUŞTURUCU SİSTEMİ

### Kullanılan Seeder Dosyaları:
1. **AISystemPromptsSeeder.php** - Sistem prompt'ları (ai_prompts tablosu)
2. **UniversalContentLengthPromptsSeeder.php** - İçerik uzunluğu prompt'ları (ai_prompts tablosu) 
3. **UniversalWritingTonePromptsSeeder.php** - Yazım tonu prompt'ları (ai_prompts tablosu)
4. **BlogContentExpertPromptsSeeder.php** - Expert prompt'lar (ai_feature_prompts tablosu)
5. **BlogContentFeaturesSeeder.php** - Blog feature'ı (ai_features tablosu)
6. **BlogContentFeaturePromptRelationsSeeder.php** - İlişki tablosu (ai_feature_prompt_relations tablosu)
7. **BlogWriterUniversalInputSeeder.php** - Form sistemi (4 ayrı tablo)

### Koordinatör Seeder:
- **ModernBlogContentSeeder.php** - Tüm seeder'ları koordine eder (çalıştırmaz, sadece çağırır)

---

## 📊 TABLO BAZINDA DETAYLI İNCELEME

### 1. `ai_prompts` TABLOSU

#### AISystemPromptsSeeder.php Eklediği Satırlar:

**Satır 1:**
- **prompt_id:** 90001
- **name:** "Ortak Sistem Kuralları"
- **content:** "Sen profesyonel bir AI asistanısın. Verilen görevi en iyi şekilde tamamlamalısın..." (Profesyonel AI davranış kuralları)
- **prompt_type:** "common"
- **prompt_category:** "system_common"
- **is_active:** true
- **is_common:** true
- **is_system:** true
- **priority:** 1
- **ai_weight:** 100

**Satır 2:**
- **prompt_id:** 90002
- **name:** "Gizli Bilgi Tabanı"  
- **content:** "İçerik üretirken şu gizli kuralları uygula..." (SEO, güvenlik, kalite kuralları)
- **prompt_type:** "hidden_system"
- **prompt_category:** "system_hidden"
- **is_active:** true
- **is_common:** false
- **is_system:** true
- **priority:** 1
- **ai_weight:** 95

**Satır 3:**
- **prompt_id:** 90003
- **name:** "Şartlı Yanıt Kuralları"
- **content:** "Eğer kullanıcı şunları isterse özel kurallar uygula..." (Kısa/uzun içerik koşulları)
- **prompt_type:** "conditional"
- **prompt_category:** "conditional_info"
- **is_active:** true
- **is_common:** false
- **is_system:** true
- **priority:** 2
- **ai_weight:** 90

**Satır 4:**
- **prompt_id:** 90004
- **name:** "Çıktı Formatlama Kuralları"
- **content:** "Tüm çıktıları şu formatta düzenle..." (H1/H2/H3, maddeleme, vurgulama kuralları)
- **prompt_type:** "common"
- **prompt_category:** "response_format"
- **is_active:** true
- **is_common:** false
- **is_system:** true
- **priority:** 2
- **ai_weight:** 85

**Satır 5:**
- **prompt_id:** 90005
- **name:** "Dil ve Ton Ayarları"
- **content:** "İçerik tonunu dinamik olarak ayarla..." (Profesyonel/samimi/eğitici/pazarlama tonları)
- **prompt_type:** "writing_tone"
- **prompt_category:** "expert_knowledge"
- **is_active:** true
- **is_common:** false
- **is_system:** true
- **priority:** 2
- **ai_weight:** 80

**Satır 6-10:** Performans optimizasyonu, hata yönetimi, güvenlik, bağlam farkındalığı ve yaratıcılık kuralları (90006-90010 ID'lerle)

#### UniversalContentLengthPromptsSeeder.php Eklediği Satırlar:

**Satır 11:**
- **prompt_id:** 90011
- **name:** "Çok Kısa İçerik"
- **content:** "İçeriği çok kısa tutun: - Maksimum 50-75 kelime..."
- **prompt_type:** "content_length"
- **prompt_category:** "response_format"
- **is_active:** true
- **is_common:** false
- **is_system:** false
- **priority:** 5
- **ai_weight:** 95

**Satır 12:**
- **prompt_id:** 90012
- **name:** "Kısa İçerik"
- **content:** "İçeriği kısa ve öz tutun: - 100-200 kelime arası..."
- **prompt_type:** "content_length"
- **prompt_category:** "response_format"
- **is_active:** true
- **is_common:** false
- **is_system:** false
- **priority:** 4
- **ai_weight:** 90

**Satır 13:**
- **prompt_id:** 90013
- **name:** "Normal İçerik"
- **content:** "İçeriği dengeli uzunlukta hazırla: - 300-500 kelime arası..."
- **prompt_type:** "content_length"
- **prompt_category:** "response_format"
- **is_active:** true
- **is_common:** true (varsayılan seçim)
- **is_system:** false
- **priority:** 3
- **ai_weight:** 85

**Satır 14-15:** Uzun İçerik (90014) ve Çok Detaylı İçerik (90015)

#### UniversalWritingTonePromptsSeeder.php Eklediği Satırlar:

**Satır 16:**
- **prompt_id:** 90021
- **name:** "Profesyonel"
- **content:** "Profesyonel bir yaklaşım benimseyin: - İş hayatına uygun terminoloji..."
- **prompt_type:** "writing_tone"
- **prompt_category:** "response_format"
- **is_active:** true
- **is_common:** true (varsayılan seçim)
- **is_system:** false
- **priority:** 5
- **ai_weight:** 95

**Satır 17:**
- **prompt_id:** 90022
- **name:** "Samimi"
- **content:** "Samimi ve yakın bir dil kullan: - Dostane ve sıcak ifadeler..."
- **prompt_type:** "writing_tone"
- **prompt_category:** "response_format"
- **is_active:** true
- **is_common:** true
- **is_system:** false
- **priority:** 4
- **ai_weight:** 90

**Satır 18-20:** Eğitici (90023), Eğlenceli (90024), Uzman (90025) tonları

---

### 2. `ai_feature_prompts` TABLOSU

#### BlogContentExpertPromptsSeeder.php Eklediği Satırlar:

**Satır 1:**
- **id:** 1001
- **name:** "İçerik Üretim Uzmanı"
- **slug:** "content-creation-expert"
- **description:** "Blog yazısı oluşturma konusunda uzman. İçerik yapısı, akış ve okunabilirlik konularında rehberlik eder."
- **expert_prompt:** "Sen deneyimli bir içerik üretim uzmanısın. Blog yazısı oluştururken şu konularda uzmansın: TEMEL YAPILANDIRMA, İÇERİK KALİTESİ, OKUMAYA YÖNLENDIRME..." (Detaylı expert bilgisi)
- **response_template:** `{"sections": ["başlık", "giriş", "ana_içerik", "sonuç"], "format": "structured_blog", "include_meta": true}`
- **supported_categories:** `[2, 12, 16]` (İçerik, Yaratıcı İçerik, Eğitim)
- **expert_persona:** "content_creator"
- **personality_traits:** "Yaratıcı, sistematik, okuyucu odaklı, yapılandırılmış düşünen"
- **expertise_areas:** `["blog_writing", "content_structure", "audience_engagement", "copywriting"]`
- **priority:** 90
- **complexity_level:** "advanced"
- **context_weight:** 85

**Satır 2:**
- **id:** 1002
- **name:** "SEO İçerik Uzmanı"
- **slug:** "seo-content-expert"
- **description:** "SEO odaklı içerik oluşturma konusunda uzman. Anahtar kelime optimizasyonu ve arama motoru uyumluluğu sağlar."
- **expert_prompt:** "Sen SEO konusunda uzman bir içerik stratejistisin. Blog yazısı oluştururken şu SEO faktörlerine odaklanırsın: ANAHTAR KELİME OPTİMİZASYONU, İÇERİK YAPISI, TEKNİK SEO, KULLANICI DENEYİMİ..." (Detaylı SEO rehberi)
- **response_template:** `{"sections": ["seo_title", "meta_description", "content", "keywords"], "format": "seo_optimized", "include_seo_score": true}`
- **supported_categories:** `[1, 2, 4]` (SEO, İçerik, Pazarlama)
- **expert_persona:** "seo_specialist"
- **personality_traits:** "Analitik, detay odaklı, veri yönelimli, teknik bilgiye sahip"
- **expertise_areas:** `["seo_optimization", "keyword_research", "content_marketing", "search_algorithms"]`
- **priority:** 85
- **complexity_level:** "expert"
- **context_weight:** 80

**Satır 3:**
- **id:** 1003  
- **name:** "Blog Yazarı Uzmanı"
- **slug:** "professional-blogger"
- **description:** "Profesyonel blog yazarlığı konusunda uzman. Okuyucu etkileşimi ve engagement arttırma konularında rehberlik eder."
- **expert_prompt:** "Sen profesyonel bir blog yazarısın. Okuyucu etkileşimi yüksek blog yazıları oluşturma konusunda uzmansın: OKUYUCU ETKİLEŞİMİ, YAZIŞAL ÜSLUP, İÇERİK ZENGİNLEŞTİRME, SONUÇLANDIRMA..." (Blog yazıcılığı rehberi)
- **response_template:** `{"sections": ["hook", "story", "main_content", "engagement_cta"], "format": "engaging_blog", "include_interaction": true}`
- **supported_categories:** `[2, 6, 12]` (İçerik, Sosyal Medya, Yaratıcı İçerik)
- **expert_persona:** "professional_blogger"
- **personality_traits:** "Empatik, etkileşimci, hikaye anlatıcısı, sosyal medya savvy"
- **expertise_areas:** `["blog_writing", "audience_engagement", "storytelling", "social_media"]`
- **priority:** 75
- **complexity_level:** "intermediate"
- **context_weight:** 70

**Satır 4-5:** Yaratıcı İçerik Uzmanı (1004) ve Sosyal Medya Entegrasyonu Uzmanı (1005)

---

### 3. `ai_features` TABLOSU

#### BlogContentFeaturesSeeder.php Eklediği Satırlar:

**Satır 1:**
- **id:** 201
- **ai_feature_category_id:** 2 (İçerik Üretimi kategorisi)
- **name:** "Blog Yazısı Oluşturucu"
- **slug:** "blog-yazisi-olusturucu"
- **description:** "Kolay kullanımlı AI blog yazma asistanı. Sadece konunuzu yazın, AI sizin için profesyonel, SEO uyumlu ve okunabilir blog yazıları oluştursun."

**V3 Universal Input System Alanları:**
- **module_type:** "blog"
- **category:** "content_generation"
- **supported_modules:** `["page", "blog", "portfolio", "announcement"]`
- **context_rules:** `{"auto_activate": ["blog_creation", "content_writing"], "module_specific": {"blog": true, "page": true}, "user_level": ["beginner", "intermediate", "advanced"], "content_type": ["blog", "article", "post"]}`
- **template_support:** true
- **bulk_support:** true
- **streaming_support:** true

**Prompt Sistemi:**
- **quick_prompt:** "Sen profesyonel bir blog yazarısın. Verilen konuda engaging, SEO-friendly ve okuyucu odaklı blog yazıları oluştururun..."
- **response_template:** `{"sections": ["Çekici Başlık", "Meta Açıklama", "Giriş", "Ana İçerik", "Sonuç", "SEO Anahtar Kelimeler", "Sosyal Medya Önerileri"], "format": "structured_blog_content", "features": ["seo_optimized", "social_ready", "engaging_format"], "word_count_range": [400, 1500]}`

**Helper Sistemi:**
- **helper_function:** "ai_blog_yaz"
- **helper_examples:** `{"basic": {"description": "Basit blog yazısı oluşturma", "code": "ai_blog_yaz('Web tasarım trendleri 2025')", "estimated_tokens": "800-1200"}, "advanced": {...}, "seo_focused": {...}}`
- **helper_parameters:** `{"konu": "string - Blog yazısının ana konusu (required)", "uzunluk": "integer - Hedef kelime sayısı", "ton": "string - Yazım tonu", ...}`
- **helper_description:** "Professional blog yazıları oluşturan AI helper function..."
- **helper_returns:** `{"baslik": "string - SEO-optimized blog başlığı", "meta_aciklama": "string - Meta description", "icerik": "string - Tam blog içeriği", ...}`

**UI Ayarları:**
- **icon:** "ti ti-pencil"
- **emoji:** "📝"
- **badge_color:** "primary"
- **complexity_level:** "intermediate"
- **requires_input:** true
- **input_placeholder:** "Hangi konu hakkında blog yazısı yazmak istiyorsunuz?..."
- **button_text:** "Blog Yazısı Oluştur"

**Validation & Settings:**
- **input_validation:** `{"konu": ["required", "string", "min:10", "max:500"], "uzunluk": ["integer", "min:300", "max:2000"], ...}`
- **settings:** `{"max_processing_time": 120, "auto_save_drafts": true, "enable_preview": true, ...}`
- **error_messages:** `{"validation_failed": "Girilen bilgiler geçersiz...", ...}`
- **success_messages:** `{"content_generated": "Blog yazısı başarıyla oluşturuldu!", ...}`

**Durum Alanları:**
- **status:** "active"
- **is_featured:** true
- **show_in_examples:** true
- **sort_order:** 1
- **usage_count:** 0
- **avg_rating:** 0.0

---

### 4. `ai_feature_prompt_relations` TABLOSU

#### BlogContentFeaturePromptRelationsSeeder.php Eklediği Satırlar:

**Satır 1:**
- **feature_id:** 201 (Blog Yazısı Oluşturma)
- **prompt_id:** null
- **feature_prompt_id:** 1001 (İçerik Üretim Uzmanı)
- **role:** "primary"
- **priority:** 1
- **is_active:** true

**Satır 2:**
- **feature_id:** 201
- **prompt_id:** null
- **feature_prompt_id:** 1002 (SEO İçerik Uzmanı)
- **role:** "secondary"
- **priority:** 2
- **is_active:** true

**Satır 3:**
- **feature_id:** 201
- **prompt_id:** null
- **feature_prompt_id:** 1003 (Blog Yazarı Uzmanı)
- **role:** "secondary"
- **priority:** 2
- **is_active:** true

**Satır 4:**
- **feature_id:** 201
- **prompt_id:** null
- **feature_prompt_id:** 1004 (Yaratıcı İçerik Uzmanı)
- **role:** "supportive"
- **priority:** 3
- **is_active:** true

**Satır 5:**
- **feature_id:** 201
- **prompt_id:** null
- **feature_prompt_id:** 1005 (Sosyal Medya Entegrasyonu Uzmanı)
- **role:** "supportive"
- **priority:** 3
- **is_active:** true

---

### 5. UNIVERSAL INPUT SYSTEM TABLOLARI

#### BlogWriterUniversalInputSeeder.php - 4 Tabloya Eklenen Veriler:

##### 5.1 `ai_input_groups` TABLOSU

**Satır 1:**
- **id:** 1
- **name:** "Temel Blog Ayarları"
- **slug:** "blog_basic_inputs"
- **feature_id:** 201
- **sort_order:** 1
- **is_collapsible:** false
- **is_expanded:** true
- **description:** "Blog yazısı oluşturma için temel gerekli alanlar"

**Satır 2:**
- **id:** 2
- **name:** "İleri Düzey Ayarlar"
- **slug:** "blog_advanced_settings"
- **feature_id:** 201
- **sort_order:** 2
- **is_collapsible:** true
- **is_expanded:** false
- **description:** "Blog yazısını özelleştirmek için gelişmiş seçenekler"

##### 5.2 `ai_feature_inputs` TABLOSU

**Satır 1:**
- **id:** 1
- **name:** "Blog Konusu"
- **slug:** "blog_topic"
- **feature_id:** 201
- **group_id:** 1
- **type:** "textarea"
- **placeholder:** "Hangi konu hakkında blog yazısı yazmak istiyorsunuz?"
- **help_text:** "Yapay zeka ile yazılacak konuyu belirtin. Açık ve detaylı konu tanımlaması daha iyi sonuç verir."
- **is_primary:** true
- **is_required:** true
- **validation_rules:** `["required", "string", "min:10", "max:1000"]`
- **sort_order:** 1
- **config:** `{"rows": 4, "character_limit": 1000, "show_counter": true}`

**Satır 2:**
- **id:** 2
- **name:** "Yazım Tonu"
- **slug:** "writing_tone"
- **feature_id:** 201
- **group_id:** 2
- **type:** "select"
- **placeholder:** "Yazım tonunu seçin"
- **help_text:** "İçeriğinizin hangi tonla yazılmasını istiyorsunuz?"
- **is_primary:** false
- **is_required:** false
- **validation_rules:** `["nullable", "string"]`
- **sort_order:** 2
- **config:** `{"data_source": "ai_prompts", "data_filter": {"prompt_type": "writing_tone", "is_active": true}, "value_field": "prompt_id", "label_field": "name", "default_value": null}`

**Satır 3:**
- **id:** 3
- **name:** "İçerik Uzunluğu"
- **slug:** "content_length"
- **feature_id:** 201
- **group_id:** 1
- **type:** "range"
- **help_text:** "Blog yazısının ne kadar detaylı olmasını istiyorsunuz?"
- **is_primary:** true
- **is_required:** true
- **validation_rules:** `["required", "integer", "min:1", "max:5"]`
- **sort_order:** 3
- **config:** `{"data_source": "ai_prompts", "data_filter": {"prompt_type": "content_length", "is_active": true}, "value_field": "prompt_id", "label_field": "name", "min_value": 1, "max_value": 5, "default_value": 3, "step": 1}`

**Satır 4:**
- **id:** 4
- **name:** "Hedef Kitle"
- **slug:** "target_audience"
- **feature_id:** 201
- **group_id:** 2
- **type:** "text"
- **help_text:** "Yaş grubu, meslek, deneyim seviyesi, ilgi alanları gibi detayları ekleyebilirsiniz."
- **is_primary:** true
- **is_required:** false
- **validation_rules:** `["nullable", "string", "min:3", "max:500"]`
- **sort_order:** 1
- **config:** `{"character_limit": 500, "show_counter": true, "autocomplete_suggestions": ["18-25 yaş gençler", "25-35 yaş profesyoneller", ...]}`

**Satır 5:**
- **id:** 5
- **name:** "Şirket Profilimi Kullan"
- **slug:** "use_company_profile"
- **feature_id:** 201
- **group_id:** 2
- **type:** "checkbox"
- **help_text:** "AI, şirket bilgilerinizi kullanarak daha kişiselleştirilmiş içerik üretir"
- **is_primary:** false
- **is_required:** false
- **validation_rules:** `["nullable", "boolean"]`
- **sort_order:** 4
- **config:** `{"style": "switch", "size": "default", "color": "success", "icon": "ti ti-building-store", "api_check": "/admin/ai/api/profiles/company-info", "show_status": true}`

##### 5.3 `ai_input_options` TABLOSU
- Bu seeder'da bu tabloya veri eklenmiyor (dinamik veri kullanımı tercih edilmiş)

---

## 🚀 ÇALIŞMA SİSTEMİ - PROMPT HİYERARŞİSİ

Blog Yazısı Oluşturucu çalışırken şu sırada prompt'lar devreye girer:

### 1. Sistem Prompt'ları (ai_prompts - Otomatik)
1. Ortak Sistem Kuralları (90001) - AI davranış kuralları
2. Gizli Bilgi Tabanı (90002) - SEO, güvenlik kuralları
3. Çıktı Formatlama Kuralları (90004) - Format standartları

### 2. Kullanıcı Tercihleri (ai_prompts - Seçime Göre)
1. Yazım Tonu (90021-90025) - Kullanıcı seçimine göre
2. İçerik Uzunluğu (90011-90015) - Kullanıcı seçimine göre

### 3. Feature Quick Prompt (ai_features)
- Blog feature'ının kendi quick_prompt'u çalışır

### 4. Expert Prompt'lar (ai_feature_prompts - Priority Sırasına Göre)
1. İçerik Üretim Uzmanı (1001) - Priority: 1, Role: primary
2. SEO İçerik Uzmanı (1002) - Priority: 2, Role: secondary
3. Blog Yazarı Uzmanı (1003) - Priority: 2, Role: secondary  
4. Yaratıcı İçerik Uzmanı (1004) - Priority: 3, Role: supportive
5. Sosyal Medya Uzmanı (1005) - Priority: 3, Role: supportive

### 5. Response Template
- ai_features tablosundaki response_template JSON'ı son çıktı formatını belirler

---

## 📊 SEEDER ÇALIŞMA SIRASI

```
ModernBlogContentSeeder.php (Ana Koordinatör)
├── 1. AISystemPromptsSeeder.php (AIDatabaseSeeder'dan çağrılıyor)
│   ├── ai_prompts tablosuna 10 sistem prompt'u (90001-90010)
│   └── UniversalContentLengthPromptsSeeder.php (AIDatabaseSeeder'dan çağrılıyor)
│       └── ai_prompts tablosuna 5 içerik uzunluğu (90011-90015)
│   └── UniversalWritingTonePromptsSeeder.php (AIDatabaseSeeder'dan çağrılıyor)
│       └── ai_prompts tablosuna 5 yazım tonu (90021-90025)
│
├── 2. BlogContentExpertPromptsSeeder.php
│   └── ai_feature_prompts tablosuna 5 expert (1001-1005)
│
├── 3. BlogContentFeaturesSeeder.php  
│   └── ai_features tablosuna 1 blog feature (201)
│
├── 4. BlogContentFeaturePromptRelationsSeeder.php
│   └── ai_feature_prompt_relations tablosuna 5 ilişki satırı
│
└── 5. BlogWriterUniversalInputSeeder.php
    ├── ai_input_groups tablosuna 2 grup
    ├── ai_feature_inputs tablosuna 5 input
    └── ai_input_options tablosuna 0 satır (dinamik veri kullanımı)
```

## 🔄 ÇALIŞMA AKIŞI VE KOD İLİŞKİLERİ

### 1. Frontend İşleyişi

#### Form Gönderimi (`/admin/ai/features/201/test`):
```php
// Route: Modules/AI/routes/admin.php
Route::post('/features/{feature}/test', [AIFeaturesController::class, 'test']);

// Controller: AIFeaturesController@test
public function test(Request $request, $featureId)
{
    // 1. Feature'ı veritabanından al
    $feature = AIFeature::findOrFail($featureId);
    
    // 2. Kredi kontrolü yap
    $tenant = tenant();
    $tokensNeeded = $this->aiTokenService->estimateTokenCost('feature_test', [
        'feature' => $feature->name,
        'input' => $request->input
    ]);
    
    if (!$this->aiTokenService->canUseTokens($tenant, $tokensNeeded)) {
        return response()->json(['error' => 'Yetersiz kredi'], 402);
    }
    
    // 3. AIService'e gönder
    $response = $this->aiService->askFeature($feature, $request->input, $options);
    
    // 4. Yanıtı döndür
    return response()->json(['response' => $response]);
}
```

### 2. AIService İşleyişi

#### `AIService@askFeature` Metodu:
```php
// Modules/AI/app/Services/AIService.php
public function askFeature($feature, string $userInput, array $options = [])
{
    // 1. Token kontrolü (ikinci kez - güvenlik için)
    if (!$this->checkTokens()) return "Yetersiz kredi";
    
    // 2. Prompt'ları hazırla
    $systemPrompt = $this->buildFeatureSystemPrompt($feature, $options);
    
    // 3. AI Provider'a gönder (DeepSeek, OpenAI vs.)
    $apiResponse = $this->currentService->ask($messages, false);
    
    // 4. Kredi düşür
    ai_use_calculated_credits($tokenData, $providerName, [
        'usage_type' => 'feature_test',
        'tenant_id' => $tenant->id,
        'feature_slug' => $feature->slug,
        'feature_id' => $feature->id,
        'feature_name' => $feature->name,
        'description' => 'AI Feature: ' . $feature->name,
        'source' => 'ai_service_ask_feature'
    ]);
    
    // 5. Conversation kaydı oluştur
    $this->createConversationRecord($userInput, $response, 'feature_test');
    
    // 6. Debug log kaydet
    $this->logDebugInfo([...]);
    
    return $response;
}
```

### 3. Prompt Sistemi Detayı

#### `buildFeatureSystemPrompt` Metodu:
```php
protected function buildFeatureSystemPrompt($feature, $options)
{
    $prompts = [];
    
    // 1. Sistem prompt'ları (priority 1)
    $systemPrompts = Prompt::where('is_system', true)
                          ->where('is_active', true)
                          ->orderBy('priority')
                          ->get();
    
    // 2. Feature quick prompt
    if ($feature->quick_prompt) {
        $prompts[] = $feature->quick_prompt;
    }
    
    // 3. Expert prompt'lar (relations tablosundan)
    $expertPrompts = $feature->expertPrompts()
                            ->orderBy('pivot.priority')
                            ->get();
    
    foreach ($expertPrompts as $expert) {
        $prompts[] = $expert->expert_prompt;
    }
    
    // 4. User seçimleri (tone, length)
    if (isset($options['writing_tone'])) {
        $tonePrompt = Prompt::find($options['writing_tone']);
        $prompts[] = $tonePrompt->content;
    }
    
    if (isset($options['content_length'])) {
        $lengthPrompt = Prompt::find($options['content_length']);
        $prompts[] = $lengthPrompt->content;
    }
    
    // 5. Response template'i ekle
    if ($feature->response_template) {
        $prompts[] = "Yanıtını şu formatta ver: " . json_encode($feature->response_template);
    }
    
    return implode("\n\n", $prompts);
}
```

### 4. Kredi Sistemi Detayı

#### Kredi Hesaplama:
```php
// app/Helpers/AIHelper.php
function ai_use_calculated_credits($tokenData, $provider, $meta = [])
{
    // 1. Token sayısını belirle (fallback sistemi)
    $totalTokens = $tokenData['tokens_used'] 
                ?? $tokenData['total_tokens'] 
                ?? $tokenData['token_count'] 
                ?? 0;
    
    // 2. Provider çarpanını al
    $multiplier = AIProviderMultiplier::getMultiplier($provider);
    
    // 3. Kredi hesapla
    $credits = ceil($totalTokens * $multiplier);
    
    // 4. Tenant'tan düş
    $tenant = tenant();
    if ($tenant) {
        $tenant->decrement('ai_credits', $credits);
        $tenant->increment('ai_credits_used', $credits);
        
        // 5. Kullanım kaydı oluştur
        AIUsage::create([
            'tenant_id' => $tenant->id,
            'feature_id' => $meta['feature_id'] ?? null,
            'tokens_used' => $totalTokens,
            'credits_used' => $credits,
            'provider' => $provider,
            'usage_type' => $meta['usage_type'],
            'description' => $meta['description']
        ]);
    }
    
    // 6. Debug log
    Log::info('AI Credits Used', [
        'tokens' => $totalTokens,
        'credits' => $credits,
        'provider' => $provider,
        'feature' => $meta['feature_name'] ?? 'unknown'
    ]);
    
    return $credits;
}
```

### 5. Logging ve Debug Sistemi

#### Debug Dashboard için Log:
```php
protected function logDebugInfo($data)
{
    // Modules/AI/app/Services/AIService.php
    Cache::put(
        "ai_debug_{$data['tenant_id']}_{$data['feature_slug']}_" . time(),
        $data,
        now()->addHours(24)
    );
    
    // Laravel log
    Log::channel('ai_debug')->info('AI Feature Request', $data);
    
    // Database log (opsiyonel)
    if (config('ai.enable_db_logging')) {
        AIDebugLog::create($data);
    }
}
```

### 6. Conversation Kaydı

```php
protected function createConversationRecord($input, $response, $type, $meta = [])
{
    AIConversation::create([
        'tenant_id' => tenant('id'),
        'user_id' => auth()->id(),
        'feature_id' => $meta['feature_id'] ?? null,
        'type' => $type,
        'input' => $input,
        'response' => $response,
        'tokens_used' => $meta['tokens'] ?? 0,
        'credits_used' => $meta['credits'] ?? 0,
        'provider' => $this->currentProvider->name ?? 'unknown',
        'metadata' => json_encode($meta)
    ]);
}
```

## 📊 VERİTABANI İLİŞKİLERİ

### Tablo İlişki Diyagramı:
```
ai_features (Blog Feature)
    ├── ai_feature_prompt_relations (5 ilişki)
    │   └── ai_feature_prompts (5 expert prompt)
    ├── ai_input_groups (2 grup)
    │   └── ai_feature_inputs (5 input field)
    └── ai_prompts (sistem, tone, length prompt'ları)
```

### Kullanılan Tablolar ve Rolleri:

1. **ai_features** - Blog feature'ının ana kaydı
2. **ai_feature_prompts** - Expert prompt'lar (İçerik, SEO, Blog uzmanları)
3. **ai_feature_prompt_relations** - Feature ile expert'leri bağlar
4. **ai_prompts** - Sistem, ton ve uzunluk prompt'ları
5. **ai_input_groups** - Form grupları (Temel, İleri Düzey)
6. **ai_feature_inputs** - Form alanları (konu, ton, uzunluk vs.)
7. **ai_conversations** - Tüm AI konuşmaları kaydedilir
8. **ai_usage** - Kredi kullanım kayıtları
9. **ai_debug_logs** - Debug ve performans logları

## ✅ SONUÇ

Blog Yazısı Oluşturucu feature'ı:
- **7 seeder dosyası** kullanır
- **9 farklı tabloda** veri yönetir  
- **37+ satır seed verisi** ekler
- **Tamamen dinamik** - sıfır hardcode
- **Kredi sistemi entegre** - her işlem kredi düşürür
- **Detaylı loglama** - debug ve analiz için
- **Conversation tracking** - tüm konuşmalar kaydedilir
- **Fallback-free** - gerçek AI yanıtları, sahte yanıt yok














************


