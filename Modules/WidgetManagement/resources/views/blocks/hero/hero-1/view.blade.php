<section class="py-12 lg:py-20 text-center bg-gradient-to-br from-gray-50 to-gray-100" x-data="{ loaded: false }" x-init="loaded = true">
    <div class="container mx-auto px-4" x-show="loaded" x-transition.duration.500ms>
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl lg:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                Full Width Hero
            </h1>
            <p class="text-xl lg:text-2xl text-gray-600 mb-8 leading-relaxed">
                Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın. Modern bir görünüm kazandırabilirsiniz.
            </p>
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <button class="w-full sm:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                    Ana Buton
                </button>
                <button class="w-full sm:w-auto px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                    İkincil Buton
                </button>
            </div>
        </div>
    </div>
</section>