# 🎯 AI FEATURE & PROMPT SEEDER KURALLARI - STATİK ID SİSTEMİ

## 🚨 **KRİTİK KURALLAR - CLAUDE IÇIN ZORUNLU**

### **KURAL 1: STATİK ID SİSTEMİ**
- **Her kategori**: Sabit ID (1,2,3,4...)
- **Her feature**: Sabit ID (1001,1002,1003...)  
- **Her prompt**: Sabit ID (2001,2002,2003...)
- **ASLA değişken ID kullanma!**
- **ASLA Auto Increment güvenme!**

### **KURAL 2: AYRI DOSYA YAPISI**
- **Her feature = Ayrı seeder dosyası**
- **Her prompt = Ayrı seeder dosyası**
- **Her kategori = Ayrı seeder dosyası**
- **Dosya adı = ID numarası içermeli**

### **KURAL 3: ÇAKIŞMA ÖNLEME**
- **Kategori ID'ler**: 1-100 arasında
- **Feature ID'ler**: 1001-9999 arasında  
- **Prompt ID'ler**: 10001-99999 arasında
- **Bu aralıkları ASLA aşma!**

---

## 📁 **DOSYA YAPISI ŞABLONU**

### **Kategori Seederları**
```
AICategory_01_SEO_Seeder.php         (id: 1)
AICategory_02_Writing_Seeder.php      (id: 2)
AICategory_03_Translation_Seeder.php  (id: 3)
```

### **Feature Seederları**
```
AIFeature_1001_SEO_Title_Generator.php       (id: 1001, category_id: 1)
AIFeature_1002_Meta_Description_Writer.php   (id: 1002, category_id: 1)
AIFeature_2001_Blog_Article_Writer.php       (id: 2001, category_id: 2)
```

### **Prompt Seederları**
```
AIPrompt_10001_SEO_Expert_Prompt.php         (id: 10001)
AIPrompt_10002_Content_Writing_Expert.php    (id: 10002)
AIPrompt_10003_Translation_Expert.php        (id: 10003)
```

---

## 🎨 **KATEGORİ ID PLANI (ÖNEM SIRASINA GÖRE)**

### **🔥 ÇOK YÜKSEk ÖNCELİK (1-6)**

### **ID 1: SEO ve Optimizasyon** 🥇
```
id: 1
name: "SEO ve Optimizasyon"
slug: "seo-optimization"  
icon: "fas fa-search"
color: "#4CAF50"
description: "Arama motoru optimizasyonu ve web site performansı"
priority: 1
```

### **ID 2: İçerik Yazıcılığı** 🥈
```
id: 2
name: "İçerik Yazıcılığı"
slug: "content-writing"
icon: "fas fa-pen-fancy"
color: "#2196F3"
description: "Blog, makale, sosyal medya içerik üretimi"
priority: 2
```

### **ID 3: Çeviri ve Lokalizasyon** 🥉
```
id: 3
name: "Çeviri ve Lokalizasyon" 
slug: "translation"
icon: "fas fa-language"
color: "#FF9800"
description: "Çoklu dil çeviri ve yerelleştirme hizmetleri"
priority: 3
```

### **ID 4: Pazarlama & Reklam** 🎯
```
id: 4
name: "Pazarlama & Reklam"
slug: "marketing-advertising"
icon: "fas fa-bullhorn"
color: "#FF5722"
description: "Reklam metinleri, kampanya içerikleri, landing page"
priority: 4
```

### **ID 5: E-ticaret ve Satış** 🛒
```
id: 5
name: "E-ticaret ve Satış"
slug: "ecommerce-sales"
icon: "fas fa-shopping-cart"
color: "#9C27B0"
description: "Ürün açıklamaları, satış metinleri, e-ticaret içerikleri"
priority: 5
```

### **ID 6: Sosyal Medya** 📱
```
id: 6
name: "Sosyal Medya"
slug: "social-media"
icon: "fas fa-share-alt"
color: "#E91E63"
description: "Sosyal medya paylaşımları, hashtag önerileri, engagement"
priority: 6
```

