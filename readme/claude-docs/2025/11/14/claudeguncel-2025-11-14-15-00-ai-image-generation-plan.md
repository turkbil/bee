# AI Otomatik Görsel Üretimi - Analiz ve Uygulama Planı

**Tarih:** 2025-11-14
**Konu:** AI ile otomatik görsel üretimi + MediaManagement entegrasyonu
**Kullanıcı İsteği:** Prompt'a göre AI görsel üretimi, media DB'ye kaydetme, kredi düşürme sistemi

---

## 📊 MEVCUT SİSTEM ANALİZİ

### 1. MediaManagement Modülü (Universal System)

**Modül Konumu:** `Modules/MediaManagement/`

**Ana Bileşenler:**
```php
// Universal Media Component
Modules/MediaManagement/app/Http/Livewire/Admin/UniversalMediaComponent.php
- Tüm modüllerde kullanılabilen evrensel medya yönetimi
- Spatie Media Library entegrasyonu
- Collection sistemi: featured_image, gallery, seo_og_image, videos, documents
- Tenant-aware disk sistemi (storage/tenant{id}/)
- Thumbmaker entegrasyonu (otomatik WebP thumbnail)

// Media Model
Modules/MediaManagement/app/Models/MediaLibraryItem.php
- Global media library host model
- Spatie InteractsWithMedia trait kullanıyor
- Meta data desteği (JSON field)
- Responsive image conversions
```

**Database Tabloları:**
- `media_library_items`: Media metadata tablosu
- `media`: Spatie Media Library tablosu (morphable)

**Disk Yapısı:**
```
storage/
├── tenant2/          # ixtif.com (Tenant ID: 2)
│   └── app/
│       └── public/
│           └── media/
└── public/           # Central domain
    └── media/
```

### 2. AI Kredi Sistemi

**Modül Konumu:** `Modules/AI/`

**Kredi Yönetim Servisi:**
```php
Modules/AI/app/Services/AICreditService.php

// Kredi kategorileri ve maliyetleri
const USAGE_CATEGORIES = [
    'basic_query' => 1.0,
    'advanced_analysis' => 2.5,
    'content_generation' => 3.0,
    'seo_analysis' => 2.0,
    'translation' => 1.5,
    'code_generation' => 4.0,
    'image_analysis' => 3.5,      // ✅ Mevcut
    'bulk_operations' => 5.0,
];

// ⚠️ Önerimiz: Yeni kategori ekle
'image_generation' => 4.5,  // AI görsel üretimi için

// Kredi kullanım metodu
$creditService->useCredits(
    $user,                  // User instance
    'image_generation',     // Kategori
    1.0,                    // Base cost (1 görsel = 1 base cost)
    'openai',              // Provider
    'image_generation',     // Feature
    [                      // Metadata
        'model' => 'dall-e-3',
        'prompt' => 'User prompt',
        'resolution' => '1024x1024'
    ]
);
```

**Database Tabloları:**
```sql
-- Kredi satın alma kayıtları (Central DB)
ai_credit_purchases
├── tenant_id
├── user_id
├── credit_amount
├── price_paid
├── status (completed, pending, failed)
└── purchased_at

-- Kredi kullanım kayıtları (Central DB)
ai_credit_usage
├── tenant_id
├── user_id
├── provider_name (openai, anthropic, etc.)
├── model (gpt-4, dall-e-3, etc.)
├── credits_used (decimal)
├── feature_slug (hangi AI feature)
├── metadata (JSON - prompt, parametreler vs.)
└── used_at
```

**Mevcut Kredi Akışı:**
1. Kullanıcı AI işlemi yapar
2. `AICreditService->useCredits()` çağrılır
3. Kredi kontrolü yapılır (yeterli mi?)
4. Kredi düşülür ve `ai_credit_usage` tablosuna kayıt atılır
5. Cache temizlenir

---

## 🎨 AI GÖRSEL ÜRETİM SERVİSLERİ KARŞILAŞTIRMASI

