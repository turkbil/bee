<?php

namespace Modules\AI\App\Http\Livewire\Admin\Features;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\AIResponseRepository;
use Modules\AI\App\Models\AIFeature;

#[Layout('admin.layout')]
class UniversalInputComponent extends Component
{
    public $featureId;
    public $feature;
    
    // Form Properties
    public $blogTopic = '';
    public $writingTone = '90021'; // Default: Profesyonel
    public $contentLength = 3; // Default: Normal
    public $targetAudience = '';
    public $useCompanyProfile = true;
    
    // UI State
    public $isLoading = false;
    public $result = '';
    public $showResult = false;
    public $resultMeta = '';
    
    // Options
    public $writingToneOptions = [];
    public $contentLengthOptions = [];
    public $hasCompanyProfile = false;

    public function mount($featureId)
    {
        $this->featureId = $featureId;
        $this->loadFeature();
        $this->loadOptions();
        $this->checkCompanyProfile();
    }

    private function loadFeature()
    {
        $this->feature = AIFeature::find($this->featureId);
        if (!$this->feature) {
            throw new \Exception("AI Feature bulunamadı: {$this->featureId}");
        }
    }

    private function loadOptions()
    {
        // Writing tone options - Universal prompts
        $this->writingToneOptions = Prompt::where('prompt_type', 'writing_tone')
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->select('prompt_id', 'name')
            ->get()
            ->toArray();

        // Content length options
        $this->contentLengthOptions = [
            1 => 'Çok Kısa',
            2 => 'Kısa',
            3 => 'Normal',
            4 => 'Uzun',
            5 => 'Çok Detaylı'
        ];
    }

    private function checkCompanyProfile()
    {
        try {
            // AI Tenant Profile tablosundan gerçek profil verilerini kontrol et
            $aiProfile = \Modules\AI\App\Models\AITenantProfile::currentOrCreate();
            
            // Minimum profil tamamlanma kontrolü - temel bilgiler ve sektör yeterli
            $hasBasicCompanyInfo = $aiProfile->company_info && 
                                   !empty($aiProfile->company_info['brand_name']);
                                   
            $hasSectorSelection = $aiProfile->sector_details && 
                                !empty($aiProfile->sector_details['sector_selection']);
            
            // Bu minimum bilgiler varsa profil kullanılabilir
            $this->hasCompanyProfile = $hasBasicCompanyInfo && $hasSectorSelection;
            
        } catch (\Exception $e) {
            \Log::warning('AI Profile kontrolü başarısız: ' . $e->getMessage());
            $this->hasCompanyProfile = false; // Güvenli fallback
        }
    }

