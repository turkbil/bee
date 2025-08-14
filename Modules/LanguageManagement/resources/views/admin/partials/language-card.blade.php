<div class="col-md-6 col-lg-3" data-id="{{ $language->id }}">
    <div class="card {{ $category === 'hidden' ? 'opacity-75' : '' }}" style="cursor: {{ $category === 'active' ? 'move' : 'default' }};">
        <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl mb-3" style="font-size: 1.5rem;">{{ $language->flag_icon ?? '🌐' }}</span>
            <h3 class="m-0 mb-1 {{ $category === 'hidden' ? 'text-muted' : '' }}">{{ $language->native_name }}</h3>
            <div class="text-secondary">{{ $language->name }} ({{ $language->code }}) • {{ $language->direction === 'rtl' ? 'RTL' : 'LTR' }}</div>
            <div class="mt-3">
                @if($language->is_default)
                    <span class="badge bg-primary">{{ __('admin.default') }}</span>
                @endif
                
                @if($category === 'active')
                    <span class="badge bg-success-lt">{{ __('admin.active') }}</span>
                @elseif($category === 'inactive')
                    <span class="badge bg-warning-lt">{{ __('admin.inactive') }}</span>
                @else
                    <span class="badge bg-secondary-lt">{{ __('admin.hidden') }}</span>
                @endif
            </div>
        </div>
        
        <div class="d-flex">
            @if($category === 'active')
                <button type="button" onclick="showLanguageActionModal('deactivate', {{ $language->id }}, '{{ $language->native_name }}')" 
                        class="card-btn border-0 bg-transparent text-start p-2">
                    <i class="fas fa-pause me-2 text-muted"></i>
                    Pasif Yap
                </button>
            @elseif($category === 'inactive')
                <button type="button" onclick="showLanguageActionModal('activate', {{ $language->id }}, '{{ $language->native_name }}')" 
                        class="card-btn border-0 bg-transparent text-start p-2">
                    <i class="fas fa-play me-2 text-muted"></i>
                    Aktif Yap
                </button>
            @else
                <button type="button" onclick="showLanguageActionModal('show', {{ $language->id }}, '{{ $language->native_name }}')" 
                        class="card-btn border-0 bg-transparent text-start p-2 {{ $category === 'hidden' ? 'text-muted' : '' }}">
                    <i class="fas fa-eye me-2 text-muted"></i>
                    Görünür Yap
                </button>
            @endif
            
            <a href="{{ route('admin.languagemanagement.site.manage', $language->id) }}" 
               class="card-btn {{ $category === 'hidden' ? 'text-muted' : '' }}">
                <i class="fas fa-edit me-2 text-muted"></i>
                Düzenle
            </a>
            @if($category === 'inactive')
                <button type="button" onclick="showLanguageActionModal('hide', {{ $language->id }}, '{{ $language->native_name }}')" 
                        class="card-btn border-0 bg-transparent text-start p-2">
                    <i class="fas fa-trash me-2 text-muted"></i>
                    Sil
                </button>
            @endif
        </div>
    </div>
</div>