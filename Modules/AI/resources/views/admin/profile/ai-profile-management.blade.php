{{-- AI Profile Wizard - Modern Digital Experience - Single Root Element --}}
<div class="ai-profile-wizard-container">

    {{-- Main Content Container --}}
    <div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-12">
            
            {{-- Step Hero Section --}}
            <div class="step-hero-section mb-4">
                <div class="hero-section">
                    <div class="hero-background">
                        <div class="digital-grid"></div>
                        <div class="floating-elements"></div>
                        <div class="cyber-waves"></div>
                    </div>
                    
                    <div class="hero-content">
                        <div class="container">
                            {{-- Step Badge --}}
                            <div class="row mb-2">
                                <div class="col-12 text-start">
                                    <div class="hero-main-badge-container">
                                        <span class="badge hero-main-badge">
                                            Adım {{ $currentStep }}/{{ $totalSteps }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Step Content --}}
                            <div class="row align-items-center">
                                {{-- Sol - Step Bilgileri --}}
                                <div class="col-lg-8 col-md-7">
                                    <div class="hero-left-content">
                                        <div class="step-info-container">
                                            <div class="ai-hologram" style="
                                                width: 80px;
                                                height: 80px;
                                                background: conic-gradient(from 0deg, #00d4ff, #9333ea, #f59e0b, #10b981, #00d4ff);
                                                border-radius: 50%;
                                                animation: hologram-pulse 4s ease-in-out infinite;
                                                filter: drop-shadow(0 0 20px rgba(0, 212, 255, 0.6));
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                position: relative;
                                            ">
                                                <div style="
                                                    width: 68px;
                                                    height: 68px;
                                                    background: linear-gradient(135deg, #0f0f23, #1a1a2e);
                                                    border-radius: 50%;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                ">
                                                    @switch($currentStep)
                                                        @case(1) 
                                                            <i class="fas fa-industry" style="
                                                                font-size: 2rem;
                                                                color: #00d4ff;
                                                                filter: drop-shadow(0 0 10px rgba(0, 212, 255, 0.8));
                                                                animation: float-icon 3s ease-in-out infinite;
                                                            "></i> 
                                                            @break
                                                        @case(2) 
                                                            <i class="fas fa-building" style="
                                                                font-size: 2rem;
                                                                color: #00d4ff;
                                                                filter: drop-shadow(0 0 10px rgba(0, 212, 255, 0.8));
                                                                animation: float-icon 3s ease-in-out infinite;
                                                            "></i> 
                                                            @break
                                                        @case(3) 
                                                            <i class="fas fa-palette" style="
                                                                font-size: 2rem;
                                                                color: #00d4ff;
                                                                filter: drop-shadow(0 0 10px rgba(0, 212, 255, 0.8));
                                                                animation: float-icon 3s ease-in-out infinite;
                                                            "></i> 
                                                            @break
                                                        @case(4) 
                                                            <i class="fas fa-user-tie" style="
                                                                font-size: 2rem;
                                                                color: #00d4ff;
                                                                filter: drop-shadow(0 0 10px rgba(0, 212, 255, 0.8));
                                                                animation: float-icon 3s ease-in-out infinite;
                                                            "></i> 
                                                            @break
                                                        @case(5) 
                                                            <i class="fas fa-trophy" style="
                                                                font-size: 2rem;
                                                                color: #00d4ff;
                                                                filter: drop-shadow(0 0 10px rgba(0, 212, 255, 0.8));
                                                                animation: float-icon 3s ease-in-out infinite;
                                                            "></i> 
                                                            @break
                                                        @case(6) 
                                                            <i class="fas fa-robot" style="
                                                                font-size: 2rem;
                                                                color: #00d4ff;
                                                                filter: drop-shadow(0 0 10px rgba(0, 212, 255, 0.8));
                                                                animation: float-icon 3s ease-in-out infinite;
                                                            "></i> 
                                                            @break
                                                    @endswitch
                                                </div>
                                            </div>
                                            <div class="step-text-content">
                                                <h1 class="hero-title">
                                                    @switch($currentStep)
                                                        @case(1) Sektör Seçimi @break
                                                        @case(2) Temel Bilgiler @break
                                                        @case(3) Marka Detayları @break
                                                        @case(4) Kurucu Bilgileri @break
                                                        @case(5) Yapay Zeka Davranış Ayarları @break
                                                    @endswitch
                                                </h1>
                                                <p class="hero-subtitle">
                                                    @switch($currentStep)
                                                        @case(1) Yapay zeka asistanınız için en uygun sektörü seçin @break
                                                        @case(2) İşletmenizin temel bilgilerini girin @break
                                                        @case(3) Markanızın kişiliğini tanımlayın @break
                                                        @case(4) Kurucu bilgilerini paylaşın (isteğe bağlı) @break
                                                        @case(5) Yapay zeka asistanınızın davranış tarzını belirleyin @break
                                                    @endswitch
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Sağ - Progress Circle --}}
                                <div class="col-lg-4 col-md-5">
                                    <div class="hero-right-content">
                                        <div class="progress-section">
                                            <div class="progress-circle-container">
                                                <div class="progress-circle progress-circle-large">
                                                    <svg class="progress-svg" viewBox="0 0 100 100">
                                                        <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(var(--tblr-muted-rgb, 255,255,255),0.1)" stroke-width="6"/>
                                                        <circle cx="50" cy="50" r="45" fill="none" stroke="url(#stepGradient)" stroke-width="6" 
                                                                stroke-dasharray="282.74" stroke-dashoffset="{{ 282.74 - (282.74 * $realProgressPercentage / 100) }}"
                                                                transform="rotate(-90 50 50)" stroke-linecap="round"/>
                                                        <defs>
                                                            <linearGradient id="stepGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                                                <stop offset="0%" style="stop-color:#00d4ff"/>
                                                                <stop offset="50%" style="stop-color:#9333ea"/>
                                                                <stop offset="100%" style="stop-color:#f59e0b"/>
                                                            </linearGradient>
                                                        </defs>
                                                    </svg>
                                                    <div class="progress-text">
                                                        <span class="progress-percentage">{{ round($realProgressPercentage) }}%</span>
                                                        <small class="progress-label">Tamamlandı</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Steps Indicator --}}
                    <div class="container">
                        <div class="steps-indicator">
                            @for($i = 1; $i <= $totalSteps; $i++)
                                <a href="{{ route('admin.ai.profile.edit', ['step' => $i]) }}" 
                                   class="step-item {{ $i <= $currentStep ? 'active' : '' }} {{ $i < $currentStep ? 'completed' : '' }}"
                                   style="text-decoration: none; color: inherit;">
                                    <div class="step-circle">
                                        @if($i < $currentStep)
                                            <i class="fas fa-check"></i>
                                        @else
                                            {{ $i }}
                                        @endif
                                    </div>
                                    <span class="step-label">
                                        @switch($i)
                                            @case(1) Sektör @break
                                            @case(2) Bilgiler @break
                                            @case(3) Marka @break
                                            @case(4) Kurucu @break
                                            @case(5) Yapay Zeka @break
                                        @endswitch
                                    </span>
                                </a>
                                @if($i < $totalSteps)
                                    <div class="step-connector {{ $i < $currentStep ? 'completed' : '' }}"></div>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- Wizard Card --}}
            <div class="card wizard-card border-0 shadow-lg">
                <div class="card-body p-5">
                    {{-- Form Content --}}
                    <form wire:submit.prevent="completeProfile">
                        <div class="form-content">
                            
                            @foreach($questions as $question)
                                @php
                                    $fieldKey = match($currentStep) {
                                        1 => $question->question_key,
                                        2 => 'company_info.' . $question->question_key,
                                        3 => 'sector_details.' . $question->question_key,
                                        4 => 'company_info.' . $question->question_key,
                                        5 => 'success_stories.' . $question->question_key,
                                        6 => 'ai_behavior_rules.' . $question->question_key,
                                        default => $question->question_key
                                    };
                                @endphp
                                
                                <div class="form-group mb-4">
                                    
                                    {{-- Question Label --}}
                                    <label class="form-label fw-bold fs-5 mb-3">
                                        {{ $question->question_text }}
                                        @if($question->is_required)
                                            <span class="text-danger ms-1">*</span>
                                        @endif
                                    </label>
                                    
                                    @if($question->help_text)
                                        <div class="form-hint">{{ $question->help_text }}</div>
                                    @endif
                                    
                                    {{-- Input Based on Type --}}
                                    @switch($question->input_type)
                                        
                                        @case('select')
                                            {{-- Special handling for sector selection with categorized grid --}}
                                            @if($question->question_key === 'sector' && $currentStep === 1)
                                                @foreach($sectors as $mainCategory)
                                                    {{-- Ana Kategori Başlığı (Tıklanamaz Bant) --}}
                                                    <div class="col-12 mb-3">
                                                        <div class="category-header-band">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon me-3">
                                                                    @if($mainCategory->emoji)
                                                                        <span class="category-emoji">{{ $mainCategory->emoji }}</span>
                                                                    @elseif($mainCategory->icon)
                                                                        <i class="{{ $mainCategory->icon }} category-icon-style"></i>
                                                                    @else
                                                                        <i class="fas fa-folder category-icon-style"></i>
                                                                    @endif
                                                                </div>
                                                                <div class="category-title">
                                                                    <h5 class="mb-0 fw-bold text-{{ $mainCategory->color ?? 'primary' }}">{{ $mainCategory->name }}</h5>
                                                                    @if($mainCategory->description)
                                                                        <small class="text-muted">{{ $mainCategory->description }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Alt Kategoriler (Sektörler) --}}
                                                    <div class="row g-3 mb-4">
                                                        @foreach($mainCategory->subCategories as $sector)
                                                            <div class="col-6 col-md-4 col-lg-3">
                                                                <label class="form-imagecheck mb-2">
                                                                    <input type="radio" name="formData[{{ $fieldKey }}]" value="{{ $sector->code }}" 
                                                                           class="form-imagecheck-input profile-field-input" 
                                                                           data-field="{{ $fieldKey }}"
                                                                           x-data="{ isChecked: '{{ $formData[$fieldKey] ?? '' }}' === '{{ $sector->code }}' }"
                                                                           x-init="$el.checked = isChecked"
                                                                           @if(isset($formData[$fieldKey]) && $formData[$fieldKey] == $sector->code) checked @endif
                                                                           @if($question->is_required) required @endif>
                                                                    <span class="form-imagecheck-figure">
                                                                        <div class="form-imagecheck-image sector-card d-flex flex-column" style="min-height: 120px;">
                                                                            <div class="sector-icon">
                                                                                @if($sector->emoji)
                                                                                    <span class="sector-emoji">{{ $sector->emoji }}</span>
                                                                                @elseif($sector->icon)
                                                                                    <i class="{{ $sector->icon }}"></i>
                                                                                @else
                                                                                    <i class="fas fa-briefcase"></i>
                                                                                @endif
                                                                            </div>
                                                                            <div class="sector-name">{{ $sector->name }}</div>
                                                                            <div class="sector-desc flex-grow-1">{{ $sector->description }}</div>
                                                                        </div>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            @else
                                                {{-- Normal select dropdown for other fields --}}
                                                <select class="form-select profile-field-input" 
                                                        name="formData[{{ $fieldKey }}]"
                                                        data-field="{{ $fieldKey }}"
                                                        x-data="{ fieldValue: '{{ addslashes($formData[$fieldKey] ?? '') }}' }"
                                                        x-init="$el.value = fieldValue"
                                                        @if($question->is_required) required @endif>
                                                    <option value="">Seçiniz...</option>
                                                    @if($question->options)
                                                        @foreach($question->options as $option)
                                                            <option value="{{ $option['value'] }}" 
                                                                    @if(isset($formData[$fieldKey]) && $formData[$fieldKey] == $option['value']) selected @endif>
                                                                {{ $option['label'] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            @endif
                                            @break
                                            
                                        @case('radio')
                                            @php
                                                // Tüm soru için en uzun metni bul
                                                $maxLength = 0;
                                                foreach($question->options as $opt) {
                                                    $maxLength = max($maxLength, strlen($opt['label']));
                                                }
                                                
                                                // Tüm soru için col class belirle
                                                if ($maxLength > 40) {
                                                    $questionColClass = 'col-12';
                                                } elseif ($maxLength > 25) {
                                                    $questionColClass = 'col-md-6 col-12';
                                                } else {
                                                    $questionColClass = 'col-md-6 col-12';
                                                }
                                            @endphp
                                            <div class="row">
                                                @if($question->options)
                                                    @foreach($question->options as $option)
                                                        <div class="{{ $questionColClass }} mb-2">
                                                            <label class="form-selectgroup-item flex-fill">
                                                                <input type="radio" name="formData[{{ $fieldKey }}]" value="{{ $option['value'] }}" 
                                                                       class="form-selectgroup-input profile-field-input @if(isset($option['has_custom_input'])) custom-radio-trigger @endif" 
                                                                       data-field="{{ $fieldKey }}"
                                                                       @if(isset($option['has_custom_input'])) data-custom-field="{{ $fieldKey }}_custom" @endif
                                                                       x-data="{ isChecked: '{{ $formData[$fieldKey] ?? '' }}' === '{{ $option['value'] }}' }"
                                                                       x-init="$el.checked = isChecked"
                                                                       @if(isset($formData[$fieldKey]) && $formData[$fieldKey] == $option['value']) checked @endif
                                                                       @if($question->is_required) required @endif>
                                                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                                    <div class="me-3">
                                                                        <span class="form-selectgroup-check"></span>
                                                                    </div>
                                                                    <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                        @if(isset($option['icon']))
                                                                            <i class="{{ $option['icon'] }} me-3 text-muted"></i>
                                                                        @else
                                                                            <i class="fas fa-dot-circle me-3 text-muted"></i>
                                                                        @endif
                                                                        <div>
                                                                            <div class="font-weight-medium">{{ $option['label'] }}</div>
                                                                            @if(isset($option['description']))
                                                                                <div class="text-secondary small">{{ $option['description'] }}</div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    
                                                    {{-- Custom Input Field (Hidden by default) --}}
                                                    @foreach($question->options as $option)
                                                        @if(isset($option['has_custom_input']))
                                                            <div class="col-12 mt-3" id="{{ $fieldKey }}_custom_container" 
                                                                 style="display: none;">
                                                                <input type="text" 
                                                                       class="form-control profile-field-input" 
                                                                       name="formData[{{ $fieldKey }}_custom]"
                                                                       data-field="{{ $fieldKey }}_custom"
                                                                       placeholder="{{ $option['custom_placeholder'] ?? 'Özel bilginizi giriniz...' }}"
                                                                       value="{{ $formData[$fieldKey . '_custom'] ?? '' }}"
                                                                       x-data="{ fieldValue: '{{ addslashes($formData[$fieldKey . '_custom'] ?? '') }}' }"
                                                                       x-init="$el.value = fieldValue">
                                                            </div>
                                                            @break
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                            @break
                                            
                                        @case('checkbox')
                                            @php
                                                // Tüm soru için en uzun metni bul
                                                $maxLength = 0;
                                                foreach($question->options as $opt) {
                                                    $maxLength = max($maxLength, strlen($opt['label']));
                                                }
                                                
                                                // Tüm soru için col class belirle
                                                if ($maxLength > 40) {
                                                    $questionColClass = 'col-12';
                                                } elseif ($maxLength > 25) {
                                                    $questionColClass = 'col-md-6 col-12';
                                                } else {
                                                    $questionColClass = 'col-md-6 col-12';
                                                }
                                            @endphp
                                            <div class="row">
                                                @if($question->options)
                                                    @foreach($question->options as $option)
                                                        <div class="{{ $questionColClass }} mb-2">
                                                            <label class="form-selectgroup-item flex-fill">
                                                                <input type="checkbox" value="{{ $option['value'] }}" 
                                                                       class="form-selectgroup-input profile-field-checkbox @if(isset($option['has_custom_input'])) custom-checkbox-trigger @endif" 
                                                                       name="formData[{{ $fieldKey }}][]"
                                                                       data-field="{{ $fieldKey }}"
                                                                       @if(isset($option['has_custom_input'])) data-custom-field="{{ $fieldKey }}_custom" @endif
                                                                       @php
                                                                           $nestedFieldKey = $fieldKey . '.' . $option['value'];
                                                                           $isChecked = isset($formData[$nestedFieldKey]) && $formData[$nestedFieldKey];
                                                                       @endphp
                                                                       x-data="{ isChecked: Boolean({{ $isChecked ? 'true' : 'false' }}) }"
                                                                       x-init="$el.checked = isChecked"
                                                                       @if($isChecked) checked @endif>
                                                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                                    <div class="me-3">
                                                                        <span class="form-selectgroup-check"></span>
                                                                    </div>
                                                                    <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                        @if(isset($option['icon']))
                                                                            <i class="{{ $option['icon'] }} me-3 text-muted"></i>
                                                                        @else
                                                                            <i class="fas fa-check me-3 text-muted"></i>
                                                                        @endif
                                                                        <div>
                                                                            <div class="font-weight-medium">{{ $option['label'] }}</div>
                                                                            @if(isset($option['description']))
                                                                                <div class="text-secondary small">{{ $option['description'] }}</div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    
                                                    {{-- Custom Input Field for Checkbox (Hidden by default) --}}
                                                    @foreach($question->options as $option)
                                                        @if(isset($option['has_custom_input']))
                                                            <div class="col-12 mt-3" id="{{ $fieldKey }}_custom_container" 
                                                                 style="display: none;">
                                                                <input type="text" 
                                                                       class="form-control profile-field-input" 
                                                                       name="formData[{{ $fieldKey }}_custom]"
                                                                       data-field="{{ $fieldKey }}_custom"
                                                                       placeholder="{{ $option['custom_placeholder'] ?? 'Özel bilginizi giriniz...' }}"
                                                                       value="{{ $formData[$fieldKey . '_custom'] ?? '' }}"
                                                                       x-data="{ fieldValue: '{{ addslashes($formData[$fieldKey . '_custom'] ?? '') }}' }"
                                                                       x-init="$el.value = fieldValue">
                                                            </div>
                                                            @break
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                            @break
                                            
                                        @case('textarea')
                                            <textarea class="form-control profile-field-input" rows="4" 
                                                      name="formData[{{ $fieldKey }}]"
                                                      data-field="{{ $fieldKey }}"
                                                      x-data="{ fieldValue: '{{ addslashes($formData[$fieldKey] ?? '') }}' }"
                                                      x-init="$el.value = fieldValue"
                                                      placeholder="{{ $question->input_placeholder ?? '' }}"
                                                      @if($question->is_required) required @endif>{{ $formData[$fieldKey] ?? '' }}</textarea>
                                            @break
                                            
                                        @default
                                            <input type="text" class="form-control profile-field-input" 
                                                   name="formData[{{ $fieldKey }}]"
                                                   data-field="{{ $fieldKey }}"
                                                   x-data="{ fieldValue: '{{ addslashes($formData[$fieldKey] ?? '') }}' }"
                                                   x-init="$el.value = fieldValue"
                                                   placeholder="{{ $question->input_placeholder ?? '' }}"
                                                   @if($question->is_required) required @endif>
                                            @break
                                            
                                    @endswitch
                                    
                                    {{-- Error Messages --}}
                                    @error("formData.{$fieldKey}")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    
                                    {{-- Custom validation errors --}}
                                    @if(isset($customErrors[$fieldKey]))
                                        <div class="invalid-feedback d-block">
                                            @if(is_array($customErrors[$fieldKey]))
                                                @foreach($customErrors[$fieldKey] as $error)
                                                    <div>{{ $error }}</div>
                                                @endforeach
                                            @else
                                                {{ $customErrors[$fieldKey] }}
                                            @endif
                                        </div>
                                    @endif
                                    
                                </div>
                                
                            @endforeach
                            
                            {{-- Founder Questions (if enabled) --}}
                            @if($currentStep === 4)
                                <div class="founder-section" id="founder-questions-section" style="display: none;">
                                    <h5 class="mb-3">Kurucu Bilgileri</h5>
                                    @php
                                        $founderQuestions = \Modules\AI\app\Models\AIProfileQuestion::getOptionalSectionQuestions('founder_info', $this->currentSectorCode, 4);
                                    @endphp
                                    
                                    @foreach($founderQuestions as $question)
                                        @php
                                            $fieldKey = 'founder_info.' . $question->question_key;
                                        @endphp
                                        
                                        <div class="form-group mb-4">
                                            <label class="form-label">{{ $question->question_text }}</label>
                                            @if($question->help_text)
                                                <div class="form-hint">{{ $question->help_text }}</div>
                                            @endif
                                            
                                            {{-- Input Based on Type --}}
                                            @switch($question->input_type)
                                                
                                                @case('radio')
                                                    @php
                                                        // Tüm soru için en uzun metni bul
                                                        $maxLength = 0;
                                                        foreach($question->options as $opt) {
                                                            $maxLength = max($maxLength, strlen($opt['label']));
                                                        }
                                                        
                                                        // Tüm soru için col class belirle
                                                        if ($maxLength > 40) {
                                                            $questionColClass = 'col-12';
                                                        } elseif ($maxLength > 25) {
                                                            $questionColClass = 'col-md-6 col-12';
                                                        } else {
                                                            $questionColClass = 'col-md-6 col-12';
                                                        }
                                                    @endphp
                                                    <div class="row">
                                                        @if($question->options)
                                                            @foreach($question->options as $option)
                                                                <div class="{{ $questionColClass }} mb-2">
                                                                    <label class="form-selectgroup-item flex-fill">
                                                                        <input type="radio" name="formData[{{ $fieldKey }}]" value="{{ $option['value'] }}" 
                                                                               class="form-selectgroup-input profile-field-input @if(isset($option['has_custom_input'])) custom-radio-trigger @endif" 
                                                                               data-field="{{ $fieldKey }}"
                                                                               @if(isset($option['has_custom_input'])) data-custom-field="{{ $fieldKey }}_custom" @endif
                                                                               x-data="{ isChecked: '{{ $formData[$fieldKey] ?? '' }}' === '{{ $option['value'] }}' }"
                                                                               x-init="$el.checked = isChecked"
                                                                               @if(isset($formData[$fieldKey]) && $formData[$fieldKey] == $option['value']) checked @endif>
                                                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                                            <div class="me-3">
                                                                                <span class="form-selectgroup-check"></span>
                                                                            </div>
                                                                            <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                                @if(isset($option['icon']))
                                                                                    <i class="{{ $option['icon'] }} me-3 text-muted"></i>
                                                                                @else
                                                                                    <i class="fas fa-dot-circle me-3 text-muted"></i>
                                                                                @endif
                                                                                <div>
                                                                                    <div class="font-weight-medium">{{ $option['label'] }}</div>
                                                                                    @if(isset($option['description']))
                                                                                        <div class="text-secondary small">{{ $option['description'] }}</div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                            
                                                            {{-- Custom Input Field for Radio (Hidden by default) --}}
                                                            @foreach($question->options as $option)
                                                                @if(isset($option['has_custom_input']))
                                                                    <div class="col-12 mt-3" id="{{ $fieldKey }}_custom_container" 
                                                                         style="display: none;">
                                                                        <input type="text" 
                                                                               class="form-control profile-field-input" 
                                                                               name="formData[{{ $fieldKey }}_custom]"
                                                                               data-field="{{ $fieldKey }}_custom"
                                                                               placeholder="{{ $option['custom_placeholder'] ?? 'Özel bilginizi giriniz...' }}"
                                                                               value="{{ $formData[$fieldKey . '_custom'] ?? '' }}"
                                                                               x-data="{ fieldValue: '{{ addslashes($formData[$fieldKey . '_custom'] ?? '') }}' }"
                                                                               x-init="$el.value = fieldValue">
                                                                    </div>
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    @break
                                                    
                                                @case('checkbox')
                                                    @php
                                                        // Tüm soru için en uzun metni bul
                                                        $maxLength = 0;
                                                        foreach($question->options as $opt) {
                                                            $maxLength = max($maxLength, strlen($opt['label']));
                                                        }
                                                        
                                                        // Tüm soru için col class belirle
                                                        if ($maxLength > 40) {
                                                            $questionColClass = 'col-12';
                                                        } elseif ($maxLength > 25) {
                                                            $questionColClass = 'col-md-6 col-12';
                                                        } else {
                                                            $questionColClass = 'col-md-6 col-12';
                                                        }
                                                    @endphp
                                                    <div class="row">
                                                        @if($question->options)
                                                            @foreach($question->options as $option)
                                                                <div class="{{ $questionColClass }} mb-2">
                                                                    <label class="form-selectgroup-item flex-fill">
                                                                        <input type="checkbox" value="{{ $option['value'] }}" 
                                                                               class="form-selectgroup-input profile-field-checkbox @if(isset($option['has_custom_input'])) custom-checkbox-trigger @endif" 
                                                                               name="formData[{{ $fieldKey }}][]"
                                                                               data-field="{{ $fieldKey }}"
                                                                               @if(isset($option['has_custom_input'])) data-custom-field="{{ $fieldKey }}_custom" @endif
                                                                               @php
                                                                                   $nestedFieldKey = $fieldKey . '.' . $option['value'];
                                                                                   $isChecked = isset($formData[$nestedFieldKey]) && $formData[$nestedFieldKey];
                                                                               @endphp
                                                                               x-data="{ isChecked: Boolean({{ $isChecked ? 'true' : 'false' }}) }"
                                                                               x-init="$el.checked = isChecked"
                                                                               @if($isChecked) checked @endif>
                                                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                                            <div class="me-3">
                                                                                <span class="form-selectgroup-check"></span>
                                                                            </div>
                                                                            <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                                @if(isset($option['icon']))
                                                                                    <i class="{{ $option['icon'] }} me-3 text-muted"></i>
                                                                                @else
                                                                                    <i class="fas fa-check me-3 text-muted"></i>
                                                                                @endif
                                                                                <div>
                                                                                    <div class="font-weight-medium">{{ $option['label'] }}</div>
                                                                                    @if(isset($option['description']))
                                                                                        <div class="text-secondary small">{{ $option['description'] }}</div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                            
                                                            {{-- Custom Input Field for Checkbox (Hidden by default) --}}
                                                            @foreach($question->options as $option)
                                                                @if(isset($option['has_custom_input']))
                                                                    <div class="col-12 mt-3" id="{{ $fieldKey }}_custom_container" 
                                                                         style="display: none;">
                                                                        <input type="text" 
                                                                               class="form-control profile-field-input" 
                                                                               name="formData[{{ $fieldKey }}_custom]"
                                                                               data-field="{{ $fieldKey }}_custom"
                                                                               placeholder="{{ $option['custom_placeholder'] ?? 'Özel bilginizi giriniz...' }}"
                                                                               value="{{ $formData[$fieldKey . '_custom'] ?? '' }}"
                                                                               x-data="{ fieldValue: '{{ addslashes($formData[$fieldKey . '_custom'] ?? '') }}' }"
                                                                               x-init="$el.value = fieldValue">
                                                                    </div>
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    @break
                                                    
                                                @case('textarea')
                                                    <textarea class="form-control profile-field-input" rows="3" 
                                                              name="formData[{{ $fieldKey }}]"
                                                              data-field="{{ $fieldKey }}"
                                                              x-data="{ fieldValue: '{{ addslashes($formData[$fieldKey] ?? '') }}' }"
                                                              x-init="$el.value = fieldValue"
                                                              placeholder="{{ $question->input_placeholder ?? '' }}">{{ $formData[$fieldKey] ?? '' }}</textarea>
                                                    @break
                                                    
                                                @default
                                                    <input type="text" class="form-control profile-field-input" 
                                                           name="formData[{{ $fieldKey }}]"
                                                           data-field="{{ $fieldKey }}"
                                                           x-data="{ fieldValue: '{{ addslashes($formData[$fieldKey] ?? '') }}' }"
                                                           x-init="$el.value = fieldValue"
                                                           placeholder="{{ $question->input_placeholder ?? '' }}">
                                                    @break
                                                    
                                            @endswitch
                                            
                                            {{-- Founder Error Messages --}}
                                            @php $founderFieldKey = "founder_info.{$question->question_key}"; @endphp
                                            @error("formData.{$founderFieldKey}")
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            
                                            @if(isset($customErrors[$founderFieldKey]))
                                                <div class="invalid-feedback d-block">
                                                    @if(is_array($customErrors[$founderFieldKey]))
                                                        @foreach($customErrors[$founderFieldKey] as $error)
                                                            <div>{{ $error }}</div>
                                                        @endforeach
                                                    @else
                                                        {{ $customErrors[$founderFieldKey] }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        {{-- Navigation Buttons - jQuery Controlled --}}
                        <div class="form-footer mt-5">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex gap-2">
                                    @if($currentStep > 1)
                                        <button type="button" class="btn btn-ghost-primary btn-nav-previous"
                                                data-current-step="{{ $currentStep }}"
                                                data-target-step="{{ $currentStep - 1 }}">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Önceki Adım
                                        </button>
                                    @endif
                                    
                                </div>
                                <div>
                                    @if($currentStep < $totalSteps)
                                        <button type="button" class="btn btn-primary btn-lg btn-nav-next" 
                                                data-current-step="{{ $currentStep }}"
                                                data-target-step="{{ $currentStep + 1 }}">
                                            Sonraki Adım
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success btn-lg btn-complete-profile">
                                            <i class="fas fa-magic me-2"></i>
                                            Yapay Zeka Asistanını Aktifleştir
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
            
        </div>
    </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('admin-assets/libs/choices/choices.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-assets/css/ai-profile-wizard.css') }}?v={{ time() }}">
@endpush

@push('scripts')
<script src="{{ asset('admin-assets/libs/jquery@3.7.1/jquery.min.js') }}"></script>
<script src="{{ asset('admin-assets/libs/choices/choices.min.js') }}"></script>
<script src="{{ asset('admin-assets/js/ai-profile-wizard.js') }}"></script>


<script>
// Global auto-save function (Livewire olmasa bile çalışsın)
window.autoSaveAndNavigate = window.autoSaveAndNavigate || function(nextUrl) {
    // Fallback: direkt yönlendirme
    window.location.href = nextUrl;
};

// Force reload form values after Livewire hydration
document.addEventListener('livewire:load', function () {
    initializeFormFields();
});

document.addEventListener('livewire:update', function () {
    setTimeout(initializeFormFields, 100); // Kısa delay ile
});

function initializeFormFields() {
    // Find all elements with x-data for field initialization
    document.querySelectorAll('[x-data*="fieldValue"]').forEach(function(element) {
        if (element.type === 'checkbox' || element.type === 'radio') {
            // For checkboxes and radios, use the x-data isChecked property
            if (element.hasAttribute('x-data') && element.getAttribute('x-data').includes('isChecked')) {
                try {
                    // Extract the isChecked value from x-data
                    const xDataAttr = element.getAttribute('x-data');
                    const isCheckedMatch = xDataAttr.match(/isChecked:\s*(true|false|\w+)/);
                    if (isCheckedMatch) {
                        const shouldBeChecked = isCheckedMatch[1] === 'true';
                        if (element.checked !== shouldBeChecked) {
                            element.checked = shouldBeChecked;
                        }
                    }
                } catch (e) {
                    // Checkbox/radio initialization error
                }
            }
        } else {
            // For text inputs, selects, textareas
            try {
                const xDataAttr = element.getAttribute('x-data');
                const fieldValueMatch = xDataAttr.match(/fieldValue:\s*'([^']*)'/);
                if (fieldValueMatch) {
                    const expectedValue = fieldValueMatch[1];
                    if (element.value !== expectedValue) {
                        element.value = expectedValue;
                    }
                }
            } catch (e) {
                // Input value initialization error
            }
        }
    });
}

