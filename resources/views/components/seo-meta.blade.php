{{-- 
===========================================================
🎯 GLOBAL SEO META TAGS COMPONENT - FULL ORGANIZATION
===========================================================
Bu component frontend'de şu şekilde çıkıyor:
1. TITLE TAG 
2. BASIC META TAGS (description, keywords, robots)
3. OPEN GRAPH TAGS (Facebook/LinkedIn) 
4. TWITTER CARD TAGS (Twitter/X)
5. HREFLANG TAGS (Çoklu dil)
6. SCHEMA.ORG STRUCTURED DATA (JSON-LD)
===========================================================
--}}
<title>{{ $metaTags['title'] }}</title>

{{-- BASIC META TAGS --}}
@if($metaTags['description'])<meta name="description" content="{{ $metaTags['description'] }}">
@endif
{{-- Keywords, publisher, copyright meta tags removed - 2025 standards --}}

{{-- 2025 CORE WEB VITALS & MODERN META TAGS --}}
<meta name="theme-color" content="{{ setting('site_theme_color', '#000000') }}" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="{{ setting('site_theme_color_light', '#ffffff') }}" media="(prefers-color-scheme: light)">
<meta name="color-scheme" content="light dark">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta http-equiv="X-Content-Type-Options" content="nosniff">

{{-- ROBOTS & SEARCH ENGINE DIRECTIVES (2025 Standards) --}}
@if($metaTags['robots'])<meta name="robots" content="{{ $metaTags['robots'] }}, max-snippet:320, max-image-preview:standard, max-video-preview:-1">
@else
<meta name="robots" content="index, follow, max-snippet:320, max-image-preview:standard, max-video-preview:-1">
@endif
{{-- Bot-specific directives removed - robots meta sufficient for all crawlers --}}
@if($metaTags['canonical_url'])<link rel="canonical" href="{{ $metaTags['canonical_url'] }}">
@endif

{{-- OPEN GRAPH TAGS --}}
@if($metaTags['og_titles'])<meta property="og:title" content="{{ $metaTags['og_titles'] }}">
@endif
@if($metaTags['og_descriptions'])<meta property="og:description" content="{{ $metaTags['og_descriptions'] }}">
@endif
@if($metaTags['og_image'])<meta property="og:image" content="{{ $metaTags['og_image'] }}">
<meta property="og:image:secure_url" content="{{ str_replace('http:', 'https:', $metaTags['og_image']) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/{{ pathinfo($metaTags['og_image'], PATHINFO_EXTENSION) }}">
@endif
@if($metaTags['og_type'])<meta property="og:type" content="{{ $metaTags['og_type'] }}">
@endif
@if($metaTags['og_locale'])<meta property="og:locale" content="{{ $metaTags['og_locale'] }}">
@endif
@if($metaTags['og_site_name'])<meta property="og:site_name" content="{{ $metaTags['og_site_name'] }}">
@else
<meta property="og:site_name" content="{{ setting('site_title') }}">
@endif
<meta property="og:url" content="{{ url()->current() }}">

{{-- TWITTER CARD TAGS --}}
@if($metaTags['twitter_card'])<meta name="twitter:card" content="{{ $metaTags['twitter_card'] }}">
@endif
@if($metaTags['twitter_title'])<meta name="twitter:title" content="{{ $metaTags['twitter_title'] }}">
@endif
@if($metaTags['twitter_description'])<meta name="twitter:description" content="{{ $metaTags['twitter_description'] }}">
@endif
@if($metaTags['twitter_image'])<meta name="twitter:image" content="{{ $metaTags['twitter_image'] }}">
<meta name="twitter:image:alt" content="{{ $metaTags['twitter_title'] ?? $metaTags['title'] }}">
@endif
@if(isset($metaTags['twitter_site']) && !empty($metaTags['twitter_site']))<meta name="twitter:site" content="{{ $metaTags['twitter_site'] }}">
@endif
@if(isset($metaTags['twitter_creator']) && !empty($metaTags['twitter_creator']))<meta name="twitter:creator" content="{{ $metaTags['twitter_creator'] }}">
@endif

{{-- HREFLANG TAGS --}}
@if(isset($metaTags['hreflang']) && is_array($metaTags['hreflang']))
@foreach($metaTags['hreflang'] as $locale => $link)<link rel="alternate" hreflang="{{ $link['hreflang'] }}" href="{{ $link['url'] }}">
@endforeach
@php
    $defaultLocale = get_tenant_default_locale();
@endphp
@if(isset($metaTags['hreflang'][$defaultLocale]))<link rel="alternate" hreflang="x-default" href="{{ $metaTags['hreflang'][$defaultLocale]['url'] }}">
@endif
@endif

{{-- SCHEMA.ORG STRUCTURED DATA --}}
@if($metaTags['schema'] && is_array($metaTags['schema']) && count($metaTags['schema']) > 0)<script type="application/ld+json">
{!! json_encode($metaTags['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif