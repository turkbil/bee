# 🌐 AI ÇEVİRİ MODAL SİSTEMİ - KOMPLE DOKÜMANTASYON

## 🎯 AMATÖR İÇİN BASIT ANLATIM

### AI Çeviri Nasıl Çalışır?

1. **Çeviri Modalını Açma:**
   - Admin panelde herhangi bir sayfada (Page, Portfolio, Announcement vs.)
   - İçerik listesinde her satırda "dil" ikonu görürsünüz 🌐
   - Bu ikona tıkladığınızda AI çeviri modalı açılır

2. **Tekli Çeviri (Tek İçerik):**
   - Bir içeriğin yanındaki dil ikonuna tıklayın
   - Modal açılır ve o içeriği çevirebilirsiniz
   - **Kaynak Dil**: Genellikle Türkçe (otomatik seçili)
   - **Hedef Diller**: Tüm diller otomatik işaretli gelir (kaynak dil hariç)
   - "Çeviriyi Başlat" butonuna tıklayın

3. **Toplu Çeviri (Birden Fazla İçerik):**
   - Liste sayfasında soldaki checkboxlardan birkaç içerik seçin
   - Üstte beliren "Toplu İşlemler" menüsünden "AI ile Çevir" seçin
   - Modal açılır ve seçtiğiniz tüm içerikler listelenir
   - Tek seferde hepsini seçtiğiniz dillere çevirir

4. **Çeviri Seçenekleri:**
   - **Çeviri Kalitesi**: 
     - Hızlı (düşük kalite, az kredi)
     - Dengeli (orta kalite, orta kredi)
     - Premium (yüksek kalite, fazla kredi) ✨
   - **Gelişmiş Seçenekler**:
     - ✅ Mevcut çevirilerin üzerine yaz
     - ✅ SEO alanlarını da çevir

5. **İşlem Süreci:**
   - "Çeviriyi Başlat" butonuna tıkladığınızda
   - Güzel bir animasyonlu loading ekranı çıkar 🤖
   - AI tüm içeriği profesyonelce çevirir
   - İşlem bitince modal otomatik kapanır
   - Sayfa yenilenir ve çeviriler görünür

### Ne Kadar Kredi Harcar?

- **Kısa içerik** (100-500 kelime): ~50-100 kredi
- **Orta içerik** (500-1500 kelime): ~200-400 kredi
- **Uzun içerik** (1500+ kelime): ~500-1000 kredi
- **SEO alanları dahil**: +%30 fazla kredi

### Hangi Alanlar Çevrilir?

- ✅ **Başlık** (title)
- ✅ **İçerik** (body/content)
- ✅ **Özet** (excerpt/summary)
- ✅ **SEO Başlığı** (seo_title) - SEO seçeneği açıksa
- ✅ **Meta Açıklama** (meta_description) - SEO seçeneği açıksa
- ✅ **Anahtar Kelimeler** (keywords) - SEO seçeneği açıksa

## 🔧 TEKNİK DOKÜMANTASYON

### Sistem Mimarisi

AI Çeviri Modal sistemi **modül-agnostik** bir yapıda tasarlanmıştır. Tek bir modal, tüm modüllerde çalışır.

### Dosya Yapısı

```
📁 AI Çeviri Sistemi
├── 📄 resources/views/admin/partials/global-ai-translation-modal.blade.php (Modal HTML)
├── 📄 public/assets/js/ai-translation-system.js (Frontend Logic)
├── 📄 Modules/AI/app/Http/Controllers/Admin/Translation/
│   ├── GlobalTranslationController.php (Ana Controller)
│   └── TranslationController.php (Legacy/Backup)
├── 📄 Modules/AI/app/Services/Translation/
│   ├── CentralizedTranslationService.php (Core Service)
│   └── TranslationEngine.php (AI Engine)
└── 📄 Modules/AI/routes/admin.php (Route tanımları)
```

### Frontend İşleyişi

#### 1. Modal Açılması:
```javascript
// Global fonksiyon olarak tanımlanmış
function openTranslationModal(module, itemId) {
    const translationSystem = window.aiTranslationSystem;
    if (translationSystem) {
        translationSystem.open(module, itemId, 'single');
    }
}

// Blade'de kullanım:
<a href="javascript:void(0);" 
   onclick="openTranslationModal('page', {{ $page->page_id }})">
    <i class="fas fa-language"></i>
</a>
```

