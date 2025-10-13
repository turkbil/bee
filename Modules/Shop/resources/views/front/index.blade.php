@extends('themes.blank.layouts.app')

@section('module_content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
        <header class="text-center">
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('shop::front.general.products_title') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ __('shop::front.general.products_subtitle') }}
            </p>
        </header>

        @if ($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($products as $product)
                    <article
                        class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            <a href="{{ Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product) }}"
                                class="hover:text-blue-500">
                                {{ $product->getTranslated('title', app()->getLocale()) }}
                            </a>
                        </h2>

                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ \Illuminate\Support\Str::limit($product->getTranslated('short_description', app()->getLocale()) ?? strip_tags($product->getTranslated('body', app()->getLocale())), 150) }}
                        </p>

                        <dl class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <div class="flex justify-between">
                                <dt>{{ __('shop::front.general.category') }}</dt>
                                <dd>{{ optional($product->category)->getTranslated('title', app()->getLocale()) ?? '—' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>{{ __('shop::front.general.brand') }}</dt>
                                <dd>{{ optional($product->brand)->getTranslated('title', app()->getLocale()) ?? '—' }}</dd>
                            </div>
                        </dl>

                        <a href="{{ Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product) }}"
                            class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline">
                            {{ __('shop::front.general.read_more') }}
                            <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M12.293 4.293a1 1 0 011.414 0L18 8.586a1 1 0 010 1.414l-4.293 4.293a1 1 0 01-1.414-1.414L14.586 10H4a1 1 0 110-2h10.586l-2.293-2.293a1 1 0 010-1.414z" />
                            </svg>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="pt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-gray-500 dark:text-gray-400">
                    {{ __('shop::front.general.no_products') }}
                </p>
            </div>
        @endif
    </div>
@endsection
