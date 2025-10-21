/**
 * Shop Product Detail Page JavaScript
 * Theme: iXtif
 * Handles sticky sidebar, TOC navigation, scroll spy, and AI chat integration
 *
 * Version 2.0:
 * - ✅ AI Chat click handler (moved from inline)
 * - ✅ Updated TOC scroll behavior (moved from inline)
 * - ✅ Sticky sidebar functionality
 * - ✅ Scroll spy
 */

/**
 * AI Chat Click Handler
 * Opens AI chat widget in hero section when user clicks AI button
 */
function handleAIChatClick() {
    // Hero section'daki AI chat widget'ını kontrol et
    const chatWidget = document.querySelector('.ai-inline-widget, [x-data*="aiInlineWidget"]');

    if (!chatWidget) {
        // Widget bulunamadıysa hero section'a scroll yap
        window.scrollTo({top: 0, behavior: 'smooth'});
        return;
    }

    // Chat açık mı kontrol et
    const isOpen = chatWidget.classList.contains('open') ||
                  chatWidget.querySelector('[x-show="isOpen"]')?.style.display !== 'none';

    // Chat input alanını bul
    const chatInput = chatWidget.querySelector('textarea, input[type="text"]');
    const hasMessages = chatWidget.querySelectorAll('.message, [class*="message"]').length > 1;

    if (isOpen && !hasMessages && chatInput) {
        // Chat açık, mesaj yok ve input küçükse -> inputa tıkla (büyüt)
        chatInput.focus();
        chatInput.click();
    } else if (!isOpen) {
        // Chat kapalıysa -> aç
        const openButton = chatWidget.querySelector('button[x-on\\:click*="isOpen"], button[onclick*="open"]');
        if (openButton) {
            openButton.click();
        }
    }

    // Her durumda chat widget'a scroll yap
    chatWidget.scrollIntoView({behavior: 'smooth', block: 'center'});
}