### 1. OpenAI DALL-E 3 ⭐⭐⭐⭐⭐ (ÖNERİLEN)

**장점:**
- ✅ **Resmi API**: `POST https://api.openai.com/v1/images/generations`
- ✅ **Yüksek Kalite**: En iyi prompt takibi ve tutarlılık
- ✅ **Kolay Entegrasyon**: RESTful API, JSON response
- ✅ **Hızlı**: ~30-60 saniye/görsel
- ✅ **Çeşitli Boyutlar**: 1024x1024, 1792x1024, 1024x1792
- ✅ **Güvenlik**: Content policy filtering (zararlı içerik engelleme)

**Fiyatlandırma (2024):**
- Standard (1024x1024): **$0.040/görsel** (~1.2 TRY)
- HD (1024x1792): **$0.080/görsel** (~2.4 TRY)

**API Örnek:**
```php
use OpenAI\Laravel\Facades\OpenAI;

$response = OpenAI::images()->create([
    'model' => 'dall-e-3',
    'prompt' => 'A modern forklift in a warehouse, professional product photo',
    'n' => 1,
    'size' => '1024x1024',
    'quality' => 'standard', // veya 'hd'
    'response_format' => 'url', // veya 'b64_json'
]);

$imageUrl = $response->data[0]->url;
```

**Kredi Maliyeti Önerisi:**
- Standard quality: **5 kredi** (1024x1024)
- HD quality: **10 kredi** (1792x1024, 1024x1792)

---

### 2. Stability AI (Stable Diffusion) ⭐⭐⭐⭐

**장점:**
- ✅ **Açık Kaynak**: Self-hosting mümkün
- ✅ **Uygun Fiyat**: OpenAI'dan %40-60 daha ucuz
- ✅ **Özelleştirilebilir**: Fine-tuning, LoRA models
- ✅ **Çeşitli Modeller**: SD 1.5, SDXL 1.0, SDXL Turbo
- ✅ **API Sağlayıcıları**: stability.ai, replicate.com, huggingface.co

**Fiyatlandırma (Stability AI API):**
- SDXL 1.0: **$0.020/görsel** (~0.6 TRY)
- SD 1.5: **$0.002/görsel** (~0.06 TRY)

**API Örnek (Stability AI):**
```php
use Stability\Client;

$client = new Client(env('STABILITY_API_KEY'));

$response = $client->generate([
    'text_prompts' => [
        ['text' => 'Professional forklift photo', 'weight' => 1],
    ],
    'cfg_scale' => 7,
    'height' => 1024,
    'width' => 1024,
    'samples' => 1,
    'steps' => 30,
]);

$imageBase64 = $response['artifacts'][0]['base64'];
```

**Kredi Maliyeti Önerisi:**
- SDXL 1.0: **3 kredi**
- SD 1.5: **1 kredi**

---

### 3. Midjourney ⭐⭐⭐ (Sınırlı API)

**장점:**
- ✅ **En Yüksek Kalite**: Sanatsal ve estetik sonuçlar
- ✅ **Stil Çeşitliliği**: Farklı sanat stilleri

**Dezavantajlar:**
- ❌ **Resmi API Yok**: Discord bot üzerinden çalışıyor
- ❌ **API Wrapper'lar Pahalı**: 3. parti wrapper'lar $0.10-0.20/görsel
- ❌ **Yavaş**: Discord queue sistemi nedeniyle 2-5 dakika

**Sonuç:** ❌ **ÖNERİLMEZ** (Resmi API olmadığı için)

---

## 🎯 ÖNERİ: OPENAI DALL-E 3

**Neden DALL-E 3?**
1. ✅ **Resmi API** - Güvenilir ve kararlı
2. ✅ **Yüksek Kalite** - Prompt'lara en iyi uyum
3. ✅ **Laravel Paketi** - `openai-php/laravel` zaten sisteminizde
4. ✅ **Content Safety** - Zararlı içerik otomatik filtreleniyor
5. ✅ **Hızlı** - 30-60 saniye ortalama
6. ✅ **E-ticaret Uyumlu** - Ürün görselleri için ideal

