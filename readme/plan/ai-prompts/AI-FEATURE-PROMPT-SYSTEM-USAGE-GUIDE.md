# 🎯 AI FEATURE PROMPT SİSTEMİ KULLANIM KILAVUZU

## 📋 SİSTEM MİMARİSİ ÖZET

```
ai_features (Ana Feature'lar)
    ↓ (Quick Prompt + Response Template)
    ↓
ai_feature_prompt_relations (İlişki Tablosu)
    ↓ (Priority + Role + Filtering)
    ↓
ai_feature_prompts (Expert Prompt'lar)
    ↓ (Expert Persona + Content)
```

---

## 🔧 TABLO YAPILARI ve KULLANIMI

### 1. **ai_feature_prompts** - Expert Prompt'lar

**Amaç**: AI uzmanlarının (SEO expert, content creator, etc.) prompt'larını saklar

**Temel Alanlar:**
```php
id: 1
name: "SEO İçerik Uzmanı"
slug: "seo-icerik-uzmani" 
expert_prompt: "Sen profesyonel bir SEO uzmanısın. E-E-A-T prensiplerine göre..."
expert_persona: "seo_expert"
supported_categories: [1, 4, 6] // SEO, Pazarlama, Sosyal Medya
response_template: {
    "format": "structured",
    "sections": ["SEO Analizi", "Anahtar Kelimeler", "Öneriler"],
    "scoring": true
}
```

**KULLANIM:**
- ✅ Her expert'ın kendi kişiliği ve uzmanlık alanı var
- ✅ Birden fazla kategoride çalışabilir (static ID 1-18)
- ✅ Response template ile sabit çıktı formatı
- ✅ Usage tracking ve quality scoring

### 2. **ai_feature_prompt_relations** - İlişki Yönetimi

**Amaç**: Hangi feature hangi expert prompt'ları kullanacağını belirler

**Temel Alanlar:**
```php
id: 1
feature_id: 25          // "Blog Yazısı" feature'ı
feature_prompt_id: 3    // "İçerik Üretim Uzmanı"
priority: 1             // İlk sırada çalışacak
role: "primary"         // Ana prompt
category_context: [2]   // Sadece "İçerik Yazıcılığı" kategorisinde
feature_type_filter: "category_based"
is_active: true
```

**KULLANIM:**
- ✅ Bir feature'a birden fazla expert prompt bağlanabilir
- ✅ Priority sırasına göre çalışır (1 = en öncelikli)
- ✅ Role ile prompt'ın işlevi belirlenir
- ✅ Category context ile hangi kategorilerde aktif olacağı

---

## 🎯 PRATİK KULLANIM ÖRNEKLERİ

### ÖRNEK 1: Blog Yazısı Feature'ı

```sql
-- 1. Expert Prompt Oluştur
INSERT INTO ai_feature_prompts (
    name, slug, expert_prompt, expert_persona, supported_categories, response_template
) VALUES (
    'İçerik Üretim Uzmanı',
    'icerik-uretim-uzmani',
    'Sen profesyonel bir içerik yazarısın. E-E-A-T prensiplerine uygun, SEO dostu, okuyucu odaklı içerikler üretirsin...',
    'content_creator',
    '[2, 4, 6]', -- İçerik, Pazarlama, Sosyal Medya
    '{"format": "markdown", "sections": ["Giriş", "Ana İçerik", "Sonuç"], "word_count": true}'
);

-- 2. Feature ile İlişkilendir  
INSERT INTO ai_feature_prompt_relations (
    feature_id, feature_prompt_id, priority, role, category_context
) VALUES (
    17, 1, 1, 'primary', '[2]' -- Blog Yazısı feature'ı, İçerik kategorisinde
);
```

### ÖRNEK 2: SEO Analizi Feature'ı (Çoklu Expert)

