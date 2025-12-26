@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-6 py-8">
    {{-- Header - Alternatif 2: Icon + Text --}}
    <div class="mb-8 flex items-center gap-5">
        <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-broadcast-tower text-3xl text-white"></i>
        </div>
        <div>
            <h1 class="text-5xl font-extrabold text-white mb-1">Canlı Radyolar</h1>
            <p class="text-gray-400 text-lg">En popüler radyo istasyonlarını canlı dinle</p>
        </div>
    </div>

    {{-- Radios Grid --}}
    @include('themes.muzibu.partials.radios-grid', ['radios' => $radios])
</div>
@endsection
