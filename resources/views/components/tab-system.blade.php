<div class="card-header" style="padding-bottom: 0px;">
    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" id="dynamic-tabs">
        @foreach($tabs as $index => $tab)
        <li class="nav-item">
            <a href="#{{ $index }}" 
               class="nav-link {{ $index === 0 ? 'active' : '' }}" 
               data-bs-toggle="tab"
               data-tab-key="{{ $index }}">
                <i class="{{ $tab['icon'] ?? 'fas fa-file' }} me-2"></i>{{ $tab['name'] ?? $tab['title'] ?? 'Tab' }}
            </a>
        </li>
        @endforeach
        
        {{ $slot ?? '' }}
    </ul>
</div>

@push('scripts')
<script>
// Tab System - Storage-based active tab management
document.addEventListener('DOMContentLoaded', function() {
    const storageKey = '{{ $storageKey }}';
    
    // ✅ GÜNCELLEME: Tab system component artık page manage ile uyumlu!
    const isPageManage = window.location.pathname.includes('/admin/page/manage');
    if (isPageManage) {
        console.log('✅ Tab-system component page manage için aktif edildi');
        // Page manage sayfasında localStorage key'i özel olacak
        // storageKey zaten 'page_active_tab' olarak geliyor
    }
    
    // Sayfa yüklendiğinde kaydedilmiş tab'ı restore et
    const savedTab = localStorage.getItem(storageKey);
    if (savedTab) {
        // CSS selector güvenliği için ID kontrol et (HTML5 uyumlu - sayı ile başlayabilir)
        const isValidSelector = /^#[a-zA-Z0-9][a-zA-Z0-9_-]*$/.test(savedTab);
        if (!isValidSelector) {
            console.warn('⚠️ Geçersiz tab selector:', savedTab);
            localStorage.removeItem(storageKey);
            return;
        }
        
        const targetTab = document.querySelector(`[href="${savedTab}"]`);
        const targetPane = document.getElementById(savedTab.replace('#', ''));
        
        if (targetTab && targetPane) {
            // Tüm tab'ları deaktif et
            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Hedef tab'ı aktif et
            targetTab.classList.add('active');
            targetPane.classList.add('show', 'active');
        }
    } else {
        // Eğer kaydedilmiş tab yoksa ilk tab'ı aktif et
        const firstTab = document.querySelector('.nav-link');
        const firstPane = document.querySelector('.tab-pane');
        if (firstTab && firstPane) {
            firstTab.classList.add('active');
            firstPane.classList.add('show', 'active');
        }
    }
    
    // Tab değişimlerini dinle ve kaydet
    document.querySelectorAll('.nav-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            const tabHref = this.getAttribute('href');
            localStorage.setItem(storageKey, tabHref);
        });
    });
});
</script>
@endpush