### **⚡ YÜKSEK ÖNCELİK (7-12)**

### **ID 7: Email & İletişim** 📧
```
id: 7
name: "Email & İletişim"
slug: "email-communication"
icon: "fas fa-envelope"
color: "#607D8B"
description: "Newsletter, email marketing, iş iletişimi"
priority: 7
```

### **ID 8: Analiz ve Raporlama** 📊
```
id: 8
name: "Analiz ve Raporlama"
slug: "analytics-reporting"
icon: "fas fa-chart-line"
color: "#00BCD4"
description: "Veri analizi, rapor yazımı, istatistiksel değerlendirmeler"
priority: 8
```

### **ID 9: Müşteri Hizmetleri** 🎧
```
id: 9
name: "Müşteri Hizmetleri"
slug: "customer-service"
icon: "fas fa-headset"
color: "#CDDC39"
description: "Müşteri yanıtları, destek metinleri, FAQ'lar"
priority: 9
```

### **ID 10: İş Geliştirme** 💼
```
id: 10
name: "İş Geliştirme"
slug: "business-development"
icon: "fas fa-briefcase"
color: "#795548"
description: "İş planları, sunum metinleri, kurumsal içerikler"
priority: 10
```

### **ID 11: Araştırma & Pazar** 🔍
```
id: 11
name: "Araştırma & Pazar"
slug: "research-market"
icon: "fas fa-chart-pie"
color: "#00BCD4"
description: "Pazar araştırması, competitor analizi, survey"
priority: 11
```

### **ID 12: Yaratıcı İçerik** 🎨
```
id: 12
name: "Yaratıcı İçerik"
slug: "creative-content"
icon: "fas fa-palette"
color: "#FF5722"
description: "Hikaye yazımı, yaratıcı metinler, senaryolar"
priority: 12
```

### **🔧 ORTA ÖNCELİK (13-18)**

### **ID 13: Teknik Dokümantasyon** 📚
```
id: 13
name: "Teknik Dokümantasyon"
slug: "technical-docs"
icon: "fas fa-book"
color: "#607D8B"
description: "API dokümantasyonu, kullanıcı kılavuzları, teknik açıklamalar"
priority: 13
```

### **ID 14: Kod & Yazılım** 💻
```
id: 14
name: "Kod & Yazılım"
slug: "code-software"
icon: "fas fa-laptop-code"
color: "#424242"
description: "API dokümantasyonu, kod açıklamaları, tutorial"
priority: 14
```

### **ID 15: Tasarım & UI/UX** 🖌️
```
id: 15
name: "Tasarım & UI/UX"
slug: "design-ui-ux"
icon: "fas fa-paint-brush"
color: "#E91E63"
description: "Microcopy, error messages, UI metinleri"
priority: 15
```

### **ID 16: Eğitim ve Öğretim** 🎓
```
id: 16
name: "Eğitim ve Öğretim"
slug: "education"
icon: "fas fa-graduation-cap"
color: "#3F51B5"
description: "Eğitim materyalleri, kurs içerikleri, sınav soruları"
priority: 16
```

### **ID 17: Finans & İş** 💰
```
id: 17
name: "Finans & İş"
slug: "finance-business"
icon: "fas fa-calculator"
color: "#4CAF50"
description: "İş planları, finansal analiz, ROI raporları"
priority: 17
```

### **ID 18: Hukuki ve Uyumluluk** ⚖️
```
id: 18
name: "Hukuki ve Uyumluluk"
slug: "legal-compliance"
icon: "fas fa-gavel"
color: "#9E9E9E"
description: "Sözleşmeler, kullanım şartları, gizlilik politikaları"
priority: 18
```

---

## 🔧 **SEEDER DOSYA ŞABLONLARI**