// Run immediately on load
document.addEventListener('DOMContentLoaded', initializeFormFields);

// ===== JQUERY AUTO-SAVE SİSTEMİ (LİVEWİRE'DAN BAĞIMSIZ) =====
$(document).ready(function() {
    let autoSaveTimeout;
    
    // Auto-save system initialization
    
    // Checkbox functionality initialization
    setTimeout(() => {
        // Initialize checkbox handlers
    }, 1000);
    
    // Initialize step 3 and 5 checkboxes
    setTimeout(() => {
        // Checkbox initialization for steps 3 and 5
    }, 1000);
    
    // STEP 3 & 5 CHECKBOX'LAR İÇİN UNIFIED HANDLER
    $(document).on('change', 'input[data-field*="sector_details"]:checkbox, input[data-field*="success_stories"]:checkbox', function(e) {
        const $cb = $(this);
        const field = $cb.data('field');
        const fieldValue = [];
        
        // EXACT FIELD MATCH İLE VALUE COLLECTION
        $(`input[data-field="${field}"]`).each(function() {
            const $checkbox = $(this);
            if ($checkbox.is(':checked')) {
                fieldValue.push($checkbox.val());
            }
        });
        
        // INSTANT SAVE
        saveFieldData(field, fieldValue);
    });
    
    // STEP 4 (FOUNDER) KORUNAN NORMAL HANDLER
    $(document).on('change', 'input[data-field*="founder_info"]', function(event) {
        const fieldName = $(this).data('field');
        if (!fieldName) return;
        
        const fieldValue = getFieldValue($(this));
        
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            saveFieldData(fieldName, fieldValue);
        }, 500);
    });
    
    // DİĞER ALANLAR İÇİN GENERIC HANDLER (TEXT, TEXTAREA ETC)
    $(document).on('change', 'input[data-field]:not([data-field*="sector_details"]):not([data-field*="success_stories"]):not([data-field*="founder_info"]), select[data-field], textarea[data-field]', function(event) {
        const fieldName = $(this).data('field');
        if (!fieldName) return;
        
        const fieldValue = getFieldValue($(this));
        
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            saveFieldData(fieldName, fieldValue);
        }, 500);
    });
    
    // Textarea için typing sırasında (input event)
    $(document).on('input', 'textarea[data-field]', function() {
        const fieldName = $(this).data('field');
        const fieldValue = $(this).val();
        
        // Auto-save tetikle (debounce ile)
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            saveFieldData(fieldName, fieldValue);
        }, 2000); // 2 saniye sonra kaydet
    });
    
    function getFieldValue($element) {
        if ($element.is(':checkbox')) {
            // Checkbox array değeri - Step 4 founder pattern'i ile aynı
            const fieldName = $element.data('field');
            const checkedValues = [];
            
            // Aynı field name'e sahip tüm checkbox'ları bul
            const selector = `input[data-field="${fieldName}"]`;
            const checkedCheckboxes = $(selector + ':checked');
            
            // Seçili checkbox'ları topla
            checkedCheckboxes.each(function(index) {
                const value = $(this).val();
                checkedValues.push(value);
            });
            
            return checkedValues;
            
        } else if ($element.is(':radio')) {
            // Radio tek değer - Step 4 founder pattern'i ile aynı
            const value = $element.val();
            return value;
            
        } else {
            // Text, select, textarea - Step 4 founder pattern'i ile aynı
            const value = $element.val();
            return value;
        }
    }
    
    function saveFieldData(fieldName, fieldValue) {
        // AJAX data hazırla
        const ajaxData = {
            _token: '{{ csrf_token() }}',
            field: fieldName,
            value: fieldValue,
            step: {{ $currentStep }}
        };
        
        // AJAX ile Livewire component'ine gönder - Step 4 founder pattern'i ile aynı
        $.ajax({
            url: '{{ route("admin.ai.profile.save-field") }}',
            method: 'POST',
            data: ajaxData,
            success: function(response, textStatus, xhr) {
                // Başarı bildirimi (opsiyonel) - Step 4 founder pattern'i ile aynı
                if (response && response.success) {
                    // Küçük bir visual feedback
                    $(`[data-field="${fieldName}"]`).addClass('field-saved');
                    setTimeout(function() {
                        $(`[data-field="${fieldName}"]`).removeClass('field-saved');
                    }, 1000);
                }
            },
            error: function(xhr, status, error) {
                // Error handling without verbose logging
            }
        });
    }
});

