{{-- Global SEO Meta Tags Component --}}
{{-- Title --}}
<title>{{ $metaTags['title'] }}</title>
{{-- Basic Meta Tags --}}
@if($metaTags['description'])
<meta name="description" content="{{ $metaTags['description'] }}">
@endif
@if($metaTags['keywords'])
<meta name="keywords" content="{{ $metaTags['keywords'] }}">
@endif
@if($metaTags['robots'])
<meta name="robots" content="{{ $metaTags['robots'] }}">
@endif
{{-- Open Graph Tags --}}
@if($metaTags['og_title'])
<meta property="og:title" content="{{ $metaTags['og_title'] }}">
@endif
@if($metaTags['og_description'])
<meta property="og:description" content="{{ $metaTags['og_description'] }}">
@endif
@if($metaTags['og_image'])
<meta property="og:image" content="{{ $metaTags['og_image'] }}">
@endif
@if($metaTags['og_type'])
<meta property="og:type" content="{{ $metaTags['og_type'] }}">
@endif
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ setting('site_title') }}">
{{-- Twitter Card Tags --}}
@if($metaTags['twitter_card'])
<meta name="twitter:card" content="{{ $metaTags['twitter_card'] }}">
@endif
@if($metaTags['twitter_title'])
<meta name="twitter:title" content="{{ $metaTags['twitter_title'] }}">
@endif
@if($metaTags['twitter_description'])
<meta name="twitter:description" content="{{ $metaTags['twitter_description'] }}">
@endif
@if($metaTags['twitter_image'])
<meta name="twitter:image" content="{{ $metaTags['twitter_image'] }}">
@endif
{{-- Hreflang Tags --}}
@if(isset($metaTags['hreflang']) && is_array($metaTags['hreflang']))
@foreach($metaTags['hreflang'] as $locale => $link)
<link rel="alternate" hreflang="{{ $link['hreflang'] }}" href="{{ $link['url'] }}">
@endforeach
@php
    $defaultLocale = get_tenant_default_locale();
@endphp
@if(isset($metaTags['hreflang'][$defaultLocale]))
<link rel="alternate" hreflang="x-default" href="{{ $metaTags['hreflang'][$defaultLocale]['url'] }}">
@endif
@endif
{{-- Schema.org Structured Data --}}
@if($metaTags['schema'] && is_array($metaTags['schema']) && count($metaTags['schema']) > 0)
<script type="application/ld+json">
{!! json_encode($metaTags['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif