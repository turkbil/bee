@include('themes.blank.layouts.header')


<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 transition-colors duration-300">
        @php
        ob_start();
        @endphp

        @yield('content')

        @php
        $content = ob_get_clean();
        echo app('widget.resolver')->resolveWidgetContent($content);
        @endphp
    </div>
</main>

@include('themes.blank.layouts.footer')