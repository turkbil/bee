@extends('admin.layout')

@include('ai::helper')

@php
    // Debug: Blade template render ediliyor mu?
    \Log::info('🔥 jquery-edit.blade.php view rendering', ['step' => $initialStep ?? 'unknown']);
@endphp

@section('title', 'AI Profil Düzenle - jQuery')

@section('content')
{{-- AI Profile Wizard - Modern Digital Experience - Single Root Element --}}
<div class="ai-profile-wizard-container">

    {{-- Main Content Container --}}
    <div class="container mt-2">
    <div class="row justify-content-center">
        <div class="col-12">
            
            {{-- Step Hero Section --}}
            <div class="step-hero-section mb-3">
                <div class="hero-section">                    
                    <div class="hero-content">
                        <div class="container">
                            {{-- Step Badge --}}
                            <div class="row mb-2">
                                <div class="col-12 text-start">
                                    <div class="hero-main-badge-container">
                                        <span class="badge hero-main-badge">
                                            Adım <span id="current-step">{{ $initialStep ?? 1 }}</span>/{{ $totalSteps ?? 5 }}
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
                                            <div class="step-icon">
                                                @switch($initialStep ?? 1)
                                                    @case(1) 
                                                        <i class="fas fa-industry"></i>
                                                        @break
                                                    @case(2) 
                                                        <i class="fas fa-building"></i>
                                                        @break
                                                    @case(3) 
                                                        <i class="fas fa-palette"></i>
                                                        @break
                                                    @case(4) 
                                                        <i class="fas fa-user-tie"></i>
                                                        @break
                                                    @case(5) 
                                                        <i class="fas fa-trophy"></i>
                                                        @break
                                                    @default 
                                                        <i class="fas fa-robot"></i>
                                                        @break
                                                @endswitch
                                            </div>
                                            
                                            <div class="step-text-content">
                                                <h1 class="hero-title" id="step-title">
                                                    @switch($initialStep ?? 1)
                                                        @case(1) Sektör Seçimi @break
                                                        @case(2) Temel Bilgiler @break
                                                        @case(3) Marka Detayları @break
                                                        @case(4) Kurucu Bilgileri @break
                                                        @case(5) AI Davranış ve İletişim Ayarları @break
                                                    @endswitch
                                                </h1>
                                                <p class="hero-subtitle" id="step-subtitle">
                                                    @switch($initialStep ?? 1)
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
                                            <div class="progress-ring-container">
                                                <div class="progress-ring-wrapper">
                                                    <svg class="progress-ring" width="120" height="120">
                                                        <circle class="progress-ring-background" cx="60" cy="60" r="50"></circle>
                                                        <circle class="progress-ring-progress" cx="60" cy="60" r="50" id="progress-circle" 
                                                               style="stroke-dashoffset: {{ 314.16 - (314.16 * ($completionPercentage ?? 0) / 100) }}px;"></circle>
                                                    </svg>
                                                    <div class="progress-ring-text">
                                                        <span class="progress-percentage" id="progress-percentage">{{ $completionPercentage ?? 0 }}%</span>
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
                    
                    {{-- Steps Indicator --}}
                    <div class="container">
                        <div class="steps-indicator">
                            @for($i = 1; $i <= ($totalSteps ?? 5); $i++)
                                <div class="step-item {{ $i <= ($initialStep ?? 1) ? 'active' : '' }} {{ $i < ($initialStep ?? 1) ? 'completed' : '' }} step-link"
                                     data-step="{{ $i }}"
                                     style="cursor: pointer; text-decoration: none; color: inherit;">
                                    <div class="step-circle">
                                        @if($i < ($initialStep ?? 1))
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
                                </div>
                                @if($i < ($totalSteps ?? 5))
                                    <div class="step-connector {{ $i < ($initialStep ?? 1) ? 'completed' : '' }}"></div>
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

                        {{-- Questions Container --}}
                        <div id="questions-container" class="questions-container">
                            {{-- Step 1: Sektör Seçimi --}}
                            @if($initialStep == 1)
                                <div class="form-group mb-4">
                                    {{-- Step 1 için ortalanmış başlık --}}
                                    <div class="text-center mb-4">
                                        <label class="form-label fw-bold fs-5 mb-2 d-block">
                                            Sektör Seçimi <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="form-hint text-muted">Yapay zeka asistanınız için en uygun sektörü seçin</div>
                                    </div>
                                    
                                    {{-- SIMPLE SEARCH INPUT --}}
                                    <div class="search-container mb-4">
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8 col-md-10 col-12">
                                                <div class="position-relative">
                                                    <input type="text" 
                                                           class="form-control form-control-lg search-input" 
                                                           id="sectorSearch" 
                                                           placeholder="Sektör ara... (teknoloji, sağlık, eğitim, pazarlama...)"
                                                           style="padding-left: 3rem; padding-right: 3rem;">
                                                    <i class="fas fa-search position-absolute" 
                                                       style="left: 1rem; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                                                    <div class="position-absolute" id="clearSearchBtn" style="right: 1rem; top: 50%; transform: translateY(-50%); cursor: pointer; display: none; color: #6c757d;">
                                                        <i class="fas fa-times"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- SEÇİLİ SEKTÖR BÖLÜMÜ --}}
                                    <div class="selected-sector-section mb-3" id="selectedSectorSection" style="display: {{ $selectedSector ? 'block' : 'none' }};">
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8 col-md-10 col-12">
                                                <div class="card" style="background: var(--tblr-bg-surface);">
                                            <div class="card-body px-3 py-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    {{-- Sol: İkon + Bilgiler --}}
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-4">
                                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center position-relative" 
                                                                 style="width: 56px; height: 56px; box-shadow: 0 4px 12px rgba(6, 111, 209, 0.3);">
                                                                @if($selectedSector && $selectedSector->emoji)
                                                                    <span style="font-size: 24px;">{{ $selectedSector->emoji }}</span>
                                                                @else
                                                                    <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                                                                @endif
                                                                {{-- Primary pulse animation --}}
                                                                <div class="position-absolute" style="width: 56px; height: 56px; border: 2px solid rgba(6, 111, 209, 0.4); border-radius: 50%; animation: pulse-primary 3s infinite;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-2 text-muted fw-bold" style="font-size: 15px; letter-spacing: 0.5px;">
                                                                SEÇİLİ KATEGORİ
                                                            </h6>
                                                            <div class="d-flex align-items-center mb-1">
                                                                <span id="selectedSectorName" class="fw-medium" style="font-size: 16px; color: var(--tblr-body-color);">
                                                                    {{ $selectedSector->name ?? 'Sektör Seçiniz' }}
                                                                </span>
                                                            </div>
                                                            <small id="selectedSectorDesc" class="d-block" style="line-height: 1.4; max-width: 300px; color: var(--tblr-body-color); opacity: 0.7;">
                                                                {{ $selectedSector->description ?? '' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                            
                                                            {{-- Sağ: Değiştir Butonu --}}
                                                            <div class="ms-3">
                                                                <button type="button" 
                                                                        class="btn btn-outline-secondary px-4 py-2" 
                                                                        id="changeSectorBtn">
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
                                    </div>
                                    
                                    {{-- ARAMA SONUÇLARI (Başlangıçta gizli) --}}
                                    <div class="search-results-container mb-4" id="searchResults" style="display: none;">
                                        <div class="search-results-header mb-3 text-center">
                                            <h6 class="text-muted mb-0">
                                                <i class="fas fa-search me-2"></i>
                                                Arama Sonuçları
                                            </h6>
                                        </div>
                                        <div class="row g-3" id="searchResultsGrid">
                                            <!-- Arama sonuçları buraya gelecek -->
                                        </div>
                                        
                                        {{-- Arama Sonucu Bulunamadı Uyarısı --}}
                                        <div class="no-results-message" id="noResultsMessage" style="display: none;">
                                            <div class="text-center py-5">
                                                <div class="mb-4">
                                                    <i class="fas fa-search text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                                </div>
                                                <h5 class="text-muted mb-3">Arama sonucu bulunamadı</h5>
                                                <p class="text-muted mb-4">Aradığınız kriterlere uygun sektör bulunamadı. Tüm kategorileri görüntüleyerek uygun sektörü bulabilirsiniz.</p>
                                                <button type="button" class="btn btn-outline-primary px-4 py-2" id="showAllFromNoResults"
                                                        style="border-radius: 25px; font-weight: 500;">
                                                    <i class="fas fa-th-large me-2"></i>
                                                    Tüm Kategorileri Göster
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- TÜM SEKTÖRLER (Kategorili görünüm) --}}
                                    <div class="all-sectors-container" id="allSectorsContainer" style="display: none;">
                                        @foreach($sectors as $mainCategory)
                                            {{-- Ana Kategori Başlığı --}}
                                            <div class="category-section mb-4">
                                                <div class="category-header-band mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="category-icon me-3">
                                                            @if($mainCategory->emoji)
                                                                <span class="category-emoji" style="font-size: 24px;">{{ $mainCategory->emoji }}</span>
                                                            @elseif($mainCategory->icon)
                                                                <i class="{{ $mainCategory->icon }} category-icon-style" style="font-size: 20px; color: #0d6efd;"></i>
                                                            @else
                                                                <i class="fas fa-folder category-icon-style" style="font-size: 20px; color: #0d6efd;"></i>
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

                                                {{-- Alt Kategoriler (Sektörler) --}}
                                                <div class="row g-3">
                                                    @foreach($mainCategory->subCategories as $sector)
                                                        <div class="col-6 col-md-4 col-lg-3">
                                                            <div class="sector-card-wrapper">
                                                                <input type="radio" name="sector" value="{{ $sector->code }}" 
                                                                       class="sector-radio d-none" 
                                                                       id="sector_{{ $sector->code }}"
                                                                       data-sector="{{ $sector->code }}"
                                                                       data-sector-name="{{ $sector->name }}"
                                                                       data-sector-desc="{{ $sector->description ?? '' }}"
                                                                       data-sector-icon="@if($sector->emoji){{ $sector->emoji }}@elseif($sector->icon){{ $sector->icon }}@else fas fa-briefcase @endif"
                                                                       data-sector-icon-type="@if($sector->emoji)emoji@elseif($sector->icon)icon@else icon @endif"
                                                                       {{ ($profileData['sector'] ?? '') == $sector->code ? 'checked' : '' }} required>
                                                                <label for="sector_{{ $sector->code }}" class="sector-card-label">
                                                                    <div class="sector-card d-flex flex-column p-3" 
                                                                         style="min-height: 140px; height: 140px; border: 2px solid #e9ecef; cursor: pointer; transition: all 0.3s ease; background: white;">
                                                                        <div class="sector-icon mb-2" style="min-height: 40px; display: flex; align-items: center; justify-content: center;">
                                                                            @if($sector->emoji)
                                                                                <span class="sector-emoji" style="font-size: 24px;">{{ $sector->emoji }}</span>
                                                                            @elseif($sector->icon)
                                                                                <i class="{{ $sector->icon }}" style="font-size: 24px; color: #0d6efd;"></i>
                                                                            @else
                                                                                <i class="fas fa-briefcase" style="font-size: 24px; color: #0d6efd;"></i>
                                                                            @endif
                                                                        </div>
                                                                        <div class="sector-name text-center fw-bold mb-2" style="font-size: 13px; line-height: 1.2; min-height: 32px; display: flex; align-items: center; justify-content: center;">
                                                                            {{ $sector->name }}
                                                                        </div>
                                                                        <div class="sector-desc text-center flex-grow-1 text-muted" style="font-size: 11px; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                                            {{ $sector->description ?? '' }}
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
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
                                        
                                        // Conditional rendering check
                                        $shouldShow = true;
                                        
                                        // KRITIK: Kurucu soruları için özel durum - her zaman render et, JavaScript ile toggle et
                                        $isFounderQuestion = in_array($fieldName, ['founder_name', 'founder_role', 'founder_additional_info']);
                                        
                                        if ($question->depends_on && $question->show_if && !$isFounderQuestion) {
                                            $shouldShow = false;
                                            $dependsOnField = $question->depends_on;
                                            $showIfConditions = is_string($question->show_if) ? json_decode($question->show_if, true) : $question->show_if;
                                            
                                            if ($showIfConditions && is_array($showIfConditions)) {
                                                foreach ($showIfConditions as $conditionField => $conditionValue) {
                                                    $currentValue = $profileData[$conditionField] ?? '';
                                                    if ($currentValue === $conditionValue) {
                                                        $shouldShow = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        
                                    @endphp
                                    
                                    {{-- Only show question if conditions are met --}}
                                    @if($shouldShow)
                                    <div class="conditional-question" 
                                         data-question-key="{{ $fieldName }}"
                                         @if($question->depends_on) data-depends-on="{{ $question->depends_on }}" @endif
                                         @if($question->show_if) data-show-if="{{ json_encode($question->show_if) }}" @endif
                                         @if($isFounderQuestion) 
                                         style="display: {{ ($profileData['share_founder_info'] ?? '') === 'evet' ? 'block' : 'none' }};"
                                         @endif>
                                    
                                    {{-- Sektör seçimi Step 1'de özel olarak yapılıyor, burada gizle --}}
                                    @if($fieldName === 'sector_selection' && $initialStep == 1)
                                        @continue
                                    @endif
                                    
                                    <div class="form-group mb-3">
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
                                                                // Debug: Radio checked kontrolü
                                                                $isChecked = $value == $optionValue;
                                                                if ($fieldName === 'founder_role' || $fieldName === 'share_founder_info') {
                                                                    echo "<!-- ==================== DEBUG RADIO START ==================== -->";
                                                                    echo "<!-- Field: {$fieldName} -->";
                                                                    echo "<!-- Current Value: '{$value}' -->";
                                                                    echo "<!-- Option Value: '{$optionValue}' -->";
                                                                    echo "<!-- Is Checked: " . ($isChecked ? 'YES' : 'NO') . " -->";
                                                                    echo "<!-- ==================== DEBUG RADIO END ==================== -->";
                                                                }
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
                                                                // Checkbox checked kontrolü - hem direct hem dot notation
                                                                $checked = (isset($profileData[$fieldName][$optionValue]) && $profileData[$fieldName][$optionValue]) ||
                                                                          (isset($profileData[$fieldName . '.' . $optionValue]) && $profileData[$fieldName . '.' . $optionValue]);
                                                                // Debug: Checkbox checked kontrolü
                                                                if ($fieldName === 'target_customers') {
                                                                    echo "<!-- ==================== DEBUG CHECKBOX START ==================== -->";
                                                                    echo "<!-- Field: {$fieldName} -->";
                                                                    echo "<!-- Option Value: '{$optionValue}' -->";
                                                                    echo "<!-- Is Checked: " . ($checked ? 'YES' : 'NO') . " -->";
                                                                    echo "<!-- Direct Data: " . json_encode($profileData[$fieldName] ?? 'NOT SET') . " -->";
                                                                    echo "<!-- Dot Data: " . json_encode($profileData[$fieldName . '.' . $optionValue] ?? 'NOT SET') . " -->";
                                                                    echo "<!-- ==================== DEBUG CHECKBOX END ==================== -->";
                                                                }
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
                                                
                                            @case('checkbox_dynamic')
                                                @php
                                                    // Mevcut seçili sektör
                                                    $selectedSector = $profileData['sector'] ?? null;
                                                    $sectorServices = [];
                                                    
                                                    // Debug: Seçili sektörü göster
                                                    echo "<!-- DEBUG: Seçili sektör: " . ($selectedSector ?? 'YOK') . " -->";
                                                    
                                                    // Seçili sektör varsa hizmetleri veritabanından çek
                                                    if ($selectedSector) {
                                                        $sector = \DB::table('ai_profile_sectors')
                                                            ->where('code', $selectedSector)
                                                            ->where('is_active', 1)
                                                            ->first();
                                                        
                                                        echo "<!-- DEBUG: Sektör bulundu: " . ($sector ? 'EVET' : 'HAYIR') . " -->";
                                                        
                                                        if ($sector && $sector->possible_services) {
                                                            $sectorServices = json_decode($sector->possible_services, true) ?? [];
                                                            echo "<!-- DEBUG: Sektör hizmetleri: " . count($sectorServices) . " adet -->";
                                                        }
                                                    }
                                                    
                                                    // Fallback - sektör yoksa genel hizmetler
                                                    if (empty($sectorServices)) {
                                                        $sectorServices = [
                                                            'Müşteri Danışmanlığı',
                                                            'Satış ve Pazarlama', 
                                                            'Proje Yönetimi',
                                                            'Müşteri Hizmetleri',
                                                            'Eğitim ve Seminer',
                                                            'Teknik Destek'
                                                        ];
                                                        echo "<!-- DEBUG: Fallback hizmetler kullanıldı -->";
                                                    }
                                                @endphp
                                                
                                                <div class="row">
                                                    @if(!empty($sectorServices))
                                                        @foreach($sectorServices as $service)
                                                            @php
                                                                $checked = (isset($profileData[$fieldName][$service]) && $profileData[$fieldName][$service]);
                                                            @endphp
                                                            <div class="col-md-6 col-12 mb-2">
                                                                <label class="form-selectgroup-item flex-fill">
                                                                    <input type="checkbox" value="{{ $service }}" 
                                                                           class="form-selectgroup-input checkbox-field" 
                                                                           name="{{ $fieldName }}[]"
                                                                           {{ $checked ? 'checked' : '' }}>
                                                                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                                        <div class="me-3">
                                                                            <span class="form-selectgroup-check"></span>
                                                                        </div>
                                                                        <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                            <i class="fas fa-check me-3 text-muted"></i>
                                                                            <div style="text-align: left; width: 100%;">
                                                                                <div class="font-weight-medium">{{ $service }}</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="col-12 mb-3">
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                Bu sektör için henüz tanımlanmış hizmet bulunmuyor. Lütfen 'Eklemek istediğiniz hizmetler' alanını kullanın.
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                @break
                                        @endswitch
                                    </div>
                                    </div> {{-- End of conditional question wrapper --}}
                                    @endif {{-- End of conditional rendering --}}
                                @endforeach
                            @endif
                        </div>
                        
                        {{-- Navigation Buttons - Kompakt Style --}}
                        <div class="form-footer mt-2">
                            {{-- Modern Footer Navigation --}}
                            <div class="modern-footer-container">
                                {{-- Navigation Buttons --}}
                                <div class="navigation-buttons-section">
                                    <div class="d-flex justify-content-between align-items-center">
                                        
                                        {{-- Sol: Önceki Adım Butonu veya Kategorileri Göster (Step 1'de) --}}
                                        <div class="form-footer-left">
                                            <div id="prev-btn-container" style="{{ ($initialStep ?? 1) == 1 ? 'display: none;' : '' }}">
                                                <button type="button" 
                                                        class="btn btn-outline-secondary px-5 py-3 btn-nav-previous"
                                                        id="prev-btn">
                                                    <i class="fas fa-arrow-left me-2"></i>
                                                    <span class="d-none d-sm-inline">Önceki Adım</span>
                                                    <span class="d-sm-none">Önceki</span>
                                                </button>
                                            </div>
                                            
                                            {{-- Step 1'de Kategorileri Göster Butonu --}}
                                            <div id="categories-btn-container" style="{{ ($initialStep ?? 1) == 1 ? '' : 'display: none;' }}">
                                                <button type="button" 
                                                        class="btn btn-outline-info px-5 py-3"
                                                        id="showCategoriesBtn">
                                                    <i class="fas fa-th-large me-2"></i>
                                                    <span class="d-none d-sm-inline">Kategorileri Göster</span>
                                                    <span class="d-sm-none">Kategoriler</span>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        {{-- Sağ: Sonraki Adım / Tamamla Butonu --}}
                                        <div class="form-footer-right">
                                            <div id="next-btn-container" style="{{ ($initialStep ?? 1) < ($totalSteps ?? 5) ? '' : 'display: none;' }}">
                                                <button type="button" 
                                                        class="btn btn-primary px-6 py-3 btn-nav-next" 
                                                        id="next-btn">
                                                    <span class="d-none d-sm-inline">Sonraki Adım</span>
                                                    <span class="d-sm-none">Sonraki</span>
                                                    <i class="fas fa-arrow-right ms-2"></i>
                                                </button>
                                            </div>
                                            
                                            <div id="complete-btn-container" style="{{ ($initialStep ?? 1) == ($totalSteps ?? 5) ? '' : 'display: none;' }}">
                                                <button type="button" 
                                                        class="btn btn-success px-6 py-3" 
                                                        id="complete-btn">
                                                    <i class="fas fa-check me-2"></i>
                                                    <span class="d-none d-sm-inline">Profili Tamamla</span>
                                                    <span class="d-sm-none">Tamamla</span>
                                                </button>
                                            </div>
                                        </div>
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

{{-- Success/Error Messages --}}
<div id="message-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('admin-assets/css/ai-profile-wizard.css') }}">
<style>
/* Page Height Optimization */
.ai-profile-wizard-container {
    min-height: auto !important;
}

body {
    overflow-x: hidden;
}

.container {
    max-width: 100%;
    padding-left: 15px;
    padding-right: 15px;
}

/* Footer kompakt hale getirme */
.form-footer {
    margin-top: 0.5rem !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.modern-footer-container {
    padding: 5px 0 !important;
    margin: 0 !important;
    border-radius: 0 !important;
    background: none !important;
}

/* Buton hover zıplamaları kaldır */
.btn:hover {
    transform: none !important;
}

.btn {
    transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, color 0.15s ease-in-out !important;
}

.navigation-buttons-section {
    margin: 0 !important;
    padding: 0 !important;
}

/* Card body kompakt hale getirme */
.wizard-card .card-body {
    padding: 1rem 1.5rem !important;
}
/* Sector Card Hover Effects */
.sector-card:hover {
    border-color: #0d6efd !important;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15) !important;
}

.sector-radio:checked + .sector-card-label .sector-card {
    border-color: #0d6efd !important;
    background: #f8f9ff !important;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2) !important;
}

/* Search input focus */
#sectorSearch:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
}

/* Category header band */
.category-header-band {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 16px 20px;
    border-left: 4px solid #0d6efd;
}

/* Search results animation */
.search-results-container {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Sector category badge */
.sector-category {
    background: rgba(13, 110, 253, 0.1);
    border-radius: 8px;
    padding: 2px 6px;
    margin-top: 4px;
}

/* Simple search input focus */
#sectorSearch:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
    outline: none;
}

/* Clear button hover */
#clearSearchBtn:hover {
    color: #0d6efd !important;
}

/* Steps Indicator CSS */
.steps-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin: 2rem 0;
    padding: 0 2rem;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none !important;
    color: inherit;
    transition: all 0.3s ease;
    min-width: 60px;
    position: relative;
}

.step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 13px;
    color: #6c757d;
    transition: all 0.3s ease;
    margin-bottom: 6px;
    position: relative;
    z-index: 2;
}

.step-label {
    font-size: 12px;
    font-weight: 500;
    color: #6c757d;
    text-align: center;
    transition: all 0.3s ease;
    line-height: 1.2;
}

.step-connector {
    flex: 1;
    height: 2px;
    background: #e9ecef;
    margin: 0 -1px;
    margin-top: -22px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

/* Active step styles */
.step-item.active .step-circle {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
    transform: scale(1.05);
}

.step-item.active .step-label {
    color: #0d6efd;
    font-weight: 600;
}

/* Completed step styles */
.step-item.completed .step-circle {
    background: #198754;
    border-color: #198754;
    color: white;
}

.step-item.completed .step-label {
    color: #198754;
    font-weight: 600;
}

.step-connector.completed {
    background: #198754;
}

/* Hover effects */
.step-item:hover .step-circle {
    border-color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.2);
}

.step-item:hover .step-label {
    color: #0d6efd;
}

/* Step icon CSS */
.step-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #0d6efd;
    margin-right: 0;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.step-icon:hover {
    transform: scale(1.05);
    border-color: #0d6efd;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
}

.step-icon i {
    animation: float-icon 3s ease-in-out infinite;
}

@keyframes float-icon {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-3px); }
}
</style>
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
    console.log('🚀 DOCUMENT READY - AI Profile Edit JavaScript loaded!');
    
    let currentStep = {{ $initialStep ?? 1 }};
    let profileData = @json($profileData ?? []);
    let currentQuestions = @json($questions ?? []);
    let completionPercentage = {{ $completionPercentage ?? 0 }}; // PHP'den gelen doğru yüzde
    let selectedSectorData = null; // Seçilen sektör bilgilerini saklar
    
    console.log('Initial Step:', currentStep);
    console.log('Profile Data:', profileData);
    console.log('Questions:', currentQuestions);
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
            let selectedSector = $(`.sector-radio[value="${profileData.sector}"]`);
            if (selectedSector.length > 0) {
                // Sector radio'yu check et
                selectedSector.prop('checked', true);
                
                let sectorName = selectedSector.data('sector-name');
                let sectorDesc = selectedSector.data('sector-desc');
                
                // PHP'den gelen veriyi JS ile güncelle (eğer boşsa)
                if (!$('#selectedSectorName').text().trim()) {
                    $('#selectedSectorName').text(sectorName);
                }
                if (!$('#selectedSectorDesc').text().trim()) {
                    $('#selectedSectorDesc').text(sectorDesc);
                }
                
                $('#selectedSectorSection').show();
                $('#searchResults').hide();
                $('#allSectorsContainer').hide();
            } else {
                // Sektör seçilmemişse her şeyi gizle, arama ile açılacak
                $('#selectedSectorSection').hide();
                $('#searchResults').hide();
                $('#allSectorsContainer').hide();
            }
        } else if (currentStep === 1) {
            // Step 1'de ve sektör seçilmemişse her şeyi gizle (arama ile açılacak)
            $('#selectedSectorSection').hide();
            $('#searchResults').hide();
            $('#allSectorsContainer').hide();
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
            console.log('Checkbox custom field:', customField, 'Container found:', container.length > 0);
        });
        
        updateUI();
        bindEvents();
        
        // Debug: Sayfa yüklendiğinde temel bilgiler
        console.log('=== AI PROFILE DEBUG START ===');
        console.log('Current Step:', currentStep);
        console.log('Total custom checkbox triggers:', $('.custom-checkbox-trigger').length);
        console.log('Total checked custom checkbox triggers:', $('.custom-checkbox-trigger:checked').length);
        console.log('Total custom radio triggers:', $('.custom-radio-trigger').length);
        console.log('Total checked custom radio triggers:', $('.custom-radio-trigger:checked').length);
        
        // Debug: Tüm checkbox'ları listele
        console.log('=== ALL CHECKBOXES DEBUG ===');
        $('input[type="checkbox"]').each(function(index) {
            console.log('Checkbox ' + index + ':', {
                name: $(this).attr('name'),
                value: $(this).val(),
                checked: $(this).is(':checked'),
                hasCustomTrigger: $(this).hasClass('custom-checkbox-trigger'),
                customField: $(this).data('custom-field')
            });
        });
        
        // Debug: Tüm radio'ları listele
        console.log('=== ALL RADIOS DEBUG ===');
        $('input[type="radio"]').each(function(index) {
            console.log('Radio ' + index + ':', {
                name: $(this).attr('name'),
                value: $(this).val(),
                checked: $(this).is(':checked'),
                hasCustomTrigger: $(this).hasClass('custom-radio-trigger'),
                customField: $(this).data('custom-field')
            });
        });
        
        console.log('=== AI PROFILE DEBUG END ===');
        
        // Step 1'de sektörleri yükle
        if (currentStep === 1) {
            loadSectorsFromServer();
        }
        
        // Step 3'e geçildiğinde otomatik business activities doldur
        if (currentStep === 3) {
            fillBusinessActivitiesOnStep3();
        }
        
        // Step 4'e geçildiğinde conditional questions'ları initial state'e getir
        if (currentStep === 4) {
            initializeConditionalQuestions();
        }
    }

    // Bu fonksiyon artık kullanılmıyor - sorular PHP'den geliyor

    // Bu fonksiyon artık kullanılmıyor - sorular PHP'den geliyor

    function bindEvents() {
        // Auto-save on change
        $('#questions-container').on('change', 'input, select, textarea', function() {
            let field = $(this).attr('name');
            let value = $(this).val();
            
            // Skip if no field name (like search inputs)
            if (!field || field === '' || $(this).hasClass('search-input') || $(this).attr('id') === 'sectorSearch') {
                return;
            }
            
            // Handle conditional questions (Step 4 founder info)
            if (field === 'share_founder_info') {
                handleConditionalQuestions(field, value);
            }
            
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
        
        // Sector search functionality
        $('#sectorSearch').on('input', function() {
            let searchTerm = $(this).val().toLowerCase().trim();
            
            // Show/hide clear button
            if (searchTerm.length > 0) {
                $('#clearSearchBtn').show();
            } else {
                $('#clearSearchBtn').hide();
            }
            
            if (searchTerm.length >= 1) {
                // Show search results
                $('#searchResults').show();
                $('#allSectorsContainer').hide();
                
                // Filter sectors
                let allSectors = getAllSectors();
                let filteredSectors = allSectors.filter(sector => 
                    sector.name.toLowerCase().includes(searchTerm) ||
                    sector.description.toLowerCase().includes(searchTerm) ||
                    sector.code.toLowerCase().includes(searchTerm)
                );
                
                displaySearchResults(filteredSectors);
            } else {
                // Hide search results
                $('#searchResults').hide();
                $('#allSectorsContainer').hide();
            }
        });
        

        // Show all sectors button (multiple triggers)
        $('#showCategoriesBtn, #showAllFromNoResults').on('click', function() {
            $('#sectorSearch').val(''); // Clear search
            $('#clearSearchBtn').hide(); // Hide clear button
            $('#searchResults').hide();
            $('#noResultsMessage').hide();
            displayAllSectors();
            $('#allSectorsContainer').show();
        });
        
        // Sector selection handling (updated for new structure)
        $(document).on('change', '.sector-radio', function() {
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
                
                // Hide all sector containers
                $('#searchResults').hide();
                $('#allSectorsContainer').hide();
                
                // Save sector selection
                profileData.sector = sectorCode;
                saveField('sector', sectorCode);
                
                // Sektöre göre ana iş kolları textarea'sını doldur
                fillBusinessActivitiesTextarea(sectorCode, sectorName);
            }
        });
        
        // Step navigation click handler
        $('.step-link').on('click', function() {
            let targetStep = $(this).data('step');
            goToStep(targetStep);
        });

        // Change sector button
        $(document).on('click', '#changeSectorBtn', function() {
            $('#selectedSectorSection').hide();
            $('#searchResults').hide();
            $('#allSectorsContainer').hide();
            $('#sectorSearch').val('').focus();
        });
        
        // Clear search button
        $('#clearSearchBtn').on('click', function() {
            $('#sectorSearch').val('').trigger('input').focus();
            $(this).hide();
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
            
            console.log('Checkbox changed:', customField, 'Container found:', container.length > 0);
            
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
                
                // Update global completion percentage from PHP response
                if (response.completion_percentage !== undefined) {
                    completionPercentage = response.completion_percentage;
                }
                
                // Update progress circle with PHP calculated data
                let percentage = response.completion_percentage || calculateDataCompletionPercentage();
                $('#progress-percentage').text(Math.round(percentage) + '%');
                let circumference = 2 * Math.PI * 50;
                let offset = circumference - (percentage / 100) * circumference;
                $('#progress-circle').css('stroke-dashoffset', offset);
                
                // Sadece önemli alanlar için bildirim göster
                if (field !== 'sector') {
                    showSuccess('Kaydedildi');
                }
            } else {
                showError('Kayıt hatası: ' + response.message);
            }
        }).fail(function(xhr, status, error) {
            showError('Bağlantı hatası: ' + error);
        });
    }

    // Navigation
    $('.step-item').click(function(e) {
        e.preventDefault();
        let step = parseInt($(this).find('.step-circle').text());
        if (!isNaN(step)) {
            goToStep(step);
        }
    });

    $('#prev-btn').click(function() {
        if (currentStep > 1) {
            goToStep(currentStep - 1);
        }
    });

    $('#next-btn').click(function() {
        if (currentStep < ({{ $totalSteps ?? 5 }})) {
            goToStep(currentStep + 1);
        }
    });

    $('#complete-btn').click(function() {
        showSuccess('Profil tamamlandı!');
        
        // Kısa bir delay ile redirect
        setTimeout(function() {
            window.location.href = '{{ route("admin.ai.profile.show") }}';
        }, 1000);
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
        
        
        // Update progress ring - JSON data based calculation
        let percentage = calculateDataCompletionPercentage();
        $('#progress-percentage').text(Math.round(percentage) + '%');
        
        // Update progress circle
        let circumference = 2 * Math.PI * 50;
        let offset = circumference - (percentage / 100) * circumference;
        $('#progress-circle').css('stroke-dashoffset', offset);
        
        // Update step navigation
        $('.step-item').removeClass('active');
        $(`.step-item:eq(${currentStep - 1})`).addClass('active');
        
        // Update hologram icon
        $('.ai-hologram i').removeClass().addClass(stepIcons[currentStep]);
        
        // Update navigation buttons (Livewire style)
        if (currentStep === 1) {
            $('#prev-btn-container').hide();
            $('#categories-btn-container').show();
        } else {
            $('#prev-btn-container').show(); 
            $('#categories-btn-container').hide();
        }
        
        if (currentStep < ({{ $totalSteps ?? 5 }})) {
            $('#next-btn-container').show();
            $('#complete-btn-container').hide();
        } else {
            $('#next-btn-container').hide();
            $('#complete-btn-container').show();
        }
    }

    // Helper functions for sector search
    let cachedSectors = null;
    
    function getAllSectors() {
        if (cachedSectors) {
            return cachedSectors;
        }
        
        // Eğer PHP'den sektörler geliyorsa kullan
        @if(isset($sectors) && (is_array($sectors) ? count($sectors) : $sectors->count()) > 0)
            let allSectors = [];
            @foreach($sectors as $mainCategory)
                @foreach($mainCategory->subCategories as $sector)
                    allSectors.push({
                        code: '{{ $sector->code }}',
                        name: '{{ $sector->name }}',
                        description: '{{ $sector->description ?? '' }}',
                        icon: '@if($sector->emoji){{ $sector->emoji }}@elseif($sector->icon){{ $sector->icon }}@else fas fa-briefcase @endif',
                        iconType: '@if($sector->emoji)emoji@elseif($sector->icon)icon@else icon @endif',
                        categoryName: '{{ $mainCategory->name }}'
                    });
                @endforeach
            @endforeach
            cachedSectors = allSectors;
            return allSectors;
        @else
            // Sektörler boşsa AJAX ile yükle
            if (!cachedSectors) {
                loadSectorsFromServer();
            }
            return cachedSectors || [];
        @endif
    }
    
    function loadSectorsFromServer() {
        $.ajax({
            url: '{{ route("admin.ai.profile.get-questions", ["step" => 1]) }}',
            type: 'GET',
            data: {
                sector_code: null
            },
            success: function(response) {
                if (response.success && response.sectors && response.sectors.length > 0) {
                    cachedSectors = [];
                    response.sectors.forEach(function(mainCategory) {
                        if (mainCategory.sub_categories) {
                            mainCategory.sub_categories.forEach(function(sector) {
                                cachedSectors.push({
                                    code: sector.code,
                                    name: sector.name,
                                    description: sector.description || '',
                                    icon: sector.emoji || sector.icon || 'fas fa-briefcase',
                                    iconType: sector.emoji ? 'emoji' : 'icon',
                                    categoryName: mainCategory.name
                                });
                            });
                        }
                    });
                    
                    // Sektörler yüklendikten sonra "Tümünü Göster" butonunu aktif et
                    $('#showAllSectorsBtn').prop('disabled', false);
                    console.log('Sectors loaded from server:', cachedSectors);
                } else {
                    // Sektörler yüklenemezse fallback veri kullan
                    console.warn('No sectors from server, using fallback data');
                    cachedSectors = getFallbackSectors();
                    $('#showAllSectorsBtn').prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error loading sectors:', error);
                // Hata durumunda fallback veri kullan
                cachedSectors = getFallbackSectors();
                $('#showAllSectorsBtn').prop('disabled', false);
            }
        });
    }
    
    function getFallbackSectors() {
        return [
            {
                code: 'web_design',
                name: 'Web Tasarım',
                description: 'Website tasarım, UI/UX',
                icon: 'fas fa-globe',
                iconType: 'icon',
                categoryName: 'Teknoloji'
            },
            {
                code: 'software_development',
                name: 'Yazılım Geliştirme',
                description: 'Mobil ve web uygulamaları',
                icon: 'fas fa-code',
                iconType: 'icon',
                categoryName: 'Teknoloji'
            },
            {
                code: 'digital_marketing',
                name: 'Dijital Pazarlama',
                description: 'SEO, SEM, sosyal medya',
                icon: 'fas fa-bullhorn',
                iconType: 'icon',
                categoryName: 'Pazarlama'
            },
            {
                code: 'graphic_design',
                name: 'Grafik Tasarım',
                description: 'Logo, kurumsal kimlik',
                icon: 'fas fa-palette',
                iconType: 'icon',
                categoryName: 'Tasarım'
            },
            {
                code: 'consulting',
                name: 'Danışmanlık',
                description: 'İş danışmanlığı',
                icon: 'fas fa-handshake',
                iconType: 'icon',
                categoryName: 'Hizmet'
            },
            {
                code: 'e_commerce',
                name: 'E-Ticaret',
                description: 'Online satış',
                icon: 'fas fa-shopping-cart',
                iconType: 'icon',
                categoryName: 'Ticaret'
            }
        ];
    }
    
    function displaySearchResults(sectors) {
        let html = '';
        
        if (sectors.length === 0) {
            // Arama sonucu yok - özel uyarı mesajını göster
            $('#searchResultsGrid').hide();
            $('#noResultsMessage').show();
            return;
        } else {
            // Arama sonuçları var - normal görünümü göster
            $('#searchResultsGrid').show();
            $('#noResultsMessage').hide();
            sectors.forEach(function(sector) {
                let isSelected = profileData.sector === sector.code;
                html += `
                    <div class="col-6 col-md-4 col-lg-3" style="display: flex;">
                        <label class="form-imagecheck mb-2" style="width: 100%; display: flex;">
                            <input type="radio" name="sector_selection" value="${sector.code}" 
                                   class="form-imagecheck-input sector-radio" 
                                   id="search_sector_${sector.code}"
                                   data-sector="${sector.code}"
                                   data-sector-name="${sector.name}"
                                   data-sector-desc="${sector.description}"
                                   ${isSelected ? 'checked' : ''}
                                   data-sector-icon="${sector.icon}"
                                   data-sector-icon-type="${sector.iconType}" required>
                            <span class="form-imagecheck-figure" style="width: 100%; display: flex;">
                                <div class="form-imagecheck-image sector-card d-flex flex-column" 
                                     style="min-height: 140px; height: 140px; width: 100%; flex: 1;"
                                     data-sector="${sector.code}"
                                     data-sector-name="${sector.name}"
                                     data-sector-desc="${sector.description}"
                                     data-sector-icon="${sector.icon}"
                                     data-sector-icon-type="${sector.iconType}">
                                    <div class="sector-icon mb-2" style="min-height: 40px; display: flex; align-items: center; justify-content: center;">
                                        ${sector.iconType === 'emoji' ? 
                                            `<span class="sector-emoji" style="font-size: 24px;">${sector.icon}</span>` :
                                            `<i class="${sector.icon}" style="font-size: 24px;"></i>`
                                        }
                                    </div>
                                    <div class="sector-name text-center fw-bold mb-2" style="font-size: 13px; line-height: 1.2; min-height: 32px; display: flex; align-items: center; justify-content: center;">
                                        ${sector.name}
                                    </div>
                                    <div class="sector-desc text-center flex-grow-1" style="font-size: 11px; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                        ${sector.description}
                                    </div>
                                </div>
                            </span>
                        </label>
                    </div>
                `;
            });
        }
        
        $('#searchResultsGrid').html(html);
    }
    
    function displayAllSectors() {
        let sectors = getAllSectors();
        
        if (sectors.length === 0) {
            $('#allSectorsContainer').html('<div class="text-center text-muted py-4"><i class="fas fa-exclamation-triangle me-2"></i>Sektörler yükleniyor...</div>');
            return;
        }
        
        // Group sectors by category
        let categories = {};
        sectors.forEach(function(sector) {
            if (!categories[sector.categoryName]) {
                categories[sector.categoryName] = [];
            }
            categories[sector.categoryName].push(sector);
        });
        
        let html = '';
        Object.keys(categories).forEach(function(categoryName) {
            html += `
                <div class="col-12 mb-3">
                    <div class="category-header-band">
                        <div class="d-flex align-items-center">
                            <div class="category-icon me-3">
                                <i class="fas fa-folder category-icon-style"></i>
                            </div>
                            <div class="category-title">
                                <h5 class="mb-0 fw-bold text-primary">${categoryName}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-4">
            `;
            
            categories[categoryName].forEach(function(sector) {
                let isSelected = profileData.sector === sector.code;
                html += `
                    <div class="col-6 col-md-4 col-lg-3" style="display: flex;">
                        <label class="form-imagecheck mb-2" style="width: 100%; display: flex;">
                            <input type="radio" name="sector_selection" value="${sector.code}" 
                                   class="form-imagecheck-input sector-radio" 
                                   id="all_sector_${sector.code}"
                                   data-sector="${sector.code}"
                                   data-sector-name="${sector.name}"
                                   data-sector-desc="${sector.description}"
                                   ${isSelected ? 'checked' : ''}
                                   data-sector-icon="${sector.icon}"
                                   data-sector-icon-type="${sector.iconType}" required>
                            <span class="form-imagecheck-figure" style="width: 100%; display: flex;">
                                <div class="form-imagecheck-image sector-card d-flex flex-column" 
                                     style="min-height: 140px; height: 140px; width: 100%; flex: 1;"
                                     data-sector="${sector.code}"
                                     data-sector-name="${sector.name}"
                                     data-sector-desc="${sector.description}"
                                     data-sector-icon="${sector.icon}"
                                     data-sector-icon-type="${sector.iconType}">
                                    <div class="sector-icon mb-2" style="min-height: 40px; display: flex; align-items: center; justify-content: center;">
                                        ${sector.iconType === 'emoji' ? 
                                            `<span class="sector-emoji" style="font-size: 24px;">${sector.icon}</span>` :
                                            `<i class="${sector.icon}" style="font-size: 24px;"></i>`
                                        }
                                    </div>
                                    <div class="sector-name text-center fw-bold mb-2" style="font-size: 13px; line-height: 1.2; min-height: 32px; display: flex; align-items: center; justify-content: center;">
                                        ${sector.name}
                                    </div>
                                    <div class="sector-desc text-center flex-grow-1" style="font-size: 11px; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                        ${sector.description}
                                    </div>
                                </div>
                            </span>
                        </label>
                    </div>
                `;
            });
            
            html += '</div>';
        });
        
        $('#allSectorsContainer').html(html);
    }

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

    // PHP'den gelen doğru completion percentage'ını döndür
    function calculateDataCompletionPercentage() {
        // PHP modelinden hesaplanan doğru yüzdeyi kullan
        return completionPercentage;
    }

    // Sektöre göre ana iş kolları textarea'sını otomatik doldur
    function fillBusinessActivitiesTextarea(sectorCode, sectorName) {
        // Sektör bazlı ana iş kolları mapping
        const sectorActivities = {
            'web_design': 'Website tasarımı, UI/UX tasarım, Logo tasarımı, Responsive tasarım, E-ticaret siteleri, Kurumsal web siteleri',
            'software_development': 'Mobil uygulama geliştirme, Web uygulaması geliştirme, Yazılım danışmanlığı, Sistem entegrasyonu, API geliştirme, Veritabanı yönetimi',
            'digital_marketing': 'SEO optimizasyonu, Google Ads yönetimi, Sosyal medya pazarlaması, İçerik pazarlaması, Email pazarlaması, Influencer pazarlama',
            'graphic_design': 'Logo tasarımı, Kurumsal kimlik tasarımı, Basılı materyal tasarımı, Ambalaj tasarımı, İllüstrasyon, Marka rehberi hazırlama',
            'consulting': 'İş danışmanlığı, Strateji danışmanlığı, Süreç optimizasyonu, Eğitim ve koçluk, Proje yönetimi, Pazarlama danışmanlığı',
            'e_commerce': 'Online mağaza kurulumu, Ürün yönetimi, Pazaryeri entegrasyonu, Ödeme sistemi kurulumu, Lojistik çözümleri, Müşteri hizmetleri',
            'education': 'Online eğitim, Yüz yüze eğitim, Kurumsal eğitim, Sertifika programları, Eğitim danışmanlığı, Müfredat geliştirme',
            'health': 'Sağlık danışmanlığı, Beslenme danışmanlığı, Fizik tedavi, Psikolog desteği, Wellness koçluğu, Tıbbi muayene',
            'construction': 'İnşaat projesi yürütme, Mimari tasarım, İç mimari, Tadilat işleri, Peyzaj düzenlemesi, Proje yönetimi',
            'food_service': 'Restoran işletmeciliği, Catering hizmeti, Fast food, Organik gıda, Yemek servisi, Özel etkinlik yemekleri',
            'automotive': 'Araç satışı, Araç bakım-onarım, Yedek parça satışı, Lastik-jant, Araç ekspertizi, Sigorta işlemleri',
            'beauty': 'Kuaförlük hizmeti, Güzellik bakımı, Cilt bakımı, Makyaj hizmeti, Masaj terapisi, Estetik danışmanlık',
            'fitness': 'Kişisel antrenörlük, Grup dersleri, Beslenme koçluğu, Spor salonu işletme, Online fitness, Spor malzemesi satışı',
            'finance': 'Muhasebe hizmeti, Mali müşavirlik, Vergi danışmanlığı, Finansal planlama, Kredi danışmanlığı, Sigorta aracılığı',
            'legal': 'Hukuki danışmanlık, Dava takibi, Sözleşme hazırlama, Arabuluculuk, Noterlik işlemleri, Fikri mülkiyet'
        };

        // Sektöre göre aktiviteleri al
        let activities = sectorActivities[sectorCode];
        
        // Eğer mapping'de yoksa sektör adından çıkar
        if (!activities && sectorName) {
            activities = extractActivitiesFromSectorName(sectorName);
        }
        
        // Son çare genel aktiviteler
        if (!activities) {
            activities = 'Müşteri danışmanlığı, Satış ve pazarlama, Proje yönetimi, Müşteri hizmetleri, Eğitim ve seminer, Teknik destek';
        }

        // Seçilen sektör verilerini global olarak sakla
        selectedSectorData = {
            code: sectorCode,
            name: sectorName,
            activities: activities
        };

        console.log('🎯 Sektör seçildi:', sectorCode, '| Activities hazırlandı:', activities);
        console.log('📊 Selected sector data saved globally');

        // Eğer şu anda Step 3'teysek hemen doldur
        let textarea = $('textarea[name="main_business_activities"]');
        if (textarea.length > 0 && !textarea.val().trim()) {
            textarea.val(activities);
            saveField('main_business_activities', activities);
            console.log('✅ Ana iş kolları ANINDA dolduruldu (Step 3 aktif)');
        } else {
            console.log('⏳ Textarea henüz yok - Step 3\'e geçince otomatik dolacak');
        }
    }

    // Step 3'e geçildiğinde business activities'i otomatik doldur
    function fillBusinessActivitiesOnStep3() {
        // Seçili sektör var mı kontrol et
        if (!profileData.sector) {
            console.log('⚠️ Sektör seçilmemiş, business activities doldurulamadı');
            return;
        }
        
        setTimeout(function() {
            let textarea = $('textarea[name="main_business_activities"]');
            if (textarea.length > 0 && !textarea.val().trim()) {
                // Sektör kodu var, activities'i oluştur
                fillBusinessActivitiesTextarea(profileData.sector, 'Seçili Sektör');
                console.log('🎯 Step 3\'te otomatik business activities dolduruldu');
            } else if (textarea.length > 0) {
                console.log('ℹ️ Business activities zaten dolu, değiştirilmedi');
            } else {
                console.log('❌ main_business_activities textarea bulunamadı');
            }
        }, 200); // Step render edilmesini bekle
    }

    // Initialize conditional questions on Step 4 load
    function initializeConditionalQuestions() {
        // Check current share_founder_info value
        const shareFounderValue = profileData.share_founder_info || '';
        console.log('🔄 Initializing conditional questions, share_founder_info:', shareFounderValue);
        console.log('📊 Full profileData:', profileData);
        
        // Debug: Check if conditional question elements exist
        const founderFields = ['founder_name', 'founder_role', 'founder_additional_info'];
        founderFields.forEach(function(fieldName) {
            const fieldContainer = $(`.conditional-question[data-question-key="${fieldName}"]`);
            console.log(`🔍 Field ${fieldName} container found:`, fieldContainer.length > 0);
        });
        
        // Apply initial state
        handleConditionalQuestions('share_founder_info', shareFounderValue);
    }

    // Conditional questions handling (Step 4 founder info)
    function handleConditionalQuestions(field, value) {
        console.log('🔄 Conditional question changed:', field, '=', value);
        
        if (field === 'share_founder_info') {
            // Founder info fields to show/hide
            const founderFields = ['founder_name', 'founder_role', 'founder_additional_info'];
            
            founderFields.forEach(function(fieldName) {
                const fieldContainer = $(`.conditional-question[data-question-key="${fieldName}"]`);
                
                if (value === 'evet') {
                    // Show founder fields with CSS class and smooth animation
                    fieldContainer.addClass('show').slideDown(300);
                    console.log('✅ Showing founder field:', fieldName);
                } else {
                    // Hide founder fields with smooth animation
                    fieldContainer.removeClass('show').slideUp(300);
                    // Clear field values when hiding
                    fieldContainer.find('input, textarea, select').val('');
                    console.log('❌ Hiding founder field:', fieldName);
                }
            });
        }
    }

    // Sektör adından aktivite çıkarma
    function extractActivitiesFromSectorName(sectorName) {
        const name = sectorName.toLowerCase();
        
        // Teknoloji ve Bilişim
        if (name.includes('teknoloji') || name.includes('bilişim') || name.includes('yazılım') || name.includes('it')) {
            return 'Teknoloji danışmanlığı, Yazılım geliştirme, Sistem kurulumu, IT destek hizmetleri, Veri yönetimi, Siber güvenlik';
        }
        
        // Sağlık ve Tıp
        if (name.includes('sağlık') || name.includes('tıp') || name.includes('medikal') || name.includes('hastane') || name.includes('doktor')) {
            return 'Sağlık hizmeti, Tıbbi danışmanlık, Hasta bakımı, Medikal destek hizmetleri, Sağlık kontrolü, Tedavi süreçleri';
        }
        
        // Eğitim ve Öğretim
        if (name.includes('eğitim') || name.includes('öğretim') || name.includes('okul') || name.includes('kurs') || name.includes('akademi')) {
            return 'Eğitim hizmeti, Kurs ve seminer, Özel ders, Eğitim danışmanlığı, Müfredat geliştirme, Sertifika programları';
        }
        
        // İnşaat ve Yapı
        if (name.includes('inşaat') || name.includes('yapı') || name.includes('mimari') || name.includes('tadilat') || name.includes('dekorasyon')) {
            return 'İnşaat işleri, Tadilat, Mimari hizmet, Proje yönetimi, Dekorasyon, Peyzaj düzenlemesi';
        }
        
        // Gıda ve Yemek
        if (name.includes('gıda') || name.includes('yemek') || name.includes('restoran') || name.includes('catering') || name.includes('aşçı')) {
            return 'Gıda üretimi, Yemek servisi, Catering, Restoran işletmeciliği, Mutfak danışmanlığı, Beslenme hizmeti';
        }
        
        // Turizm ve Otel
        if (name.includes('turizm') || name.includes('otel') || name.includes('tatil') || name.includes('rezervasyon') || name.includes('rehber')) {
            return 'Turizm rehberliği, Otel işletmeciliği, Tur organizasyonu, Rezervasyon hizmetleri, Tatil planlaması, Konaklama hizmetleri';
        }
        
        // Finans ve Mali
        if (name.includes('finans') || name.includes('mali') || name.includes('muhasebe') || name.includes('vergi') || name.includes('banka')) {
            return 'Mali müşavirlik, Muhasebe hizmeti, Vergi danışmanlığı, Finansal planlama, Kredi danışmanlığı, Bütçe yönetimi';
        }
        
        // Hukuk ve Yasal
        if (name.includes('hukuk') || name.includes('avukat') || name.includes('yasal') || name.includes('mahkeme') || name.includes('dava')) {
            return 'Hukuki danışmanlık, Dava takibi, Sözleşme hazırlama, Arabuluculuk, Yasal süreç yönetimi, Fikri mülkiyet';
        }
        
        // Spor ve Fitness
        if (name.includes('spor') || name.includes('fitness') || name.includes('antrenör') || name.includes('jimnastik') || name.includes('yoga')) {
            return 'Kişisel antrenörlük, Grup dersleri, Beslenme koçluğu, Spor salonu işletme, Online fitness, Spor malzemesi satışı';
        }
        
        // Güzellik ve Bakım
        if (name.includes('güzellik') || name.includes('kuaför') || name.includes('berber') || name.includes('estetik') || name.includes('cilt')) {
            return 'Kuaförlük hizmeti, Güzellik bakımı, Cilt bakımı, Makyaj hizmeti, Masaj terapisi, Estetik danışmanlık';
        }
        
        // Otomotiv
        if (name.includes('otomotiv') || name.includes('araba') || name.includes('araç') || name.includes('lastik') || name.includes('motor')) {
            return 'Araç satışı, Araç bakım-onarım, Yedek parça satışı, Lastik-jant, Araç ekspertizi, Sigorta işlemleri';
        }
        
        // Medya ve Reklam
        if (name.includes('medya') || name.includes('reklam') || name.includes('pazarlama') || name.includes('tasarım') || name.includes('ajans')) {
            return 'Reklam tasarımı, Pazarlama danışmanlığı, Sosyal medya yönetimi, İçerik üretimi, Marka geliştirme, Dijital pazarlama';
        }
        
        // Üretim ve Sanayi
        if (name.includes('üretim') || name.includes('sanayi') || name.includes('fabrika') || name.includes('imalat') || name.includes('makine')) {
            return 'Üretim planlama, Sanayi danışmanlığı, Makine bakımı, Kalite kontrol, Lojistik yönetimi, Süreç optimizasyonu';
        }
        
        // Taşımacılık ve Lojistik
        if (name.includes('taşımacılık') || name.includes('lojistik') || name.includes('kargo') || name.includes('nakliye') || name.includes('depo')) {
            return 'Taşımacılık hizmeti, Lojistik yönetimi, Kargo servisi, Nakliye organizasyonu, Depo yönetimi, Dağıtım hizmetleri';
        }
        
        // Emlak ve Gayrimenkul
        if (name.includes('emlak') || name.includes('gayrimenkul') || name.includes('satış') || name.includes('kiralama') || name.includes('arsa')) {
            return 'Emlak danışmanlığı, Gayrimenkul satışı, Kiralama hizmetleri, Emlak değerlendirmesi, Yatırım danışmanlığı, Proje geliştirme';
        }
        
        // Temizlik ve Bakım
        if (name.includes('temizlik') || name.includes('bakım') || name.includes('hijyen') || name.includes('sanitasyon') || name.includes('peyzaj')) {
            return 'Temizlik hizmeti, Bakım onarım, Hijyen hizmetleri, Sanitasyon, Peyzaj bakımı, Teknik servis';
        }
        
        // Güvenlik
        if (name.includes('güvenlik') || name.includes('koruma') || name.includes('alarm') || name.includes('kamera') || name.includes('servis')) {
            return 'Güvenlik hizmeti, Koruma hizmetleri, Alarm sistemleri, Kamera güvenliği, Güvenlik danışmanlığı, Risk yönetimi';
        }
        
        // Sanat ve Kültür
        if (name.includes('sanat') || name.includes('kültür') || name.includes('müzik') || name.includes('tiyatro') || name.includes('sinema')) {
            return 'Sanat eğitimi, Kültürel etkinlik, Müzik dersi, Tiyatro oyunları, Sinema prodüksiyonu, Sanat danışmanlığı';
        }
        
        // Tarım ve Gıda
        if (name.includes('tarım') || name.includes('çiftçi') || name.includes('hayvancılık') || name.includes('sera') || name.includes('organik')) {
            return 'Tarım danışmanlığı, Çiftçilik hizmetleri, Hayvancılık, Sera işletmeciliği, Organik üretim, Tarımsal sulama';
        }
        
        return null; // Fallback'e düş
    }
    
});
</script>

{{-- CSS Stiller - Livewire uyumlu navigation --}}
<style>
/* Modern Footer Design */
.form-footer {
    padding: 0.5rem 0 !important;
    margin-top: 0.5rem !important;
}

.modern-footer-container {
    background: none !important;
    border-radius: 0 !important;
    padding: 5px 0 !important;
    position: relative;
    overflow: hidden;
}

.modern-footer-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(13, 110, 253, 0.2), transparent);
}

.footer-search-section {
    text-align: center;
}

.search-wrapper {
    position: relative;
    max-width: none;
}

.modern-search-input {
    height: 60px !important;
    font-size: 18px !important;
    font-weight: 500 !important;
    padding-left: 4rem !important;
    padding-right: 4rem !important;
    border-radius: 30px !important;
    border: 2px solid var(--tblr-border-color) !important;
    background: var(--tblr-bg-surface) !important;
    color: var(--tblr-body-color) !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
}

.modern-search-input:focus {
    border-color: var(--tblr-primary) !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15), 0 6px 20px rgba(0, 0, 0, 0.1) !important;
    outline: 0 !important;
    transform: translateY(-2px);
}

