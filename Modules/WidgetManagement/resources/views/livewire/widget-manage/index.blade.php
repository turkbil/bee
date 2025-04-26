@include('widgetmanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $widgetId ? 'edit' : 'plus' }} me-2"></i>
                    {{ $widgetId ? 'Widget Düzenle: ' . $widget['name'] : 'Yeni Widget Ekle' }}
                </h3>
            </div>
            
            <div wire:loading class="position-fixed top-0 start-0 w-100" style="z-index: 1050;" wire:target="setFormMode">
                <div class="progress rounded-0" style="height: 12px;">
                    <div class="progress-bar progress-bar-striped progress-bar-indeterminate bg-primary"></div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="d-flex mb-3">
                    <ul class="nav nav-tabs nav-fill w-100">
                        <li class="nav-item">
                            <a class="nav-link {{ $formMode === 'base' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('base')">
                                <i class="fas fa-info-circle me-2"></i>
                                Temel Bilgiler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $formMode === 'design' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('design')">
                                <i class="fas fa-palette me-2"></i>
                                {{ ($widget['type'] === 'file' || $widget['type'] === 'module') ? 'Dosya Yolu' : 'İçerik' }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $formMode === 'items' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('items')">
                                <i class="fas fa-layer-group me-2"></i>
                                İçerik Yapısı
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $formMode === 'settings' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('settings')">
                                <i class="fas fa-sliders-h me-2"></i>
                                Özelleştirme
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content">
                    @include('widgetmanagement::livewire.widget-manage.base')
                    @include('widgetmanagement::livewire.widget-manage.design')
                    @include('widgetmanagement::livewire.widget-manage.items')
                    @include('widgetmanagement::livewire.widget-manage.settings')
                </div>
            </div>
            
            <!-- Form Footer -->
            <x-form-footer route="admin.widgetmanagement" :model-id="$widgetId" />
        </div>
    </form>
</div>

@push('styles')
<style>
.form-label.required:after {
    content: " *";
    color: red;
}

/* Sürükle-bırak dosya alanı stillemesi */
.file-drop-area {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    border: 2px dashed #ccc;
    border-radius: 6px;
    background-color: #f8f9fa;
    transition: 0.2s;
}

.file-drop-area:hover,
.file-drop-area.is-active {
    background-color: #eef2f7;
    border-color: #adb5bd;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('livewire:initialized', function() {
    // Dosya sürükle-bırak işlemleri
    const fileDropArea = document.querySelector('.file-drop-area');
    if (fileDropArea) {
        const fileInput = document.getElementById('file-upload');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            fileDropArea.classList.add('is-active');
        }

        function unhighlight() {
            fileDropArea.classList.remove('is-active');
        }

        fileDropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        }
    }
});
</script>
@endpush