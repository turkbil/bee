<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    </body>
</html>