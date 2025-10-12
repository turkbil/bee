/**
 * Shop Product Detail Page JavaScript
 * Theme: Blank
 * Handles sticky sidebar, TOC navigation, and scroll spy functionality
 */

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
});
