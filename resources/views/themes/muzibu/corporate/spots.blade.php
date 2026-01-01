@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.corporate.spot_management') . ' - ' . ($account->company_name ?? __('muzibu::front.footer.corporate')))

@php
$pageData = [
    'spots' => $spotsJson,
    'settings' => [
        'spot_enabled' => $account->spot_enabled ? true : false,
        'spot_songs_between' => $account->spot_songs_between ?: 10,
        'songs_played' => $songsPlayed ?? 0,
    ]
];
@endphp

@section('content')
<div id="spots-data" style="display:none;">@json($pageData)</div>

<div x-data="spotManager()" x-init="init()" class="min-h-screen pb-24">

    {{-- Header --}}
    <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-600/20 via-orange-600/10 to-transparent"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6">
            <div class="flex items-center justify-between">
                <div>
                    <a href="/corporate/dashboard" class="inline-flex items-center text-amber-300 hover:text-white text-sm mb-3 transition-colors" data-spa>
                        <i class="fas fa-arrow-left mr-2"></i>
                        {{ __('muzibu::front.corporate.back_to_panel') }}
                    </a>
                    <h1 class="text-2xl font-bold text-white">{{ __('muzibu::front.corporate.spot_management') }}</h1>
                    <p class="text-gray-400 text-sm mt-1">{{ $account->company_name }}</p>
                </div>
                <button @click="settingsModal = true" class="p-3 bg-slate-800/80 hover:bg-slate-700 rounded-xl border border-white/10 transition-colors">
                    <i class="fas fa-cog text-gray-400"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Status Bar --}}
        <div class="flex items-center justify-between mb-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" :class="settings.spot_enabled ? 'bg-green-500' : 'bg-gray-500'"></span>
                <span class="text-gray-400" x-text="settings.spot_enabled ? 'Aktif' : 'Kapalı'"></span>
                <span class="text-gray-600">•</span>
                <span class="text-gray-500" x-text="'Her ' + settings.spot_songs_between + ' şarkıda'"></span>
            </div>
            <span class="text-gray-500" x-text="spots.length + ' spot'"></span>
        </div>

        {{-- Spot List --}}
        <div class="bg-slate-800/50 rounded-xl border border-white/10 overflow-hidden">

            {{-- Empty State --}}
            <template x-if="spots.length === 0">
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-slate-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bullhorn text-slate-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">{{ __('muzibu::front.corporate.no_spots') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('muzibu::front.corporate.no_spots_description') }}</p>
                </div>
            </template>

            {{-- List --}}
            <div class="divide-y divide-white/5">
                <template x-for="(spot, index) in spots" :key="spot.id">
                    <div class="flex items-center gap-3 p-4 hover:bg-white/5 transition-colors"
                         :class="[spot.is_archived && 'opacity-40', dropTargetIndex === index && 'bg-amber-500/10 border-t-2 border-amber-500']"
                         draggable="true"
                         @dragstart="dragStart($event, index)"
                         @dragover.prevent="dragOver(index)"
                         @drop.prevent="drop(index)"
                         @dragend="dragEnd()">

                        {{-- Position --}}
                        <span class="w-6 text-center text-xs text-gray-600 font-medium" x-text="index + 1"></span>

                        {{-- Play --}}
                        <button @click.stop="togglePlay(spot)"
                                class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0"
                                :class="currentPlaying === spot.id ? 'bg-amber-500' : 'bg-slate-700 hover:bg-slate-600'">
                            <i class="fas text-white text-sm" :class="currentPlaying === spot.id ? 'fa-pause' : (spot.audio_url ? 'fa-play' : 'fa-volume-mute')"></i>
                        </button>

                        {{-- Info (clickable) --}}
                        <div class="flex-1 min-w-0 cursor-pointer" @click="openEditModal(spot, index)">
                            <h3 class="text-white font-medium truncate" x-text="spot.title"></h3>
                            <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
                                <span x-text="formatDuration(spot.duration)"></span>
                                <span x-text="spot.play_count + ' çalma'"></span>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span x-show="spot.is_archived" class="text-xs text-amber-500 bg-amber-500/10 px-2 py-0.5 rounded">arşiv</span>
                            <span class="w-2 h-2 rounded-full" :class="spot.is_enabled ? 'bg-green-500' : 'bg-gray-600'"></span>
                        </div>

                        {{-- Drag Handle (right side) --}}
                        <div class="p-2 text-gray-600 hover:text-gray-400 cursor-grab active:cursor-grabbing flex-shrink-0">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Contact --}}
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>{{ __('muzibu::front.corporate.spot_contact_info') }}</p>
            @php
                $contactPhone = setting('contact_phone') ?: setting('site_phone');
                $contactWhatsapp = setting('whatsapp') ?: setting('contact_whatsapp');
                $contactEmail = setting('contact_email') ?: setting('site_email');
            @endphp
            <div class="flex items-center justify-center gap-4 mt-2">
                @if($contactPhone)
                <a href="tel:{{ $contactPhone }}" class="text-gray-400 hover:text-white"><i class="fas fa-phone mr-1"></i>{{ $contactPhone }}</a>
                @endif
                @if($contactWhatsapp)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank" class="text-green-500 hover:text-green-400"><i class="fab fa-whatsapp mr-1"></i>WhatsApp</a>
                @endif
                @if($contactEmail)
                <a href="mailto:{{ $contactEmail }}" class="text-gray-400 hover:text-white"><i class="fas fa-envelope mr-1"></i>{{ $contactEmail }}</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Settings Modal --}}
    <div x-show="settingsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="settingsModal = false"></div>
        <div class="relative bg-slate-800 rounded-2xl w-full max-w-sm border border-white/10 shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-white/10">
                <h3 class="text-lg font-semibold text-white">Spot Ayarları</h3>
                <button @click="settingsModal = false" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-5 space-y-5">
                {{-- System Toggle with Status --}}
                <div class="flex items-center justify-between p-4 rounded-xl" :class="settings.spot_enabled ? 'bg-green-500/10 border border-green-500/30' : 'bg-slate-700/50 border border-white/5'">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="settings.spot_enabled ? 'bg-green-500/20' : 'bg-slate-600'">
                            <i class="fas fa-bullhorn" :class="settings.spot_enabled ? 'text-green-400' : 'text-gray-500'"></i>
                        </div>
                        <div>
                            <h4 class="text-white font-medium">Spot Sistemi</h4>
                            <p class="text-xs" :class="settings.spot_enabled ? 'text-green-400' : 'text-gray-500'" x-text="settings.spot_enabled ? 'Aktif - Anonslar çalınıyor' : 'Pasif - Anonslar kapalı'"></p>
                        </div>
                    </div>
                    <button @click="toggleSystem()" class="w-14 h-7 rounded-full transition-colors" :class="settings.spot_enabled ? 'bg-green-500' : 'bg-slate-600'">
                        <span class="block w-5 h-5 bg-white rounded-full shadow transition-transform ml-1" :class="settings.spot_enabled && 'translate-x-7'"></span>
                    </button>
                </div>

                {{-- Interval Setting --}}
                <div class="flex items-center justify-between p-4 bg-slate-700/50 rounded-xl" x-show="settings.spot_enabled">
                    <div>
                        <h4 class="text-white font-medium">Çalma Aralığı</h4>
                        <p class="text-xs text-gray-500">Kaç şarkıda bir spot çalsın?</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center bg-slate-800 rounded-full border border-white/10">
                            <button type="button"
                                    @click="if(settings.spot_songs_between > 1) { settings.spot_songs_between--; updateSettings(); }"
                                    class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-full transition-colors">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span class="w-10 text-center text-white font-semibold tabular-nums" x-text="settings.spot_songs_between"></span>
                            <button type="button"
                                    @click="if(settings.spot_songs_between < 100) { settings.spot_songs_between++; updateSettings(); }"
                                    class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-full transition-colors">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                        <span class="text-gray-500 text-sm">şarkı</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="closeEditModal()"></div>
        <div class="relative bg-slate-800 rounded-2xl w-full max-w-md border border-white/10 shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-white/10">
                <h3 class="text-lg font-semibold text-white">Spot Düzenle</h3>
                <button @click="closeEditModal()" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-5 space-y-4">
                {{-- Başlık --}}
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Başlık</label>
                    <input type="text" x-model="editData.title" class="w-full bg-slate-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/20">
                </div>
                {{-- Tarih Aralığı --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1.5">Başlangıç</label>
                        <input type="datetime-local" x-model="editData.starts_at" class="w-full bg-slate-900 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/20 [color-scheme:dark]">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1.5">Bitiş</label>
                        <input type="datetime-local" x-model="editData.ends_at" class="w-full bg-slate-900 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/20 [color-scheme:dark]">
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-300">Aktif</span>
                    <button @click="editData.is_enabled = !editData.is_enabled" class="w-11 h-6 rounded-full transition-colors" :class="editData.is_enabled ? 'bg-green-500' : 'bg-slate-600'">
                        <span class="block w-4 h-4 bg-white rounded-full shadow transition-transform ml-1" :class="editData.is_enabled && 'translate-x-5'"></span>
                    </button>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-300">Arşivle</span>
                    <button @click="editData.is_archived = !editData.is_archived" class="w-11 h-6 rounded-full transition-colors" :class="editData.is_archived ? 'bg-amber-500' : 'bg-slate-600'">
                        <span class="block w-4 h-4 bg-white rounded-full shadow transition-transform ml-1" :class="editData.is_archived && 'translate-x-5'"></span>
                    </button>
                </div>
            </div>
            <div class="flex gap-3 p-5 border-t border-white/10">
                <button @click="closeEditModal()" class="flex-1 px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">İptal</button>
                <button @click="saveEdit()" class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors font-medium" :disabled="saving">
                    <span x-show="!saving">Kaydet</span>
                    <span x-show="saving"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    <audio x-ref="audioPlayer" @ended="currentPlaying = null" class="hidden"></audio>

    <script>
    function spotManager() {
        return {
            spots: [],
            settings: { spot_enabled: false, spot_songs_between: 10, songs_played: 0 },
            currentPlaying: null,
            settingsModal: false,
            editModal: false,
            editData: { id: null, title: '', starts_at: '', ends_at: '', is_enabled: true, is_archived: false },
            saving: false,
            draggedIndex: null,
            dropTargetIndex: null,

            init() {
                const el = document.getElementById('spots-data');
                if (el) {
                    try {
                        const data = JSON.parse(el.textContent);
                        this.spots = data.spots || [];
                        this.settings = { ...{ spot_enabled: false, spot_songs_between: 10, songs_played: 0 }, ...data.settings };
                    } catch(e) { console.error(e); }
                }
            },

            // Drag & Drop
            dragStart(event, index) {
                if (!event || !event.dataTransfer) return;
                this.draggedIndex = index;
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', index);
                event.target.style.opacity = '0.5';
            },

            dragOver(index) {
                if (this.draggedIndex !== null && this.draggedIndex !== index) {
                    this.dropTargetIndex = index;
                }
            },

            drop(dropIndex) {
                if (this.draggedIndex === null || this.draggedIndex === dropIndex) {
                    this.draggedIndex = null;
                    this.dropTargetIndex = null;
                    return;
                }

                const draggedSpot = this.spots[this.draggedIndex];
                const newSpots = [...this.spots];
                newSpots.splice(this.draggedIndex, 1);
                newSpots.splice(dropIndex, 0, draggedSpot);
                this.spots = newSpots;

                this.draggedIndex = null;
                this.dropTargetIndex = null;
                this.saveOrder();
            },

            dragEnd(event) {
                if (event && event.target) event.target.style.opacity = '1';
                this.draggedIndex = null;
                this.dropTargetIndex = null;
            },

            formatDuration(s) {
                if (!s) return '--:--';
                return String(Math.floor(s/60)).padStart(2,'0') + ':' + String(s%60).padStart(2,'0');
            },

            togglePlay(spot) {
                if (!spot.audio_url) return;
                const audio = this.$refs.audioPlayer;
                if (this.currentPlaying === spot.id) {
                    audio.pause();
                    this.currentPlaying = null;
                } else {
                    audio.src = spot.audio_url;
                    audio.play();
                    this.currentPlaying = spot.id;
                }
            },

            openEditModal(spot, index) {
                this.editData = {
                    id: spot.id,
                    title: spot.title,
                    starts_at: spot.starts_at || '',
                    ends_at: spot.ends_at || '',
                    is_enabled: spot.is_enabled,
                    is_archived: spot.is_archived
                };
                this.editModal = true;
            },

            closeEditModal() { this.editModal = false; },

            async saveEdit() {
                this.saving = true;
                try {
                    const res = await fetch('/corporate/spots/' + this.editData.id + '/update', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({
                            title: this.editData.title,
                            starts_at: this.editData.starts_at || null,
                            ends_at: this.editData.ends_at || null,
                            is_enabled: this.editData.is_enabled,
                            is_archived: this.editData.is_archived
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        const spot = this.spots.find(s => s.id === this.editData.id);
                        if (spot) Object.assign(spot, data.spot);
                        this.closeEditModal();
                    }
                } catch(e) { console.error(e); }
                this.saving = false;
            },

            async saveOrder() {
                const order = this.spots.map(s => s.id);
                try {
                    await fetch('/corporate/spots/reorder', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ order })
                    });
                } catch(e) { console.error(e); }
            },

            async toggleSystem() {
                try {
                    const res = await fetch('/corporate/spots/settings', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ spot_enabled: !this.settings.spot_enabled })
                    });
                    const data = await res.json();
                    if (data.success) this.settings.spot_enabled = data.settings.spot_enabled;
                } catch(e) { console.error(e); }
            },

            async updateSettings() {
                try {
                    const res = await fetch('/corporate/spots/settings', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ spot_songs_between: parseInt(this.settings.spot_songs_between) })
                    });
                    const data = await res.json();
                    if (data.success) this.settings.spot_songs_between = data.settings.spot_songs_between;
                } catch(e) { console.error(e); }
            },

            async updateSongsPlayed() {
                try {
                    await fetch('/corporate/spots/settings', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ songs_played: parseInt(this.settings.songs_played) })
                    });
                } catch(e) { console.error(e); }
            },

            async resetSongsPlayed() {
                this.settings.songs_played = 0;
                await this.updateSongsPlayed();
            }
        };
    }
    </script>
</div>
@endsection
