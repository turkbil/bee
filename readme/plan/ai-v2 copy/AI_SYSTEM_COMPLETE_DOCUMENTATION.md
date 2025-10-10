# 🚀 AI SİSTEMİ KOMPLE DOKÜMANTASYON

## 📋 İÇİNDEKİLER
1. [Genel Bakış](#genel-bakış)
2. [Priority Engine (Öncelik Motoru)](#priority-engine)
3. [Response Template Engine](#response-template-engine)
4. [Smart Response Formatter](#smart-response-formatter)
5. [Credit Sistemi](#credit-sistemi)
6. [Veritabanı Öğrenme Sistemi](#veritabanı-öğrenme-sistemi)
7. [Modüler Chat Widget](#modüler-chat-widget)
8. [Chat Panel Aktivasyon](#chat-panel-aktivasyon)
9. [Frontend API Entegrasyonu](#frontend-api-entegrasyonu)

---

## 🎯 GENEL BAKIŞ

### Sistem Mimarisi
```
AI Sistemi
├── Priority Engine (Provider seçimi ve fallback)
├── Response Template Engine (Yanıt formatları)
├── Smart Response Formatter (Akıllı formatlama)
├── Credit System (Tenant & User bazlı)
├── Database Learning (Veritabanı ilişki öğrenme)
├── Chat Widget (Modüler sohbet)
└── Frontend API (Public erişim)
```

### Temel Prensipler
1. **Universal AI**: Tek bir global AI sistemi - kredisi olan herkes kullanır
2. **Credit Based**: Tenant ve User bazlı ayrı kredi paketleri
3. **Multi-Provider**: Çoklu AI sağlayıcı desteği ve otomatik fallback
4. **Smart Formatting**: Feature'a göre strict/flexible yanıt formatı
5. **Database Learning**: Aktif modüllerin veritabanı ilişkilerini öğrenme
6. **Modular Widget**: Her yerde kullanılabilir chat widget

---

## 🔄 PRIORITY ENGINE

### Konsept
Provider seçimi için öncelik tabanlı sistem. Hata durumunda otomatik fallback.

### Veritabanı Yapısı
```sql
ai_providers:
- id
- name (openai, deepseek, anthropic)
- is_active
- is_default
- priority (1-10)
- api_key
- rate_limit
- monthly_quota
- used_quota
```

### Çalışma Mantığı
```php
class PriorityEngine {
    public function selectProvider() {
        // 1. Default provider'ı dene
        $default = AIProvider::where('is_default', true)->first();
        if ($this->isProviderAvailable($default)) {
            return $default;
        }
        
        // 2. Priority sırasına göre dene
        $providers = AIProvider::where('is_active', true)
            ->orderBy('priority', 'asc')
            ->get();
            
        foreach ($providers as $provider) {
            if ($this->isProviderAvailable($provider)) {
                return $provider;
            }
        }
        
        throw new NoAvailableProviderException();
    }
    
    private function isProviderAvailable($provider) {
        return $provider->is_active 
            && !empty($provider->api_key)
            && $provider->used_quota < $provider->monthly_quota
            && !$this->isRateLimited($provider);
    }
}
```

### Provider Özellikleri
```php
PROVIDER_FEATURES = [
    'openai' => [
        'models' => ['gpt-4', 'gpt-3.5-turbo'],
        'max_tokens' => 4096,
        'supports_functions' => true,
        'supports_vision' => true
    ],
    'deepseek' => [
        'models' => ['deepseek-chat', 'deepseek-coder'],
        'max_tokens' => 16384,
        'supports_functions' => false,
        'supports_vision' => false
    ],
    'anthropic' => [
        'models' => ['claude-3-opus', 'claude-3-sonnet'],
        'max_tokens' => 100000,
        'supports_functions' => true,
        'supports_vision' => true
    ]
];
```

---

## 📝 RESPONSE TEMPLATE ENGINE

### İki Katmanlı Prompt Yapısı
1. **Quick Prompt**: Feature'ın NE yapacağı
2. **Expert Prompt**: NASIL yapacağı (detaylı)
3. **Response Template**: Sabit yanıt formatı

### Veritabanı Yapısı
```sql
ai_features:
- quick_prompt (TEXT)
- expert_prompt_id (FK -> ai_prompts)
- response_template (JSON)
- strictness_level (ENUM: strict, flexible, adaptive)

ai_prompts:
- id
- title
- content (Detaylı prompt)
- priority
```

### Örnek Feature
```php
// Çeviri Feature
[
    'quick_prompt' => 'Sen bir çeviri uzmanısın. Verilen metni hedef dile çevir.',
    'expert_prompt_id' => 5, // "İçerik Üretim Uzmanı"
    'response_template' => [
        'format' => 'translated_text',
        'preserve_formatting' => true,
        'show_original' => false
    ],
    'strictness_level' => 'strict' // Format korunmalı
]
```

### Template Örnekleri
```json
// Blog Yazısı Template
{
    "format": "article",
    "sections": ["intro", "body", "conclusion"],
    "use_headings": true,
    "include_examples": true,
    "tone": "professional"
}

// SEO Analiz Template
{
    "format": "analysis",
    "sections": ["keywords", "content", "meta", "score"],
    "use_tables": true,
    "scoring": true,
    "recommendations": true
}
```

---

## 🎨 SMART RESPONSE FORMATTER

### Konsept
**PROBLEM**: AI her yanıtta monoton 1-2-3 listeler kullanıyor. 
**ÇÖZÜM**: Feature'a ve içeriğe göre dinamik format belirleme.

### Strictness Levels (Katılık Seviyeleri)
```php
const STRICTNESS_LEVELS = [
    // STRICT - Format değişmez
    'ceviri' => 'strict',        // Soru nasılsa yanıt öyle
    'kod-uret' => 'strict',      // Kod bloğu formatı
    'sql-sorgu' => 'strict',     // SQL formatı
    
    // FLEXIBLE - Karma format
    'blog-yaz' => 'flexible',    // Paragraf, liste, tablo karışık
    'seo-analiz' => 'flexible',  // Analiz tabloları, maddeler
    'makale-olustur' => 'flexible',
    
    // ADAPTIVE - Tamamen serbest
    'icerik-uret' => 'adaptive',
    'yaratici-yaz' => 'adaptive',
];
```

### Format Algılama ve Uygulama
```php
class SmartResponseFormatter {
    public function format($input, $output, $feature) {
        $strictness = STRICTNESS_LEVELS[$feature] ?? 'flexible';
        
        switch($strictness) {
            case 'strict':
                return $this->strictFormat($input, $output);
            case 'flexible':
                return $this->flexibleFormat($input, $output, $feature);
            case 'adaptive':
                return $this->adaptiveFormat($output);
        }
    }
    
    private function strictFormat($input, $output) {
        // INPUT formatını KORUR
        $inputFormat = $this->detectFormat($input);
        
        switch($inputFormat) {
            case 'numbered_list':
                return $this->convertToNumberedList($output);
            case 'bullet_list':
                return $this->convertToBulletList($output);
            case 'table':
                return $this->convertToTable($output);
            default:
                return $output; // Olduğu gibi
        }
    }
    
    private function flexibleFormat($input, $output, $feature) {
        // İçeriğe göre UYGUN format seçer
        
        // Blog yazısı için
        if ($feature === 'blog-yaz') {
            $segments = [];
            
            // Giriş paragrafı
            $segments[] = $this->extractIntro($output);
            
            // Karşılaştırma varsa → Tablo
            if ($this->hasComparison($output)) {
                $segments[] = $this->createComparisonTable($output);
            }
            
            // Ana noktalar → Başlıklı bölümler
            if ($this->hasMainPoints($output)) {
                $segments[] = $this->createHeadingSections($output);
            }
            
            // Örnekler → Kod blokları veya kutular
            if ($this->hasExamples($output)) {
                $segments[] = $this->formatExamples($output);
            }
            
            // Sonuç paragrafı
            $segments[] = $this->extractConclusion($output);
            
            return implode("\n\n", $segments);
        }
        
        // SEO Analizi için
        if ($feature === 'seo-analiz') {
            return $this->formatSeoAnalysis($output);
        }
    }
    
    private function formatSeoAnalysis($content) {
        $formatted = [];
        
        // Genel skor tablosu
        $formatted[] = "## SEO Analiz Sonuçları\n";
        $formatted[] = $this->createScoreTable($content);
        
        // Anahtar kelime analizi
        $formatted[] = "\n### 🔍 Anahtar Kelime Analizi";
        $formatted[] = $this->extractKeywordAnalysis($content);
        
        // İçerik önerileri - madde listesi
        $formatted[] = "\n### 📝 İçerik Önerileri";
        $formatted[] = $this->createBulletList($this->extractSuggestions($content));
        
        // Teknik detaylar - tablo
        $formatted[] = "\n### ⚙️ Teknik Detaylar";
        $formatted[] = $this->createTechnicalTable($content);
        
        return implode("\n", $formatted);
    }
}
```

### Örnek Dönüşümler

#### Monoton Format (ESKİ)
```
1. Başlık etiketi eksik
2. Meta açıklama çok kısa
3. Anahtar kelime yoğunluğu düşük
4. Görsel alt metni yok
5. İç bağlantı eksik
```

#### Akıllı Format (YENİ)
```markdown
## SEO Analiz Sonuçları

| Kriter | Durum | Puan |
|--------|-------|------|
| Başlık Etiketi | ❌ Eksik | 0/20 |
| Meta Açıklama | ⚠️ Kısa | 5/20 |
| Anahtar Kelime | ⚠️ Düşük | 10/20 |

### 📝 Kritik Eksikler
• **Başlık etiketi** tamamen eksik - hemen ekleyin
• **Meta açıklama** sadece 120 karakter (min 150 olmalı)

### ✅ Yapılması Gerekenler
Öncelikle başlık etiketini ekleyin. Ardından meta açıklamayı 
150-160 karakter aralığına genişletin. Anahtar kelime yoğunluğunu 
%2-3 aralığına çıkarın.
```

### Feature-Specific Formatters

```php
// ÇEVİRİ - Strict formatter
class TranslationFormatter {
    public function format($input, $output) {
        // GİRDİ NE İSE ÇIKTI O
        // Paragraf → Paragraf
        // Liste → Liste 
        // Tablo → Tablo
        // Kendi fikir KATMAZ
    }
}

// BLOG - Flexible formatter  
class BlogFormatter {
    public function shouldUseTable($content) {
        return preg_match('/(karşılaştır|fark|vs|avantaj.*dezavantaj)/i', $content);
    }
    
    public function shouldUseList($content) {
        return preg_match('/(özellik|adım|yöntem|ipucu)/i', $content);
    }
    
    public function shouldUseParagraph($content) {
        return strlen($content) < 200 || $this->isNarrative($content);
    }
}
```

---

## 💰 CREDIT SİSTEMİ

### Dinamik Multiplier Sistemi
```php
// PROVIDER ÇARPANLARI - Admin panelden değiştirilebilir!
ai_provider_models tablosu:
- provider_id
- model_name
- base_multiplier (1K token = X credit)
- discount_rate (0.0 - 1.0) // %50 indirim = 0.5
- surge_rate (1.0 - 3.0)    // 2x zam = 2.0
- effective_multiplier      // Hesaplanan: base * (1-discount) * surge

// Örnek hesaplama
$effectiveMultiplier = $baseMultiplier * (1 - $discountRate) * $surgeRate;
// GPT-4: 30 * (1 - 0.2) * 1.0 = 24 credit (20% indirimli)
// DeepSeek: 0.14 * (1 - 0) * 2.0 = 0.28 credit (2x zamlı)
```

### Tenant & User Ayrı Paket Sistemleri

#### Tenant Paketleri (Büyük)
```php
const TENANT_PACKAGES = [
    'startup' => [
        'credits' => 10000,
        'price' => 99.99,
        'description' => 'Startup paketi'
    ],
    'business' => [
        'credits' => 50000,
        'price' => 399.99,
        'description' => 'İşletme paketi'
    ],
    'enterprise' => [
        'credits' => 200000,
        'price' => 1499.99,
        'description' => 'Kurumsal paket'
    ],
    'unlimited_monthly' => [
        'credits' => -1, // Sınırsız
        'price' => 2999.99,
        'duration' => 30, // gün
        'description' => 'Aylık sınırsız'
    ]
];
```

#### User Paketleri (Küçük)
```php
const USER_PACKAGES = [
    'trial' => [
        'credits' => 50,
        'price' => 0,
        'description' => 'Deneme paketi'
    ],
    'mini' => [
        'credits' => 100,
        'price' => 4.99,
        'description' => 'Mini paket'
    ],
    'standard' => [
        'credits' => 500,
        'price' => 19.99,
        'description' => 'Standart paket'
    ],
    'plus' => [
        'credits' => 2000,
        'price' => 69.99,
        'description' => 'Plus paket'
    ]
];
```

### Credit Mode Sistemi
```php
// Tenant ayarları
tenant_settings:
- credit_mode: ENUM('tenant_only', 'user_only', 'mixed', 'unlimited')
- credit_priority: ENUM('tenant_first', 'user_first') // mixed için
- allow_user_purchase: BOOLEAN

// Kullanım mantığı
class CreditManager {
    public function canUseAI($tenantId, $userId, $requiredCredits) {
        $tenant = Tenant::find($tenantId);
        
        switch($tenant->credit_mode) {
            case 'unlimited':
                // Bizim kendi projelerimiz - sınırsız
                return true;
                
            case 'tenant_only':
                // Sadece tenant kredisi kontrol edilir
                return $this->getTenantBalance($tenantId) >= $requiredCredits;
                
            case 'user_only':
                // Sadece user kredisi kontrol edilir
                return $this->getUserBalance($userId) >= $requiredCredits;
                
            case 'mixed':
                // Öncelik sırasına göre kontrol
                if ($tenant->credit_priority === 'user_first') {
                    $userBalance = $this->getUserBalance($userId);
                    if ($userBalance >= $requiredCredits) {
                        return ['use' => 'user', 'balance' => $userBalance];
                    }
                    $tenantBalance = $this->getTenantBalance($tenantId);
                    return $tenantBalance >= $requiredCredits ? 
                        ['use' => 'tenant', 'balance' => $tenantBalance] : false;
                } else {
                    // tenant_first - varsayılan
                    $tenantBalance = $this->getTenantBalance($tenantId);
                    if ($tenantBalance >= $requiredCredits) {
                        return ['use' => 'tenant', 'balance' => $tenantBalance];
                    }
                    $userBalance = $this->getUserBalance($userId);
                    return $userBalance >= $requiredCredits ? 
                        ['use' => 'user', 'balance' => $userBalance] : false;
                }
        }
    }
}
```

### Kullanım Takibi
```sql
ai_credit_usage:
- tenant_id
- user_id
- credit_source ('tenant' | 'user')
- feature_slug
- provider_name
- model_name
- input_tokens
- output_tokens
- credits_used
- credit_cost
- effective_multiplier
- used_at
```

### Otomatik Kredi Kontrolü
```php
// Her AI isteğinde otomatik çalışır
trait HasCreditCheck {
    public function checkAndDeductCredits($feature, $tokens) {
        $requiredCredits = $this->calculateCredits($tokens);
        $canUse = CreditManager::canUseAI(
            tenant()->id, 
            auth()->id(), 
            $requiredCredits
        );
        
        if (!$canUse) {
            // Otomatik yönlendirme
            throw new InsufficientCreditsException(
                message: 'Kredi bakiyeniz yetersiz',
                redirectTo: route('credits.purchase')
            );
        }
        
        // Kredi düşme
        $this->deductCredits($canUse['use'], $requiredCredits);
        
        // Debug kaydı
        Log::channel('ai_credits')->info('Credit used', [
            'tenant' => tenant()->id,
            'user' => auth()->id(),
            'source' => $canUse['use'],
            'amount' => $requiredCredits,
            'feature' => $feature
        ]);
    }
}
```

---

## 🧠 VERİTABANI ÖĞRENME SİSTEMİ

### Konsept
AI'nın tenant'ın aktif modüllerinin veritabanı yapısını öğrenmesi ve müşterilere bu veriler hakkında bilgi verebilmesi.

### Otomatik İlişki Keşfi
```php
class DatabaseLearningService {
    public function discoverRelations($tenantId) {
        $tenant = Tenant::find($tenantId);
        $activeModules = $tenant->activeModules();
        $discoveries = [];
        
        foreach ($activeModules as $module) {
            // 1. Foreign Key'lerden ilişki keşfi
            $tables = $this->getModuleTables($module);
            foreach ($tables as $table) {
                $foreignKeys = $this->getForeignKeys($table);
                foreach ($foreignKeys as $fk) {
                    $discoveries[] = [
                        'type' => 'foreign_key',
                        'from_table' => $table,
                        'from_column' => $fk->column,
                        'to_table' => $fk->referenced_table,
                        'to_column' => $fk->referenced_column,
                        'confidence' => 1.0 // FK = kesin ilişki
                    ];
                }
            }
            
            // 2. İsimlendirme pattern'lerinden tahmin
            $discoveries = array_merge(
                $discoveries, 
                $this->guessRelationsByNaming($tables)
            );
            
            // 3. Model dosyalarından ilişki çıkarımı
            $discoveries = array_merge(
                $discoveries,
                $this->extractFromModels($module)
            );
        }
        
        return $discoveries;
    }
}
```

### Manuel İlişki Tanımlama
```php
// Admin Panel UI
ai_database_relations:
- id
- tenant_id
- module_name
- relation_type (belongsTo, hasMany, manyToMany)
- from_table
- from_column
- to_table
- to_column
- pivot_table (manyToMany için)
- description
- is_verified (admin onayı)

// Örnek e-ticaret ilişkileri
[
    [
        'from_table' => 'products',
        'from_column' => 'category_id',
        'to_table' => 'categories',
        'to_column' => 'id',
        'relation_type' => 'belongsTo',
        'description' => 'Her ürün bir kategoriye ait'
    ],
    [
        'from_table' => 'product_prices',
        'from_column' => 'product_id',
        'to_table' => 'products',
        'to_column' => 'id',
        'relation_type' => 'belongsTo',
        'description' => 'Ürün fiyat geçmişi'
    ]
]
```

### AI Context Builder
```php
class AIContextBuilder {
    public function buildDatabaseContext($tenantId, $question) {
        // 1. Sorudan ilgili tabloları çıkar
        $tables = $this->extractTablesFromQuestion($question);
        
        // 2. Schema bilgilerini topla
        $schema = [];
        foreach ($tables as $table) {
            $schema[$table] = [
                'columns' => $this->getTableColumns($table),
                'relations' => $this->getTableRelations($table),
                'sample_data' => $this->getSampleData($table, 3)
            ];
        }
        
        // 3. AI için context oluştur
        return [
            'database_schema' => $schema,
            'available_relations' => $this->getVerifiedRelations($tenantId),
            'query_examples' => $this->getQueryExamples($tables)
        ];
    }
}
```

### Akıllı Sorgu Oluşturucu
```php
class AIQueryBuilder {
    public function buildQuery($naturalLanguageQuery, $context) {
        // Örnek: "Elektronik kategorisindeki en pahalı ürün hangisi?"
        
        // 1. NLP ile parçala
        $parsed = $this->parseQuery($naturalLanguageQuery);
        // {
        //     'action': 'find',
        //     'target': 'product',
        //     'filters': ['category' => 'Elektronik'],
        //     'order': ['price' => 'desc'],
        //     'limit': 1
        // }
        
        // 2. İlişkileri kullanarak SQL oluştur
        $sql = $this->generateSQL($parsed, $context);
        // SELECT p.*, pr.amount as price
        // FROM products p
        // JOIN categories c ON p.category_id = c.id
        // JOIN product_prices pr ON pr.product_id = p.id
        // WHERE c.name = 'Elektronik'
        // ORDER BY pr.amount DESC
        // LIMIT 1
        
        return [
            'sql' => $sql,
            'bindings' => ['Elektronik'],
            'explanation' => $this->explainQuery($sql)
        ];
    }
}
```

### Güvenlik Katmanı
```php
class DatabaseSecurityLayer {
    // İzin verilen işlemler
    const ALLOWED_OPERATIONS = ['SELECT', 'COUNT', 'SUM', 'AVG', 'MAX', 'MIN'];
    
    // Hassas tablolar
    const RESTRICTED_TABLES = ['users', 'passwords', 'api_keys', 'payment_methods'];
    
    public function validateQuery($sql) {
        // 1. Sadece SELECT sorgularına izin ver
        if (!$this->isReadOnly($sql)) {
            throw new SecurityException('Sadece okuma işlemlerine izin verilir');
        }
        
        // 2. Hassas tabloları kontrol et
        if ($this->accessesRestrictedTable($sql)) {
            throw new SecurityException('Bu tabloya erişim yasak');
        }
        
        // 3. Rate limiting
        if (!$this->checkRateLimit()) {
            throw new RateLimitException('Çok fazla sorgu');
        }
        
        return true;
    }
}
```

### Öğrenme Süreci
```php
// 1. İlk kurulum - Otomatik keşif
$discoveries = DatabaseLearningService::discoverRelations($tenantId);

// 2. Admin onayı
foreach ($discoveries as $discovery) {
    if ($discovery['confidence'] < 0.8) {
        // Düşük güvenli tahminler admin onayına sunulur
        PendingRelation::create($discovery);
    } else {
        // Yüksek güvenli (FK) otomatik onaylanır
        VerifiedRelation::create($discovery);
    }
}

// 3. AI kullanımı
$userQuestion = "En çok satan ürünler hangileri?";
$context = AIContextBuilder::buildDatabaseContext($tenantId, $userQuestion);
$query = AIQueryBuilder::buildQuery($userQuestion, $context);
$results = DB::select($query['sql'], $query['bindings']);
$response = AIResponseFormatter::format($results, 'table');
```

### Modül-Specific Öğrenme
```php
// Her modülün kendi schema helper'ı
interface ModuleSchemaInterface {
    public function getTables(): array;
    public function getRelations(): array;
    public function getSampleQueries(): array;
}

// E-ticaret modülü örneği
class EcommerceSchema implements ModuleSchemaInterface {
    public function getRelations(): array {
        return [
            'products.category_id' => 'categories.id',
            'order_items.product_id' => 'products.id',
            'order_items.order_id' => 'orders.id',
            'orders.customer_id' => 'customers.id'
        ];
    }
    
    public function getSampleQueries(): array {
        return [
            'En çok satan ürünler' => 'SELECT ... FROM order_items...',
            'Kategori bazlı satışlar' => 'SELECT ... JOIN categories...',
            'Müşteri sipariş geçmişi' => 'SELECT ... FROM orders...'
        ];
    }
}
```

---

## 🔌 MODÜLER CHAT WIDGET

### Widget Özellikleri
- **Multi-position**: Fixed, relative, absolute
- **Multi-instance**: Aynı sayfada birden fazla
- **Theme Support**: Light, dark, custom
- **Responsive**: Mobile uyumlu
- **Event System**: Custom events
- **State Management**: LocalStorage

### Kullanım
```blade
<!-- Sabit köşede -->
<x-ai::chat-widget 
    :position="'fixed'"
    :fixedPosition="'bottom-right'"
    :theme="'dark'"
/>

<!-- Sayfa içinde -->
<x-ai::chat-widget 
    :position="'relative'"
    :height="'400px'"
    :context="['page_id' => $page->id]"
/>

<!-- Özel konumda -->
<x-ai::chat-widget 
    :position="'absolute'"
    :top="'100px'"
    :right="'20px'"
/>
```

### JavaScript API
```javascript
// Widget oluştur
const widget = new AIChatWidget({
    widgetId: 'main-chat',
    position: 'fixed',
    theme: 'dark',
    apiEndpoint: '/admin/ai/stream'
});

// Event dinle
widget.on('message:sent', (data) => {
    console.log('Mesaj gönderildi:', data);
});

// Programatik mesaj gönder
widget.sendMessage('Merhaba!');

// Widget'ı minimize et
widget.minimize();
```

---

## 🚀 CHAT PANEL AKTİVASYON

### Kontrol Listesi
1. **Provider Kontrolü**
   ```sql
   -- En az 1 aktif provider
   SELECT * FROM ai_providers WHERE is_active = 1;
   
   -- Default provider
   SELECT * FROM ai_providers WHERE is_default = 1;
   ```

2. **API Key Kontrolü**
   ```sql
   -- API key dolu mu?
   SELECT name, api_key FROM ai_providers WHERE is_active = 1;
   ```

3. **Credit Kontrolü**
   ```php
   // Tenant credit bakiyesi
   $balance = ai_get_credit_balance($tenantId);
   if ($balance <= 0) {
       // Credit ekle
       ai_add_credits($tenantId, 1000);
   }
   ```

### Test Senaryosu
1. http://laravel.test/admin/ai adresine git
2. "Merhaba" mesajı gönder
3. Streaming yanıt gelmeli
4. Credit düşmeli
5. Konuşma kaydedilmeli

---

## 🌐 FRONTEND API ENTEGRASYONU

### Public Endpoint
```php
// routes/api.php
Route::prefix('api/v1/ai')->group(function () {
    Route::post('/chat', 'AIChatController@publicChat')
        ->middleware(['throttle:ai', 'verify.api.credits']);
    
    Route::post('/feature/{slug}', 'AIFeatureController@execute')
        ->middleware(['throttle:ai', 'verify.api.credits']);
});
```

### Request Format
```json
{
    "message": "Blog yazısı yaz",
    "feature_slug": "blog-yaz",
    "context": {
        "topic": "Laravel",
        "length": "medium",
        "tone": "professional"
    },
    "api_key": "tenant_api_key_here"
}
```

### Response Format
```json
{
    "success": true,
    "response": "AI yanıtı...",
    "formatted_response": "<h2>Başlık</h2><p>İçerik...</p>",
    "credits_used": 125,
    "remaining_credits": 875,
    "metadata": {
        "provider": "deepseek",
        "model": "deepseek-chat",
        "tokens": 1250
    }
}
```

### Rate Limiting
```php
// Tenant bazlı rate limit
RateLimiter::for('ai', function (Request $request) {
    $tenant = $request->user()?->tenant;
    
    return [
        Limit::perMinute(10)->by($tenant?->id),
        Limit::perHour(100)->by($tenant?->id),
        Limit::perDay(1000)->by($tenant?->id)
    ];
});
```

---

## 🎯 ÖZET

### Tamamlanan Özellikler
1. ✅ **Universal AI**: Tek global sistem - kredisi olan herkes kullanır
2. ✅ **Smart Response Formatter**: Monoton 1-2-3 yerine dinamik formatlar
3. ✅ **Dinamik Credit Sistemi**: 
   - Tenant & User ayrı paket sistemleri
   - Provider bazlı dinamik multiplier (indirim/zam yapılabilir)
   - Unlimited mode (kendi projelerimiz için)
4. ✅ **Veritabanı Öğrenme**: 
   - Otomatik foreign key keşfi
   - Manuel ilişki tanımlama
   - Güvenli sorgu oluşturma
5. ✅ **Priority Engine**: Provider fallback sistemi
6. ✅ **Modular Widget**: Her yerde kullanılabilir chat widget
7. ✅ **Frontend API**: Rate limiting ile hazır

### Kritik Noktalar
- **AI Features ortak**: Modül bağımsız, herkes kullanır
- **Kredi kontrolü otomatik**: Her istekte kontrol, yoksa satın alma sayfasına yönlendirme
- **Chat panel odağı**: http://laravel.test/admin/ai aktif edilecek
- **Widget hazırlığı**: Aynı kod her yerde çalışacak şekilde modüler

### Aktivasyon İçin Yapılacaklar
1. Provider kontrolü (en az 1 aktif)
2. API key kontrolü
3. Default provider tanımlı mı?
4. Tenant'a başlangıç kredisi ekle
5. Test et!

**HATIRLATMA**: Tüm yanıtlar ve dokümantasyon Türkçe!