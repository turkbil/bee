<?php
/**
 * PHPMyAdmin Advanced Configuration
 * Laravel Multi-Tenant System - Database Hiding & Auto-Login
 */

declare(strict_types=1);

// Server configuration
$i = 0;
$i++;
$cfg['Servers'][$i]['verbose'] = 'Laravel Docker MySQL';
$cfg['Servers'][$i]['host'] = 'mysql';
$cfg['Servers'][$i]['port'] = 3306;
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = 'pass';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

// KRITIK: Database gizleme - Regex pattern ile
$cfg['Servers'][$i]['hide_db'] = '^(information_schema|performance_schema|mysql|sys)$';

// PHPMyAdmin configuration storage ayarları
$cfg['Servers'][$i]['pmadb'] = 'phpmyadmin';
$cfg['Servers'][$i]['bookmarktable'] = 'pma__bookmark';
$cfg['Servers'][$i]['relation'] = 'pma__relation';
$cfg['Servers'][$i]['table_info'] = 'pma__table_info';
$cfg['Servers'][$i]['table_coords'] = 'pma__table_coords';
$cfg['Servers'][$i]['pdf_pages'] = 'pma__pdf_pages';
$cfg['Servers'][$i]['column_info'] = 'pma__column_info';
$cfg['Servers'][$i]['history'] = 'pma__history';
$cfg['Servers'][$i]['table_uiprefs'] = 'pma__table_uiprefs';
$cfg['Servers'][$i]['tracking'] = 'pma__tracking';
$cfg['Servers'][$i]['userconfig'] = 'pma__userconfig';
$cfg['Servers'][$i]['recent'] = 'pma__recent';
$cfg['Servers'][$i]['favorite'] = 'pma__favorite';
$cfg['Servers'][$i]['users'] = 'pma__users';
$cfg['Servers'][$i]['usergroups'] = 'pma__usergroups';
$cfg['Servers'][$i]['navigationhiding'] = 'pma__navigationhiding';
$cfg['Servers'][$i]['savedsearches'] = 'pma__savedsearches';
$cfg['Servers'][$i]['central_columns'] = 'pma__central_columns';
$cfg['Servers'][$i]['designer_settings'] = 'pma__designer_settings';
$cfg['Servers'][$i]['export_templates'] = 'pma__export_templates';

// Güvenlik
$cfg['blowfish_secret'] = 'laravel-multi-tenant-secret-key-2025-ultra-secure';

// UI Konfigürasyonu
$cfg['ThemeDefault'] = 'blueberry';
$cfg['DefaultLang'] = 'tr';
$cfg['ServerDefault'] = 1;

// Navigation ayarları - Database listesini kontrol et
$cfg['MaxNavigationItems'] = 50;
$cfg['NavigationTreeEnableGrouping'] = false; // Gruplamayı kapat
$cfg['NavigationTreeDbSeparator'] = '_';
$cfg['NavigationDisplayServers'] = false;
$cfg['DisplayServersList'] = false;

// Veritabanı listesi filtreleme
$cfg['NavigationTreeDisplayItemFilterMinimum'] = 1;

// FIXED: Cookie ve session ayarları - Uyarıları gider
$cfg['LoginCookieValidity'] = 14400; // 4 saat (session.gc_maxlifetime ile uyumlu)
$cfg['LoginCookieStore'] = 14400; // Aynı değer
$cfg['LoginCookieDeleteAll'] = true;

// Performans
$cfg['MemoryLimit'] = '512M';
$cfg['ExecTimeLimit'] = 300;

// Gereksiz özellik kapatmaları
$cfg['ShowStats'] = false;
$cfg['ShowServerInfo'] = false;
$cfg['ShowPhpInfo'] = false;
$cfg['ShowCreateDb'] = true; // Sadece Laravel database'leri için

// Export/Import
$cfg['Export']['method'] = 'quick';
$cfg['Export']['format'] = 'sql';

