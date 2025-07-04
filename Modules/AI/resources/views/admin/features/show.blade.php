@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('title', $feature->name . ' - AI Özelliği')

@push('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.ai.index') }}">AI Modülü</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.ai.features.index') }}">AI Özellikleri</a>
            </li>
            <li class="breadcrumb-item active">{{ $feature->name }}</li>
        </ol>
    </nav>
@endpush

@push('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">AI Modülü</div>
            <h2 class="page-title">
                <span class="me-2">{{ $feature->emoji ?: '🤖' }}</span>
                {{ $feature->name }}
            </h2>
            <div class="page-subtitle text-muted">
                <span class="{{ $feature->getBadgeClass() }}">{{ ucfirst($feature->status) }}</span>
                <span class="badge bg-azure-lt ms-2">{{ $feature->getCategoryName() }}</span>
                @if($feature->is_system)
                    <span class="badge bg-info-lt ms-2">Sistem Özelliği</span>
                @endif
                @if($feature->is_featured)
                    <span class="badge bg-warning-lt ms-2">Öne Çıkan</span>
                @endif
            </div>
        </div>
        <div class="col-auto">
            <div class="btn-list">
                <a href="{{ route('admin.ai.features.manage', $feature) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Düzenle
                </a>
                @if(!$feature->is_system)
                    <button type="button" class="btn btn-outline-info" onclick="duplicateFeature({{ $feature->id }})">
                        <i class="fas fa-copy me-2"></i>Kopyala
                    </button>
                @endif
                <a href="{{ route('admin.ai.features.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Geri Dön
                </a>
            </div>
        </div>
    </div>
@endpush

@section('content')
<div class="row">
    <!-- Sol Kolon - Temel Bilgiler -->
    <div class="col-md-8">
        <!-- Özellik Detayları -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Özellik Bilgileri</h3>
            </div>
            <div class="card-body">
                @if($feature->description)
                    <p class="text-muted mb-3">{{ $feature->description }}</p>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Slug:</dt>
                            <dd class="col-sm-8"><code>{{ $feature->slug }}</code></dd>
                            
                            <dt class="col-sm-4">Kategori:</dt>
                            <dd class="col-sm-8">{{ $feature->getCategoryName() }}</dd>
                            
                            <dt class="col-sm-4">Karmaşıklık:</dt>
                            <dd class="col-sm-8">{{ $feature->getComplexityName() }}</dd>
                            
                            <dt class="col-sm-4">Yanıt Uzunluğu:</dt>
                            <dd class="col-sm-8">{{ ucfirst($feature->response_length) }}</dd>
                            
                            <dt class="col-sm-4">Yanıt Formatı:</dt>
                            <dd class="col-sm-8">{{ ucfirst($feature->response_format) }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Sıralama:</dt>
                            <dd class="col-sm-8">{{ $feature->sort_order }}</dd>
                            
                            <dt class="col-sm-4">Input Gerekli:</dt>
                            <dd class="col-sm-8">
                                @if($feature->requires_input)
                                    <span class="badge bg-success">Evet</span>
                                @else
                                    <span class="badge bg-secondary">Hayır</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Pro Gerekli:</dt>
                            <dd class="col-sm-8">
                                @if($feature->requires_pro)
                                    <span class="badge bg-warning">Evet</span>
                                @else
                                    <span class="badge bg-success">Hayır</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Examples'da Göster:</dt>
                            <dd class="col-sm-8">
                                @if($feature->show_in_examples)
                                    <span class="badge bg-success">Evet</span>
                                @else
                                    <span class="badge bg-secondary">Hayır</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                @if($feature->input_placeholder)
                    <div class="mt-3">
                        <h5>Input Placeholder:</h5>
                        <p class="text-muted fst-italic">"{{ $feature->input_placeholder }}"</p>
                    </div>
                @endif

                @if($feature->button_text && $feature->button_text !== 'Canlı Test Et')
                    <div class="mt-3">
                        <h5>Buton Metni:</h5>
                        <p class="text-muted">"{{ $feature->button_text }}"</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bağlı Prompt'lar -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Bağlı Prompt'lar ({{ $feature->featurePrompts->count() }})</h3>
            </div>
            @if($feature->featurePrompts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Öncelik</th>
                                <th>Prompt</th>
                                <th>Rol</th>
                                <th>Durum</th>
                                <th>Ayarlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feature->featurePrompts->sortBy('priority') as $featurePrompt)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $featurePrompt->priority }}</span>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold">{{ $featurePrompt->prompt->name }}</div>
                                        <div class="text-muted small">{{ Str::limit($featurePrompt->prompt->content, 60) }}</div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $roleColors = [
                                            'primary' => 'success',
                                            'secondary' => 'info', 
                                            'hidden' => 'warning',
                                            'conditional' => 'purple',
                                            'formatting' => 'cyan',
                                            'validation' => 'pink'
                                        ];
                                        $roleNames = [
                                            'primary' => 'Ana Prompt',
                                            'secondary' => 'İkincil',
                                            'hidden' => 'Gizli Sistem',
                                            'conditional' => 'Şartlı',
                                            'formatting' => 'Format',
                                            'validation' => 'Doğrulama'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $roleColors[$featurePrompt->prompt_role] ?? 'secondary' }}">
                                        {{ $roleNames[$featurePrompt->prompt_role] ?? ucfirst($featurePrompt->prompt_role) }}
                                    </span>
                                </td>
                                <td>
                                    @if($featurePrompt->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                    @if($featurePrompt->is_required)
                                        <span class="badge bg-warning-lt ms-1">Zorunlu</span>
                                    @endif
                                </td>
                                <td>
                                    @if($featurePrompt->conditions)
                                        <small class="text-muted">Şartlı</small>
                                    @endif
                                    @if($featurePrompt->parameters)
                                        <small class="text-info">Parametreli</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body text-center py-4">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Bu özellik için henüz prompt bağlantısı yok</p>
                    <a href="{{ route('admin.ai.features.manage', $feature) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Prompt Ekle
                    </a>
                </div>
            @endif
        </div>

        <!-- Örnek Inputs -->
        @if($feature->example_inputs && count($feature->example_inputs) > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Hızlı Örnekler ({{ count($feature->example_inputs) }})</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($feature->getFormattedExamples() as $index => $example)
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <h6 class="text-primary">{{ $example['label'] ?: 'Örnek ' . ($index + 1) }}</h6>
                                <p class="text-muted mb-0">"{{ $example['text'] }}"</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sağ Kolon - İstatistikler -->
    <div class="col-md-4">
        <!-- Kullanım İstatistikleri -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Kullanım İstatistikleri</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-center">
                        <div class="h1 m-0 text-primary">{{ number_format($usage_stats['total_usage']) }}</div>
                        <div class="text-muted">Toplam Kullanım</div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="h1 m-0 text-success">
                            {{ $usage_stats['avg_rating'] > 0 ? number_format($usage_stats['avg_rating'], 1) : '0.0' }}
                        </div>
                        <div class="text-muted">Ortalama Puan</div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6 text-center">
                        <div class="h3 m-0 text-warning">{{ number_format($usage_stats['rating_count']) }}</div>
                        <div class="text-muted">Puan Sayısı</div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="h3 m-0 text-info">{{ number_format($usage_stats['total_tokens'] ?? 0) }}</div>
                        <div class="text-muted">Token</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sistem Bilgileri -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Sistem Bilgileri</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-5">Oluşturulma:</dt>
                    <dd class="col-7">{{ $feature->created_at->format('d.m.Y H:i') }}</dd>
                    
                    <dt class="col-5">Son Güncelleme:</dt>
                    <dd class="col-7">{{ $feature->updated_at->format('d.m.Y H:i') }}</dd>
                    
                    @if($usage_stats['last_used_at'])
                    <dt class="col-5">Son Kullanım:</dt>
                    <dd class="col-7">{{ $usage_stats['last_used_at']->format('d.m.Y H:i') }}</dd>
                    @endif
                </dl>
                
                @if($feature->is_system)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Bu bir sistem özelliğidir. Silinemez ancak düzenlenebilir.
                    </div>
                @endif
            </div>
        </div>

        <!-- SEO Bilgileri -->
        @if($feature->meta_title || $feature->meta_description)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">SEO Ayarları</h3>
            </div>
            <div class="card-body">
                @if($feature->meta_title)
                    <div class="mb-3">
                        <label class="form-label">Meta Title:</label>
                        <p class="text-muted">{{ $feature->meta_title }}</p>
                    </div>
                @endif
                @if($feature->meta_description)
                    <div>
                        <label class="form-label">Meta Description:</label>
                        <p class="text-muted">{{ $feature->meta_description }}</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@push('js')
<script>
function duplicateFeature(featureId) {
    if (confirm('Bu AI özelliğini kopyalamak istediğinizden emin misiniz?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.ai.features.duplicate', '') }}/${featureId}`;
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

@push('css')
<style>
.border-left-primary {
    border-left: 3px solid var(--tblr-primary) !important;
}

.card-body .h1 {
    font-weight: 600;
}

.card-body .h3 {
    font-weight: 500;
}

.badge {
    font-weight: 500;
}

.alert {
    border: none;
    border-radius: 0.5rem;
}
</style>
@endpush
@endsection