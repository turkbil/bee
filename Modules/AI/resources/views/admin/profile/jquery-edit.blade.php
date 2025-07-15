@extends('admin.layout')

@include('ai::helper')

@section('title', 'AI Profil Düzenle - jQuery')

@section('content')
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
                                            Adım <span id="current-step">1</span>/5
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
                                            <div class="ai-hologram" style="width: 80px; height: 80px; background: conic-gradient(from 0deg, #00d4ff, #9333ea, #f59e0b, #10b981, #00d4ff); border-radius: 50%; animation: hologram-pulse 4s ease-in-out infinite; filter: drop-shadow(0 0 20px rgba(0, 212, 255, 0.6)); display: flex; align-items: center; justify-content: center; position: relative; flex-shrink: 0;">
                                                <div style="width: 68px; height: 68px; background: linear-gradient(135deg, #0f0f23, #1a1a2e); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-industry" style="font-size: 2rem; color: #00d4ff; filter: drop-shadow(0 0 10px rgba(0, 212, 255, 0.8)); animation: float-icon 3s ease-in-out infinite;"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="step-text-content">
                                                <h1 class="hero-title m-0" id="step-title">
                                                    Adım 1: Sektör Seçimi
                                                </h1>
                                                <p class="hero-subtitle m-0" id="step-subtitle">
                                                    Markanızın sektörünü seçin
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Sağ - Progress Ring --}}
                                <div class="col-lg-4 col-md-5">
                                    <div class="progress-ring-container">
                                        <div class="progress-ring-wrapper">
                                            <svg class="progress-ring" width="120" height="120">
                                                <circle class="progress-ring-background" cx="60" cy="60" r="50"></circle>
                                                <circle class="progress-ring-progress" cx="60" cy="60" r="50" id="progress-circle"></circle>
                                            </svg>
                                            <div class="progress-ring-text">
                                                <span class="progress-percentage" id="progress-percentage">20%</span>
                                                <span class="progress-label">Tamamlandı</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step Navigation --}}
            <div class="step-navigation-container mb-4">
                <div class="step-nav-wrapper">
                    @for($i = 1; $i <= 5; $i++)
                        <div class="step-nav-item step-nav {{ $i == 1 ? 'active' : '' }}" data-step="{{ $i }}">
                            <div class="step-number">{{ $i }}</div>
                            <div class="step-title">Adım {{ $i }}</div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Form Container --}}
            <div class="wizard-form-container">
                <div class="wizard-form-card">
                    <div class="wizard-form-header">
                        <div class="form-header-content">
                            <h3 class="form-title" id="form-title">Sektör Seçimi</h3>
                            <p class="form-description" id="form-description">Markanızın faaliyet gösterdiği sektörü seçin</p>
                        </div>
                    </div>
                    
                    <div class="wizard-form-body">
                        {{-- Loading artık gerekli değil - sorular PHP'den geliyor --}}

                        {{-- Questions Container --}}
                        <div id="questions-container" class="questions-container">
                            {{-- Step 1: Sektör Seçimi --}}
                            @if($initialStep == 1 && isset($sectors) && $sectors->count() > 0)
                                <div class="form-group mb-4">
                                    <label class="form-label question-label">
                                        Sektör Seçimi <span class="text-danger">*</span>
                                    </label>
                                    <div class="form-hint mb-3">Yapay zeka asistanınız için en uygun sektörü seçin</div>
                                    
                                    {{-- SEÇİLİ SEKTÖR BÖLÜMÜ --}}
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
                                    
                                    {{-- Kategori Listesi --}}
                                    <div class="sectors-grid" id="sectorsGrid">
                                        @foreach($sectors as $mainCategory)
                                            {{-- Ana Kategori Başlığı --}}
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
                                                            <input type="radio" name="sector" value="{{ $sector->code }}" 
                                                                   class="form-imagecheck-input" 
                                                                   data-sector="{{ $sector->code }}"
                                                                   data-sector-name="{{ $sector->name }}"
                                                                   data-sector-desc="{{ $sector->description ?? '' }}"
                                                                   data-sector-icon="@if($sector->emoji){{ $sector->emoji }}@elseif($sector->icon){{ $sector->icon }}@else fas fa-briefcase @endif"
                                                                   data-sector-icon-type="@if($sector->emoji)emoji@elseif($sector->icon)icon@else icon @endif"
                                                                   {{ ($profileData['sector'] ?? '') == $sector->code ? 'checked' : '' }} required>
                                                            <span class="form-imagecheck-figure" style="width: 100%; display: flex;">
                                                                <div class="form-imagecheck-image sector-card d-flex flex-column" 
                                                                     style="min-height: 140px; height: 140px; width: 100%; flex: 1;">
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
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Diğer Sorular --}}
                            @if(isset($questions))
                                @foreach($questions as $question)
                                    @php
                                        $fieldName = $question->question_key;
                                        $value = $profileData[$fieldName] ?? '';
                                    @endphp
                                    
                                    <div class="form-group mb-4">
                                        <label class="form-label question-label">
                                            {{ $question->question_text }}
                                            @if($question->is_required) <span class="text-danger">*</span> @endif
                                        </label>
                                        
                                        @switch($question->input_type)
                                            @case('text')
                                                <input type="text" class="form-control" 
                                                       id="{{ $fieldName }}" name="{{ $fieldName }}" 
                                                       value="{{ $value }}" 
                                                       placeholder="{{ $question->input_placeholder ?? '' }}"
                                                       {{ $question->is_required ? 'required' : '' }}>
                                                @break
                                                
                                            @case('textarea')
                                                <textarea class="form-control" rows="4"
                                                          id="{{ $fieldName }}" name="{{ $fieldName }}" 
                                                          placeholder="{{ $question->input_placeholder ?? '' }}"
                                                          {{ $question->is_required ? 'required' : '' }}>{{ $value }}</textarea>
                                                @break
                                                
                                            @case('select')
                                                <select class="form-select" 
                                                        id="{{ $fieldName }}" name="{{ $fieldName }}" 
                                                        {{ $question->is_required ? 'required' : '' }}>
                                                    <option value="">Seçiniz...</option>
                                                    @php
                                                        $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                                    @endphp
                                                    @if($options)
                                                        @foreach($options as $option)
                                                            @php
                                                                $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                                                                $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                                                            @endphp
                                                            <option value="{{ $optionValue }}" {{ $value == $optionValue ? 'selected' : '' }}>
                                                                {{ $optionLabel }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @break
                                                
                                            @case('radio')
                                                @php
                                                    $radioOptions = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                                @endphp
                                                <div class="row">
                                                    @if($radioOptions)
                                                        @foreach($radioOptions as $option)
                                                            @php
                                                                $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                                                                $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                                                                $optionIcon = is_array($option) ? ($option['icon'] ?? '') : '';
                                                                $optionDescription = is_array($option) ? ($option['description'] ?? '') : '';
                                                                $hasCustomInput = (is_array($option) && !empty($option['has_custom_input'])) || 
                                                                                (strpos($optionLabel, 'Diğer') !== false || strpos($optionValue, 'diger') !== false);
                                                            @endphp
                                                            <div class="col-md-6 col-12 mb-2">
                                                                <label class="form-selectgroup-item flex-fill">
                                                                    <input type="radio" name="{{ $fieldName }}" value="{{ $optionValue }}" 
                                                                           class="form-selectgroup-input @if($hasCustomInput) custom-radio-trigger @endif" 
                                                                           @if($hasCustomInput) data-custom-field="{{ $fieldName }}_custom" @endif
                                                                           {{ $value == $optionValue ? 'checked' : '' }}
                                                                           {{ $question->is_required ? 'required' : '' }}>
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
                                                        @foreach($radioOptions as $option)
                                                            @php
                                                                $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                                                                $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                                                                $hasCustomInput = (is_array($option) && !empty($option['has_custom_input'])) || 
                                                                                (strpos($optionLabel, 'Diğer') !== false || strpos($optionValue, 'diger') !== false);
                                                            @endphp
                                                            @if($hasCustomInput)
                                                                <div class="col-12 mt-3" id="{{ $fieldName }}_custom_container" 
                                                                     style="display: none;">
                                                                    <input type="text" 
                                                                           class="form-control" 
                                                                           name="{{ $fieldName }}_custom"
                                                                           data-field="{{ $fieldName }}_custom"
                                                                           placeholder="{{ (is_array($option) && isset($option['custom_placeholder'])) ? $option['custom_placeholder'] : 'Özel bilginizi giriniz...' }}"
                                                                           value="{{ $profileData[$fieldName . '_custom'] ?? '' }}">
                                                                </div>
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                                @break
                                                
                                            @case('checkbox')
                                                @php
                                                    $checkboxOptions = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                                @endphp
                                                <div class="row">
                                                    @if($checkboxOptions)
                                                        @foreach($checkboxOptions as $option)
                                                            @php
                                                                $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                                                                $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                                                                $optionIcon = is_array($option) ? ($option['icon'] ?? '') : '';
                                                                $optionDescription = is_array($option) ? ($option['description'] ?? '') : '';
                                                                $checked = isset($profileData[$fieldName . '.' . $optionValue]) && $profileData[$fieldName . '.' . $optionValue];
                                                                $hasCustomInput = (is_array($option) && !empty($option['has_custom_input'])) || 
                                                                                (strpos($optionLabel, 'Diğer') !== false || strpos($optionValue, 'diger') !== false);
                                                            @endphp
                                                            <div class="col-md-6 col-12 mb-2">
                                                                <label class="form-selectgroup-item flex-fill">
                                                                    <input type="checkbox" value="{{ $optionValue }}" 
                                                                           class="form-selectgroup-input checkbox-field @if($hasCustomInput) custom-checkbox-trigger @endif" 
                                                                           name="{{ $fieldName }}[]"
                                                                           @if($hasCustomInput) data-custom-field="{{ $fieldName }}_{{ $optionValue }}_custom" @endif
                                                                           {{ $checked ? 'checked' : '' }}>
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
                                                        
                                                        {{-- Custom Input Fields for checkboxes (Hidden by default) --}}
                                                        @foreach($checkboxOptions as $option)
                                                            @php
                                                                $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                                                                $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                                                                $hasCustomInput = (is_array($option) && !empty($option['has_custom_input'])) || 
                                                                                (strpos($optionLabel, 'Diğer') !== false || strpos($optionValue, 'diger') !== false);
                                                            @endphp
                                                            @if($hasCustomInput)
                                                                <div class="col-12 mt-3" id="{{ $fieldName }}_{{ $optionValue }}_custom_container" 
                                                                     style="display: none;">
                                                                    <input type="text" 
                                                                           class="form-control" 
                                                                           name="{{ $fieldName }}_{{ $optionValue }}_custom"
                                                                           data-field="{{ $fieldName }}_{{ $optionValue }}_custom"
                                                                           placeholder="{{ (is_array($option) && isset($option['custom_placeholder'])) ? $option['custom_placeholder'] : 'Özel bilginizi giriniz...' }}"
                                                                           value="{{ $profileData[$fieldName . '_' . $optionValue . '_custom'] ?? '' }}">
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                                @break
                                        @endswitch
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- Navigation Buttons --}}
                    <div class="wizard-form-footer">
                        <div class="form-navigation">
                            <button type="button" class="btn btn-outline-secondary" id="prev-btn" disabled>
                                <i class="fas fa-arrow-left me-2"></i>Önceki
                            </button>
                            <button type="button" class="btn btn-primary" id="next-btn">
                                Sonraki<i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <button type="button" class="btn btn-success" id="complete-btn" style="display: none;">
                                <i class="fas fa-check me-2"></i>Tamamla
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
</div>

