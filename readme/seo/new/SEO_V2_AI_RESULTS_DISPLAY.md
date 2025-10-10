# 🤖 SEO TAB - AI SONUÇLARI GÖRÜNTÜLEME SİSTEMİ
*Statik/Dinamik AI Veri Yönetimi*

## 🎯 KULLANICI TALEBİ - KRİTİK ÖZELLIK

### TEMEL İHTİYAÇ
SEO Tab'ında **2 adet AI alanı** var:
1. **Veri Analizi** (SEO Analysis)
2. **SEO Önerileri** (AI Recommendations)

### DAVRANŞ KURALLARI

#### ✅ SAYFA İLK YÜKLENMESİ
```
EĞER veritabanında AI sonuçları VARSA:
→ STATİK olarak göster (buton altında)
→ Accordion başlıkları görünür
→ İçerikler hazır şekilde yüklü

EĞER veritabanında AI sonuçları YOKSA:
→ Sadece butonlar göster
→ Aşağıda hiçbir alan YOK
→ Başlık bile yok
```

#### ✅ BUTONA TIKLANDIĞINDA
```
1. Mevcut statik sonuçlar KALKACAK (varsa)
2. Loading/Progress göster (buton altında inline)
3. AI'dan yeni sonuç gelince:
   → Loading kaybolacak
   → Yeni dinamik sonuç AYNI YERDE gösterilecek
   → Livewire ile real-time update
```

#### ✅ YER VE PATTERN KURALLARI
```
PATTERN SABİT:
- Buton pozisyonu
- Results container pozisyonu
- Accordion yapısı
- CSS class'ları

DEĞİŞEN:
- İçerik verileri
- Statik ↔ Dinamik state
```

---

## 🏗️ TEKNİK UYGULAMA PLANI

### 1. VERİTABANI YAPISI

#### Yeni Kolon Ekleme
```sql
-- pages tablosuna AI sonuçları için kolonlar
ALTER TABLE pages ADD COLUMN ai_seo_analysis JSON DEFAULT NULL;
ALTER TABLE pages ADD COLUMN ai_seo_recommendations JSON DEFAULT NULL;
ALTER TABLE pages ADD COLUMN ai_analysis_generated_at TIMESTAMP NULL;
ALTER TABLE pages ADD COLUMN ai_recommendations_generated_at TIMESTAMP NULL;
```

#### JSON Veri Yapısı
```json
// ai_seo_analysis
{
  "tr": {
    "overall_score": 85,
    "health_status": "🟢 İyi",
    "strengths": [
      "Başlık uzunluğu optimal",
      "Meta description mevcut"
    ],
    "improvements": [
      "Focus keyword eksik",
      "İçerik çok kısa"
    ],
    "action_items": [
      {
        "priority": "high",
        "title": "Focus keyword ekle",
        "description": "Sayfa başlığında ana anahtar kelime belirtin"
      }
    ],
    "generated_at": "2025-09-25T15:44:27Z"
  }
}

// ai_seo_recommendations
{
  "tr": {
    "recommendations": [
      {
        "type": "title",
        "alternatives": [
          {"id": 1, "value": "İletişim - Profesyonel Destek", "score": 95},
          {"id": 2, "value": "İletişim Bilgileri | Firma Adı", "score": 90}
        ]
      },
      {
        "type": "description",
        "alternatives": [
          {"id": 1, "value": "Profesyonel destek için bizimle iletişime geçin", "score": 92}
        ]
      }
    ],
    "generated_at": "2025-09-25T15:49:59Z"
  }
}
```

### 2. LIVEWIRE COMPONENT GÜNCELLEMESİ

