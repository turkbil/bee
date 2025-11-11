/**
 * Navigation Loading States
 * Dil değiştirme ve cache temizleme butonları için loading state yönetimi
 */

document.addEventListener('livewire:init', () => {
    
    // Cache Loading States - Quick Actions Button
    Livewire.on('cacheStarted', (event) => {
        showQuickActionsLoading();
    });
    
    Livewire.on('cacheFinished', (event) => {
        hideQuickActionsLoading();
    });
    
    // Language Loading States - Language Switcher Button
    Livewire.on('languageStarted', (event) => {
        showLanguageSwitcherLoading();
    });
    
    Livewire.on('languageFinished', (event) => {
        hideLanguageSwitcherLoading();
    });
});

/**
 * Quick Actions Button Loading
 */
function showQuickActionsLoading() {
    const icon = document.getElementById('quick-actions-icon');
    if (icon) {
        icon.className = 'fa-solid fa-spinner fa-spin';
        icon.style.fontSize = '18px';
    } else {
    }
}

function hideQuickActionsLoading() {
    const icon = document.getElementById('quick-actions-icon');
    if (icon) {
        icon.className = 'fa-solid fa-grid-2';
        icon.style.fontSize = '18px';
    } else {
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