// CSS için field-saved class ve animasyonlar
const style = document.createElement('style');
style.textContent = `
.field-saved {
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.3) !important;
    transition: box-shadow 0.3s ease;
}

@keyframes float-icon {
    0%, 100% { 
        transform: scale(1); 
    }
    50% { 
        transform: scale(1.2); 
    }
}

@keyframes hologram-pulse {
    0% { 
        transform: scale(1); 
        filter: drop-shadow(0 0 20px rgba(0, 212, 255, 0.6));
    }
    25% { 
        transform: scale(1.05);
        filter: drop-shadow(0 0 25px rgba(147, 51, 234, 0.6));
    }
    50% { 
        transform: scale(1.1);
        filter: drop-shadow(0 0 30px rgba(245, 158, 11, 0.6));
    }
    75% { 
        transform: scale(1.05);
        filter: drop-shadow(0 0 25px rgba(16, 185, 129, 0.6));
    }
    100% { 
        transform: scale(1); 
        filter: drop-shadow(0 0 20px rgba(0, 212, 255, 0.6));
    }
}
`;
document.head.appendChild(style);

// ===== JQUERY NAVIGATION SİSTEMİ (LİVEWİRE OLMADAN) =====
$(document).ready(function() {
    
    // Önceki adım buton handler
    $(document).on('click', '.btn-nav-previous', function() {
        const currentStep = $(this).data('current-step');
        const targetStep = $(this).data('target-step');
        
        // URL routing ile step değiştir (validation yok, sadece navigation)
        window.location.href = '{{ route("admin.ai.profile.edit", ["step" => 1]) }}'.replace('/1', '/' + targetStep);
    });
    
    // Sonraki adım buton handler
    $(document).on('click', '.btn-nav-next', function() {
        const currentStep = $(this).data('current-step');
        const targetStep = $(this).data('target-step');
        
        // Loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Kontrol ediliyor...');
        
        // Livewire ile validation + save çağır
        @this.call('saveAndNavigateNext').then(result => {
            // Success ise URL routing ile yönlendir
        }).catch(error => {
            // Button'u eski haline getir
            $(this).prop('disabled', false).html('Sonraki Adım <i class="fas fa-arrow-right ms-2"></i>');
        });
    });
    
    // Profil tamamlama buton handler (son adım)
    $(document).on('click', '.btn-complete-profile', function() {
        
        // Loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Aktifleştiriliyor...');
        
        // Livewire ile complete profile çağır
        @this.call('completeProfile').then(result => {
            // Profile completion success
        }).catch(error => {
            // Button'u eski haline getir
            $(this).prop('disabled', false).html('<i class="fas fa-magic me-2"></i>Yapay Zeka Asistanını Aktifleştir');
        });
    });
});

