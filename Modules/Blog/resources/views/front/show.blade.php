@extends('themes.blank.layouts.app')

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

{!! \App\Services\SEOService::getPageSchema($item) !!}

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

{{-- JSON-LD Structured Data --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "{{ $metaTitle }}",
  "description": "{{ $metaExcerpt }}",
  "url": "{{ $item->getUrl($metaLocale) }}",
  "datePublished": "{{ $item->published_at ? $item->published_at->toISOString() : $item->created_at->toISOString() }}",
  "dateModified": "{{ $item->updated_at->toISOString() }}",
  @if($featuredImageUrl)
  "image": {
    "@type": "ImageObject",
    "url": "{{ $featuredImageUrl }}",
    "width": 1200,
    "height": 630
  },
  @endif
  "author": {
    "@type": "Person",
    "name": "{{ config('app.name') }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "{{ config('app.name') }}",
    "url": "{{ url('/') }}"
  },
  @if($item->category)
  "articleSection": "{{ $item->category->getTranslated('name', $metaLocale) }}",
  @endif
  @if(!empty($tagList))
  "keywords": "{{ implode(', ', $tagList) }}",
  @endif
  "wordCount": {{ $wordCount }}
}
</script>
@endpush

@section('module_content')
    @include('blog::themes.blank.partials.show-content', ['item' => $item])
@endsection
