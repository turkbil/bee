# 🗄️ VERİTABANI TASARIM DOKÜMANI

> **AI Blog Otomasyon Sistemi - Database Schema Design**

---

## 📋 TABLOLAR GENEL BAKIŞ

```
blog_automation_schedules     → Zamanlama kuralları
blog_automation_logs          → İşlem logları
content_strategies            → İçerik stratejileri
blog_performance_metrics      → Performans metrikleri
blog_topic_queue              → Konu kuyruğu
blog_keyword_bank             → Anahtar kelime havuzu
```

---

## 📊 TABLO DETAYLARI

### 1. `blog_automation_schedules`
**Amaç:** Hangi saatlerde, hangi konularda blog üretileceğini tanımlar

```sql
CREATE TABLE blog_automation_schedules (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT UNSIGNED NOT NULL,

    -- Zamanlama
    name VARCHAR(255) NOT NULL COMMENT 'Örn: Sabah Blog Üretimi',
    schedule_type ENUM('once', 'daily', 'weekly', 'monthly', 'custom') DEFAULT 'daily',
    run_time TIME NOT NULL COMMENT 'Örn: 06:00:00',
    run_days JSON NULL COMMENT '["monday", "wednesday", "friday"]',
    timezone VARCHAR(50) DEFAULT 'Europe/Istanbul',

    -- Konu Belirleme
    topic_source ENUM('manual', 'product_based', 'category_based', 'keyword_bank', 'trending', 'mixed') DEFAULT 'mixed',
    topic_config JSON NOT NULL COMMENT '{
        "sources": ["products", "categories"],
        "product_ids": [1,2,3],
        "category_ids": [5,8],
        "keywords": ["transpalet", "forklift"],
        "selection_method": "random|top_viewed|least_covered",
        "max_topics_per_run": 3
    }',

    -- İçerik Stratejisi
    content_strategy_id BIGINT UNSIGNED NULL,
    content_length ENUM('short', 'medium', 'long', 'custom') DEFAULT 'medium',
    word_count_min INT DEFAULT 1500,
    word_count_max INT DEFAULT 2500,

    -- AI Ayarları
    ai_provider ENUM('openai', 'anthropic', 'custom') DEFAULT 'openai',
    ai_model VARCHAR(100) DEFAULT 'gpt-4-turbo',
    ai_temperature DECIMAL(3,2) DEFAULT 0.7 COMMENT '0.0-1.0',

    -- Yayınlama
    auto_publish BOOLEAN DEFAULT FALSE,
    publish_delay_hours INT DEFAULT 0 COMMENT 'Gecikme süresi (review için)',
    default_category_id BIGINT UNSIGNED NULL,
    default_tags JSON NULL COMMENT '["otomasyonlu", "ai-generated"]',

    -- SEO
    seo_priority INT DEFAULT 5 COMMENT '1-10',
    include_schema_markup BOOLEAN DEFAULT TRUE,
    include_faq BOOLEAN DEFAULT TRUE,
    faq_question_count INT DEFAULT 5,

    -- Durum
    is_active BOOLEAN DEFAULT TRUE,
    last_run_at TIMESTAMP NULL,
    next_run_at TIMESTAMP NULL,
    total_runs INT DEFAULT 0,
    successful_runs INT DEFAULT 0,
    failed_runs INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_tenant_active (tenant_id, is_active),
    INDEX idx_next_run (next_run_at),
    INDEX idx_schedule_type (schedule_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. `blog_automation_logs`
**Amaç:** Her blog üretim işleminin detaylı kaydını tutar

```sql
CREATE TABLE blog_automation_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT UNSIGNED NOT NULL,
    schedule_id BIGINT UNSIGNED NULL COMMENT 'Hangi schedule tetikledi?',
    blog_id BIGINT UNSIGNED NULL COMMENT 'Oluşturulan blog ID',

    -- İşlem Bilgisi
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    step ENUM('topic_selection', 'content_generation', 'seo_optimization', 'image_generation', 'publishing') NULL,

    -- Konu Bilgisi
    topic_title VARCHAR(500) NULL,
    topic_slug VARCHAR(500) NULL,
    topic_source VARCHAR(100) NULL COMMENT 'product_id:123, category_id:5, keyword:transpalet',
    keywords JSON NULL COMMENT '["ana-keyword", "destek-keyword-1"]',

    -- AI Bilgileri
    ai_provider VARCHAR(50) NULL,
    ai_model VARCHAR(100) NULL,
    ai_prompt TEXT NULL COMMENT 'Kullanılan prompt',
    ai_response LONGTEXT NULL COMMENT 'AI yanıtı (başarısızsa)',
    ai_tokens_used INT NULL,
    credits_used INT NULL,

    -- Performans
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    generation_time_seconds INT NULL,

    -- Hata Bilgisi
    error_code VARCHAR(50) NULL,
    error_message TEXT NULL,
    error_trace TEXT NULL,
    retry_count INT DEFAULT 0,

    -- Metadata
    metadata JSON NULL COMMENT '{
        "word_count": 2340,
        "image_count": 5,
        "faq_count": 8,
        "internal_links": 12,
        "seo_score": 87
    }',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_tenant_status (tenant_id, status),
    INDEX idx_schedule (schedule_id),
    INDEX idx_blog (blog_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. `content_strategies`
**Amaç:** Farklı içerik stratejilerini tanımlar (SEO-focused, conversion-focused, etc.)

```sql
CREATE TABLE content_strategies (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT UNSIGNED NOT NULL,

    -- Strateji Tanımı
    name VARCHAR(255) NOT NULL COMMENT 'Örn: SEO-Focused Tutorial',
    description TEXT NULL,
    strategy_type ENUM('seo', 'conversion', 'engagement', 'educational', 'promotional', 'mixed') DEFAULT 'seo',

    -- Hedef Kitle
    target_audience VARCHAR(255) NULL COMMENT 'Örn: B2B Depo Yöneticileri',
    buyer_persona JSON NULL COMMENT '{
        "job_title": "Depo Müdürü",
        "age_range": "35-50",
        "pain_points": ["maliyet", "verimlilik"],
        "goals": ["optimize operations", "reduce costs"]
    }',

    -- İçerik Tonu ve Stili
    content_tone ENUM('professional', 'casual', 'technical', 'conversational', 'authoritative') DEFAULT 'professional',
    writing_style ENUM('tutorial', 'listicle', 'comparison', 'guide', 'faq', 'case_study') DEFAULT 'guide',

    -- İçerik Gereksinimleri
    content_length ENUM('short', 'medium', 'long') DEFAULT 'medium',
    word_count_target INT DEFAULT 2000,

    -- Yapı Gereksinimleri
    include_introduction BOOLEAN DEFAULT TRUE,
    include_table_of_contents BOOLEAN DEFAULT FALSE,
    include_key_takeaways BOOLEAN DEFAULT TRUE,
    include_faq BOOLEAN DEFAULT TRUE,
    faq_min_questions INT DEFAULT 5,
    include_cta BOOLEAN DEFAULT TRUE,
    cta_type ENUM('contact', 'product', 'newsletter', 'demo', 'custom') DEFAULT 'product',

    -- SEO Ayarları
    seo_priority INT DEFAULT 5 COMMENT '1-10',
    focus_keyword_density DECIMAL(4,2) DEFAULT 1.5 COMMENT '%1.5',
    lsi_keyword_count INT DEFAULT 10,
    internal_link_min INT DEFAULT 5,
    external_link_min INT DEFAULT 3,

    -- Schema Markup
    schema_types JSON NULL COMMENT '["Article", "FAQPage", "HowTo"]',

    -- Görsel Gereksinimleri (v2)
    featured_image_required BOOLEAN DEFAULT TRUE,
    inline_image_count INT DEFAULT 5,
    icon_usage ENUM('none', 'minimal', 'moderate', 'extensive') DEFAULT 'moderate',

    -- AI Prompt Şablonu
    custom_prompt_template TEXT NULL COMMENT 'Özel AI prompt (opsiyonel)',

    is_active BOOLEAN DEFAULT TRUE,
    usage_count INT DEFAULT 0,
    avg_seo_score DECIMAL(5,2) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_tenant_active (tenant_id, is_active),
    INDEX idx_type (strategy_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4. `blog_performance_metrics`
**Amaç:** Blog performansını takip eder (analytics entegrasyonu)

```sql
CREATE TABLE blog_performance_metrics (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    blog_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,

    -- Trafik Metrikleri
    page_views INT DEFAULT 0,
    unique_visitors INT DEFAULT 0,
    organic_visitors INT DEFAULT 0,
    direct_visitors INT DEFAULT 0,
    referral_visitors INT DEFAULT 0,
    social_visitors INT DEFAULT 0,

    -- Engagement Metrikleri
    avg_time_on_page INT NULL COMMENT 'Saniye',
    bounce_rate DECIMAL(5,2) NULL COMMENT 'Yüzde',
    scroll_depth_avg DECIMAL(5,2) NULL COMMENT 'Yüzde',

    -- SEO Metrikleri
    keyword_rankings JSON NULL COMMENT '{
        "transpalet nedir": 3,
        "manuel transpalet": 7,
        "transpalet fiyatları": 12
    }',
    featured_snippet_keywords JSON NULL COMMENT '["transpalet nedir"]',
    average_position DECIMAL(5,2) NULL,
    impressions INT DEFAULT 0,
    clicks INT DEFAULT 0,
    ctr DECIMAL(5,2) NULL COMMENT 'Click-through rate %',

    -- Conversion Metrikleri
    goal_completions INT DEFAULT 0,
    conversion_rate DECIMAL(5,2) NULL,
    leads_generated INT DEFAULT 0,

    -- Social Metrikleri
    social_shares INT DEFAULT 0,
    social_shares_breakdown JSON NULL COMMENT '{
        "facebook": 10,
        "twitter": 5,
        "linkedin": 8
    }',

    -- Backlinks
    backlinks_count INT DEFAULT 0,
    referring_domains INT DEFAULT 0,

    -- Core Web Vitals
    lcp_score DECIMAL(5,2) NULL COMMENT 'Largest Contentful Paint (seconds)',
    fid_score DECIMAL(5,2) NULL COMMENT 'First Input Delay (ms)',
    cls_score DECIMAL(5,4) NULL COMMENT 'Cumulative Layout Shift',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_blog_date (blog_id, date),
    INDEX idx_blog (blog_id),
    INDEX idx_date (date),
    INDEX idx_organic (organic_visitors)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5. `blog_topic_queue`
