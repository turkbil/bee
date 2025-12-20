@if($radios && $radios->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-4 gap-4 sm:gap-6">
        @foreach($radios as $radio)
            <x-muzibu.radio-card :radio="$radio" />
        @endforeach
    </div>
@else
    <div class="text-center py-20">
        <div class="mb-6">
            <i class="fas fa-radio text-gray-600 text-6xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-white mb-2">Henüz radyo yok</h3>
        <p class="text-gray-400">Yakında yeni radyo kanalları eklenecek</p>
    </div>
@endif