#### PageManageComponent.php - Yeni Properties
```php
class PageManageComponent extends Component
{
    // Mevcut properties...

    // YENİ: AI Results için
    public $staticAiAnalysis = [];
    public $staticAiRecommendations = [];
    public $dynamicAiAnalysis = [];
    public $dynamicAiRecommendations = [];
    public $showAnalysisLoader = false;
    public $showRecommendationsLoader = false;

    protected function getListeners()
    {
        return [
            'aiAnalysisCompleted' => 'handleAiAnalysisCompleted',
            'aiRecommendationsCompleted' => 'handleAiRecommendationsCompleted',
        ];
    }

    public function mount($pageId)
    {
        // Mevcut mount logic...

        // Statik AI sonuçlarını yükle
        $this->loadStaticAiResults();
    }

    private function loadStaticAiResults()
    {
        if ($this->currentPage) {
            // Analysis yükle
            if ($this->currentPage->ai_seo_analysis) {
                $analysisData = json_decode($this->currentPage->ai_seo_analysis, true);
                $this->staticAiAnalysis = $analysisData[$this->currentLanguage] ?? [];
            }

            // Recommendations yükle
            if ($this->currentPage->ai_seo_recommendations) {
                $recommendationsData = json_decode($this->currentPage->ai_seo_recommendations, true);
                $this->staticAiRecommendations = $recommendationsData[$this->currentLanguage] ?? [];
            }
        }
    }

    public function handleAiAnalysisCompleted($analysisData)
    {
        // Statik veriyi temizle, dinamik veriyi set et
        $this->staticAiAnalysis = [];
        $this->dynamicAiAnalysis = $analysisData;
        $this->showAnalysisLoader = false;

        // Veritabanına kaydet (opsiyonel - kullanıcı karar verecek)
        $this->saveAiAnalysisToDatabase($analysisData);
    }

    public function handleAiRecommendationsCompleted($recommendationsData)
    {
        // Statik veriyi temizle, dinamik veriyi set et
        $this->staticAiRecommendations = [];
        $this->dynamicAiRecommendations = $recommendationsData;
        $this->showRecommendationsLoader = false;

        // Veritabanına kaydet (opsiyonel)
        $this->saveAiRecommendationsToDatabase($recommendationsData);
    }

    private function saveAiAnalysisToDatabase($data)
    {
        $existingData = json_decode($this->currentPage->ai_seo_analysis ?? '{}', true);
        $existingData[$this->currentLanguage] = $data;

        $this->currentPage->update([
            'ai_seo_analysis' => json_encode($existingData),
            'ai_analysis_generated_at' => now()
        ]);
    }

    private function saveAiRecommendationsToDatabase($data)
    {
        $existingData = json_decode($this->currentPage->ai_seo_recommendations ?? '{}', true);
        $existingData[$this->currentLanguage] = $data;

        $this->currentPage->update([
            'ai_seo_recommendations' => json_encode($existingData),
            'ai_recommendations_generated_at' => now()
        ]);
    }
}
```

### 3. BLADE TEMPLATE GÜNCELLEMESİ

