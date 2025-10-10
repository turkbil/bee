# 🎯 AI FEATURE AYARLARI VE ÇALIŞMA MEKANİZMASI

## 📝 FORM AYARLARI SİSTEMİ

### **Temel Yapı:**
Her AI feature'ın kendine özel bir **form_config** JSON'u var. Bu JSON, kullanıcının göreceği tüm ayar seçeneklerini tanımlar.

```json
{
  "inputs": [
    // Ana textarea (accordion dışında)
    {
      "key": "topic",
      "type": "textarea", 
      "label": "Blog Konusu",
      "is_primary": true,
      "required": true
    },
    
    // İleri düzey ayarlar (accordion içinde)
    {
      "key": "writing_style",
      "type": "radio",
      "label": "Yazım Stili",
      "group": "advanced",
      "options": [
        {"value": "professional", "label": "Profesyonel"},
        {"value": "casual", "label": "Samimi"},
        {"value": "technical", "label": "Teknik"}
      ]
    }
  ]
}
```

## 🔄 AYARLARDAN PROMPT'A DÖNÜŞÜM MEKANİZMASI

### **1. Kullanıcı Seçimi → Prompt ID Eşleştirme**

Kullanıcı bir ayar seçtiğinde (örn. "Yazım Stili: Profesyonel"), sistem bu seçimi **prompt_id**'ye çevirir:

```php
// Örnek seçim:
$userSelections = [
    'writing_style' => 'professional',
    'audience_level' => 'intermediate', 
    'content_length' => 'long'
];

// Her seçim bir prompt_id'ye karşılık gelir:
$promptMappings = [
    'writing_style.professional' => 10001,  // "Profesyonel Yazım Uzmanı" prompt'u
    'audience_level.intermediate' => 10015, // "Orta Seviye Hedef Kitle" prompt'u  
    'content_length.long' => 10025          // "Uzun İçerik Formatı" prompt'u
];
```

### **2. Prompt ID'lerin Priority Sırasına Göre Dizilmesi**

Seçilen prompt'lar **priority** değerlerine göre sıralanır:

```php
// Priority tablosu (küçük sayı = yüksek öncelik):
Prompt ID 10001 → Priority: 50  (Yazım stili)
Prompt ID 10015 → Priority: 90  (Hedef kitle) 
Prompt ID 10025 → Priority: 130 (İçerik uzunluğu)

// Sıralı hali:
1. Yazım Stili prompt'u (priority: 50)
2. Hedef Kitle prompt'u (priority: 90)
3. İçerik Uzunluğu prompt'u (priority: 130)
```

### **3. Final Prompt Oluşturma**

AI'ya gönderilecek son prompt şu sırayla birleştirilir:

```
=== GÖREV TANIMI ===
Sen bir blog yazarısın. Verilen konuda blog yazısı yaz. (Quick Prompt)

=== YAZIM STİLİ UZMANLIĞI ===
Profesyonel ve kurumsal ton kullan. Uzman görünümü ver... (Priority: 50)

=== HEDEF KİTLE BİLGİSİ === 
Orta seviye okuyuculara hitap et. Temel bilgileri açıkla... (Priority: 90)

=== İÇERİK UZUNLUK FORMATI ===
750+ kelimelik kapsamlı içerik hazırla. Detaylı örnekler ver... (Priority: 130)

=== KULLANICI GİRDİSİ ===
WordPress SEO optimizasyonu hakkında blog yazısı
```

## ⚙️ RUNTIME ÇALIŞMA AKIŞI

### **Adım 1: Kullanıcı Form Doldurur**
```javascript
// Frontend'de seçimler:
{
  topic: "WordPress SEO hakkında yazı",
  writing_style: "professional", 
  audience_level: "intermediate",
  content_length: "long",
  use_brand_info: true
}
```

### **Adım 2: Form Veriler Backend'e Gider**
```php
// Controller'da işlem:
public function generateContent(Request $request) 
{
    $feature = AIFeature::find($request->feature_id);
    $userInputs = $request->all();
    
    // Form config'den prompt mapping'leri al
    $selectedPrompts = $this->mapUserSelectionsToPrompts(
        $userInputs, 
        $feature->form_config
    );
}
```

