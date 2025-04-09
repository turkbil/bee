{{-- Modules/TaskManagement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Görevler
@endpush

{{-- Başlık --}}
@push('title')
Görev Yönetimi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('taskmanagement', 'view')
            <a href="{{ route('admin.taskmanagement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                Görevler
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('taskmanagement', 'create')
            <button type="button" class="dropdown-module-item btn btn-primary" wire:click="showTaskModal()">
                <i class="fas fa-plus me-2"></i> Yeni Görev
            </button>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush