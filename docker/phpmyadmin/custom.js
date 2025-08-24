// Laravel Multi-Tenant PHPMyAdmin Database Hiding System
// System database'lerini gizlemek için JavaScript

(function() {
    'use strict';
    
    const systemDatabases = ['information_schema', 'performance_schema', 'mysql', 'sys'];
    let hideAttempts = 0;
    const maxAttempts = 50;
    
    function hideSystemDatabases() {
        hideAttempts++;
        
        // Navigation tree içindeki database linklerini bul
        const dbLinks = document.querySelectorAll('#pma_navigation_tree a, .navigation_tree a, [data-table-name]');
        let hiddenCount = 0;
        
        dbLinks.forEach(function(link) {
            const text = link.textContent.trim().toLowerCase();
            const href = link.getAttribute('href') || '';
            
            // System database isimlerini kontrol et
            systemDatabases.forEach(function(sysDb) {
                if (text === sysDb || href.includes('db=' + sysDb) || href.includes('server=1&db=' + sysDb)) {
                    // Parent elementi bul ve gizle
                    let parent = link.closest('li');
                    if (!parent) parent = link.closest('div');
                    if (!parent) parent = link.parentElement;
                    
                    if (parent) {
                        parent.style.display = 'none';
                        hiddenCount++;
                        console.log('Laravel Multi-Tenant: Gizlendi ->', sysDb);
                    }
                }
            });
        });
        
        // Database listesi dropdown'ında da gizle
        const selectOptions = document.querySelectorAll('select option');
        selectOptions.forEach(function(option) {
            const value = option.value.toLowerCase();
            if (systemDatabases.includes(value)) {
                option.style.display = 'none';
                option.disabled = true;
                hiddenCount++;
            }
        });
        
        // Sol sidebar'da database listesini kontrol et
        const sidebarItems = document.querySelectorAll('.list_container .list, .list_container a');
        sidebarItems.forEach(function(item) {
            const text = item.textContent.trim().toLowerCase();
            if (systemDatabases.includes(text)) {
                item.style.display = 'none';
                hiddenCount++;
            }
        });
        
        if (hiddenCount > 0) {
            console.log(`Laravel Multi-Tenant: ${hiddenCount} sistem database'i gizlendi`);
        }
        
        // Tekrar kontrol et (sayfanın dinamik yüklenme durumu için)
        if (hideAttempts < maxAttempts) {
            setTimeout(hideSystemDatabases, 500);
        }
    }
    
    // CSS ile de gizleme ekle
    function addCustomCSS() {
        const style = document.createElement('style');
        style.textContent = `
            /* System database'lerini CSS ile gizle */
            #pma_navigation_tree a[href*="db=information_schema"],
            #pma_navigation_tree a[href*="db=performance_schema"],
            #pma_navigation_tree a[href*="db=mysql"],
            #pma_navigation_tree a[href*="db=sys"],
            .navigation_tree a[href*="db=information_schema"],
            .navigation_tree a[href*="db=performance_schema"],
            .navigation_tree a[href*="db=mysql"],
            .navigation_tree a[href*="db=sys"] {
                display: none !important;
            }
            
            /* Parent container'ları da gizle */
            #pma_navigation_tree li:has(a[href*="db=information_schema"]),
            #pma_navigation_tree li:has(a[href*="db=performance_schema"]),
            #pma_navigation_tree li:has(a[href*="db=mysql"]),
            #pma_navigation_tree li:has(a[href*="db=sys"]) {
                display: none !important;
            }
            
            /* Select option'ları gizle */
            select option[value="information_schema"],
            select option[value="performance_schema"],
            select option[value="mysql"],
            select option[value="sys"] {
                display: none !important;
            }
            
            /* Laravel branding */
            .logo:after {
                content: " - Laravel Multi-Tenant";
                font-size: 12px;
                color: #6c757d;
            }
        `;
        document.head.appendChild(style);
        console.log('Laravel Multi-Tenant: CSS gizleme kuralları eklendi');
    }
    
    // Sayfa yüklendiğinde çalıştır
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            addCustomCSS();
            setTimeout(hideSystemDatabases, 100);
        });
    } else {
        addCustomCSS();
        setTimeout(hideSystemDatabases, 100);
    }
    
    // AJAX istekleri sonrası da çalıştır
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args).then(function(response) {
            setTimeout(hideSystemDatabases, 200);
            return response;
        });
    };
    
    // MutationObserver ile DOM değişikliklerini izle
    const observer = new MutationObserver(function(mutations) {
        let shouldHide = false;
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                shouldHide = true;
            }
        });
        
        if (shouldHide) {
            setTimeout(hideSystemDatabases, 100);
        }
    });
    
    // Observer'ı başlat
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    console.log('Laravel Multi-Tenant Database Hiding System: Aktif');
})();