// Global olarak erişilebilir yap
window.handleAIChatClick = handleAIChatClick;

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sticky-sidebar');
    const heroSection = document.getElementById('hero-section');
    const faqSection = document.getElementById('faq');
    const trustSignals = document.getElementById('trust-signals');
    const contactForm = document.getElementById('contact');
    const header = document.querySelector('header') || document.querySelector('nav');
    const tocBar = document.getElementById('toc-bar');
    const tocButtons = document.getElementById('toc-buttons');

    // ==========================================
    // 0. TOC BAR STICKY POSITIONING & HIDING
    // ==========================================
    function updateTocPosition() {
        if (header && tocBar) {
            const headerHeight = header.offsetHeight;
            tocBar.style.top = headerHeight + 'px';
        }
    }

    // TOC hide on scroll to contact form
    function handleTocVisibility() {
        if (!tocBar || !trustSignals) return;

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const trustSignalsTop = trustSignals.getBoundingClientRect().top + scrollTop;
        const headerHeight = header ? header.offsetHeight : 0;

        // Hide TOC when reaching trust signals
        if (scrollTop + headerHeight >= trustSignalsTop - 100) {
            tocBar.style.transform = 'translateY(-100%)';
        } else {
            tocBar.style.transform = 'translateY(0)';
        }
    }

    // Initial update and on resize
    updateTocPosition();
    window.addEventListener('resize', updateTocPosition);
    window.addEventListener('scroll', handleTocVisibility, {
        passive: true
    });

    // ==========================================
    // 1. SIDEBAR STICKY FUNCTIONALITY (Desktop Only)
    // ==========================================

    if (sidebar && heroSection && faqSection) {
        const sidebarParent = sidebar.parentElement;
        sidebarParent.style.position = 'relative';

        const HEADER_MARGIN = 32;

        function handleScroll() {
            // Only enable sticky on large screens (>= 1024px)
            if (window.innerWidth < 1024) {
                sidebar.style.position = '';
                sidebar.style.top = '';
                sidebar.style.width = '';
                sidebar.style.zIndex = '';
                return;
            }

            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const heroHeight = heroSection.offsetHeight;
            const headerHeight = header ? header.offsetHeight : 0;
            const tocHeight = tocBar ? tocBar.offsetHeight : 0;
            const sidebarHeight = sidebar.offsetHeight;
            const sidebarWidth = sidebar.offsetWidth;
            const faqRect = faqSection.getBoundingClientRect();
            const faqBottom = faqRect.top + faqRect.height + scrollTop;
            const parentTop = sidebarParent.getBoundingClientRect().top + scrollTop;

            if (scrollTop <= heroHeight) {
                sidebar.style.position = 'static';
                sidebar.style.top = '';
                sidebar.style.width = '';
                sidebar.style.zIndex = '';
                return;
            }

            const stickyOffset = headerHeight + tocHeight + HEADER_MARGIN;
            const stickyBottomPosition = scrollTop + stickyOffset + sidebarHeight;

            // Stop at FAQ section bottom (where FAQ ends) - NO GAP
            if (stickyBottomPosition >= faqBottom) {
                const absoluteTop = faqBottom - parentTop - sidebarHeight;
                sidebar.style.position = 'absolute';
                sidebar.style.top = absoluteTop + 'px';
                sidebar.style.width = sidebarWidth + 'px';
                sidebar.style.zIndex = '20';
                return;
            }

            sidebar.style.position = 'fixed';
            sidebar.style.top = stickyOffset + 'px';
            sidebar.style.width = sidebarWidth + 'px';
            sidebar.style.zIndex = '20';
        }

        window.addEventListener('scroll', handleScroll, {
            passive: true
        });
        window.addEventListener('resize', handleScroll);
        handleScroll();
    }

    // ==========================================
    // 2. SCROLL SPY & SMOOTH SCROLL
    // ==========================================
    const tocLinks = document.querySelectorAll('.toc-link');
    const sections = [];

    // Collect all sections with IDs
    tocLinks.forEach(link => {
        const target = link.getAttribute('data-target');
        const section = document.getElementById(target);
        if (section) {
            sections.push({
                element: section,
                link: link,
                id: target
            });
        }
    });

    // Smooth scroll on click
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const targetSection = document.getElementById(targetId);

            if (targetSection) {
                const headerHeight = header ? header.offsetHeight : 0;
                const tocHeight = tocBar ? tocBar.offsetHeight : 0;
                const offset = headerHeight + tocHeight + 20; // 20px extra padding

                const targetPosition = targetSection.getBoundingClientRect().top + window
                    .pageYOffset - offset;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Scroll Spy with Intersection Observer
    const observerOptions = {
        root: null,
        rootMargin: '-20% 0px -70% 0px',
        threshold: 0
    };

    const observerCallback = (entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Remove active class from all links
                tocLinks.forEach(link => {
                    link.classList.remove('bg-blue-600', 'dark:bg-blue-500',
                        'text-white');
                    link.classList.add('bg-gray-100', 'dark:bg-gray-800',
                        'text-gray-700', 'dark:text-gray-300');
                });

                // Add active class to current section's link
                const activeSection = sections.find(s => s.element === entry.target);
                if (activeSection) {
                    activeSection.link.classList.remove('bg-gray-100', 'dark:bg-gray-800',
                        'text-gray-700', 'dark:text-gray-300');
                    activeSection.link.classList.add('bg-blue-600', 'dark:bg-blue-500',
                        'text-white');

                    // Auto-scroll active button into view
                    if (tocButtons) {
                        activeSection.link.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest',
                            inline: 'center'
                        });
                    }
                }
            }
        });
    };

    const observer = new IntersectionObserver(observerCallback, observerOptions);

    // Observe all sections
    sections.forEach(section => {
        observer.observe(section.element);
    });

    // ==========================================
    // 3. UPDATED TOC SCROLL BEHAVIOR (v2.0)
    // ==========================================
    // TOC Bar: Initially relative, becomes fixed after scrolling past it
    // Replaces old simple sticky behavior with layout-shift prevention

    const tocPlaceholder = document.getElementById('toc-placeholder');

    if (tocBar && tocPlaceholder) {
        // 🚨 CRITICAL: Clear TOC initial state (prevent cache issues)
        tocBar.style.position = 'relative';
        tocBar.style.top = '0';
        tocBar.style.left = '';
        tocBar.style.right = '';
        tocBar.style.transform = '';
        tocBar.style.opacity = '';

        // Header height - Responsive
        const getHeaderHeight = () => {
            return window.innerWidth >= 1024 ? 84 : 56; // Desktop: 84px, Mobile: 56px
        };

        let mainNavHeight = getHeaderHeight();

        // Update header height on resize
        window.addEventListener('resize', () => {
            mainNavHeight = getHeaderHeight();
        });

        // Get TOC's initial offset from top
        let tocOffsetTop = tocBar.offsetTop;
        let tocHeight = tocBar.offsetHeight;

        // Scroll state
        let isTocFixed = false;
        let isTocHidden = false;

        function handleTocScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const tocContainer = document.getElementById('toc-container');

            // Update TOC offset if in relative mode
            if (!isTocFixed) {
                tocOffsetTop = tocBar.offsetTop;
                tocHeight = tocBar.offsetHeight;
            }

            // Make TOC fixed when scrolled past it - LAYOUT SHIFT PREVENTION
            const threshold = tocOffsetTop - mainNavHeight;

            if (scrollTop >= threshold && !isTocFixed && !isTocHidden) {
                // LAYOUT SHIFT PREVENTION: Show placeholder (hold TOC's space)
                tocPlaceholder.style.display = 'block';
                tocPlaceholder.style.height = tocHeight + 'px';

                // Make TOC fixed
                tocBar.style.position = 'fixed';
                tocBar.style.top = mainNavHeight + 'px';
                tocBar.style.left = '0';
                tocBar.style.right = '0';
                tocBar.style.zIndex = '40';

                // Reduce padding (compact mode)
                if (tocContainer) {
                    tocContainer.style.paddingTop = '0.375rem'; // py-1.5
                    tocContainer.style.paddingBottom = '0.375rem';
                }
                isTocFixed = true;
            } else if (scrollTop < threshold && isTocFixed) {
                // Hide placeholder
                tocPlaceholder.style.display = 'none';

                // Make TOC relative
                tocBar.style.position = 'relative';
                tocBar.style.top = 'auto';
                tocBar.style.left = 'auto';
                tocBar.style.right = 'auto';

                // Normal padding
                if (tocContainer) {
                    tocContainer.style.paddingTop = '0.5rem'; // py-2
                    tocContainer.style.paddingBottom = '0.5rem';
                }
                isTocFixed = false;
            }
        }

        // Scroll event
        window.addEventListener('scroll', handleTocScroll);

        // Trust signals intersection - Hide TOC when visible
        if (trustSignals) {
            const tocObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Trust signals visible - Hide TOC
                        tocBar.style.transform = 'translateY(-100%)';
                        tocBar.style.opacity = '0';
                        isTocHidden = true;
                    } else {
                        // Trust signals not visible - Show TOC
                        // ONLY apply transform/opacity in fixed mode (preserve initial state)
                        if (isTocFixed && isTocHidden) {
                            tocBar.style.transform = 'translateY(0)';
                            tocBar.style.opacity = '1';
                        }
                        isTocHidden = false;
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '-80px 0px 0px 0px'
            });

            tocObserver.observe(trustSignals);
        }
    }
});