#### 2. AITranslationSystem Class:
```javascript
class AITranslationSystem {
    constructor() {
        this.modal = null;
        this.currentModule = null;
        this.currentItemId = null;
        this.selectedItems = [];
        this.mode = 'single'; // 'single' veya 'bulk'
        this.config = {
            endpoints: {
                languages: '/admin/ai/translation/languages',
                estimate: '/admin/ai/translation/estimate-tokens',
                start: '/admin/ai/translation/start',
                progress: '/admin/ai/translation/progress'
            }
        };
    }
    
    // Modal açma
    async open(module, itemId, mode = 'single') {
        this.currentModule = module;
        this.currentItemId = itemId;
        this.mode = mode;
        
        // 1. Modalı aç
        this.modal.show();
        
        // 2. İçerik bilgilerini yükle
        await this.loadItemData();
        
        // 3. Token tahminini güncelle
        await this.updateTokenEstimation();
    }
    
    // Çeviri başlatma
    async startTranslation() {
        // 1. Loading overlay göster
        this.showLoadingOverlay();
        
        // 2. Backend'e istek gönder
        const response = await fetch(this.config.endpoints.start, {
            method: 'POST',
            body: JSON.stringify({
                module: this.currentModule,
                items: this.getItemsToTranslate(),
                source_language: this.getSourceLanguage(),
                target_languages: this.getTargetLanguages(),
                include_seo: this.includeSEO(),
                quality: this.getQuality()
            })
        });
        
        // 3. Sonucu işle
        if (response.ok) {
            this.handleSuccess();
        } else {
            this.handleError();
        }
    }
}
```

### Backend İşleyişi

#### 1. Route Tanımları:
```php
// Modules/AI/routes/admin.php
Route::prefix('ai/translation')->group(function () {
    Route::get('/languages', [GlobalTranslationController::class, 'getLanguages']);
    Route::post('/estimate-tokens', [GlobalTranslationController::class, 'estimateTokens']);
    Route::post('/start', [GlobalTranslationController::class, 'startTranslation']);
    Route::get('/progress/{operationId}', [GlobalTranslationController::class, 'getProgress']);
});
```

#### 2. GlobalTranslationController:
```php
class GlobalTranslationController extends Controller
{
    public function __construct(
        private readonly OpenAIService $aiService,
        private readonly AICreditService $creditService,
        private readonly CentralizedTranslationService $translationService
    ) {}
    
    public function startTranslation(Request $request): JsonResponse
    {
        // 1. Parametreleri al
        $items = $request->input('items', []);
        $targetLanguages = $request->input('target_languages', []);
        $sourceLanguage = $request->input('source_language', 'tr');
        $includeSeo = $request->input('include_seo', false);
        $module = $this->detectModule($request);
        
        // 2. Kredi kontrolü
        $currentBalance = $this->creditService->getCurrentBalance(auth()->id());
        if ($currentBalance <= 0) {
            return response()->json(['error' => 'Yetersiz kredi'], 400);
        }
        
        // 3. Çeviri işlemini başlat
        $results = $this->translationService->translateItems([
            'items' => $items,
            'source_language' => $sourceLanguage,
            'target_languages' => $targetLanguages,
            'module' => $module,
            'include_seo' => $includeSeo,
            'user_id' => auth()->id()
        ]);
        
        // 4. Sonuçları döndür
        return response()->json([
            'success' => true,
            'results' => $results,
            'message' => 'Çeviriler tamamlandı'
        ]);
    }
}
```

#### 3. CentralizedTranslationService:
```php
class CentralizedTranslationService
{
    public function translateItems(array $config): array
    {
        $results = [];
        $module = $config['module'];
        $items = $config['items'];
        
        foreach ($items as $itemId) {
            // 1. İçeriği veritabanından al
            $item = $this->getItemByModule($module, $itemId);
            
            // 2. Her hedef dil için çevir
            foreach ($config['target_languages'] as $targetLang) {
                // 3. AI'ya çeviri yaptır
                $translatedContent = $this->translateWithAI(
                    $item->getTranslated('title', $config['source_language']),
                    $item->getTranslated('body', $config['source_language']),
                    $targetLang,
                    $config['quality']
                );
                
                // 4. Veritabanına kaydet
                $this->saveTranslation($item, $translatedContent, $targetLang);
                
                // 5. Kredi düşür
                $this->deductCredits($translatedContent['tokens_used']);
                
                $results[] = [
                    'item_id' => $itemId,
                    'language' => $targetLang,
                    'success' => true
                ];
            }
        }
        
        return $results;
    }
    
    private function translateWithAI($title, $content, $targetLang, $quality): array
    {
        // AI prompt hazırla
        $prompt = $this->buildTranslationPrompt($title, $content, $targetLang, $quality);
        
        // OpenAI/DeepSeek'e gönder
        $response = $this->aiService->translate($prompt);
        
        // Parse et ve döndür
        return $this->parseAIResponse($response);
    }
}
```

