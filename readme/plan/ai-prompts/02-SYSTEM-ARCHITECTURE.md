# 🏗️ AI SYSTEM ARCHITECTURE V2.0

## 📌 NURULLAH'IN TALEBİ İLE İLİŞKİSİ
**Bu mimari, 00-REQUIREMENTS-TALEPLER.md dosyasındaki tüm talepleri karşılamak için tasarlandı:**
- ✅ Uzun yazı sorunu → Smart Template Engine ile çözülecek
- ✅ Context tanıma → Context Engine ile çözülecek
- ✅ Aptal yanıtlar → AI Behavior System ile önlenecek
- ✅ Veritabanı erişimi → Integration Features ile sağlanacak

## 🎯 SİSTEM MİMARİSİ

### **3 KATMANLI YAPIYA GEÇİŞ**

```
┌─────────────────────────────────────────────┐
│             USER INTERFACE LAYER            │
│  Chat Panel | Prowess Page | Module Buttons │
└─────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────┐
│            SMART PROCESSING LAYER           │
│   Context Engine | AI Profile | Permissions │
└─────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────┐
│            AI PROVIDER LAYER                │
│      DeepSeek | Claude | ChatGPT | ...      │
└─────────────────────────────────────────────┘
```

---

## 🧠 CONTEXT ENGINE (Yeni Sistem)

### **Context Türleri:**

#### **1. USER CONTEXT (Kullanıcı Bağlamı)**
```php
[
    'user_id' => 123,
    'user_name' => 'Nurullah',
    'user_role' => 'admin',
    'user_permissions' => ['module_page', 'module_seo'],
    'last_activity' => '2025-08-07 10:30:00',
    'preferred_language' => 'tr',
    'usage_history' => [...]
]
```

#### **2. TENANT CONTEXT (Firma Bağlamı)**
```php
[
    'tenant_id' => 1,
    'company_name' => 'TechCorp',
    'sector' => 'Teknoloji',
    'city' => 'İstanbul',
    'brand_voice' => 'Profesyonel',
    'target_audience' => 'B2B',
    'languages' => ['tr', 'en'],
    'ai_profile' => [...]
]
```

#### **3. PAGE CONTEXT (Sayfa Bağlamı)**
```php
[
    'current_module' => 'pages',
    'current_action' => 'edit',
    'page_id' => 456,
    'page_content' => 'Mevcut sayfa içeriği...',
    'available_fields' => ['title', 'content', 'meta_desc'],
    'empty_languages' => ['en', 'de']
]
```

#### **4. SESSION CONTEXT (Oturum Bağlamı)**
```php
[
    'conversation_history' => [...],
    'recent_features_used' => ['blog_write', 'seo_analyze'],
    'current_task' => 'content_creation',
    'selected_preferences' => [...],
    // NURULLAH'IN TALEBİ: Önceki konuşmaları hatırlasın
    'last_topics' => ['bilişim', 'teknoloji'],
    'preferred_length' => 'uzun', // Kullanıcının tercih ettiği uzunluk
    'writing_style' => 'detaylı' // Kullanıcının yazım stili
]
```

---

## 🤖 AI BEHAVIOR SYSTEM

### **Davranış Kuralları Hiyerarşisi:**

#### **LEVEL 1: SYSTEM RULES (Sistem Kuralları)**
```
- Her zaman Türkçe yanıt ver
- Profesyonel ve yardımsever ol
- Hata durumlarında açık bilgi ver
- Güvenlik kurallarına uy
```

#### **LEVEL 2: CONTEXT RULES (Bağlam Kuralları)**
```
- Chat'te: Kişisel tanıma (Merhaba Nurullah)
- Prowess'te: Firma odaklı (TechCorp için)
- Modül butonlarında: Task odaklı (Bu sayfayı çevir)
```

#### **LEVEL 3: FEATURE RULES (Özellik Kuralları)**
```
- Blog yazarken: Minimum 5 paragraf
- SEO analizinde: Teknik ve detaylı
- Çeviride: Katı ve yorum katmadan
```

#### **LEVEL 4: DYNAMIC RULES (Dinamik Kurallar)**
```
⚠️ NURULLAH'IN KRİTİK TALEBİ:
- "Uzun yaz" = MİNİMUM 800-1200 kelime (ZORUNLU)
- "Çok uzun yaz" = MİNİMUM 1500-2500 kelime (ZORUNLU)
- "Kısa yaz" = 200-400 kelime
- "Detaylı yaz" = 1000-1500 kelime
- "500 kelimelik" = TAM 500 kelime (±%20)

⚠️ PARAGRAF ZORUNLULUĞU:
- MİNİMUM 4 paragraf (HER ZAMAN)
- Her paragraf 3-6 cümle
- Paragraflar arası boş satır

⚠️ YASAKLAR:
- "Bu konuda yardımcı olamam" DEMESİN
- "Hangi konuda?" diye SORMASIN
- Tek paragraf YAZMASIN
- "Üzgünüm" ile BAŞLAMASIN
```