{{-- Success/Error Messages --}}
<div id="message-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('admin-assets/css/ai-profile-wizard.css') }}">
@endpush

@push('scripts')
<script>
// CSRF token setup
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    let currentStep = {{ $initialStep ?? 1 }};
    let profileData = @json($profileData ?? []);
    let currentQuestions = @json($questions ?? []);
    let stepTitles = {
        1: 'Sektör Seçimi',
        2: 'Temel Bilgiler',
        3: 'Marka Detayları',
        4: 'Kurucu Bilgileri',
        5: 'AI Ayarları'
    };
    
    let stepSubtitles = {
        1: 'Markanızın faaliyet gösterdiği sektörü seçin',
        2: 'Şirketinizin temel bilgilerini girin',
        3: 'Marka kimliğinizi ve özelliklerinizi belirleyin',
        4: 'Kurucu bilgilerinizi paylaşın (isteğe bağlı)',
        5: 'AI davranış ayarlarını yapılandırın'
    };
    
    let stepIcons = {
        1: 'fas fa-industry',
        2: 'fas fa-building',
        3: 'fas fa-palette',
        4: 'fas fa-user-tie',
        5: 'fas fa-robot'
    };

    // Initialize
    init();

    function init() {
        // Sorular ve profil verisi artık PHP'den geliyor - AJAX yok
        console.log('Initializing with server-side data...');
        console.log('Profile data:', profileData);
        console.log('Current questions:', currentQuestions);
        
        // Eğer step 1 ve sektör seçilmişse seçili sektör bölümünü göster
        if (currentStep === 1 && profileData.sector) {
            let selectedSector = $('.form-imagecheck-input:checked');
            if (selectedSector.length > 0) {
                let sectorName = selectedSector.data('sector-name');
                let sectorDesc = selectedSector.data('sector-desc');
                
                $('#selectedSectorName').text(sectorName);
                $('#selectedSectorDesc').text(sectorDesc);
                $('#selectedSectorSection').show();
                $('#sectorsGrid').hide();
            }
        }
        
        // Initialize custom input fields if "Diğer" options are selected
        $('.custom-radio-trigger:checked').each(function() {
            let customField = $(this).data('custom-field');
            let container = $('#' + customField + '_container');
            container.show();
        });
        
        $('.custom-checkbox-trigger:checked').each(function() {
            let customField = $(this).data('custom-field');
            let container = $('#' + customField + '_container');
            container.show();
        });
        
        updateUI();
        bindEvents();
    }

    // Bu fonksiyon artık kullanılmıyor - sorular PHP'den geliyor

    // Bu fonksiyon artık kullanılmıyor - sorular PHP'den geliyor

    function bindEvents() {
        // Auto-save on change
        $('#questions-container').on('change', 'input, select, textarea', function() {
            let field = $(this).attr('name');
            let value = $(this).val();
            
            // Checkbox special handling
            if ($(this).hasClass('checkbox-field')) {
                let checkboxName = $(this).attr('name').replace('[]', '');
                let checkedValues = [];
                $(`input[name="${checkboxName}[]"]:checked`).each(function() {
                    checkedValues.push($(this).val());
                });
                field = checkboxName;
                value = checkedValues;
            }
            
            // Custom input field handling
            if ($(this).attr('data-field')) {
                field = $(this).attr('data-field');
                value = $(this).val();
            }
            
            saveField(field, value);
        });
        
        // Sector selection handling
        $('#questions-container').on('change', '.form-imagecheck-input', function() {
            if ($(this).is(':checked')) {
                let sectorCode = $(this).val();
                let sectorName = $(this).data('sector-name');
                let sectorDesc = $(this).data('sector-desc');
                let sectorIcon = $(this).data('sector-icon');
                let sectorIconType = $(this).data('sector-icon-type');
                
                // Update selected sector display
                $('#selectedSectorName').text(sectorName);
                $('#selectedSectorDesc').text(sectorDesc);
                
                // Show selected sector section
                $('#selectedSectorSection').show();
                
                // Hide sectors grid
                $('#sectorsGrid').hide();
                
                // Save sector selection
                profileData.sector = sectorCode;
                saveField('sector', sectorCode);
                
                showSuccess('Sektör seçildi: ' + sectorName);
            }
        });
        
        // Change sector button
        $('#questions-container').on('click', '#changeSectorBtn', function() {
            $('#selectedSectorSection').hide();
            $('#sectorsGrid').show();
        });
        
        // Custom input field toggle for "Diğer" options
        $('#questions-container').on('change', '.custom-radio-trigger', function() {
            let customField = $(this).data('custom-field');
            let container = $('#' + customField + '_container');
            
            if ($(this).is(':checked')) {
                container.show();
                container.find('input').focus();
            } else {
                container.hide();
                container.find('input').val('');
            }
        });
        
        // Hide custom input when other radio options are selected
        $('#questions-container').on('change', 'input[type="radio"]:not(.custom-radio-trigger)', function() {
            let radioName = $(this).attr('name');
            let customContainers = $(`input[name="${radioName}"].custom-radio-trigger`);
            
            customContainers.each(function() {
                let customField = $(this).data('custom-field');
                let container = $('#' + customField + '_container');
                container.hide();
                container.find('input').val('');
            });
        });
        
        // Custom input field toggle for checkbox "Diğer" options
        $('#questions-container').on('change', '.custom-checkbox-trigger', function() {
            let customField = $(this).data('custom-field');
            let container = $('#' + customField + '_container');
            
            if ($(this).is(':checked')) {
                container.show();
                container.find('input').focus();
            } else {
                container.hide();
                container.find('input').val('');
            }
        });
    }

    function saveField(field, value) {
        let data = {
            field: field,
            value: value,
            step: currentStep,
            _token: '{{ csrf_token() }}'
        };
        
        $.post('{{ route("admin.ai.profile.save-field") }}', data, function(response) {
            if (response.success) {
                // Update local profile data
                profileData[field] = value;
                showSuccess('Kaydedildi: ' + field);
            } else {
                showError('Kayıt hatası: ' + response.message);
            }
        });
    }

    // Navigation
    $('.step-nav').click(function() {
        let step = parseInt($(this).data('step'));
        goToStep(step);
    });

    $('#prev-btn').click(function() {
        if (currentStep > 1) {
            goToStep(currentStep - 1);
        }
    });

    $('#next-btn').click(function() {
        if (currentStep < 5) {
            goToStep(currentStep + 1);
        }
    });

    $('#complete-btn').click(function() {
        showSuccess('Profil tamamlandı!');
        // Redirect to profile show page
        window.location.href = '{{ route("admin.ai.profile.show") }}';
    });

    function goToStep(step) {
        // URL'yi güncelle
        let url = '{{ route("admin.ai.profile.jquery-edit", ":step") }}'.replace(':step', step);
        window.location.href = url;
    }

    function updateUI() {
        // Update step indicator
        $('#current-step').text(currentStep);
        $('#step-title').text(`Adım ${currentStep}: ${stepTitles[currentStep]}`);
        $('#step-subtitle').text(stepSubtitles[currentStep]);
        
        // Update form titles
        $('#form-title').text(stepTitles[currentStep]);
        $('#form-description').text(stepSubtitles[currentStep]);
        
        // Update progress ring
        let percentage = (currentStep / 5) * 100;
        $('#progress-percentage').text(percentage + '%');
        
        // Update progress circle
        let circumference = 2 * Math.PI * 50;
        let offset = circumference - (percentage / 100) * circumference;
        $('#progress-circle').css('stroke-dashoffset', offset);
        
        // Update step navigation
        $('.step-nav').removeClass('active');
        $(`.step-nav[data-step="${currentStep}"]`).addClass('active');
        
        // Update hologram icon
        $('.ai-hologram i').removeClass().addClass(stepIcons[currentStep]);
        
        // Update buttons
        $('#prev-btn').prop('disabled', currentStep === 1);
        $('#next-btn').toggle(currentStep < 5);
        $('#complete-btn').toggle(currentStep === 5);
    }

    // Bu fonksiyonlar artık gerekli değil - sorular PHP'den geliyor

    function showSuccess(message) {
        showMessage(message, 'success');
    }

    function showError(message) {
        showMessage(message, 'danger');
    }

    function showMessage(message, type) {
        let html = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        
        $('#message-container').append(html);
        
        // Auto remove after 3 seconds
        setTimeout(function() {
            $('#message-container .alert').first().remove();
        }, 3000);
    }
});
</script>
@endpush