### **Adım 3: Prompt Mapping ve Sıralama**
```php
private function mapUserSelectionsToPrompts($inputs, $formConfig)
{
    $promptIds = [];
    
    foreach ($inputs as $key => $value) {
        // Her seçim için karşılık gelen prompt ID'yi bul
        $promptId = $this->findPromptIdForSelection($key, $value);
        if ($promptId) {
            $promptIds[] = $promptId;
        }
    }
    
    // Priority'ye göre sırala
    return $this->sortPromptsByPriority($promptIds);
}
```

### **Adım 4: Final Prompt Oluşturma**
```php
private function buildFinalPrompt($feature, $sortedPromptIds, $userContent)
{
    $promptParts = [];
    
    // 1. Quick prompt (feature'ın temel görevi)
    $promptParts[] = "=== GÖREV ===" . $feature->quick_prompt;
    
    // 2. Seçilen prompt'ları priority sırasına göre ekle
    foreach ($sortedPromptIds as $promptId) {
        $prompt = Prompt::where('prompt_id', $promptId)->first();
        $promptParts[] = "=== " . strtoupper($prompt->name) . " ===" . $prompt->content;
    }
    
    // 3. Response template (yanıt formatı)
    if ($feature->response_template) {
        $promptParts[] = "=== YANIT FORMATI ===" . $this->formatTemplate($feature->response_template);
    }
    
    // 4. Kullanıcı içeriği
    $promptParts[] = "=== KULLANICI GİRDİSİ ===" . $userContent;
    
    return implode("\n\n" . str_repeat("-", 50) . "\n\n", $promptParts);
}
```

## 🎨 ÖZEL PROMPT TİPLERİ VE ÇALIŞMA MANTIĞI

### **Focus Type (Odak Noktası) Prompt'ları:**
```php
// Kullanıcı "Yaratıcı" seçerse:
focus_type: "creative" → Prompt ID: 10003

// Bu prompt içeriği:
"Yaratıcı ve özgün yaklaşım benimse. İnovatif örnekler ver. 
Sıra dışı bakış açıları sun. Metaforlar ve analojiler kullan."
```

### **Audience Level (Hedef Kitle) Prompt'ları:**
```php
// Kullanıcı "Yeni Başlayan" seçerse:
audience_level: "beginners" → Prompt ID: 10015

// Bu prompt içeriği:
"Temel seviye bilgi ver. Teknik terimleri açıkla. 
Adım adım anlatım yap. Örneklerle destekle."
```

### **Content Length (İçerik Uzunluğu) Prompt'ları:**
```php
// Kullanıcı "Kapsamlı" seçerse:
content_length: "detailed" → Prompt ID: 10028

// Bu prompt içeriği:
"750+ kelimelik detaylı içerik hazırla. Alt başlıklar kullan.
Derinlemesine analiz yap. Çoklu örnekler ver."
```

## 🚀 SONUÇ: NASIL BİRLEŞİR?

Kullanıcı **"WordPress SEO"** konusunda, **"Profesyonel"** tonda, **"Orta seviye"** okuyuculara, **"Uzun"** format seçerse:

```
FINAL PROMPT = 

Quick Prompt (Feature görevi)
+ Profesyonel Yazım prompt'u (Priority: 50)  
+ Orta Seviye Hedef Kitle prompt'u (Priority: 90)
+ Uzun İçerik Format prompt'u (Priority: 130)
+ Response Template (JSON format)
+ Kullanıcının "WordPress SEO" girdisi
```

Bu sistem **tamamen dinamik** - her feature'ın farklı ayarları var, her ayar farklı prompt'lara eşleşir, ve AI her seferinde bu **kişiselleştirilmiş prompt zinciri** ile çalışır.

---

*Bu dokümantasyon AI Feature ayarları sisteminin çalışma mekanizmasını açıklar. Her kullanıcı seçimi belirli prompt'lara eşleşir ve bu prompt'lar priority sırasına göre birleştirilerek AI'ya gönderilir.*