// Global navigation helper (diğer scriptlerden de çağrılabilir)
window.navigateToStep = function(stepNumber) {
    window.location.href = '{{ route("admin.ai.profile.edit", ["step" => 1]) }}'.replace('/1', '/' + stepNumber);
};

// Custom input field toggle functionality
$(document).on('change', 'input[type="radio"].custom-radio-trigger', function() {
    const customField = $(this).data('custom-field');
    const fieldKey = $(this).data('field');
    const value = $(this).val();
    
    if (customField && value === 'custom') {
        // Show custom input (escape dots in CSS selector)
        const containerSelector = '#' + fieldKey.replace(/\./g, '\\.') + '_custom_container';
        $(containerSelector).show();
        $(containerSelector + ' input').focus();
    } else if (customField) {
        // Hide custom input when other option selected (escape dots in CSS selector)
        const containerSelector = '#' + fieldKey.replace(/\./g, '\\.') + '_custom_container';
        $(containerSelector).hide();
        $(containerSelector + ' input').val('');
    }
});

// Hide custom inputs when other radio options are selected (for same field)
$(document).on('change', 'input[type="radio"].profile-field-input', function() {
    const fieldKey = $(this).data('field');
    const value = $(this).val();
    
    // If this is not a custom option, hide any custom container for this field
    if (value !== 'custom') {
        const containerSelector = '#' + fieldKey.replace(/\./g, '\\.') + '_custom_container';
        $(containerSelector).hide();
        $(containerSelector + ' input').val('');
    }
});

