<div class="card-header">
    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" id="dynamic-tabs">
        @foreach($tabs as $index => $tab)
        <li class="nav-item">
            <a href="#tabs-{{ $index + 1 }}" 
               class="nav-link {{ $index === 0 ? 'active' : '' }}" 
               data-bs-toggle="tab"
               data-tab-key="{{ $tab['key'] }}">
                <i class="fas fa-{{ $tab['icon'] }} me-2"></i>{{ $tab['name'] }}
            </a>
        </li>
        @endforeach
        
        {{ $slot ?? '' }}
    </ul>
</div>

@push('scripts')
<script>
// Tab System için özel event binding
document.addEventListener('DOMContentLoaded', function() {
    // Global tab manager varsa kullan
    if (window.pageManagement?.tabManager) {
        // Storage key'i güncelle
        window.pageManagement.tabManager.storageKey = '{{ $storageKey }}';
        
        // Tab'ları yeniden bind et
        window.pageManagement.tabManager.bindTabEvents();
        window.pageManagement.tabManager.restoreActiveTab();
    }
});
</script>
@endpush