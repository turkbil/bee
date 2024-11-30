{{--  resources\views\admin\partials\form-js-css.php --}}


@push('css')
<link rel="stylesheet" href="{{ asset('tema-admin-2024/plugins/select2-4.1.0/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('tema-admin-2024/plugins/select2-4.1.0/css/select2-bootstrap-5-theme.min.css') }}">
@endpush

@push('js')
<script src="{{ asset('tema-admin-2024/plugins/select2-4.1.0/js/select2.min.js') }}"></script>
<script src="{{ asset('tema-admin-2024/js/form.js') }}?v={{ filemtime(public_path('tema-admin-2024/js/form.js')) }}"></script>
@endpush