// Custom checkbox toggle functionality
$(document).on('change', 'input[type="checkbox"].custom-checkbox-trigger', function() {
    const customField = $(this).data('custom-field');
    const fieldKey = $(this).data('field');
    const value = $(this).val();
    const isChecked = $(this).is(':checked');
    
    if (customField && value === 'custom' && isChecked) {
        // Show custom input when "Diğer" checkbox is checked
        const containerSelector = '#' + fieldKey.replace(/\./g, '\\.') + '_custom_container';
        $(containerSelector).show();
        $(containerSelector + ' input').focus();
    } else if (customField && value === 'custom' && !isChecked) {
        // Hide custom input when "Diğer" checkbox is unchecked
        const containerSelector = '#' + fieldKey.replace(/\./g, '\\.') + '_custom_container';
        $(containerSelector).hide();
        $(containerSelector + ' input').val('');
    }
});

// Auto-select "Diğer" when user types in custom input (for radio fields)
$(document).on('input', 'input.profile-field-input[type="text"]', function() {
    const fieldValue = $(this).val().trim();
    const container = $(this).closest('[id$="_custom_container"]');
    
    if (container.length) {
        // Extract field name from container ID
        const fieldName = container.attr('id').replace('_custom_container', '');
        const customRadio = $('input[data-field="' + fieldName + '"][value="custom"]');
        
        if (fieldValue.length > 0) {
            // Auto-select "Diğer" when user types
            customRadio.prop('checked', true);
            container.show();
        } else {
            // Unselect "Diğer" when input is empty
            customRadio.prop('checked', false);
            container.hide();
        }
    }
});