### Kredi Sistemi

#### Kredi Hesaplama:
```php
// Token bazlı kredi hesaplama
$tokensUsed = $response['usage']['total_tokens'] ?? 0;
$creditsUsed = ceil($tokensUsed * 0.1); // Her 10 token = 1 kredi

// Tenant'tan kredi düş
$tenant = tenant();
$tenant->decrement('ai_credits', $creditsUsed);

// Kullanım kaydı oluştur
AIUsage::create([
    'tenant_id' => $tenant->id,
    'feature_id' => 301, // Translation feature ID
    'tokens_used' => $tokensUsed,
    'credits_used' => $creditsUsed,
    'usage_type' => 'translation',
    'metadata' => json_encode([
        'module' => $module,
        'items_count' => count($items),
        'languages_count' => count($targetLanguages),
        'include_seo' => $includeSeo
    ])
]);
```

### Veritabanı Tabloları

#### Kullanılan Tablolar:
1. **pages** - Sayfa içerikleri (JSON kolonlar: title, body, slug)
2. **portfolios** - Portfolio içerikleri
3. **announcements** - Duyuru içerikleri
4. **tenant_languages** - Mevcut diller
5. **ai_usage** - AI kullanım kayıtları
6. **ai_conversations** - Çeviri geçmişi
7. **tenants** - Kredi bilgileri (ai_credits, ai_credits_used)

### Modül Uyumluluğu

#### Desteklenen Modüller:
- ✅ **Page** - Tam destek
- ✅ **Portfolio** - Tam destek
- ✅ **Announcement** - Tam destek
- ✅ **Blog** - Tam destek
- ✅ **Product** - E-ticaret modülü için hazır
- ✅ **FAQ** - SSS modülü için hazır

#### Yeni Modül Ekleme:
```php
// 1. CentralizedTranslationService'e modül desteği ekle:
private function getItemByModule(string $module, int $itemId)
{
    return match($module) {
        'page' => Page::find($itemId),
        'portfolio' => Portfolio::find($itemId),
        'announcement' => Announcement::find($itemId),
        'yeni_modul' => YeniModul::find($itemId), // Yeni satır
        default => null
    };
}

// 2. Blade'de çeviri ikonunu ekle:
<a href="javascript:void(0);" 
   onclick="openTranslationModal('yeni_modul', {{ $item->id }})">
    <i class="fas fa-language"></i>
</a>
```

### Hata Yönetimi

```javascript
// Frontend hata yönetimi
try {
    const response = await fetch(url);
    if (!response.ok) {
        if (response.status === 402) {
            alert('Yetersiz kredi. Lütfen kredi satın alın.');
        } else {
            alert('Çeviri başarısız oldu.');
        }
    }
} catch (error) {
    console.error('Translation error:', error);
    alert('Bağlantı hatası oluştu.');
}
```

```php
// Backend hata yönetimi
try {
    $result = $this->translateWithAI($content);
} catch (InsufficientCreditsException $e) {
    Log::error('Insufficient credits', ['user' => auth()->id()]);
    throw new HttpException(402, 'Yetersiz kredi');
} catch (AIServiceException $e) {
    Log::error('AI service error', ['error' => $e->getMessage()]);
    throw new HttpException(500, 'AI servisi yanıt vermiyor');
}
```

## 🚀 PERFORMANS VE OPTİMİZASYON

### Cache Sistemi:
- Dil listesi 24 saat cache'lenir
- Token tahminleri 5 dakika cache'lenir
- Çeviri sonuçları kalıcı olarak saklanır

### Batch İşleme:
- Toplu çevirilerde her 5 item bir batch olarak işlenir
- Timeout: Tekli çeviri 60sn, Toplu çeviri 600sn
- Memory limit: 512MB (toplu işlemler için)

### Rate Limiting:
- Dakikada maksimum 10 çeviri isteği
- Saatte maksimum 100 çeviri isteği
- Günde maksimum 1000 çeviri isteği

## ✅ SONUÇ

AI Çeviri Modal Sistemi:
- **Tek modal** tüm modüllerde çalışır
- **Tekli ve toplu** çeviri destekler
- **SEO alanları** dahil edilebilir
- **Kredi sistemi** entegre
- **Detaylı loglama** ve hata yönetimi
- **Modern UI/UX** animasyonlu loading
- **Modül agnostik** - kolayca genişletilebilir
- **Performance optimized** - cache ve batch sistemi