.modern-search-input::placeholder {
    color: var(--tblr-body-color) !important;
    font-weight: 400 !important;
    opacity: 0.7 !important;
}

.search-icon {
    position: absolute;
    left: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--tblr-body-color);
    font-size: 20px;
    z-index: 5;
    opacity: 0.6;
}

.search-clear-btn {
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--tblr-body-color);
    font-size: 18px;
    cursor: pointer;
    z-index: 5;
    opacity: 0.6;
    transition: all 0.2s ease;
}

.search-clear-btn:hover {
    opacity: 1;
    color: var(--tblr-danger);
}

.navigation-buttons-section {
    margin-top: 0.5rem !important;
    padding-top: 0.5rem !important;
    border-top: 1px solid rgba(var(--tblr-border-color-rgb), 0.3);
}

.form-footer .btn-nav-previous:hover {
    transform: none !important;
    background-color: var(--tblr-secondary);
    color: white;
    border-color: var(--tblr-secondary);
    box-shadow: 0 2px 8px rgba(108, 117, 125, 0.15);
}

.form-footer .btn-nav-next:hover {
    transform: none !important;
    background-color: var(--tblr-primary);
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.25);
}

.form-footer .btn:hover {
    transform: none !important;
    transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, color 0.15s ease-in-out !important;
}

.no-results-message {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Conditional questions styling */
.conditional-question {
    transition: all 0.3s ease;
}

.conditional-question[data-depends-on] {
    /* Hidden by default if depends on something */
    display: none;
}

.conditional-question[data-depends-on].show {
    /* Show when JavaScript adds show class */
    display: block !important;
}

.question-label {
    font-weight: 500;
    margin-bottom: 8px;
}

.form-group {
    margin-bottom: 1.5rem;
}
</style>
@endpush