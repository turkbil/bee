@include('themes.ixtif.layouts.header')

{{-- Universal Notification System --}}
@include('themes.ixtif.layouts.notification')

<main class="flex-1 min-h-[60vh] relative pb-32 md:pb-0" style="z-index: 10;">
    @php
    ob_start();
    @endphp

    @yield('content')
    @yield('module_content')

    @php
    $content = ob_get_clean();
    echo app('widget.resolver')->resolveWidgetContent($content);
    @endphp
</main>

@include('themes.ixtif.layouts.footer')