#### universal-seo-tab.blade.php - AI Results Section
```blade
<div class="seo-tab-container">
    <!-- Mevcut butonlar -->
    <div class="ai-buttons-section mb-4">
        <button type="button" class="btn btn-primary"
                onclick="startSeoAnalysis({{ $pageId }}, '{{ $currentLanguage }}')">
            <i class="ti ti-search"></i> SEO Analizi
        </button>

        <button type="button" class="btn btn-success"
                onclick="getAiRecommendations({{ $pageId }}, '{{ $currentLanguage }}')">
            <i class="ti ti-bulb"></i> AI Önerileri Al
        </button>
    </div>

    <!-- AI RESULTS CONTAINER - Koşullu Görüntüleme -->
    @if($staticAiAnalysis || $dynamicAiAnalysis || $staticAiRecommendations || $dynamicAiRecommendations || $showAnalysisLoader || $showRecommendationsLoader)
    <div class="ai-results-container">

        <!-- SEO ANALİZİ SONUÇLARI -->
        @if($staticAiAnalysis || $dynamicAiAnalysis || $showAnalysisLoader)
        <div class="accordion-item mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#seo-analysis-results">
                    <i class="ti ti-chart-line me-2"></i>
                    SEO Analizi Sonuçları
                    @if($staticAiAnalysis && !$dynamicAiAnalysis)
                        <span class="badge bg-secondary ms-2">Kaydedilmiş</span>
                    @elseif($dynamicAiAnalysis)
                        <span class="badge bg-primary ms-2">Güncel</span>
                    @endif
                </button>
            </h2>
            <div id="seo-analysis-results" class="accordion-collapse collapse show">
                <div class="accordion-body" wire:loading.remove wire:target="handleAiAnalysisCompleted">

                    @if($showAnalysisLoader)
                        <!-- LOADING STATE -->
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Analiz ediliyor...</span>
                            </div>
                            <p class="mt-2">SEO analizi yapılıyor...</p>
                        </div>
                    @else
                        <!-- ACTUAL RESULTS -->
                        @php
                            $analysis = $dynamicAiAnalysis ?: $staticAiAnalysis;
                        @endphp

                        @if($analysis)
                            <!-- Overall Score -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h3 class="card-title">{{ $analysis['overall_score'] ?? 0 }}</h3>
                                            <p class="text-muted">Genel SEO Skoru</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="alert alert-info">
                                        <strong>Durum:</strong> {{ $analysis['health_status'] ?? 'Bilinmiyor' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Strengths -->
                            @if(isset($analysis['strengths']) && count($analysis['strengths']) > 0)
                            <div class="mb-4">
                                <h5 class="text-success"><i class="ti ti-check"></i> Güçlü Yönler</h5>
                                <ul class="list-group">
                                    @foreach($analysis['strengths'] as $strength)
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="ti ti-check text-success me-2"></i>
                                        {{ $strength }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <!-- Improvements -->
                            @if(isset($analysis['improvements']) && count($analysis['improvements']) > 0)
                            <div class="mb-4">
                                <h5 class="text-warning"><i class="ti ti-alert-triangle"></i> İyileştirme Alanları</h5>
                                <ul class="list-group">
                                    @foreach($analysis['improvements'] as $improvement)
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="ti ti-alert-triangle text-warning me-2"></i>
                                        {{ $improvement }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <!-- Action Items -->
                            @if(isset($analysis['action_items']) && count($analysis['action_items']) > 0)
                            <div class="mb-4">
                                <h5 class="text-danger"><i class="ti ti-list-check"></i> Yapılacaklar</h5>
                                @foreach($analysis['action_items'] as $item)
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="card-title">{{ $item['title'] ?? '' }}</h6>
                                                <p class="card-text">{{ $item['description'] ?? '' }}</p>
                                            </div>
                                            <span class="badge bg-{{ $item['priority'] === 'high' ? 'danger' : ($item['priority'] === 'medium' ? 'warning' : 'info') }}">
                                                {{ ucfirst($item['priority'] ?? 'normal') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- AI ÖNERİLERİ SONUÇLARI -->
        @if($staticAiRecommendations || $dynamicAiRecommendations || $showRecommendationsLoader)
        <div class="accordion-item mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ai-recommendations-results">
                    <i class="ti ti-bulb me-2"></i>
                    AI SEO Önerileri
                    @if($staticAiRecommendations && !$dynamicAiRecommendations)
                        <span class="badge bg-secondary ms-2">Kaydedilmiş</span>
                    @elseif($dynamicAiRecommendations)
                        <span class="badge bg-success ms-2">Güncel</span>
                    @endif
                </button>
            </h2>
            <div id="ai-recommendations-results" class="accordion-collapse collapse">
                <div class="accordion-body" wire:loading.remove wire:target="handleAiRecommendationsCompleted">

                    @if($showRecommendationsLoader)
                        <!-- LOADING STATE -->
                        <div class="text-center p-4">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Öneriler üretiliyor...</span>
                            </div>
                            <p class="mt-2">AI öneriler hazırlanıyor...</p>
                        </div>
                    @else
                        <!-- ACTUAL RECOMMENDATIONS -->
                        @php
                            $recommendations = $dynamicAiRecommendations ?: $staticAiRecommendations;
                        @endphp

                        @if(isset($recommendations['recommendations']))
                            @foreach($recommendations['recommendations'] as $recommendation)
                            <div class="mb-4">
                                <h5 class="text-primary">
                                    <i class="ti ti-{{ $recommendation['type'] === 'title' ? 'heading' : ($recommendation['type'] === 'description' ? 'file-description' : 'tag') }}"></i>
                                    {{ ucfirst(str_replace('_', ' ', $recommendation['type'])) }} Önerileri
                                </h5>

                                @if(isset($recommendation['alternatives']))
                                <div class="list-group">
                                    @foreach($recommendation['alternatives'] as $alternative)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <span>{{ $alternative['value'] ?? '' }}</span>
                                            <small class="text-muted d-block">{{ strlen($alternative['value'] ?? '') }} karakter</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">{{ $alternative['score'] ?? 0 }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
    @endif

    <!-- Mevcut SEO form alanları... -->
</div>
```

