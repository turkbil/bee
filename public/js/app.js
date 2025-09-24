// COMPILED FRONTEND JS - Manual Build
console.log('🚀 Tenant-safe frontend loaded');

// CSRF token setup
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Mobile menu functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');

    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('open');
        });
    }

    // Language switcher
    const langSwitchers = document.querySelectorAll('[data-language-switch]');
    langSwitchers.forEach(switcher => {
        switcher.addEventListener('click', function(e) {
            e.preventDefault();
            const locale = this.dataset.languageSwitch;
            window.location.href = `/language/${locale}`;
        });
    });
});
