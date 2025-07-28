/**
 * CORE SYSTEM SCRIPTS - DO NOT MODIFY
 * ===================================
 * Bu dosya sistem çekirdeği için kritik JavaScript kodlarını içerir.
 * TEMA DEĞİŞİKLİKLERİNDEN ETKİLENMEZ - HER ZAMAN YÜKLÜ OLMALIDIR
 * 
 * 🚨 UYARI: BU DOSYA YAPAY ZEKA TARAFINDAN DEĞİŞTİRİLMEMELİDİR
 * 🚨 WARNING: THIS FILE SHOULD NOT BE MODIFIED BY AI
 * 
 * Version: 1.0.0
 * Last Updated: 2025-07-28
 * Author: System Core
 * 
 * İçerik:
 * - Language Switcher System
 * - Dark Mode Detection
 * - Core Utilities
 */

(function() {
    'use strict';
    
    // ========================================
    // LANGUAGE SWITCHER SYSTEM
    // ========================================
    
    /**
     * Language switch handler with loading overlay
     */
    function switchLanguage(e) {
        e.preventDefault();
        
        const targetUrl = this.href;
        
        // Create overlay
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            backdrop-filter: blur(0px);
            -webkit-backdrop-filter: blur(0px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999999;
            transition: all 0.3s ease;
        `;
        
        // Create spinner container - minimal design
        const spinnerContainer = document.createElement('div');
        spinnerContainer.style.cssText = `
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            opacity: 0;
            transform: scale(0.9);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        `;
        
        // Spinner with minimal design - just dots
        const spinner = document.createElement('div');
        spinner.style.cssText = 'display: flex; gap: 8px; align-items: center; justify-content: center;';
        spinner.innerHTML = `
            <div style="
                width: 12px;
                height: 12px;
                background: ${isDarkMode() ? '#93c5fd' : '#60a5fa'};
                border-radius: 50%;
                animation: pulse 1.4s ease-in-out infinite;
                animation-delay: -0.32s;
            "></div>
            <div style="
                width: 12px;
                height: 12px;
                background: ${isDarkMode() ? '#93c5fd' : '#60a5fa'};
                border-radius: 50%;
                animation: pulse 1.4s ease-in-out infinite;
                animation-delay: -0.16s;
            "></div>
            <div style="
                width: 12px;
                height: 12px;
                background: ${isDarkMode() ? '#93c5fd' : '#60a5fa'};
                border-radius: 50%;
                animation: pulse 1.4s ease-in-out infinite;
            "></div>
        `;
        
        // Language text - minimal style
        const langText = document.createElement('p');
        langText.style.cssText = `
            font-size: 0.875rem;
            margin: 0;
            font-weight: 500;
            letter-spacing: 0.05em;
            color: ${isDarkMode() ? '#d1d5db' : '#6b7280'};
            text-transform: uppercase;
        `;
        langText.textContent = getLanguageText();
        
        // Add CSS animations if not already added
        if (!document.getElementById('core-system-animations')) {
            const style = document.createElement('style');
            style.id = 'core-system-animations';
            style.textContent = `
                @keyframes pulse {
                    0% {
                        transform: scale(1);
                        opacity: 1;
                    }
                    50% {
                        transform: scale(0.8);
                        opacity: 0.5;
                    }
                    100% {
                        transform: scale(1);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Assemble elements - spinner first, then text
        spinnerContainer.appendChild(spinner);
        spinnerContainer.appendChild(langText);
        overlay.appendChild(spinnerContainer);
        document.body.appendChild(overlay);
        
        // Trigger animations
        requestAnimationFrame(() => {
            overlay.style.backdropFilter = 'blur(12px)';
            overlay.style.webkitBackdropFilter = 'blur(12px)';
            overlay.style.background = isDarkMode() ? 'rgba(17, 24, 39, 0.5)' : 'rgba(249, 250, 251, 0.5)';
            
            spinnerContainer.style.opacity = '1';
            spinnerContainer.style.transform = 'scale(1) translateY(0)';
        });
        
        // Clear caches and redirect
        if ('caches' in window) {
            caches.keys().then(function(names) {
                for (let name of names) {
                    caches.delete(name);
                }
            });
        }
        
        // Redirect after animation
        setTimeout(() => {
            // Direkt URL'e git - timestamp ekleme
            window.location.href = targetUrl;
        }, 500);
    }
    
    // ========================================
    // DARK MODE DETECTION
    // ========================================
    
    /**
     * Check if dark mode is active
     */
    function isDarkMode() {
        return document.documentElement.classList.contains('dark') || 
               document.body.classList.contains('dark') ||
               localStorage.getItem('theme') === 'dark' ||
               (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
    }
    
    // ========================================
    // LANGUAGE UTILITIES
    // ========================================
    
    /**
     * Get localized text for language switcher
     */
    function getLanguageText() {
        const lang = document.documentElement.lang || 'en';
        const texts = {
            'tr': 'Dil değiştiriliyor',
            'en': 'Switching language',
            'ar': 'تغيير اللغة'
        };
        return texts[lang] || texts['en'];
    }
    
    // ========================================
    // INITIALIZATION
    // ========================================
    
    /**
     * Initialize core system features
     */
    function initCoreSystem() {
        // Initialize language switcher
        initLanguageSwitcher();
        
        // Future core features can be initialized here
    }
    
    /**
     * Initialize language switcher functionality
     */
    function initLanguageSwitcher() {
        // Find all language switch links - artık direkt alternate URL'leri kullanıyoruz
        const languageSwitcher = document.querySelector('.language-switcher-header');
        
        if (languageSwitcher) {
            // Dropdown içindeki tüm dil linklerini bul
            const languageLinks = languageSwitcher.querySelectorAll('a[href]:not([href="#"])');
            
            languageLinks.forEach(link => {
                // Sadece aktif olmayan dil linklerine event ekle
                if (!link.classList.contains('bg-blue-50') && !link.classList.contains('text-blue-600')) {
                    link.addEventListener('click', switchLanguage);
                }
            });
        }
        
        // Eski /language/ route'ları için fallback (varsa)
        const oldLanguageLinks = document.querySelectorAll('a[href^="/language/"]');
        oldLanguageLinks.forEach(link => {
            link.addEventListener('click', switchLanguage);
        });
    }
    
    // ========================================
    // EVENT LISTENERS
    // ========================================
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCoreSystem);
    } else {
        initCoreSystem();
    }
    
    // Re-initialize on dynamic content load (for Alpine.js, Livewire, etc.)
    document.addEventListener('alpine:initialized', initLanguageSwitcher);
    document.addEventListener('livewire:navigated', initLanguageSwitcher);
    document.addEventListener('turbo:load', initLanguageSwitcher);
    
    // ========================================
    // PUBLIC API (if needed by other scripts)
    // ========================================
    
    window.CoreSystem = {
        isDarkMode: isDarkMode,
        initLanguageSwitcher: initLanguageSwitcher,
        version: '1.0.0'
    };
    
})();

// 🚨 END OF CORE SYSTEM SCRIPTS - DO NOT MODIFY 🚨