    public function testFeature()
    {
        $this->validate([
            'blogTopic' => 'required|min:10|max:500',
            'writingTone' => 'required|numeric',
            'contentLength' => 'required|numeric|min:1|max:5',
            'targetAudience' => 'nullable|min:3|max:500',
        ], [
            'blogTopic.required' => 'Blog konusu zorunludur.',
            'blogTopic.min' => 'Blog konusu en az 10 karakter olmalıdır.',
            'blogTopic.max' => 'Blog konusu en fazla 500 karakter olmalıdır.',
            'targetAudience.min' => 'Hedef kitle en az 3 karakter olmalıdır.',
            'targetAudience.max' => 'Hedef kitle en fazla 500 karakter olmalıdır.',
        ]);

        $this->isLoading = true;
        $this->showResult = false;

        try {
            // Universal Input Data hazırla
            $universalInputs = [
                'main_input' => $this->blogTopic,
                'writing_tone' => $this->writingTone,
                'content_length' => $this->contentLength,
                'target_audience' => $this->targetAudience,
                'use_company_profile' => $this->useCompanyProfile,
            ];

            // Input text oluştur (display için)
            $writingToneLabel = collect($this->writingToneOptions)->firstWhere('prompt_id', $this->writingTone)['name'] ?? 'Profesyonel';
            $contentLengthLabel = $this->contentLengthOptions[$this->contentLength] ?? 'Normal';
            
            $inputText = "Blog Konusu: {$this->blogTopic}\n";
            $inputText .= "Yazım Tonu: {$writingToneLabel}\n";
            $inputText .= "İçerik Uzunluğu: {$contentLengthLabel}";
            
            if ($this->targetAudience) {
                $inputText .= "\nHedef Kitle: {$this->targetAudience}";
            }
            
            if ($this->useCompanyProfile) {
                $inputText .= "\nŞirket bilgilerini kullan: Evet";
            }

            // AI Service'i çağır
            $aiService = app(AIResponseRepository::class);
            $response = $aiService->executeRequest('prowess_test', [
                'feature_id' => $this->featureId,
                'input_text' => $inputText,
                'universal_inputs' => $universalInputs,
            ]);

            if ($response['success'] ?? false) {
                $rawResponse = $response['response'] ?? $response['ai_result'] ?? '';
                
                // Debug için raw response'u logla
                \Log::info('AI Raw Response:', [
                    'raw_response' => $rawResponse,
                    'feature_id' => $this->featureId
                ]);
                
                $this->result = $this->formatAIResponse($rawResponse);
                
                // Token usage meta bilgisi - Multiple fallback options
                $tokensUsed = $response['tokens_used'] ?? $response['total_tokens'] ?? $response['token_count'] ?? 0;
                $tokensUsedFormatted = $response['tokens_used_formatted'] ?? $response['token_info'] ?? '';
                
                if ($tokensUsedFormatted) {
                    $this->resultMeta = $tokensUsedFormatted;
                } else {
                    $this->resultMeta = $tokensUsed > 0 ? ($tokensUsed . ' token kullanıldı') : 'Token bilgisi mevcut değil';
                }
                
                $this->showResult = true;
                
                // Token balance güncellemesi
                if (isset($response['new_balance_formatted'])) {
                    $this->dispatch('token-updated', $response['new_balance_formatted']);
                }
                
                session()->flash('message', 'AI test başarıyla tamamlandı!');
            } else {
                $this->result = 'Hata: ' . ($response['message'] ?? 'Bilinmeyen hata oluştu');
                $this->showResult = true;
                session()->flash('error', $response['message'] ?? 'AI test başarısız oldu');
            }

        } catch (\Exception $e) {
            $this->result = 'Bağlantı hatası: ' . $e->getMessage();
            $this->showResult = true;
            session()->flash('error', 'Bağlantı hatası: ' . $e->getMessage());
            \Log::error('AI Test Error: ' . $e->getMessage(), [
                'feature_id' => $this->featureId,
                'user_id' => auth()->id(),
                'inputs' => $universalInputs,
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function clearResult()
    {
        $this->showResult = false;
        $this->result = '';
        $this->resultMeta = '';
    }

    public function resetForm()
    {
        $this->blogTopic = '';
        $this->writingTone = '90021';
        $this->contentLength = 3;
        $this->targetAudience = '';
        $this->useCompanyProfile = true;
        $this->clearResult();
    }

    private function formatAIResponse($aiResult): string
    {
        if (!$aiResult) return 'Sonuç bulunamadı';
        
        // HTML temizleme ve tekrarlı içeriği tespit etme
        $formatted = $aiResult;
        
        // Mevcut HTML etiketlerini temizle (eğer varsa)
        $formatted = strip_tags($formatted, '<h1><h2><h3><h4><h5><h6><p><br><strong><em><ul><ol><li>');
        
        // Aynı cümle/paragrafın tekrarını engelleme
        $sentences = preg_split('/(?<=[.!?])\s+/', $formatted);
        $uniqueSentences = [];
        
        foreach ($sentences as $sentence) {
            $cleanSentence = trim($sentence);
            if (!empty($cleanSentence) && !in_array($cleanSentence, $uniqueSentences)) {
                $uniqueSentences[] = $cleanSentence;
            }
        }
        
        $formatted = implode(' ', $uniqueSentences);
        
        // Blog-specific div'leri temizle (blog-intro, blog-content sınıfları)
        $formatted = preg_replace('/<div class="[^"]*blog-[^"]*"[^>]*>(.*?)<\/div>/s', '$1', $formatted);
        
        // Temel markdown temizleme
        $formatted = preg_replace('/^### (.*$)/m', '<h5 class="text-primary mb-2">$1</h5>', $formatted);
        $formatted = preg_replace('/^## (.*$)/m', '<h4 class="text-primary mb-2">$1</h4>', $formatted);
        $formatted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formatted);
        $formatted = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $formatted);
        
        // Bullet point'ler
        $formatted = preg_replace('/^[\s]*[-•*] (.+)$/m', '<div class="mb-1">• $1</div>', $formatted);
        
        // Çoklu boşlukları ve satır sonlarını temizle
        $formatted = preg_replace('/\n{3,}/', "\n\n", $formatted);
        $formatted = preg_replace('/\s{2,}/', ' ', $formatted);
        
        // Satır sonları
        $formatted = nl2br($formatted);
        
        return $formatted;
    }

    public function render()
    {
        return view('ai::admin.livewire.features.universal-input-component');
    }
}