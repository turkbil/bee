<?php

namespace Modules\AI\App\Http\Livewire\Admin\Profile;

use Livewire\Component;
use Modules\AI\app\Models\AITenantProfile;
use Modules\AI\app\Models\AIProfileSector;
use Modules\AI\app\Models\AIProfileQuestion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class AIProfileManagement extends Component
{
    public $currentStep = 1;
    public $totalSteps = 5;
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
     * Progress Circle için toplam ve yanıtlanan soru sayısını hesapla
     */
    public function getTotalQuestionsProperty()
    {
        if (!$this->questions) {
            return 0;
        }
        return count($this->questions);
    }
    
    public function getAnsweredQuestionsProperty()
    {
        if (!$this->questions || !$this->formData) {
            return 0;
        }
        
        $answeredCount = 0;
        foreach ($this->questions as $question) {
            $fieldKey = $this->getFieldKey($question);
            
            // Formdata'da bu soru için veri var mı kontrol et
            if (isset($this->formData[$fieldKey]) && 
                !empty($this->formData[$fieldKey]) && 
                $this->formData[$fieldKey] !== null && 
                $this->formData[$fieldKey] !== '') {
                $answeredCount++;
            }
        }
        
        // Maksimum %100 garantisi
        return min($answeredCount, $this->totalQuestions);
    }
    
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
        $this->currentStep = max(1, min($this->totalSteps, (int) $initialStep));
        
        // Mevcut profili yükle veya yeni oluştur
        $this->profile = AITenantProfile::currentOrCreate();
        
        // Profil verilerini forma yükle
        $this->loadProfileData();
        
        // Kategorize edilmiş sektörleri yükle
        $this->sectors = AIProfileSector::getCategorizedSectors();
        
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
                        
                        // Tüm alanları blade template'in arayacağı formatta da yükle
                        $this->formData[$key] = $value;
                        \Log::debug("Loaded {$key} (direct)", ['value' => $value]);
                    }
                }
            }
            
            // Sector details
            if ($this->profile->sector_details && is_array($this->profile->sector_details)) {
                // Önce sector_selection'ı kontrol et, yoksa sector'ı kullan
                if (isset($this->profile->sector_details['sector_selection'])) {
                    $this->currentSectorCode = $this->profile->sector_details['sector_selection'];
                    $this->formData['sector'] = $this->currentSectorCode;
                    \Log::debug('Loaded sector from sector_selection', ['sector' => $this->currentSectorCode]);
                } elseif (isset($this->profile->sector_details['sector'])) {
                    $this->currentSectorCode = $this->profile->sector_details['sector'];
                    $this->formData['sector'] = $this->currentSectorCode;
                    \Log::debug('Loaded sector from sector', ['sector' => $this->currentSectorCode]);
                }
                
                foreach ($this->profile->sector_details as $key => $value) {
                    // Checkbox alanları için özel mapping (target_audience, brand_voice, digital_services, digital_technologies gibi)
                    if (is_array($value) && in_array($key, ['target_audience', 'brand_voice', 'digital_services', 'digital_project_types', 'tech_client_sectors', 'tech_daily_work', 'tech_project_size', 'tech_specialization', 'tech_work_style', 'tech_challenges', 'tech_pricing_model', 'health_daily_services', 'health_patient_age', 'health_specialization', 'health_facility_type', 'health_common_problems', 'health_appointment_type', 'health_pricing_system', 'education_daily_activities', 'education_subjects', 'education_student_level', 'education_teaching_method', 'education_class_size', 'education_challenges', 'education_goals', 'food_daily_operations', 'food_atmosphere', 'food_cuisine_type', 'food_service_type', 'food_customer_type', 'food_meal_times', 'food_challenges', 'retail_daily_tasks', 'retail_price_range', 'retail_product_category', 'retail_sales_channel', 'retail_customer_support', 'retail_inventory_size', 'retail_challenges', 'construction_daily_work', 'construction_scale', 'finance_daily_tasks', 'finance_client_type', 'law_daily_activities', 'law_service_type', 'beauty_daily_services', 'beauty_client_profile'])) {
                        foreach ($value as $subKey => $subValue) {
                            $this->formData["sector_details.{$key}.{$subKey}"] = $subValue;
                            \Log::debug("Loaded sector_details.{$key}.{$subKey}", ['value' => $subValue]);
                        }
                        
                        // Tüm alanları blade template'in arayacağı formatta da yükle
                        $this->formData[$key] = $value;
                        \Log::debug("Loaded {$key} (direct)", ['value' => $value]);
                    } else {
                        $this->formData["sector_details.{$key}"] = $value;
                        \Log::debug("Loaded sector_details.{$key}", ['value' => $value]);
                        
                        // Tüm alanları blade template'in arayacağı formatta da yükle
                        $this->formData[$key] = $value;
                        \Log::debug("Loaded {$key} (direct)", ['value' => $value]);
                    }
                }
            }
            
            // Success stories
            if ($this->profile->success_stories && is_array($this->profile->success_stories)) {
                foreach ($this->profile->success_stories as $key => $value) {
                    $this->formData["success_stories.{$key}"] = $value;
                    \Log::debug("Loaded success_stories.{$key}", ['value' => $value]);
                    
                    // Tüm alanları blade template'in arayacağı formatta da yükle
                    $this->formData[$key] = $value;
                    \Log::debug("Loaded {$key} (direct)", ['value' => $value]);
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
                        
                        // Tüm alanları blade template'in arayacağı formatta da yükle
                        $this->formData[$key] = $value;
                        \Log::debug("Loaded {$key} (direct)", ['value' => $value]);
                    }
                }
            }
            
            // Founder permission durumu kontrol et
            if (isset($this->formData['company_info.founder_permission'])) {
                $this->showFounderQuestions = in_array($this->formData['company_info.founder_permission'], ['Evet, bilgilerimi paylaşmak istiyorum']);
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
                        
                        // Tüm alanları blade template'in arayacağı formatta da yükle
                        $this->formData[$key] = $value;
                        \Log::debug("Loaded {$key} (direct)", ['value' => $value]);
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
                // Başarı hikayeleri ve AI davranış kuralları
                $this->questions = AIProfileQuestion::getByStep(5);
                // Step 6 sorularını da ekle (brand_voice vs)
                $additionalQuestions = AIProfileQuestion::getByStep(6);
                $this->questions = $this->questions->merge($additionalQuestions);
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
                // Kurucu bilgilerini temizle - hem formData hem de database
                foreach ($this->formData as $k => $v) {
                    if (str_starts_with($k, 'founder_info.')) {
                        unset($this->formData[$k]);
                    }
                }
                
                // Veritabanından da kurucu bilgilerini temizle
                if ($this->profile && $this->profile->exists) {
                    $this->profile->founder_info = [];
                    $this->profile->save();
                    
                    \Log::info('AIProfileManagement - Founder info cleared from database', [
                        'profile_id' => $this->profile->id,
                        'founder_permission' => $value
                    ]);
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
            
            // CRITICAL FIX: jQuery auto-save'den sonra güncel veritabanı verilerini al
            $this->refreshData();
            
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
            
            // CRITICAL FIX: jQuery auto-save'den sonra güncel veritabanı verilerini al
            // Bu sayede form verilerinin üzerine yazmayız
            $this->refreshData();
            
            \Log::info('AIProfileManagement - saveCurrentStep after refreshData', [
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
        // CRITICAL FIX: jQuery auto-save'den sonra güncel veritabanı verilerini al
        $this->refreshData();
        
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
            $founderQuestions = AIProfileQuestion::getOptionalSectionQuestions('founder_info', $this->currentSectorCode, 4);
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
        // Section varsa onu kullan önce
        if ($question->section) {
            $prefix = $question->section;
        } else {
            // Step 6 sorularını (brand_voice vs) ai_behavior_rules'a yönlendir
            if ($question->step == 6) {
                $prefix = 'ai_behavior_rules';
            } else {
                // Adıma göre field prefix belirle
                $prefix = match($this->currentStep) {
                    1 => '', // Sektör seçimi - prefix yok
                    2 => 'company_info', // Temel bilgiler
                    3 => 'sector_details', // Marka detayları / sektör bilgileri
                    4 => 'company_info', // Kurucu izin sorusu (sadece founder_permission)
                    5 => 'success_stories', // Başarı hikayeleri ve rekabet avantajları
                    default => ''
                };
            }
        }
        
        // Step 5 için özel field mapping - response_style ai_behavior_rules'a gitmeli
        if ($this->currentStep == 5 && $question->question_key == 'response_style') {
            $prefix = 'ai_behavior_rules';
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
                // Başarı hikayeleri ve AI davranış kuralları
                \Log::info('AIProfileManagement - Step 5: Saving success stories and AI behavior rules');
                
                // Başarı hikayeleri kaydet
                $successStoriesData = $normalizedData['success_stories'] ?? [];
                if (!empty($successStoriesData)) {
                    $this->profile->updateSection('success_stories', $successStoriesData);
                    \Log::info('AIProfileManagement - Step 5: Success stories saved successfully');
                }
                
                // AI davranış kuralları kaydet  
                $aiBehaviorData = $normalizedData['ai_behavior_rules'] ?? [];
                if (!empty($aiBehaviorData)) {
                    $this->profile->updateSection('ai_behavior_rules', $aiBehaviorData);
                    \Log::info('AIProfileManagement - Step 5: AI behavior rules saved successfully');
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
            
            \Log::info('Profile completed successfully', [
                'tenant_id' => tenant('id'),
                'profile_id' => $this->profile->id,
                'completion_percentage' => $this->calculateRealProgress()['percentage']
            ]);
            
            // Başarı mesajı
            session()->flash('success', 'Yapay zeka profili başarıyla tamamlandı! Profil sayfasında marka hikayeniz oluşturulacak.');
            
            // AI profil sayfasına yönlendir - hikaye oluşturma orada yapılacak
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
        
        return view('ai::admin.profile.ai-profile-management', [
            'progressPercentage' => ($this->currentStep / $this->totalSteps) * 100,
            'realProgressPercentage' => $progressData['percentage'],
            'completedFields' => $progressData['completed'],
            'totalFields' => $progressData['total']
        ]);
    }
    
    public function calculateRealProgress(): array
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
        
        // Step 5: Başarı hikayeleri ve rekabet avantajları (soru bazında hesapla)
        $step5Questions = \Modules\AI\app\Models\AIProfileQuestion::where('step', 5)->get();
        $totalFields += $step5Questions->count();
        
        foreach ($step5Questions as $question) {
            $fieldKey = 'success_stories.' . $question->question_key;
            
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