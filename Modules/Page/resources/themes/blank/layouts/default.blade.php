<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sayfa Başlığı' }} - {{ config('app.name') }} (Modül Tema)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }
        header, footer { padding: 20px 0; background: #e9f5ff; margin-bottom: 20px; }
        .page-item { border-bottom: 1px solid #eee; padding: 15px 0; }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .module-badge { display: inline-block; background: #007bff; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>{{ config('app.name') }} <span class="module-badge">Modül Tema</span></h1>
            <nav>
                <a href="{{ route('pages.index') }}">Tüm Sayfalar</a>
            </nav>
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