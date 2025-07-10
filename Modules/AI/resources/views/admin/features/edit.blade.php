@extends('admin.layout')

@section('title', 'AI Feature Düzenle')

@include('ai::helper')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.ai.features.index') }}">AI Features</a></li>
                        <li class="breadcrumb-item active">{{ $feature->name }}</li>
                    </ol>
                </nav>
                <h2 class="page-title">
                    <i class="fas fa-edit me-2"></i>
                    AI Feature Düzenle
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('admin.ai.features.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Geri Dön
                    </a>
                    @if(!$feature->is_system)
                        <button class="btn btn-outline-danger" id="delete-feature-btn">
                            <i class="fas fa-trash me-1"></i>
                            Sil
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Hata!</strong> Aşağıdaki alanları kontrol edin:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('admin.ai.features.update', $feature->id) }}" method="POST" id="feature-form">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Sol Kolon: Temel Bilgiler -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Temel Bilgiler</h3>
                            <div class="card-actions">
                                <span class="badge bg-{{ $feature->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $statuses[$feature->status] ?? $feature->status }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Feature Adı</label>
                                        <input type="text" name="name" class="form-control" 
                                               placeholder="Örn: Blog Yazısı Oluşturma" 
                                               value="{{ old('name', $feature->name) }}" 
                                               required {{ $feature->is_system ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Slug</label>
                                        <input type="text" name="slug" class="form-control" 
                                               placeholder="Örn: blog-post-generator" 
                                               value="{{ old('slug', $feature->slug) }}" 
                                               required {{ $feature->is_system ? 'readonly' : '' }}>
                                        <small class="form-hint">URL'de kullanılacak benzersiz tanımlayıcı</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Açıklama</label>
                                <textarea name="description" class="form-control" rows="3" 
                                          placeholder="Bu AI feature'ının ne yaptığını açıklayın..." 
                                          required>{{ old('description', $feature->description) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">Emoji</label>
                                        <input type="text" name="emoji" class="form-control" 
                                               placeholder="🤖" 
                                               value="{{ old('emoji', $feature->emoji) }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">FontAwesome Icon</label>
                                        <input type="text" name="icon" class="form-control" 
                                               placeholder="fas fa-robot" 
                                               value="{{ old('icon', $feature->icon) }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">Badge Rengi</label>
                                        <select name="badge_color" class="form-select">
                                            <option value="primary" {{ old('badge_color', $feature->badge_color) == 'primary' ? 'selected' : '' }}>Primary (Mavi)</option>
                                            <option value="success" {{ old('badge_color', $feature->badge_color) == 'success' ? 'selected' : '' }}>Success (Yeşil)</option>
                                            <option value="warning" {{ old('badge_color', $feature->badge_color) == 'warning' ? 'selected' : '' }}>Warning (Sarı)</option>
                                            <option value="danger" {{ old('badge_color', $feature->badge_color) == 'danger' ? 'selected' : '' }}>Danger (Kırmızı)</option>
                                            <option value="info" {{ old('badge_color', $feature->badge_color) == 'info' ? 'selected' : '' }}>Info (Açık Mavi)</option>
                                            <option value="secondary" {{ old('badge_color', $feature->badge_color) == 'secondary' ? 'selected' : '' }}>Secondary (Gri)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Input Placeholder</label>
                                <input type="text" name="input_placeholder" class="form-control" 
                                       placeholder="Kullanıcıların göreceği placeholder metni..." 
                                       value="{{ old('input_placeholder', $feature->input_placeholder) }}">
                            </div>

                            <!-- Usage Stats -->
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">Kullanım Sayısı</label>
                                        <input type="number" class="form-control" 
                                               value="{{ $feature->usage_count }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">Ortalama Puan</label>
                                        <input type="text" class="form-control" 
                                               value="{{ number_format($feature->avg_rating, 1) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">Puan Veren Sayısı</label>
                                        <input type="number" class="form-control" 
                                               value="{{ $feature->rating_count }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Prompt Kartı -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Custom Prompt</h3>
                            <div class="card-actions">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable-custom-prompt" 
                                           {{ $feature->custom_prompt ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable-custom-prompt">
                                        Custom Prompt Kullan
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="custom-prompt-area" style="display: {{ $feature->custom_prompt ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label class="form-label">Prompt İçeriği</label>
                                <textarea name="custom_prompt" class="form-control" rows="10" 
                                          placeholder="You are a professional AI assistant...">{{ old('custom_prompt', $feature->custom_prompt) }}</textarea>
                                <small class="form-hint">Bu feature için özel AI prompt'u yazın. Boş bırakılırsa varsayılan sistem prompt'u kullanılır.</small>
                            </div>
                            
                            <div class="alert alert-info">
                                <h4 class="alert-title">Prompt Yazma İpuçları:</h4>
                                <ul class="mb-0">
                                    <li><strong>TASK:</strong> AI'ın ne yapması gerektiğini net olarak belirtin</li>
                                    <li><strong>OUTPUT:</strong> Çıktı formatını detaylı tanımlayın</li>
                                    <li><strong>LANGUAGE:</strong> "Write in Turkish" ekleyin</li>
                                    <li><strong>FORBIDDEN:</strong> Yapmaması gerekenleri listeleyin</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Helper Function Kartı -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Helper Function</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Helper Function Adı</label>
                                <input type="text" name="helper_function" class="form-control" 
                                       placeholder="ai_feature_blog_generator" 
                                       value="{{ old('helper_function', $feature->helper_function) }}">
                                <small class="form-hint">PHP'de çağrılacak helper function adı (isteğe bağlı)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon: Ayarlar -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ayarlar</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Kategori</label>
                                <select name="category" class="form-select" required>
                                    <option value="">Kategori Seçin</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category', $feature->category) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Zorluk Seviyesi</label>
                                <select name="complexity_level" class="form-select" required>
                                    @foreach($complexityLevels as $key => $label)
                                        <option value="{{ $key }}" {{ old('complexity_level', $feature->complexity_level) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Durum</label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ old('status', $feature->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status', $feature->status) == 'inactive' ? 'selected' : '' }}>Pasif</option>
                                    <option value="beta" {{ old('status', $feature->status) == 'beta' ? 'selected' : '' }}>Beta</option>
                                    <option value="planned" {{ old('status', $feature->status) == 'planned' ? 'selected' : '' }}>Planlanmış</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sıra</label>
                                <input type="number" name="sort_order" class="form-control" 
                                       placeholder="999" 
                                       value="{{ old('sort_order', $feature->sort_order) }}" 
                                       min="1" max="9999">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Yanıt Uzunluğu</label>
                                <select name="response_length" class="form-select">
                                    <option value="short" {{ old('response_length', $feature->response_length) == 'short' ? 'selected' : '' }}>Kısa</option>
                                    <option value="medium" {{ old('response_length', $feature->response_length) == 'medium' ? 'selected' : '' }}>Orta</option>
                                    <option value="long" {{ old('response_length', $feature->response_length) == 'long' ? 'selected' : '' }}>Uzun</option>
                                    <option value="variable" {{ old('response_length', $feature->response_length) == 'variable' ? 'selected' : '' }}>Değişken</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Yanıt Formatı</label>
                                <select name="response_format" class="form-select">
                                    <option value="text" {{ old('response_format', $feature->response_format) == 'text' ? 'selected' : '' }}>Düz Metin</option>
                                    <option value="markdown" {{ old('response_format', $feature->response_format) == 'markdown' ? 'selected' : '' }}>Markdown</option>
                                    <option value="structured" {{ old('response_format', $feature->response_format) == 'structured' ? 'selected' : '' }}>Yapılandırılmış</option>
                                    <option value="code" {{ old('response_format', $feature->response_format) == 'code' ? 'selected' : '' }}>Kod</option>
                                    <option value="list" {{ old('response_format', $feature->response_format) == 'list' ? 'selected' : '' }}>Liste</option>
                                </select>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" 
                                           id="is_featured" {{ old('is_featured', $feature->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Öne Çıkan Feature
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="show_in_examples" value="1" 
                                           id="show_in_examples" {{ old('show_in_examples', $feature->show_in_examples) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_in_examples">
                                        Örnekler Sayfasında Göster
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requires_input" value="1" 
                                           id="requires_input" {{ old('requires_input', $feature->requires_input) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_input">
                                        Kullanıcı Input'u Gerekli
                                    </label>
                                </div>
                            </div>

                            @if($feature->is_system)
                                <div class="alert alert-warning">
                                    <i class="fas fa-lock me-2"></i>
                                    <strong>Sistem Feature'ı:</strong> Bu feature sistem tarafından korunmaktadır.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- JSON Ayarları -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Gelişmiş Ayarlar</h3>
                            <div class="card-actions">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable-advanced"
                                           {{ $feature->additional_config || $feature->usage_examples || $feature->input_validation ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable-advanced">
                                        Gelişmiş Ayarları Göster
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="advanced-area" style="display: {{ $feature->additional_config || $feature->usage_examples || $feature->input_validation ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label class="form-label">Additional Config (JSON)</label>
                                <textarea name="additional_config" class="form-control" rows="4" 
                                          placeholder='{"template_selection": true}'>{{ old('additional_config', $feature->additional_config) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Usage Examples (JSON)</label>
                                <textarea name="usage_examples" class="form-control" rows="3" 
                                          placeholder='{"basic": "function_name()", "advanced": "function_name(params)"}'>{{ old('usage_examples', $feature->usage_examples) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Input Validation (JSON)</label>
                                <textarea name="input_validation" class="form-control" rows="3" 
                                          placeholder='{"title": "required|string|max:255"}'>{{ old('input_validation', $feature->input_validation) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.ai.features.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    İptal
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success" name="action" value="save">
                                    <i class="fas fa-save me-1"></i>
                                    Güncelle
                                </button>
                                <button type="submit" class="btn btn-primary" name="action" value="save_and_continue">
                                    <i class="fas fa-save me-1"></i>
                                    Güncelle ve Düzenlemeye Devam Et
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Feature Silme Onayı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu AI Feature'ını silmek istediğinizden emin misiniz?</p>
                <div class="alert alert-warning">
                    <strong>{{ $feature->name }}</strong> feature'ı kalıcı olarak silinecek ve geri getirilemeyecek.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Sil</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from name (only if not system feature)
    @if(!$feature->is_system)
    $('input[name="name"]').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
            .replace(/\s+/g, '-') // Replace spaces with hyphens
            .replace(/-+/g, '-') // Replace multiple hyphens with single
            .trim('-'); // Remove leading/trailing hyphens
        $('input[name="slug"]').val(slug);
    });
    @endif

    // Toggle custom prompt area
    $('#enable-custom-prompt').change(function() {
        $('#custom-prompt-area').toggle($(this).is(':checked'));
    });

    // Toggle advanced area
    $('#enable-advanced').change(function() {
        $('#advanced-area').toggle($(this).is(':checked'));
    });

    // Form validation
    $('#feature-form').submit(function(e) {
        let isValid = true;
        const requiredFields = ['name', 'slug', 'description', 'category', 'complexity_level', 'status'];
        
        requiredFields.forEach(function(field) {
            const input = $(`[name="${field}"]`);
            if (!input.val().trim()) {
                input.addClass('is-invalid');
                isValid = false;
            } else {
                input.removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            toastr.error('Lütfen tüm zorunlu alanları doldurun.');
        }
    });

    // Remove validation errors on input
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });

    // Delete feature
    $('#delete-feature-btn').click(function() {
        $('#deleteModal').modal('show');
    });
    
    $('#confirm-delete').click(function() {
        const form = $('<form>', {
            'method': 'POST',
            'action': '{{ route("admin.ai.features.destroy", $feature->id) }}'
        });
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_method',
            'value': 'DELETE'
        }));
        
        $('body').append(form);
        form.submit();
    });
});
</script>
@endpush

@push('styles')
<style>
.form-label.required::after {
    content: " *";
    color: #e74c3c;
}

.is-invalid {
    border-color: #e74c3c;
}

.card-actions .form-check {
    margin-bottom: 0;
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.alert-title {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

#advanced-area textarea,
#custom-prompt-area textarea {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 13px;
}
</style>
@endpush