<?php

namespace Modules\AI\app\Http\Livewire\Admin;

use Livewire\Component;
use Modules\AI\app\Models\AITenantProfile;
use Modules\AI\app\Models\AIProfileSector;
use Modules\AI\app\Models\AIProfileQuestion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class AIProfileManagement extends Component
{
    public $currentStep = 1;
    public $totalSteps = 6;
    public $formData = [];
    public $sectors = [];
    public $currentSectorCode = null;
    public $questions = [];
    public $profile;
    public $showFounderQuestions = false;
    
    // Validation mesajları için (custom messages)
    public $customErrors = [];
    
    protected $listeners = ['refreshComponent' => '$refresh', 'refreshData' => 'refreshData'];
    
    /**
     * jQuery auto-save sonrası profile data'sını yeniden yükle
     * Bu metod AJAX save işleminden sonra component'in güncel veriyi görmesini sağlar
     */
    public function refreshData()
    {
        \Log::info('AIProfileManagement - refreshData called', [
            'tenant_id' => tenant('id'),
            'current_profile_id' => $this->profile?->id
        ]);
        
        // Cache'i temizle
        Cache::forget('ai_tenant_profile_' . tenant('id'));
        
        // Profili yeniden yükle
        $this->profile = AITenantProfile::currentOrCreate();
        
        // Form verilerini yeniden yükle
        $this->loadProfileData();
        
        // Mevcut step sorularını yeniden yükle
        $this->loadQuestionsForStep();
        
        // Component'i refresh et
        $this->fill(['formData' => $this->formData]);
        
        \Log::info('AIProfileManagement - refreshData completed', [
            'formData_count' => count($this->formData),
            'profile_exists' => $this->profile?->exists,
            'current_step' => $this->currentStep
        ]);
    }
    
    public function hydrate()
    {
        // Her Livewire request'inde profili ve form verilerini yeniden yükle
        if (!$this->profile) {
            $this->profile = AITenantProfile::currentOrCreate();
        }
        
        // Form verileri boşsa yeniden yükle
        if ($this->profile && $this->profile->exists && count($this->formData) < 10) {
            \Log::info('AIProfileManagement - Hydrate: Reloading form data', [
                'current_formData_count' => count($this->formData),
                'profile_exists' => $this->profile->exists
            ]);
            $this->loadProfileData();
            $this->fill(['formData' => $this->formData]);
        }
    }
    
    public function mount($initialStep = 1)
    {
        // URL'den gelen step'i ayarla
        $this->currentStep = max(1, min(6, (int) $initialStep));
        
        // Mevcut profili yükle veya yeni oluştur
        $this->profile = AITenantProfile::currentOrCreate();
        
        // Profil verilerini forma yükle
        $this->loadProfileData();
        
        // Sektörleri yükle
        $this->sectors = AIProfileSector::getActive();
        
        // Mevcut adım sorularını yükle
        $this->loadQuestionsForStep();
        
        // Livewire component'ini manuel olarak güncelle
        $this->fill(['formData' => $this->formData]);
    }
    
    private function loadProfileData()
    {
        \Log::info('AIProfileManagement - loadProfileData called', [
            'profile_exists' => !!$this->profile,
            'profile_id' => $this->profile?->id ?? 'N/A'
        ]);
        
        if ($this->profile && $this->profile->exists) {
            \Log::info('AIProfileManagement - Loading existing profile data', [
                'profile_data' => [
                    'company_info' => $this->profile->company_info ?? null,
                    'sector_details' => $this->profile->sector_details ?? null,
                    'success_stories' => $this->profile->success_stories ?? null,
                    'ai_behavior_rules' => $this->profile->ai_behavior_rules ?? null,
                    'founder_info' => $this->profile->founder_info ?? null,
                ]
            ]);
            
            // Company info
            if ($this->profile->company_info && is_array($this->profile->company_info)) {
                foreach ($this->profile->company_info as $key => $value) {
                    // Checkbox alanları için özel mapping
                    if ($key === 'contact_info' && is_array($value)) {
                        foreach ($value as $contactKey => $contactValue) {
                            $this->formData["company_info.contact_info.{$contactKey}"] = $contactValue;
                            \Log::debug("Loaded company_info.contact_info.{$contactKey}", ['value' => $contactValue]);
                        }
                    } else {
                        $this->formData["company_info.{$key}"] = $value;
                        \Log::debug("Loaded company_info.{$key}", ['value' => $value]);
                    }
                }
            }
            
            // Sector details
            if ($this->profile->sector_details && is_array($this->profile->sector_details)) {
                if (isset($this->profile->sector_details['sector'])) {
                    $this->currentSectorCode = $this->profile->sector_details['sector'];
                    $this->formData['sector'] = $this->currentSectorCode;
                    \Log::debug('Loaded sector', ['sector' => $this->currentSectorCode]);
                }
                
                foreach ($this->profile->sector_details as $key => $value) {
                    $this->formData["sector_details.{$key}"] = $value;
                    \Log::debug("Loaded sector_details.{$key}", ['value' => $value]);
                }
            }
            
            // Success stories
            if ($this->profile->success_stories && is_array($this->profile->success_stories)) {
                foreach ($this->profile->success_stories as $key => $value) {
                    $this->formData["success_stories.{$key}"] = $value;
                    \Log::debug("Loaded success_stories.{$key}", ['value' => $value]);
                }
            }
            
            // AI behavior rules
            if ($this->profile->ai_behavior_rules && is_array($this->profile->ai_behavior_rules)) {
                foreach ($this->profile->ai_behavior_rules as $key => $value) {
                    // Nested checkbox alanları için özel mapping
                    if (is_array($value)) {
                        foreach ($value as $subKey => $subValue) {
                            $this->formData["ai_behavior_rules.{$key}.{$subKey}"] = $subValue;
                            \Log::debug("Loaded ai_behavior_rules.{$key}.{$subKey}", ['value' => $subValue]);
                        }
                    } else {
                        $this->formData["ai_behavior_rules.{$key}"] = $value;
                        \Log::debug("Loaded ai_behavior_rules.{$key}", ['value' => $value]);
                    }
                }
            }
            
            // Founder permission durumu kontrol et
            if (isset($this->formData['company_info.founder_permission'])) {
                $this->showFounderQuestions = in_array($this->formData['company_info.founder_permission'], ['yes_full', 'yes_limited']);
                \Log::debug('Founder questions visibility', [
                    'permission' => $this->formData['company_info.founder_permission'],
                    'show' => $this->showFounderQuestions
                ]);
            }
            
            // Founder info
            if ($this->profile->founder_info && is_array($this->profile->founder_info) && $this->showFounderQuestions) {
                foreach ($this->profile->founder_info as $key => $value) {
                    // Nested checkbox alanları için özel mapping
                    if (is_array($value)) {
                        foreach ($value as $subKey => $subValue) {
                            $this->formData["founder_info.{$key}.{$subKey}"] = $subValue;
                            \Log::debug("Loaded founder_info.{$key}.{$subKey}", ['value' => $subValue]);
                        }
                    } else {
                        $this->formData["founder_info.{$key}"] = $value;
                        \Log::debug("Loaded founder_info.{$key}", ['value' => $value]);
                    }
                }
            }
            
            \Log::info('AIProfileManagement - Profile data loaded successfully', [
                'formData_keys' => array_keys($this->formData),
                'currentSectorCode' => $this->currentSectorCode,
                'showFounderQuestions' => $this->showFounderQuestions
            ]);
        } else {
            \Log::info('AIProfileManagement - No existing profile data to load');
        }
        
    }
    
    private function loadQuestionsForStep()
    {
        \Log::info('AIProfileManagement - loadQuestionsForStep called', [
            'currentStep' => $this->currentStep,
            'currentSectorCode' => $this->currentSectorCode
        ]);
        
        $this->questions = [];
        
        switch ($this->currentStep) {
            case 1:
                // Sektör seçimi
                $this->questions = AIProfileQuestion::getByStep(1);
                break;
                
            case 2:
                // Temel bilgiler
                $this->questions = AIProfileQuestion::getByStep(2);
                break;
                
            case 3:
                // Marka detayları + sektöre özel sorular
                $this->questions = AIProfileQuestion::getByStep(3, $this->currentSectorCode);
                break;
                
            case 4:
                // Kurucu izin sorusu + kurucu bilgileri
                $this->questions = AIProfileQuestion::getByStep(4);
                break;
                
            case 5:
                // Başarı hikayeleri - MANUAL QUESTIONS (veritabanında yok)
                $this->questions = collect([
                    (object) [
                        'question_key' => 'major_projects',
                        'question_text' => 'Önemli Projeleriniz',
                        'input_type' => 'textarea',
                        'help_text' => 'Gerçekleştirdiğiniz büyük projeleri kısaca anlatın',
                        'is_required' => false,
                        'validation_rules' => null,
                        'section' => 'success_stories'
                    ],
                    (object) [
                        'question_key' => 'client_references',
                        'question_text' => 'Müşteri Referansları',
                        'input_type' => 'textarea',
                        'help_text' => 'Memnun müşterilerinizden örnekler',
                        'is_required' => false,
                        'validation_rules' => null,
                        'section' => 'success_stories'
                    ],
                    (object) [
                        'question_key' => 'success_metrics',
                        'question_text' => 'Başarı Metrikleri',
                        'input_type' => 'textarea',
                        'help_text' => 'Ölçülebilir başarı göstergeleriniz (sayısal veriler)',
                        'is_required' => false,
                        'validation_rules' => null,
                        'section' => 'success_stories'
                    ]
                ]);
                break;
                
            case 6:
                // AI davranış kuralları
                $this->questions = AIProfileQuestion::getByStep(6);
                break;
        }
        
        \Log::info('AIProfileManagement - questions loaded', [
            'currentStep' => $this->currentStep,
            'questionCount' => $this->questions->count(),
            'questions' => $this->questions->pluck('question_key')->toArray()
        ]);
    }
    
    public function updatedFormData($value, $key)
    {
        \Log::info('AIProfileManagement - formData updated', [
            'key' => $key,
            'value' => $value,
            'currentStep' => $this->currentStep
        ]);
        
        // founder_permission değiştiğinde kurucu sorularını göster/gizle
        if ($key === 'company_info.founder_permission') {
            $this->showFounderQuestions = in_array($value, ['yes_full', 'yes_limited']);
            
            if (!$this->showFounderQuestions) {
                // Kurucu bilgilerini temizle
                foreach ($this->formData as $k => $v) {
                    if (str_starts_with($k, 'founder_info.')) {
                        unset($this->formData[$k]);
                    }
                }
            }
        }
        
        // Sektör değiştiğinde
        if ($key === 'sector') {
            $this->currentSectorCode = $value;
            
            // Eski sektör verilerini temizle
            foreach ($this->formData as $k => $v) {
                if (str_starts_with($k, 'sector_details.') && $k !== 'sector_details.sector') {
                    unset($this->formData[$k]);
                }
            }
            
            $this->formData['sector_details.sector'] = $value;
        }
    }
    
    public function nextStep()
    {
        // Debug logging
        \Log::info('AIProfileManagement - nextStep called', [
            'currentStep' => $this->currentStep,
            'formData' => $this->formData,
            'totalSteps' => $this->totalSteps
        ]);
        
        // Mevcut adımı validate et
        if ($this->validateCurrentStep()) {
            \Log::info('AIProfileManagement - validation passed', [
                'currentStep' => $this->currentStep
            ]);
            
            // Verileri kaydet
            $this->saveStepData();
            
            if ($this->currentStep < $this->totalSteps) {
                $nextStep = $this->currentStep + 1;
                
                // URL routing ile step değiştir
                return redirect()->route('admin.ai.profile.edit', ['step' => $nextStep]);
            }
        } else {
            \Log::warning('AIProfileManagement - validation failed', [
                'currentStep' => $this->currentStep,
                'customErrors' => $this->customErrors
            ]);
        }
    }
    
    public function saveCurrentStep()
    {
        try {
            \Log::info('AIProfileManagement - saveCurrentStep called', [
                'currentStep' => $this->currentStep,
                'formData' => $this->formData
            ]);
            
            // Mevcut step'in verilerini profile'e kaydet
            $this->saveStepData();
            
            session()->flash('success', 'Adım ' . $this->currentStep . ' kaydedildi.');
            
            return true;
        } catch (\Exception $e) {
            \Log::error('AIProfileManagement - saveCurrentStep error', [
                'error' => $e->getMessage(),
                'currentStep' => $this->currentStep
            ]);
            
            session()->flash('error', 'Kayıt sırasında hata: ' . $e->getMessage());
            return false;
        }
    }
    
    public function saveAndNavigateNext()
    {
        // Mevcut step'i kaydet
        $saved = $this->saveCurrentStep();
        
        if ($saved && $this->currentStep < $this->totalSteps) {
            $nextStep = $this->currentStep + 1;
            return redirect()->route('admin.ai.profile.edit', ['step' => $nextStep]);
        }
        
        // Hata durumunda aynı step'te kal
        return redirect()->route('admin.ai.profile.edit', ['step' => $this->currentStep]);
    }
    
    public function saveAndNavigatePrevious()
    {
        // Mevcut step'i kaydet
        $this->saveCurrentStep();
        
        if ($this->currentStep > 1) {
            $prevStep = $this->currentStep - 1;
            return redirect()->route('admin.ai.profile.edit', ['step' => $prevStep]);
        }
        
        // Hata durumunda aynı step'te kal
        return redirect()->route('admin.ai.profile.edit', ['step' => $this->currentStep]);
    }
    
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $prevStep = $this->currentStep - 1;
            
            // URL routing ile step değiştir
            return redirect()->route('admin.ai.profile.edit', ['step' => $prevStep]);
        }
    }
    
    public function goToStep($step)
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            // URL routing ile step değiştir
            return redirect()->route('admin.ai.profile.edit', ['step' => $step]);
        }
    }
    
    public function resetProfile()
    {
        try {
            \Log::info('AIProfileManagement - resetProfile called', [
                'tenant_id' => tenant('id'),
                'profile_id' => $this->profile?->id
            ]);
            
            if ($this->profile && $this->profile->exists) {
                // Profil veritabanından tamamen sil
                $this->profile->forceDelete(); // Hard delete - ID'yi de siler
                
                \Log::info('AIProfileManagement - Profile force deleted successfully', [
                    'tenant_id' => tenant('id')
                ]);
            }
            
            // Cache'i temizle
            \Illuminate\Support\Facades\Cache::forget('ai_tenant_profile_' . tenant('id'));
            
            // Form verilerini tamamen temizle
            $this->formData = [];
            $this->currentSectorCode = null;
            $this->showFounderQuestions = false;
            $this->customErrors = [];
            
            // Profil referansını null yap
            $this->profile = null;
            
            // Component state'ini sıfırla
            $this->currentStep = 1;
            $this->loadQuestionsForStep();
            
            \Log::info('AIProfileManagement - Reset completed successfully');
            
            // Profile sayfasına yönlendir (sıfırlanmış hali)
            return redirect()->route('admin.ai.profile.show')
                           ->with('success', 'Yapay zeka profili tamamen sıfırlandı! Yeni profil oluşturabilirsiniz.');
            
        } catch (\Exception $e) {
            \Log::error('AIProfileManagement - Reset error', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id'),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Profil sıfırlanırken bir hata oluştu: ' . $e->getMessage());
        }
    }
    
    private function validateCurrentStep()
    {
        // 🔧 FRESH DATA RELOAD - Validation öncesi jQuery auto-save verilerini yükle
        // Profile'ı yeniden yükle (jQuery auto-save'ler için)
        $this->profile = $this->profile->fresh();
        
        // Form verilerini fresh profile'dan yeniden yükle
        $this->loadProfileData();
        
        $rules = [];
        $messages = [];
        $attributes = [];
        
        // FormData'yı normalize et validation için
        $normalizedData = $this->normalizeFormData($this->formData);
        
        \Log::info('AIProfileManagement - validateCurrentStep called', [
            'currentStep' => $this->currentStep,
            'originalFormData' => $this->formData,
            'normalizedData' => $normalizedData
        ]);
        
        foreach ($this->questions as $question) {
            $fieldKey = $this->getFieldKey($question);
            
            if ($question->validation_rules || $question->is_required) {
                $rules[$fieldKey] = $question->getLaravelValidationRules();
                $attributes[$fieldKey] = $question->question_text;
                
                \Log::info('AIProfileManagement - Adding validation rule', [
                    'fieldKey' => $fieldKey,
                    'rules' => $question->getLaravelValidationRules(),
                    'is_required' => $question->is_required,
                    'current_value' => data_get($normalizedData, $fieldKey)
                ]);
            }
        }
        
        // Kurucu bilgileri için özel validation
        if ($this->currentStep === 4 && $this->showFounderQuestions) {
            $founderQuestions = AIProfileQuestion::getOptionalSectionQuestions('founder_info', $this->currentSectorCode);
            foreach ($founderQuestions as $question) {
                $fieldKey = $this->getFieldKey($question);
                if ($question->validation_rules || $question->is_required) {
                    $rules[$fieldKey] = $question->getLaravelValidationRules();
                    $attributes[$fieldKey] = $question->question_text;
                }
            }
        }
        
        \Log::info('AIProfileManagement - Final validation setup', [
            'rules' => $rules,
            'attributes' => $attributes,
            'dataToValidate' => $normalizedData
        ]);
        
        // Normalize edilmiş data ile validate et
        $validator = Validator::make($normalizedData, $rules, $messages, $attributes);
        
        if ($validator->fails()) {
            $this->customErrors = $validator->errors()->toArray();
            \Log::warning('AIProfileManagement - Validation failed', [
                'errors' => $this->customErrors,
                'failed_rules' => $validator->failed()
            ]);
            $this->dispatch('validation-error', ['errors' => $this->customErrors]);
            return false;
        }
        
        \Log::info('AIProfileManagement - Validation passed successfully');
        $this->customErrors = [];
        return true;
    }
    
    private function getFieldKey($question)
    {
        // Adıma göre field prefix belirle
        $prefix = match($this->currentStep) {
            1 => '', // Sektör seçimi - prefix yok
            2 => 'company_info', // Temel bilgiler
            3 => 'sector_details', // Marka detayları / sektör bilgileri
            4 => 'company_info', // Kurucu izin + bilgileri
            5 => 'success_stories', // Başarı hikayeleri
            6 => 'ai_behavior_rules', // AI davranış
            default => ''
        };
        
        // Section varsa onu kullan
        if ($question->section) {
            $prefix = $question->section;
        }
        
        return $prefix ? "{$prefix}.{$question->question_key}" : $question->question_key;
    }
    
    private function saveStepData()
    {
        \Log::info('AIProfileManagement - saveStepData called', [
            'currentStep' => $this->currentStep,
            'formData' => $this->formData,
            'profile_id' => $this->profile?->id
        ]);
        
        // FormData'yı normalize et - dot notation'ları nested object'e çevir
        $normalizedData = $this->normalizeFormData($this->formData);
        
        \Log::info('AIProfileManagement - Normalized form data', [
            'original' => $this->formData,
            'normalized' => $normalizedData
        ]);
        
        switch ($this->currentStep) {
            case 1:
                // Sektör seçimi
                \Log::info('AIProfileManagement - Step 1: Saving sector', [
                    'sector_in_formData' => $normalizedData['sector'] ?? 'NOT_SET'
                ]);
                
                if (isset($normalizedData['sector'])) {
                    $this->profile->updateSection('sector_details', ['sector' => $normalizedData['sector']]);
                    \Log::info('AIProfileManagement - Sector saved successfully', [
                        'sector' => $normalizedData['sector']
                    ]);
                } else {
                    \Log::warning('AIProfileManagement - No sector found in formData');
                }
                break;
                
            case 2:
                // Temel bilgiler - Company info
                \Log::info('AIProfileManagement - Step 2: Saving company info');
                
                $sectionData = $normalizedData['company_info'] ?? [];
                
                \Log::info('AIProfileManagement - Step 2: Company data to save', [
                    'sectionData' => $sectionData
                ]);
                
                if (!empty($sectionData)) {
                    $this->profile->updateSection('company_info', $sectionData);
                    \Log::info('AIProfileManagement - Step 2: Company info saved successfully');
                } else {
                    \Log::warning('AIProfileManagement - Step 2: No company info data to save');
                }
                break;
                
            case 3:
                // Marka detayları - Sektöre özel bilgiler
                \Log::info('AIProfileManagement - Step 3: Saving sector details');
                
                $existingSector = $normalizedData['sector'] ?? null;
                $sectorDetailsData = $normalizedData['sector_details'] ?? [];
                
                // Merge with existing sector if exists
                if ($existingSector) {
                    $sectorDetailsData['sector'] = $existingSector;
                }
                
                \Log::info('AIProfileManagement - Step 3: Sector data to save', [
                    'sectionData' => $sectorDetailsData
                ]);
                
                if (!empty($sectorDetailsData)) {
                    $this->profile->updateSection('sector_details', $sectorDetailsData);
                    \Log::info('AIProfileManagement - Step 3: Sector details saved successfully');
                } else {
                    \Log::warning('AIProfileManagement - Step 3: No sector details data to save');
                }
                break;
                
            case 4:
                // Kurucu bilgileri
                \Log::info('AIProfileManagement - Step 4: Saving founder info');
                
                $companyData = $normalizedData['company_info'] ?? [];
                
                \Log::info('AIProfileManagement - Step 4: Company data to save', [
                    'companyData' => $companyData
                ]);
                
                if (!empty($companyData)) {
                    $this->profile->updateSection('company_info', $companyData);
                }
                
                // Kurucu bilgileri
                if ($this->showFounderQuestions) {
                    $founderData = $normalizedData['founder_info'] ?? [];
                    
                    \Log::info('AIProfileManagement - Step 4: Founder data to save', [
                        'founderData' => $founderData
                    ]);
                    
                    if (!empty($founderData)) {
                        $this->profile->updateSection('founder_info', $founderData);
                    }
                }
                
                \Log::info('AIProfileManagement - Step 4: Founder info saved successfully');
                break;
                
            case 5:
                // Başarı hikayeleri
                \Log::info('AIProfileManagement - Step 5: Saving success stories');
                
                $sectionData = $normalizedData['success_stories'] ?? [];
                
                \Log::info('AIProfileManagement - Step 5: Success data to save', [
                    'sectionData' => $sectionData
                ]);
                
                if (!empty($sectionData)) {
                    $this->profile->updateSection('success_stories', $sectionData);
                    \Log::info('AIProfileManagement - Step 5: Success stories saved successfully');
                } else {
                    \Log::warning('AIProfileManagement - Step 5: No success stories data to save');
                }
                break;
                
            case 6:
                // AI davranış kuralları
                \Log::info('AIProfileManagement - Step 6: Saving AI behavior rules');
                
                $sectionData = $normalizedData['ai_behavior_rules'] ?? [];
                
                \Log::info('AIProfileManagement - Step 6: AI behavior data to save', [
                    'sectionData' => $sectionData
                ]);
                
                if (!empty($sectionData)) {
                    $this->profile->updateSection('ai_behavior_rules', $sectionData);
                    \Log::info('AIProfileManagement - Step 6: AI behavior rules saved successfully');
                } else {
                    \Log::warning('AIProfileManagement - Step 6: No AI behavior rules data to save');
                }
                break;
        }
        
        // Cache'i temizle
        Cache::forget('ai_tenant_profile_' . tenant('id'));
    }
    
    /**
     * FormData'yı normalize et - dot notation'ları nested object'lere çevir
     */
    private function normalizeFormData(array $formData): array
    {
        $normalized = [];
        
        // Önce nested object'leri kopyala
        foreach ($formData as $key => $value) {
            if (strpos($key, '.') === false && is_array($value)) {
                $normalized[$key] = $value;
            } elseif (strpos($key, '.') === false) {
                $normalized[$key] = $value;
            }
        }
        
        // Sonra dot notation'ları işle
        foreach ($formData as $key => $value) {
            if (strpos($key, '.') !== false) {
                // Dot notation - nested object'e çevir
                $this->setNestedValue($normalized, $key, $value);
            }
        }
        
        return $normalized;
    }
    
    /**
     * Nested value set etme helper'ı
     */
    private function setNestedValue(array &$array, string $key, $value): void
    {
        $keys = explode('.', $key);
        $current = &$array;
        
        for ($i = 0; $i < count($keys) - 1; $i++) {
            $k = $keys[$i];
            if (!isset($current[$k])) {
                $current[$k] = [];
            } elseif (!is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        
        // Son key'i set et
        $lastKey = end($keys);
        $current[$lastKey] = $value;
    }
    
    public function completeProfile()
    {
        // Son adımı validate et
        if ($this->validateCurrentStep()) {
            // Son adım verilerini kaydet
            $this->saveStepData();
            
            // Profili tamamlandı olarak işaretle
            $this->profile->is_completed = true;
            $this->profile->save();
            
            // Cache'i temizle
            Cache::forget('ai_tenant_profile_' . tenant('id'));
            
            // Marka hikayesi oluştur (eğer yoksa)
            if (!$this->profile->hasBrandStory()) {
                try {
                    $brandStory = $this->profile->generateBrandStory();
                    session()->flash('brand_story_generated', true);
                    session()->flash('brand_story_content', $brandStory);
                } catch (\Exception $e) {
                    \Log::error('Brand story generation failed', ['error' => $e->getMessage()]);
                    session()->flash('brand_story_error', 'Marka hikayesi oluşturulurken hata oluştu: ' . $e->getMessage());
                }
            }
            
            // Başarı mesajı
            session()->flash('success', 'Yapay zeka profili başarıyla tamamlandı! Marka hikayeniz oluşturuldu ve profil sayfasında görüntüleniyor.');
            
            // AI profil sayfasına yönlendir
            return redirect()->route('admin.ai.profile.show');
        }
    }
    
    private function buildBrandContext(): string
    {
        $context = [];
        
        // Temel bilgiler
        if ($this->profile->company_info) {
            $context[] = "Marka Adı: " . ($this->profile->company_info['brand_name'] ?? 'Belirtilmemiş');
            $context[] = "Lokasyon: " . ($this->profile->company_info['city'] ?? 'Belirtilmemiş');
            $context[] = "Ana Hizmet: " . ($this->profile->company_info['main_service'] ?? 'Belirtilmemiş');
        }
        
        // Sektör bilgileri
        if ($this->profile->sector_details) {
            $context[] = "Sektör: " . ($this->profile->sector_details['sector'] ?? 'Belirtilmemiş');
            
            if (isset($this->profile->sector_details['brand_personality'])) {
                $personalities = is_array($this->profile->sector_details['brand_personality']) 
                    ? implode(', ', $this->profile->sector_details['brand_personality'])
                    : $this->profile->sector_details['brand_personality'];
                $context[] = "Marka Kişiliği: " . $personalities;
            }
            
            if (isset($this->profile->sector_details['company_size'])) {
                $context[] = "Şirket Büyüklüğü: " . $this->profile->sector_details['company_size'];
            }
            
            if (isset($this->profile->sector_details['brand_age'])) {
                $context[] = "Marka Yaşı: " . $this->profile->sector_details['brand_age'];
            }
        }
        
        // Başarı hikayeleri
        if ($this->profile->success_stories) {
            if (!empty($this->profile->success_stories['major_projects'])) {
                $context[] = "Önemli Projeler: " . $this->profile->success_stories['major_projects'];
            }
            if (!empty($this->profile->success_stories['client_references'])) {
                $context[] = "Müşteri Referansları: " . $this->profile->success_stories['client_references'];
            }
        }
        
        // Kurucu bilgileri (varsa)
        if ($this->profile->founder_info && !empty($this->profile->founder_info['founder_name'])) {
            $context[] = "Kurucu: " . $this->profile->founder_info['founder_name'];
            if (!empty($this->profile->founder_info['founder_title'])) {
                $context[] = "Kurucu Ünvanı: " . $this->profile->founder_info['founder_title'];
            }
        }
        
        return implode("\n", $context);
    }
    
    private function buildBrandStoryOptions(): array
    {
        $options = [
            'language' => 'Turkish'
        ];
        
        // Sektör
        if (isset($this->profile->sector_details['sector'])) {
            $options['industry'] = $this->profile->sector_details['sector'];
        }
        
        // Marka yaşına göre stage belirleme
        if (isset($this->profile->sector_details['brand_age'])) {
            $options['stage'] = match($this->profile->sector_details['brand_age']) {
                'new' => 'startup',
                'growing' => 'growth',
                'established' => 'mature',
                'mature' => 'established',
                default => 'growth'
            };
        }
        
        // Marka kişiliğinden değerler çıkarma
        if (isset($this->profile->sector_details['brand_personality']) && is_array($this->profile->sector_details['brand_personality'])) {
            $personalities = $this->profile->sector_details['brand_personality'];
            if (in_array('trustworthy', $personalities)) {
                $options['values'] = ['trust', 'quality'];
            } elseif (in_array('modern', $personalities)) {
                $options['values'] = ['innovation', 'technology'];
            } elseif (in_array('friendly', $personalities)) {
                $options['values'] = ['customer_service', 'relationships'];
            } else {
                $options['values'] = ['quality', 'excellence'];
            }
        }
        
        // Hedef kitle
        if (isset($this->profile->sector_details['target_audience']) && is_array($this->profile->sector_details['target_audience'])) {
            $audiences = $this->profile->sector_details['target_audience'];
            if (in_array('b2b-large', $audiences) || in_array('b2b-medium', $audiences)) {
                $options['audience'] = 'business';
            } elseif (in_array('b2c-young', $audiences) || in_array('b2c-family', $audiences)) {
                $options['audience'] = 'consumers';
            } else {
                $options['audience'] = 'general';
            }
        }
        
        // Benzersiz faktör
        if (isset($this->profile->sector_details['market_position'])) {
            $options['unique_factor'] = match($this->profile->sector_details['market_position']) {
                'premium' => 'quality',
                'luxury' => 'exclusivity',
                'innovative' => 'innovation',
                'specialist' => 'expertise',
                'budget' => 'affordability',
                default => 'customer_focus'
            };
        }
        
        return $options;
    }
    
    public function render()
    {
        $progressData = $this->calculateRealProgress();
        
        return view('ai::admin.livewire.ai-profile-management', [
            'progressPercentage' => ($this->currentStep / $this->totalSteps) * 100,
            'realProgressPercentage' => $progressData['percentage'],
            'completedFields' => $progressData['completed'],
            'totalFields' => $progressData['total']
        ]);
    }
    
    private function calculateRealProgress(): array
    {
        $totalFields = 0;
        $completedFields = 0;
        
        // Step 1: Sektör seçimi (1 alan)
        $totalFields += 1;
        if (!empty($this->formData['sector'])) {
            $completedFields += 1;
        }
        
        // Step 2: Temel bilgiler (4 alan)
        $step2Fields = ['company_info.brand_name', 'company_info.city', 'company_info.main_service', 'company_info.contact_info'];
        $totalFields += count($step2Fields);
        foreach ($step2Fields as $field) {
            if (!empty($this->formData[$field])) {
                $completedFields += 1;
            }
        }
        
        // Step 3: Marka detayları (6 alan) + sektöre özel sorular
        $step3Fields = [
            'sector_details.brand_personality',
            'sector_details.brand_age', 
            'sector_details.company_size',
            'sector_details.branches',
            'sector_details.target_audience',
            'sector_details.market_position'
        ];
        
        // Sektöre özel sorular varsa ekle
        if ($this->currentSectorCode) {
            $sectorQuestions = \Modules\AI\app\Models\AIProfileQuestion::where('step', 3)
                ->where('sector_code', $this->currentSectorCode)
                ->count();
            $totalFields += $sectorQuestions;
            
            // Sektöre özel alanları kontrol et
            foreach ($this->formData as $key => $value) {
                if (str_starts_with($key, 'sector_details.') && !in_array($key, $step3Fields) && !empty($value)) {
                    $completedFields += 1;
                }
            }
        }
        
        $totalFields += count($step3Fields);
        foreach ($step3Fields as $field) {
            if (!empty($this->formData[$field])) {
                $completedFields += 1;
            }
        }
        
        // Step 4: Kurucu bilgileri (1 zorunlu + isteğe bağlı)
        $totalFields += 1; // founder_permission
        if (!empty($this->formData['company_info.founder_permission'])) {
            $completedFields += 1;
            
            // Kurucu bilgileri aktifse ek alanlar
            if ($this->showFounderQuestions) {
                $founderFields = ['founder_info.founder_name', 'founder_info.founder_title', 'founder_info.founder_background', 'founder_info.founder_qualities'];
                $totalFields += count($founderFields);
                foreach ($founderFields as $field) {
                    if (!empty($this->formData[$field])) {
                        $completedFields += 1;
                    }
                }
            }
        }
        
        // Step 5: Başarı hikayeleri (3 alan - isteğe bağlı)
        $step5Fields = ['success_stories.major_projects', 'success_stories.client_references', 'success_stories.success_metrics'];
        $totalFields += count($step5Fields);
        foreach ($step5Fields as $field) {
            if (!empty($this->formData[$field])) {
                $completedFields += 1;
            }
        }
        
        // Step 6: AI davranış kuralları (soru bazında hesapla)
        $step6Questions = \Modules\AI\app\Models\AIProfileQuestion::where('step', 6)->get();
        $totalFields += $step6Questions->count();
        
        foreach ($step6Questions as $question) {
            $fieldKey = 'ai_behavior_rules.' . $question->question_key;
            
            // Checkbox/radio için en az bir seçim yapılmış mı kontrol et
            if ($question->input_type === 'checkbox') {
                $hasAnySelection = false;
                foreach ($this->formData as $key => $value) {
                    if (str_starts_with($key, $fieldKey . '.') && $value) {
                        $hasAnySelection = true;
                        break;
                    }
                }
                if ($hasAnySelection) {
                    $completedFields += 1;
                }
            } else {
                // Normal alanlar için
                if (!empty($this->formData[$fieldKey])) {
                    $completedFields += 1;
                }
            }
        }
        
        $percentage = $totalFields > 0 ? ($completedFields / $totalFields) * 100 : 0;
        
        return [
            'percentage' => $percentage,
            'completed' => $completedFields,
            'total' => $totalFields
        ];
    }
}