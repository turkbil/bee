{{-- SPA Loading Skeleton --}}
<div class="px-6 py-8 space-y-8">
    {{-- Header Skeleton --}}
    <div class="space-y-3 animate-pulse">
        <div class="h-10 bg-white/10 rounded-lg w-64"></div>
        <div class="h-4 bg-white/5 rounded w-96"></div>
    </div>

    {{-- Grid Skeleton --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        @for($i = 0; $i < 12; $i++)
        <div class="space-y-3 animate-pulse" style="animation-delay: {{ $i * 50 }}ms">
            <div class="w-full aspect-square bg-white/10 rounded-lg"></div>
            <div class="h-4 bg-white/5 rounded w-3/4"></div>
            <div class="h-3 bg-white/5 rounded w-1/2"></div>
        </div>
        @endfor
    </div>
</div>