**Maliyet Analizi:**
- OpenAI API: $0.040/görsel = ~1.2 TRY
- Sistem kredisi: 5 kredi/görsel (Standard)
- Kredi paketi: 100 kredi = $5 (100 TRY) → 20 görsel = 5 TRY/görsel
- **Kar marjı**: ~%300 ✅

---

## 🏗️ UYGULAMA PLANI

### Faz 1: AI Görsel Üretim Servisi (2-3 gün)

**1.1. AIImageGenerationService Oluşturma**

**Dosya:** `Modules/AI/app/Services/AIImageGenerationService.php`

**Sorumluluklar:**
- DALL-E 3 API entegrasyonu
- Görsel URL'sini indirme ve kaydetme
- Kredi düşürme (`AICreditService` entegrasyonu)
- MediaManagement'a kaydetme
- Hata yönetimi ve retry mekanizması

**Metotlar:**
```php
class AIImageGenerationService
{
    /**
     * AI ile görsel üret ve MediaManagement'a kaydet
     *
     * @param string $prompt Kullanıcı prompt'u
     * @param array $options Görsel seçenekleri
     * @return MediaLibraryItem Oluşturulan medya
     * @throws InsufficientCreditsException
     * @throws AIGenerationException
     */
    public function generateImage(
        string $prompt,
        array $options = [
            'size' => '1024x1024',     // 1024x1024, 1792x1024, 1024x1792
            'quality' => 'standard',    // standard, hd
            'model' => 'dall-e-3',
            'user_id' => null,          // Kullanıcı ID (kredi düşürme)
            'collection' => 'ai_generated', // Media collection
            'metadata' => [],           // Ek metadata
        ]
    ): MediaLibraryItem;

    /**
     * Toplu görsel üretimi (queue ile)
     */
    public function generateBatch(array $prompts, array $options = []): array;

    /**
     * Kredi maliyetini hesapla
     */
    public function calculateCreditCost(array $options): float;
}
```

**Flow:**
```php
1. Kullanıcı kredi kontrolü
   ├─ getCurrentBalance($userId)
   └─ Yeterli değilse → throw InsufficientCreditsException

2. DALL-E 3 API çağrısı
   ├─ OpenAI::images()->create([...])
   └─ Response URL al

3. Görsel indirme
   ├─ URL'den dosya indir
   ├─ /tmp/ klasörüne geçici kaydet
   └─ Dosya tipini kontrol et (image/png, image/jpeg)

4. MediaLibraryItem oluştur
   ├─ MediaLibraryItem::create([...])
   ├─ $item->addMedia($tempPath)->toMediaCollection('ai_generated')
   └─ Spatie Media Library otomatik thumbnail oluşturur

5. Kredi düşürme
   ├─ $creditService->useCredits($user, 'image_generation', ...)
   └─ Metadata: prompt, model, size, quality

6. Cleanup
   ├─ Geçici dosyayı sil
   └─ Cache temizle

7. Return MediaLibraryItem
```

---

**1.2. Migration: AI Generated Images**

**Dosya:** `Modules/AI/database/migrations/2025_11_14_150000_add_ai_image_generation_support.php`

```php
Schema::table('media_library_items', function (Blueprint $table) {
    $table->string('generation_source')->nullable()->after('meta');
    // 'user_upload', 'ai_generated', 'api_import'

    $table->text('generation_prompt')->nullable()->after('generation_source');
    // AI prompt'u sakla (SEO ve referans için)

    $table->json('generation_params')->nullable()->after('generation_prompt');
    // DALL-E parametreleri: model, size, quality, style
});

// Yeni kategori ekle: ai_credit_usage tablosuna
Schema::table('ai_credit_usage', function (Blueprint $table) {
    // Zaten JSON metadata field var, sadece dokümantasyon ekle
});
```

---

**1.3. Kredi Kategorisi Güncelleme**

**Dosya:** `Modules/AI/app/Services/AICreditService.php`