---

## 📋 FEATURE TYPE SYSTEM

### **4 Farklı Feature Tipi:**

#### **TYPE 1: STATIC FEATURES (Statik)**
- **Örnek**: Blog yazma, makale oluşturma
- **Özellik**: Sadece text input alır
- **UI**: Basit textarea
- **Process**: Direkt AI'ya gönder

#### **TYPE 2: SELECTION FEATURES (Seçimli)**  
- **Örnek**: Çeviri (dil seçimi), SEO analizi (sayfa seçimi)
- **Özellik**: Önce seçim ekranı
- **UI**: Dropdown'lar, checkboxlar
- **Process**: Seçim + Input → AI

**NURULLAH'IN ÇEVİRİ TALEBİ:**
```
A) Prowess'te Çeviri:
   1. Metin kutusuna yapıştır
   2. Dropdown'dan hedef dil seç
   3. Çevir (YORUM KATMADAN)

B) Pages Modülünde:
   1. Edit sayfasında "Çevir" butonu
   2. Boş JSON field'lara otomatik yaz
   3. Veritabanına kaydet
```

#### **TYPE 3: CONTEXT FEATURES (Bağlamsal)**
- **Örnek**: "Bu sayfayı çevir", "Bu içeriği analiz et"
- **Özellik**: Mevcut sayfa/içeriği kullanır
- **UI**: Minimal, sadece onay butonu
- **Process**: Auto context + AI

#### **TYPE 4: INTEGRATION FEATURES (Entegrasyon)**
- **Örnek**: Veritabanından ürün bilgisi al, otomatik kaydet
- **Özellik**: Database read/write
- **UI**: Karmaşık form'lar
- **Process**: DB Query + AI + DB Write

---

## 🔐 PERMISSION SYSTEM

### **Yetki Katmanları:**

#### **ROOT LEVEL**
```php
- access: ['*']
- modules: ['*'] 
- tenants: ['*']
- ai_features: ['*']
```

#### **ADMIN LEVEL**  
```php
- access: ['tenant_admin']
- modules: ['tenant_modules']
- tenants: ['own_tenant']
- ai_features: ['all_except_system']
```

#### **EDITOR LEVEL**
```php  
- access: ['content_management']
- modules: ['pages', 'blog', 'seo']
- tenants: ['own_tenant']
- ai_features: ['content_features_only']
```

#### **USER LEVEL**
```php
- access: ['basic_usage'] 
- modules: ['limited_access']
- tenants: ['own_tenant']
- ai_features: ['safe_features_only']
```

---

## 📊 SMART TEMPLATE ENGINE

### **Template Seviyeler:**

#### **LEVEL 1: BASE TEMPLATE**
```json
{
    "format": "structured",
    "language": "turkish",
    "tone": "professional",
    "structure": {
        "intro": true,
        "body": true,
        "conclusion": true
    }
}
```

#### **LEVEL 2: FEATURE TEMPLATE**  
```json
{
    "extends": "base_template",
    "feature_specific": {
        "blog_writing": {
            "min_paragraphs": 5,
            "min_words": 500,
            "include_examples": true
        }
    }
}
```

#### **LEVEL 3: CONTEXT TEMPLATE**
```json  
{
    "extends": "feature_template",
    "context_rules": {
        "if_user_says_uzun": {
            "min_words": 800,
            "min_paragraphs": 6
        },
        "if_company_tech": {
            "use_technical_terms": true
        }
    }
}
```

#### **LEVEL 4: DYNAMIC TEMPLATE**
```json
{
    "extends": "context_template", 
    "runtime_rules": {
        "generated_from_context": true,
        "adaptive_length": true,
        "smart_formatting": true
    }
}
```

---

## 🚀 IMPLEMENTATION STRATEGY

### **ADIM 1: Context Engine**
1. User/Tenant/Page context collectors
2. Context storage & retrieval system
3. Context-aware prompt builder

### **ADIM 2: Feature Type System**
1. Feature type classifier
2. Type-specific UI components  
3. Type-specific processors

### **ADIM 3: Permission Integration**
1. Role-based feature filtering
2. Module-based restrictions
3. Tenant isolation

### **ADIM 4: Smart Templates**  
1. Multi-level template inheritance
2. Dynamic rule evaluation
3. Context-aware formatting

---

## 💡 GELECEK GELİŞTİRMELER

- **Machine Learning**: Kullanım pattern'larını öğrenme
- **Auto-Optimization**: Template'leri otomatik iyileştirme  
- **Predictive Context**: Kullanıcının ne isteyeceğini tahmin etme
- **Multi-Modal**: Text + Image + Audio processing