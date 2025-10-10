# 🏗️ PHASE 2: MİMARİ ALTYAPI

## 📅 Tarih: 09-10.08.2025
## 👤 Sorumlu: AI Development Team  
## ⏱️ Süre: 2-3 Gün
## 📊 Durum: BAŞLAMADI

---

## 🎯 HEDEF
Kaliteli feature'lar için sağlam mimari altyapı kurmak.

---

## 🏛️ MİMARİ KOMPONENTLER

### 1️⃣ **CONTEXT ENGINE**
**Hedef**: User, Tenant, Page ve Session context'lerini yönetmek

#### **Yapılacaklar:**
```php
// 1. ContextEngine.php servisi
class ContextEngine
{
    public function getUserContext(): array;
    public function getTenantContext(): array;
    public function getPageContext(): array;
    public function getSessionContext(): array;
    public function buildCompleteContext(): array;
}

// 2. Context Collectors
- UserContextCollector
- TenantContextCollector  
- PageContextCollector
- SessionContextCollector

// 3. Context Storage
- Redis cache entegrasyonu
- TTL yönetimi
- Context versioning
```

#### **Context Veri Yapıları:**

##### **User Context**
```json
{
    "user_id": 123,
    "name": "Nurullah",
    "role": "admin",
    "permissions": ["all"],
    "preferences": {
        "language": "tr",
        "writing_style": "detailed",
        "default_length": "long"
    },
    "history": {
        "last_prompts": [],
        "favorite_features": []
    }
}
```

##### **Tenant Context**
```json
{
    "tenant_id": 1,
    "company": "TechCorp",
    "sector": "Technology",
    "brand": {
        "voice": "professional",
        "values": ["innovation", "quality"],
        "target_audience": "B2B"
    },
    "ai_profile": {
        "completed": true,
        "summary": "..."
    }
}
```

##### **Page Context**
```json
{
    "module": "pages",
    "action": "edit",
    "entity_id": 456,
    "current_content": "...",
    "available_actions": ["translate", "seo_analyze", "improve"],
    "empty_fields": ["meta_description", "en_content"]
}
```

##### **Session Context**
```json
{
    "conversation_id": "abc123",
    "message_count": 5,
    "recent_topics": ["blog", "seo"],
    "active_task": "content_creation",
    "temporary_preferences": {}
}
```

---

### 2️⃣ **SMART TEMPLATE ENGINE**
**Hedef**: Multi-level template inheritance ve dynamic rules

#### **Template Hierarchy:**
```
1. Base Template (Temel format)
   ↓
2. Category Template (Kategori özellikleri)
   ↓  
3. Feature Template (Feature'a özel)
   ↓
4. Context Template (Context'e göre)
   ↓
5. Dynamic Template (Runtime adaptation)
```

#### **Yapılacaklar:**
```php
// 1. ResponseTemplateEngine güçlendirmesi
class ResponseTemplateEngine
{
    public function loadBaseTemplate(): array;
    public function applyInheritance(array $templates): array;
    public function evaluateDynamicRules(array $context): array;
    public function renderTemplate(string $content, array $template): string;
}

// 2. Template Components
- TemplateLoader
- TemplateInheritance
- RuleEvaluator
- TemplateRenderer

// 3. Rule Engine
- LengthRules
- FormatRules
- StyleRules
- ContextRules
```

#### **Template Örneği:**
```json
{
    "base": {
        "format": "markdown",
        "language": "tr",
        "structure": {
            "has_intro": true,
            "has_body": true,
            "has_conclusion": true
        }
    },
    "feature": {
        "blog_writing": {
            "min_paragraphs": 5,
            "include_examples": true,
            "seo_optimized": true
        }
    },
    "context": {
        "if_long_requested": {
            "min_words": 1000,
            "detailed_examples": true
        }
    },
    "dynamic": {
        "adapt_to_user_style": true,
        "learn_from_feedback": true
    }
}
```

---

### 3️⃣ **FEATURE TYPE SYSTEM**
**Hedef**: 4 farklı feature tipini desteklemek

#### **Feature Tipleri:**

##### **TYPE 1: STATIC**
```php
// Sadece text input
// Örnek: Blog yazma
interface StaticFeatureInterface
{
    public function processInput(string $text): string;
    public function validateInput(string $text): bool;
}
```

##### **TYPE 2: SELECTION**
```php
// Önce seçim, sonra input
// Örnek: Çeviri (dil seçimi)
interface SelectionFeatureInterface
{
    public function getSelectionOptions(): array;
    public function processWithSelection($selection, string $text): string;
}
```

##### **TYPE 3: CONTEXT**
```php
// Mevcut sayfa/içerik kullanımı
// Örnek: "Bu sayfayı SEO analiz et"
interface ContextFeatureInterface
{
    public function extractContext(): array;
    public function processWithContext(array $context): string;
}
```

