@extends('admin.layout')

@section('title', $feature->name . ' - Input Yönetimi')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">
                AI Feature Management
            </div>
            <h2 class="page-title">
                {{ $feature->name }} - Form Yapısı
            </h2>
            <div class="text-muted mt-1">
                Feature ID: #{{ $feature->id }} • Slug: {{ $feature->slug }}
            </div>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('admin.ai.features.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Geri Dön
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInputModal">
                    <i class="fa-solid fa-plus"></i>
                    Yeni Input Ekle
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Primary Input Section --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="card-title">Ana Input (Primary)</h3>
                        <p class="card-subtitle">Bu input accordion dışında görüntülenir ve ana kullanıcı girdisidir</p>
                    </div>
                    <div class="col-auto">
                        @if($feature->primaryInput)
                            <span class="badge bg-success">Tanımlanmış</span>
                        @else
                            <span class="badge bg-warning">Henüz Tanımlanmamış</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($feature->primaryInput)
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <strong>{{ $feature->primaryInput->label }}</strong>
                                @if($feature->primaryInput->is_required)
                                    <span class="text-danger">*</span>
                                @endif
                            </div>
                            <div class="text-muted mb-2">
                                <strong>Tip:</strong> {{ ucfirst($feature->primaryInput->input_type) }}
                            </div>
                            @if($feature->primaryInput->placeholder)
                                <div class="text-muted mb-2">
                                    <strong>Placeholder:</strong> {{ $feature->primaryInput->placeholder }}
                                </div>
                            @endif
                            @if($feature->primaryInput->help_text)
                                <div class="text-muted">
                                    <strong>Açıklama:</strong> {{ $feature->primaryInput->help_text }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-list">
                                <button class="btn btn-sm btn-outline-primary edit-input-btn" 
                                        data-input-id="{{ $feature->primaryInput->id }}">
                                    <i class="fa-solid fa-pen"></i>
                                    Düzenle
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-input-btn" 
                                        data-input-id="{{ $feature->primaryInput->id }}">
                                    <i class="fa-solid fa-trash"></i>
                                    Sil
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="empty">
                        <div class="empty-img">
                            <i class="fa-solid fa-clipboard-list" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                        <p class="empty-title">Ana Input Tanımlanmamış</p>
                        <p class="empty-subtitle text-muted">
                            Bu feature için henüz ana input tanımlanmamış. Ana input kullanıcıların ilk göreceği temel girdi alanıdır.
                        </p>
                        <div class="empty-action">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInputModal" data-is-primary="true">
                                <i class="fa-solid fa-plus"></i>
                                Ana Input Oluştur
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Secondary Inputs Section (Grouped) --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="card-title">İleri Düzey Ayarlar</h3>
                        <p class="card-subtitle">Bu inputlar accordion içinde gruplandırılmış şekilde görüntülenir</p>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-info">{{ $feature->inputs->where('is_primary', false)->count() }} Input</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @php
                    $secondaryInputs = $feature->inputs->where('is_primary', false)->sortBy('display_order');
                @endphp
                
                @if($secondaryInputs->count() > 0)
                    <div id="inputs-list" class="sortable-list">
                        @foreach($secondaryInputs as $input)
                            <div class="input-item card mb-3" data-input-id="{{ $input->id }}">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="drag-handle" style="cursor: move;">
                                                <i class="ti ti-grip-vertical text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-1">
                                                <strong>{{ $input->label }}</strong>
                                                @if($input->is_required)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small">
                                                <span class="badge bg-light text-dark">{{ ucfirst($input->input_type) }}</span>
                                                @if($input->group_key)
                                                    <span class="badge bg-light text-dark">{{ $input->group_key }}</span>
                                                @endif
                                                @if($input->input_key)
                                                    <code class="small">{{ $input->input_key }}</code>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            @if($input->placeholder)
                                                <div class="text-muted small">
                                                    {{ Str::limit($input->placeholder, 50) }}
                                                </div>
                                            @endif
                                            @if($input->options->count() > 0)
                                                <div class="text-muted small">
                                                    {{ $input->options->count() }} seçenek
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-list">
                                                @if($input->input_type === 'select' || $input->input_type === 'radio' || $input->input_type === 'checkbox')
                                                    <button class="btn btn-sm btn-outline-info manage-options-btn" 
                                                            data-input-id="{{ $input->id }}">
                                                        <i class="ti ti-settings"></i>
                                                        Seçenekler
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-outline-primary edit-input-btn" 
                                                        data-input-id="{{ $input->id }}">
                                                    <i class="fa-solid fa-pen"></i>
                                                    Düzenle
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary duplicate-input-btn" 
                                                        data-input-id="{{ $input->id }}">
                                                    <i class="ti ti-copy"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-input-btn" 
                                                        data-input-id="{{ $input->id }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty">
                        <div class="empty-img">
                            <i class="ti ti-settings" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                        <p class="empty-title">İleri Düzey Input Yok</p>
                        <p class="empty-subtitle text-muted">
                            Bu feature için henüz ileri düzey input tanımlanmamış. Bu inputlar kullanıcılara ek seçenekler sunar.
                        </p>
                        <div class="empty-action">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addInputModal" data-is-primary="false">
                                <i class="fa-solid fa-plus"></i>
                                İlk Input'u Ekle
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Add Input Modal --}}
<div class="modal fade" id="addInputModal" tabindex="-1" aria-labelledby="addInputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addInputForm" action="{{ route('admin.ai.features.inputs.store', $feature->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addInputModalLabel">Yeni Input Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="input_key" class="form-label">Input Key <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="input_key" name="input_key" required 
                                   placeholder="örn: topic, writing_style">
                            <div class="form-text">Programatik olarak kullanılacak anahtar (benzersiz olmalı)</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="input_type" class="form-label">Input Tipi <span class="text-danger">*</span></label>
                            <select class="form-select" id="input_type" name="input_type" required>
                                <option value="">Tip Seçin</option>
                                <option value="textarea">Textarea (Uzun Metin)</option>
                                <option value="text">Text (Kısa Metin)</option>
                                <option value="select">Select (Dropdown)</option>
                                <option value="radio">Radio (Tek Seçim)</option>
                                <option value="checkbox">Checkbox (Çoklu Seçim)</option>
                                <option value="range">Range (Kaydırıcı)</option>
                                <option value="number">Number (Sayı)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="label" name="label" required 
                                   placeholder="Kullanıcının göreceği etiket">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="group_key" class="form-label">Grup</label>
                            <input type="text" class="form-control" id="group_key" name="group_key" 
                                   placeholder="örn: advanced, seo, content">
                            <div class="form-text">Boş bırakılırsa 'advanced' kullanılır</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="placeholder" class="form-label">Placeholder</label>
                        <input type="text" class="form-control" id="placeholder" name="placeholder" 
                               placeholder="Kullanıcı için ipucu metni">
                    </div>
                    
                    <div class="mb-3">
                        <label for="help_text" class="form-label">Yardım Metni</label>
                        <textarea class="form-control" id="help_text" name="help_text" rows="2" 
                                  placeholder="Bu input hakkında açıklayıcı bilgi"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="default_value" class="form-label">Varsayılan Değer</label>
                        <input type="text" class="form-control" id="default_value" name="default_value" 
                               placeholder="Önceden doldurulacak değer">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1">
                                <label class="form-check-label" for="is_required">
                                    Zorunlu Alan
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                                <label class="form-check-label" for="is_primary">
                                    Ana Input (Primary)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="validation-rules" class="mb-3" style="display: none;">
                        <label for="validation_rules_text" class="form-label">Validation Kuralları</label>
                        <input type="text" class="form-control" id="validation_rules_text" name="validation_rules_text" 
                               placeholder="örn: min:5,max:100">
                        <div class="form-text">Virgül ile ayırarak Laravel validation kurallarını yazın</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i>
                        Input Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Input Modal --}}