### **Kategori Seeder Şablonu**
```php
<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AICategory_01_SEO_Seeder extends Seeder
{
    public function run(): void
    {
        // STATIK ID: 1 - SEO Kategori
        DB::table('ai_feature_categories')->insertOrIgnore([
            'id' => 1,
            'name' => 'SEO ve Optimizasyon',
            'slug' => 'seo-optimization',
            'icon' => 'fas fa-search',
            'color' => '#4CAF50',
            'description' => 'Arama motoru optimizasyonu ve web site performansı',
            'priority' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
```

### **Feature Seeder Şablonu**
```php
<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIFeature_1001_SEO_Title_Generator extends Seeder
{
    public function run(): void
    {
        // STATIK ID: 1001 - SEO Title Generator
        DB::table('ai_features')->insertOrIgnore([
            'id' => 1001,
            'category_id' => 1, // SEO Kategori
            'name' => 'SEO Başlık Üretici',
            'slug' => 'seo-title-generator',
            'quick_prompt' => 'Sen bir SEO uzmanısın. Verilen anahtar kelime için optimize edilmiş başlık önerileri üret.',
            'expert_prompt_id' => 10001, // Prompt ID referansı
            'response_template' => json_encode([
                'format' => 'list',
                'sections' => ['Ana Başlık', 'Alternatif Öneriler', 'Karakter Sayısı'],
                'max_items' => 5
            ]),
            'feature_type' => 'static',
            'input_fields' => json_encode([
                ['name' => 'keyword', 'type' => 'text', 'required' => true, 'label' => 'Ana Anahtar Kelime'],
                ['name' => 'business', 'type' => 'text', 'required' => false, 'label' => 'İşletme Adı']
            ]),
            'is_active' => true,
            'priority' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
```

### **Prompt Seeder Şablonu**
```php
<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIPrompt_10001_SEO_Expert_Prompt extends Seeder
{
    public function run(): void
    {
        // STATIK ID: 10001 - SEO Expert Prompt
        DB::table('ai_prompts')->insertOrIgnore([
            'id' => 10001,
            'name' => 'SEO Uzmanı Prompt',
            'content' => 'Sen 10 yıl deneyimli bir SEO uzmanısın. Görevin:
            
1. Verilen anahtar kelime için Google\'da üst sıralarda çıkabilecek başlık önerileri sunmak
2. Başlık uzunluğunu 50-60 karakter arasında tutmak  
3. Clickbait olmadan çekici başlıklar yazmak
4. Anahtar kelimeyi doğal şekilde yerleştirmek
5. Türkçe dilbilgisi kurallarına uymak

YANIT FORMATI:
- Ana Başlık (karaktere sayısıyla birlikte)
- 4 Alternatif öneri 
- Her başlık için kısa SEO analizi',
            'category' => 'SEO',
            'priority' => 100,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
```

---

## 📋 **ID REZERVASYON LİSTESİ**

### **Kategori ID'ler (1-100)**
```
🔥 ÇOK YÜKSEK ÖNCELİK:
1:  SEO ve Optimizasyon          ✅ REZERVE
2:  İçerik Yazıcılığı           ✅ REZERVE  
3:  Çeviri ve Lokalizasyon      ✅ REZERVE
4:  Pazarlama & Reklam          ✅ REZERVE
5:  E-ticaret ve Satış          ✅ REZERVE
6:  Sosyal Medya                ✅ REZERVE

⚡ YÜKSEK ÖNCELİK:
7:  Email & İletişim            ✅ REZERVE
8:  Analiz ve Raporlama         ✅ REZERVE
9:  Müşteri Hizmetleri          ✅ REZERVE
10: İş Geliştirme               ✅ REZERVE
11: Araştırma & Pazar           ✅ REZERVE
12: Yaratıcı İçerik             ✅ REZERVE

🔧 ORTA ÖNCELİK:
13: Teknik Dokümantasyon        ✅ REZERVE
14: Kod & Yazılım               ✅ REZERVE
15: Tasarım & UI/UX             ✅ REZERVE
16: Eğitim ve Öğretim           ✅ REZERVE
17: Finans & İş                 ✅ REZERVE
18: Hukuki ve Uyumluluk         ✅ REZERVE

19-100: BOŞ (Gelecek kullanım)
```