##### **TYPE 4: INTEGRATION**
```php
// Database read/write
// Örnek: "Sayfayı çevir ve kaydet"
interface IntegrationFeatureInterface
{
    public function readFromDatabase(): array;
    public function writeToDatabase(array $data): bool;
    public function processWithIntegration(): string;
}
```

#### **Yapılacaklar:**
```php
// 1. Feature Type Manager
class FeatureTypeManager
{
    public function detectType(AIFeature $feature): string;
    public function getHandler(string $type): FeatureHandlerInterface;
    public function process(AIFeature $feature, array $input): mixed;
}

// 2. Type Handlers
- StaticFeatureHandler
- SelectionFeatureHandler
- ContextFeatureHandler
- IntegrationFeatureHandler

// 3. Type-specific UI Components
- StaticFeatureUI
- SelectionFeatureUI
- ContextFeatureUI
- IntegrationFeatureUI
```

---

### 4️⃣ **TEST FRAMEWORK**
**Hedef**: Automated testing infrastructure

#### **Test Kategorileri:**
```php
// 1. Unit Tests
- ContextEngineTest
- TemplateEngineTest
- FeatureTypeTest

// 2. Integration Tests
- AIServiceIntegrationTest
- DatabaseIntegrationTest
- CacheIntegrationTest

// 3. Feature Tests
- BlogFeatureTest
- SEOFeatureTest
- TranslationFeatureTest

// 4. Performance Tests
- ResponseTimeTest
- TokenUsageTest
- ConcurrencyTest
```

#### **Test Helpers:**
```php
class AITestHelper
{
    public function mockUserContext(): array;
    public function mockTenantContext(): array;
    public function assertResponseLength(string $response, int $min): void;
    public function assertParagraphCount(string $response, int $min): void;
    public function assertNoForbiddenPhrases(string $response): void;
}
```

---

## 📋 IMPLEMENTATION PLAN

### **Day 1: Context Engine**
- [ ] ContextEngine.php servisi
- [ ] Context Collectors (4 adet)
- [ ] Redis cache entegrasyonu
- [ ] Context storage system
- [ ] Unit tests

### **Day 2: Template Engine**
- [ ] ResponseTemplateEngine güçlendirmesi
- [ ] Template inheritance system
- [ ] Rule evaluation engine
- [ ] Dynamic adaptation
- [ ] Unit tests

### **Day 3: Feature Type System**
- [ ] Feature Type Manager
- [ ] Type handlers (4 adet)
- [ ] Type detection logic
- [ ] UI components
- [ ] Integration tests

### **Day 4: Test Framework**
- [ ] Test infrastructure setup
- [ ] Test helpers
- [ ] Initial test suite
- [ ] CI/CD integration
- [ ] Documentation

---

## 🎯 BAŞARI KRİTERLERİ

### **Context Engine İçin:**
- [ ] 4 context tipi çalışıyor
- [ ] Redis cache entegre
- [ ] Context versioning aktif
- [ ] Performance < 50ms

### **Template Engine İçin:**
- [ ] Multi-level inheritance
- [ ] Dynamic rule evaluation
- [ ] Context-aware templates
- [ ] Consistent outputs

### **Feature Type System İçin:**
- [ ] 4 tip destekleniyor
- [ ] Auto-detection çalışıyor
- [ ] Type-specific handlers
- [ ] UI components hazır

### **Test Framework İçin:**
- [ ] %80+ code coverage
- [ ] Automated test runs
- [ ] Performance benchmarks
- [ ] CI/CD pipeline

---

## 📝 NOTLAR

### **Kritik Noktalar:**
- Context Engine tüm sistemin temeli
- Template Engine consistency sağlayacak
- Feature Type System flexibility verecek
- Test Framework kaliteyi garanti edecek

### **Dependencies:**
- Redis kurulu olmalı
- PHPUnit configured
- Composer packages updated
- Laravel Telescope active

---

## 🚀 SONRAKİ ADIMLAR

1. **Context Engine implementasyonu**
2. **Template Engine geliştirmesi**
3. **Feature Type System kurulumu**
4. **Test Framework setup**
5. **Phase 3'e geçiş**

---

## 📈 İLERLEME

```
Phase 2 İlerlemesi: [░░░░░░░░░░░░░░░░░░░░] 0%

⏳ Context Engine
⏳ Template Engine
⏳ Feature Type System
⏳ Test Framework
```

---

**DURUM**: Başlamadı
**TAHMİNİ BAŞLANGIÇ**: 09.08.2025
**SONRAKİ**: Phase 3 - Temizlik ve Reset