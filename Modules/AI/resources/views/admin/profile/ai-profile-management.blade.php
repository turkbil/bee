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
                <div class="card-body px-4 py-3">
                    {{-- Form Content --}}
                    <form id="ai-profile-form" method="POST">
                        <div class="form-content">
                            
                            @foreach($questions as $question)
                                @php
                                    $fieldKey = match($currentStep) {
                                        1 => $question->question_key,
                                        2 => 'company_info.' . $question->question_key,
                                        3 => 'sector_details.' . $question->question_key,
                                        4 => 'company_info.' . $question->question_key,
                                        5 => in_array($question->question_key, ['brand_character', 'response_style']) ? 'ai_behavior_rules.' . $question->question_key : 'success_stories.' . $question->question_key,
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
                                    @if($currentStep === 1)
                                        {{-- Step 1 için ortalanmış başlık --}}
                                        <div class="text-center mb-2">
                                            <label class="form-label fw-bold fs-5 mb-1 d-block">
                                                {{ $question->question_text }}
                                                @if($question->is_required)
                                                    <span class="text-danger ms-1">*</span>
                                                @endif
                                            </label>
                                        </div>
                                    @else
                                        {{-- Diğer step'ler için normal başlık --}}
                                        <label class="form-label fw-bold fs-5 mb-3 d-block">
                                            {{ $question->question_text }}
                                            @if($question->is_required)
                                                <span class="text-danger ms-1">*</span>
                                            @endif
                                        </label>
                                    @endif
                                    
                                    @if($question->help_text)
                                        @if($currentStep === 1)
                                            <div class="text-center mb-3">
                                                <div class="form-hint">{{ $question->help_text }}</div>
                                            </div>
                                        @else
                                            <div class="form-hint">{{ $question->help_text }}</div>
                                        @endif
                                    @endif
                                    
                                    {{-- Input Based on Type --}}
                                    @switch($question->input_type)
                                        
                                        @case('select')
                                            {{-- Special handling for sector selection with categorized grid --}}
                                            @if(in_array($question->question_key, ['sector_selection', 'sector']) && $currentStep === 1)
                                                {{-- Premium Arama Interface - Daha Büyük ve Şık --}}
                                                <div class="premium-search-container text-center mb-6" id="searchContainer">
                                                    <div class="row justify-content-center">
                                                        <div class="col-lg-10 col-xl-8">
                                                            {{-- Premium Ana Arama Kutusu --}}
                                                            <div class="position-relative premium-search-box">
                                                                <input type="text" 
                                                                       class="form-control" 
                                                                       placeholder="Sektörünüzü arayın..." 
                                                                       id="sectorSearch"
                                                                       style="
                                                                           height: clamp(68px, 10vw, 88px); 
                                                                           font-size: clamp(20px, 3.5vw, 28px); 
                                                                           padding: 0 clamp(120px, 18vw, 140px) 0 clamp(32px, 5vw, 40px); 
                                                                           border: 3px solid var(--tblr-primary) !important; 
                                                                           border-radius: clamp(34px, 6vw, 44px); 
                                                                           box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 4px 16px rgba(0,0,0,0.04); 
                                                                           font-weight: 500; 
                                                                           letter-spacing: 0.3px; 
                                                                           transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                                                                       "
                                                                       onfocus="this.style.fontSize='clamp(24px, 4.5vw, 34px)'; this.style.boxShadow='0 12px 40px rgba(var(--tblr-primary-rgb), 0.15), 0 6px 20px rgba(var(--tblr-primary-rgb), 0.08)'; this.style.transform='translateY(-2px)'"
                                                                       onblur="this.style.fontSize='clamp(20px, 3.5vw, 28px)'; this.style.boxShadow='0 8px 32px rgba(0,0,0,0.08), 0 4px 16px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'">
                                                                       
                                                                {{-- Arama İkonu --}}
                                                                <div class="position-absolute" style="right: clamp(60px, 9vw, 75px); top: 50%; transform: translateY(-50%); color: var(--tblr-primary);">
                                                                    <i class="fas fa-search" style="font-size: clamp(16px, 2vw, 20px);"></i>
                                                                </div>
                                                                
                                                                {{-- Temizleme Butonu --}}
                                                                <button type="button" 
                                                                        class="btn position-absolute" 
                                                                        id="clearSearchBtn"
                                                                        style="
                                                                            right: clamp(15px, 2vw, 20px); 
                                                                            top: 50%; 
                                                                            transform: translateY(-50%); 
                                                                            border: none; 
                                                                            background: none; 
                                                                            color: var(--tblr-secondary); 
                                                                            padding: clamp(8px, 1.5vw, 10px); 
                                                                            border-radius: 50%; 
                                                                            transition: all 0.3s ease;
                                                                            width: clamp(32px, 4vw, 38px);
                                                                            height: clamp(32px, 4vw, 38px);
                                                                            display: flex;
                                                                            align-items: center;
                                                                            justify-content: center;
                                                                        "
                                                                        onmouseover="this.style.backgroundColor='var(--tblr-bg-surface-secondary)'; this.style.color='var(--tblr-body-color)'"
                                                                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--tblr-secondary)'"
                                                                        title="Temizle">
                                                                    <i class="fas fa-times" style="font-size: clamp(14px, 1.8vw, 16px);"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- SEÇİLİ KATEGORİ BÖLÜMÜ (BAĞIMSIZ & SİMETRİK) --}}
                                                <div class="selected-sector-section mb-5" id="selectedSectorSection" style="display: none;">
                                                    <div class="row justify-content-center">
                                                        <div class="col-lg-8 col-xl-6">
                                                            <div class="card" style="background: var(--tblr-bg-surface); box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 4px 16px rgba(0,0,0,0.04); border-radius: 16px; border: 3px solid var(--tblr-muted) !important;">
                                                                <div class="card-body p-4">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        {{-- Sol: İkon + Bilgiler --}}
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="me-4">
                                                                                <div class="bg-muted text-white rounded-circle d-flex align-items-center justify-content-center position-relative" 
                                                                                     style="width: 56px; height: 56px; box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);">
                                                                                    <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                                                                                    {{-- Muted pulse animation --}}
                                                                                    <div class="position-absolute" style="width: 56px; height: 56px; border: 2px solid rgba(108, 117, 125, 0.4); border-radius: 50%; animation: pulse-muted 3s infinite;"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <h6 class="mb-2 text-muted fw-bold" style="font-size: 15px; letter-spacing: 0.5px;">
                                                                                    SEÇİLİ KATEGORİ
                                                                                </h6>
                                                                                <div class="d-flex align-items-center mb-1">
                                                                                    <span id="selectedSectorName" class="fw-medium" style="font-size: 16px; color: var(--tblr-body-color);"></span>
                                                                                </div>
                                                                                <small id="selectedSectorDesc" class="d-block" style="line-height: 1.4; max-width: 300px; color: var(--tblr-body-color); opacity: 0.7;"></small>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        {{-- Sağ: Değiştir Butonu --}}
                                                                        <div class="ms-3">
                                                                            <button type="button" 
                                                                                    class="btn btn-outline-muted px-4 py-2" 
                                                                                    id="changeSectorBtn"
                                                                                    style="border-radius: 25px; font-weight: 500; border-width: 2px; transition: all 0.3s ease;">
                                                                                <i class="fas fa-edit me-2"></i>
                                                                                <span class="d-none d-sm-inline">Değiştir</span>
                                                                                <span class="d-sm-none">Değiştir</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Kategori Listesi (Arama Sonuçları) --}}
                                                <div class="sectors-grid" id="sectorsGrid" style="display: none;">
                                                
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
                                                                        <div class="form-imagecheck-image sector-card d-flex flex-column" 
                                                                             style="min-height: 140px; height: 140px; width: 100%; flex: 1;"
                                                                             data-sector="{{ $sector->code }}"
                                                                             data-sector-name="{{ $sector->name }}"
                                                                             data-sector-desc="{{ $sector->description ?? '' }}"
                                                                             data-sector-icon="@if($sector->emoji){{ $sector->emoji }}@elseif($sector->icon){{ $sector->icon }}@else fas fa-briefcase @endif"
                                                                             data-sector-icon-type="@if($sector->emoji)emoji@elseif($sector->icon)icon@else icon @endif">
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
                                                                            <div class="sector-desc text-center flex-grow-1" style="font-size: 11px; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                                                {{ $sector->description ?? '' }}
                                                                            </div>
                                                                        </div>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                                </div> {{-- sectors-grid kapatma tag'i --}}
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
                                    {{-- DEBUG INFO --}}
                                    @php
                                        // Component'in zaten yüklediği sorulardan founder sorularını filtrele (section = 'founder_info')
                                        $founderQuestions = $questions->filter(function($question) {
                                            return $question->section === 'founder_info';
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
                        
                        {{-- Navigation Buttons + Toggle Categories - SİMETRİK LAYOUT --}}
                        <div class="form-footer mt-2 pt-2">
                            <div class="row align-items-center g-3">
                                {{-- Sol taraf: Kategorileri Göster + Önceki Adım --}}
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <div class="d-flex gap-3 flex-wrap justify-content-md-start justify-content-center">
                                        {{-- Kategorileri Göster Butonu (Sadece Adım 1'de) --}}
                                        @if($currentStep === 1)
                                            <button type="button" 
                                                    class="btn btn-outline-primary px-5 py-3"
                                                    id="toggleCategoriesBtn"
                                                    style="font-weight: 500; transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, color 0.15s ease-in-out !important; transform: none !important;">
                                                <i class="fas fa-th-large me-2"></i>
                                                <span id="toggleCategoriesText">Tüm Kategorileri Göster</span>
                                            </button>
                                        @endif
                                        
                                        {{-- Önceki Adım Butonu --}}
                                        @if($currentStep > 1)
                                            <button type="button" 
                                                    class="btn btn-outline-secondary px-5 py-3 btn-nav-previous"
                                                    data-current-step="{{ $currentStep }}"
                                                    data-target-step="{{ $currentStep - 1 }}"
                                                    style="font-weight: 500; transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, color 0.15s ease-in-out !important; transform: none !important;">
                                                <i class="fas fa-arrow-left me-2"></i>
                                                <span class="d-none d-sm-inline">Önceki Adım</span>
                                                <span class="d-sm-none">Önceki</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Sağ taraf: Sonraki Adım --}}
                                <div class="col-12 col-md-6">
                                    <div class="d-flex justify-content-md-end justify-content-center">
                                        @if($currentStep < $totalSteps)
                                            <button type="button" 
                                                    class="btn btn-primary btn-lg px-6 py-3 btn-nav-next" 
                                                    data-current-step="{{ $currentStep }}"
                                                    data-target-step="{{ $currentStep + 1 }}"
                                                    style="font-weight: 600; font-size: 16px; box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15); transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, color 0.15s ease-in-out !important; transform: none !important;">
                                                <span class="d-none d-sm-inline">Sonraki Adım</span>
                                                <span class="d-sm-none">Sonraki</span>
                                                <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-success btn-lg px-6 py-3 btn-complete-profile"
                                                    style="font-weight: 600; font-size: 16px; box-shadow: 0 2px 8px rgba(25, 135, 84, 0.15); transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, color 0.15s ease-in-out !important; transform: none !important;">
                                                <i class="fas fa-magic me-2"></i>
                                                <span class="d-none d-sm-inline">Yapay Zeka Asistanını Aktifleştir</span>
                                                <span class="d-sm-none">AI'ı Aktifleştir</span>
                                            </button>
                                        @endif
                                    </div>
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

// ===== BASIT VE NET ARAMA SİSTEMİ =====
let isSearchMode = false; // Arama modu açık mı?
let showAllMode = false; // Tümünü göster modu açık mı?

function performSearch(searchTerm) {
    const term = searchTerm.toLowerCase().trim();
    const sectorCards = $('.sector-card');
    const categoryBands = $('.category-header-band');
    const sectorsGrid = $('#sectorsGrid');
    const toggleBtn = $('#toggleCategoriesBtn');
    const noResultsDiv = $('#noSearchResults');
    const selectedSectorSection = $('#selectedSectorSection');
    
    if (term.length > 0) {
        // ARAMA MODU AÇIK
        isSearchMode = true;
        toggleBtn.hide(); // "Tümünü Göster" butonunu gizle
        selectedSectorSection.hide(); // Seçili sektör bölümünü gizle (arama sırasında)
        sectorsGrid.show();
        
        let hasResults = false;
        let visibleCategoriesCount = {};
        
        // Önce tüm kartları gizle
        sectorCards.each(function() {
            $(this).closest('.col-6, .col-md-4, .col-lg-3').hide();
        });
        categoryBands.each(function() {
            $(this).closest('.col-12').hide();
        });
        
        // Arama sonuçları için özel container oluştur
        let searchResultsContainer = $('#searchResultsContainer');
        if (searchResultsContainer.length === 0) {
            searchResultsContainer = $('<div id="searchResultsContainer" class="row"></div>');
            sectorsGrid.prepend(searchResultsContainer);
        }
        searchResultsContainer.empty();
        
        // Duplicate control için array
        let addedSectors = [];
        
        // Arama kriterine uyan kartları bul ve yanyana dizilmiş şekilde göster
        sectorCards.each(function() {
            const $card = $(this);
            const sectorName = $card.find('.sector-name').text().toLowerCase();
            const sectorDesc = $card.find('.sector-desc').text().toLowerCase();
            const $cardContainer = $card.closest('.col-6, .col-md-4, .col-lg-3');
            const $categoryContainer = $cardContainer.closest('.row').prev();
            const categoryTitle = $categoryContainer.find('h5').text().toLowerCase();
            
            // Sector unique identifier
            const sectorValue = $card.data('sector') || $card.find('input').val();
            
            // Arama terimi sektör adında, açıklamasında VEYA ana kategori adında var mı?
            const matchesSector = sectorName.includes(term) || sectorDesc.includes(term);
            const matchesCategory = categoryTitle.includes(term);
            
            if ((matchesSector || matchesCategory) && !addedSectors.includes(sectorValue)) {
                // Kartı klonla ve search container'a ekle
                const $clonedCard = $cardContainer.clone();
                $clonedCard.show();
                searchResultsContainer.append($clonedCard);
                addedSectors.push(sectorValue);
                hasResults = true;
            }
        });
        
        // Sonuç yoksa hata göster
        if (!hasResults) {
            showNoResults(term);
        } else {
            hideNoResults();
        }
        
    } else {
        // ARAMA KAPATILDI
        isSearchMode = false;
        toggleBtn.show(); // "Tümünü Göster" butonunu göster
        selectedSectorSection.show(); // Seçili sektör bölümünü tekrar göster
        sectorsGrid.hide(); // Grid'i gizle (normal duruma dön)
        hideNoResults();
        
        // Search results container'ını temizle
        const searchResultsContainer = $('#searchResultsContainer');
        if (searchResultsContainer.length > 0) {
            searchResultsContainer.remove();
        }
    }
}

function showAllCategories() {
    const sectorCards = $('.sector-card');
    const categoryBands = $('.category-header-band');
    const sectorsGrid = $('#sectorsGrid');
    const selectedSectorCode = '{{ $formData["sector_selection"] ?? "" }}';
    const toggleBtn = $('#toggleCategoriesBtn');
    const selectedSectorSection = $('#selectedSectorSection');
    
    showAllMode = true;
    selectedSectorSection.hide(); // Seçili sektör bölümünü gizle (tümü gösterilirken)
    sectorsGrid.show();
    
    // TÜM kartları ve kategorileri göster
    sectorCards.each(function() {
        $(this).closest('.col-6, .col-md-4, .col-lg-3').show();
    });
    categoryBands.each(function() {
        $(this).closest('.col-12').show();
    });
    
    // Seçili sektörü vurgula
    highlightSelectedSector(selectedSectorCode);
    
    // Buton metnini güncelle
    $('#toggleCategoriesText').text('Kategorileri Gizle');
}

function hideCategories() {
    showAllMode = false;
    
    // Seçili sektör bölümünü tekrar göster ve grid'i gizle
    $('#selectedSectorSection').show();
    $('#sectorsGrid').hide();
    
    // Buton metnini güncelle
    $('#toggleCategoriesText').text('Tüm Kategorileri Göster');
}

function showSelectedSectorOnly() {
    const selectedSectorCode = '{{ $formData["sector_selection"] ?? "" }}';
    const sectorsGrid = $('#sectorsGrid');
    
    if (selectedSectorCode) {
        sectorsGrid.show();
        
        const sectorCards = $('.sector-card');
        const categoryBands = $('.category-header-band');
        
        // Önce tüm kartları gizle
        sectorCards.each(function() {
            $(this).closest('.col-6, .col-md-4, .col-lg-3').hide();
        });
        categoryBands.each(function() {
            $(this).closest('.col-12').hide();
        });
        
        let foundSelected = false;
        
        // Seçili sektörü bul ve göster
        sectorCards.each(function() {
            const $card = $(this);
            const cardSectorCode = $card.find('input[type="radio"]').val();
            
            if (cardSectorCode === selectedSectorCode) {
                const $cardContainer = $card.closest('.col-6, .col-md-4, .col-lg-3');
                const $categoryContainer = $cardContainer.closest('.row').prev();
                
                // Seçili sektörü ve kategorisini göster
                $cardContainer.show();
                $categoryContainer.show();
                
                foundSelected = true;
                
                // Vurgula
                highlightSelectedSector(selectedSectorCode);
                return false; // Break loop
            }
        });
        
        if (!foundSelected) {
            // Seçili sektör bulunamadıysa grid'i gizle
            sectorsGrid.hide();
        }
    } else {
        sectorsGrid.hide();
    }
}

function highlightSelectedSector(selectedSectorCode) {
    const sectorCards = $('.sector-card');
    
    sectorCards.each(function() {
        const $card = $(this);
        const cardSectorCode = $card.find('input[type="radio"]').val();
        
        if (cardSectorCode === selectedSectorCode) {
            $card.addClass('selected-sector');
            $card.css({
                'border': '2px solid var(--tblr-secondary)',
                'box-shadow': '0 0 10px rgba(108, 117, 125, 0.2)',
                'background': 'var(--tblr-bg-surface)'
            });
        } else {
            $card.removeClass('selected-sector');
            $card.css({
                'border': '',
                'box-shadow': '',
                'background': ''
            });
        }
    });
}

function showNoResults(searchTerm) {
    let noResultsDiv = $('#noSearchResults');
    if (noResultsDiv.length === 0) {
        $('#sectorsGrid').before(`
            <div id="noSearchResults" class="text-center py-5">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="fas fa-search fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title h3">Arama sonucu bulunamadı</p>
                    <p class="empty-subtitle text-muted mb-3">
                        "<strong id="searchTermDisplay"></strong>" için uygun sektör bulunamadı.
                        <br>Farklı anahtar kelimeler deneyebilirsiniz.
                    </p>
                    <button type="button" 
                            class="btn btn-primary" 
                            id="showAllFromNoResults"
                            style="border-radius: 20px;">
                        <i class="fas fa-th-large me-2"></i>
                        Tüm kategorileri görebilirsiniz
                    </button>
                </div>
            </div>
        `);
        noResultsDiv = $('#noSearchResults');
        
        // Tüm kategorileri göster butonuna event ekle
        $('#showAllFromNoResults').on('click', function() {
            $('#sectorSearch').val(''); // Arama kutusunu temizle
            isSearchMode = false;
            showAllMode = true;
            $('#toggleCategoriesBtn').show();
            showAllCategories(); // Tüm kategorileri göster
            hideNoResults(); // "Sonuç bulunamadı" mesajını gizle
        });
    }
    
    $('#searchTermDisplay').text(searchTerm);
    noResultsDiv.show();
}

function hideNoResults() {
    $('#noSearchResults').hide();
}

function clearSearch() {
    $('#sectorSearch').val('');
    isSearchMode = false;
    showAllMode = false;
    $('#toggleCategoriesBtn').show();
    $('#toggleCategoriesText').text('Tüm Kategorileri Göster');
    $('#selectedSectorSection').show(); // Seçili sektör bölümünü tekrar göster
    $('#sectorsGrid').hide(); // Grid'i gizle (geri normal duruma)
    hideNoResults(); // Eğer "sonuç bulunamadı" varsa gizle
}

// ===== JQUERY EVENT HANDLERS =====
$(document).ready(function() {
    // Sayfa yüklendiğinde seçili sektörü göster
    initializeSelectedSectorVisibility();
    
    // Arama input event handler
    $('#sectorSearch').on('input', function() {
        performSearch($(this).val());
    });
    
    // Arama kutusu focus/blur events
    $('#sectorSearch').on('focus', function() {
        $(this).closest('.google-search-box').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.google-search-box').removeClass('focused');
    });
    
    // Toggle kategoriler butonu - sadece arama modunda değilken çalışır
    $('#toggleCategoriesBtn').on('click', function() {
        if (!isSearchMode) {
            if (showAllMode) {
                hideCategories();
            } else {
                showAllCategories();
            }
        }
    });
    
    // Temizle butonu
    $('#clearSearchBtn').on('click', function() {
        clearSearch();
    });
    
    // ===== KURUCU SORULARI GÖSTER/GİZLE (STEP 4) =====
    $(document).on('change', 'input[name="formData[company_info.share_founder_info]"]', function() {
        const value = $(this).val();
        const founderSection = $('#founder-questions-section');
        
        if (value === 'evet') {
            // Kurucu sorularını göster
            founderSection.fadeIn(300);
        } else {
            // Kurucu sorularını gizle
            founderSection.fadeOut(300);
            
            // Kurucu alanlarını temizle
            $('input[name^="formData[founder_info"]').val('');
            $('input[name^="formData[founder_info"]').prop('checked', false);
        }
    });
});

// Sayfa yüklendiğinde seçili sektörün görünür olmasını sağla
function initializeSelectedSectorVisibility() {
    const selectedSectorCode = '{{ $formData["sector_selection"] ?? "" }}';
    
    // DOM'un hazır olmasını bekle
    setTimeout(function() {
        if (selectedSectorCode) {
            // Seçili sektör varsa, sadece seçili sektörü göster
            showAllMode = false; // İlk açılışta sadece seçili sektör
            $('#toggleCategoriesText').text('Tüm Kategorileri Göster');
            showSelectedSectorOnly();
        } else {
            // Seçili sektör yoksa grid'i gizle
            showAllMode = false;
            $('#toggleCategoriesText').text('Tüm Kategorileri Göster');
            $('#sectorsGrid').hide();
        }
    }, 500);
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
    $(document).on('change', 'input[data-field*="sector_details"]:checkbox, input[data-field*="success_stories"]:checkbox, input[data-field*="ai_behavior_rules"]:checkbox', function(e) {
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
    
    // STEP 5 (AI BEHAVIOR) ÖZEL HANDLER - SADECE RADIO İÇİN (checkbox'lar yukarıdaki unified handler'da)
    $(document).on('change', 'input[data-field*="ai_behavior_rules"]:radio, input[data-field="success_stories.ai_response_style"]:radio, input[data-field="ai_behavior_rules.response_style"]:radio', function(event) {
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

/* ZıPLAMA EFEKTLERİNİ ENGELLE */
#toggleCategoriesBtn {
    transform: none !important;
    transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease !important;
}

#toggleCategoriesBtn:hover {
    background-color: #0d6efd !important;
    color: white !important;
    border-color: #0d6efd !important;
    transform: none !important;
}

#toggleCategoriesBtn:active {
    transform: none !important;
}

#toggleCategoriesBtn:focus {
    transform: none !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
}

/* ARAMA KUTUSU STİLLERİ - ZIPLAMASIZ */
#sectorSearch {
    transform: none !important;
    transition: border-color 0.3s ease, box-shadow 0.3s ease !important;
}

#sectorSearch:hover {
    border-color: #b0b7c3 !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12) !important;
    transform: none !important;
}

#sectorSearch:focus {
    border-color: #4285f4 !important;
    box-shadow: 0 6px 20px rgba(66, 133, 244, 0.15) !important;
    outline: none !important;
    transform: none !important;
}

.google-search-box {
    transform: none !important;
}

.google-search-box.focused {
    transform: none !important;
}

#clearSearchBtn {
    transform: translateY(-50%) !important;
    transition: color 0.2s ease, background-color 0.2s ease !important;
}

#clearSearchBtn:hover {
    color: #5f6368 !important;
    background-color: rgba(95, 99, 104, 0.1) !important;
    transform: translateY(-50%) !important;
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
    
    // Önceki adım buton handler - Loading State ile
    $(document).on('click', '.btn-nav-previous', function() {
        const currentStep = $(this).data('current-step');
        const targetStep = $(this).data('target-step');
        
        // Loading state aktif et
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Yükleniyor...');
        
        // Kısa gecikme sonrası yönlendirme (loading görünümü için)
        setTimeout(function() {
            window.location.href = '{{ route("admin.ai.profile.edit", ["step" => 1]) }}'.replace('/1', '/' + targetStep);
        }, 300);
    });
    
    // Sonraki adım buton handler - Loading State ile
    $(document).on('click', '.btn-nav-next', function() {
        const currentStep = $(this).data('current-step');
        const targetStep = $(this).data('target-step');
        
        // Loading state aktif et
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Yükleniyor...');
        
        // Kısa gecikme sonrası yönlendirme (loading görünümü için)
        setTimeout(function() {
            window.location.href = '{{ route("admin.ai.profile.edit", ["step" => 1]) }}'.replace('/1', '/' + targetStep);
        }, 300);
    });
    
    // Profil tamamlama buton handler (son adım)
    $(document).on('click', '.btn-complete-profile', function() {
        
        // Loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Aktifleştiriliyor...');
        
        // Livewire ile complete profile çağır
        @this.call('completeProfile').then(result => {
            // Profile completion success - redirect handled by Livewire
        }).catch(error => {
            console.error('Profile completion error:', error);
            // Button'u eski haline getir
            $(this).prop('disabled', false).html('<i class="fas fa-magic me-2"></i>Yapay Zeka Asistanını Aktifleştir');
            
            // Hata mesajını göster
            if (error && error.message) {
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
    const isDigerOption = value.includes('Diğer') || value.includes('diger') || value === 'custom' || value === 'diger' || value === 'other';
    
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
    // ===== SEÇİLİ SEKTÖR BAĞIMSIZ BÖLÜMÜ =====
    updateSelectedSectorDisplay();
    
    // Sector seçildiğinde güncelle
    $(document).on('change', 'input[name*="sector_selection"]', function() {
        updateSelectedSectorDisplay();
    });
    
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

// ===== SEÇİLİ SEKTÖR YÖNETİM FONKSİYONLARI =====
function updateSelectedSectorDisplay() {
    const selectedSector = $('input[name*="sector_selection"]:checked');
    const selectedSection = $('#selectedSectorSection');
    
    if (selectedSector.length) {
        const sectorValue = selectedSector.val();
        
        // Seçili sektörün kartını bul ve bilgilerini al
        const selectedCard = $(`.sector-card[data-sector="${sectorValue}"]`);
        if (selectedCard.length) {
            const sectorName = selectedCard.data('sector-name');
            const sectorDesc = selectedCard.data('sector-desc');
            
            // Seçili sektör bölümünü güncelle (ikon olmadan)
            $('#selectedSectorName').text(sectorName);
            $('#selectedSectorDesc').text(sectorDesc);
            
            // Seçili sektör bölümünü göster
            selectedSection.show();
            
        }
    } else {
        // Seçili sektör yoksa bölümü gizle
        selectedSection.hide();
    }
}

// Değiştir butonu handler
$(document).on('click', '#changeSectorBtn', function() {
    // Grid'i göster, arama kutusunu temizle
    $('#sectorsGrid').show();
    $('#sectorSearch').val('').focus();
    
    // State'leri sıfırla
    showAllMode = true;
    isSearchMode = false;
    
    // Tüm kartları göster
    $('.sector-card').closest('.col-6, .col-md-4, .col-lg-3').show();
    $('.category-header-band').closest('.col-12').show();
    
    // Toggle butonu güncelle
    updateToggleButton();
    
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

{{-- Google Benzeri Arama CSS --}}
<style>
/* Google Search Box Styling */
.google-search-box .form-control:focus {
    border-color: #4285f4 !important;
    box-shadow: 0 4px 16px rgba(66, 133, 244, 0.2) !important;
    outline: none;
}

.google-search-box.focused {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

.google-search-container {
    padding: 40px 0;
}

.categories-section {
    border-top: 1px solid #e8eaed;
    padding-top: 24px;
    margin-top: 24px;
}

/* Kategori geçiş animasyonları */
.sectors-grid {
    transition: all 0.3s ease;
}

/* Hover effects */
.google-search-box:hover .form-control {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Kategorileri göster butonu hover */
.btn-outline-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
    transition: all 0.3s ease;
}

/* Search icon pulse animation */
.google-search-box .fa-search {
    transition: color 0.3s ease;
}

.google-search-box .form-control:focus + div .fa-search {
    color: #4285f4;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Seçili Kategori Success Pulse Animation */
@keyframes pulse-success {
    0% { 
        transform: scale(1); 
        opacity: 1; 
    }
    50% { 
        transform: scale(1.15); 
        opacity: 0.7; 
    }
    100% { 
        transform: scale(1); 
        opacity: 1; 
    }
}

/* Seçili Kategori Hover Effects */
.selected-sector-section .btn-outline-success:hover {
    transform: none !important;
    background-color: var(--tblr-success);
    color: white;
    border-color: var(--tblr-success);
    box-shadow: 0 2px 8px rgba(25, 135, 84, 0.15);
}

/* Navigation Buton Hover Effects */
.form-footer .btn-nav-next:hover {
    transform: none !important;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.25) !important;
}

.form-footer .btn-complete-profile:hover {
    transform: none !important;
    box-shadow: 0 2px 8px rgba(25, 135, 84, 0.25) !important;
}

.form-footer .btn-nav-previous:hover {
    transform: none !important;
    background-color: var(--tblr-secondary);
    color: white;
    border-color: var(--tblr-secondary);
    box-shadow: 0 2px 8px rgba(108, 117, 125, 0.15);
}

.form-footer .btn-outline-primary:hover {
    transform: none !important;
    background-color: var(--tblr-primary);
    color: white;
    border-color: var(--tblr-primary);
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
}

/* Light/Dark Mode Uyumlu Renkler */
.selected-sector-section .card {
    background: var(--tblr-bg-surface) !important;
    border-color: var(--tblr-success) !important;
}

.selected-sector-section #selectedSectorName {
    color: var(--tblr-body-color) !important;
}

.selected-sector-section #selectedSectorDesc {
    color: var(--tblr-body-color) !important;
    opacity: 0.7;
}

/* Light/Dark Mode Uyumlu Renkler */
.selected-sector-section .card {
    background: var(--tblr-bg-surface) !important;
    border-color: var(--tblr-primary) !important;
}

.selected-sector-section #selectedSectorName {
    color: var(--tblr-body-color) !important;
}

.selected-sector-section #selectedSectorDesc {
    color: var(--tblr-body-color) !important;
    opacity: 0.7;
}

/* Form Labels ve Text Light/Dark Mode */
.form-label, .question-label {
    color: var(--tblr-body-color) !important;
}

.form-hint {
    color: var(--tblr-body-color) !important;
    opacity: 0.7;
}

/* Seçili Kategori Primary Pulse Animation */
@keyframes pulse-primary {
    0% { 
        transform: scale(1); 
        opacity: 1; 
    }
    50% { 
        transform: scale(1.15); 
        opacity: 0.7; 
    }
    100% { 
        transform: scale(1); 
        opacity: 1; 
    }
}

/* Seçili Kategori Hover Effects */
.selected-sector-section .btn-outline-primary:hover {
    transform: translateY(-2px);
    background-color: var(--tblr-primary);
    color: white;
    border-color: var(--tblr-primary);
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.25);
}

/* Responsive Seçili Kategori Düzenlemeleri */
@media (max-width: 576px) {
    .selected-sector-section .card-body {
        padding: 1.5rem !important;
    }
    
    .selected-sector-section .bg-primary {
        width: 48px !important;
        height: 48px !important;
    }
    
    .selected-sector-section .bg-primary i {
        font-size: 20px !important;
    }
    
    .selected-sector-section h6 {
        font-size: 13px !important;
    }
    
    .selected-sector-section #selectedSectorName {
        font-size: 14px !important;
    }
}

/* Anti-Jumping CSS - Butonlar ve elementler zıplamasın */
.btn, .form-control, .google-search-box {
    transform: none !important;
    transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}

.btn:hover {
    transform: none !important;
}

/* Google search box özel hover (sadece box-shadow) */
.google-search-box:hover .form-control {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: none !important;
}

/* Toggle categories button hover - sadece renk değişimi */
.btn-outline-primary:hover {
    transform: none !important;
    background-color: var(--tblr-primary);
    color: white;
    border-color: var(--tblr-primary);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .google-search-container {
        padding: 20px 0;
    }
    
    .google-search-box .form-control {
        height: clamp(56px, 8vw, 72px);
        font-size: clamp(18px, 3vw, 24px);
        max-width: min(800px, 95vw);
    }
}
</style>

@endpush