@props([
    'toc' => [],
    'title' => 'İçindekiler',
    'count' => null,
])

@if(!empty($toc))
    @php
        $calculateTotal = function (array $items) use (&$calculateTotal): int {
            $total = 0;
            foreach ($items as $entry) {
                $total++;
                if (!empty($entry['children'])) {
                    $total += $calculateTotal($entry['children']);
                }
            }

            return $total;
        };

        $totalItems = $count ?? $calculateTotal($toc);
        $listId = 'toc-' . \Illuminate\Support\Str::uuid()->toString();
    @endphp

    <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-md pb-4">
        <header class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-blue-500 dark:bg-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-list-ul text-xs text-white"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                    {{ $title }}
                </h3>
            </div>
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                {{ $totalItems }}
            </span>
        </header>

        <nav class="px-4 py-4 pb-8 max-h-[350px] overflow-y-auto toc-scroll" aria-label="{{ $title }}">
            <ul class="space-y-1 pb-6" data-toc-list id="{{ $listId }}">
                @foreach ($toc as $item)
                    @include('components.blog.toc-item', ['item' => $item, 'level' => 0])
                @endforeach
            </ul>
        </nav>
    </div>

    @once
        @push('styles')
            <style>
                /* TOC Link */
                .toc-link {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    padding: 0.5rem 0.75rem;
                    border-radius: 0.5rem;
                    text-decoration: none;
                    transition: background-color 0.2s ease, color 0.2s ease;
                }

                /* TOC Dot */
                .toc-dot {
                    flex-shrink: 0;
                    width: 5px;
                    height: 5px;
                    border-radius: 50%;
                    background-color: #94a3b8;
                    transition: background-color 0.2s ease;
                }

                html.dark .toc-dot {
                    background-color: #64748b;
                }

                /* TOC Text */
                .toc-text {
                    flex: 1;
                    font-size: 0.875rem;
                    font-weight: 500;
                    line-height: 1.5;
                    color: #64748b;
                    transition: color 0.2s ease;
                }

                html.dark .toc-text {
                    color: #94a3b8;
                }

                /* Hover */
                .toc-link:hover {
                    background-color: #f1f5f9;
                }

                .toc-link:hover .toc-dot {
                    background-color: #3b82f6;
                }

                .toc-link:hover .toc-text {
                    color: #3b82f6;
                }

                html.dark .toc-link:hover {
                    background-color: #1e293b;
                }

                html.dark .toc-link:hover .toc-dot {
                    background-color: #60a5fa;
                }

                html.dark .toc-link:hover .toc-text {
                    color: #60a5fa;
                }

                /* Active */
                .toc-link[data-active="true"] {
                    background-color: #eff6ff;
                }

                .toc-link[data-active="true"] .toc-dot {
                    background-color: #3b82f6;
                }

                .toc-link[data-active="true"] .toc-text {
                    color: #1e40af;
                    font-weight: 600;
                }

                html.dark .toc-link[data-active="true"] {
                    background-color: #1e3a5f;
                }

                html.dark .toc-link[data-active="true"] .toc-dot {
                    background-color: #60a5fa;
                }

                html.dark .toc-link[data-active="true"] .toc-text {
                    color: #93c5fd;
                    font-weight: 600;
                }

                /* Levels */
                .toc-level-0 .toc-text {
                    font-weight: 600;
                }

                .toc-level-1 {
                    margin-left: 1rem;
                }

                .toc-level-2 {
                    margin-left: 2rem;
                }

                .toc-level-2 .toc-text {
                    font-size: 0.8125rem;
                }

                .toc-level-3 {
                    margin-left: 3rem;
                }

                .toc-level-3 .toc-text {
                    font-size: 0.75rem;
                }

                /* Children */
                .toc-children {
                    margin-top: 0.125rem;
                    margin-bottom: 0.125rem;
                    padding-left: 0;
                    list-style: none;
                }

                /* Custom Scrollbar */
                .toc-scroll {
                    scrollbar-width: thin;
                    scrollbar-color: rgba(148, 163, 184, 0.3) transparent;
                }

                .toc-scroll::-webkit-scrollbar {
                    width: 6px;
                }

                .toc-scroll::-webkit-scrollbar-track {
                    background: transparent;
                }

                .toc-scroll::-webkit-scrollbar-thumb {
                    background-color: rgba(148, 163, 184, 0.3);
                    border-radius: 3px;
                }

                .toc-scroll::-webkit-scrollbar-thumb:hover {
                    background-color: rgba(148, 163, 184, 0.5);
                }

                html.dark .toc-scroll {
                    scrollbar-color: rgba(71, 85, 105, 0.5) transparent;
                }

                html.dark .toc-scroll::-webkit-scrollbar-thumb {
                    background-color: rgba(71, 85, 105, 0.5);
                }

                html.dark .toc-scroll::-webkit-scrollbar-thumb:hover {
                    background-color: rgba(71, 85, 105, 0.7);
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const tocLists = document.querySelectorAll('[data-toc-list]');

                    tocLists.forEach((list) => {
                        const links = Array.from(list.querySelectorAll('a[data-target]'));
                        if (!links.length) {
                            return;
                        }

                        let isAutoScrolling = false;
                        let tocScrollTimeout = null;

                        // Passive: Sadece active state değiştir, scroll ETME
                        const setActivePassive = (id) => {
                            links.forEach((link) => {
                                link.dataset.active = link.dataset.target === id ? 'true' : 'false';
                            });
                        };

                        // Active: State değiştir VE TOC'yi scroll et
                        const setActiveWithScroll = (id) => {
                            links.forEach((link) => {
                                const isActive = link.dataset.target === id;
                                link.dataset.active = isActive ? 'true' : 'false';

                                if (isActive && link.closest('.toc-scroll')) {
                                    const tocContainer = link.closest('.toc-scroll');
                                    const linkRect = link.getBoundingClientRect();
                                    const containerRect = tocContainer.getBoundingClientRect();

                                    // Link container'ın görünür alanında değilse scroll et
                                    if (linkRect.top < containerRect.top || linkRect.bottom > containerRect.bottom) {
                                        // Smooth scroll TOC içinde
                                        const scrollTop = link.offsetTop - tocContainer.offsetTop - (containerRect.height / 2) + (linkRect.height / 2);
                                        tocContainer.scrollTo({
                                            top: scrollTop,
                                            behavior: 'smooth'
                                        });
                                    }
                                }
                            });
                        };

                        // Manual click: Hem sayfa hem TOC scroll
                        links.forEach((link) => {
                            link.addEventListener('click', (event) => {
                                event.preventDefault();
                                const targetId = link.dataset.target;
                                const target = document.getElementById(targetId);

                                if (!target) {
                                    return;
                                }

                                // Flag: Auto-scroll başlıyor
                                isAutoScrolling = true;

                                const offset = 120;
                                const top = target.getBoundingClientRect().top + window.scrollY - offset;

                                window.scrollTo({
                                    top,
                                    behavior: 'smooth'
                                });

                                history.replaceState(null, '', `#${targetId}`);
                                setActiveWithScroll(targetId);

                                // Flag temizle (scroll bitince)
                                setTimeout(() => {
                                    isAutoScrolling = false;
                                }, 1000);
                            });
                        });

                        // Initial hash
                        const initialHash = window.location.hash.slice(1);
                        if (initialHash) {
                            setActivePassive(initialHash);
                        } else if (links.length > 0) {
                            setActivePassive(links[0].dataset.target);
                        }

                        // IntersectionObserver: Sadece passive update (scroll ETME!)
                        if ('IntersectionObserver' in window) {
                            const observer = new IntersectionObserver((entries) => {
                                // Auto-scroll sırasında observer'ı yok say
                                if (isAutoScrolling) {
                                    return;
                                }

                                // Debounce: Çok hızlı değişimleri engelle
                                clearTimeout(tocScrollTimeout);
                                tocScrollTimeout = setTimeout(() => {
                                    const visibleEntries = entries.filter(entry => entry.isIntersecting);

                                    if (visibleEntries.length > 0) {
                                        // En üstteki görünür heading'i seç
                                        const topEntry = visibleEntries
                                            .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top)[0];

                                        if (topEntry && topEntry.target.id) {
                                            // Sadece passive update - TOC'yi SCROLL ETME
                                            setActivePassive(topEntry.target.id);
                                        }
                                    }
                                }, 150); // 150ms debounce
                            }, {
                                root: null,
                                rootMargin: '-20% 0px -60% 0px', // Daha dar margin
                                threshold: 0
                            });

                            // Tüm heading'leri observe et
                            links.forEach((link) => {
                                const target = document.getElementById(link.dataset.target);
                                if (target) {
                                    observer.observe(target);
                                }
                            });
                        }
                    });
                });
            </script>
        @endpush
    @endonce
@endif
