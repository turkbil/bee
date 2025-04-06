<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sayfa Başlığı' }} - {{ config('app.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        header, footer { padding: 20px 0; background: #f5f5f5; margin-bottom: 20px; }
        .page-item { border-bottom: 1px solid #eee; padding: 15px 0; }
    </style>
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
</body>
</html>