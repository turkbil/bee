@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

    <div class="px-4 sm:px-6 py-6 sm:py-8">
        {{-- Header --}}
        <div class="mb-8 animate-slide-up">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-2">Canlı Radyolar</h1>
            <p class="text-sm sm:text-base text-gray-400">En popüler radyo istasyonlarını canlı dinle</p>
        </div>

        {{-- Radios Grid --}}
        @include('themes.muzibu.partials.radios-grid', ['radios' => $radios])
    </div>
@endsection
