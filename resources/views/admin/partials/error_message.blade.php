{{-- resources/views/admin/partials/error_message.blade.php  --}}
{{-- BU ALAN FORMLARDA HATA VARSA GÖSTERİLECEK  --}}
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif