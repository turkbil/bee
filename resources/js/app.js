/**
 * TENANT-SAFE FRONTEND APPLICATION
 * Bu dosya tenant sistemde güvenli çalışacak şekilde tasarlandı
 */

// Alpine.js - Tenant bağımsız frontend interactivity
import Alpine from 'alpinejs';

// Axios - CSRF token ve tenant-aware requests
import axios from 'axios';

// iXtif Theme JavaScript - Sticky systems, animations
import './ixtif-theme.js';

// Bootstrap Alpine.js globally
window.Alpine = Alpine;

// Livewire search entangle fix - Alpine başlamadan önce Livewire'ı bekle
document.addEventListener('livewire:init', () => {
    console.log('✅ Livewire initialized, Alpine can safely start');
});

// Livewire zaten Alpine'i başlatıyor, duplicate start'ı önle
if (!window.livewire) {
    // Alpine'i 100ms geciktir, Livewire components yüklensin
    setTimeout(() => {
        Alpine.start();
        console.log('✅ Alpine started after Livewire');
    }, 100);
}

// Configure Axios for tenant requests
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token setup - tenant-safe
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Tenant-aware base URL detection
const currentDomain = window.location.hostname;
const isSubdomain = currentDomain.includes('.') && currentDomain !== 'laravel.test';

if (isSubdomain) {
    // Tenant context
    console.log('🏢 Tenant mode detected:', currentDomain);
} else {
    // Central domain
    console.log('🏠 Central domain mode');
}

// Global error handler for tenant requests
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 403) {
            console.error('🚫 Tenant access denied');
        }
        return Promise.reject(error);
    }
);

// Language switcher functionality - tenant-aware
document.addEventListener('DOMContentLoaded', function() {
    // Language switching
    const languageSwitchers = document.querySelectorAll('[data-language-switch]');
    languageSwitchers.forEach(switcher => {
        switcher.addEventListener('click', function(e) {
            e.preventDefault();
            const locale = this.dataset.languageSwitch;

            // Tenant-safe language switching
            const switchUrl = isSubdomain
                ? `/language/${locale}`
                : `/language/${locale}`;

            window.location.href = switchUrl;
        });
    });

    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
});

console.log('✅ Tenant-safe frontend assets loaded');