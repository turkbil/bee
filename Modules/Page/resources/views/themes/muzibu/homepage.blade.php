@extends('themes.muzibu.layouts.app')

@section('module_content')
<div class="min-h-screen bg-gray-900 text-white p-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold mb-8">Muzibu - Telifsiz Müzik Platformu</h1>

        <p class="text-xl text-gray-300 mb-12">
            İşletmeniz için yasal ve telifsiz müzik çözümü
        </p>

        {{-- Placeholder Content --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-800 p-6 rounded-lg">
                <i class="fas fa-music text-green-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">25.000+ Şarkı</h3>
                <p class="text-gray-400">Geniş müzik kütüphanesi</p>
            </div>

            <div class="bg-gray-800 p-6 rounded-lg">
                <i class="fas fa-shield-check text-green-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">100% Yasal</h3>
                <p class="text-gray-400">Telif cezası riski yok</p>
            </div>

            <div class="bg-gray-800 p-6 rounded-lg">
                <i class="fas fa-store text-green-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">İşletmeler İçin</h3>
                <p class="text-gray-400">Cafe, restoran, mağaza</p>
            </div>
        </div>
    </div>
</div>
@endsection
