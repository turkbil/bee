<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- JavaScript için CSRF token'ı ayarla -->
        <script>
            window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
        </script>
    </head>
    <body style="margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; -webkit-font-smoothing: antialiased;">
        <div style="min-height: 100vh; background-color: #1a1a1a;">
            @include('layouts.navigation')

            @isset($header)
                <header style="background-color: #2d2d2d; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <div style="max-width: 1280px; margin: 0 auto; padding: 24px 16px;">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main style="padding: 16px;">
                {{ $slot }}
            </main>
        </div>
        
        <!-- CSRF Token yenileme scripti -->
        <script>
            // AJAX istekleri için CSRF token'ı ayarlama
            document.addEventListener('DOMContentLoaded', function() {
                // AJAX istekleri için CSRF token'ını ayarla (jQuery yoksa fetch ile)
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Token yenileme fonksiyonu
                function refreshToken() {
                    fetch('{{ route("csrf.refresh") }}')
                        .then(response => response.text())
                        .then(token => {
                            document.querySelector('meta[name="csrf-token"]').setAttribute('content', token);
                            console.log('CSRF token yenilendi');
                        })
                        .catch(error => console.error('CSRF token yenileme hatası:', error));
                }
                
                // Her 30 dakikada bir token yenileme
                setInterval(refreshToken, 30 * 60 * 1000);
            });
        </script>
    </body>
</html>