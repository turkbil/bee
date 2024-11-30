{{--  resources\views\admin\partials\js\form.blade.php --}}

@push('js')
<script src="{{ asset('tabler/js/form.js') }}?v={{ filemtime(public_path('tabler/js/form.js')) }}" defer></script>am
@endpush
