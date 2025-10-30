@include('themes.ixtif.layouts.header')

{{-- Universal Notification System --}}
@include('themes.ixtif.layouts.notification')

{{-- Cookie Consent - GDPR Uyumlu --}}
@include('components.cookie-consent')

<main class="flex-1 min-h-[60vh] relative" style="z-index: 10;" @mouseenter="$dispatch('close-megamenu')">
    {{ $slot ?? '' }}
    @yield('content')
    @yield('module_content')
</main>

@include('themes.ixtif.layouts.footer')