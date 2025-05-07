{{-- Modules/AI/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Yapay Zeka
@endpush

{{-- Başlık --}}
@push('title')
AI Asistan
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    AI İşlemleri
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.index') }}">
                        AI Asistan
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.conversations.index') }}">
                        Konuşmalarım
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('ai', 'update')
                    <a class="dropdown-item" href="{{ route('admin.ai.settings') }}">
                        AI Ayarları
                    </a>
                    @endhasmoduleaccess
                </div>
            </div>
            @hasmoduleaccess('ai', 'view')
            <a href="{{ route('admin.ai.index') }}" class="btn btn-primary">
                AI Asistanı Aç
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush