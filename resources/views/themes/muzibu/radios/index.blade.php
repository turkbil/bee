@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Canlı Radyolar</h1>
        <p class="text-gray-400">En popüler radyo istasyonlarını canlı dinle</p>
    </div>

    {{-- Radios Grid --}}
    @include('themes.muzibu.partials.radios-grid', ['radios' => $radios])
</div>
@endsection
