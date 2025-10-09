<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $group->name ?? 'Form Builder' }} - Form Düzenleyici</title>
    
    
    <link rel="stylesheet" href="{{ asset('admin-assets/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/tabler-vendors.min.css') }}">
    

    <link rel="stylesheet" href="/admin-assets/libs/fontawesome-pro@7.1.0/css/all.css">
    
    
    <link rel="stylesheet" href="{{ asset('admin-assets/libs/form-builder/settingmanagement/css/form-builder.css') }}">
    
    
    @stack('styles')
    
    @livewireStyles
</head>
<body>
    {{ $slot }}
    
    
    <script src="{{ asset('admin-assets/js/tabler.min.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
    
    
    <script src="{{ asset('admin-assets/libs/form-builder/settingmanagement/js/form-builder-templates.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/form-builder/settingmanagement/js/form-builder-ui.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/form-builder/settingmanagement/js/form-builder-drag-drop.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/form-builder/settingmanagement/js/form-builder-operations.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/form-builder/settingmanagement/js/form-builder-elements.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/form-builder/settingmanagement/js/form-builder-core.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/form-builder/settingmanagement/js/form-builder.js') }}"></script>
    
    
    @stack('scripts')
    
    @livewireScripts
</body>
</html>
