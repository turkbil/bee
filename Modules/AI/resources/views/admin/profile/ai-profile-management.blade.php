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
                                        {{-- Debug Info (sadece geliştirme için) --}}
                                        @if(config('app.debug'))
                                        <div class="alert alert-warning mb-3" style="font-size: 12px;">
                                            <strong>🔍 Debug Info:</strong><br>
                                            Profile ID: {{ $profile?->id ?? 'null' }}<br>
                                            Current Step: {{ $currentStep }}<br>
                                            Form Data Keys: {{ count($formData) }}<br>
                                            @if(isset($formData['brand_name']))
                                                Brand Name: "{{ $formData['brand_name'] }}"<br>
                                            @endif
                                            @if(isset($formData['city']))
                                                City: "{{ $formData['city'] }}"<br>
                                            @endif
                                            Sector: "{{ $formData['sector'] ?? 'null' }}"<br>
                                        </div>
                                        @endif
                                        
                                        <div class="step-info-container d-flex align-items-center gap-3">
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
                                                flex-shrink: 0;
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
                                                        @case(5) AI Davranış ve İletişim Ayarları @break
                                                    @endswitch
                                                </h1>
                                                <p class="hero-subtitle">
                                                    @switch($currentStep)
                                                        @case(1) Yapay zeka asistanınız için en uygun sektörü seçin @break
                                                        @case(2) İşletmenizin temel bilgilerini girin @break
                                                        @case(3) Markanızın kişiliğini tanımlayın @break
                                                        @case(4) Kurucu bilgilerini paylaşın (isteğe bağlı) @break
                                                        @case(5) AI asistanınızın iletişim tarzı ve davranış şeklini ayarlayın @break
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
                                            <x-progress-circle 
                                                :total-questions="$totalFields" 
                                                :answered-questions="$completedFields" 
                                                size="large" />
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
                                        5 => $question->question_key == 'response_style' ? 'ai_behavior_rules.' . $question->question_key : 'success_stories.' . $question->question_key,
                                        6 => 'ai_behavior_rules.' . $question->question_key,
                                        default => $question->question_key
                                    };
                                    
                                    // Step 4'te founder sorularını ana loop'ta gizle (share_founder_info hariç)
                                    $skipQuestion = false;
                                    if ($currentStep === 4 && $question->question_key !== 'share_founder_info' && str_contains($question->question_key, 'founder_')) {
                                        $skipQuestion = true;
                                    }
                                @endphp
                                
                                @if(!$skipQuestion)
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
                                            @if(in_array($question->question_key, ['sector_selection', 'sector']) && $currentStep === 1)
                                                {{-- Sektör Arama Kutusu --}}
                                                <div class="col-12 mb-4">
                                                    <div class="position-relative">
                                                        <input type="text" 
                                                               class="form-control form-control-lg" 
                                                               placeholder="Sektör ara... (örn: web tasarım, e-ticaret, muhasebe)" 
                                                               id="sectorSearch"
                                                               x-data="{ searchTerm: '' }"
                                                               x-model="searchTerm"
                                                               @input="filterSectors($event.target.value)"
                                                               style="padding-left: 50px; font-size: 16px; height: 60px; border-radius: 12px; border: 2px solid #e2e8f0; transition: all 0.3s ease;">
                                                        <div class="position-absolute" style="left: 18px; top: 50%; transform: translateY(-50%); color: #64748b;">
                                                            <i class="fas fa-search fs-4"></i>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted mt-2 d-block">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Sektörünüzü hızlıca bulmak için arama yapabilirsiniz
                                                    </small>
                                                </div>
                                                
                                                {{-- DEBUG: Sectors count = {{ $sectors->count() ?? 'NULL' }} --}}
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
                                                            <div class="col-6 col-md-4 col-lg-3" style="display: flex;">
                                                                <label class="form-imagecheck mb-2" style="width: 100%; display: flex;">
                                                                    <input type="radio" name="formData[{{ $fieldKey }}]" value="{{ $sector->code }}" 
                                                                           class="form-imagecheck-input profile-field-input" 
                                                                           data-field="{{ $fieldKey }}"
                                                                           x-data="{ isChecked: '{{ $formData[$fieldKey] ?? '' }}' === '{{ $sector->code }}' }"
                                                                           x-init="$el.checked = isChecked"
                                                                           @if(isset($formData[$fieldKey]) && $formData[$fieldKey] == $sector->code) checked @endif
                                                                           @if($question->is_required) required @endif>
                                                                    <span class="form-imagecheck-figure" style="width: 100%; display: flex;">
                                                                        <div class="form-imagecheck-image sector-card d-flex flex-column" style="min-height: 140px; height: 140px; width: 100%; flex: 1;">
                                                                            <div class="sector-icon mb-2" style="min-height: 40px; display: flex; align-items: center; justify-content: center;">
                                                                                @if($sector->emoji)
                                                                                    <span class="sector-emoji" style="font-size: 24px;">{{ $sector->emoji }}</span>
                                                                                @elseif($sector->icon)
                                                                                    <i class="{{ $sector->icon }}" style="font-size: 24px;"></i>
                                                                                @else
                                                                                    <i class="fas fa-briefcase" style="font-size: 24px;"></i>
                                                                                @endif
                                                                            </div>
                                                                            <div class="sector-name text-center fw-bold mb-2" style="font-size: 13px; line-height: 1.2; min-height: 32px; display: flex; align-items: center; justify-content: center;">
                                                                                {{ $sector->name }}
                                                                            </div>
                                                                            <div class="sector-desc text-center text-muted flex-grow-1" style="font-size: 11px; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                                                {{ $sector->description ?? '' }}
                                                                            </div>
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
                                                    @foreach(is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []) as $option)
                                                        @php
                                                            $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                                                            $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                                                        @endphp
                                                        <option value="{{ $optionValue }}" 
                                                                @if(isset($formData[$fieldKey]) && $formData[$fieldKey] == $optionValue) selected @endif>
                                                            {{ $optionLabel }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @endif
                                            @break
                                            
                                        @case('select_with_custom')
                                            <input type="text" 
                                                   class="form-control profile-field-input" 
                                                   name="formData[{{ $fieldKey }}]"
                                                   data-field="{{ $fieldKey }}"
                                                   x-data="{ fieldValue: '{{ addslashes($formData[$fieldKey] ?? '') }}' }"
                                                   x-init="$el.value = fieldValue"
                                                   placeholder="{{ $question->input_placeholder ?? 'Örn: 2020 yılından beri, 15+ yıllık deneyim, aile işi vb.' }}"
                                                   @if($question->is_required) required @endif>
                                            @break
                                            
                                        @case('radio')
                                            @php
                                                // Özel durumlar için farklı col class'lar
                                                if ($question->question_key === 'share_founder_info') {
                                                    // Kurucu paylaşım sorusu için col-6 (Evet/Hayır)
                                                    $questionColClass = 'col-6';
                                                } else {
                                                    // Diğer radio sorular için mevcut logic
                                                    $maxLength = 0;
                                                    foreach(is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []) as $opt) {
                                                        $maxLength = max($maxLength, strlen($opt['label'] ?? ''));
                                                    }
                                                    
                                                    if ($maxLength > 40) {
                                                        $questionColClass = 'col-12';
                                                    } elseif ($maxLength > 25) {
                                                        $questionColClass = 'col-md-6 col-12';
                                                    } else {
                                                        $questionColClass = 'col-md-6 col-12';
                                                    }
                                                }
                                            @endphp
                                            <div class="row">
                                                @if($question->options)
                                                    @php
                                                        $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []);
                                                        // "diger" key'ini en sona koy
                                                        if (isset($options['diger'])) {
                                                            $digerOption = $options['diger'];
                                                            unset($options['diger']);
                                                            $options['diger'] = $digerOption;
                                                        }
                                                    @endphp
                                                    @foreach($options as $optionKey => $option)
                                                        @php
                                                            $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                                                            $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                                                            $optionIcon = is_array($option) ? ($option['icon'] ?? '') : '';
                                                            $optionDescription = is_array($option) ? ($option['description'] ?? '') : '';
                                                            $hasCustomInput = (is_array($option) && !empty($option['has_custom_input'])) || 
                                                                            (strpos($optionLabel, 'Diğer') !== false || strpos($optionValue, 'diger') !== false);
                                                            
                                                            // Default value logic - share_founder_info için "hayir" default
                                                            if ($question->question_key === 'share_founder_info') {
                                                                $isChecked = isset($formData[$fieldKey]) ? 
                                                                    ($formData[$fieldKey] == $optionValue) : 
                                                                    ($optionValue === 'hayir'); // Default "hayir"
                                                            } else {
                                                                $isChecked = isset($formData[$fieldKey]) && $formData[$fieldKey] == $optionValue;
                                                            }
                                                        @endphp
                                                        <div class="{{ $questionColClass }} mb-2">
                                                            <label class="form-selectgroup-item flex-fill">
                                                                <input type="radio" name="formData[{{ $fieldKey }}]" value="{{ $optionValue }}" 
                                                                       class="form-selectgroup-input profile-field-input @if($hasCustomInput) custom-radio-trigger @endif @if($question->question_key === 'share_founder_info') founder-radio @endif" 
                                                                       data-field="{{ $fieldKey }}"
                                                                       @if($hasCustomInput) data-custom-field="{{ $fieldKey }}_custom" @endif
                                                                       x-data="{ isChecked: {{ $isChecked ? 'true' : 'false' }} }"
                                                                       x-init="$el.checked = isChecked"
                                                                       @if($isChecked) checked @endif
                                                                       @if($question->is_required) required @endif>
                                                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                                    <div class="me-3">
                                                                        <span class="form-selectgroup-check"></span>
                                                                    </div>
                                                                    <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                        @if($optionIcon)
                                                                            <i class="{{ $optionIcon }} me-3 text-muted"></i>
                                                                        @else
                                                                            <i class="fas fa-dot-circle me-3 text-muted"></i>
                                                                        @endif
                                                                        <div style="text-align: left; width: 100%;">
                                                                            <div class="font-weight-medium">{{ $optionLabel }}</div>
                                                                            @if($optionDescription)
                                                                                <div class="text-secondary small">{{ $optionDescription }}</div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    
                                                    {{-- Custom Input Field (Hidden by default) --}}
                                                    @foreach(is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []) as $option)
                                                        @if(is_array($option) && !empty($option['has_custom_input']))
                                                            <div class="col-12 mt-3" id="{{ $fieldKey }}_custom_container" 
                                                                 style="display: none;">
                                                                <input type="text" 
                                                                       class="form-control profile-field-input" 
                                                                       name="formData[{{ $fieldKey }}_custom]"
                                                                       data-field="{{ $fieldKey }}_custom"
                                                                       placeholder="{{ ($option['custom_placeholder'] ?? '') ?? 'Özel bilginizi giriniz...' }}"
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
                                                // Step 5 checkbox alanları için özel col ayarı
                                                $step5CheckboxFields = ['brand_personality', 'ai_response_style', 'sales_approach', 'brand_character', 'writing_style'];
                                                
                                                if ($currentStep === 5 && in_array($question->question_key, $step5CheckboxFields)) {
                                                    // Step 5 AI davranış sorularında col-6 kullan
                                                    $questionColClass = 'col-6';
                                                } else {
                                                    // Diğer checkbox'larda mevcut logic
                                                    $maxLength = 0;
                                                    foreach(is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []) as $opt) {
                                                        $maxLength = max($maxLength, strlen($opt['label'] ?? ''));
                                                    }
                                                    
                                                    if ($maxLength > 40) {
                                                        $questionColClass = 'col-12';
                                                    } elseif ($maxLength > 25) {
                                                        $questionColClass = 'col-md-6 col-12';
                                                    } else {
                                                        $questionColClass = 'col-md-6 col-12';
                                                    }
                                                }
                                            @endphp
                                            <div class="row">
                                                @if($question->options)
                                                    @php
                                                        $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []);
                                                        // "diger" key'ini en sona koy
                                                        if (isset($options['diger'])) {
                                                            $digerOption = $options['diger'];
                                                            unset($options['diger']);
                                                            $options['diger'] = $digerOption;
                                                        }
                                                    @endphp
                                                    @foreach($options as $optionKey => $option)
                                                        @php
                                                            $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                                                            $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                                                            $optionIcon = is_array($option) ? ($option['icon'] ?? '') : '';
                                                            $optionDescription = is_array($option) ? ($option['description'] ?? '') : '';
                                                            $hasCustomInput = (is_array($option) && !empty($option['has_custom_input'])) || 
                                                                            (strpos($optionLabel, 'Diğer') !== false || strpos($optionValue, 'diger') !== false);
                                                            $nestedFieldKey = $fieldKey . '.' . $optionValue;
                                                            $isChecked = isset($formData[$nestedFieldKey]) && $formData[$nestedFieldKey];
                                                        @endphp
                                                        <div class="{{ $questionColClass }} mb-2">
                                                            <label class="form-selectgroup-item flex-fill">
                                                                <input type="checkbox" value="{{ $optionValue }}" 
                                                                       class="form-selectgroup-input profile-field-checkbox @if($hasCustomInput) custom-checkbox-trigger @endif" 
                                                                       name="formData[{{ $fieldKey }}][]"
                                                                       data-field="{{ $fieldKey }}"
                                                                       @if($hasCustomInput) data-custom-field="{{ $fieldKey }}_custom" @endif
                                                                       x-data="{ isChecked: {{ $isChecked ? 'true' : 'false' }} }"
                                                                       x-init="$el.checked = isChecked"
                                                                       @if($isChecked) checked @endif>
                                                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                                    <div class="me-3">
                                                                        <span class="form-selectgroup-check"></span>
                                                                    </div>
                                                                    <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                        @if($optionIcon)
                                                                            <i class="{{ $optionIcon }} me-3 text-muted"></i>
                                                                        @else
                                                                            <i class="fas fa-check me-3 text-muted"></i>
                                                                        @endif
                                                                        <div style="text-align: left; width: 100%;">
                                                                            <div class="font-weight-medium">{{ $optionLabel }}</div>
                                                                            @if($optionDescription)
                                                                                <div class="text-secondary small">{{ $optionDescription }}</div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    
                                                    {{-- Custom Input Field for Checkbox (Hidden by default) --}}
                                                    @foreach(is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []) as $option)
                                                        @if(is_array($option) && !empty($option['has_custom_input']))
                                                            <div class="col-12 mt-3" id="{{ $fieldKey }}_custom_container" 
                                                                 style="display: none;">
                                                                <input type="text" 
                                                                       class="form-control profile-field-input" 
                                                                       name="formData[{{ $fieldKey }}_custom]"
                                                                       data-field="{{ $fieldKey }}_custom"
                                                                       placeholder="{{ ($option['custom_placeholder'] ?? '') ?? 'Özel bilginizi giriniz...' }}"
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
                                @endif
                                
                            @endforeach
                            
                            {{-- Founder Questions (always rendered, visibility controlled) --}}
                            @if($currentStep === 4)
                                <div class="founder-section" id="founder-questions-section" style="display: {{ $showFounderQuestions ? 'block' : 'none' }}; transition: opacity 0.2s ease;">
                                    <h5 class="mb-3">Kurucu Bilgileri</h5>
                                    @php
                                        // Component'in zaten yüklediği sorulardan founder sorularını filtrele (share_founder_info hariç)
                                        $founderQuestions = $questions->filter(function($question) {
                                            return $question->question_key !== 'share_founder_info' && str_contains($question->question_key, 'founder_');
                                        });
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
                                                        // Founder section'da da "Diğer" hariç radyo sorular için col-6 kullan
                                                        $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []);
                                                        $optionCount = count($options);
                                                        
                                                        // "Kurucu & Sahip", "Genel Müdür" gibi kısa seçenekler varsa col-6
                                                        if ($optionCount <= 3) {
                                                            $questionColClass = 'col-6';
                                                        } else {
                                                            // Diğer durumlar için mevcut logic
                                                            $maxLength = 0;
                                                            foreach($options as $opt) {
                                                                $maxLength = max($maxLength, strlen($opt['label'] ?? ''));
                                                            }
                                                            
                                                            if ($maxLength > 40) {
                                                                $questionColClass = 'col-12';
                                                            } else {
                                                                $questionColClass = 'col-md-6 col-12';
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="row">
                                                        @if($question->options)
                                                            @php
                                                                // Options array'ini al ve "diger" key'ini en sona taşı
                                                                $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []);
                                                                if (isset($options['diger'])) {
                                                                    $digerOption = $options['diger'];
                                                                    unset($options['diger']);
                                                                    $options['diger'] = $digerOption;
                                                                }
                                                            @endphp
                                                            @foreach($options as $option)
                                                                @php
                                                                    $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                                                                    $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                                                                    $optionIcon = is_array($option) ? ($option['icon'] ?? '') : '';
                                                                    $optionDescription = is_array($option) ? ($option['description'] ?? '') : '';
                                                                    $hasCustomInput = is_array($option) && !empty($option['has_custom_input']);
                                                                @endphp
                                                                <div class="{{ $questionColClass }} mb-2">
                                                                    <label class="form-selectgroup-item flex-fill">
                                                                        <input type="radio" name="formData[{{ $fieldKey }}]" value="{{ $optionValue }}" 
                                                                               class="form-selectgroup-input profile-field-input @if($hasCustomInput) custom-radio-trigger @endif" 
                                                                               data-field="{{ $fieldKey }}"
                                                                               @if($hasCustomInput) data-custom-field="{{ $fieldKey }}_custom" @endif
                                                                               x-data="{ isChecked: '{{ $formData[$fieldKey] ?? '' }}' === '{{ $optionValue }}' }"
                                                                               x-init="$el.checked = isChecked"
                                                                               @if(isset($formData[$fieldKey]) && $formData[$fieldKey] == $optionValue) checked @endif>
                                                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                                            <div class="me-3">
                                                                                <span class="form-selectgroup-check"></span>
                                                                            </div>
                                                                            <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                                @if($optionIcon)
                                                                                    <i class="{{ $optionIcon }} me-3 text-muted"></i>
                                                                                @else
                                                                                    <i class="fas fa-dot-circle me-3 text-muted"></i>
                                                                                @endif
                                                                                <div>
                                                                                    <div class="font-weight-medium">{{ $optionLabel }}</div>
                                                                                    @if($optionDescription)
                                                                                        <div class="text-secondary small">{{ $optionDescription }}</div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                            
                                                            {{-- Custom Input Field for Radio (Hidden by default) --}}
                                                            @foreach(is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []) as $option)
                                                                @if(is_array($option) && !empty($option['has_custom_input']))
                                                                    <div class="col-12 mt-3" id="{{ $fieldKey }}_custom_container" 
                                                                         style="display: none;">
                                                                        <input type="text" 
                                                                               class="form-control profile-field-input" 
                                                                               name="formData[{{ $fieldKey }}_custom]"
                                                                               data-field="{{ $fieldKey }}_custom"
                                                                               placeholder="{{ ($option['custom_placeholder'] ?? '') ?? 'Özel bilginizi giriniz...' }}"
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
                                                        // Step 5 checkbox alanları için özel col ayarı
                                                        $step5CheckboxFields = ['brand_personality', 'ai_response_style', 'sales_approach', 'brand_character', 'writing_style'];
                                                        
                                                        if ($currentStep === 5 && in_array($question->question_key, $step5CheckboxFields)) {
                                                            // Step 5 AI davranış sorularında col-6 kullan
                                                            $questionColClass = 'col-6';
                                                        } else {
                                                            // Diğer checkbox'larda mevcut logic
                                                            $maxLength = 0;
                                                            foreach(is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []) as $opt) {
                                                                $maxLength = max($maxLength, strlen($opt['label'] ?? ''));
                                                            }
                                                            
                                                            if ($maxLength > 40) {
                                                                $questionColClass = 'col-12';
                                                            } elseif ($maxLength > 25) {
                                                                $questionColClass = 'col-md-6 col-12';
                                                            } else {
                                                                $questionColClass = 'col-md-6 col-12';
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="row">
                                                        @if($question->options)
                                                            @php
                                                                // Options array'ini al ve "diger" key'ini en sona taşı
                                                                $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []);
                                                                if (isset($options['diger'])) {
                                                                    $digerOption = $options['diger'];
                                                                    unset($options['diger']);
                                                                    $options['diger'] = $digerOption;
                                                                }
                                                            @endphp
                                                            @foreach($options as $option)
                                                                @php
                                                                    $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                                                                    $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                                                                    $optionIcon = is_array($option) ? ($option['icon'] ?? '') : '';
                                                                    $optionDescription = is_array($option) ? ($option['description'] ?? '') : '';
                                                                    $hasCustomInput = is_array($option) && !empty($option['has_custom_input']);
                                                                    $nestedFieldKey = $fieldKey . '.' . $optionValue;
                                                                    $isChecked = isset($formData[$nestedFieldKey]) && $formData[$nestedFieldKey];
                                                                @endphp
                                                                <div class="{{ $questionColClass }} mb-2">
                                                                    <label class="form-selectgroup-item flex-fill">
                                                                        <input type="checkbox" value="{{ $optionValue }}" 
                                                                               class="form-selectgroup-input profile-field-checkbox @if($hasCustomInput) custom-checkbox-trigger @endif" 
                                                                               name="formData[{{ $fieldKey }}][]"
                                                                               data-field="{{ $fieldKey }}"
                                                                               @if($hasCustomInput) data-custom-field="{{ $fieldKey }}_custom" @endif
                                                                               x-data="{ isChecked: {{ $isChecked ? 'true' : 'false' }} }"
                                                                               x-init="$el.checked = isChecked"
                                                                               @if($isChecked) checked @endif>
                                                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                                            <div class="me-3">
                                                                                <span class="form-selectgroup-check"></span>
                                                                            </div>
                                                                            <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                                @if($optionIcon)
                                                                                    <i class="{{ $optionIcon }} me-3 text-muted"></i>
                                                                                @else
                                                                                    <i class="fas fa-check me-3 text-muted"></i>
                                                                                @endif
                                                                                <div>
                                                                                    <div class="font-weight-medium">{{ $optionLabel }}</div>
                                                                                    @if($optionDescription)
                                                                                        <div class="text-secondary small">{{ $optionDescription }}</div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                            
                                                            {{-- Custom Input Field for Checkbox (Hidden by default) --}}
                                                            @foreach(is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []) as $option)
                                                                @if(is_array($option) && !empty($option['has_custom_input']))
                                                                    <div class="col-12 mt-3" id="{{ $fieldKey }}_custom_container" 
                                                                         style="display: none;">
                                                                        <input type="text" 
                                                                               class="form-control profile-field-input" 
                                                                               name="formData[{{ $fieldKey }}_custom]"
                                                                               data-field="{{ $fieldKey }}_custom"
                                                                               placeholder="{{ ($option['custom_placeholder'] ?? '') ?? 'Özel bilginizi giriniz...' }}"
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
                                                           value="{{ $formData[$fieldKey] ?? '' }}"
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

