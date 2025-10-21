{{--
    Tailwind Screen Size Indicator

    Development tool - Sadece Root kullanıcılar görebilir
    Sol alt köşede ekran boyutunu gösterir (XS, SM, MD, LG, XL, 2XL)

    Changelog v2.0:
    - ✅ Inline JS kaldırıldı (65+ satır temizlendi)
    - ✅ External file kullanılıyor: dev-tools.js
    - ✅ Sadece root kullanıcılar görebilir
--}}
@auth
    @if(auth()->user()->hasRole('root'))
        <div id="tailwind-screen-indicator"></div>
        <script src="{{ asset('assets/js/dev-tools.js') }}?v={{ now()->timestamp }}" defer></script>
    @endif
@endauth
