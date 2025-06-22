@push('styles')
<style>
.translation-grid {
    display: grid;
    grid-template-columns: 1fr 2fr auto;
    gap: 0.5rem;
    align-items: center;
}
.translation-key {
    font-family: monospace;
    font-size: 0.875rem;
    color: #64748b;
}
.translation-value {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    padding: 0.5rem;
}
</style>
@endpush

<div>
    <!-- Header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Dil Yönetimi</div>
                    <h2 class="page-title">Çeviri Yönetimi</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Type Selection -->
                                <div class="col-md-3">
                                    <label class="form-label">Çeviri Türü</label>
                                    <select wire:model="selectedType" class="form-select">
                                        <option value="system">Sistem</option>
                                        <option value="module">Modül</option>
                                        <option value="tenant">Tenant</option>
                                    </select>
                                </div>

                                <!-- Module Selection -->
                                @if($selectedType === 'module')
                                <div class="col-md-3">
                                    <label class="form-label">Modül</label>
                                    <select wire:model="selectedModule" class="form-select">
                                        <option value="">Modül Seçin</option>
                                        @foreach($modules as $module)
                                            <option value="{{ $module }}">{{ $module }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <!-- Language Selection -->
                                <div class="col-md-2">
                                    <label class="form-label">Dil</label>
                                    <select wire:model="selectedLocale" class="form-select">
                                        @foreach($availableLocales as $locale)
                                            <option value="{{ $locale }}">{{ locale_name($locale) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- File Selection -->
                                <div class="col-md-3">
                                    <label class="form-label">Dosya</label>
                                    <select wire:model="selectedFile" class="form-select">
                                        @foreach($translationFiles as $file)
                                            <option value="{{ $file }}">{{ $file }}.php</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Create New File -->
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#newFileModal">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Translation Editor -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ ucfirst($selectedType) }} Çevirileri
                                @if($selectedType === 'module' && $selectedModule)
                                    - {{ $selectedModule }}
                                @endif
                                - {{ $selectedFile }}.php
                            </h3>
                            <div class="card-actions">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTranslationModal">
                                    <i class="ti ti-plus"></i> Yeni Çeviri
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if(empty($translations))
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-language"></i>
                                    </div>
                                    <p class="empty-title">Çeviri bulunamadı</p>
                                    <p class="empty-subtitle text-muted">
                                        Bu dosyada henüz çeviri bulunmuyor. Yeni çeviri ekleyebilirsiniz.
                                    </p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%">Anahtar</th>
                                                <th style="width: 60%">Değer</th>
                                                <th style="width: 10%">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($translations as $key => $value)
                                                <tr>
                                                    <td>
                                                        <code class="translation-key">{{ $key }}</code>
                                                    </td>
                                                    <td>
                                                        @if($editingKey === $key)
                                                            <div class="input-group">
                                                                <input wire:model="editingValue" type="text" class="form-control" 
                                                                       wire:keydown.enter="saveEdit" wire:keydown.escape="cancelEdit">
                                                                <button wire:click="saveEdit" class="btn btn-success btn-sm">
                                                                    <i class="ti ti-check"></i>
                                                                </button>
                                                                <button wire:click="cancelEdit" class="btn btn-outline-secondary btn-sm">
                                                                    <i class="ti ti-x"></i>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div class="translation-value">{{ $value }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($editingKey === $key)
                                                            <!-- Editing mode buttons are in the value column -->
                                                        @else
                                                            <div class="btn-group btn-group-sm">
                                                                <button wire:click="startEdit('{{ $key }}')" class="btn btn-outline-primary">
                                                                    <i class="ti ti-edit"></i>
                                                                </button>
                                                                <button wire:click="deleteTranslation('{{ $key }}')" 
                                                                        class="btn btn-outline-danger"
                                                                        onclick="return confirm('Bu çeviriyi silmek istediğinizden emin misiniz?')">
                                                                    <i class="ti ti-trash"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Translation Modal -->
    <div class="modal fade" id="addTranslationModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Çeviri Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Anahtar</label>
                        <input wire:model="newKey" type="text" class="form-control" placeholder="örn: welcome_message">
                        <div class="form-hint">Anahtar sadece harf, rakam ve alt çizgi içerebilir</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Değer</label>
                        <textarea wire:model="newValue" class="form-control" rows="3" placeholder="Çeviri metni"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button wire:click="addTranslation" type="button" class="btn btn-primary">Ekle</button>
                </div>
            </div>
        </div>
    </div>

    <!-- New File Modal -->
    <div class="modal fade" id="newFileModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Çeviri Dosyası</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Dosya Adı</label>
                        <input wire:model="newKey" type="text" class="form-control" placeholder="örn: messages">
                        <div class="form-hint">Dosya adı sadece harf, rakam, tire ve alt çizgi içerebilir</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button wire:click="createNewFile" type="button" class="btn btn-primary">Oluştur</button>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible position-fixed" style="top: 20px; right: 20px; z-index: 1050;">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible position-fixed" style="top: 20px; right: 20px; z-index: 1050;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Modal'lar kapandığında form alanlarını temizle
    document.addEventListener('hidden.bs.modal', function () {
        @this.call('$refresh');
    });
</script>
@endpush