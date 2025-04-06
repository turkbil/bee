<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sayfa Başlığı' }} - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('themes/blank/assets/css/style.css') }}">
</head>
<body>
    <header>
        <div class="container">
            <h1>{{ config('app.name') }}</h1>
        </div>
    </header>

    <main class="container">
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </footer>

    <script src="{{ asset('themes/blank/assets/js/main.js') }}"></script>
</body>
</html>