```php
// USAGE_CATEGORIES sabitine ekle:
private const USAGE_CATEGORIES = [
    // ... mevcut kategoriler
    'image_generation' => 4.5,  // DALL-E 3 Standard: 5 kredi
    'image_generation_hd' => 9.0, // DALL-E 3 HD: 10 kredi
];
```

---

### Faz 2: Admin Panel Entegrasyonu (1-2 gün)

**2.1. AI Image Generator Livewire Component**

**Dosya:** `Modules/AI/app/Http/Livewire/Admin/AIImageGeneratorComponent.php`

**Özellikler:**
- Prompt input field (textarea)
- Görsel boyutu seçimi (dropdown: 1024x1024, 1792x1024, 1024x1792)
- Kalite seçimi (standard, hd)
- Kredi bakiyesi gösterimi
- Önizleme ve indirme
- Media Library'ye kaydetme butonu

**Blade:** `Modules/AI/resources/views/admin/livewire/ai-image-generator.blade.php`

```blade
<div class="card">
    <div class="card-header">
        <h3>AI Görsel Üretici (DALL-E 3)</h3>
        <div class="badge bg-primary">Kredi: {{ $creditBalance }}</div>
    </div>

    <div class="card-body">
        <!-- Prompt Input -->
        <div class="mb-3">
            <label>Görsel Açıklaması (Prompt)</label>
            <textarea wire:model="prompt" class="form-control" rows="4"
                placeholder="Örn: A modern forklift in a warehouse..."></textarea>
        </div>

        <!-- Boyut Seçimi -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Boyut</label>
                <select wire:model="size" class="form-select">
                    <option value="1024x1024">Kare (1024x1024)</option>
                    <option value="1792x1024">Yatay (1792x1024)</option>
                    <option value="1024x1792">Dikey (1024x1792)</option>
                </select>
            </div>

            <div class="col-md-6">
                <label>Kalite</label>
                <select wire:model="quality" class="form-select">
                    <option value="standard">Standard (5 kredi)</option>
                    <option value="hd">HD (10 kredi)</option>
                </select>
            </div>
        </div>

        <!-- Generate Button -->
        <button wire:click="generate" class="btn btn-primary"
            wire:loading.attr="disabled">
            <span wire:loading.remove>Görsel Üret</span>
            <span wire:loading>Üretiliyor...</span>
        </button>

        <!-- Preview -->
        @if($generatedImage)
            <div class="mt-4">
                <img src="{{ $generatedImage->getUrl() }}" class="img-fluid">
                <button wire:click="saveToLibrary" class="btn btn-success mt-2">
                    Media Library'ye Kaydet
                </button>
            </div>
        @endif
    </div>
</div>
```

---

**2.2. Admin Route ve Navigation**

**Dosya:** `Modules/AI/routes/admin.php`

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/ai/image-generator', function () {
        return view('ai::admin.image-generator');
    })->name('admin.ai.image-generator');
});
```

**Navigation:** `Modules/AI/resources/views/admin/partials/navigation.blade.php`

```blade
<li class="nav-item">
    <a href="{{ route('admin.ai.image-generator') }}" class="nav-link">
        <i class="ti ti-photo-ai"></i>
        <span>AI Görsel Üretici</span>
    </a>