```sql
-- SEO Feature'ına 3 farklı expert ekleyelim:

-- 1. Ana SEO Expert (Priority 1)
INSERT INTO ai_feature_prompt_relations (
    feature_id, feature_prompt_id, priority, role, category_context
) VALUES (
    1, 2, 1, 'primary', '[1]' -- SEO kategorisi
);

-- 2. İçerik Expert (Priority 2) 
INSERT INTO ai_feature_prompt_relations (
    feature_id, feature_prompt_id, priority, role, category_context
) VALUES (
    1, 1, 2, 'supportive', '[1]' -- SEO kategorisi
);

-- 3. Teknik SEO Expert (Priority 3)
INSERT INTO ai_feature_prompt_relations (
    feature_id, feature_prompt_id, priority, role, category_context  
) VALUES (
    1, 4, 3, 'secondary', '[1]' -- SEO kategorisi
);
```

---

## 💻 BACKEND KULLANIM (Laravel)

### TemplateEngine Service Güncellemesi

```php
// Modules/AI/app/Services/Template/TemplateEngine.php

private function getExpertPrompts(AIFeature $feature): array
{
    // Yeni relations sistemi ile feature prompt'larını al
    $featurePromptRelations = DB::table('ai_feature_prompt_relations')
        ->join('ai_feature_prompts', 'ai_feature_prompt_relations.feature_prompt_id', '=', 'ai_feature_prompts.id')
        ->where('ai_feature_prompt_relations.feature_id', $feature->id)
        ->where('ai_feature_prompt_relations.is_active', true)
        ->select([
            'ai_feature_prompts.name',
            'ai_feature_prompts.expert_prompt as content',
            'ai_feature_prompt_relations.priority',
            'ai_feature_prompt_relations.role'
        ])
        ->orderBy('ai_feature_prompt_relations.priority')
        ->get();
    
    $expertPrompts = [];
    foreach ($featurePromptRelations as $relation) {
        $expertPrompts[] = [
            'content' => $relation->content,
            'priority' => $relation->priority,
            'name' => $relation->name,
            'role' => $relation->role
        ];
    }
    
    return $expertPrompts;
}
```

### Model İlişkileri

```php
// AIFeature Model
public function expertPrompts()
{
    return $this->belongsToMany(
        AIFeaturePrompt::class,
        'ai_feature_prompt_relations',
        'feature_id',
        'feature_prompt_id'
    )->withPivot(['priority', 'role', 'is_active', 'category_context'])
     ->orderBy('pivot_priority');
}

// AIFeaturePrompt Model  
public function features()
{
    return $this->belongsToMany(
        AIFeature::class,
        'ai_feature_prompt_relations', 
        'feature_prompt_id',
        'feature_id'
    )->withPivot(['priority', 'role', 'is_active']);
}
```

---

## 🎨 FRONTEND KULLANIM ÖRNEKLERİ

### Admin Panel - Expert Prompt Yönetimi

```blade
{{-- Expert Prompts Listesi --}}
@foreach($expertPrompts as $prompt)
<div class="card mb-3">
    <div class="card-header">
        <h5>{{ $prompt->name }}</h5>
        <span class="badge bg-primary">{{ $prompt->expert_persona }}</span>
        @if($prompt->supported_categories)
            @foreach(json_decode($prompt->supported_categories) as $categoryId)
                <span class="badge bg-info">{{ $categories[$categoryId] ?? $categoryId }}</span>
            @endforeach
        @endif
    </div>
    <div class="card-body">
        <p>{{ Str::limit($prompt->expert_prompt, 200) }}</p>
        
        {{-- Response Template Preview --}}
        @if($prompt->response_template)
            <strong>Yanıt Formatı:</strong>
            <code>{{ json_encode(json_decode($prompt->response_template), JSON_PRETTY_PRINT) }}</code>
        @endif
        
        {{-- Usage Stats --}}
        <div class="mt-2">
            <small class="text-muted">
                Kullanım: {{ $prompt->usage_count }} | 
                Kalite: {{ $prompt->avg_quality_score }}/5.00 |
                Son Kullanım: {{ $prompt->last_used_at?->diffForHumans() }}
            </small>
        </div>
    </div>
</div>
@endforeach
```

