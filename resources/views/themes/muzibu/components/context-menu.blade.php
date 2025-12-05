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
     class="bg-gray-800 rounded-lg shadow-2xl border border-white/20 py-1 min-w-[280px] max-h-[500px] overflow-y-auto">

    {{-- Context Menu Header --}}
    <div class="px-3 py-1.5 border-b border-white/10">
        <p class="text-xs text-gray-400 uppercase font-semibold" x-text="$store.contextMenu.type"></p>
        <p class="text-sm text-white font-semibold truncate" x-text="$store.contextMenu.data?.title"></p>
    </div>

    {{-- Actions --}}
    <template x-for="(action, index) in $store.contextMenu.actions" :key="index">
        <div>
            {{-- Divider --}}
            <div x-show="action.divider" class="border-t border-white/10 my-1"></div>

            {{-- Row (2 buttons side by side) --}}
            <div x-show="!action.divider && action.row"
                 class="grid grid-cols-2 gap-1 px-1">
                <template x-for="btn in action.buttons" :key="btn.action">
                    <button x-on:click="$store.contextMenu.executeAction(btn.action); $store.contextMenu.visible = false"
                            :class="btn.debug ? 'bg-orange-900/20 hover:bg-orange-900/40' : 'hover:bg-white/10'"
                            class="px-2 py-1.5 rounded transition-all duration-200 flex items-center justify-start gap-2 text-white text-xs group">
                        <i :class="`fas ${btn.icon} transition-colors text-xs`"
                           :class="btn.debug ? 'text-orange-500' : 'text-gray-400 group-hover:text-orange-500'"></i>
                        <span class="group-hover:translate-x-0.5 transition-transform truncate"
                              :class="btn.debug ? 'text-orange-300 font-mono text-[10px]' : ''"
                              x-text="btn.label"></span>
                    </button>
                </template>
            </div>

            {{-- Single Action Button --}}
            <button x-show="!action.divider && !action.row"
                    x-on:click="$store.contextMenu.executeAction(action.action); $store.contextMenu.visible = false"
                    :class="action.debug ? 'bg-orange-900/20 hover:bg-orange-900/40' : 'hover:bg-white/10'"
                    class="w-full px-3 py-1.5 text-left transition-all duration-200 flex items-center gap-2 text-white text-xs group">
                <i :class="`fas ${action.icon} transition-colors text-xs`"
                   :class="action.debug ? 'text-orange-500' : 'text-gray-400 group-hover:text-orange-500'"></i>
                <span class="group-hover:translate-x-0.5 transition-transform"
                      :class="action.debug ? 'text-orange-300 font-mono text-[10px]' : ''"
                      x-text="action.label"></span>
                <i x-show="action.submenu" class="fas fa-chevron-right ml-auto text-[10px] text-gray-500"></i>
            </button>
        </div>
    </template>
</div>
