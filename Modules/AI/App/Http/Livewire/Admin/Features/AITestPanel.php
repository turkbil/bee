<?php

namespace Modules\AI\App\Http\Livewire\Admin\Features;

use Livewire\Component;
use App\Helpers\TokenHelper;
use Modules\AI\App\Services\AIService;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class AITestPanel extends Component
{
    public $selectedFeature = '';
    public $inputText = '';
    public $result = '';
    public $isProcessing = false;
    public $showResult = false;
    public $tokensUsed = 0;
    public $remainingTokens = 0;
    
    public $features = [
        'İçerik Oluşturma' => [
            'desc' => 'Blog yazısı, makale veya ürün açıklaması oluşturma',
            'example' => 'Organik bal üretimi hakkında bir blog yazısı',
            'icon' => 'ti-pencil'
        ],
        'Başlık Önerileri' => [
            'desc' => 'Bir konu için alternatif başlık önerileri',
            'example' => 'Sağlıklı Yaşam İpuçları',
            'icon' => 'ti-heading'
        ],
        'İçerik Özeti' => [
            'desc' => 'Uzun metinleri kısa ve anlaşılır hale getirme',
            'example' => 'E-ticaret, elektronik ortamda gerçekleştirilen ticari faaliyetlerin tümüdür...',
            'icon' => 'ti-file-text'
        ],
        'SSS Oluşturma' => [
            'desc' => 'İçerikten sıkça sorulan sorular üretme',
            'example' => 'Ücretsiz kargo hizmeti sunuyoruz. 150 TL ve üzeri alışverişlerde...',
            'icon' => 'ti-help-circle'
        ],
        'SEO Analizi' => [
            'desc' => 'İçeriğin arama motoru optimizasyonunu kontrol etme',
            'example' => 'En İyi Kahve Makineleri 2024 - Alım Rehberi',
            'icon' => 'ti-search'
        ],
        'İçerik Çevirisi' => [
            'desc' => 'Metinleri farklı dillere çevirme',
            'example' => 'Merhaba, bugün nasılsınız? Umarım iyi bir gün geçiriyorsunuzdur.',
            'icon' => 'ti-world'
        ],
        'İçerik İyileştirme' => [
            'desc' => 'Mevcut içeriği daha iyi hale getirme',
            'example' => 'Ürünümüz kalitelidir ve ucuzdur.',
            'icon' => 'ti-wand'
        ],
        'Sosyal Medya Metni' => [
            'desc' => 'Sosyal medya için kısa paylaşım metinleri',
            'example' => 'Yeni sezon ürünlerimiz mağazamızda! Modern tasarımlar...',
            'icon' => 'ti-share'
        ]
    ];
    
    protected $listeners = ['refreshTokens' => 'updateTokens'];
    
    public function mount()
    {
        $this->updateTokens();
    }
    
    public function updateTokens()
    {
        $this->remainingTokens = TokenHelper::remaining();
    }
    
    public function selectFeature($feature)
    {
        $this->selectedFeature = $feature;
        $this->inputText = $this->features[$feature]['example'] ?? '';
        $this->showResult = false;
        $this->result = '';
    }
    
    public function testAI()
    {
        $this->validate([
            'selectedFeature' => 'required',
            'inputText' => 'required|min:3|max:1000'
        ], [
            'selectedFeature.required' => 'Lütfen bir özellik seçin',
            'inputText.required' => 'Test metni yazın',
            'inputText.min' => 'En az 3 karakter yazın',
            'inputText.max' => 'En fazla 1000 karakter yazabilirsiniz'
        ]);
        
        if ($this->remainingTokens <= 0) {
            $this->addError('tokens', 'Token bakiyeniz yetersiz. Lütfen token satın alın.');
            return;
        }
        
        $this->isProcessing = true;
        $this->showResult = false;
        
        try {
            $tenant = tenant() ?? Tenant::first();
            $aiService = app(AIService::class);
            
            $prompt = $this->buildSimplePrompt($this->selectedFeature, $this->inputText);
            
            $response = $aiService->sendRequest([[
                'role' => 'user',
                'content' => $prompt
            ]], $tenant->id);
            
            if ($response['success']) {
                $this->result = $response['data']['content'] ?? 'Yanıt alınamadı';
                $this->tokensUsed = $response['tokens_used'] ?? 0;
                $this->showResult = true;
                $this->updateTokens();
                
                $this->dispatchBrowserEvent('ai-test-success', [
                    'message' => 'Test başarıyla tamamlandı!',
                    'tokens' => $this->tokensUsed
                ]);
            } else {
                $this->addError('ai', $response['error'] ?? 'AI servisi yanıt vermedi');
            }
            
        } catch (\Exception $e) {
            Log::error('AI Test Panel Error: ' . $e->getMessage());
            $this->addError('ai', 'Bir hata oluştu. Lütfen tekrar deneyin.');
        } finally {
            $this->isProcessing = false;
        }
    }
    
    private function buildSimplePrompt($feature, $input)
    {
        $prompts = [
            'İçerik Oluşturma' => "'{$input}' konusunda kısa bir yazı oluştur. Maksimum 3 paragraf olsun.",
            'Başlık Önerileri' => "'{$input}' için 5 yaratıcı başlık öner. Her biri farklı olsun.",
            'İçerik Özeti' => "Şu metni 2-3 cümlede özetle: {$input}",
            'SSS Oluşturma' => "Şu bilgiden 3 soru-cevap oluştur: {$input}",
            'SEO Analizi' => "'{$input}' başlığını SEO açısından değerlendir. Kısa öneriler ver.",
            'İçerik Çevirisi' => "Şu metni İngilizce'ye çevir: {$input}",
            'İçerik İyileştirme' => "Şu metni daha profesyonel yap: {$input}",
            'Sosyal Medya Metni' => "Şu konuda Instagram postu yaz (emoji kullan): {$input}"
        ];
        
        return $prompts[$feature] ?? "'{$input}' hakkında kısa bilgi ver.";
    }
    
    public function clearAll()
    {
        $this->selectedFeature = '';
        $this->inputText = '';
        $this->result = '';
        $this->showResult = false;
        $this->tokensUsed = 0;
        $this->resetErrorBag();
    }
    
    public function render()
    {
        return view('ai::admin.features.ai-test-panel');
    }
}