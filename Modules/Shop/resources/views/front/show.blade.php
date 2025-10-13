@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <article class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="mb-8">
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                {{ $item->getTranslated('title', app()->getLocale()) }}
            </h1>
            <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                <span>{{ __('shop::front.general.sku') }}: {{ $item->sku }}</span>
                <span>{{ __('shop::front.general.category') }}:
                    {{ optional($item->category)->getTranslated('title', app()->getLocale()) ?? '—' }}</span>
                <span>{{ __('shop::front.general.brand') }}:
                    {{ optional($item->brand)->getTranslated('title', app()->getLocale()) ?? '—' }}</span>
            </div>
        </header>

        <section class="prose prose-lg dark:prose-invert max-w-none mb-12">
            {!! $item->getTranslated('body', app()->getLocale()) !!}
        </section>

        {{-- Features --}}
        @if ($item->features && isset($item->features[app()->getLocale()]))
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('shop::front.general.features') }}
                </h2>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($item->features[app()->getLocale()] as $feature)
                        <li class="flex items-start text-gray-700 dark:text-gray-300">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Use Cases --}}
        @if ($item->use_cases && isset($item->use_cases[app()->getLocale()]))
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Kullanım Alanları</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($item->use_cases[app()->getLocale()] as $useCase)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <p class="text-gray-700 dark:text-gray-300">{{ $useCase }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Competitive Advantages --}}
        @if ($item->competitive_advantages && isset($item->competitive_advantages[app()->getLocale()]))
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Rekabet Avantajları</h2>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border-l-4 border-blue-500">
                    <ul class="space-y-2">
                        @foreach ($item->competitive_advantages[app()->getLocale()] as $advantage)
                            <li class="flex items-start text-gray-700 dark:text-gray-300">
                                <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                <span>{{ $advantage }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        {{-- FAQ --}}
        @if ($item->faq_data && count($item->faq_data) > 0)
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Sık Sorulan Sorular</h2>
                <div class="space-y-4">
                    @foreach ($item->faq_data as $faq)
                        <details
                            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
                            <summary class="font-semibold text-gray-900 dark:text-white cursor-pointer">
                                {{ $faq['question'][app()->getLocale()] ?? $faq['question']['tr'] }}
                            </summary>
                            <p class="mt-3 text-gray-600 dark:text-gray-400">
                                {{ $faq['answer'][app()->getLocale()] ?? $faq['answer']['tr'] }}
                            </p>
                        </details>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Target Industries --}}
        @if ($item->target_industries && isset($item->target_industries[app()->getLocale()]))
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Hedef Sektörler</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($item->target_industries[app()->getLocale()] as $industry)
                        <span
                            class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full text-sm font-medium border border-gray-200 dark:border-gray-700">
                            {{ $industry }}
                        </span>
                    @endforeach
                </div>
            </section>
        @endif

        <footer class="mt-10 border-t border-gray-200 dark:border-gray-700 pt-6 text-sm text-gray-500 dark:text-gray-400">
            <div class="flex flex-wrap gap-4">
                <span>{{ __('shop::front.general.updated_at') }}:
                    {{ $item->updated_at?->translatedFormat('d F Y') }}</span>
                <span>{{ __('shop::front.general.published_at') }}:
                    {{ $item->published_at?->translatedFormat('d F Y') }}</span>
            </div>
        </footer>
    </article>
@endsection