**Amaç:** Üretilecek blog konularını kuyruğa alır

```sql
CREATE TABLE blog_topic_queue (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT UNSIGNED NOT NULL,
    schedule_id BIGINT UNSIGNED NULL,

    -- Konu Bilgisi
    topic_title VARCHAR(500) NOT NULL,
    topic_slug VARCHAR(500) NULL,
    topic_description TEXT NULL,

    -- Kaynak
    source_type ENUM('manual', 'product', 'category', 'keyword', 'trending', 'competitor') NOT NULL,
    source_id VARCHAR(255) NULL COMMENT 'product_id:123, category_id:5',

    -- Anahtar Kelimeler
    focus_keyword VARCHAR(255) NOT NULL,
    secondary_keywords JSON NULL,
    search_volume INT NULL,
    keyword_difficulty INT NULL COMMENT '1-100',

    -- Strateji
    content_strategy_id BIGINT UNSIGNED NULL,
    priority INT DEFAULT 5 COMMENT '1-10',

    -- Zamanlama
    scheduled_for TIMESTAMP NULL,
    processing_started_at TIMESTAMP NULL,

    -- Durum
    status ENUM('queued', 'processing', 'completed', 'failed', 'cancelled') DEFAULT 'queued',
    blog_id BIGINT UNSIGNED NULL COMMENT 'Oluşturulan blog ID',
    automation_log_id BIGINT UNSIGNED NULL,

    -- Hata
    error_message TEXT NULL,
    retry_count INT DEFAULT 0,
    max_retries INT DEFAULT 3,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_tenant_status (tenant_id, status),
    INDEX idx_scheduled (scheduled_for),
    INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 6. `blog_keyword_bank`
**Amaç:** Kullanılacak anahtar kelimeleri yönetir

```sql
CREATE TABLE blog_keyword_bank (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT UNSIGNED NOT NULL,

    -- Anahtar Kelime
    keyword VARCHAR(255) NOT NULL,
    keyword_type ENUM('primary', 'secondary', 'long_tail', 'lsi') DEFAULT 'primary',

    -- SEO Metrikleri
    search_volume INT NULL,
    keyword_difficulty INT NULL COMMENT '1-100',
    cpc DECIMAL(8,2) NULL COMMENT 'Cost per click',
    competition ENUM('low', 'medium', 'high') NULL,

    -- İlişkiler
    related_keywords JSON NULL COMMENT '["keyword1", "keyword2"]',
    category_ids JSON NULL COMMENT '[1,5,8]',
    product_ids JSON NULL COMMENT '[12,34,56]',

    -- Kullanım
    usage_count INT DEFAULT 0,
    last_used_at TIMESTAMP NULL,
    current_best_rank INT NULL COMMENT 'Mevcut en iyi sıralama',

    -- Durum
    status ENUM('active', 'inactive', 'exhausted') DEFAULT 'active',
    priority INT DEFAULT 5 COMMENT '1-10',

    -- Notlar
    notes TEXT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_tenant_keyword (tenant_id, keyword),
    INDEX idx_tenant_status (tenant_id, status),
    INDEX idx_difficulty (keyword_difficulty),
    INDEX idx_search_volume (search_volume)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🔗 İLİŞKİLER (FOREIGN KEYS)

```sql
-- blog_automation_schedules
ALTER TABLE blog_automation_schedules
    ADD CONSTRAINT fk_schedule_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    ADD CONSTRAINT fk_schedule_strategy
        FOREIGN KEY (content_strategy_id) REFERENCES content_strategies(id) ON DELETE SET NULL;

-- blog_automation_logs
ALTER TABLE blog_automation_logs
    ADD CONSTRAINT fk_log_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    ADD CONSTRAINT fk_log_schedule
        FOREIGN KEY (schedule_id) REFERENCES blog_automation_schedules(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_log_blog
        FOREIGN KEY (blog_id) REFERENCES blogs(blog_id) ON DELETE SET NULL;

-- content_strategies
ALTER TABLE content_strategies
    ADD CONSTRAINT fk_strategy_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE;

-- blog_performance_metrics
ALTER TABLE blog_performance_metrics
    ADD CONSTRAINT fk_metrics_blog
        FOREIGN KEY (blog_id) REFERENCES blogs(blog_id) ON DELETE CASCADE;

-- blog_topic_queue
ALTER TABLE blog_topic_queue
    ADD CONSTRAINT fk_queue_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    ADD CONSTRAINT fk_queue_schedule
        FOREIGN KEY (schedule_id) REFERENCES blog_automation_schedules(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_queue_strategy
        FOREIGN KEY (content_strategy_id) REFERENCES content_strategies(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_queue_blog
        FOREIGN KEY (blog_id) REFERENCES blogs(blog_id) ON DELETE SET NULL;

-- blog_keyword_bank
ALTER TABLE blog_keyword_bank
    ADD CONSTRAINT fk_keyword_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE;
```

---

## 📦 ÖRNEK VERİ

### Schedule Örneği
```sql
INSERT INTO blog_automation_schedules (
    tenant_id, name, schedule_type, run_time,
    topic_source, topic_config, content_strategy_id,
    ai_provider, ai_model, auto_publish, is_active
) VALUES (
    2,
    'Sabah Blog Üretimi - Ürün Odaklı',
    'daily',
    '06:00:00',
    'product_based',
    JSON_OBJECT(
        'sources', JSON_ARRAY('products'),
        'selection_method', 'top_viewed',
        'max_topics_per_run', 1,
        'min_view_count', 100
    ),
    1,
    'openai',
    'gpt-4-turbo',
    0,
    1
);
```

### Content Strategy Örneği
```sql
INSERT INTO content_strategies (
    tenant_id, name, description, strategy_type,
    target_audience, content_tone, writing_style,
    content_length, word_count_target,
    include_faq, faq_min_questions, include_cta,
    seo_priority, is_active
) VALUES (
    2,
    'SEO-Focused Product Guide',
    'Ürün odaklı, SEO-optimize rehber içerikler',
    'seo',
    'B2B Depo Yöneticileri ve Satın Alma Müdürleri',
    'professional',
    'guide',
    'long',
    2500,
    1,
    8,
    1,
    8,
    1
);
```

### Keyword Bank Örneği
```sql
INSERT INTO blog_keyword_bank (
    tenant_id, keyword, keyword_type,
    search_volume, keyword_difficulty, competition,
    related_keywords, status, priority
) VALUES (
    2,
    'transpalet nedir',
    'primary',
    1200,
    35,
    'medium',
    JSON_ARRAY('manuel transpalet', 'elektrikli transpalet', 'transpalet fiyatları'),
    'active',
    8
);
```

---

## 🎯 İNDEKS OPTİMİZASYONU

### En Çok Kullanılacak Sorgular

```sql
-- Aktif schedule'ları getir (next_run_at'a göre)
SELECT * FROM blog_automation_schedules
WHERE tenant_id = 2
  AND is_active = 1
  AND next_run_at <= NOW()
ORDER BY next_run_at ASC;
-- INDEX: idx_tenant_active, idx_next_run

-- Başarısız logları getir (retry için)
SELECT * FROM blog_automation_logs
WHERE tenant_id = 2
  AND status = 'failed'
  AND retry_count < 3
ORDER BY created_at DESC;
-- INDEX: idx_tenant_status

-- En iyi performans gösteren blogları bul
SELECT blog_id, SUM(organic_visitors) as total_organic
FROM blog_performance_metrics
WHERE date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY blog_id
ORDER BY total_organic DESC
LIMIT 10;
-- INDEX: idx_date, idx_organic

-- Bekleyen konu kuyruğu
SELECT * FROM blog_topic_queue
WHERE tenant_id = 2
  AND status = 'queued'
  AND scheduled_for <= NOW()
ORDER BY priority DESC, scheduled_for ASC;
-- INDEX: idx_tenant_status, idx_scheduled, idx_priority
```

---

## 📊 VERİ AKIŞ DİYAGRAMI

```
1. Schedule Tetiklenir (Cron Job)
   ↓
2. Topic Belirlenir (TopicSelectorService)
   ↓
3. Topic Queue'ya Eklenir (blog_topic_queue)
   ↓
4. AI Content Generate Başlar (AIBlogGeneratorService)
   ↓ [Log: processing]
5. Blog Oluşturulur (blogs tablosu)
   ↓
6. SEO Optimize Edilir (seo_settings)
   ↓ [Log: completed]
7. Yayınlanır / Review Bekler
   ↓
8. Performance Tracking Başlar (blog_performance_metrics)
```

---

**Son Güncelleme:** 2025-11-14
**Versiyon:** 1.0-DESIGN
