@extends('themes.blank.layouts.app')

@section('module_content')
        <div class="min-h-screen">
            @php
                $currentLocale = app()->getLocale();
                $title = $item->getTranslated('title', $currentLocale);
                $body = $item->getTranslated('body', $currentLocale);
            @endphp

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div
                    class="prose prose-xl max-w-none dark:prose-invert
                  prose-headings:text-gray-900 dark:prose-headings:text-white
                  prose-p:text-gray-700 dark:prose-p:text-gray-300
                  prose-a:text-blue-600 dark:prose-a:text-blue-400 hover:prose-a:text-blue-700 dark:hover:prose-a:text-blue-300
                  prose-strong:text-gray-900 dark:prose-strong:text-white
                  prose-blockquote:border-l-blue-500 prose-blockquote:bg-blue-50/50 dark:prose-blockquote:bg-blue-900/10
                  prose-code:text-blue-600 dark:prose-code:text-blue-400 prose-code:bg-blue-50 dark:prose-code:bg-blue-900/20
                  prose-pre:bg-gray-900 dark:prose-pre:bg-gray-800
                  prose-img:rounded-lg">
                    @parsewidgets($body ?? '')
                </div>
            </div>

            @if (isset($item->js))
                <script>
                    {!! $item->js !!}
                </script>
            @endif

            @if (isset($item->css))
                <style>
                    {!! $item->css !!}
                </style>
            @endif
        </div>
    @else
        <div class="min-h-screen">
            @php
                $currentLocale = app()->getLocale();
                $title = $item->getTranslated('title', $currentLocale);
                $body = $item->getTranslated('body', $currentLocale);

                $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                $indexSlug = $moduleSlugService->getMultiLangSlug('Announcement', 'index', $currentLocale);
                $defaultLocale = get_tenant_default_locale();
                $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
                $pageIndexUrl = $localePrefix . '/' . $indexSlug;
            @endphp

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <article
                    class="prose prose-xl max-w-none dark:prose-invert
                      prose-headings:text-gray-900 dark:prose-headings:text-white
                      prose-p:text-gray-700 dark:prose-p:text-gray-300
                      prose-a:text-blue-600 dark:prose-a:text-blue-400 hover:prose-a:text-blue-700 dark:hover:prose-a:text-blue-300
                      prose-strong:text-gray-900 dark:prose-strong:text-white
                      prose-blockquote:border-l-blue-500 prose-blockquote:bg-blue-50/50 dark:prose-blockquote:bg-blue-900/10
                      prose-code:text-blue-600 dark:prose-code:text-blue-400 prose-code:bg-blue-50 dark:prose-code:bg-blue-900/20
                      prose-pre:bg-gray-900 dark:prose-pre:bg-gray-800
                      prose-img:rounded-lg">
                    @parsewidgets($body ?? '')
                </article>

                @if (isset($item->js))
                    <script>
                        {!! $item->js !!}
                    </script>
                @endif

                @if (isset($item->css))
                    <style>
                        {!! $item->css !!}
                    </style>
                @endif

                <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ $pageIndexUrl }}"
                        class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('announcement::front.general.all_pages') }}
                    </a>
                </footer>
            </div>
        </div>
    @endif
@endsection