<div class="modal fade" id="editInputModal" tabindex="-1" aria-labelledby="editInputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editInputForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editInputModalLabel">Input Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form alanları addInputModal ile aynı --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_input_key" class="form-label">Input Key <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_input_key" name="input_key" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_input_type" class="form-label">Input Tipi <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_input_type" name="input_type" required>
                                <option value="textarea">Textarea (Uzun Metin)</option>
                                <option value="text">Text (Kısa Metin)</option>
                                <option value="select">Select (Dropdown)</option>
                                <option value="radio">Radio (Tek Seçim)</option>
                                <option value="checkbox">Checkbox (Çoklu Seçim)</option>
                                <option value="range">Range (Kaydırıcı)</option>
                                <option value="number">Number (Sayı)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_label" class="form-label">Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_label" name="label" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_group_key" class="form-label">Grup</label>
                            <input type="text" class="form-control" id="edit_group_key" name="group_key">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_placeholder" class="form-label">Placeholder</label>
                        <input type="text" class="form-control" id="edit_placeholder" name="placeholder">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_help_text" class="form-label">Yardım Metni</label>
                        <textarea class="form-control" id="edit_help_text" name="help_text" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_default_value" class="form-label">Varsayılan Değer</label>
                        <input type="text" class="form-control" id="edit_default_value" name="default_value">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_required" name="is_required" value="1">
                                <label class="form-check-label" for="edit_is_required">Zorunlu Alan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_primary" name="is_primary" value="1">
                                <label class="form-check-label" for="edit_is_primary">Ana Input (Primary)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check"></i>
                        Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Options Management Modal --}}
