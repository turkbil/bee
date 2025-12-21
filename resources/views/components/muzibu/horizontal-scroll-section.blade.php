@props(['title' => null, 'gridMode' => false])

{{--
╔═══════════════════════════════════════════════════════════════════════════╗
║ MUZIBU COMPONENT: Horizontal Scroll Section                               ║
╠═══════════════════════════════════════════════════════════════════════════╣
║ Açıklama: Spotify-style yatay scroll bölümü                               ║
║           Tekrar kullanılabilir scroll container (141 satır → 27 satır!)  ║
║                                                                            ║
║ Props:                                                                     ║
║   - title: String|null - Bölüm başlığı (opsiyonel)                        ║
║   - gridMode: Boolean - Grid layout (2 satır) veya flex (varsayılan)     ║
║                                                                            ║
║ Kullanım:                                                                  ║
║   Flex Mode (Normal):                                                      ║
║   <x-muzibu.horizontal-scroll-section title="Öne Çıkanlar">               ║
║       Loop: $albums                                                        ║
║           <div class="flex-shrink-0 w-[190px]">                           ║
║               <x-muzibu.album-card :album="$album" />                      ║
║           </div>                                                           ║
║       End Loop                                                             ║
║   </x-muzibu.horizontal-scroll-section>                                   ║
║                                                                            ║
║   Grid Mode (Quick Access - 2 rows):                                      ║
║   <x-muzibu.horizontal-scroll-section :grid-mode="true">                  ║
║       Loop: $playlists                                                     ║
║           <x-muzibu.playlist-quick-card :playlist="$playlist" />          ║
║       End Loop                                                             ║
║   </x-muzibu.horizontal-scroll-section>                                   ║
║                                                                            ║
║ Özellikler:                                                                ║
║   ✓ Auto-scroll on hover (left/right arrows)                             ║
║   ✓ Smooth scroll animation (400px jump, 20px/50ms hover)                ║
║   ✓ Scroll arrows (opacity 0 → 100 on hover)                             ║
║   ✓ Slot-based content (any component içine yerleştirilebilir)           ║
║   ✓ Scrollbar gizli (scrollbar-hide utility)                             ║
║   ✓ Grid mode support (2-row layout for Quick Access)                    ║
║                                                                            ║
║ Dependencies:                                                              ║
║   - Alpine.js: horizontalScroll() data component                          ║
║   - Tailwind: scrollbar-hide utility                                      ║
║   - JavaScript: setInterval/clearInterval                                 ║
║                                                                            ║
║ Kod Azaltma:                                                               ║
║   Öncesi: Her scroll section için 47 satır × 4 = 188 satır               ║
║   Sonrası: Component kullanımı = 9 satır × 4 = 36 satır                  ║
║   Kazanç: 152 satır (81% azalma)                                          ║
╚═══════════════════════════════════════════════════════════════════════════╝
--}}

<div class="mb-6 relative group/scroll" x-data="horizontalScroll()">
    @if($title)
        <h2 class="text-2xl font-bold text-white mb-2">{{ $title }}</h2>
    @endif

    {{-- Left Arrow --}}
    <button @click="scrollLeft()"
            @mouseenter="startAutoScroll('left')"
            @mouseleave="stopAutoScroll()"
            class="absolute left-[-12px] top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/90 hover:bg-black rounded-full flex items-center justify-center text-white opacity-0 group-hover/scroll:opacity-100 transition-opacity shadow-xl">
        <i class="fas fa-chevron-left"></i>
    </button>

    {{-- Right Arrow --}}
    <button @click="scrollRight()"
            @mouseenter="startAutoScroll('right')"
            @mouseleave="stopAutoScroll()"
            class="absolute right-[-12px] top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/90 hover:bg-black rounded-full flex items-center justify-center text-white opacity-0 group-hover/scroll:opacity-100 transition-opacity shadow-xl">
        <i class="fas fa-chevron-right"></i>
    </button>

    {{-- Scroll Container --}}
    <div x-ref="scrollContainer" class="overflow-x-auto scrollbar-hide scroll-smooth pb-4 @if($gridMode) grid grid-rows-2 grid-flow-col auto-cols-[minmax(280px,1fr)] gap-2 @else flex gap-4 @endif">
        {{ $slot }}
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('horizontalScroll', () => ({
            scrollContainer: null,
            scrollInterval: null,

            init() {
                this.scrollContainer = this.$refs.scrollContainer;
            },

            scrollLeft() {
                this.scrollContainer.scrollBy({ left: -400, behavior: 'smooth' });
            },

            scrollRight() {
                this.scrollContainer.scrollBy({ left: 400, behavior: 'smooth' });
            },

            startAutoScroll(direction) {
                this.scrollInterval = setInterval(() => {
                    this.scrollContainer.scrollBy({ left: direction === 'right' ? 20 : -20 });
                }, 50);
            },

            stopAutoScroll() {
                if (this.scrollInterval) {
                    clearInterval(this.scrollInterval);
                    this.scrollInterval = null;
                }
            }
        }));
    });
</script>
