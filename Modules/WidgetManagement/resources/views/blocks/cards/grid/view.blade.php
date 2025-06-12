<div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
    @foreach([1,2,3] as $index)
    <article class="group rounded-xl shadow-lg bg-white overflow-hidden h-full transform hover:scale-105 transition-all duration-300" 
             x-show="loaded" 
             x-transition.delay.{{ ($index - 1) * 100 }}ms.duration.500ms>
        <div class="relative overflow-hidden">
            <img src="https://placehold.co/600x400" 
                 class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500" 
                 alt="Kart Görseli"
                 loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>
        <div class="p-6">
            <h5 class="text-xl font-semibold mb-3 text-gray-900 group-hover:text-blue-600 transition-colors">Kart Başlığı {{ $index }}</h5>
            <p class="text-gray-600 leading-relaxed">Kart içeriği buraya gelir. Bu alanı kullanarak kısa açıklamalar veya bilgiler ekleyebilirsiniz.</p>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t">
            <button class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transform hover:scale-105 transition-all duration-200">
                Detayları Gör
            </button>
        </div>
    </article>
    @endforeach
</div>