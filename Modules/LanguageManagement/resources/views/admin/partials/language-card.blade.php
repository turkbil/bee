<div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-3" data-id="{{ $language->id }}">
    <div class="card {{ $category === 'hidden' ? 'opacity-75' : '' }}" style="cursor: {{ $category === 'active' ? 'move' : 'default' }};">
        <div class="card-body p-2 p-md-4 text-center">
            <span class="avatar avatar-md avatar-xl-md mb-1 mb-md-3" style="font-size: 1rem; font-size: 1.5rem;">{{ $language->flag_icon ?? '🌐' }}</span>
            <h6 class="m-0 mb-1 {{ $category === 'hidden' ? 'text-muted' : '' }}">{{ $language->native_name }}</h6>
            <div class="text-secondary small d-none d-md-block">{{ $language->name }} ({{ $language->code }}) • {{ $language->direction === 'rtl' ? 'RTL' : 'LTR' }}</div>
            <div class="text-secondary small d-md-none">{{ $language->code }}</div>
            <div class="mt-2 d-none d-md-block">
                @if($language->is_default)
                    <span class="badge">{{ __('admin.default') }}</span>
                @endif
                
                @if($category === 'active')
                    <span class="badge">{{ __('admin.active') }}</span>
                @elseif($category === 'inactive')
                    <span class="badge">{{ __('admin.inactive') }}</span>
                @else
                    <span class="badge">{{ __('admin.hidden') }}</span>
                @endif
            </div>
        </div>
        
        <div class="d-flex">
            @if($category === 'active')
                <a href="#" onclick="showLanguageActionModal('deactivate', {{ $language->id }}, '{{ $language->native_name }}'); return false;" 
                   class="card-btn {{ $category === 'hidden' ? 'text-muted' : '' }}">
                    <i class="fas fa-pause text-muted d-md-none"></i>
                    <span class="d-none d-md-inline"><i class="fas fa-pause me-2"></i>Pasifle</span>
                </a>
            @elseif($category === 'inactive')
                <a href="#" onclick="showLanguageActionModal('activate', {{ $language->id }}, '{{ $language->native_name }}'); return false;" 
                   class="card-btn {{ $category === 'hidden' ? 'text-muted' : '' }}">
                    <i class="fas fa-play text-muted d-md-none"></i>
                    <span class="d-none d-md-inline"><i class="fas fa-play me-2"></i>Aktifle</span>
                </a>
            @else
                <a href="#" onclick="showLanguageActionModal('show', {{ $language->id }}, '{{ $language->native_name }}'); return false;" 
                   class="card-btn {{ $category === 'hidden' ? 'text-muted' : '' }}">
                    <i class="fas fa-check text-muted d-md-none"></i>
                    <span class="d-none d-md-inline"><i class="fas fa-check me-2"></i>Kullan</span>
                </a>
            @endif
            
            @if(auth()->user()->is_root)
                <a href="{{ route('admin.languagemanagement.site.manage', $language->id) }}" 
                   class="card-btn {{ $category === 'hidden' ? 'text-muted' : '' }}">
                    <i class="fas fa-edit text-muted d-md-none"></i>
                    <span class="d-none d-md-inline"><i class="fas fa-edit me-2"></i>Düzenle</span>
                </a>
            @endif
            
            @if(!$language->is_default && $category !== 'active')
                @if(auth()->user()->is_root || ($category === 'inactive' || $category === 'hidden'))
                    <a href="#" onclick="showLanguageActionModal('hide', {{ $language->id }}, '{{ $language->native_name }}'); return false;" 
                       class="card-btn {{ $category === 'hidden' ? 'text-muted' : '' }}">
                        <i class="fas fa-trash text-muted d-md-none"></i>
                        <span class="d-none d-md-inline"><i class="fas fa-trash me-2"></i>Sil</span>
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>