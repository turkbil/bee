/**
 * 🎯 GSAP PROGRESSIVE STICKY SYSTEM V8
 * TOC Bar: Smooth header sticking → Disappears at trust-signals
 * Sidebar: Smooth header+TOC+16px tracking → Stops at FAQ bottom
 * Performance: GSAP ScrollTrigger + Smooth easing
 */

(function() {
    'use strict';

    // Wait for GSAP to be available (app.js might still be loading)
    function initWhenGSAPReady() {
        if (!window.gsap || !window.ScrollTrigger) {
            // GSAP not ready, wait 50ms and try again
            setTimeout(initWhenGSAPReady, 50);
            return;
        }

        // GSAP is ready, proceed with initialization
        const gsap = window.gsap;
        const ScrollTrigger = window.ScrollTrigger;

    // ============================================
    // 📊 CONFIG
    // ============================================
    const CONFIG = {
        SIDEBAR_GAP: 16,
        DESKTOP_BREAKPOINT: 1024
    };

    // ============================================
    // 🎯 DOM ELEMENTS
    // ============================================
    const elements = {
        header: document.getElementById('main-header'),
        tocBar: document.getElementById('toc-bar'),
        sidebar: document.getElementById('sticky-sidebar'),
        trustSignals: document.getElementById('trust-signals'),
        faqSection: document.getElementById('faq'),
        tocLinks: document.querySelectorAll('.toc-link'),
        sections: document.querySelectorAll('[id^="description"], [id^="features"], [id^="competitive"], [id^="gallery"], [id^="variants"], [id^="technical"], [id^="accessories"], [id^="usecases"], [id^="industries"], [id^="certifications"], [id^="warranty"], [id^="faq"], [id^="contact"]')
    };

    // ============================================
    // 🎨 TOC BAR - GSAP SCROLL TRIGGER
    // ============================================
    function initTOC() {
        if (!elements.tocBar || !elements.header) return;

        const tocBar = elements.tocBar;
        const header = elements.header;

        // Get initial TOC position
        const tocInitialTop = tocBar.offsetTop;

        ScrollTrigger.create({
            trigger: tocBar,
            start: () => `top ${header.offsetHeight}px`,
            end: () => elements.trustSignals ? `${elements.trustSignals.offsetTop - header.offsetHeight - tocBar.offsetHeight}px top` : 'bottom top',
            pin: true,
            pinSpacing: false,
            onUpdate: (self) => {
                const headerHeight = header.offsetHeight;

                // Update TOC position smoothly
                gsap.set(tocBar, {
                    position: 'fixed',
                    top: headerHeight,
                    left: 0,
                    right: 0,
                    zIndex: 40
                });

                // Trust signals collision detection
                if (elements.trustSignals) {
                    const trustRect = elements.trustSignals.getBoundingClientRect();
                    const tocBottom = headerHeight + tocBar.offsetHeight;
                    const shouldHide = trustRect.top <= (tocBottom - 20);

                    gsap.to(tocBar, {
                        y: shouldHide ? '-100%' : '0%',
                        opacity: shouldHide ? 0 : 1,
                        pointerEvents: shouldHide ? 'none' : 'auto',
                        duration: 0.3,
                        ease: 'power2.out'
                    });
                }
            },
            onLeaveBack: () => {
                // Return to initial state
                gsap.set(tocBar, {
                    clearProps: 'all'
                });
            }
        });

        // Refresh on resize (responsive header height changes)
        ScrollTrigger.addEventListener('refresh', () => {
            ScrollTrigger.refresh();
        });
    }

    // ============================================
    // 📌 SIDEBAR - GSAP DYNAMIC STICKY
    // ============================================
    function initSidebar() {
        if (!elements.sidebar || !elements.faqSection) return;

        // Desktop only
        const mm = gsap.matchMedia();

        mm.add(`(min-width: ${CONFIG.DESKTOP_BREAKPOINT}px)`, () => {
            const sidebar = elements.sidebar;
            const sidebarParent = sidebar.parentElement;

            ScrollTrigger.create({
                trigger: sidebar,
                start: () => {
                    const headerHeight = elements.header ? elements.header.offsetHeight : 0;
                    const tocHeight = elements.tocBar ? elements.tocBar.offsetHeight : 0;
                    return `top ${headerHeight + tocHeight + CONFIG.SIDEBAR_GAP}px`;
                },
                end: () => {
                    const faqRect = elements.faqSection.getBoundingClientRect();
                    const faqBottom = window.pageYOffset + faqRect.top + faqRect.height;
                    const sidebarHeight = sidebar.offsetHeight;
                    return `${faqBottom - sidebarHeight}px bottom`;
                },
                pin: true,
                pinSpacing: false,
                onUpdate: (self) => {
                    const headerHeight = elements.header ? elements.header.offsetHeight : 0;
                    const tocHeight = elements.tocBar ? elements.tocBar.offsetHeight : 0;
                    const stickyTop = headerHeight + tocHeight + CONFIG.SIDEBAR_GAP;

                    // Smooth sidebar positioning
                    gsap.set(sidebar, {
                        position: 'fixed',
                        top: stickyTop,
                        width: sidebarParent ? sidebarParent.offsetWidth : 'auto'
                    });
                }
            });
        });
    }

    // ============================================
    // 🎯 ACTIVE SECTION TRACKING (IntersectionObserver)
    // ============================================
    function initSectionTracking() {
        if (!elements.tocLinks.length || !elements.sections.length) return;

        const observerOptions = {
            rootMargin: '-20% 0px -75% 0px',
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const sectionId = entry.target.getAttribute('id');

                    // Update TOC links with smooth transition
                    elements.tocLinks.forEach(link => {
                        const target = link.getAttribute('data-target');
                        if (target === sectionId) {
                            gsap.to(link, {
                                backgroundColor: '#2563eb',
                                color: '#ffffff',
                                duration: 0.3,
                                ease: 'power2.out'
                            });
                        } else {
                            gsap.to(link, {
                                backgroundColor: '#f3f4f6',
                                color: '#374151',
                                duration: 0.3,
                                ease: 'power2.out'
                            });
                        }
                    });
                }
            });
        }, observerOptions);

        elements.sections.forEach(section => observer.observe(section));
    }

    // ============================================
    // 🎯 SMOOTH SCROLL FOR TOC LINKS (GSAP)
    // ============================================
    function initSmoothScroll() {
        elements.tocLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('href');
                const targetElement = document.querySelector(target);

                if (targetElement) {
                    const headerHeight = elements.header ? elements.header.offsetHeight : 0;
                    const tocHeight = elements.tocBar ? elements.tocBar.offsetHeight : 0;
                    const offset = headerHeight + tocHeight + 20;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;

                    gsap.to(window, {
                        scrollTo: offsetPosition,
                        duration: 1,
                        ease: 'power2.inOut'
                    });
                }
            });
        });
    }

    // ============================================
    // 🚀 INITIALIZATION
    // ============================================
    function init() {
        // Init GSAP ScrollTrigger features
        initTOC();
        initSidebar();
        initSectionTracking();
        initSmoothScroll();

        // Refresh on window resize (responsive)
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                ScrollTrigger.refresh();
            }, 150);
        });

        console.log('✅ GSAP Progressive Sticky System initialized');
    }

    // Start when DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    } // Close initWhenGSAPReady function

    // Start initialization (will wait for GSAP if needed)
    initWhenGSAPReady();

})();
