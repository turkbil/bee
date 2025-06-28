@include('themes.blank.layouts.header', ['title' => $title ?? ($item->title ?? __('page::general.page'))])

<main class="container mx-auto px-4 py-8">
    @yield('content')
</main>

@include('themes.blank.layouts.footer')