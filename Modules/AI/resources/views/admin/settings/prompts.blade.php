@extends('admin.layout')

@include('ai::admin.helper')

@section('pretitle', 'AI Ayarları')
@section('title', 'Promptlar & Şablonlar')

@section('content')
    <div class="row">
        <div class="col-3">
            @include('ai::admin.settings.sidebar')
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-comments me-2"></i>
                        Prompt Yönetimi
                    </h3>
                    <div class="card-actions">
                        <div class="btn-group me-2" role="group">
                            <input type="radio" class="btn-check" name="prompt_filter" id="filter_all" value="all" checked>
                            <label class="btn btn-outline-primary btn-sm" for="filter_all">Tümü</label>
                            
                            <input type="radio" class="btn-check" name="prompt_filter" id="filter_standard" value="standard">
                            <label class="btn btn-outline-secondary btn-sm" for="filter_standard">Standart</label>
                            
                            <input type="radio" class="btn-check" name="prompt_filter" id="filter_common" value="common">
                            <label class="btn btn-outline-info btn-sm" for="filter_common">Ortak</label>
                            
                            <input type="radio" class="btn-check" name="prompt_filter" id="filter_system" value="hidden">
                            <label class="btn btn-outline-warning btn-sm" for="filter_system">Sistem</label>
                        </div>
                        <a href="{{ route('admin.ai.settings.prompts.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Yeni Prompt
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif


                    <!-- Prompt Listesi - Modern Design -->
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th class="w-1"></th>
                                    <th>Prompt Bilgileri</th>
                                    <th>Durum</th>
                                    <th>Özellikler</th>
                                    <th class="text-end">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prompts as $prompt)
                                    <tr data-prompt-type="{{ $prompt->prompt_type ?? 'standard' }}" 
                                        data-is-common="{{ $prompt->is_common ? 'true' : 'false' }}" 
                                        data-is-system="{{ $prompt->is_system ? 'true' : 'false' }}">
                                        <td>
                                            <div class="avatar avatar-sm bg-{{ $prompt->is_active ? 'green' : 'red' }}-lt">
                                                @if($prompt->prompt_type == 'hidden_system' || $prompt->prompt_type == 'secret_knowledge' || $prompt->prompt_type == 'conditional')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"/>
                                                        <path d="M11 4a2 2 0 0 1 4 0v4h-4v-4z"/>
                                                    </svg>
                                                @elseif($prompt->is_system)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/>
                                                        <circle cx="12" cy="11" r="1"/>
                                                        <line x1="12" y1="12" x2="12" y2="14.5"/>
                                                    </svg>
                                                @elseif($prompt->is_common || $prompt->prompt_type == 'common')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <circle cx="12" cy="12" r="9"/>
                                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M8 9h8"/>
                                                        <path d="M8 13h6"/>
                                                        <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium">{{ $prompt->name }}</div>
                                                    <div class="text-muted small">
                                                        {{ Str::limit($prompt->content ?? 'İçerik yok', 60) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $prompt->is_active ? 'green' : 'red' }} badge-pill">
                                                {{ $prompt->is_active ? 'Aktif' : 'Pasif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @if($prompt->is_system)
                                                    <span class="badge bg-blue-lt">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/>
                                                        </svg>
                                                        Sistem
                                                    </span>
                                                @endif
                                                @if($prompt->is_default)
                                                    <span class="badge bg-warning-lt">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                                        </svg>
                                                        Varsayılan
                                                    </span>
                                                @endif
                                                @if($prompt->is_common || $prompt->prompt_type == 'common')
                                                    <span class="badge bg-cyan-lt">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <circle cx="12" cy="12" r="9"/>
                                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                                        </svg>
                                                        Ortak
                                                    </span>
                                                @endif
                                                @if($prompt->prompt_type == 'hidden_system')
                                                    <span class="badge bg-warning-lt">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"/>
                                                            <path d="M11 4a2 2 0 0 1 4 0v4h-4v-4z"/>
                                                        </svg>
                                                        Gizli Sistem
                                                    </span>
                                                @endif
                                                @if($prompt->prompt_type == 'secret_knowledge')
                                                    <span class="badge bg-danger-lt">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                                            <path d="M9 12l2 2l4 -4"/>
                                                        </svg>
                                                        Gizli Bilgi
                                                    </span>
                                                @endif
                                                @if($prompt->prompt_type == 'conditional')
                                                    <span class="badge bg-purple-lt">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M8 9h8"/>
                                                            <path d="M8 13h6"/>
                                                            <path d="M12 17l-3 -3l3 -3"/>
                                                            <path d="M9 14h6"/>
                                                        </svg>
                                                        Şartlı Yanıt
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center align-middle" style="width: 160px">
                                            <div class="container">
                                                <div class="row">
                                                    @if(!$prompt->is_system || $prompt->is_common || in_array($prompt->prompt_type, ['hidden_system', 'secret_knowledge', 'conditional']))
                                                    <div class="col">
                                                        <a href="{{ route('admin.ai.settings.prompts.manage', $prompt->id) }}"
                                                           data-bs-toggle="tooltip" data-bs-placement="top" title="Düzenle">
                                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                                        </a>
                                                    </div>
                                                    @endif
                                                    
                                                    @if(!$prompt->is_system && !$prompt->is_default)
                                                    <div class="col">
                                                        <a href="#" onclick="makeDefault({{ $prompt->id }})"
                                                           data-bs-toggle="tooltip" data-bs-placement="top" title="Varsayılan Yap">
                                                            <i class="fa-solid fa-star link-secondary fa-lg"></i>
                                                        </a>
                                                    </div>
                                                    @endif
                                                    
                                                    @if(!$prompt->is_system && !$prompt->is_common && !in_array($prompt->prompt_type, ['hidden_system', 'secret_knowledge', 'conditional', 'common']))
                                                    <div class="col lh-1">
                                                        <div class="dropdown mt-1">
                                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                               aria-haspopup="true" aria-expanded="false">
                                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a href="#" onclick="deletePrompt({{ $prompt->id }})" class="dropdown-item link-danger">
                                                                    Sil
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="empty">
                                                <div class="empty-img">
                                                    <img src="{{ asset('admin/img/undraw_printing_invoices_5r4r.svg') }}" height="128" alt="No data">
                                                </div>
                                                <p class="empty-title">Henüz prompt eklenmemiş</p>
                                                <p class="empty-subtitle text-muted">
                                                    AI yanıtlarını yönlendirmek için yeni prompts ekleyebilirsiniz.
                                                </p>
                                                <div class="empty-action">
                                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#promptModal">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <line x1="12" y1="5" x2="12" y2="19"/>
                                                            <line x1="5" y1="12" x2="19" y2="12"/>
                                                        </svg>
                                                        İlk Prompt'unu Ekle
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function makeDefault(id) {
    if (confirm('Bu promptu varsayılan yapmak istediğinizden emin misiniz?')) {
        fetch(`/admin/ai/prompts/${id}/default`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Bir hata oluştu');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('İşlem sırasında hata oluştu');
        });
    }
}

function deletePrompt(id) {
    if (confirm('Bu promptu silmek istediğinizden emin misiniz?')) {
        fetch(`/admin/ai/prompts/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Silme işlemi başarısız');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Silme işlemi sırasında hata oluştu');
        });
    }
}

// Prompt filtreleme sistemi
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('input[name="prompt_filter"]');
    const promptRows = document.querySelectorAll('tr[data-prompt-type]');
    
    filterButtons.forEach(button => {
        button.addEventListener('change', function() {
            const filterValue = this.value;
            
            promptRows.forEach(row => {
                const promptType = row.getAttribute('data-prompt-type');
                const isCommon = row.getAttribute('data-is-common') === 'true';
                const isSystem = row.getAttribute('data-is-system') === 'true';
                
                let shouldShow = false;
                
                switch(filterValue) {
                    case 'all':
                        shouldShow = true;
                        break;
                    case 'standard':
                        shouldShow = promptType === 'standard' || (!promptType && !isCommon && !isSystem);
                        break;
                    case 'common':
                        shouldShow = promptType === 'common' || isCommon;
                        break;
                    case 'hidden':
                        shouldShow = promptType === 'hidden_system' || 
                                   promptType === 'secret_knowledge' || 
                                   promptType === 'conditional' ||
                                   (isSystem && !isCommon);
                        break;
                }
                
                if (shouldShow) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Hiç görünür satır yoksa boş mesaj göster
            const visibleRows = Array.from(promptRows).filter(row => row.style.display !== 'none');
            const emptyRow = document.querySelector('.empty');
            
            if (visibleRows.length === 0 && !emptyRow) {
                // Dinamik boş mesaj ekleme (opsiyonel)
                console.log('Filtreye uygun prompt bulunamadı');
            }
        });
    });
});
</script>
@endpush