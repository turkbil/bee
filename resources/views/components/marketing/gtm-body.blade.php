{{--
    Google Tag Manager Body Snippet (No-Script Fallback)

    Kullanım (body açılış tag'inden hemen sonra):
    <x-marketing.gtm-body />
--}}

@php
    $gtmId = setting('marketing_gtm_id') ?: setting('seo_google_tag_manager_id');
@endphp

@if($gtmId)
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif
