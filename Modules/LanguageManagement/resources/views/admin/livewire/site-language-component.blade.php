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
                        placeholder="Dil adı, kod veya yerel adla arayın... (örn: İngilizce, en, English)">
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
                    <span class="badge bg-success ms-2">{{ $activeLanguages->count() }}</span>
                    <small class="text-muted ms-3">Sitede gözüken diller • <i class="fas fa-arrows-alt"></i> Sürükle-bırak ile sıralayabilirsiniz</small>
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
                    <span class="badge bg-warning ms-2">{{ $inactiveLanguages->count() }}</span>
                    <small class="text-muted ms-3">Admin panelde hazırlık için gözüken, sitede gözükmeyen diller</small>
                </div>
                <div class="row" id="inactive-sortable-list">
                    @foreach($inactiveLanguages as $language)
                        @include('languagemanagement::admin.partials.language-card', ['language' => $language, 'category' => 'inactive'])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. DİĞER DİLLER (Dünya dilleri - Seçilebilir) -->
        @if($hiddenLanguages->count() > 0)
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-info">
                        <i class="fas fa-globe me-2"></i>Diğer Diller
                    </h5>
                    <span class="badge bg-info ms-2">{{ $hiddenLanguages->count() }}</span>
                    <small class="text-muted ms-3">Kullanılabilir dünya dilleri - pasif veya aktif yapılabilir</small>
                </div>
                <div class="row" id="hidden-sortable-list">
                    @foreach($hiddenLanguages as $language)
                        @include('languagemanagement::admin.partials.language-card', ['language' => $language, 'category' => 'hidden'])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Arama Sonucu Bulunamadı -->
        @if($search && $activeLanguages->count() === 0 && $inactiveLanguages->count() === 0 && $hiddenLanguages->count() === 0)
            <div class="alert alert-info">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="alert-heading mb-1">
                            <i class="fas fa-search me-2"></i>Aradığınız dil bulunamadı
                        </h5>
                        <p class="mb-0">
                            "<strong>{{ $search }}</strong>" araması için sonuç bulunamadı. 
                            Bu dili sisteme eklemek ister misiniz?
                        </p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.languagemanagement.site.manage') }}?suggested={{ urlencode($search) }}" 
                           class="btn btn-info">
                            <i class="fas fa-plus me-1"></i> "{{ $search }}" Dilini Ekle
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sadece aktif diller için sortable aktif et
    const activeSortableList = document.getElementById('active-sortable-list');
    if (activeSortableList) {
        new Sortable(activeSortableList, {
            animation: 150,
            ghostClass: 'opacity-25',
            chosenClass: 'shadow',
            handle: '.card', // Kartın tamamını sürüklenebilir yap
            onEnd: function(evt) {
                const itemIds = Array.from(activeSortableList.children).map(item => 
                    item.getAttribute('data-id')
                );
                @this.call('updateOrder', itemIds);
            }
        });
    }
    
    // Pasif ve Diğer diller alfabetik kalır (sortable yok)
    console.log('🎯 Sortable sadece aktif dillerde aktif edildi');
});
</script>
@endpush
</div>