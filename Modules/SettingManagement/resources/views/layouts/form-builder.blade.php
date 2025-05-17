<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $group->name ?? 'Form Builder' }} - Form Düzenleyici</title>
    
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/tabler-vendors.min.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Form Builder CSS -->
    <link rel="stylesheet" href="{{ asset('admin/libs/form-builder/css/form-builder.css') }}">
    
    <!-- Özel Stiller -->
    @stack('styles')
    
    @livewireStyles
</head>
<body>
    {{ $slot }}
    
    <!-- JavaScript -->
    <script src="{{ asset('admin/js/tabler.min.js') }}"></script>
    <script src="{{ asset('admin/libs/form-builder/js/form-builder.js') }}"></script>
    <script src="{{ asset('admin/libs/sortable/sortable.min.js') }}"></script>
    
    @stack('scripts')
    @livewireScripts
</body>
</html>