@props([
    'icon' => 'music',
    'title' => 'İçerik Bulunamadı',
    'message' => null,
    'actionText' => null,
    'actionUrl' => null
])

{{--
╔═══════════════════════════════════════════════════════════════════════════╗
║ MUZIBU COMPONENT: Empty State                                             ║
╠═══════════════════════════════════════════════════════════════════════════╣
║ Açıklama: Boş içerik durumları için kullanıcı dostu mesaj ekranı          ║
║           Favoriler, arama sonuçları, playlist gibi boş listeler için      ║
║                                                                            ║
║ Props:                                                                     ║
║   - icon: String - FontAwesome icon ismi (varsayılan: 'music')           ║
║   - title: String - Başlık metni (varsayılan: 'İçerik Bulunamadı')       ║
║   - message: String|null - Açıklama metni (opsiyonel)                     ║
║   - actionText: String|null - Buton metni (opsiyonel)                     ║
║   - actionUrl: String|null - Buton URL'i (opsiyonel)                      ║
║                                                                            ║
║ Kullanım:                                                                  ║
║   {{-- Basit kullanım --}}                                                 ║
║   <x-muzibu.empty-state icon="heart" title="Favorileriniz Boş" />        ║
║                                                                            ║
║   {{-- Mesaj ve butonla --}}                                               ║
║   <x-muzibu.empty-state                                                    ║
║       icon="heart"                                                         ║
║       title="Favorileriniz Boş"                                           ║
║       message="Beğendiğiniz şarkıları buradan takip edebilirsiniz."      ║
║       actionText="Müziklere Göz At"                                        ║
║       actionUrl="/"                                                        ║
║   />                                                                       ║
║                                                                            ║
║   {{-- Slot ile custom content --}}                                        ║
║   <x-muzibu.empty-state icon="search" title="Sonuç Bulunamadı">          ║
║       <button class="btn">Filtreleri Temizle</button>                     ║
║   </x-muzibu.empty-state>                                                  ║
║                                                                            ║
║ Özellikler:                                                                ║
║   ✓ Gradient icon background (coral/purple)                              ║
║   ✓ Responsive layout (centered)                                          ║
║   ✓ Optional action button (coral → green hover)                         ║
║   ✓ Slot support (custom content eklenebilir)                            ║
║                                                                            ║
║ Dependencies:                                                              ║
║   - FontAwesome icons                                                      ║
║   - Tailwind utilities                                                     ║
╚═══════════════════════════════════════════════════════════════════════════╝
--}}

<div class="flex flex-col items-center justify-center py-16 px-6 text-center">
    {{-- Icon --}}
    <div class="w-24 h-24 bg-gradient-to-br from-muzibu-coral/20 to-purple-600/20 rounded-full flex items-center justify-center mb-6">
        <i class="fas fa-{{ $icon }} text-muzibu-coral text-4xl"></i>
    </div>

    {{-- Title --}}
    <h3 class="text-2xl font-bold text-white mb-3">{{ $title }}</h3>

    {{-- Message --}}
    @if($message)
        <p class="text-gray-400 max-w-md mb-6">{{ $message }}</p>
    @endif

    {{-- Action Button --}}
    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-muzibu-coral hover:bg-green-500 text-white font-semibold rounded-full transition-all hover:scale-105 shadow-lg">
            {{ $actionText }}
            <i class="fas fa-arrow-right"></i>
        </a>
    @endif

    {{-- Custom Slot Content --}}
    @if($slot->isNotEmpty())
        <div class="mt-6">
            {{ $slot }}
        </div>
    @endif
</div>
