{{-- Context Menu Component --}}
{{-- Desktop: Positioned menu --}}
<div x-show="$store.contextMenu.visible"
     x-cloak
     x-on:click.outside="$store.contextMenu.visible = false"
     x-on:keydown.escape.window="$store.contextMenu.visible = false"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     :style="`position: fixed; left: ${$store.contextMenu.x}px; top: ${$store.contextMenu.y}px; z-index: 99999;`"
     class="hidden sm:block bg-gray-900 rounded-lg shadow-2xl border border-gray-700 py-1.5 w-[200px] max-h-[500px] overflow-y-auto">

    {{-- Context Menu Header - Kompakt Tasarım --}}
    <div class="bg-gradient-to-r from-[#ff7f50] to-[#ff6a3d] text-white px-2 py-1.5 mb-1 rounded mx-1">
        <div class="text-sm font-black uppercase tracking-wide" x-text="window.getContextTypeLabel?.($store.contextMenu.type) || $store.contextMenu.type"></div>
        <div class="text-[10px] opacity-80 truncate" x-text="$store.contextMenu.data?.title"></div>
    </div>

    {{-- Actions - Kompakt Tasarım --}}
    <template x-for="(action, index) in $store.contextMenu.actions" :key="index">
        <div>
            {{-- Divider --}}
            <div x-show="action.divider" class="border-t border-gray-800 my-0.5"></div>

            {{-- Single Action Button --}}
            <button x-show="!action.divider"
                    x-on:click="$store.contextMenu.executeAction(action.action); $store.contextMenu.visible = false"
                    :class="action.debug ? 'bg-orange-900/20 hover:bg-orange-900/40' : 'hover:bg-gray-800'"
                    class="w-full px-3 py-2 text-left transition-colors duration-200 flex items-center gap-2 text-gray-300 text-xs">
                <i :class="`${action.iconPrefix || 'fas'} ${action.icon} w-3`"
                   :class="[action.debug ? 'text-orange-500' : 'text-gray-400', action.action === 'toggleFavorite' && $store.contextMenu.data?.is_favorite ? 'text-red-500' : '']"></i>
                <span :class="action.debug ? 'text-orange-300 font-mono text-[10px]' : ''"
                      x-text="action.label"></span>
                <i x-show="action.submenu" class="fas fa-chevron-right ml-auto text-[10px] text-gray-500"></i>
            </button>
        </div>
    </template>
</div>

{{-- Mobile: Bottom Sheet with Swipe-to-dismiss --}}
<div x-show="$store.contextMenu.visible"
     x-cloak
     class="sm:hidden fixed inset-0 z-[99999]"
     x-data="{ startY: 0, currentY: 0, isDragging: false }">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
         x-on:click="$store.contextMenu.visible = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    {{-- Bottom Sheet --}}
    <div class="absolute bottom-0 left-0 right-0 bg-zinc-900 rounded-t-3xl border-t border-white/10 shadow-2xl max-h-[70vh] overflow-hidden"
         :style="isDragging && currentY > 0 ? `transform: translateY(${currentY}px); opacity: ${Math.max(0.5, 1 - currentY/200)}` : ''"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         x-on:keydown.escape.window="$store.contextMenu.visible = false">

        {{-- Handle Bar --}}
        <div class="flex justify-center pt-3 pb-2 touch-none"
             style="overscroll-behavior: contain;"
             @touchstart="startY = $event.touches[0].clientY; isDragging = true; currentY = 0"
             @touchmove.prevent="if(isDragging) { currentY = Math.max(0, $event.touches[0].clientY - startY); }"
             @touchend="if(currentY > 80) { $store.contextMenu.visible = false; } isDragging = false; currentY = 0;">
            <div class="w-12 h-1.5 bg-zinc-600 rounded-full"></div>
        </div>

        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#ff7f50] to-[#ff6a3d] text-white px-4 py-3 mx-3 mb-2 rounded-xl touch-none"
             style="overscroll-behavior: contain;"
             @touchstart="startY = $event.touches[0].clientY; isDragging = true; currentY = 0"
             @touchmove.prevent="if(isDragging) { currentY = Math.max(0, $event.touches[0].clientY - startY); }"
             @touchend="if(currentY > 80) { $store.contextMenu.visible = false; } isDragging = false; currentY = 0;">
            <div class="text-sm font-black uppercase tracking-wide" x-text="window.getContextTypeLabel?.($store.contextMenu.type) || $store.contextMenu.type"></div>
            <div class="text-sm opacity-90 truncate mt-0.5" x-text="$store.contextMenu.data?.title"></div>
        </div>

        {{-- Actions --}}
        <div class="px-2 pb-6 overflow-y-auto max-h-[50vh]">
            <template x-for="(action, index) in $store.contextMenu.actions" :key="'mobile-'+index">
                <div>
                    {{-- Divider --}}
                    <div x-show="action.divider" class="border-t border-zinc-800 my-1 mx-2"></div>

                    {{-- Action Button --}}
                    <button x-show="!action.divider"
                            x-on:click="$store.contextMenu.executeAction(action.action); $store.contextMenu.visible = false"
                            :class="action.debug ? 'bg-orange-900/20' : 'active:bg-white/10'"
                            class="w-full px-4 py-3.5 text-left flex items-center gap-3 text-white rounded-xl">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                             :class="action.debug ? 'bg-orange-500/20' : 'bg-white/10'">
                            <i :class="`${action.iconPrefix || 'fas'} ${action.icon}`"
                               :class="[action.debug ? 'text-orange-500' : 'text-white', action.action === 'toggleFavorite' && $store.contextMenu.data?.is_favorite ? 'text-red-500' : '']"></i>
                        </div>
                        <span :class="action.debug ? 'text-orange-300 font-mono text-sm' : 'text-base'"
                              x-text="action.label"></span>
                        <i x-show="action.submenu" class="fas fa-chevron-right ml-auto text-zinc-500"></i>
                    </button>
                </div>
            </template>
        </div>

        {{-- Safe Area for iPhone --}}
        <div class="h-safe-area-inset-bottom"></div>
    </div>
</div>
