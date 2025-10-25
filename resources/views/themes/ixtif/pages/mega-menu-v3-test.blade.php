@extends('themes.ixtif.layout')

@section('title', 'Mega Menu V3 - Professional with Search | iXtif')

@section('content')
<div class="container mx-auto px-6 py-12 space-y-20">

    <!-- Header -->
    <div class="text-center mb-16">
        <h1 class="text-5xl font-black text-gray-900 dark:text-white mb-4">
            🚀 Mega Menu V3 - Professional
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-300 mb-2">
            Gerçek Ürünlerle + Canlı Search + Asymmetric Pro Design
        </p>
        <div class="inline-flex items-center gap-2 bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-bold">
            <i class="fa-solid fa-check-circle"></i>
            100% GERÇEK VERİ - Fake Ürün YOK!
        </div>
    </div>

    <!-- V3 Features -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-2xl p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">✨ V3 Özellikleri</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-magnifying-glass text-indigo-600 text-2xl mt-1"></i>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-1">Canlı Arama</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">300ms debounce ile gerçek zamanlı ürün araması</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-database text-purple-600 text-2xl mt-1"></i>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-1">Gerçek Veri</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Veritabanından gerçek ürünler - fake veri YOK!</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-bolt text-yellow-600 text-2xl mt-1"></i>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-1">Livewire Powered</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Dinamik, hızlı ve modern component</p>
                </div>
            </div>
        </div>
    </div>

    <!-- V3.1: Forklift Category -->
    <section class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">V3.1: Forklift Mega Menu</h2>
                <p class="text-gray-600 dark:text-gray-400">Category ID: 1 | 128 gerçek ürün</p>
            </div>
            <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-full text-sm font-bold shadow-lg">
                💼 FORKLIFT
            </span>
        </div>

        <livewire:theme.mega-menu-v3 :categoryId="1" />

        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 text-sm text-gray-700 dark:text-gray-300">
            <strong>Test:</strong> Yukarıdaki search kutusuna "direk" veya "ton" yazın - gerçek ürünler gelecek!
        </div>
    </section>

    <!-- V3.2: Transpalet Category -->
    <section class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">V3.2: Transpalet Mega Menu</h2>
                <p class="text-gray-600 dark:text-gray-400">Category ID: 2 | 69 gerçek ürün</p>
            </div>
            <span class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full text-sm font-bold shadow-lg">
                🚛 TRANSPALET
            </span>
        </div>

        <livewire:theme.mega-menu-v3 :categoryId="2" />

        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 text-sm text-gray-700 dark:text-gray-300">
            <strong>Test:</strong> "wpl" veya "çatal" yazarak arama yapın - İXTİF WPL202 modelleri listelenecek!
        </div>
    </section>

    <!-- V3.3: İstif Makinesi Category -->
    <section class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">V3.3: İstif Makinesi Mega Menu</h2>
                <p class="text-gray-600 dark:text-gray-400">Category ID: 3 | 106 gerçek ürün</p>
            </div>
            <span class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full text-sm font-bold shadow-lg">
                📦 İSTİF
            </span>
        </div>

        <livewire:theme.mega-menu-v3 :categoryId="3" />

        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-4 text-sm text-gray-700 dark:text-gray-300">
            <strong>Test:</strong> "wsa" veya "direk" yazın - İXTİF WSA161i modelleri gelecek!
        </div>
    </section>

    <!-- Comparison: V2 vs V3 -->
    <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">📊 V2 vs V3 Karşılaştırması</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 px-3 font-bold text-gray-700 dark:text-gray-300">Özellik</th>
                        <th class="text-center py-3 px-3 font-bold text-orange-600">V2<br><span class="text-xs font-normal text-gray-600 dark:text-gray-400">Static HTML</span></th>
                        <th class="text-center py-3 px-3 font-bold text-indigo-600">V3<br><span class="text-xs font-normal text-gray-600 dark:text-gray-400">Livewire</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-3 px-3 font-medium text-gray-700 dark:text-gray-300">Gerçek Veri</td>
                        <td class="text-center py-3 px-3">❌ Fake ürünler</td>
                        <td class="text-center py-3 px-3">✅ 100% gerçek DB</td>
                    </tr>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-3 px-3 font-medium text-gray-700 dark:text-gray-300">Search</td>
                        <td class="text-center py-3 px-3">❌ Yok</td>
                        <td class="text-center py-3 px-3">✅ Canlı arama</td>
                    </tr>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-3 px-3 font-medium text-gray-700 dark:text-gray-300">Dinamik</td>
                        <td class="text-center py-3 px-3">❌ Static HTML</td>
                        <td class="text-center py-3 px-3">✅ Livewire component</td>
                    </tr>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-3 px-3 font-medium text-gray-700 dark:text-gray-300">Profesyonellik</td>
                        <td class="text-center py-3 px-3">⭐⭐⭐⭐</td>
                        <td class="text-center py-3 px-3">⭐⭐⭐⭐⭐</td>
                    </tr>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-3 px-3 font-medium text-gray-700 dark:text-gray-300">Ürün Sayısı</td>
                        <td class="text-center py-3 px-3 text-xs">1-2 ürün (fake)</td>
                        <td class="text-center py-3 px-3 text-xs">5 ürün (gerçek)</td>
                    </tr>
                    <tr class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 font-semibold">
                        <td class="py-3 px-3 text-gray-900 dark:text-white">💡 Sonuç</td>
                        <td class="text-center py-3 px-3 text-orange-700 dark:text-orange-400">Design Demo</td>
                        <td class="text-center py-3 px-3 text-indigo-700 dark:text-indigo-400">Production Ready!</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Footer Info -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-8 text-white shadow-2xl">
        <div class="flex items-start gap-6">
            <div class="text-6xl">🎯</div>
            <div class="flex-1">
                <h3 class="text-3xl font-bold mb-3">V3 Production Ready!</h3>
                <p class="text-white/90 mb-4 text-lg">
                    Bu mega menu sisteminizde <strong>şu an kullanılabilir</strong> - gerçek ürünlerle çalışıyor!
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-green-300"></i>
                        <span>✅ Gerçek ürünler - fake veri YOK</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-green-300"></i>
                        <span>✅ Canlı search (300ms debounce)</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-green-300"></i>
                        <span>✅ Livewire component (kolay entegrasyon)</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-green-300"></i>
                        <span>✅ Asymmetric Pro tasarım (V2.5 seviyesi)</span>
                    </li>
                </ul>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                    <strong class="text-yellow-300">📌 Kullanım:</strong>
                    <code class="block mt-2 text-sm bg-black/30 rounded px-3 py-2">
                        &lt;livewire:theme.mega-menu-v3 :categoryId="1" /&gt;
                    </code>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