<div class="modal fade" id="manageOptionsModal" tabindex="-1" aria-labelledby="manageOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageOptionsModalLabel">Seçenek Yönetimi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Mevcut Seçenekler</h6>
                        <div id="options-list">
                            {{-- JavaScript ile doldurulacak --}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Yeni Seçenek Ekle</h6>
                        <form id="addOptionForm">
                            <div class="mb-3">
                                <label for="option_value" class="form-label">Değer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="option_value" name="option_value" required>
                            </div>
                            <div class="mb-3">
                                <label for="option_label" class="form-label">Label <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="option_label" name="option_label" required>
                            </div>
                            <div class="mb-3">
                                <label for="prompt_id" class="form-label">Bağlı Prompt</label>
                                <select class="form-select" id="prompt_id" name="prompt_id">
                                    <option value="">Prompt Seçin (Opsiyonel)</option>
                                    @foreach($availablePrompts as $prompt)
                                        <option value="{{ $prompt->id }}">{{ $prompt->title }} ({{ $prompt->type }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-plus"></i>
                                Seçenek Ekle
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // Sortable functionality for inputs
    $("#inputs-list").sortable({
        handle: '.drag-handle',
        items: '.input-item',
        axis: 'y',
        update: function(event, ui) {
            var order = [];
            $('.input-item').each(function(index) {
                order.push({
                    id: $(this).data('input-id'),
                    order: index + 1
                });
            });
            
            // Send AJAX request to update order
            $.post('{{ route("admin.ai.features.inputs.update-order", $feature->id) }}', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                order: order
            }).done(function(response) {
                if (response.success) {
                    toastr.success('Sıralama güncellendi');
                }
            }).fail(function() {
                toastr.error('Sıralama güncellenirken hata oluştu');
            });
        }
    });
    
    // Add Input Modal
    $('#addInputModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var isPrimary = button.data('is-primary');
        
        if (isPrimary === true) {
            $('#is_primary').prop('checked', true);
            $('#addInputModalLabel').text('Ana Input Oluştur');
        } else if (isPrimary === false) {
            $('#is_primary').prop('checked', false);
            $('#addInputModalLabel').text('Yeni Input Ekle');
        }
    });
    
    // Input type change - show/hide validation rules
    $('#input_type, #edit_input_type').change(function() {
        var type = $(this).val();
        if (type === 'text' || type === 'textarea' || type === 'number') {
            $('#validation-rules').show();
        } else {
            $('#validation-rules').hide();
        }
    });
    
    // Edit Input
    $('.edit-input-btn').click(function() {
        var inputId = $(this).data('input-id');
        
        // Load input data via AJAX
        $.get('/admin/ai/features/{{ $feature->id }}/inputs/' + inputId)
            .done(function(input) {
                // Populate edit form
                $('#edit_input_key').val(input.input_key);
                $('#edit_input_type').val(input.input_type);
                $('#edit_label').val(input.label);
                $('#edit_group_key').val(input.group_key);
                $('#edit_placeholder').val(input.placeholder);
                $('#edit_help_text').val(input.help_text);
                $('#edit_default_value').val(input.default_value);
                $('#edit_is_required').prop('checked', input.is_required);
                $('#edit_is_primary').prop('checked', input.is_primary);
                
                // Set form action
                $('#editInputForm').attr('action', '/admin/ai/features/{{ $feature->id }}/inputs/' + inputId);
                
                // Show modal
                $('#editInputModal').modal('show');
            })
            .fail(function() {
                toastr.error('Input verileri yüklenirken hata oluştu');
            });
    });
    
    // Delete Input
    $('.delete-input-btn').click(function() {
        var inputId = $(this).data('input-id');
        
        if (confirm('Bu input\'u silmek istediğinizden emin misiniz?')) {
            $.ajax({
                url: '/admin/ai/features/{{ $feature->id }}/inputs/' + inputId,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(response) {
                if (response.success) {
                    toastr.success('Input silindi');
                    location.reload();
                }
            }).fail(function() {
                toastr.error('Input silinirken hata oluştu');
            });
        }
    });
    
    // Duplicate Input
    $('.duplicate-input-btn').click(function() {
        var inputId = $(this).data('input-id');
        
        $.post('/admin/ai/features/{{ $feature->id }}/inputs/duplicate/' + inputId, {
            _token: $('meta[name="csrf-token"]').attr('content')
        }).done(function(response) {
            if (response.success) {
                toastr.success('Input kopyalandı');
                location.reload();
            }
        }).fail(function() {
            toastr.error('Input kopyalanırken hata oluştu');
        });
    });
    
    // Manage Options
    $('.manage-options-btn').click(function() {
        var inputId = $(this).data('input-id');
        
        // Load options via AJAX
        $.get('/admin/ai/features/{{ $feature->id }}/inputs/' + inputId + '/options')
            .done(function(data) {
                // Populate options list
                var optionsHtml = '';
                data.options.forEach(function(option) {
                    optionsHtml += `
                        <div class="option-item mb-2 p-2 border rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${option.option_label}</strong>
                                    <small class="text-muted">(${option.option_value})</small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger delete-option-btn" 
                                        data-option-id="${option.id}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                $('#options-list').html(optionsHtml);
                
                // Set current input ID for option form
                $('#addOptionForm').data('input-id', inputId);
                
                // Show modal
                $('#manageOptionsModal').modal('show');
            })
            .fail(function() {
                toastr.error('Seçenekler yüklenirken hata oluştu');
            });
    });
    
    // Add Option
    $('#addOptionForm').submit(function(e) {
        e.preventDefault();
        
        var inputId = $(this).data('input-id');
        var formData = $(this).serialize();
        
        $.post('/admin/ai/features/{{ $feature->id }}/inputs/' + inputId + '/options', formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'))
            .done(function(response) {
                if (response.success) {
                    toastr.success('Seçenek eklendi');
                    // Reload options list
                    $('.manage-options-btn[data-input-id="' + inputId + '"]').click();
                    // Clear form
                    $('#addOptionForm')[0].reset();
                }
            })
            .fail(function() {
                toastr.error('Seçenek eklenirken hata oluştu');
            });
    });
    
    // Delete Option
    $(document).on('click', '.delete-option-btn', function() {
        var optionId = $(this).data('option-id');
        
        if (confirm('Bu seçeneği silmek istediğinizden emin misiniz?')) {
            $.ajax({
                url: '/admin/ai/options/' + optionId,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(response) {
                if (response.success) {
                    toastr.success('Seçenek silindi');
                    $(`.delete-option-btn[data-option-id="${optionId}"]`).closest('.option-item').remove();
                }
            }).fail(function() {
                toastr.error('Seçenek silinirken hata oluştu');
            });
        }
    });
    
    // Form submissions
    $('#addInputForm').submit(function(e) {
        e.preventDefault();
        
        $.post($(this).attr('action'), $(this).serialize())
            .done(function(response) {
                if (response.success) {
                    toastr.success('Input eklendi');
                    location.reload();
                }
            })
            .fail(function(xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    Object.keys(errors).forEach(function(key) {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('Input eklenirken hata oluştu');
                }
            });
    });
    
    $('#editInputForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'PUT',
            data: $(this).serialize()
        }).done(function(response) {
            if (response.success) {
                toastr.success('Input güncellendi');
                location.reload();
            }
        }).fail(function(xhr) {
            var errors = xhr.responseJSON.errors;
            if (errors) {
                Object.keys(errors).forEach(function(key) {
                    toastr.error(errors[key][0]);
                });
            } else {
                toastr.error('Input güncellenirken hata oluştu');
            }
        });
    });
});
</script>
@endpush