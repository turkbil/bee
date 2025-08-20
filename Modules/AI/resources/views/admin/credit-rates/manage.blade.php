@extends('admin.layout')

@section('title', 'Model Kredi Oranları Yönetimi')

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">AI Kredi Sistemi</div>
                    <h2 class="page-title">Model Kredi Oranları Yönetimi</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.ai.credit-rates.calculator') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                <rect x="8" y="7" width="8" height="10" rx="1"></rect>
                                <path d="m8 11 2 2l4 -4"></path>
                            </svg>
                            Kredi Hesaplayıcı
                        </a>
                        <button class="btn btn-warning" id="import-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                <path d="M12 11v6"></path>
                                <path d="m9 14 3 -3 3 3"></path>
                            </svg>
                            Toplu İçe Aktar
                        </button>
                        <button class="btn btn-success" id="export-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                <path d="M12 17v-6"></path>
                                <path d="m15 14 -3 3 -3 -3"></path>
                            </svg>
                            Dışa Aktar
                        </button>
                        <a href="{{ route('admin.ai.credit-rates.index') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <polyline points="9,6 15,12 9,18"></polyline>
                            </svg>
                            Ana Sayfaya Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Provider Selection -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Provider Seçimi</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">AI Provider</label>
                                    <select class="form-select" id="provider-select">
                                        <option value="">Provider seçiniz...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Model</label>
                                    <select class="form-select" id="model-select" disabled>
                                        <option value="">Önce provider seçiniz</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Model Credit Rate Form -->
            <div class="row">
                <div class="col-12">
                    <div class="card" id="rate-form-card" style="display: none;">
                        <div class="card-header">
                            <h3 class="card-title">Model Kredi Oranı Ayarları</h3>
                            <div class="card-actions">
                                <span id="current-model-info" class="badge bg-blue"></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="rate-form">
                                <input type="hidden" id="provider-id">
                                <input type="hidden" id="model-name">
                                
                                <div class="row">
                                    <!-- Input Token Rate -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Input Token Oranı
                                            <span class="text-muted">(1000 token başına kredi)</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="input-rate" placeholder="0.00">
                                            <span class="input-group-text">kredi/1K token</span>
                                        </div>
                                        <small class="form-hint">Giriş token'ları için kredi oranı</small>
                                    </div>

                                    <!-- Output Token Rate -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Output Token Oranı
                                            <span class="text-muted">(1000 token başına kredi)</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="output-rate" placeholder="0.00">
                                            <span class="input-group-text">kredi/1K token</span>
                                        </div>
                                        <small class="form-hint">Çıkış token'ları için kredi oranı</small>
                                    </div>

                                    <!-- Markup Percentage -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Markup Yüzdesi
                                            <span class="text-muted">(opsiyonel)</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.1" class="form-control" id="markup-percentage" placeholder="0.0">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="form-hint">Temel orana ek markup oranı</small>
                                    </div>

                                    <!-- Base Cost Per Request -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            İstek Başına Temel Maliyet
                                            <span class="text-muted">(opsiyonel)</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.001" class="form-control" id="base-cost" placeholder="0.000">
                                            <span class="input-group-text">kredi/istek</span>
                                        </div>
                                        <small class="form-hint">Her istek için sabit ek maliyet</small>
                                    </div>

                                    <!-- Active Status -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Durum</label>
                                        <div>
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is-active" checked>
                                                <span class="form-check-label">Aktif</span>
                                            </label>
                                        </div>
                                        <small class="form-hint">Bu model için kredi hesaplama aktif/pasif</small>
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-12 mb-3">
                                        <label class="form-label">
                                            Notlar
                                            <span class="text-muted">(opsiyonel)</span>
                                        </label>
                                        <textarea class="form-control" id="notes" rows="3" placeholder="Bu model hakkında notlar..."></textarea>
                                    </div>
                                </div>

                                <!-- Cost Preview -->
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h4 class="card-title">Maliyet Önizlemesi</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-2">
                                                    <strong>1K Input Token:</strong>
                                                    <span id="preview-input" class="text-primary">0 kredi</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-2">
                                                    <strong>1K Output Token:</strong>
                                                    <span id="preview-output" class="text-primary">0 kredi</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-2">
                                                    <strong>Örnek İstek (500/500):</strong>
                                                    <span id="preview-sample" class="text-success">0 kredi</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-2">
                                                    <strong>Toplam Markup:</strong>
                                                    <span id="preview-markup" class="text-warning">0%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-danger" id="delete-btn" style="display: none;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <line x1="4" y1="7" x2="20" y2="7"></line>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                            <path d="m5 7 1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                            <path d="m9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                        </svg>
                                        Sil
                                    </button>
                                    <div>
                                        <button type="button" class="btn btn-secondary me-2" id="reset-btn">
                                            Sıfırla
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M5 12l5 5l10 -10"></path>
                                            </svg>
                                            Kaydet
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Rates Table -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Mevcut Kredi Oranları</h3>
                            <div class="card-actions">
                                <div class="form-selectgroup form-selectgroup-pills">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="status-filter" value="all" class="form-selectgroup-input" checked>
                                        <span class="form-selectgroup-label">Tümü</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="status-filter" value="active" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">Aktif</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="status-filter" value="inactive" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">Pasif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table" id="rates-table">
                                    <thead>
                                        <tr>
                                            <th>Provider</th>
                                            <th>Model</th>
                                            <th>Input Rate</th>
                                            <th>Output Rate</th>
                                            <th>Markup</th>
                                            <th>Base Cost</th>
                                            <th>Durum</th>
                                            <th>Son Güncelleme</th>
                                            <th class="w-1">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rates-table-body">
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                <div class="empty">
                                                    <div class="empty-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-loader" width="128" height="128" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <line x1="12" y1="6" x2="12" y2="3"></line>
                                                            <line x1="16.25" y1="7.75" x2="18.4" y2="5.6"></line>
                                                            <line x1="18" y1="12" x2="21" y2="12"></line>
                                                            <line x1="16.25" y1="16.25" x2="18.4" y2="18.4"></line>
                                                            <line x1="12" y1="18" x2="12" y2="21"></line>
                                                            <line x1="7.75" y1="16.25" x2="5.6" y2="18.4"></line>
                                                            <line x1="6" y1="12" x2="3" y2="12"></line>
                                                            <line x1="7.75" y1="7.75" x2="5.6" y2="5.6"></line>
                                                        </svg>
                                                    </div>
                                                    <p class="empty-title">Kredi oranları yükleniyor...</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal modal-blur fade" id="import-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Toplu Kredi Oranı İçe Aktarma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="import-form" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">CSV Dosyası</label>
                        <input type="file" class="form-control" id="import-file" accept=".csv" required>
                        <small class="form-hint">
                            CSV formatı: provider_name, model_name, input_rate, output_rate, markup_percentage, base_cost_per_request, is_active, notes
                        </small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="update-existing">
                            <label class="form-check-label">
                                Mevcut kayıtları güncelle
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h4>CSV Format Örneği:</h4>
                        <code>
                            OpenAI,gpt-4,3.0,6.0,10.0,0.001,true,"GPT-4 modeli"<br>
                            Anthropic,claude-3-haiku,1.0,2.0,5.0,0.0,true,"Hızlı model"
                        </code>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="submit" form="import-form" class="btn btn-primary">İçe Aktar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
}
.card.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let providers = [];
    let currentRateId = null;
    
    // Load initial data
    loadProviders();
    loadRatesTable();
    
    // Provider selection change
    $('#provider-select').change(function() {
        const providerId = $(this).val();
        if (providerId) {
            loadModels(providerId);
        } else {
            $('#model-select').prop('disabled', true).html('<option value="">Önce provider seçiniz</option>');
            hideRateForm();
        }
    });
    
    // Model selection change
    $('#model-select').change(function() {
        const modelName = $(this).val();
        const providerId = $('#provider-select').val();
        
        if (modelName && providerId) {
            loadExistingRate(providerId, modelName);
            showRateForm();
        } else {
            hideRateForm();
        }
    });
    
    // Form input changes for preview
    $('#input-rate, #output-rate, #markup-percentage, #base-cost').on('input', updatePreview);
    
    // Form submission
    $('#rate-form').submit(function(e) {
        e.preventDefault();
        saveRate();
    });
    
    // Reset button
    $('#reset-btn').click(function() {
        resetForm();
        updatePreview();
    });
    
    // Delete button
    $('#delete-btn').click(function() {
        if (currentRateId && confirm('Bu kredi oranını silmek istediğinizden emin misiniz?')) {
            deleteRate(currentRateId);
        }
    });
    
    // Import/Export buttons
    $('#import-btn').click(function() {
        $('#import-modal').modal('show');
    });
    
    $('#export-btn').click(function() {
        exportRates();
    });
    
    // Import form
    $('#import-form').submit(function(e) {
        e.preventDefault();
        importRates();
    });
    
    // Status filter
    $('input[name="status-filter"]').change(function() {
        loadRatesTable();
    });
    
    // Functions
    function loadProviders() {
        $.get('/admin/ai/credit-rates/api/providers-models')
            .done(function(response) {
                if (response.success) {
                    providers = response.data;
                    let options = '<option value="">Provider seçiniz...</option>';
                    response.data.forEach(provider => {
                        options += `<option value="${provider.id}">${provider.name}</option>`;
                    });
                    $('#provider-select').html(options);
                } else {
                    toastr.error(response.message);
                }
            })
            .fail(function() {
                toastr.error('Provider\'lar yüklenirken hata oluştu.');
            });
    }
    
    function loadModels(providerId) {
        $('#model-select').prop('disabled', true).html('<option value="">Yükleniyor...</option>');
        
        $.get(`/admin/ai/credit-rates/api/provider/${providerId}/models`)
            .done(function(response) {
                if (response.success) {
                    let options = '<option value="">Model seçiniz...</option>';
                    response.data.models.forEach(model => {
                        options += `<option value="${model.name}">${model.name}</option>`;
                    });
                    $('#model-select').prop('disabled', false).html(options);
                } else {
                    $('#model-select').html('<option value="">Model yüklenemedi</option>');
                    toastr.error(response.message);
                }
            })
            .fail(function() {
                $('#model-select').html('<option value="">Model yüklenemedi</option>');
                toastr.error('Modeller yüklenirken hata oluştu.');
            });
    }
    
    function loadExistingRate(providerId, modelName) {
        // Check if rate exists
        $.get('/admin/ai/credit-rates/api/calculate-cost', {
                provider_id: providerId,
                model_name: modelName,
                input_tokens: 1000,
                output_tokens: 1000
            })
            .done(function(response) {
                if (response.success && response.data.existing_rate) {
                    fillForm(response.data.existing_rate);
                    currentRateId = response.data.existing_rate.id;
                    $('#delete-btn').show();
                } else {
                    resetForm();
                    currentRateId = null;
                    $('#delete-btn').hide();
                }
                updateCurrentModelInfo();
            })
            .fail(function() {
                resetForm();
                currentRateId = null;
                $('#delete-btn').hide();
                updateCurrentModelInfo();
            });
    }
    
    function fillForm(rate) {
        $('#provider-id').val(rate.provider_id);
        $('#model-name').val(rate.model_name);
        $('#input-rate').val(rate.input_cost || rate.input_rate || 0);
        $('#output-rate').val(rate.output_cost || rate.output_rate || 0);
        $('#markup-percentage').val(rate.markup_percentage || 0);
        $('#base-cost').val(rate.base_cost_usd || rate.base_cost_per_request || 0);
        $('#is-active').prop('checked', rate.is_active);
        $('#notes').val(rate.notes || '');
        updatePreview();
    }
    
    function resetForm() {
        const providerId = $('#provider-select').val();
        const modelName = $('#model-select').val();
        
        $('#rate-form')[0].reset();
        $('#provider-id').val(providerId);
        $('#model-name').val(modelName);
        $('#is-active').prop('checked', true);
        currentRateId = null;
        $('#delete-btn').hide();
    }
    
    function updateCurrentModelInfo() {
        const providerName = $('#provider-select option:selected').text();
        const modelName = $('#model-select').val();
        $('#current-model-info').text(`${providerName} - ${modelName}`);
    }
    
    function updatePreview() {
        const inputRate = parseFloat($('#input-rate').val()) || 0;
        const outputRate = parseFloat($('#output-rate').val()) || 0;
        const markup = parseFloat($('#markup-percentage').val()) || 0;
        const baseCost = parseFloat($('#base-cost').val()) || 0;
        
        const finalInputRate = inputRate * (1 + markup / 100);
        const finalOutputRate = outputRate * (1 + markup / 100);
        
        $('#preview-input').text(`${finalInputRate.toFixed(3)} kredi`);
        $('#preview-output').text(`${finalOutputRate.toFixed(3)} kredi`);
        
        const sampleCost = (finalInputRate * 0.5) + (finalOutputRate * 0.5) + baseCost;
        $('#preview-sample').text(`${sampleCost.toFixed(3)} kredi`);
        $('#preview-markup').text(`${markup}%`);
    }
    
    function showRateForm() {
        $('#rate-form-card').slideDown();
    }
    
    function hideRateForm() {
        $('#rate-form-card').slideUp();
        currentRateId = null;
    }
    
    function saveRate() {
        const formData = {
            provider_id: $('#provider-id').val(),
            model_name: $('#model-name').val(),
            input_rate: $('#input-rate').val(),
            output_rate: $('#output-rate').val(),
            markup_percentage: $('#markup-percentage').val() || null,
            base_cost_per_request: $('#base-cost').val() || null,
            is_active: $('#is-active').is(':checked'),
            notes: $('#notes').val() || null
        };
        
        const url = currentRateId ? `/admin/ai/credit-rates/api/${currentRateId}` : '/admin/ai/credit-rates/api/store';
        const method = currentRateId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(data) {
            toastr.success(currentRateId ? 'Kredi oranı güncellendi.' : 'Kredi oranı kaydedildi.');
            loadRatesTable();
            if (!currentRateId) {
                currentRateId = data.id;
                $('#delete-btn').show();
            }
        })
        .fail(function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                Object.values(errors).flat().forEach(error => {
                    toastr.error(error);
                });
            } else {
                toastr.error('Kaydetme sırasında hata oluştu.');
            }
        });
    }
    
    function deleteRate(id) {
        $.ajax({
            url: `/admin/ai/credit-rates/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function() {
            toastr.success('Kredi oranı silindi.');
            loadRatesTable();
            resetForm();
            currentRateId = null;
            $('#delete-btn').hide();
        })
        .fail(function() {
            toastr.error('Silme sırasında hata oluştu.');
        });
    }
    
    function loadRatesTable() {
        const status = $('input[name="status-filter"]:checked').val();
        
        $.get('/admin/ai/credit-rates/api/index', { status: status })
            .done(function(response) {
                let tbody = '';
                let data = response.data || response; // Handle both API formats
                
                if (data.length === 0) {
                    tbody = `
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-database-off" width="128" height="128" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <ellipse cx="12" cy="6" rx="8" ry="3"></ellipse>
                                            <path d="M4 6v6c0 1.657 3.582 3 8 3s8 -1.343 8 -3v-6"></path>
                                            <path d="M4 12v6c0 1.657 3.582 3 8 3s8 -1.343 8 -3v-6"></path>
                                        </svg>
                                    </div>
                                    <p class="empty-title">Henüz kredi oranı bulunmuyor</p>
                                    <p class="empty-subtitle text-muted">Yukarıdaki formdan yeni kredi oranı ekleyebilirsiniz.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                } else {
                    data.forEach(rate => {
                        const statusBadge = rate.is_active 
                            ? '<span class="badge bg-success">Aktif</span>'
                            : '<span class="badge bg-danger">Pasif</span>';
                            
                        tbody += `
                            <tr>
                                <td><strong>${rate.provider_name || (rate.provider ? rate.provider.name : 'N/A')}</strong></td>
                                <td><code>${rate.model_name}</code></td>
                                <td>${parseFloat(rate.input_cost || rate.input_rate || 0).toFixed(3)}</td>
                                <td>${parseFloat(rate.output_cost || rate.output_rate || 0).toFixed(3)}</td>
                                <td>${rate.markup_percentage ? rate.markup_percentage + '%' : '-'}</td>
                                <td>${rate.base_cost_per_request ? parseFloat(rate.base_cost_per_request).toFixed(3) : '-'}</td>
                                <td>${statusBadge}</td>
                                <td>${new Date(rate.updated_at).toLocaleDateString('tr-TR')}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editRate(${rate.provider_id}, '${rate.model_name}')">
                                        Düzenle
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                
                $('#rates-table-body').html(tbody);
            })
            .fail(function() {
                toastr.error('Kredi oranları yüklenirken hata oluştu.');
            });
    }
    
    function exportRates() {
        window.location.href = '/admin/ai/credit-rates/api/export';
    }
    
    function importRates() {
        const formData = new FormData();
        formData.append('file', $('#import-file')[0].files[0]);
        formData.append('update_existing', $('#update-existing').is(':checked'));
        
        $.ajax({
            url: '/admin/ai/credit-rates/api/import',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(data) {
            toastr.success(`${data.imported} kredi oranı başarıyla içe aktarıldı.`);
            $('#import-modal').modal('hide');
            loadRatesTable();
        })
        .fail(function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                Object.values(errors).flat().forEach(error => {
                    toastr.error(error);
                });
            } else {
                toastr.error('İçe aktarma sırasında hata oluştu.');
            }
        });
    }
    
    // Global function for table edit buttons
    window.editRate = function(providerId, modelName) {
        $('#provider-select').val(providerId).change();
        setTimeout(() => {
            $('#model-select').val(modelName).change();
        }, 500);
    };
});
</script>
@endpush