// Console ayarları
$cfg['Console']['StartHistory'] = true;
$cfg['Console']['AlwaysExpand'] = false;

// JavaScript ve CSS enjeksiyon sistemi
$cfg['SendErrorReports'] = 'never';
$cfg['environment'] = 'production';

// Custom header script injection
$cfg['CustomHeaderComment'] = '
<!-- Laravel Multi-Tenant Database Hiding System -->
<script type="text/javascript">
// Laravel Multi-Tenant PHPMyAdmin Database Hiding System
(function() {
    "use strict";
    
    const systemDatabases = ["information_schema", "performance_schema", "mysql", "sys"];
    let hideAttempts = 0;
    const maxAttempts = 30;
    
    function hideSystemDatabases() {
        hideAttempts++;
        
        // Navigation tree database linklerini gizle
        const dbLinks = document.querySelectorAll("#pma_navigation_tree a, .navigation_tree a");
        let hiddenCount = 0;
        
        dbLinks.forEach(function(link) {
            const text = link.textContent.trim().toLowerCase();
            const href = link.getAttribute("href") || "";
            
            systemDatabases.forEach(function(sysDb) {
                if (text === sysDb || href.includes("db=" + sysDb)) {
                    let parent = link.closest("li");
                    if (!parent) parent = link.parentElement;
                    
                    if (parent) {
                        parent.style.display = "none";
                        hiddenCount++;
                    }
                }
            });
        });
        
        // Database dropdown options gizle
        const selectOptions = document.querySelectorAll("select option");
        selectOptions.forEach(function(option) {
            if (systemDatabases.includes(option.value.toLowerCase())) {
                option.style.display = "none";
                option.disabled = true;
                hiddenCount++;
            }
        });
        
        if (hiddenCount > 0) {
            console.log("Laravel Multi-Tenant: " + hiddenCount + " sistem database gizlendi");
        }
        
        // Tekrar kontrol et
        if (hideAttempts < maxAttempts) {
            setTimeout(hideSystemDatabases, 300);
        }
    }
    
    // CSS gizleme kuralları ekle
    function addCustomCSS() {
        const style = document.createElement("style");
        style.textContent = `
            #pma_navigation_tree a[href*="db=information_schema"],
            #pma_navigation_tree a[href*="db=performance_schema"],
            #pma_navigation_tree a[href*="db=mysql"],
            #pma_navigation_tree a[href*="db=sys"] {
                display: none !important;
            }
            
            #pma_navigation_tree li:has(a[href*="db=information_schema"]),
            #pma_navigation_tree li:has(a[href*="db=performance_schema"]),
            #pma_navigation_tree li:has(a[href*="db=mysql"]),
            #pma_navigation_tree li:has(a[href*="db=sys"]) {
                display: none !important;
            }
            
            select option[value="information_schema"],
            select option[value="performance_schema"],
            select option[value="mysql"],
            select option[value="sys"] {
                display: none !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Sayfa yüklendiğinde çalıştır
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function() {
            addCustomCSS();
            setTimeout(hideSystemDatabases, 200);
        });
    } else {
        addCustomCSS();
        setTimeout(hideSystemDatabases, 200);
    }
    
    // DOM değişikliklerini izle
    const observer = new MutationObserver(function(mutations) {
        setTimeout(hideSystemDatabases, 100);
    });
    
    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    console.log("Laravel Multi-Tenant Database Hiding: Aktif");
})();
</script>

<style type="text/css">
/* Laravel Multi-Tenant Custom Styles */
.logo:after {
    content: " - Laravel Multi-Tenant";
    font-size: 11px;
    color: #6c757d;
    font-weight: normal;
}

/* System database gizleme - CSS fallback */
#pma_navigation_tree a[href*="information_schema"],
#pma_navigation_tree a[href*="performance_schema"],
#pma_navigation_tree a[href*="mysql"],
#pma_navigation_tree a[href*="sys"] {
    display: none !important;
}
</style>
';
?>