</li>
```

---

### Faz 3: MediaManagement Entegrasyonu (1 gün)

**3.1. AI Generated Collection Ekleme**

**Config:** `Modules/MediaManagement/config/mediamanagement.php`

```php
'collections' => [
    'featured_image' => [...],
    'gallery' => [...],
    // Yeni collection
    'ai_generated' => [
        'disk' => 'tenant', // Tenant-aware
        'conversions' => ['thumb', 'medium', 'large'],
        'max_file_size' => 10240, // 10MB
        'accepted_mimes' => ['image/png', 'image/jpeg', 'image/webp'],
    ],
],
```

**3.2. UniversalMediaComponent Güncellemesi**

AI generated görseller için metadata desteği:
- Prompt bilgisi gösterimi
- Generation parametreleri
- Re-generate butonu (aynı prompt ile yeniden üretme)

---

### Faz 4: Queue ve Background Processing (1 gün)

**4.1. AIImageGenerationJob**

**Dosya:** `Modules/AI/app/Jobs/AIImageGenerationJob.php`

```php
class AIImageGenerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $prompt,
        public array $options,
        public int $userId
    ) {}

    public function handle(AIImageGenerationService $service)
    {
        try {
            $media = $service->generateImage($this->prompt, $this->options);

            // Notification gönder
            User::find($this->userId)->notify(
                new AIImageGeneratedNotification($media)
            );

        } catch (\Exception $e) {
            // Retry mekanizması
            $this->release(30); // 30 saniye sonra tekrar dene
        }
    }
}
```

**Kullanım:**
```php
// Toplu üretim
AIImageGenerationJob::dispatch($prompt, $options, auth()->id());
```

---

### Faz 5: Frontend API (Opsiyonel - 1 gün)

**5.1. Public API Endpoint**

**Route:** `routes/api.php`

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/ai/generate-image', [AIImageController::class, 'generate']);
    Route::get('/ai/credits', [AIImageController::class, 'getCredits']);
});
```

**Controller:** `Modules/AI/app/Http/Controllers/Api/AIImageController.php`

```php
class AIImageController extends Controller
{
    public function generate(Request $request, AIImageGenerationService $service)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'size' => 'in:1024x1024,1792x1024,1024x1792',
            'quality' => 'in:standard,hd',
        ]);

        try {
            $media = $service->generateImage(
                $request->prompt,
                [
                    'size' => $request->size ?? '1024x1024',
                    'quality' => $request->quality ?? 'standard',
                    'user_id' => auth()->id(),
                ]
            );

            return response()->json([
                'success' => true,
                'image_url' => $media->getUrl(),
                'thumbnail_url' => $media->getUrl('thumb'),
                'credits_used' => 5, // Hesaplanan maliyet
                'credits_remaining' => $service->getUserCredits(auth()->user()),
            ]);

        } catch (InsufficientCreditsException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Yetersiz kredi',
            ], 402);
        }
    }
}
```

---

## 💰 KREDİ YÖNETİMİ VE FİYATLANDIRMA

### Önerilen Kredi Fiyatlandırması

**Görsel Üretim Maliyetleri:**

| Kalite | Boyut | OpenAI API | Sistem Kredi | Kredi Değeri (TRY) | Kar Marjı |
|--------|-------|-----------|--------------|-------------------|-----------|
| Standard | 1024x1024 | $0.040 | 5 kredi | 5 TRY | %300 |
| Standard | 1792x1024 | $0.040 | 5 kredi | 5 TRY | %300 |
| HD | 1024x1024 | $0.080 | 10 kredi | 10 TRY | %300 |
| HD | 1792x1024 | $0.080 | 10 kredi | 10 TRY | %300 |

**Kredi Paketleri Önerisi:**

| Paket | Kredi | Görsel Sayısı | Fiyat (TRY) | Kredi/TRY |
|-------|-------|---------------|-------------|-----------|
| Başlangıç | 50 kredi | 10 görsel (Standard) | 50 TRY | 1 |
| Standart | 200 kredi | 40 görsel | 180 TRY | 1.11 (%10 bonus) |
| Premium | 500 kredi | 100 görsel | 400 TRY | 1.25 (%20 bonus) |
| Enterprise | 2000 kredi | 400 görsel | 1400 TRY | 1.43 (%30 bonus) |

---

## 🔒 GÜVENLİK VE SINIRLAMALAR

### 1. Rate Limiting

```php
// Middleware: ThrottleAIImageGeneration
// Limit: 10 görsel/saat (user bazında)
RateLimiter::for('ai-image-generation', function ($request) {
    return Limit::perHour(10)->by($request->user()->id);
});
```

### 2. Prompt Validation

```php
// Zararlı içerik engelleme (OpenAI zaten filtreliyor ama ekstra kontrol)
$bannedWords = ['violence', 'gore', 'explicit', ...];
$prompt = Str::lower($request->prompt);

foreach ($bannedWords as $word) {
    if (Str::contains($prompt, $word)) {
        throw new InvalidPromptException('Prompt uygunsuz içerik içeriyor');
    }
}
```

