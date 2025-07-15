/**
 * Navigation Loading States
 * Dil değiştirme ve cache temizleme butonları için loading state yönetimi
 */

document.addEventListener('livewire:init', () => {
    
    // Cache Loading States - Quick Actions Button
    Livewire.on('cacheStarted', (event) => {
        console.log('🔄 Cache temizleme başladı, quick actions loading gösteriliyor...');
        showQuickActionsLoading();
    });
    
    Livewire.on('cacheFinished', (event) => {
        console.log('✅ Cache temizleme bitti, quick actions loading gizleniyor...');
        hideQuickActionsLoading();
    });
    
    // Language Loading States - Language Switcher Button
    Livewire.on('languageStarted', (event) => {
        console.log('🌍 Dil değiştirme başladı, language loading gösteriliyor...');
        showLanguageSwitcherLoading();
    });
    
    Livewire.on('languageFinished', (event) => {
        console.log('🌍 Dil değiştirme bitti, language loading gizleniyor...');
        hideLanguageSwitcherLoading();
    });
});

/**
 * Quick Actions Button Loading
 */
function showQuickActionsLoading() {
    console.log('🔄 showQuickActionsLoading çağrıldı');
    const icon = document.getElementById('quick-actions-icon');
    if (icon) {
        console.log('✅ quick-actions-icon bulundu, spinner yapılıyor');
        icon.className = 'fa-solid fa-spinner fa-spin';
        icon.style.fontSize = '18px';
    } else {
        console.log('❌ quick-actions-icon bulunamadı');
    }
}

function hideQuickActionsLoading() {
    console.log('✅ hideQuickActionsLoading çağrıldı');
    const icon = document.getElementById('quick-actions-icon');
    if (icon) {
        console.log('✅ quick-actions-icon bulundu, grid yapılıyor');
        icon.className = 'fa-solid fa-grid-2';
        icon.style.fontSize = '18px';
    } else {
        console.log('❌ quick-actions-icon bulunamadı');
    }
}

/**
 * Language Switcher Button Loading
 */
function showLanguageSwitcherLoading() {
    const langContainer = document.querySelector('.lang-icon-container');
    if (langContainer) {
        const flagSpan = langContainer.querySelector('span');
        const icon = langContainer.querySelector('i');
        
        // Flag varsa değiştir
        if (flagSpan) {
            flagSpan.style.display = 'none';
            // Spinner ekle
            const spinner = document.createElement('i');
            spinner.className = 'fa-solid fa-spinner fa-spin language-loading-spinner';
            spinner.style.fontSize = '18px';
            langContainer.appendChild(spinner);
        } 
        // Icon varsa değiştir
        else if (icon) {
            icon.className = 'fa-solid fa-spinner fa-spin';
            icon.style.fontSize = '18px';
        }
    }
}

function hideLanguageSwitcherLoading() {
    const langContainer = document.querySelector('.lang-icon-container');
    if (langContainer) {
        const spinner = langContainer.querySelector('.language-loading-spinner');
        const flagSpan = langContainer.querySelector('span');
        const icon = langContainer.querySelector('i.fa-spinner');
        
        // Spinner'ı kaldır ve flag'i göster
        if (spinner) {
            spinner.remove();
            if (flagSpan) {
                flagSpan.style.display = 'inline';
            }
        }
        // Icon spinner'ını geri değiştir
        else if (icon) {
            icon.className = 'fa-solid fa-language';
            icon.style.fontSize = '18px';
        }
    }
}