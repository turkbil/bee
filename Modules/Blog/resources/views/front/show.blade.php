@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@push('head')
@php
    $metaLocale = app()->getLocale();
    $metaTitle = $item->getTranslated('title', $metaLocale);
    $metaBody = $item->getTranslated('body', $metaLocale) ?? '';
    $metaExcerpt = $item->getTranslated('excerpt', $metaLocale) ?: \Illuminate\Support\Str::limit(strip_tags($metaBody), 160);
    $featuredImageUrl = $item->getFirstMediaUrl('featured_image');
    $wordCount = str_word_count(strip_tags($metaBody));
    $tagList = $item->tag_list ?? [];
@endphp

{!! \App\Services\SEOService::getAllSchemas($item) !!}

{{-- Open Graph Meta Tags --}}
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaExcerpt }}">
<meta property="og:url" content="{{ $item->getUrl($metaLocale) }}">
@if($featuredImageUrl)
<meta property="og:image" content="{{ $featuredImageUrl }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaExcerpt }}">
@if($featuredImageUrl)
<meta name="twitter:image" content="{{ $featuredImageUrl }}">
@endif
@endpush

@section('module_content')
    @php
        // Theme fallback: try active theme, then simple
        $partialView = 'blog::themes.' . $themeName . '.partials.show-content';
        if (!view()->exists($partialView)) {
            $partialView = 'blog::themes.simple.partials.show-content';
        }
    @endphp
    @include($partialView, ['item' => $item])
@endsection
