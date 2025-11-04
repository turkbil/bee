# 🛠️ Skill Creator & Template Skill - Kapsamlı Kılavuz

**Tarih:** 2025-10-17
**Skill Versiyonları:** skill-creator v1.0, template-skill v1.0
**Proje:** Laravel Multi-Tenant CMS

---

## 📚 İçindekiler

1. [Skill Creator Nedir?](#skill-creator-nedir)
2. [Template Skill Nedir?](#template-skill-nedir)
3. [Skill Anatomisi](#skill-anatomisi)
4. [Skill Oluşturma Süreci (6 Adım)](#skill-oluşturma-süreci)
5. [Laravel Projesi için Özel Skill Örnekleri](#laravel-projesi-için-özel-skill-örnekleri)
6. [Gerçek Kullanım Senaryoları](#gerçek-kullanım-senaryoları)
7. [Progressive Disclosure Prensibi](#progressive-disclosure-prensibi)
8. [Komut Referansı](#komut-referansı)
9. [Best Practices](#best-practices)

---

## 🎯 Skill Creator Nedir?

### Tanım
**Skill Creator**, yeni özel skill'ler oluşturmak için **interaktif rehber skill**'dir.

### Ana Özellikler

✅ **İnteraktif Guidance**: Soru-cevap formatıyla skill oluşturur
✅ **Otomatik Template**: SKILL.md ve klasör yapısını hazırlar
✅ **Validation**: Skill'i kontrol eder, hataları bildirir
✅ **Packaging**: Paylaşılabilir .zip dosyası oluşturur
✅ **Iteration Support**: Mevcut skill'leri günceller

### Ne Zaman Kullanılır?

```
✅ "Laravel Livewire component generator skill'i oluştur"
✅ "Database seeder oluşturucu skill'i yap"
✅ "AI prompt optimizer skill'i geliştir"
✅ "Özel bir skill oluşturmak istiyorum"
✅ "Bu skill'i güncelle ve iyileştir"
```

### Yetenekleri

| Özellik | Açıklama |
|---------|----------|
| **Init Skill** | Yeni skill klasörü + SKILL.md oluşturur |
| **Validate** | Skill yapısını kontrol eder |
| **Package** | Zip dosyası oluşturur (paylaşım için) |
| **Iterate** | Mevcut skill'i günceller |

---

## 📋 Template Skill Nedir?

### Tanım
**Template Skill**, yeni skill oluştururken başlangıç noktası olarak kullanılan **boş template**'dir.

### İçeriği

```
template-skill/
└── SKILL.md (sadece YAML header + placeholder)
```

**SKILL.md içeriği:**
```markdown
---
name: template-skill
description: Replace with description of the skill and when Claude should use it.
---

# Insert instructions below
```

### Ne İşe Yarar?

1. **Manuel skill oluşturma** için başlangıç
2. **Skill formatını** gösterir
3. **YAML header** yapısını örnekler

### Kullanım

**Yöntem 1: Manuel Kopyalama**
```bash
cp -r ~/.claude/skills/template-skill ~/.claude/skills/my-new-skill
nano ~/.claude/skills/my-new-skill/SKILL.md
```

**Yöntem 2: skill-creator Kullan (ÖNERİLEN)**
```
"skill-creator ile yeni bir skill oluştur"
```

---

## 🏗️ Skill Anatomisi

### Temel Yapı

```
skill-name/
├── SKILL.md              (zorunlu)
│   ├── YAML frontmatter  (zorunlu)
│   │   ├── name:         (zorunlu)
│   │   └── description:  (zorunlu)
│   └── Markdown body     (zorunlu)
│
└── Bundled Resources     (opsiyonel)
    ├── scripts/          (executable kod)
    ├── references/       (dökümanlar)
    └── assets/           (template'ler, görseller)
```

---

### 1. SKILL.md (Zorunlu)

**YAML Frontmatter:**
```yaml
---
name: my-laravel-helper
description: Laravel modül geliştirme için yardımcı skill. Page pattern'ini takip eden controller, model, migration ve view oluşturur. Livewire component'leri için kullanılır.
---
```

**Markdown Body:**
```markdown
# My Laravel Helper

## Overview
Laravel projesi için modül geliştirme skill'i.

## Usage
1. Modül adını sor
2. Page pattern'i kullan
3. Dosyaları oluştur
```

**Metadata Quality (ÖNEMLİ):**
- `name` ve `description` Claude'un **hangi durumlarda** skill'i yükleyeceğini belirler
- **Spesifik** ol: "Helper skill" ❌ → "Laravel Livewire component generator" ✅
- **3. şahıs** kullan: "Use this..." ❌ → "This skill should be used when..." ✅

---

### 2. Bundled Resources (Opsiyonel)

#### scripts/ - Executable Kod

**Ne İçin:**
- Tekrar tekrar yazılan kod
- Deterministik güvenilirlik gereken işlemler
- Token tasarrufu

**Örnekler:**
```
scripts/
├── rotate_pdf.py           (PDF döndürme)
├── generate_seeder.py      (Laravel seeder generator)
└── optimize_images.sh      (Görsel optimizasyon)
```

**Avantajlar:**
- ✅ Context'e yüklenmeden çalıştırılabilir
- ✅ Token-efficient
- ✅ Deterministik sonuç

**Ne Zaman Ekle:**
- Aynı kod sürekli yeniden yazılıyorsa
- Deterministik sonuç gerekiyorsa
- Karmaşık algoritma varsa

---

#### references/ - Dökümanlar

**Ne İçin:**
- API dökümanları
- Database schema'ları
- Domain bilgisi
- Workflow rehberleri

**Örnekler:**
```
references/
├── api_docs.md             (API referansı)
├── database_schema.md      (DB yapısı)
├── workflows.md            (İş akışları)
└── policies.md             (Şirket politikaları)
```

**Avantajlar:**
- ✅ SKILL.md'yi kısa tutar
- ✅ Sadece gerektiğinde yüklenir
- ✅ Context window'u tıkamaz

**Best Practice:**
- Büyük dosyalar (>10k kelime) için grep pattern ekle
- Bilgi tekrarından kaçın (ya SKILL.md'de ya references'da)
- Detaylı bilgiyi references'a taşı

---

#### assets/ - Template'ler ve Dosyalar

**Ne İçin:**
- Output'ta kullanılacak dosyalar
- Template'ler
- Görseller, fontlar
- Boilerplate kod

**Örnekler:**
```
assets/
├── logo.png                (Marka görseli)
├── template.pptx           (PowerPoint template)
├── boilerplate/            (Başlangıç kod)
│   ├── index.html
│   └── style.css
└── font.ttf                (Özel font)
```

**Avantajlar:**
- ✅ Context'e yüklenmez
- ✅ Direkt kullanılır/kopyalanır
- ✅ Output resources'ı ayırır

**Ne Zaman Ekle:**
- Output'ta kullanılacak dosyalar varsa
- Template gerekiyorsa
- Boilerplate kod tekrar ediyorsa

---

## 🚀 Skill Oluşturma Süreci (6 Adım)

### Adım 1: Concrete Examples ile Anlamak

**Amaç:** Skill'in nasıl kullanılacağını net örneklerle belirle

**Sorulacak Sorular:**
```
❓ "Bu skill hangi fonksiyonları desteklemeli?"
❓ "Kullanım örnekleri neler?"
❓ "Hangi durumlar bu skill'i tetiklemeli?"
❓ "Kullanıcı ne dediğinde bu skill aktif olmalı?"
```

**Örnek - PDF Editor Skill:**
```
Q: "PDF ile ne yapmak istiyorsun?"
A: "PDF döndürme, birleştirme, sayfa çıkarma"

Q: "Örnek kullanım nasıl olacak?"
A: "Bu PDF'i 90 derece döndür"
A: "Bu iki PDF'i birleştir"
A: "PDF'den 3-5 arası sayfaları çıkar"
```

**Sonuç:** Skill'in yapması gerekenlerin net listesi

---

### Adım 2: Reusable Contents Planlama

**Amaç:** Her örnek için hangi kaynaklar gerekli?

**Analiz:**
1. Her örneği sıfırdan nasıl yaparsın?
2. Hangi scripts/references/assets yardımcı olur?

**Örnek - PDF Editor Skill:**

| Kullanım Örneği | Analiz | Gerekli Kaynak |
|-----------------|--------|----------------|
| "PDF döndür" | Her seferinde kod yazıyorum | `scripts/rotate_pdf.py` |
| "PDF birleştir" | Her seferinde kod yazıyorum | `scripts/merge_pdf.py` |
| "Sayfa çıkar" | Her seferinde kod yazıyorum | `scripts/extract_pages.py` |

**Örnek - BigQuery Skill:**

| Kullanım Örneği | Analiz | Gerekli Kaynak |
|-----------------|--------|----------------|
| "Bugün kaç user login oldu?" | Schema'yı keşfetmem lazım | `references/schema.md` |
| "Satış analizi yap" | Table relationships lazım | `references/relationships.md` |

**Sonuç:** scripts/, references/, assets/ listesi

---

### Adım 3: Skill'i Initialize Et

**Amaç:** Otomatik template oluştur

**Komut:**
```bash
~/.claude/skills/skill-creator/scripts/init_skill.py <skill-adi> --path ~/.claude/skills
```

**Örnek:**
```bash
~/.claude/skills/skill-creator/scripts/init_skill.py laravel-module-generator --path ~/.claude/skills
```

**Oluşturulan Yapı:**
```
laravel-module-generator/
├── SKILL.md                (TODO placeholder'lı)
├── scripts/
│   └── example.py          (örnek script)
├── references/
│   └── api_reference.md    (örnek referans)
└── assets/
    └── example_asset.txt   (örnek asset)
```

**Output:**
```
✅ Created skill directory: ~/.claude/skills/laravel-module-generator
✅ Created SKILL.md
✅ Created scripts/example.py
✅ Created references/api_reference.md
✅ Created assets/example_asset.txt

Next steps:
1. Edit SKILL.md to complete the TODO items
2. Customize or delete the example files
3. Run validator when ready
```

---

### Adım 4: Skill'i Düzenle

**Amaç:** Template'i gerçek içerikle doldur

#### 4.1: Reusable Resources'ı Ekle

**Adım 2'de** belirlediğin kaynakları ekle:

```bash
# Scripts ekle
nano ~/.claude/skills/laravel-module-generator/scripts/generate_controller.py

# References ekle
nano ~/.claude/skills/laravel-module-generator/references/page_pattern.md

# Assets ekle
cp -r boilerplate/ ~/.claude/skills/laravel-module-generator/assets/
```

**Gereksiz örnek dosyaları sil:**
```bash
rm ~/.claude/skills/laravel-module-generator/scripts/example.py
rm ~/.claude/skills/laravel-module-generator/references/api_reference.md
```

---

#### 4.2: SKILL.md'yi Güncelle

**Yazım Stili:** **Imperative/Infinitive form** (emir kipi)

✅ **Doğru:**
```markdown
To create a component, run the generator script.
```

❌ **Yanlış:**
```markdown
You should run the generator script.
If you need to create a component...
```

**Cevaplanması Gereken Sorular:**

1️⃣ **Skill'in amacı nedir?** (2-3 cümle)
```markdown
## Overview
This skill generates Laravel modules following the Page pattern.
It creates controller, model, migration, views, and routes.
```

2️⃣ **Ne zaman kullanılmalı?**
```markdown
## When to Use
- Creating new Laravel CRUD modules
- Following Page pattern structure
- Generating Livewire components
```

3️⃣ **Pratikte nasıl kullanılır?**
```markdown
## Usage

### Step 1: Get Module Name
Ask user for module name (singular, PascalCase)

### Step 2: Run Generator
Execute scripts/generate_module.py with module name

### Step 3: Review Output
Check generated files in references/page_pattern.md
```

---

### Adım 5: Skill'i Package'le

**Amaç:** Validate + Zip dosyası oluştur

**Komut:**
```bash
~/.claude/skills/skill-creator/scripts/package_skill.py ~/.claude/skills/laravel-module-generator
```

**Ne Yapar:**

1️⃣ **Validation (Otomatik)**
```
✅ YAML frontmatter formatı
✅ Required fields (name, description)
✅ Skill naming conventions
✅ Directory structure
✅ Description quality
✅ File organization
```

2️⃣ **Packaging (Validation geçerse)**
```
✅ laravel-module-generator.zip oluşturur
✅ Tüm dosyaları içerir
✅ Directory yapısını korur
```

**Output:**
```
✅ Validation passed
✅ Package created: laravel-module-generator.zip
📦 Ready for distribution
```

**Hata varsa:**
```
❌ Validation failed:
  - Description too short (min 50 chars)
  - Missing YAML field: name
  - Invalid directory structure
```

---

### Adım 6: Iterate (İyileştir)

**Amaç:** Skill'i test et ve güncelle

**Workflow:**
```
1. Skill'i gerçek görevde kullan
2. Zorlukları/verimsizlikleri not et
3. SKILL.md veya resources'ı güncelle
4. Tekrar test et
```

**Örnek Iteration:**

**İlk Test:**
```
Kullanıcı: "Blog modülü oluştur"
Claude: [migration oluşturdu ama timestamps unuttu]
```

**İyileştirme:**
```markdown
# SKILL.md'ye ekle:

## Migration Best Practices
Always include timestamps():
$table->timestamps();
```

**İkinci Test:**
```
Kullanıcı: "Portfolio modülü oluştur"
Claude: [timestamp'leri doğru ekledi] ✅
```

---

## 🎨 Laravel Projesi için Özel Skill Örnekleri

### 1. Laravel Module Generator Skill

**Amaç:** Page pattern'i takip eden modül oluşturucu

**Kullanım:**
```
"Blog modülü oluştur. Page pattern ile."
```

**Yapısı:**
```
laravel-module-generator/
├── SKILL.md
├── scripts/
│   ├── generate_controller.py
│   ├── generate_model.py
│   ├── generate_migration.py
│   └── generate_views.py
├── references/
│   ├── page_pattern.md          (Proje Page pattern dökümanı)
│   ├── livewire_patterns.md     (Livewire best practices)
│   └── module_structure.md      (Klasör yapısı)
└── assets/
    ├── controller_template.php
    ├── model_template.php
    └── views/
        ├── index.blade.php
        ├── create.blade.php
        └── show.blade.php
```

**SKILL.md Örneği:**
```markdown
---
name: laravel-module-generator
description: Laravel modül oluşturma skill'i. Page pattern'ini takip ederek controller, model, migration, view ve route oluşturur. Livewire component'leri için kullanılır.
---

# Laravel Module Generator

## Overview
Generates complete Laravel CRUD modules following the Page pattern.
Creates controller, model, migration, views, routes, and optionally Livewire components.

## When to Use
- User requests "create [Module] module"
- User requests "generate CRUD for [Entity]"
- User mentions "Page pattern"
- User wants Livewire component

## Workflow

### Step 1: Gather Requirements
Ask user:
- Module name (singular, PascalCase)
- Database fields
- Relationships (if any)
- Frontend theme (ixtif/default)

### Step 2: Generate Files
Execute scripts in order:
1. `scripts/generate_migration.py` - Database schema
2. `scripts/generate_model.py` - Model with relationships
3. `scripts/generate_controller.py` - Page pattern controller
4. `scripts/generate_views.py` - Blade views

### Step 3: Update Routes
Add routes to:
- Modules/[Module]/routes/web.php (frontend)
- Modules/[Module]/routes/admin.php (backend)

### Step 4: Reference Check
Verify against `references/page_pattern.md` standards

## Resources

### scripts/generate_controller.py
Generates Page pattern controller with:
- JSON multilang support
- SEO fields
- Tenant scope
- Helper methods

### references/page_pattern.md
Complete Page pattern documentation:
- JSON structure
- SEO fields
- Multilang handling
- Tenant management

### assets/controller_template.php
Base controller template with:
- Multilang methods
- SEO helpers
- Tenant scope
- CRUD boilerplate
```

---

### 2. AI Prompt Optimizer Skill

**Amaç:** Projede mevcut AI sistemi için prompt optimizasyonu

**Kullanım:**
```
"Shop ürün araması için AI prompt'u optimize et"
```

**Yapısı:**
```
ai-prompt-optimizer/
├── SKILL.md
├── scripts/
│   ├── analyze_prompt.py        (Prompt analizi)
│   ├── optimize_tokens.py       (Token optimizasyonu)
│   └── test_prompt.py           (Prompt test)
├── references/
│   ├── project_ai_system.md     (Mevcut AI sistemi)
│   ├── optimization_rules.md    (Optimizasyon kuralları)
│   └── prompt_templates.md      (Template'ler)
└── assets/
    └── tested_prompts/          (Test edilmiş prompt'lar)
```

**Özellikler:**
- ✅ Mevcut prompt'u analiz eder
- ✅ Token kullanımını optimize eder
- ✅ Context management yapar
- ✅ AI chat widget ile entegre
- ✅ ProductSearchService için özel

---

### 3. Database Seeder Generator Skill

**Amaç:** Shop products için bulk seeder oluşturucu

**Kullanım:**
```
"Elektronik kategorisi için 50 ürün seeder'ı oluştur"
```

**Yapısı:**
```
db-seeder-generator/
├── SKILL.md
├── scripts/
│   ├── generate_seeder.py       (Seeder generator)
│   ├── faker_helper.py          (Faker wrapper)
│   └── bulk_insert.py           (Bulk insert optimizer)
├── references/
│   ├── shop_schema.md           (Shop database schema)
│   ├── relationships.md         (Model relationships)
│   └── seeder_patterns.md       (Seeder patterns)
└── assets/
    └── seeder_templates/
        ├── shop_product.php
        ├── shop_category.php
        └── shop_brand.php
```

**Özellikler:**
- ✅ Faker ile gerçekçi veri
- ✅ Relationship'leri otomatik
- ✅ Bulk insert optimize
- ✅ Tenant-aware
- ✅ Görsel URL'leri dahil

---

### 4. Tenant Management Skill

**Amaç:** Tenant ekleme/yönetim otomasyonu

**Kullanım:**
```
"Yeni tenant ekle: example.com"
```

**Yapısı:**
```
tenant-management/
├── SKILL.md
├── scripts/
│   ├── create_tenant.py         (Tenant oluşturma)
│   ├── plesk_config.py          (Plesk ayarları)
│   └── test_tenant.py           (Tenant test)
├── references/
│   ├── tenant_setup.md          (Tenant kurulum)
│   ├── plesk_commands.md        (Plesk komutları)
│   └── troubleshooting.md       (Sorun giderme)
└── assets/
    └── checklists/
        └── tenant_checklist.md
```

**Özellikler:**
- ✅ Plesk alias oluşturur
- ✅ Tinker ile tenant + domain ekler
- ✅ Config günceller
- ✅ SEO redirect'i kapatır
- ✅ Test yapar

---

### 5. Livewire Component Builder Skill

**Amaç:** Livewire component oluşturucu

**Kullanım:**
```
"Product filter için Livewire component oluştur"
```

**Yapısı:**
```
livewire-component-builder/
├── SKILL.md
├── scripts/
│   ├── generate_component.py    (Component generator)
│   ├── generate_test.py         (Test generator)
│   └── wire_helper.py           (Wire:model helper)
├── references/
│   ├── livewire_patterns.md     (Livewire patterns)
│   ├── alpine_integration.md    (Alpine.js entegrasyon)
│   └── best_practices.md        (Best practices)
└── assets/
    └── components/
        ├── base_component.php
        └── views/
            └── base.blade.php
```

**Özellikler:**
- ✅ Class + View oluşturur
- ✅ Alpine.js entegrasyonu
- ✅ Wire:model helpers
- ✅ Test file oluşturur
- ✅ Tabler.io/Tailwind uyumlu

---

### 6. API Documentation Generator Skill

**Amaç:** Laravel routes'lardan otomatik API docs

**Kullanım:**
```
"Tüm modüllerin API dökümanlarını oluştur"
```

**Yapısı:**
```
api-doc-generator/
├── SKILL.md
├── scripts/
│   ├── parse_routes.py          (Route parser)
│   ├── extract_docblocks.py     (Docblock extractor)
│   └── generate_openapi.py      (OpenAPI spec generator)
├── references/
│   ├── api_standards.md         (API standartları)
│   └── openapi_spec.md          (OpenAPI spesifikasyonu)
└── assets/
    └── templates/
        ├── api_doc.md
        └── postman_collection.json
```

**Özellikler:**
- ✅ Routes'lardan endpoint'leri çıkarır
- ✅ Controller docblock'ları parse eder
- ✅ OpenAPI spec oluşturur
- ✅ Postman collection export
- ✅ Markdown döküman oluşturur

---

## 🌟 Gerçek Kullanım Senaryoları

### Senaryo 1: Laravel Module Generator Oluşturma

**Durum:** Her yeni modül için aynı dosyaları manuel oluşturuyoruz.

**Çözüm:** Laravel Module Generator Skill

**Adımlar:**

```
1. Sen: "Laravel modül generator skill'i oluştur"

2. Claude (skill-creator aktif):
   - "Hangi dosyaları oluşturmalı? (controller, model, migration...)"
   - "Page pattern'i mi takip edecek?"
   - "Livewire component gerekiyor mu?"
   - "Hangi theme'leri desteklesin?"

3. Sen:
   - "Controller, Model, Migration, Views, Routes"
   - "Evet, Page pattern"
   - "Evet, opsiyonel Livewire"
   - "ixtif ve default theme"

4. Claude:
   - Init skill çalıştırır
   - SKILL.md oluşturur
   - scripts/ klasörüne generator'lar ekler
   - references/ klasörüne Page pattern dökümanı ekler
   - assets/ klasörüne template'leri ekler
   - Validate eder
   - Package oluşturur

5. Sonuç:
   ✅ laravel-module-generator.zip hazır
   ✅ ~/.claude/skills/laravel-module-generator/ aktif
```

**Kullanım:**
```
Sen: "Product modülü oluştur"

Claude (laravel-module-generator aktif):
- "Hangi alanlar olsun? (name, description, price...)"
- scripts/generate_migration.py çalıştırır
- scripts/generate_model.py çalıştırır
- scripts/generate_controller.py çalıştırır
- scripts/generate_views.py çalıştırır
- Route'ları ekler
- ✅ Product modülü hazır!
```

---

### Senaryo 2: AI Prompt Optimizer Oluşturma

**Durum:** AI chat prompt'ları optimize değil, token israfı var.

**Çözüm:** AI Prompt Optimizer Skill

**Adımlar:**

```
1. Sen: "AI prompt optimizer skill'i oluştur.
        Projemizdeki ProductSearchService ve ChatWidgetService
        için özel olsun."

2. Claude (skill-creator aktif):
   - "Hangi prompt'ları optimize edeceğiz?"
   - "Token limitleri neler?"
   - "Context management kuralları?"

3. Sen:
   - "ProductSearchService optimizePrompt metodu"
   - "4096 token max"
   - "Dynamic context, category-based"

4. Claude:
   - Mevcut OptimizedPromptService'i analiz eder
   - Optimization rules belgeler
   - Test script'i ekler
   - Template prompt'lar hazırlar

5. Sonuç:
   ✅ ai-prompt-optimizer skill'i hazır
```

**Kullanım:**
```
Sen: "Shop ürün araması prompt'unu optimize et"

Claude (ai-prompt-optimizer aktif):
- Mevcut prompt'u okur
- Token kullanımını analiz eder
- Gereksiz context'i kırpar
- Category-based optimization uygular
- Optimize edilmiş prompt'u test eder
- ✅ Token kullanımı: 1200 → 650 (46% azalma)
```

---

### Senaryo 3: Database Seeder Generator

**Durum:** 50+ shop product seeder dosyası manuel yazıldı.

**Çözüm:** Database Seeder Generator Skill

**Adımlar:**

```
1. Sen: "DB seeder generator skill'i oluştur.
        Shop products için bulk seeder üretsin."

2. Claude:
   - Shop schema'sını analiz eder
   - Faker patterns'lerini dökümanlar
   - Bulk insert optimizer ekler
   - Tenant-aware seeder template hazırlar

3. Sonuç:
   ✅ db-seeder-generator skill'i hazır
```

**Kullanım:**
```
Sen: "Elektronik kategorisi için 100 ürün seeder'ı oluştur"

Claude (db-seeder-generator aktif):
- Shop schema'sını okur (references/shop_schema.md)
- Elektronik kategorisine uygun ürünler üretir
- Brand, category relationship'leri kurar
- Görsel URL'leri ekler
- Tenant2 için seeder dosyası oluşturur
- ✅ LitefElectronicProducts100Seeder.php hazır
```

---

## 🧠 Progressive Disclosure Prensibi

### 3 Seviyeli Yükleme Sistemi

**Amaç:** Context window'u verimli kullan

```
┌────────────────────────────────────────┐
│ Level 1: Metadata (Her zaman)         │
│ - name                                 │
│ - description                          │
│ (~100 kelime)                          │
├────────────────────────────────────────┤
│ Level 2: SKILL.md Body (Trigger)      │
│ - Workflow                             │
│ - Instructions                         │
│ (<5k kelime)                           │
├────────────────────────────────────────┤
│ Level 3: Bundled Resources (As Needed)│
│ - scripts/ (execute without loading)  │
│ - references/ (load when needed)      │
│ - assets/ (use in output)             │
│ (Unlimited)                            │
└────────────────────────────────────────┘
```

### Örnek Flow

**1. Metadata Always Loaded:**
```yaml
name: laravel-module-generator
description: Laravel modül oluşturma...
```
→ Claude her zaman bu bilgiyi görür (100 kelime)

**2. SKILL.md Body Triggered:**
```
Kullanıcı: "Blog modülü oluştur"
→ Claude "module" kelimesini görür
→ laravel-module-generator skill aktif
→ SKILL.md body yüklenir (<5k kelime)
```

**3. Resources As Needed:**
```
Claude: [SKILL.md'yi okur]
Claude: [scripts/generate_controller.py'yi çalıştırır - context'e yüklemeden]
Claude: [references/page_pattern.md'yi okur - sadece gerekli kısım]
Claude: [assets/controller_template.php'yi kopyalar]
```

### Optimizasyon Stratejisi

**SKILL.md'yi Kısa Tut:**
```markdown
❌ Kötü: Tüm API dökümanını SKILL.md'ye yaz (10k kelime)
✅ İyi: API dökümanını references/api.md'ye koy, SKILL.md'de:
   "Check references/api.md for complete API documentation"
```

**References'ı Akıllıca Kullan:**
```markdown
# SKILL.md
For database schema details, read references/schema.md
Use grep pattern: "table: users" to find user table
```

**Scripts'i Context-Free Çalıştır:**
```python
# scripts/generate_migration.py
# Bu script context'e yüklenmeden çalışır
# Token tasarrufu: ~500 token
```

---

## 📖 Komut Referansı

### init_skill.py

**Amaç:** Yeni skill initialize et

**Syntax:**
```bash
init_skill.py <skill-name> --path <output-path>
```

**Örnekler:**
```bash
# Laravel helper skill oluştur
~/.claude/skills/skill-creator/scripts/init_skill.py laravel-helper --path ~/.claude/skills

# AI optimizer skill oluştur
~/.claude/skills/skill-creator/scripts/init_skill.py ai-optimizer --path ~/.claude/skills

# Custom lokasyonda oluştur
~/.claude/skills/skill-creator/scripts/init_skill.py my-skill --path /custom/path
```

**Output:**
```
🚀 Initializing skill: laravel-helper
   Location: ~/.claude/skills

✅ Created skill directory: ~/.claude/skills/laravel-helper
✅ Created SKILL.md
✅ Created scripts/example.py
✅ Created references/api_reference.md
✅ Created assets/example_asset.txt

Next steps:
1. Edit SKILL.md to complete the TODO items
2. Customize or delete the example files
3. Run validator when ready
```

---

### package_skill.py

**Amaç:** Skill'i validate et ve package'le

**Syntax:**
```bash
package_skill.py <skill-path> [output-directory]
```

**Örnekler:**
```bash
# Default output (skill klasörü içinde)
~/.claude/skills/skill-creator/scripts/package_skill.py ~/.claude/skills/laravel-helper

# Custom output directory
~/.claude/skills/skill-creator/scripts/package_skill.py ~/.claude/skills/laravel-helper ./dist
```

**Validation Kontrolleri:**
```
✅ YAML frontmatter formatı
✅ Required fields (name, description)
✅ Skill naming conventions
✅ Directory structure
✅ Description completeness (min 50 chars)
✅ File organization
✅ Resource references
```

**Başarılı Output:**
```
✅ Validation passed
✅ Package created: laravel-helper.zip
📦 Size: 45 KB
📦 Ready for distribution

Files included:
- SKILL.md
- scripts/generate_controller.py
- scripts/generate_model.py
- references/page_pattern.md
- assets/controller_template.php
```

**Hatalı Output:**
```
❌ Validation failed:

Errors:
1. Description too short (35/50 chars minimum)
2. Missing required field: name in YAML frontmatter
3. Invalid directory structure: missing SKILL.md
4. Referenced file not found: scripts/missing.py

Fix errors and run again.
```

---

### quick_validate.py

**Amaç:** Hızlı validation (packaging olmadan)

**Syntax:**
```bash
quick_validate.py <skill-path>
```

**Örnek:**
```bash
~/.claude/skills/skill-creator/scripts/quick_validate.py ~/.claude/skills/laravel-helper
```

**Output:**
```
🔍 Validating: laravel-helper

✅ YAML frontmatter: OK
✅ Required fields: OK
✅ Naming conventions: OK
✅ Directory structure: OK
✅ Description quality: OK
✅ Resource references: OK

✅ Validation passed: Ready to package
```

---

## ✨ Best Practices

### 1. Description Quality

**Bad ❌:**
```yaml
description: Helper skill
```

**Good ✅:**
```yaml
description: Laravel modül oluşturma skill'i. Page pattern'ini takip ederek controller, model, migration, view ve route oluşturur. Livewire component'leri için kullanılır.
```

**Kurallar:**
- ✅ Minimum 50 karakter
- ✅ Ne yaptığını açıkla
- ✅ Ne zaman kullanılır belirt
- ✅ Spesifik örnekler ver
- ✅ 3. şahıs kullan

---

### 2. SKILL.md Structure

**Preferred Pattern:**

**Workflow-Based (Sequential):**
```markdown
## Overview
## Workflow Decision Tree
## Step 1: Gather Input
## Step 2: Generate Files
## Step 3: Validate Output
## Resources
```

**Task-Based (Operations):**
```markdown
## Overview
## Quick Start
## Task 1: Create Module
## Task 2: Update Module
## Task 3: Delete Module
## Resources
```

---

### 3. Writing Style

**Imperative/Infinitive Form:**

✅ **Doğru:**
```markdown
To create a module, run the generator.
Execute the script with module name.
Check references/page_pattern.md for details.
```

❌ **Yanlış:**
```markdown
You should run the generator.
If you want to create a module, you can...
Please execute the script...
```

---

### 4. Resource Organization

**scripts/**
```
✅ Executable files (.py, .sh)
✅ Deterministic operations
✅ Repeatedly rewritten code
❌ Static documentation
❌ Template files
```

**references/**
```
✅ Documentation (.md)
✅ API specs
✅ Database schemas
✅ Workflow guides
❌ Executable code
❌ Output templates
```

**assets/**
```
✅ Templates (.pptx, .docx)
✅ Images, fonts
✅ Boilerplate code
✅ Output resources
❌ Documentation
❌ Executable scripts
```

---

### 5. Context Window Management

**Keep SKILL.md Lean:**
```markdown
# SKILL.md (~2k kelime)
Overview + Quick instructions + Resource pointers

# references/detailed_guide.md (~10k kelime)
Detailed step-by-step guide
```

**Large Files:**
```markdown
# SKILL.md
For database schema, search references/schema.md
Use grep pattern: "table: users"
```

---

### 6. Iteration Workflow

```
1. Create skill
   ↓
2. Test on real tasks
   ↓
3. Notice inefficiencies
   ↓
4. Update SKILL.md or resources
   ↓
5. Test again
   ↓
6. Repeat until satisfactory
```

---

## 🎯 Özet

### skill-creator Özeti

| Özellik | Açıklama |
|---------|----------|
| **Amaç** | Yeni özel skill'ler oluşturma |
| **Yöntem** | İnteraktif soru-cevap + otomatik template |
| **Tools** | init_skill.py, package_skill.py, quick_validate.py |
| **Output** | Distributable .zip file |
| **Kullanım** | "Yeni skill oluştur" dediğinde otomatik aktif |

### template-skill Özeti

| Özellik | Açıklama |
|---------|----------|
| **Amaç** | Manuel skill oluşturma başlangıcı |
| **İçerik** | Boş SKILL.md + YAML header |
| **Kullanım** | Copy-paste + manuel düzenleme |
| **Alternatif** | skill-creator kullan (daha kolay) |

---

## 🚀 Hemen Dene!

### 1. İlk Skill'ini Oluştur

```
"skill-creator ile Laravel helper skill'i oluştur.
Bu skill, Page pattern'ini takip eden modül oluştursun."
```

### 2. AI Optimizer Skill

```
"AI prompt optimizer skill'i oluştur.
ProductSearchService için optimize etsin."
```

### 3. Seeder Generator Skill

```
"Database seeder generator skill'i oluştur.
Shop products için bulk seeder üretsin."
```

---

## 📞 İhtiyacın Olursa

**Skill oluşturma:**
```
"skill-creator ile [skill adı] oluştur"
```

**Mevcut skill'i güncelle:**
```
"[skill adı] skill'ini güncelle: [yeni özellik]"
```

**Skill'i test et:**
```
"[skill adı] skill'ini kullanarak [görev] yap"
```

---

**Kurulum:** ✅ Tamamlandı
**Lokasyon:** `~/.claude/skills/skill-creator/` ve `~/.claude/skills/template-skill/`
**Döküman:** ✅ Oluşturuldu
**Durum:** 🟢 Kullanıma hazır

---

**Son Güncelleme:** 2025-10-17 16:00