### Feature-Prompt İlişki Yönetimi

```blade
{{-- Feature Edit Sayfasında Expert Prompt Seçimi --}}
<div class="expert-prompts-section">
    <h4>Expert Prompt'lar</h4>
    
    @foreach($availablePrompts as $prompt)
    <div class="card mb-3 position-relative">
        {{-- Checkbox sağ üst köşede --}}
        <input class="form-check-input position-absolute top-0 end-0 m-2" 
               type="checkbox" 
               wire:model="selectedPrompts.{{ $prompt->id }}.enabled"
               id="prompt{{ $prompt->id }}"
               style="transform: scale(1.2); accent-color: #6c757d;">
        
        <div class="card-body">
            <label class="form-check-label w-100 cursor-pointer" for="prompt{{ $prompt->id }}">
                <h5 class="card-title">{{ $prompt->name }}</h5>
                <span class="badge bg-secondary">{{ $prompt->expert_persona }}</span>
            </label>
        
            {{-- Priority ve Role Ayarları --}}
            @if($selectedPrompts[$prompt->id]['enabled'] ?? false)
            <div class="mt-3 p-3 bg-light rounded">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Priority:</label>
                        <input type="number" class="form-control form-control-sm" 
                               wire:model="selectedPrompts.{{ $prompt->id }}.priority" 
                               min="1" max="10" value="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Role:</label>
                        <select class="form-select form-select-sm" 
                                wire:model="selectedPrompts.{{ $prompt->id }}.role">
                            <option value="primary">Primary</option>
                            <option value="secondary">Secondary</option>
                            <option value="supportive">Supportive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori Context:</label>
                        <select class="form-select form-select-sm" multiple 
                                wire:model="selectedPrompts.{{ $prompt->id }}.categories">
                            @foreach($staticCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
```

---

## 🔍 QUERY ÖRNEKLERİ

### Belirli Kategorideki Feature'lar İçin Expert'ları Getir

```php
// Kategori ID 2 (İçerik Yazıcılığı) için tüm expert prompt'ları
$contentExperts = DB::table('ai_feature_prompt_relations as relations')
    ->join('ai_feature_prompts as prompts', 'relations.feature_prompt_id', '=', 'prompts.id')
    ->join('ai_features as features', 'relations.feature_id', '=', 'features.id')
    ->where('features.ai_feature_category_id', 2) // İçerik Yazıcılığı
    ->where('relations.is_active', true)
    ->where('prompts.is_active', true)
    ->select([
        'prompts.name',
        'prompts.expert_persona', 
        'relations.priority',
        'relations.role',
        'features.name as feature_name'
    ])
    ->orderBy('relations.priority')
    ->get();
```

### En Çok Kullanılan Expert'ları Getir

```php
$topExperts = AIFeaturePrompt::where('is_active', true)
    ->orderBy('usage_count', 'desc')
    ->orderBy('avg_quality_score', 'desc')
    ->take(10)
    ->get();
```

### Feature'ın Aktif Expert'larını Priority ile Getir

```php
$featureExperts = $feature->expertPrompts()
    ->wherePivot('is_active', true)
    ->orderBy('pivot_priority')
    ->get();

foreach($featureExperts as $expert) {
    echo "Priority: {$expert->pivot->priority}, Role: {$expert->pivot->role}";
}
```

---

## 📊 BUSİNESS LOGIC ÖRNEKLERİ

### 1. Conditional Logic (JSON Rules)

```json
{
  "business_rules": {
    "conditions": [
      {
        "if": "feature.category_id === 1", 
        "then": "require_seo_expert",
        "priority_boost": 5
      },
      {
        "if": "user.subscription === 'premium'",
        "then": "enable_advanced_prompts"
      }
    ],
    "usage_limits": {
      "daily_per_tenant": 100,
      "monthly_per_user": 1000
    }
  }
}
```

