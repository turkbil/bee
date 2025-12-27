@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-4 py-6 sm:px-6 sm:py-8">
    {{-- Header - Alternatif 2: Icon + Text (FA Beat-Fade Animation) --}}
    <div class="mb-4 sm:mb-6 flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-white/10 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-radio text-xl sm:text-2xl text-white fa-beat-fade" style="--fa-animation-duration: 2s; --fa-beat-fade-opacity: 0.4; --fa-beat-fade-scale: 1.1;"></i>
        </div>
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-0.5">Canlı Radyolar</h1>
            <p class="text-gray-400 text-sm sm:text-base">En popüler radyo istasyonlarını canlı dinle</p>
        </div>
    </div>

    {{-- Radios Grid --}}
    @include('themes.muzibu.partials.radios-grid', ['radios' => $radios])
</div>
@endsection