### 3. Kredi Fraud Prevention

```php
// Aynı prompt'u 1 dakika içinde tekrar üretmeyi engelle
$cacheKey = "ai_image_prompt_" . md5($prompt) . "_" . $userId;
if (Cache::has($cacheKey)) {
    throw new DuplicatePromptException('Aynı prompt 1 dakika içinde tekrar kullanılamaz');
}

Cache::put($cacheKey, true, 60); // 1 dakika cache
```

---

## 📊 ANALİTİK VE RAPORLAMA

### 1. AI Image Generation Analytics

**Dashboard Metrikleri:**
- Toplam üretilen görsel sayısı
- Kullanılan toplam kredi
- En çok kullanılan boyut/kalite
- Ortalama üretim süresi
- Başarı/hata oranı

**Database Query:**
```sql
-- Son 30 gün görsel üretim istatistikleri
SELECT
    DATE(used_at) as date,
    COUNT(*) as total_images,
    SUM(credits_used) as total_credits,
    JSON_EXTRACT(metadata, '$.size') as size,
    JSON_EXTRACT(metadata, '$.quality') as quality
FROM ai_credit_usage
WHERE feature_slug = 'image_generation'
    AND used_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(used_at), size, quality
ORDER BY date DESC;
```

---

## 🧪 TEST PLANI

### Unit Tests

```php
// tests/Feature/AI/AIImageGenerationTest.php

/** @test */
public function it_generates_image_with_valid_prompt()
{
    $user = User::factory()->create();
    $this->addCreditsToUser($user, 100); // Test için kredi ekle

    $service = app(AIImageGenerationService::class);
    $media = $service->generateImage('A beautiful sunset', [
        'user_id' => $user->id,
    ]);

    $this->assertInstanceOf(MediaLibraryItem::class, $media);
    $this->assertEquals('ai_generated', $media->type);
    $this->assertNotNull($media->generation_prompt);
}

/** @test */
public function it_throws_exception_when_insufficient_credits()
{
    $user = User::factory()->create(); // Kredi yok

    $service = app(AIImageGenerationService::class);

    $this->expectException(InsufficientCreditsException::class);
    $service->generateImage('Test prompt', ['user_id' => $user->id]);
}

/** @test */
public function it_deducts_correct_credit_amount()
{
    $user = User::factory()->create();
    $this->addCreditsToUser($user, 100);

    $initialBalance = $creditService->getUserCredits($user);

    $service->generateImage('Test', ['user_id' => $user->id, 'quality' => 'standard']);

    $newBalance = $creditService->getUserCredits($user);
    $this->assertEquals(5, $initialBalance - $newBalance);
}
```

---

## 📅 UYGULAMA ZAMANLAMA

**Toplam Süre: 6-8 iş günü**

| Faz | Görev | Süre | Öncelik |
|-----|-------|------|---------|
| 1 | AIImageGenerationService | 2-3 gün | Yüksek |
| 2 | Admin Panel Component | 1-2 gün | Yüksek |
| 3 | MediaManagement Entegrasyonu | 1 gün | Orta |
| 4 | Queue & Background Jobs | 1 gün | Orta |
| 5 | Frontend API (Opsiyonel) | 1 gün | Düşük |

**Milestone'lar:**
- ✅ **Gün 1-3**: Core service ve DALL-E 3 entegrasyonu
- ✅ **Gün 4-5**: Admin panel ve UI
- ✅ **Gün 6**: Testing ve deployment
- ✅ **Gün 7-8**: Dokümantasyon ve optimizasyon

---

## 🚀 SONRAKİ ADIMLAR

### Hemen Başlayabilecekleriniz:

1. ✅ **OpenAI API Key Kontrolü**
   ```bash
   # .env dosyasında OPENAI_API_KEY var mı kontrol et
   php artisan tinker
   >>> config('openai.api_key')
   ```

