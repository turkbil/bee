@include('themes.blank.layouts.header')

<main>
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

@include('themes.blank.layouts.footer')