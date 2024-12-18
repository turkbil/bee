{{-- resources/views/admin/partials/scripts.blade.php --}}
<script src="{{ asset('admin/js/plugins.js') }}?v={{ filemtime(public_path('admin/js/plugins.js')) }}"></script>
<script src="{{ asset('admin/js/tabler.min.js') }}" defer></script>
<script src="{{ asset('admin/js/main.js') }}?v={{ filemtime(public_path('admin/js/main.js')) }}"></script>
<!--
<script src="{{ asset('admin/libs/bootstrap-table@1.23.5/extensions/reorder-rows/bootstrap-table-reorder-rows.min.js') }}"></script>
<script src="{{ asset('admin/libs/bootstrap-table@1.23.5/extensions/mobile/bootstrap-table-mobile.min.js') }}"></script>
<script src="{{ asset('admin/libs/bootstrap-table@1.23.5/extensions/cookie/bootstrap-table-cookie.min.js') }}"></script>
<script src="{{ asset('admin/libs/bootstrap-table@1.23.5/locale/bootstrap-table-tr-TR.min.js') }}"></script>
-->
@livewireScripts
@stack('js')
