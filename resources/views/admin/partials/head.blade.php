{{-- resources/views/admin/partials/head.blade.php --}}
<meta charset="utf-8" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<title>Admin Paneli</title>
<link rel="stylesheet" href="{{ asset('admin/css/tabler.min.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/css/tabler-vendors.min.css') }}" />
@if (Str::contains(Request::url(), ['create', 'edit', 'manage', 'form']))
@else
<!-- <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css"> -->
@endif
<link rel="stylesheet" href="{{ asset('admin/css/plugins.css') }}?v={{ filemtime(public_path('admin/css/plugins.css')) }}" />
<link rel="stylesheet" href="{{ asset('admin/libs/fontawesome-pro@6.7.1/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/css/main.css') }}?v={{ filemtime(public_path('admin/css/main.css')) }}" />
@livewireStyles
@stack('css')
