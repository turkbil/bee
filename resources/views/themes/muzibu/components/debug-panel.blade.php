{{-- 🧪 DEBUG PANEL - Draggable & Scrollable --}}
<div
    x-data="{
        show: false,
        logs: [],
        position: { x: 20, y: 100 },
        isDragging: false,
        dragOffset: { x: 0, y: 0 },
        minimized: false,
        debugEnabled: false,

        init() {
            const self = this;

            // 🔄 Sync logs from window.debugLogs
            const syncLogs = () => {
                // Debug.js yüklenmemiş olabilir - array yoksa oluştur
                if (!window.debugLogs) {
                    window.debugLogs = [];
                }
                // Sadece değişiklik varsa güncelle (performans)
                if (window.debugLogs.length !== self.logs.length) {
                    self.logs = [...window.debugLogs];
                }
            };

            // Listen for debug logs (debug.js'den)
            window.addEventListener('debug-log', (e) => {
                self.logs = [...window.debugLogs];
            });

            // 🔄 debug.js yüklenene kadar bekle
            const waitForDebug = setInterval(() => {
                if (window.debugLog && window.debugFeature) {
                    clearInterval(waitForDebug);
                    self.debugEnabled = window.debugFeature.showDebugInfo === true;
                    syncLogs();
                    console.log('%c🧪 Debug Panel connected', 'color: #8b5cf6;');
                }
            }, 100);

            // 🔄 Periodic sync (500ms - daha responsive)
            setInterval(syncLogs, 500);

            // Check Alpine store
            if (this.$store?.debug) {
                this.$watch('$store.debug.showPanel', (value) => {
                    this.show = value;
                });
            }
        },

        startDrag(e) {
            if (e.target.closest('.debug-panel-content')) return;
            this.isDragging = true;
            const rect = this.$refs.panel.getBoundingClientRect();
            this.dragOffset.x = e.clientX - rect.left;
            this.dragOffset.y = e.clientY - rect.top;
        },

        onDrag(e) {
            if (!this.isDragging) return;
            this.position.x = e.clientX - this.dragOffset.x;
            this.position.y = e.clientY - this.dragOffset.y;
        },

        stopDrag() {
            this.isDragging = false;
        },

        togglePanel() {
            this.show = !this.show;
            if (window.Alpine?.store('debug')) {
                window.Alpine.store('debug').showPanel = this.show;
            }
        },

        clearLogs() {
            window.clearDebugLogs?.();
            this.logs = [];
        },

        getTypeColor(type) {
            const colors = {
                refill: 'text-green-400',
                transition: 'text-amber-400',
                exhausted: 'text-red-400',
                fallback: 'text-purple-400',
                queue: 'text-blue-400',
                play: 'text-cyan-400',
                remove: 'text-orange-400',
                shuffle: 'text-pink-400',
                info: 'text-gray-400'
            };
            return colors[type] || 'text-gray-400';
        },

        getTypeEmoji(type) {
            const emojis = {
                refill: '+',
                transition: '>',
                exhausted: '!',
                fallback: '~',
                queue: '#',
                play: '>',
                remove: '-',
                shuffle: '*',
                info: 'i'
            };
            return emojis[type] || '*';
        }
    }"
    x-cloak
    @mousemove.window="onDrag($event)"
    @mouseup.window="stopDrag()"
    class="fixed z-[9999]"
>
    {{-- Toggle Button (visible when debug.js loaded and showDebugInfo=true) --}}
    <button
        x-show="debugEnabled"
        x-transition
        @click="togglePanel()"
        class="fixed bottom-24 right-4 w-10 h-10 bg-purple-600/90 hover:bg-purple-500 text-white rounded-full flex items-center justify-center shadow-lg backdrop-blur-sm transition-all"
        :class="{ 'bg-purple-500': show }"
        title="Debug Panel"
    >
        <i class="fas fa-bug text-sm"></i>
    </button>

    {{-- Debug Panel --}}
    <div
        x-ref="panel"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        :style="`left: ${position.x}px; top: ${position.y}px;`"
        class="fixed bg-gray-900/95 backdrop-blur-md border border-gray-700 rounded-lg shadow-2xl overflow-hidden"
        :class="minimized ? 'w-48' : 'w-80 sm:w-96'"
        style="max-height: 70vh;"
    >
        {{-- Header (Draggable) --}}
        <div
            @mousedown="startDrag($event)"
            class="flex items-center justify-between px-3 py-2 bg-purple-600/30 border-b border-gray-700 cursor-move select-none"
        >
            <div class="flex items-center gap-2">
                <i class="fas fa-bug text-purple-400 text-xs"></i>
                <span class="text-white text-xs font-bold">Debug Panel</span>
                <span class="text-gray-400 text-[10px]" x-text="`(${logs.length})`"></span>
            </div>
            <div class="flex items-center gap-1">
                {{-- Minimize --}}
                <button
                    @click.stop="minimized = !minimized"
                    class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white rounded transition-colors"
                >
                    <i :class="minimized ? 'fas fa-expand' : 'fas fa-minus'" class="text-[10px]"></i>
                </button>
                {{-- Clear --}}
                <button
                    @click.stop="clearLogs()"
                    class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-red-400 rounded transition-colors"
                    title="Temizle"
                >
                    <i class="fas fa-trash text-[10px]"></i>
                </button>
                {{-- Close --}}
                <button
                    @click.stop="togglePanel()"
                    class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white rounded transition-colors"
                >
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        {{-- Content --}}
        <div
            x-show="!minimized"
            class="debug-panel-content overflow-y-auto p-2 space-y-1 select-text"
            style="max-height: calc(70vh - 40px);"
        >
            <template x-if="logs.length === 0">
                <div class="text-gray-500 text-xs text-center py-4">
                    Henuz log yok...
                </div>
            </template>

            <template x-for="(log, idx) in logs" :key="'log-' + idx + '-' + (log?.id || idx)">
                <div
                    class="text-[10px] font-mono p-1.5 rounded bg-gray-800/50 hover:bg-gray-800 transition-colors cursor-default"
                    @click="console.log(log?.details)"
                >
                    {{-- Header --}}
                    <div class="flex items-center gap-1.5">
                        <span class="text-gray-500" x-text="log?.timestamp || ''"></span>
                        <span
                            :class="getTypeColor(log?.type)"
                            class="font-bold"
                            x-text="`[${getTypeEmoji(log?.type)}]`"
                        ></span>
                        <span class="text-white flex-1 truncate" x-text="log?.message || ''"></span>
                    </div>

                    {{-- Details (expandable) --}}
                    <div
                        x-show="log?.details && Object.keys(log.details).length > 0"
                        class="mt-1 pl-2 border-l border-gray-700 text-gray-400 space-y-0.5"
                    >
                        <template x-for="(entry, entryIdx) in Object.entries(log?.details || {})" :key="'entry-' + idx + '-' + entryIdx">
                            <div class="truncate">
                                <span class="text-gray-500" x-text="entry[0] + ':'"></span>
                                <span
                                    class="text-gray-300 ml-1"
                                    x-text="typeof entry[1] === 'object' ? JSON.stringify(entry[1]) : entry[1]"
                                ></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer Stats (Minimized view) --}}
        <div
            x-show="minimized"
            class="px-3 py-2 text-[10px] text-gray-400 flex items-center justify-between"
        >
            <span x-text="`${logs.length} event`"></span>
            <span
                class="text-green-400"
                x-text="logs.filter(l => l.type === 'refill').length + ' refill'"
            ></span>
        </div>
    </div>
</div>