// Initialize founder questions visibility (not needed anymore - handled by Livewire)
function initializeFounderQuestions() {
    // No longer needed - Livewire handles visibility
}

// Force reload form values after Livewire hydration
document.addEventListener('livewire:load', function () {
    initializeFormFields();
    initializeFounderQuestions();
});

document.addEventListener('livewire:update', function () {
    setTimeout(function() {
        initializeFormFields();
        initializeFounderQuestions();
    }, 100); // Kısa delay ile
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
document.addEventListener('DOMContentLoaded', function() {
    initializeFormFields();
    initializeFounderQuestions();
});

// ===== SEKTÖR ARAMA FONKSİYONU =====
function filterSectors(searchTerm) {
    const term = searchTerm.toLowerCase().trim();
    const sectorCards = document.querySelectorAll('.sector-card');
    const categoryBands = document.querySelectorAll('.category-header-band');
    
    if (term === '') {
        // Arama boşsa tüm kartları göster
        sectorCards.forEach(card => {
            card.closest('.col-6, .col-md-4, .col-lg-3').style.display = 'block';
        });
        categoryBands.forEach(band => {
            band.closest('.col-12').style.display = 'block';
        });
        return;
    }
    
    let visibleCategoriesCount = {};
    
    // Sektör kartlarını filtrele
    sectorCards.forEach(card => {
        const sectorName = card.querySelector('.sector-name').textContent.toLowerCase();
        const sectorDesc = card.querySelector('.sector-desc').textContent.toLowerCase();
        const cardContainer = card.closest('.col-6, .col-md-4, .col-lg-3');
        const categoryContainer = cardContainer.closest('.row').previousElementSibling;
        const categoryTitle = categoryContainer.querySelector('h5').textContent;
        
        // Arama terimi sektör adında veya açıklamasında var mı?
        if (sectorName.includes(term) || sectorDesc.includes(term)) {
            cardContainer.style.display = 'block';
            visibleCategoriesCount[categoryTitle] = (visibleCategoriesCount[categoryTitle] || 0) + 1;
        } else {
            cardContainer.style.display = 'none';
        }
    });
    
    // Kategorileri gizle/göster (görünür sektörü olmayan kategoriler gizlenir)
    categoryBands.forEach(band => {
        const categoryTitle = band.querySelector('h5').textContent;
        const categoryContainer = band.closest('.col-12');
        
        if (visibleCategoriesCount[categoryTitle] > 0) {
            categoryContainer.style.display = 'block';
        } else {
            categoryContainer.style.display = 'none';
        }
    });
}

// ===== JQUERY AUTO-SAVE SİSTEMİ (LİVEWİRE'DAN BAĞIMSIZ) =====
$(document).ready(function() {
    let autoSaveTimeout;
    let isRequestInProgress = false;
    let pendingRequests = new Map(); // field -> timeout mapping
    
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
    
    // STEP 5 (AI BEHAVIOR) ÖZEL HANDLER - RADIO + CHECKBOX
    $(document).on('change', 'input[data-field*="ai_behavior_rules"], input[data-field="success_stories.ai_response_style"], input[data-field="ai_behavior_rules.response_style"]', function(event) {
        const fieldName = $(this).data('field');
        if (!fieldName) return;
        
        const fieldValue = getFieldValue($(this));
        
        // AI davranış ayarları için instant save
        saveFieldData(fieldName, fieldValue);
    });
    
    // FOUNDER QUESTIONS VISIBILITY HANDLER
    $(document).on('change', 'input[data-field="company_info.share_founder_info"]', function(event) {
        const fieldName = $(this).data('field');
        const fieldValue = $(this).val();
        
        // INSTANT CSS display toggle (no layout space when hidden)
        const founderSection = $('#founder-questions-section');
        if (fieldValue === 'evet') {
            founderSection.css('display', 'block');
        } else {
            founderSection.css('display', 'none');
            
            // Kurucu bilgileri formlarını temizle (client-side immediate clear)
            founderSection.find('input, textarea, select').each(function() {
                const $field = $(this);
                if ($field.is(':checkbox') || $field.is(':radio')) {
                    $field.prop('checked', false);
                } else {
                    $field.val('');
                }
                
                // Custom container'ları da gizle
                const fieldKey = $field.data('field');
                if (fieldKey) {
                    const containerSelector = '#' + fieldKey.replace(/\./g, '\\.') + '_custom_container';
                    $(containerSelector).hide();
                }
            });
        }
        
        // Auto-save field first
        saveFieldData(fieldName, fieldValue);
        
        // Call Livewire method to toggle founder questions (background sync)
        if (window.Livewire) {
            Livewire.dispatch('toggleFounderQuestions', { value: fieldValue });
        }
    });
    
    // DİĞER ALANLAR İÇİN GENERIC HANDLER (TEXT, TEXTAREA ETC)
    $(document).on('change', 'input[data-field]:not([data-field*="sector_details"]):not([data-field*="success_stories"]):not([data-field*="founder_info"]):not([data-field*="ai_behavior_rules"]):not([data-field="success_stories.ai_response_style"]), select[data-field], textarea[data-field]', function(event) {
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
        // Cancel any pending request for this field
        if (pendingRequests.has(fieldName)) {
            clearTimeout(pendingRequests.get(fieldName));
        }
        
        // Debounce: wait 300ms before sending request
        const timeoutId = setTimeout(() => {
            pendingRequests.delete(fieldName);
            
            // Skip if another request is in progress
            if (isRequestInProgress) {
                console.log('🚫 Request skipped - another in progress');
                return;
            }
            
            isRequestInProgress = true;
            
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
                    console.log('❌ AJAX Error:', status, error);
                },
                complete: function() {
                    isRequestInProgress = false;
                }
            });
        }, 300);
        
        pendingRequests.set(fieldName, timeoutId);
    }
});

