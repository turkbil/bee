@extends('themes.ixtif.layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 px-4 py-16">
    <div class="max-w-2xl w-full text-center">
        {{-- 404 Icon/Animation --}}
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full shadow-2xl animate-bounce">
                <span class="text-6xl text-white font-black">404</span>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
            {{ __('Sayfa Bulunamadı') }}
        </h1>

        {{-- Description --}}
        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-8">
            {{ __('Aradığınız sayfa kaldırılmış, adı değiştirilmiş veya geçici olarak kullanılamıyor olabilir.') }}
        </p>

        {{-- Search Box --}}
        <div class="max-w-lg mx-auto mb-8">
            @if(Module::isEnabled('Search'))
                <form action="{{ route('search.query') }}" method="GET" class="relative">
                    <input
                        type="text"
                        name="q"
                        placeholder="{{ __('Ne aramıştınız? Buradan arayın...') }}"
                        class="w-full px-6 py-4 pr-12 text-lg bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all"
                        autofocus
                    >
                    <button
                        type="submit"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                    >
                        <i class="fas fa-search text-xl"></i>
                    </button>
                </form>
            @endif
        </div>

        {{-- Quick Links --}}
        @php
            // Tenant-aware & Module-aware quick links - TAMAMEN DİNAMİK!
            $quickLinks = [];

            // Ana Sayfa (her zaman var)
            $quickLinks[] = [
                'url' => url('/'),
                'icon' => 'fas fa-home',
                'color' => 'text-blue-500',
                'label' => __('Ana Sayfa')
            ];

            // Modül icon/renk/label mapping (yeni modül eklenince buraya ekle)
            $moduleMap = [
                'Shop' => [
                    'icon' => 'fas fa-shopping-cart',
                    'color' => 'text-green-500',
                    'label' => __('Ürünler')
                ],
                'Blog' => [
                    'icon' => 'fas fa-newspaper',
                    'color' => 'text-purple-500',
                    'label' => __('Blog')
                ],
                'Portfolio' => [
                    'icon' => 'fas fa-briefcase',
                    'color' => 'text-orange-500',
                    'label' => __('Portfolyo')
                ],
                'Announcement' => [
                    'icon' => 'fas fa-bullhorn',
                    'color' => 'text-yellow-500',
                    'label' => __('Duyurular')
                ],
                'Page' => [
                    'icon' => 'fas fa-file-alt',
                    'color' => 'text-indigo-500',
                    'label' => __('Sayfalar')
                ],
            ];

            // Tenant'ın aktif modüllerini module_tenants'dan çek
            try {
                $activeModules = \Nwidart\Modules\Facades\Module::allEnabled();

                foreach ($activeModules as $module) {
                    $moduleName = $module->getName();

                    // Mapping'de varsa ekle
                    if (isset($moduleMap[$moduleName])) {
                        try {
                            $slug = \App\Services\ModuleSlugService::getSlug($moduleName, 'index');
                            $quickLinks[] = [
                                'url' => url($slug),
                                'icon' => $moduleMap[$moduleName]['icon'],
                                'color' => $moduleMap[$moduleName]['color'],
                                'label' => $moduleMap[$moduleName]['label']
                            ];
                        } catch (\Exception $e) {
                            // Slug yoksa skip
                            continue;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Hata varsa sadece Ana Sayfa göster
            }

            // Max 4 link göster (responsive design için)
            $quickLinks = array_slice($quickLinks, 0, 4);
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @foreach($quickLinks as $link)
            <a href="{{ $link['url'] }}" class="group p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all transform hover:-translate-y-1">
                <i class="{{ $link['icon'] }} text-3xl {{ $link['color'] }} mb-2"></i>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $link['label'] }}</p>
            </a>
            @endforeach
        </div>

        {{-- Back Button --}}
        <div class="flex gap-4 justify-center">
            <button
                onclick="window.history.back()"
                class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-all"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Geri Dön') }}
            </button>

            <a
                href="{{ url('/') }}"
                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg"
            >
                <i class="fas fa-home mr-2"></i>
                {{ __('Ana Sayfaya Git') }}
            </a>
        </div>

        {{-- Fun Easter Egg --}}
        <div class="mt-12 text-gray-400 dark:text-gray-600 text-sm">
            <p>{{ __('Kaybolmak bazen yeni şeyler keşfetmek demektir') }} 🚀</p>
        </div>
    </div>
</div>
@endsection