### 4. JAVASCRIPT GÜNCELLEMESİ

#### universal-seo-tab.js - Livewire Integration
```javascript
// Mevcut window.SeoTab object'i güncelle

window.SeoTab.startSeoAnalysis = function(pageId, language) {
    // Loading state'i aktif et
    Livewire.emit('setAnalysisLoader', true);

    fetch('/admin/ai/seo/analyze', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            module: 'page',
            model_id: pageId,
            language: language,
            feature_slug: 'page-seo-analysis',
            form_content: {
                page_id: pageId,
                language: language,
                action: 'analyze'
            }
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.results) {
            // Livewire'a sonucu gönder
            Livewire.emit('aiAnalysisCompleted', data.results);
        } else {
            console.error('Analysis failed:', data.error);
            Livewire.emit('setAnalysisLoader', false);
        }
    })
    .catch(error => {
        console.error('Analysis error:', error);
        Livewire.emit('setAnalysisLoader', false);
    });
};

window.SeoTab.getAiRecommendations = function(pageId, language) {
    // Loading state'i aktif et
    Livewire.emit('setRecommendationsLoader', true);

    fetch('/admin/ai/seo/recommendations', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            module: 'page',
            model_id: pageId,
            language: language,
            feature_slug: 'page-seo-recommendations',
            form_content: {
                page_id: pageId,
                language: language,
                action: 'recommendations'
            }
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.recommendations) {
            // Livewire'a sonucu gönder
            Livewire.emit('aiRecommendationsCompleted', {
                recommendations: data.recommendations,
                generated_at: new Date().toISOString()
            });
        } else {
            console.error('Recommendations failed:', data.error);
            Livewire.emit('setRecommendationsLoader', false);
        }
    })
    .catch(error => {
        console.error('Recommendations error:', error);
        Livewire.emit('setRecommendationsLoader', false);
    });
};
```

### 5. LIVEWIRE LISTENERS EKLEMESİ

#### PageManageComponent.php - Listener Methods
```php
public function setAnalysisLoader($loading)
{
    $this->showAnalysisLoader = $loading;
}

public function setRecommendationsLoader($loading)
{
    $this->showRecommendationsLoader = $loading;
}

// Dil değiştirme sırasında AI sonuçlarını yenile
public function updatedCurrentLanguage()
{
    parent::updatedCurrentLanguage(); // Mevcut logic

    // AI sonuçlarını yeni dil için yükle
    $this->loadStaticAiResults();

    // Dinamik sonuçları temizle (farklı dil için geçersiz)
    $this->dynamicAiAnalysis = [];
    $this->dynamicAiRecommendations = [];
}
```

---

## ✅ ÖZET - STATİK/DİNAMİK GÖRÜNTÜLEME SİSTEMİ

### TEMEL KURALLAR
1. **İlk yüklemede**: Veritabanındaki statik sonuçlar gösterilir
2. **Butona tıklanınca**: Statik → Dinamik geçiş, aynı yer
3. **Loading state**: Buton altında inline progress
4. **Real-time update**: Livewire ile anında güncelleme
5. **Pattern sabit**: Yer, yapı, accordion hiç değişmez

### TEKNİK STACK
- **Backend**: Livewire properties + Database JSON columns
- **Frontend**: Blade conditionals + Bootstrap accordions
- **State Management**: Static/Dynamic properties
- **Real-time**: Livewire events + JavaScript integration

Bu sistem sayesinde kullanıcı:
- Sayfayı yüklediğinde eski sonuçları görecek (varsa)
- Yeni analiz istediğinde eskiler kaybolup yenileri gelecek
- Loading durumunu görecek
- Sonuçlar aynı yerde, aynı formatta görünecek

**En önemli husus**: Static ve Dynamic asla aynı anda gösterilmeyecek, yer ve pattern hiç değişmeyecek!