// CSS için field-saved class ve animasyonlar
const style = document.createElement('style');
style.textContent = `
.field-saved {
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.3) !important;
    transition: box-shadow 0.3s ease;
}

.disabled-select {
    opacity: 0.5 !important;
    pointer-events: none !important;
    background-color: #f8f9fa !important;
    cursor: not-allowed !important;
}

/* Experience years field styling - simple text input */

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
            // Success - redirect already handled by Livewire method
            console.log('Navigation successful:', result);
        }).catch(error => {
            console.error('Navigation error:', error);
            // Button'u eski haline getir
            $(this).prop('disabled', false).html('Sonraki Adım <i class="fas fa-arrow-right ms-2"></i>');
            
            // Hata mesajını göster
            if (error && error.message) {
                alert('Geçiş sırasında hata: ' + error.message);
            }
        });
    });
    
    // Profil tamamlama buton handler (son adım)
    $(document).on('click', '.btn-complete-profile', function() {
        
        // Loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Aktifleştiriliyor...');
        
        // Livewire ile complete profile çağır
        @this.call('completeProfile').then(result => {
            // Profile completion success - redirect handled by Livewire
            console.log('Profile completion successful:', result);
        }).catch(error => {
            console.error('Profile completion error:', error);
            // Button'u eski haline getir
            $(this).prop('disabled', false).html('<i class="fas fa-magic me-2"></i>Yapay Zeka Asistanını Aktifleştir');
            
            // Hata mesajını göster
            if (error && error.message) {
                alert('Profil aktifleştirme sırasında hata: ' + error.message);
            }
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
    
    // Check if this is a "Diğer" option
    const isDigerOption = value.includes('Diğer') || value.includes('diger') || value === 'custom' || value === 'diger';
    
    if (customField && isDigerOption) {
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
    
    // Check if this is a "Diğer" option
    const isDigerOption = value.includes('Diğer') || value.includes('diger') || value === 'custom' || value === 'diger';
    
    // If this is not a custom option, hide any custom container for this field
    if (!isDigerOption) {
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
    
    // Check if this is a "Diğer" option
    const isDigerOption = value.includes('Diğer') || value.includes('diger') || value === 'custom' || value === 'diger';
    
    if (customField && isDigerOption && isChecked) {
        // Show custom input when "Diğer" checkbox is checked
        const containerSelector = '#' + fieldKey.replace(/\./g, '\\.') + '_custom_container';
        $(containerSelector).show();
        $(containerSelector + ' input').focus();
    } else if (customField && isDigerOption && !isChecked) {
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
    
    if (value === 'Evet, bilgilerimi paylaşmak istiyorum') {
        founderSection.show();
    } else {
        founderSection.hide();
    }
});

// Initialize founder questions visibility on page load
$(document).ready(function() {
    // Check share_founder_info value and show/hide founder section
    const checkedFounderInfo = $('input[data-field="company_info.share_founder_info"]:checked');
    
    if (checkedFounderInfo.length) {
        const value = checkedFounderInfo.val();
        const founderSection = $('#founder-questions-section');
        
        if (value === 'evet') {
            founderSection.css('display', 'block');
        } else {
            founderSection.css('display', 'none');
        }
    } else {
        // No selection yet, hide by default
        $('#founder-questions-section').css('display', 'none');
    }
    
    // Check for existing custom values and show containers
    $('input[value="diger"], input[value="custom"]').each(function() {
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

// ===== EXPERIENCE YEARS FIELD - SIMPLE TEXT INPUT =====
// Artık sadece text input olduğu için Choices.js kod bloğu kaldırıldı

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