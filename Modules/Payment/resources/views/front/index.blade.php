@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="bg-white dark:bg-gray-900" x-data="paymentsIndex()" x-init="init()">

        <!-- Header -->
        <div class="border-b border-gray-100 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="max-w-full">
                    <h1 class="text-4xl font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('payment::front.general.payments') }}
                    </h1>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            @if ($items->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    @foreach ($items as $item)
                        <article class="group">
                            <div class="mb-6">
                                <time class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->created_at->format('d M Y') }}
                                </time>
                            </div>

                            <h2
                                class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                @php
                                    $currentLocale = app()->getLocale();
                                    $slugData = $item->getRawOriginal('slug');

                                    if (is_array($slugData)) {
                                        $slug = $slugData[$currentLocale] ?? ($slugData['tr'] ?? reset($slugData));
                                    } else {
                                        $slug = $item->getCurrentSlug() ?? $item->payment_id;
                                    }

                                    $showSlug = \App\Services\ModuleSlugService::getSlug('Payment', 'show');
                                    $dynamicUrl = '/' . $showSlug . '/' . $slug;
                                @endphp
                                <a href="{{ $dynamicUrl }}" class="block">
                                    {{ $item->getTranslated('title', app()->getLocale()) }}
                                </a>
                            </h2>

                            @php
                                $excerpt =
                                    $item->getTranslated('metadesc') ??
                                    ($item->getRawOriginal('metadesc') ??
                                        ($item->metadesc ??
                                            Str::limit(
                                                strip_tags($item->getTranslated('body', app()->getLocale())),
                                                150,
                                            )));
                            @endphp

                            @if ($excerpt)
                                <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                                    {{ $excerpt }}
                                </p>
                            @endif

                            <a href="{{ $dynamicUrl }}"
                                class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                {{ __('payment::front.general.read_more') }}
                                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($items->hasPages())
                    <div class="mt-16 flex justify-center">
                        <div class="flex items-center space-x-2">
                            @if ($items->onFirstPage())
                                <span class="px-4 py-2 text-sm text-gray-400 dark:text-gray-600">
                                    {{ __('payment::front.general.previous') }}
                                </span>
                            @else
                                <a href="{{ $items->previousPageUrl() }}"
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                    {{ __('payment::front.general.previous') }}
                                </a>
                            @endif

                            <span class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ $items->currentPage() }} / {{ $items->lastPage() }}
                            </span>

                            @if ($items->hasMorePages())
                                <a href="{{ $items->nextPageUrl() }}"
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                    {{ __('payment::front.general.next') }}
                                </a>
                            @else
                                <span class="px-4 py-2 text-sm text-gray-400 dark:text-gray-600">
                                    {{ __('payment::front.general.next') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <div class="mb-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none"
                                stroke="currentColor" viewBox="0 0 48 48">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m6 0h6m-6 6v6m6-6v6M9 24h6m6 0h6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('payment::front.general.no_payments_found') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('payment::front.general.no_payments_description') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function paymentsIndex() {
            return {
                loaded: false,

                init() {
                    // Instant load
                    this.loaded = true;
                }
            }
        }
    </script>
@endsection
