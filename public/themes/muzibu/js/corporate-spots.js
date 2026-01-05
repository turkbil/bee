/**
 * Corporate Spots Manager (SPA Compatible)
 * Alpine.js component for managing corporate announcements
 */

// 🛡️ GUARD: Prevent redeclaration on SPA navigation
if (typeof window.spotManagerRegistered === 'undefined') {
    window.spotManagerRegistered = true;

    // Register Alpine.js component
    document.addEventListener('alpine:init', () => {
        if (!Alpine.data('spotManager')) {
            Alpine.data('spotManager', () => ({
                spots: [],
                settings: { spot_enabled: false, spot_is_paused: false, spot_songs_between: 10, songs_played: 0 },
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
                            this.settings = { ...{ spot_enabled: false, spot_is_paused: false, spot_songs_between: 10, songs_played: 0 }, ...data.settings };
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
                    // ✅ OPTIMISTIC UPDATE: ANINDA göster, API arka planda
                    const previousState = this.settings.spot_enabled;
                    this.settings.spot_enabled = !previousState;

                    try {
                        const res = await fetch('/corporate/spots/settings', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ spot_enabled: this.settings.spot_enabled })
                        });
                        const data = await res.json();

                        // API başarısızsa geri al
                        if (!data.success) {
                            this.settings.spot_enabled = previousState;
                        }
                    } catch(e) {
                        console.error(e);
                        // Hata olursa geri al
                        this.settings.spot_enabled = previousState;
                    }
                },

                async togglePause() {
                    try {
                        const res = await fetch('/api/spot/toggle-pause', {
                            method: 'POST',
                            credentials: 'include',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.settings.spot_is_paused = data.spot_is_paused;
                        }
                    } catch(e) { console.error(e); }
                },

                async updateSettings() {
                    // ✅ Blade'de zaten optimistic (buton state'i önce değiştiriyor)
                    const previousValue = parseInt(this.settings.spot_songs_between);

                    try {
                        const res = await fetch('/corporate/spots/settings', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ spot_songs_between: previousValue })
                        });
                        const data = await res.json();

                        // API başarısızsa geri al
                        if (!data.success) {
                            this.settings.spot_songs_between = previousValue;
                        }
                    } catch(e) {
                        console.error(e);
                        // Hata olursa geri al
                        this.settings.spot_songs_between = previousValue;
                    }
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
            }));
        }
    });
}
