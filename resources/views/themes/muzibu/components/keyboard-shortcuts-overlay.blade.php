{{-- KEYBOARD SHORTCUTS OVERLAY - Minimal & Professional --}}
<div x-show="showKeyboardHelp" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="showKeyboardHelp = false"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40"
     x-cloak>
</div>

{{-- Overlay Panel --}}
<aside x-show="showKeyboardHelp"
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="translate-x-full"
       @keydown.escape="showKeyboardHelp = false"
       class="fixed top-0 right-0 bottom-0 w-80 bg-muzibu-gray/95 backdrop-blur-md shadow-2xl z-50 flex flex-col border-l border-white/10"
       x-cloak>
    
    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-white/10">
        <h3 class="text-base font-semibold text-white">
            Klavye Kısayolları
        </h3>
        <button @click="showKeyboardHelp = false" 
                class="text-muzibu-text-gray hover:text-white transition-all">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Shortcuts List --}}
    <div class="flex-1 overflow-y-auto px-4 py-3">
        <div class="space-y-1">
            {{-- Play/Pause --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">Space / K</kbd>
                <span class="text-sm text-muzibu-text-gray">Çal / Duraklat</span>
            </div>

            {{-- Seek Backward --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">← / J</kbd>
                <span class="text-sm text-muzibu-text-gray">5sn Geri</span>
            </div>

            {{-- Seek Forward --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">→ / L</kbd>
                <span class="text-sm text-muzibu-text-gray">5sn İleri</span>
            </div>

            {{-- Volume Up --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">↑</kbd>
                <span class="text-sm text-muzibu-text-gray">Ses Artır</span>
            </div>

            {{-- Volume Down --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">↓</kbd>
                <span class="text-sm text-muzibu-text-gray">Ses Azalt</span>
            </div>

            {{-- Mute --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">M</kbd>
                <span class="text-sm text-muzibu-text-gray">Sessiz</span>
            </div>

            {{-- Loop --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">L</kbd>
                <span class="text-sm text-muzibu-text-gray">Tekrarla</span>
            </div>

            {{-- Shuffle --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">S</kbd>
                <span class="text-sm text-muzibu-text-gray">Karıştır</span>
            </div>

            {{-- Next Song --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">N</kbd>
                <span class="text-sm text-muzibu-text-gray">Sonraki Şarkı</span>
            </div>

            {{-- Previous Song --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">P</kbd>
                <span class="text-sm text-muzibu-text-gray">Önceki Şarkı</span>
            </div>

            {{-- Queue --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">Q</kbd>
                <span class="text-sm text-muzibu-text-gray">Sıra</span>
            </div>

            {{-- Lyrics --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">Y</kbd>
                <span class="text-sm text-muzibu-text-gray">Şarkı Sözü</span>
            </div>

            {{-- Favorite --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">F</kbd>
                <span class="text-sm text-muzibu-text-gray">Favori</span>
            </div>

            {{-- Number Keys --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">0-9</kbd>
                <span class="text-sm text-muzibu-text-gray">Sıradan Çal</span>
            </div>

            {{-- Help --}}
            <div class="flex items-center gap-2 py-2">
                <kbd class="px-2 py-1 bg-white/10 text-white rounded text-xs font-mono min-w-[70px] text-center">?</kbd>
                <span class="text-sm text-muzibu-text-gray">Bu Yardım</span>
            </div>
        </div>
    </div>
</aside>
