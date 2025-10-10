@extends('themes.blank.layouts.app')

@section('module_content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
        <header>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                {{ $brand->getTranslated('title', app()->getLocale()) }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ $brand->getTranslated('description', app()->getLocale()) }}
            </p>
        </header>

        @include('shop::front.partials.product-grid', ['products' => $products])
    </div>
@endsection