// Founder questions visibility control
$(document).on('change', 'input[data-field="company_info.founder_permission"]', function() {
    const value = $(this).val();
    const founderSection = $('#founder-questions-section');
    
    if (value === 'yes_full' || value === 'yes_limited') {
        founderSection.show();
    } else {
        founderSection.hide();
    }
});

// Initialize founder questions visibility on page load
$(document).ready(function() {
    // Check founder permission value and show/hide founder section
    const checkedFounderPermission = $('input[data-field="company_info.founder_permission"]:checked');
    
    if (checkedFounderPermission.length) {
        const value = checkedFounderPermission.val();
        const founderSection = $('#founder-questions-section');
        
        if (value === 'yes_full' || value === 'yes_limited') {
            founderSection.show();
        } else {
            founderSection.hide();
        }
    }
    
    // Check for existing custom values and show containers
    $('input[value="custom"]').each(function() {
        const fieldKey = $(this).data('field');
        const isChecked = $(this).is(':checked');
        const containerSelector = '#' + fieldKey.replace(/\./g, '\\.') + '_custom_container';
        const customContainer = $(containerSelector);
        const customInput = customContainer.find('input[type="text"]');
        
        if (isChecked || (customInput.length && customInput.val().trim().length > 0)) {
            customContainer.show();
            if (customInput.val().trim().length > 0) {
                $(this).prop('checked', true);
            }
        }
    });
});

// ===== AI PROFILE WIZARD TEMA FIX =====
// Sayfa yüklendiğinde mevcut temayı uygula
document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const currentTheme = body.getAttribute('data-bs-theme');
    const wizardContainer = document.querySelector('.ai-profile-wizard-container');
    
    if (wizardContainer && currentTheme) {
        // Sayfaya girişte doğru tema uygulaması
        if (currentTheme === 'dark') {
            wizardContainer.classList.add('force-dark-mode');
        } else {
            wizardContainer.classList.add('force-light-mode');
        }
        
        // Force sınıfını kısa süre sonra kaldır
        setTimeout(() => {
            wizardContainer.classList.remove('force-light-mode', 'force-dark-mode');
        }, 100);
    }
});
</script>
@endpush