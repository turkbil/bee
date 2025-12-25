{{-- Context Menu Component --}}
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
     :style="`position: fixed; left: ${$store.contextMenu.x}px; top: ${$store.contextMenu.y}px; z-index: 9999;`"
     class="bg-gray-900 rounded-lg shadow-2xl border border-gray-700 py-1.5 w-[200px] max-h-[500px] overflow-y-auto">

    {{-- Context Menu Header - Kompakt Tasarım --}}
    <div class="bg-gradient-to-r from-[#ff7f50] to-[#ff6a3d] text-white px-2 py-1.5 mb-1 rounded mx-1">
        <div class="text-sm font-black uppercase tracking-wide" x-text="$store.contextMenu.type"></div>
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