2. ✅ **Kredi Kategorisi Ekle**
   - `AICreditService.php` dosyasına `image_generation` kategorisini ekle

3. ✅ **Migration Çalıştır**
   - `media_library_items` tablosuna AI generation field'ları ekle

4. ✅ **AIImageGenerationService Oluştur**
   - Modules/AI/app/Services/AIImageGenerationService.php

### Gelecek Geliştirmeler:

1. **Toplu Görsel Üretimi**
   - CSV/Excel'den prompt listesi yükle
   - Batch processing ile 100+ görsel üret

2. **AI Image Variations**
   - Mevcut görselden varyasyon üret
   - Style transfer

3. **AI Editing**
   - Görsel üzerinde değişiklik (inpainting)
   - Arka plan değiştirme

4. **AI-Powered SEO**
   - Görsel için otomatik alt text üret
   - SEO-friendly filename üret

---

## 💡 EK ÖNERİLER

### 1. Stability AI Alternatif Entegrasyon

Eğer maliyet önemliyse, DALL-E 3 yanında **Stability AI** da eklenebilir:

```php
// config/ai-image.php
return [
    'providers' => [
        'openai' => [
            'class' => OpenAIProvider::class,
            'models' => ['dall-e-3'],
            'credit_cost' => 5,
        ],
        'stability' => [
            'class' => StabilityAIProvider::class,
            'models' => ['sdxl-1.0', 'sd-1.5'],
            'credit_cost' => 3, // Daha ucuz
        ],
    ],
    'default_provider' => 'openai',
];
```

### 2. Image Moderation

OpenAI'nin image moderation API'si ile üretilen görselleri otomatik kontrol et:

```php
$response = OpenAI::moderations()->create([
    'input' => $imageUrl,
]);

if ($response['results'][0]['flagged']) {
    // Görsel uygunsuz, silme işlemi yap
}
```

### 3. Usage Dashboard

Kullanıcılar için AI usage dashboard'u:
- Kullanılan kredi miktarı (grafik)
- Üretilen görsel sayısı
- Favori prompt'lar
- Kredi satın alma geçmişi

---

## 📞 KULLANICI SORULARI İÇİN HAZIR CEVAPLAR

**S: Hangi AI servisini öneriyorsun?**
**C:** OpenAI DALL-E 3. Resmi API, yüksek kalite, hızlı ve güvenilir. Maliyet de makul: Standard kalite 5 kredi (~5 TRY), HD kalite 10 kredi (~10 TRY).

**S: Gemini veya Midjourney kullanabilir miyiz?**
**C:**
- **Gemini**: Henüz görsel üretim API'si yok, sadece text generation var.
- **Midjourney**: Resmi API yok, Discord bot üzerinden çalışıyor. Wrapper'lar pahalı ve kararsız.

**S: Kredi sistemi nasıl çalışacak?**
**C:** Mevcut AI kredi sisteminiz var. Görsel üretimi için yeni kategori eklenecek: `image_generation` (5 kredi/Standard, 10 kredi/HD). Kullanıcı görseli üretince otomatik kredi düşülecek ve `ai_credit_usage` tablosuna kaydedilecek.

**S: Media tablosuna nasıl kaydedilecek?**
**C:** AIImageGenerationService, DALL-E 3'ten dönen URL'i indirip MediaLibraryItem oluşturacak. Spatie Media Library otomatik thumbnail ve conversions yapacak. Prompt bilgisi de `generation_prompt` field'ında saklanacak.

---

## ✅ SONUÇ

Bu plan ile:
- ✅ AI otomatik görsel üretimi (DALL-E 3)
- ✅ MediaManagement + media DB entegrasyonu
- ✅ Kredi düşürme sistemi (backend)
- ✅ Admin panel UI
- ✅ Queue ve background processing
- ✅ Güvenlik ve rate limiting
- ✅ Analytics ve raporlama

**Toplam 6-8 iş gününde** production-ready sistem kurulabilir.

Onayınızla hemen başlayalım! 🚀