### **Feature ID'ler (1001-9999)**
```
🔥 ÇOK YÜKSEK ÖNCELİK:
1001-1050: SEO ve Optimizasyon      ✅ REZERVE
2001-2050: İçerik Yazıcılığı        ✅ REZERVE
3001-3050: Çeviri ve Lokalizasyon   ✅ REZERVE
4001-4050: Pazarlama & Reklam       ✅ REZERVE
5001-5050: E-ticaret ve Satış       ✅ REZERVE
6001-6050: Sosyal Medya             ✅ REZERVE

⚡ YÜKSEK ÖNCELİK:
7001-7050: Email & İletişim         ✅ REZERVE
8001-8050: Analiz ve Raporlama      ✅ REZERVE
9001-9050: Müşteri Hizmetleri       ✅ REZERVE
1051-1100: İş Geliştirme            ✅ REZERVE
1101-1150: Araştırma & Pazar        ✅ REZERVE
1201-1250: Yaratıcı İçerik          ✅ REZERVE

🔧 ORTA ÖNCELİK:
1301-1350: Teknik Dokümantasyon     ✅ REZERVE
1401-1450: Kod & Yazılım            ✅ REZERVE
1501-1550: Tasarım & UI/UX          ✅ REZERVE
1601-1650: Eğitim ve Öğretim        ✅ REZERVE
1701-1750: Finans & İş              ✅ REZERVE
1801-1850: Hukuki ve Uyumluluk      ✅ REZERVE

2000-9999: BOŞ (Özel projeler)
```

### **Prompt ID'ler (10001-99999)**
```
10001-19999: Expert Prompts     ✅ REZERVE
20001-29999: Template Prompts   ✅ REZERVE  
30001-39999: System Prompts     ✅ REZERVE
40001-99999: BOŞ (Gelecek)
```

---

## 🚨 **CLAUDE İÇİN SERT KURALLAR**

### **YAPILMASI ZORUNLU:**
1. **Her seeder dosyası ayrı oluştur**
2. **ID'leri yukarıdaki aralıklarda tut**
3. **insertOrIgnore kullan (çakışma önleme)**
4. **Dosya isimlerinde ID numarası bulundur**
5. **JSON alanları için json_encode kullan**

### **YAPILMASI YASAK:**
1. **Auto increment ID'lere güvenme**
2. **Dinamik ID oluşturma**  
3. **Aynı dosyada birden fazla kayıt**
4. **ID aralıklarını karıştırma**
5. **Var olan ID'leri değiştirme**

### **DOSYA ADLANDIRMA ZORUNLU:**
```
AICategory_{ID}_{NAME}_Seeder.php
AIFeature_{ID}_{NAME}_Seeder.php  
AIPrompt_{ID}_{NAME}_Seeder.php
```

---

## 📊 **ÖNCELIK SIRASI**

### **İLK AŞAMA (20 Feature)**
```
SEO:        1001-1005 (5 feature)
İçerik:     2001-2005 (5 feature)  
Çeviri:     3001-3003 (3 feature)
E-ticaret:  4001-4003 (3 feature)
Sosyal:     5001-5002 (2 feature)
Teknik:     6001-6002 (2 feature)
```

### **İKİNCİ AŞAMA (30+ Feature)**
- Her kategoriden 5-8 feature daha
- Advanced özellikler
- Multi-step features

### **ÜÇÜNCÜ AŞAMA (50+ Feature)**  
- Özel projeler
- Integration features
- Custom business logic

---

**SON GÜNCELLEME**: 7 Ağustos 2025  
**GÜNCELLEYEN**: Nurullah + AI Assistant  
**KURAL UYUMU**: ZORUNLU - Her seeder bu kurallara uyacak!