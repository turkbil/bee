@php
    View::share('pretitle', 'Site Dilleri');
@endphp
<div>
@include('languagemanagement::admin.helper')

<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama Kutusu -->
            <div class="col-12">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="Dil ara... (örn: İngilizce, en, English)">
                </div>
            </div>
            <!-- Loading -->
            <div class="col-12 position-relative">
                <div wire:loading
                    wire:target="render, search, delete, toggleActive, setAsDefault, updateOrder, toggleVisibility"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px; z-index: 10;">
                    <div class="small text-muted mb-2">{{ __('admin.updating') }}...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- 3 Seviyeli Dil Kategorileri -->
        
        <!-- 1. AKTİF DİLLER (Sitede gözüken) -->
        @if($activeLanguages->count() > 0)
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-success">
                        <i class="fas fa-check-circle me-2"></i>Aktif Diller
                    </h5>
                    <span class="badge ms-2">{{ $activeLanguages->count() }}</span>
                </div>
                <div class="mb-3 d-none d-md-block">
                    <small class="text-muted">Sitede gözüken diller • <i class="fas fa-arrows-alt"></i> Sürükle-bırak ile sıralayabilirsiniz</small>
                </div>
                <div class="mb-3 d-md-none">
                    <small class="text-muted">Sitede gözüken diller<br><i class="fas fa-arrows-alt"></i> Sürükleyerek sıralayın</small>
                </div>
                <div class="row" id="active-sortable-list">
                    @foreach($activeLanguages as $language)
                        @include('languagemanagement::admin.partials.language-card', ['language' => $language, 'category' => 'active'])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 2. PASİF DİLLER (Admin panelde hazırlık için) -->
        @if($inactiveLanguages->count() > 0)
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-warning">
                        <i class="fas fa-cog me-2"></i>Pasif Diller (Hazırlık)
                    </h5>
                    <span class="badge ms-2">{{ $inactiveLanguages->count() }}</span>
                </div>
                <div class="mb-3 d-none d-md-block">
                    <small class="text-muted">Admin panelde hazırlık için gözüken, sitede gözükmeyen diller</small>
                </div>
                <div class="mb-3 d-md-none">
                    <small class="text-muted">Hazırlık aşamasındaki diller<br>Sitede gözükmez</small>
                </div>
                <div class="row" id="inactive-sortable-list">
                    @foreach($inactiveLanguages as $language)
                        @include('languagemanagement::admin.partials.language-card', ['language' => $language, 'category' => 'inactive'])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3A. ANA DİLLER (Popüler gizli diller) -->
        @if($hiddenLanguages->where('is_main_language', true)->count() > 0)
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-info">
                        <i class="fas fa-star me-2"></i>Ana Diller
                    </h5>
                    <span class="badge ms-2">{{ $hiddenLanguages->where('is_main_language', true)->count() }}</span>
                </div>
                <div class="mb-3 d-none d-md-block">
                    <small class="text-muted">Popüler dünya dilleri - pasif veya aktif yapılabilir</small>
                </div>
                <div class="mb-3 d-md-none">
                    <small class="text-muted">Popüler diller<br>Kullanmak için "Kullan" tıklayın</small>
                </div>
                <div class="row" id="main-hidden-sortable-list">
                    @foreach($hiddenLanguages->where('is_main_language', true) as $language)
                        @include('languagemanagement::admin.partials.language-card', ['language' => $language, 'category' => 'hidden'])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3B. DİĞER DİLLER (AI odaklı gizli diller) -->
        @if($hiddenLanguages->where('is_main_language', false)->count() > 0)
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-secondary">
                        <i class="fas fa-globe me-2"></i>Diğer Diller
                    </h5>
                    <span class="badge ms-2">{{ $hiddenLanguages->where('is_main_language', false)->count() }}</span>
                </div>
                <div class="mb-3 d-none d-md-block">
                    <small class="text-muted">Özel kullanım dilleri (AI çeviri için) - pasif veya aktif yapılabilir</small>
                </div>
                <div class="mb-3 d-md-none">
                    <small class="text-muted">Özel diller<br>AI çeviri için</small>
                </div>
                <div class="row" id="other-hidden-sortable-list">
                    @foreach($hiddenLanguages->where('is_main_language', false) as $language)
                        @include('languagemanagement::admin.partials.language-card', ['language' => $language, 'category' => 'hidden'])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Arama Sonucu Bulunamadı -->
        @if($search && $activeLanguages->count() === 0 && $inactiveLanguages->count() === 0 && $hiddenLanguages->count() === 0)
            <div class="alert alert-info">
                <div class="row align-items-center">
                    <div class="col-12 col-md-8">
                        <h5 class="alert-heading mb-1">
                            <i class="fas fa-search me-2"></i>Dil bulunamadı
                        </h5>
                        <p class="mb-0 small">
                            "<strong>{{ $search }}</strong>" bulunamadı. Eklemek ister misiniz?
                        </p>
                    </div>
                    <div class="col-12 col-md-4 mt-2 mt-md-0">
                        <a href="{{ route('admin.languagemanagement.site.manage') }}?suggested={{ urlencode($search) }}" 
                           class="btn btn-info btn-sm w-100">
                            <i class="fas fa-plus me-1"></i> Ekle
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Boş Durum (Hiç Dil Yok) -->
        @if(!$search && $activeLanguages->count() === 0 && $inactiveLanguages->count() === 0 && $hiddenLanguages->count() === 0)
            <div class="text-center py-5">
                <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Henüz site dili eklenmemiş</h5>
                <p class="text-muted">
                    Site dilinizi ekleyerek çoklu dil desteğini başlatabilirsiniz.
                </p>
                <a href="{{ route('admin.languagemanagement.site.manage') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> İlk Dili Ekle
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Language Action Modal -->
@include('languagemanagement::admin.partials.language-action-modal')

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script>
document.addEventListener('livewire:initialized', function() {
    initLanguageSortable();
    
    // Livewire morph (güncelleme) sonrası tekrar başlat
    Livewire.hook('morph.updated', () => {
        initLanguageSortable();
    });
    
    function initLanguageSortable() {
        const activeSortableList = document.getElementById('active-sortable-list');
        if (!activeSortableList) {
            return;
        }
        
        // Mevcut sortable'ı temizle
        if (window.languageSortable) {
            window.languageSortable.destroy();
            window.languageSortable = null;
        }
        
        // Yeni sortable oluştur
        window.languageSortable = new Sortable(activeSortableList, {
            animation: 250,
            delay: 50,
            delayOnTouchOnly: true,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            handle: '.card',
            forceFallback: false,
            
            onStart: function () {
                document.body.style.cursor = 'grabbing';
            },
            
            onEnd: function (evt) {
                document.body.style.cursor = 'default';
                
                // Sıralama verilerini hazırla
                const items = Array.from(activeSortableList.children).map((item, index) => ({
                    value: parseInt(item.dataset.id),
                    order: index + 1,
                }));

                console.log('🔄 Language order updated:', items);
                
                // Livewire metodunu çağır
                Livewire.dispatch('updateOrder', { list: items });
            }
        });
    }
});
</script>
@endpush
</div>