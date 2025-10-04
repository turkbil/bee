<title>{{ $metaTags['title'] }}</title>
@if($metaTags['description'])
<meta name="description" content="{{ $metaTags['description'] }}">
@endif
{{-- Author Meta Tag (E-E-A-T için kritik - 2025 SEO) --}}
@if(isset($metaTags['author']) && $metaTags['author'])
<meta name="author" content="{{ $metaTags['author'] }}">
@endif
<meta name="theme-color" content="{{ setting('site_theme_color', '#000000') }}" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="{{ setting('site_theme_color_light', '#ffffff') }}" media="(prefers-color-scheme: light)">
<meta name="color-scheme" content="light dark">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta http-equiv="X-Content-Type-Options" content="nosniff">
@if($metaTags['robots'])
<meta name="robots" content="{{ $metaTags['robots'] }}">
@else
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
@endif
@if($metaTags['canonical_url'])
<link rel="canonical" href="{{ $metaTags['canonical_url'] }}">
@endif
{{-- Sitemap Link (2025 SEO Best Practice) --}}
<link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap') }}">
{{-- Copyright Meta Tag (2025 SEO) --}}
<meta name="copyright" content="{{ setting('site_copyright') ?: '© ' . date('Y') . ' ' . setting('site_title') }}">
{{-- PWA Meta Tags (Mobile-First Indexing - 2025 SEO) --}}
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="{{ setting('site_name') ?: setting('site_title') }}">
@if($metaTags['og_titles'])
<meta property="og:title" content="{{ $metaTags['og_titles'] }}">
@endif
@if($metaTags['og_descriptions'])
<meta property="og:description" content="{{ $metaTags['og_descriptions'] }}">
@endif
@if($metaTags['og_image'])
<meta property="og:image" content="{{ $metaTags['og_image'] }}">
<meta property="og:image:secure_url" content="{{ str_replace('http:', 'https:', $metaTags['og_image']) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/{{ pathinfo($metaTags['og_image'], PATHINFO_EXTENSION) }}">
@endif
@if($metaTags['og_type'])
<meta property="og:type" content="{{ $metaTags['og_type'] }}">
@endif
@if(isset($metaTags['article_published_time']) && $metaTags['article_published_time'])
<meta property="article:published_time" content="{{ $metaTags['article_published_time'] }}">
@endif
@if(isset($metaTags['article_modified_time']) && $metaTags['article_modified_time'])
<meta property="article:modified_time" content="{{ $metaTags['article_modified_time'] }}">
@endif
@if(isset($metaTags['article_author']) && $metaTags['article_author'])
<meta property="article:author" content="{{ $metaTags['article_author'] }}">
@endif
@if($metaTags['og_locale'])
<meta property="og:locale" content="{{ $metaTags['og_locale'] }}">
@endif
@if($metaTags['og_site_name'])
<meta property="og:site_name" content="{{ $metaTags['og_site_name'] }}">
@else
<meta property="og:site_name" content="{{ setting('site_title') }}">
@endif
<meta property="og:url" content="{{ url()->current() }}">
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
<meta name="twitter:image:alt" content="{{ $metaTags['twitter_title'] ?? $metaTags['title'] }}">
@endif
@if(isset($metaTags['twitter_site']) && !empty($metaTags['twitter_site']))
<meta name="twitter:site" content="{{ $metaTags['twitter_site'] }}">
@endif
@if(isset($metaTags['twitter_creator']) && !empty($metaTags['twitter_creator']))
<meta name="twitter:creator" content="{{ $metaTags['twitter_creator'] }}">
@endif
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
{{-- Schema.org Structured Data (2025 Multi-Schema Approach) --}}
@if(isset($metaTags['schemas']) && is_array($metaTags['schemas']))
    @foreach($metaTags['schemas'] as $schemaKey => $schema)
        @if(is_array($schema) && count($schema) > 0)
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
        @endif
    @endforeach
@endif

{{-- Legacy Support: Eski schema yapısı için fallback --}}
@if(!isset($metaTags['schemas']) && isset($metaTags['schema']) && is_array($metaTags['schema']) && count($metaTags['schema']) > 0)
<script type="application/ld+json">
{!! json_encode($metaTags['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
@if(!isset($metaTags['schemas']) && isset($metaTags['breadcrumb_schema']) && is_array($metaTags['breadcrumb_schema']) && count($metaTags['breadcrumb_schema']) > 0)
<script type="application/ld+json">
{!! json_encode($metaTags['breadcrumb_schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif