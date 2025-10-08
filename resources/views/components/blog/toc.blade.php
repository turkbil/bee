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

    <div class="rounded-2xl border border-slate-200 bg-slate-50 shadow-md shadow-slate-900/5 dark:border-slate-700 dark:bg-slate-900">
        <header class="flex items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-list-ul text-blue-500"></i>
                <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-600 dark:text-slate-300">
                    {{ $title }}
                </h3>
            </div>
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                {{ $totalItems }} başlık
            </span>
        </header>

        <nav class="px-5 py-4" aria-label="{{ $title }}">
            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-300" data-toc-list id="{{ $listId }}">
                @foreach ($toc as $item)
                    @include('components.blog.toc-item', ['item' => $item, 'level' => 0])
                @endforeach
            </ul>
        </nav>
    </div>

    @once
        @push('styles')
            <style>
                [data-toc-list] li a {
                    display: inline-flex;
                    gap: 0.5rem;
                    align-items: center;
                    width: 100%;
                    padding: 0.45rem 0.6rem;
                    border-radius: 0.75rem;
                    font-weight: 500;
                    text-decoration: none;
                    color: inherit;
                    transition: background-color 0.2s ease, color 0.2s ease;
                }

                [data-toc-list] li a:hover {
                    background-color: rgba(59, 130, 246, 0.08);
                    color: rgb(37, 99, 235);
                }

                html.dark [data-toc-list] li a:hover {
                    background-color: rgba(37, 99, 235, 0.12);
                    color: rgb(191, 219, 254);
                }

                [data-toc-list] li a[data-active="true"] {
                    background-color: rgba(37, 99, 235, 0.12);
                    color: rgb(29, 78, 216);
                }

                html.dark [data-toc-list] li a[data-active="true"] {
                    background-color: rgba(37, 99, 235, 0.18);
                    color: rgb(191, 219, 254);
                }

                [data-toc-list] .toc-level-1 {
                    padding-left: 1.25rem;
                }

                [data-toc-list] .toc-level-2 {
                    padding-left: 2rem;
                }

                [data-toc-list] .toc-level-3 {
                    padding-left: 2.75rem;
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

                        const setActive = (id) => {
                            links.forEach((link) => {
                                link.dataset.active = link.dataset.target === id ? 'true' : 'false';
                            });
                        };

                        links.forEach((link) => {
                            link.addEventListener('click', (event) => {
                                event.preventDefault();
                                const targetId = link.dataset.target;
                                const target = document.getElementById(targetId);

                                if (!target) {
                                    return;
                                }

                                const offset = 120;
                                const top = target.getBoundingClientRect().top + window.scrollY - offset;

                                window.scrollTo({
                                    top,
                                    behavior: 'smooth'
                                });

                                history.replaceState(null, '', `#${targetId}`);
                                setActive(targetId);
                            });
                        });

                        const initialHash = window.location.hash.slice(1);
                        if (initialHash) {
                            setActive(initialHash);
                        } else {
                            setActive(links[0].dataset.target);
                        }

                        if ('IntersectionObserver' in window) {
                            const observer = new IntersectionObserver((entries) => {
                                entries
                                    .filter(entry => entry.isIntersecting)
                                    .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top)
                                    .forEach((entry) => setActive(entry.target.id));
                            }, {
                                root: null,
                                rootMargin: '-70% 0px -20%',
                                threshold: 0
                            });

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