### 2. Category-Based Auto Assignment

```php
// Service'de otomatik expert assignment
public function assignExpertsToFeature(AIFeature $feature): void
{
    $categoryId = $feature->ai_feature_category_id;
    
    // Kategori bazlı varsayılan expert'lar
    $defaultExperts = [
        1 => ['seo-expert' => 1, 'content-expert' => 2], // SEO kategorisi
        2 => ['content-expert' => 1, 'creative-expert' => 2], // İçerik kategorisi
        4 => ['marketing-expert' => 1, 'copywriter-expert' => 2], // Pazarlama
    ];
    
    if (isset($defaultExperts[$categoryId])) {
        foreach($defaultExperts[$categoryId] as $expertSlug => $priority) {
            $expert = AIFeaturePrompt::where('slug', $expertSlug)->first();
            if ($expert) {
                $feature->expertPrompts()->attach($expert->id, [
                    'priority' => $priority,
                    'role' => $priority === 1 ? 'primary' : 'supportive',
                    'category_context' => json_encode([$categoryId])
                ]);
            }
        }
    }
}
```

---

## 🚀 DEPLOYMENT ve MİGRATİON

### 1. Migration'ları Çalıştır

```bash
# AI modülü migration'larını çalıştır
php artisan migrate --path=Modules/AI/database/migrations/
```

### 2. Seeder'ları Çalıştır

```bash
# Expert prompt'ları yükle
php artisan db:seed --class=Modules\\AI\\Database\\Seeders\\AIFeaturePromptsSeeder

# Relations'ları yükle  
php artisan db:seed --class=Modules\\AI\\Database\\Seeders\\AIFeaturePromptRelationsSeeder
```

### 3. Cache Temizleme

```bash
# Template cache temizle
php artisan cache:clear
php artisan config:clear
```

---

## 🎯 BEST PRACTİCES

### ✅ DO's (Yapılması Gerekenler)

1. **Static Category ID'leri Kullan** - 1-18 arası ID'ler kalıcıdır
2. **Priority'yi Mantıklı Kullan** - 1 = en önemli, 10 = en az önemli
3. **Response Template'leri Tanımla** - Tutarlı çıktı için şart
4. **Usage Analytics Takip Et** - Performance optimization için
5. **Expert Persona'ları Kategorize Et** - Daha iyi organize etmek için

### ❌ DON'Ts (Yapılmaması Gerekenler)

1. **Static Category ID'lerini Değiştirme** - Sistem bozulur
2. **Aynı Priority'de Multiple Expert** - Unique constraint hatası
3. **Response Template'siz Expert** - Tutarsız çıktılar
4. **Category Context Boş Bırakma** - Hangi kategoride çalışacağı belirsiz
5. **Performance Index'leri Silme** - Query performance düşer

---

## 🔧 TROUBLESHOOTİNG

### Yaygın Sorunlar ve Çözümleri

**1. Expert Prompt Çalışmıyor:**
```sql
-- İlişki tablosunu kontrol et
SELECT * FROM ai_feature_prompt_relations 
WHERE feature_id = ? AND is_active = true;

-- Expert prompt aktif mi kontrol et
SELECT is_active FROM ai_feature_prompts WHERE id = ?;
```

**2. Category Context Çalışmıyor:**
```php
// JSON alanının doğru format olup olmadığını kontrol et
$context = json_decode($relation->category_context, true);
if (!is_array($context)) {
    // Hata: JSON format yanlış
}
```

**3. Priority Çakışması:**
```sql
-- Duplicate priority kontrolü
SELECT feature_id, feature_prompt_id, priority, COUNT(*) 
FROM ai_feature_prompt_relations 
GROUP BY feature_id, feature_prompt_id, priority 
HAVING COUNT(*) > 1;
```

Bu kılavuzla sistem nasıl kullanılacağı net şekilde belgelenmiş oldu! 🎯
