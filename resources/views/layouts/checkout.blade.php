<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Checkout - Muzibu' }}</title>

    {{-- Muzibu Tailwind Config --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        muzibu: {
                            coral: '#ff7f50',
                            'coral-light': '#ff9770',
                            'coral-dark': '#ff6a3d',
                            black: '#000000',
                            dark: '#121212',
                            gray: '#181818',
                            'gray-light': '#282828',
                            'text-gray': '#b3b3b3',
                        }
                    }
                }
            }
        };
    </script>

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Muzibu Layout CSS --}}
    <link rel="stylesheet" href="{{ versioned_asset('themes/muzibu/css/muzibu-layout.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('themes/muzibu/css/muzibu-custom.css') }}">

    <style>
        [x-cloak] { display: none !important; }
        body { background-color: #000000; color: #ffffff; }
    </style>
</head>
<body class="bg-black text-white">
    {{-- Muzibu Header --}}
    @include('themes.muzibu.components.header')

    {{-- Checkout Content --}}
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    {{-- Livewire Scripts (Alpine.js included) - NO muzibuApp()! --}}
    @livewireScripts

    <script>
        console.log('✅ Checkout Layout: Minimal Alpine.js (no player)');
    </script>
</body>
</html>
