# 📋 PHASE 2: RESPONSE TEMPLATE ENGINE TEST PLANI

## Test Edilecek Dosyalar

### 1. AIResponseTemplateEngine.php (Yeni)
**Lokasyon**: `Modules/AI/app/Services/AIResponseTemplateEngine.php`
**Test Özellikleri**:
- Template parsing
- Dynamic formatting
- Section rendering
- Rule application

### 2. AIFeature Model Update
**Lokasyon**: `Modules/AI/app/Models/AIFeature.php`
**Test Özellikleri**:
- response_template JSON field
- Template validation
- Default templates

### 3. AIResponseFormatter.php (Yeni)
**Lokasyon**: `Modules/AI/app/Services/AIResponseFormatter.php`
**Test Özellikleri**:
- Monoton format kırma
- Narrative/list/structured format
- Section-based rendering

## Test Senaryoları

### Test 1: Blog Template
**Test Sayfası**: `/admin/ai/features/blog-yaz`
**Kontrol Edilecek**:
- Paragraf formatında yazı
- Numaralandırma OLMAMALI
- Giriş-gelişme-sonuç yapısı
- Doğal akış

### Test 2: SEO Template
**Test Sayfası**: `/admin/ai/features/seo-analiz`
**Kontrol Edilecek**:
- Tablo formatında skor
- Yapılandırılmış analiz
- Bullet list öneriler
- Teknik dil kullanımı

### Test 3: Kod Template
**Test Sayfası**: `/admin/ai/features/kod-uret`
**Kontrol Edilecek**:
- Code block formatı
- Açıklama bölümleri
- Syntax highlighting
- Minimal metin

## Test Database Güncellemeleri

```sql
-- Response template örnekleri
UPDATE ai_features 
SET response_template = '{
    "format": "narrative",
    "style": "professional",
    "sections": [
        {"type": "paragraph", "title": "Giriş"},
        {"type": "heading", "title": "Ana Başlıklar"},
        {"type": "paragraph", "title": "İçerik"},
        {"type": "paragraph", "title": "Sonuç"}
    ],
    "rules": ["no_numbering", "use_paragraphs"]
}'
WHERE slug = 'blog-yaz';
```

## Test Komutları

```bash
# Template validation test
php artisan ai:validate-templates

# Format testing
php artisan ai:test-format --feature=blog-yaz --format=narrative

# Before/After comparison
php artisan ai:compare-formats --feature=seo-analiz
```