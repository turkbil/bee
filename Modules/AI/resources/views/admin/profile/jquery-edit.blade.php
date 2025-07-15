@extends('admin.layouts.master')

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
                        {{-- Loading --}}
                        <div id="loading" class="loading-container" style="display: none;">
                            <div class="loading-spinner">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Yükleniyor...</span>
                                </div>
                            </div>
                            <p class="loading-text">Sorular yükleniyor...</p>
                        </div>

                        {{-- Questions Container --}}
                        <div id="questions-container" class="questions-container">
                            <!-- Questions will be loaded here -->
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
$(document).ready(function() {
    let currentStep = 1;
    let profileData = {};
    let currentQuestions = [];
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
        loadProfileData();
        loadQuestions(currentStep);
        updateUI();
    }

    function loadProfileData() {
        $.get('{{ route("admin.ai.profile.get-profile-data") }}', function(response) {
            if (response.success) {
                profileData = response.profile_data;
                console.log('Profile data loaded:', profileData);
            }
        });
    }

    function loadQuestions(step) {
        showLoading();
        
        let url = '{{ route("admin.ai.profile.get-questions", ":step") }}'.replace(':step', step);
        let params = {};
        
        if (step === 3 && profileData.sector) {
            params.sector_code = profileData.sector;
        }
        
        $.get(url, params, function(response) {
            if (response.success) {
                currentQuestions = response.questions;
                renderQuestions(response.questions, response.sectors);
                hideLoading();
            } else {
                showError('Sorular yüklenirken hata: ' + response.message);
                hideLoading();
            }
        });
    }

    function renderQuestions(questions, sectors = null) {
        let html = '';
        
        // Sektörler (Step 1)
        if (sectors && sectors.length > 0) {
            html += '<div class="wizard-form-group mb-4">';
            html += '<div class="question-wrapper">';
            html += '<label for="sector" class="form-label question-label">Sektör Seçimi <span class="text-danger">*</span></label>';
            html += '<select class="form-select form-select-lg" id="sector" name="sector" required>';
            html += '<option value="">Sektör seçin...</option>';
            
            sectors.forEach(function(sector) {
                let selected = profileData.sector === sector.code ? 'selected' : '';
                html += `<option value="${sector.code}" ${selected}>${sector.name}</option>`;
            });
            
            html += '</select>';
            html += '</div></div>';
        }
        
        // Questions
        questions.forEach(function(question) {
            html += renderQuestion(question);
        });
        
        $('#questions-container').html(html);
        
        // Bind events
        bindEvents();
    }

    function renderQuestion(question) {
        let html = '<div class="wizard-form-group mb-4">';
        let fieldName = question.question_key;
        let value = profileData[fieldName] || '';
        
        // Question wrapper
        html += '<div class="question-wrapper">';
        
        // Label
        html += `<label for="${fieldName}" class="form-label question-label">
            ${question.question_text}
            ${question.is_required ? ' <span class="text-danger">*</span>' : ''}
        </label>`;
        
        // Input based on type
        switch (question.input_type) {
            case 'text':
                html += `<input type="text" class="form-control form-control-lg" id="${fieldName}" name="${fieldName}" value="${value}" ${question.is_required ? 'required' : ''}>`;
                break;
                
            case 'textarea':
                html += `<textarea class="form-control form-control-lg" id="${fieldName}" name="${fieldName}" rows="4" ${question.is_required ? 'required' : ''}>${value}</textarea>`;
                break;
                
            case 'select':
                html += `<select class="form-select form-select-lg" id="${fieldName}" name="${fieldName}" ${question.is_required ? 'required' : ''}>`;
                html += '<option value="">Seçin...</option>';
                
                let options = typeof question.options === 'string' ? JSON.parse(question.options) : question.options;
                if (options) {
                    options.forEach(function(option) {
                        let optionValue = typeof option === 'object' ? option.value : option;
                        let optionLabel = typeof option === 'object' ? option.label : option;
                        let selected = value === optionValue ? 'selected' : '';
                        html += `<option value="${optionValue}" ${selected}>${optionLabel}</option>`;
                    });
                }
                html += '</select>';
                break;
                
            case 'radio':
                html += '<div class="radio-group">';
                let radioOptions = typeof question.options === 'string' ? JSON.parse(question.options) : question.options;
                if (radioOptions) {
                    radioOptions.forEach(function(option) {
                        let optionValue = typeof option === 'object' ? option.value : option;
                        let optionLabel = typeof option === 'object' ? option.label : option;
                        let checked = value === optionValue ? 'checked' : '';
                        html += `<div class="form-check form-check-lg">
                            <input class="form-check-input" type="radio" name="${fieldName}" id="${fieldName}_${optionValue}" value="${optionValue}" ${checked}>
                            <label class="form-check-label" for="${fieldName}_${optionValue}">${optionLabel}</label>
                        </div>`;
                    });
                }
                html += '</div>';
                break;
                
            case 'checkbox':
                html += '<div class="checkbox-group">';
                let checkboxOptions = typeof question.options === 'string' ? JSON.parse(question.options) : question.options;
                if (checkboxOptions) {
                    checkboxOptions.forEach(function(option) {
                        let optionValue = typeof option === 'object' ? option.value : option;
                        let optionLabel = typeof option === 'object' ? option.label : option;
                        let checked = profileData[fieldName + '.' + optionValue] ? 'checked' : '';
                        html += `<div class="form-check form-check-lg">
                            <input class="form-check-input checkbox-field" type="checkbox" name="${fieldName}[]" id="${fieldName}_${optionValue}" value="${optionValue}" ${checked}>
                            <label class="form-check-label" for="${fieldName}_${optionValue}">${optionLabel}</label>
                        </div>`;
                    });
                }
                html += '</div>';
                break;
        }
        
        html += '</div></div>';
        return html;
    }

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
            
            saveField(field, value);
        });
        
        // Sector change - reload step 3 questions
        $('#questions-container').on('change', '#sector', function() {
            profileData.sector = $(this).val();
            if (currentStep === 1) {
                showSuccess('Sektör seçildi. Adım 3\'te sektöre özel sorular yüklenecek.');
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
        currentStep = step;
        loadQuestions(step);
        updateUI();
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

    function showLoading() {
        $('#loading').show();
        $('#questions-container').hide();
    }

    function hideLoading() {
        $('#loading').hide();
        $('#questions-container').show();
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
});
</script>
@endpush