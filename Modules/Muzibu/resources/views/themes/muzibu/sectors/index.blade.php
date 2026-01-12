@extends('themes.muzibu.layouts.app')

@section('content')
<section class="relative h-64 mb-8 bg-gradient-to-b from-green-900 via-green-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex flex-col justify-end pb-12">
        <h1 class="text-5xl font-black mb-2 text-white drop-shadow-2xl">Sektörler</h1>
        <p class="text-lg text-white/90">İşletme türüne özel müzikler</p>
    </div>
</section>

<section class="px-8 pb-12">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($sectors as $sector)
            <x-muzibu.sector-card :sector="$sector" :preview="false" />
        @endforeach
    </div>
</section>
@endsection
