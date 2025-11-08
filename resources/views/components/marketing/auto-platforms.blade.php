{{--
    Marketing Platform Auto-Loader Component

    Tenant-based: Her tenant kendi marketing platform'larını ayarlarda tanımlar
    Sadece ID varsa yüklenir, yoksa skip edilir

    Kullanım: <x-marketing.auto-platforms />
--}}

@php
    // Tenant-based marketing platform IDs
    $gtmId = setting('marketing_gtm_id') ?: setting('seo_google_tag_manager_id');
    $ga4Id = setting('marketing_ga4_id') ?: setting('seo_site_google_analytics_code');
    $fbPixelId = setting('marketing_fb_pixel_id');
    $yandexId = setting('marketing_yandex_metrika_id');
    $linkedinId = setting('marketing_linkedin_partner_id');
    $tiktokId = setting('marketing_tiktok_pixel_id');
    $clarityId = setting('marketing_ms_clarity_id');
@endphp

{{-- Google Tag Manager (Priority #1 - Tüm diğer platformları manage edebilir) --}}
@if($gtmId)
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
<!-- End Google Tag Manager -->
@endif

{{-- Google Analytics 4 - Sadece GTM yoksa direkt yükle --}}
@if($ga4Id && !$gtmId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
<script>
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('js',new Date());gtag('config','{{ $ga4Id }}');
</script>
@endif

{{-- Facebook Pixel - Sadece ID varsa yükle --}}
@if($fbPixelId)
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}
(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init','{{ $fbPixelId }}');fbq('track','PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{ $fbPixelId }}&ev=PageView&noscript=1"/></noscript>
@endif

{{-- Yandex Metrika - Sadece ID varsa yükle --}}
@if($yandexId)
<script type="text/javascript">
(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
m[i].l=1*new Date();for(var j=0;j<document.scripts.length;j++){if(document.scripts[j].src===r){return;}}
k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
(window,document,"script","https://mc.yandex.ru/metrika/tag.js","ym");
ym({{ $yandexId }},"init",{clickmap:true,trackLinks:true,accurateTrackBounce:true,webvisor:true});
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/{{ $yandexId }}" style="position:absolute;left:-9999px;" alt=""/></div></noscript>
@endif

{{-- LinkedIn Insight Tag - Sadece ID varsa yükle --}}
@if($linkedinId)
<script type="text/javascript">
_linkedin_partner_id="{{ $linkedinId }}";
window._linkedin_data_partner_ids=window._linkedin_data_partner_ids||[];
window._linkedin_data_partner_ids.push(_linkedin_partner_id);
</script>
<script type="text/javascript">
(function(l){if(!l){window.lintrk=function(a,b){window.lintrk.q.push([a,b])};window.lintrk.q=[]}
var s=document.getElementsByTagName("script")[0];var b=document.createElement("script");
b.type="text/javascript";b.async=true;b.src="https://snap.licdn.com/li.lms-analytics/insight.min.js";
s.parentNode.insertBefore(b,s);})(window.lintrk);
</script>
<noscript><img height="1" width="1" style="display:none;" alt=""
src="https://px.ads.linkedin.com/collect/?pid={{ $linkedinId }}&fmt=gif"/></noscript>
@endif

{{-- TikTok Pixel - Sadece ID varsa yükle --}}
@if($tiktokId)
<script>
!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];
ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],
ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},
ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],
ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};
var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;
var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
ttq.load('{{ $tiktokId }}');ttq.page();
}(window,document,'ttq');
</script>
@endif

{{-- Microsoft Clarity - Sadece ID varsa yükle --}}
@if($clarityId)
<script type="text/javascript">
(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
})(window,document,"clarity","script","{{ $clarityId }}");
</script>
@endif
