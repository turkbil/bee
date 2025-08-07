# 🚀 AI V2 SİSTEMİ - MASTER PLANLAMA DOKÜMANTASYONu

## 📋 İÇİNDEKİLER

1. [Mevcut Sistem Analizi](#mevcut-sistem-analizi)
2. [Yeni Priority Sistemi](#yeni-priority-sistemi)
3. [Response Template Engine](#response-template-engine)
4. [Frontend AI Entegrasyonu](#frontend-ai-entegrasyonu)
5. [Kredi Sistemi Dönüşümü](#kredi-sistemi-dönüşümü)
6. [User-Based Kredi Sistemi](#user-based-kredi-sistemi)
7. [Implementasyon Yol Haritası](#implementasyon-yol-haritası)

---

## 🔍 MEVCUT SİSTEM ANALİZİ

### Güçlü Yönler
1. **İki Katmanlı Prompt Sistemi**
   - Quick Prompt: Feature'ın NE yapacağını tanımlar
   - Expert Prompt: NASIL yapacağının detayları (ai_prompts tablosundan)
   - Response Template: Sabit yanıt formatı

2. **AIPriorityEngine**
   - Weight-based scoring sistemi
   - Context type'a göre dinamik filtreleme
   - Category bazlı önceliklendirme

3. **Modüler Yapı**
   - Feature bazlı sistem
   - Çoktan çoğa prompt ilişkileri
   - Tenant-aware yapı

### Zayıf Yönler ve İyileştirme Alanları

1. **Response Format Sorunu**
   - Sürekli 1-2-3 şeklinde madde madde yanıtlar
   - Template sistemi tam kullanılmıyor
   - Feature'a göre dinamik yanıt formatı yok

2. **Priority Sistemi**
   - Feature bazlı priority değişimi yok
   - Bazı feature'larda gereksiz prompt'lar kullanılıyor
   - Marka bilgileri her yerde kullanılıyor (SEO'da gereksiz)

3. **Frontend Eksikliği**
   - Sadece admin panel'de kullanılıyor
   - Public API endpoint'leri yok
   - Widget entegrasyonu yok

4. **Token → Kredi Geçişi**
   - Hala token referansları var
   - Kredi paket sistemi yok
   - User-based kredi yönetimi yok

---

## 🎯 YENİ PRIORITY SİSTEMİ

### Feature-Aware Priority Engine

```php
// Her feature için özel priority mapping
const FEATURE_PRIORITY_MAPS = [
    'seo-analiz' => [
        'system_common' => 10000,      // ✅ Markdown yasağı önemli
        'system_hidden' => 9000,       // ✅ Güvenlik
        'feature_definition' => 8000,  // ✅ SEO tanımı
        'expert_knowledge' => 7000,    // ✅ SEO expertise
        'tenant_identity' => 2000,     // ❌ Düşük öncelik
        'secret_knowledge' => 1000,    // ❌ Gereksiz
        'brand_context' => 500,        // ❌ SEO'da marka bilgisi gereksiz
        'response_format' => 6000,     // ✅ SEO formatı önemli
        'conditional_info' => 1000,    // ❌ Düşük öncelik
    ],
    
    'blog-yaz' => [
        'system_common' => 10000,      
        'system_hidden' => 9000,       
        'feature_definition' => 8000,  
        'expert_knowledge' => 7000,    
        'tenant_identity' => 5000,     // ✅ Blog'da tenant önemli
        'secret_knowledge' => 3000,    
        'brand_context' => 6000,       // ✅ Marka bilgisi ÇOK önemli
        'response_format' => 4000,     
        'conditional_info' => 2000,    
    ],
    
    'kod-uret' => [
        'system_common' => 10000,      
        'system_hidden' => 9000,       
        'feature_definition' => 8000,  
        'expert_knowledge' => 9500,    // ✅ Kod expertise ÇOK yüksek
        'tenant_identity' => 1000,     // ❌ Kod'da tenant önemsiz
        'secret_knowledge' => 2000,    
        'brand_context' => 500,        // ❌ Kod'da marka gereksiz
        'response_format' => 7000,     // ✅ Kod formatı kritik
        'conditional_info' => 1000,    
    ],
];
```

### AI Provider Failover System

```php
// Tenant bazlı AI provider öncelik sırası
Schema::create('tenant_ai_providers', function ($table) {
    $table->id();
    $table->foreignId('tenant_id');
    $table->string('provider'); // openai, anthropic, deepseek
    $table->string('model');
    $table->integer('priority')->default(1); // 1 = ilk tercih
    $table->boolean('is_active')->default(true);
    $table->json('settings'); // API keys, endpoints
    $table->timestamps();
    
    $table->unique(['tenant_id', 'priority']);
});

// Failover çalışma mantığı
foreach ($tenant->aiProviders()->orderBy('priority')->get() as $provider) {
    try {
        return $this->callProvider($provider, $prompt);
    } catch (Exception $e) {
        Log::warning("Provider failed: {$provider->provider}");
        continue; // Sonraki provider'ı dene
    }
}
```

### Tenant Database Learning System

```php
// Otomatik schema analizi
class TenantDatabaseAnalyzer {
    public function analyzeTenantDatabase($tenant) {
        // Foreign key'lerden ilişkileri otomatik keşfet
        $foreignKeys = DB::select("
            SELECT * FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        // İlişki tiplerini belirle (belongs_to, has_many, many_to_many)
        foreach ($foreignKeys as $fk) {
            $relationships[] = [
                'from' => $fk->TABLE_NAME,
                'to' => $fk->REFERENCED_TABLE_NAME,
                'type' => $this->detectRelationType($fk)
            ];
        }
        
        return $this->generateSchemaPrompt($relationships);
    }
}
```

### Dynamic Context Filtering

```php
// Feature'a göre otomatik context type belirleme
const FEATURE_CONTEXT_TYPES = [
    // Technical features - minimal context
    'kod-uret' => 'minimal',
    'sql-sorgu' => 'minimal',
    'regex-olustur' => 'minimal',
    
    // SEO features - essential context
    'seo-analiz' => 'essential',
    'meta-etiket' => 'essential',
    'anahtar-kelime' => 'essential',
    
    // Content features - normal context
    'blog-yaz' => 'normal',
    'makale-olustur' => 'normal',
    'icerik-uret' => 'normal',
    
    // Brand features - detailed context
    'marka-analiz' => 'detailed',
    'kurumsal-metin' => 'detailed',
    'tanitim-metni' => 'detailed',
];
```

---

## 🎨 RESPONSE TEMPLATE ENGINE

### Problem: Monoton 1-2-3 Formatı

Mevcut sistemde her yanıt şu formatta:
```
1. Birinci madde
2. İkinci madde  
3. Üçüncü madde
```

### Çözüm: Dynamic Response Templates

```json
// ai_features.response_template JSON yapısı
{
    "format": "narrative|list|structured|code|table|mixed",
    "style": "professional|casual|academic|creative|technical",
    "sections": [
        {
            "type": "heading|paragraph|list|code|table|quote",
            "title": "Bölüm Başlığı",
            "format": "markdown|plain|html",
            "required": true,
            "minLength": 100,
            "maxLength": 500
        }
    ],
    "rules": [
        "no_numbering", // Otomatik numaralandırma yapma
        "use_paragraphs", // Paragraf formatı kullan
        "vary_structure", // Yapıyı değiştir
        "contextual_format" // İçeriğe göre format
    ],
    "examples": {
        "good": ["İyi örnek 1", "İyi örnek 2"],
        "bad": ["Kötü örnek 1", "Kötü örnek 2"]
    }
}
```

### Örnek Template'ler

#### Blog Yazısı Template
```json
{
    "format": "narrative",
    "style": "professional",
    "sections": [
        {
            "type": "paragraph",
            "title": "Giriş",
            "required": true,
            "minLength": 150,
            "instruction": "Konuya ilgi çekici bir giriş yap"
        },
        {
            "type": "heading",
            "title": "Ana Başlıklar",
            "subSections": true,
            "format": "## {{title}}"
        },
        {
            "type": "paragraph",
            "title": "İçerik",
            "required": true,
            "instruction": "Akıcı paragraflar halinde yaz"
        },
        {
            "type": "paragraph",
            "title": "Sonuç",
            "required": true,
            "instruction": "Özetleyici ve çağrı içeren sonuç"
        }
    ],
    "rules": [
        "no_numbering",
        "use_paragraphs",
        "natural_flow",
        "seo_friendly"
    ]
}
```

#### SEO Analizi Template
```json
{
    "format": "structured",
    "style": "technical",
    "sections": [
        {
            "type": "table",
            "title": "SEO Skoru",
            "columns": ["Metrik", "Değer", "Durum"],
            "required": true
        },
        {
            "type": "heading",
            "title": "Detaylı Analiz",
            "subSections": [
                "Title Etiketi",
                "Meta Description", 
                "Anahtar Kelimeler",
                "İçerik Kalitesi"
            ]
        },
        {
            "type": "list",
            "title": "İyileştirme Önerileri",
            "format": "bullet",
            "prioritized": true
        }
    ],
    "rules": [
        "technical_language",
        "data_driven",
        "actionable_insights"
    ]
}
```

---

## 🌐 FRONTEND AI ENTEGRASYONU

### 1. Public API Endpoints

```php
// routes/api.php
Route::prefix('ai/v1')->group(function () {
    // Public features (rate limited)
    Route::post('/chat', 'AIController@publicChat')
        ->middleware(['throttle:ai-public', 'ai.credits']);
    
    Route::post('/feature/{slug}', 'AIController@publicFeature')
        ->middleware(['throttle:ai-public', 'ai.credits']);
    
    // User authenticated features
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/chat/user', 'AIController@userChat');
        Route::post('/feature/user/{slug}', 'AIController@userFeature');
        Route::get('/credits/balance', 'AIController@creditBalance');
    });
});
```

### 2. Widget Entegrasyonu

```php
// AI Chat Widget
class AIChatWidget extends Widget
{
    public function render()
    {
        return view('ai::widgets.chat', [
            'features' => $this->getPublicFeatures(),
            'userCredits' => $this->getUserCredits(),
            'guestCredits' => $this->getGuestCredits()
        ]);
    }
}
```

### 3. Alpine.js Components

```javascript
// AI Chat Component
Alpine.data('aiChat', () => ({
    messages: [],
    input: '',
    loading: false,
    credits: 0,
    
    async sendMessage() {
        this.loading = true;
        
        const response = await fetch('/api/ai/v1/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                message: this.input,
                feature: this.selectedFeature
            })
        });
        
        const data = await response.json();
        this.messages.push(data.message);
        this.credits = data.remainingCredits;
        this.loading = false;
    }
}));
```

---

## 💳 KREDİ SİSTEMİ DÖNÜŞÜMÜ

### 1. Token → Kredi Terminoloji Değişimi

```php
// Eski: ai_tokens, ai_token_packages, ai_token_purchases
// Yeni: ai_credits, ai_credit_packages, ai_credit_purchases

// Migration
Schema::rename('ai_tokens', 'ai_credits');
Schema::rename('ai_token_packages', 'ai_credit_packages');
Schema::rename('ai_token_purchases', 'ai_credit_purchases');

// Model güncellemeleri
class AICredit extends Model // eski AIToken
class AICreditPackage extends Model // eski AITokenPackage
class AICreditPurchase extends Model // eski AITokenPurchase

// Service güncellemeleri
class AICreditService // eski AITokenService
{
    public function getCredits($tenant) // eski getTokens
    public function useCredits($tenant, $amount) // eski useTokens
    public function purchaseCredits($tenant, $package) // eski purchaseTokens
}
```

### 2. AI Provider Çarpan Sistemi

```php
// Provider bazlı maliyet çarpanları
const PROVIDER_MULTIPLIERS = [
    'openai' => [
        'gpt-3.5-turbo' => 1.0,      // Standart
        'gpt-4' => 10.0,             // 10x pahalı
        'gpt-4-turbo' => 8.0,        // 8x pahalı
    ],
    'anthropic' => [
        'claude-3-haiku' => 1.2,     
        'claude-3-sonnet' => 5.0,    
        'claude-3-opus' => 15.0,     // En pahalı
    ],
    'deepseek' => [
        'deepseek-chat' => 0.5,      // En ucuz
        'deepseek-coder' => 0.8,
    ]
];

// Tenant bazlı indirim/zam
class AITenantPricing {
    public float $discount_rate = 0.0; // %20 indirim için 0.2
    public float $markup_rate = 0.0;   // %50 zam için 0.5
    
    public function calculateFinalCost($baseCredits, $provider, $model) {
        $multiplier = PROVIDER_MULTIPLIERS[$provider][$model] ?? 1.0;
        $cost = $baseCredits * $multiplier;
        
        if ($this->discount_rate > 0) {
            $cost *= (1 - $this->discount_rate);
        }
        if ($this->markup_rate > 0) {
            $cost *= (1 + $this->markup_rate);
        }
        
        return ceil($cost);
    }
}
```

### 3. Kredi Paket Sistemi (Ayrı Paketler)

```php
Schema::create('ai_credit_packages', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->integer('credits');
    $table->decimal('price', 10, 2);
    $table->decimal('discount_price', 10, 2)->nullable();
    
    // Paket tipleri - AYRI PAKETLER
    $table->enum('type', ['tenant', 'user'])->default('tenant');
    $table->enum('billing_type', ['one_time', 'monthly', 'yearly'])->default('one_time');
    
    // Bonus sistemi
    $table->integer('bonus_credits')->default(0);
    $table->json('features')->nullable(); // Paket özellikleri
    
    // Limitleme
    $table->integer('max_purchases_per_month')->nullable();
    $table->boolean('is_featured')->default(false);
    
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

// Örnek Tenant Paketleri (Büyük)
$tenantPackages = [
    ['name' => 'Başlangıç', 'credits' => 10000, 'price' => 100],
    ['name' => 'Professional', 'credits' => 50000, 'price' => 400],
    ['name' => 'Enterprise', 'credits' => 200000, 'price' => 1500]
];

// Örnek User Paketleri (Küçük)
$userPackages = [
    ['name' => 'Mini', 'credits' => 100, 'price' => 5],
    ['name' => 'Standart', 'credits' => 500, 'price' => 20],
    ['name' => 'Plus', 'credits' => 2000, 'price' => 70]
];
```

---

## 👤 USER-BASED KREDİ SİSTEMİ

### 1. Hibrit Kredi Sistemi

```php
Schema::table('tenants', function (Blueprint $table) {
    // Müşteri mi kendi projemiz mi?
    $table->boolean('is_ai_reseller')->default(false);
    $table->enum('credit_priority', ['tenant_first', 'user_first'])->default('tenant_first');
    $table->boolean('unlimited_user_credits')->default(false); // Kendi projeler için
    $table->json('user_credit_settings')->nullable();
});

// Kredi kontrol mantığı
class AICreditService {
    public function checkCredits($user, $amount, $tenant) {
        // Kendi projemiz ve unlimited açıksa
        if (!$tenant->is_ai_reseller && $tenant->unlimited_user_credits) {
            return true; // Sınırsız kullanım
        }
        
        // Müşteri tenant'ı
        if ($tenant->is_ai_reseller) {
            // Önce tenant kredisine bak
            if ($tenant->credit_priority === 'tenant_first') {
                if ($this->checkTenantCredits($tenant, $amount)) {
                    $this->useTenantCredits($tenant, $amount);
                    return true;
                }
            }
            
            // User kredisi kontrol et
            if ($this->checkUserCredits($user, $amount)) {
                $this->useUserCredits($user, $amount);
                // Tenant'tan da düş
                $this->useTenantCredits($tenant, $amount);
                return true;
            }
        }
        
        return false;
    }
}
```

### 2. User Credits Tablosu

```php
Schema::create('ai_user_credits', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->integer('balance')->default(0);
    $table->integer('total_purchased')->default(0);
    $table->integer('total_used')->default(0);
    $table->integer('monthly_limit')->nullable();
    $table->integer('monthly_used')->default(0);
    $table->timestamp('limit_reset_at')->nullable();
    $table->json('metadata')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'balance']);
});
```

### 3. Credit Mode Logic

```php
class AICreditService
{
    public function canUseCredits($entity, $amount)
    {
        $tenant = tenant();
        
        switch ($tenant->credit_mode) {
            case 'tenant_only':
                return $this->checkTenantCredits($tenant, $amount);
                
            case 'user_only':
                return $this->checkUserCredits($entity, $amount);
                
            case 'mixed':
                // Önce user kredisi, sonra tenant kredisi
                if ($this->checkUserCredits($entity, $amount)) {
                    return true;
                }
                return $this->checkTenantCredits($tenant, $amount);
        }
    }
}
```

### 4. Paket Stratejisi

**Senaryo 1: Ayrı Paketler**
- Tenant paketleri: Büyük, kurumsal
- User paketleri: Küçük, bireysel
- Avantaj: Esneklik
- Dezavantaj: Karmaşık yönetim

**Senaryo 2: Ortak Paketler** ✅ ÖNERİLEN
- Tek paket listesi
- `type` field ile ayırma
- Avantaj: Basit yönetim
- Dezavantaj: Daha az esneklik

```php
// Ortak paket sistemi
$packages = AICreditPackage::query()
    ->when($forUser, fn($q) => $q->whereIn('type', ['user', 'both']))
    ->when($forTenant, fn($q) => $q->whereIn('type', ['tenant', 'both']))
    ->active()
    ->ordered()
    ->get();
```

---

## 🔧 GLOBAL SİSTEM ÖZELLİKLERİ

### 1. Global AI Middleware

```php
class GlobalAIMiddleware {
    public function handle($request, Closure $next) {
        $tenant = tenant();
        
        // AI özelliği aktif mi?
        if (!$tenant->hasFeature('ai_system')) {
            return response()->json(['error' => 'AI feature not enabled'], 403);
        }
        
        $response = $next($request);
        
        // Otomatik işlemler
        if ($response->isSuccessful()) {
            $this->logAIUsage($request, $response);
            $this->recordAnalytics($request, $response);
            
            // Kredi bilgisini response'a ekle
            $data = $response->getData(true);
            $data['credit_info'] = $this->getCreditInfo();
            $response->setData($data);
        }
        
        return $response;
    }
}
```

### 2. Module/Tenant Toggle System

```php
Schema::create('ai_feature_toggles', function ($table) {
    $table->id();
    $table->morphs('toggleable'); // tenant veya module
    $table->boolean('ai_enabled')->default(false);
    $table->json('enabled_features');
    $table->json('disabled_features');
    $table->timestamps();
});

// Helper function
function ai_enabled($feature = null) {
    $tenant = tenant();
    $toggle = AIFeatureToggle::where('toggleable_type', 'tenant')
        ->where('toggleable_id', $tenant->id)
        ->first();
    
    if (!$toggle || !$toggle->ai_enabled) {
        return false;
    }
    
    if ($feature) {
        return in_array($feature, $toggle->enabled_features ?? []);
    }
    
    return true;
}
```

### 3. Merkezi Harcama Sistemi

```php
trait HasAICredits {
    public function beforeAIRequest($creditsNeeded) {
        if (!$this->hasEnoughCredits($creditsNeeded)) {
            throw new InsufficientCreditsException([
                'needed' => $creditsNeeded,
                'available' => $this->getAvailableCredits(),
                'purchase_url' => route('ai.credits.purchase'),
                'message' => 'Yetersiz kredi. Satın almak için tıklayın.'
            ]);
        }
    }
    
    public function afterAIRequest($response) {
        $creditsUsed = $this->calculateCreditsFromResponse($response);
        $this->useCredits($creditsUsed);
        
        AIUsageLog::create([
            'tenant_id' => tenant()->id,
            'user_id' => auth()->id(),
            'credits_used' => $creditsUsed,
            'provider' => $response['provider'],
            'model' => $response['model'],
            'tokens' => $response['tokens'],
            'response_time' => $response['time']
        ]);
        
        event(new AIRequestCompleted($this, $creditsUsed, $response));
    }
}
```

## 📊 İMPLEMENTASYON YOL HARİTASI

### Faz 1: Priority Engine Güncellemesi (1-2 gün)
1. ✅ Feature-based priority mapping
2. ✅ Dynamic context filtering
3. ✅ Brand context devre dışı bırakma logic'i
4. ✅ Performance optimizasyonu

### Faz 2: Response Template Engine (2-3 gün)
1. ✅ Template parser servisi
2. ✅ Dynamic response formatter
3. ✅ Feature template library
4. ✅ Template validation

### Faz 3: Kredi Sistemi Dönüşümü (1-2 gün)
1. ✅ Database migration
2. ✅ Model/Service güncellemeleri
3. ✅ UI güncellemeleri
4. ✅ Backwards compatibility

### Faz 4: Frontend Entegrasyonu (3-4 gün)
1. ✅ Public API endpoints
2. ✅ Rate limiting & security
3. ✅ Widget geliştirme
4. ✅ Alpine.js components

### Faz 5: User-Based Sistem (2-3 gün)
1. ✅ User credit tabloları
2. ✅ Credit mode logic
3. ✅ Purchase flow
4. ✅ Admin yönetim paneli

### Faz 6: Test & Optimizasyon (2 gün)
1. ✅ Unit testler
2. ✅ Integration testler
3. ✅ Performance testleri
4. ✅ User acceptance testleri

**Toplam Süre: ~2 hafta**

---

## 🎯 KRİTİK BAŞARI FAKTÖRLERİ

1. **Geriye Uyumluluk**: Mevcut API'ler çalışmaya devam etmeli
2. **Performance**: Response time %20'den fazla artmamalı
3. **Esneklik**: Yeni feature'lar kolayca eklenebilmeli
4. **UX**: Kullanıcı deneyimi kesintisiz olmalı
5. **Güvenlik**: Rate limiting ve credit validation sıkı olmalı

---

## 📝 NOTLAR

- Priority engine her AI çağrısında kullanılacak
- Response template'ler cache'lenecek
- Credit sistemi event-driven olacak
- Frontend cache stratejisi önemli
- User kredileri tenant kredilerinden bağımsız