{{-- Device Limit Modal - Sadece Tenant 1001 (Muzibu) için --}}
@if(tenant() && tenant()->id == 1001)
<div
    x-data="{
        open: false,
        devices: [],
        loading: false,
        errorMessage: '',

        async checkDeviceLimit() {
            try {
                const response = await fetch('/api/devices/check', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.limit_exceeded) {
                    this.devices = data.active_devices;
                    this.open = true;
                }
            } catch (error) {
                console.error('Device check error:', error);
            }
        },

        async terminateDevice(sessionId) {
            if (!confirm('Bu cihazdan çıkış yapmak istediğinizden emin misiniz?')) {
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                const response = await fetch(`/api/devices/${sessionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Cihazı listeden kaldır
                    this.devices = this.devices.filter(d => d.id !== sessionId);

                    // Eğer tüm cihazlar kapatıldıysa modal'ı kapat ve login formunu submit et
                    if (this.devices.length === 0) {
                        this.open = false;
                        document.querySelector('form[action=\"{{ route('login') }}\"]').submit();
                    }
                } else {
                    this.errorMessage = data.message || 'Bir hata oluştu';
                }
            } catch (error) {
                this.errorMessage = 'Bağlantı hatası oluştu';
            } finally {
                this.loading = false;
            }
        },

        async terminateAllDevices() {
            if (!confirm('Tüm cihazlardan çıkış yapmak istediğinizden emin misiniz?')) {
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                const response = await fetch('/api/devices/all', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.open = false;
                    document.querySelector('form[action=\"{{ route('login') }}\"]').submit();
                } else {
                    this.errorMessage = data.message || 'Bir hata oluştu';
                }
            } catch (error) {
                this.errorMessage = 'Bağlantı hatası oluştu';
            } finally {
                this.loading = false;
            }
        },

        getDeviceIcon(deviceType) {
            const icons = {
                'mobile': '📱',
                'tablet': '💻',
                'desktop': '🖥️'
            };
            return icons[deviceType] || '🖥️';
        }
    }"
    x-init="
        // Login form submit edildiğinde device limit kontrol et
        document.querySelector('form[action=\"{{ route('login') }}\"]')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            await checkDeviceLimit();

            // Eğer modal açılmadıysa (limit aşılmadıysa) formu submit et
            if (!open) {
                e.target.submit();
            }
        });
    "
>
    <!-- Modal Backdrop -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50"
        @click="open = false"
    ></div>

    <!-- Modal Content -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.away="open = false"
    >
        <div class="bg-spotify-dark border border-white/10 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden" @click.stop>
            <!-- Header -->
            <div class="px-8 pt-8 pb-4">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-14 h-14 bg-spotify-green/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-devices text-2xl text-spotify-green"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-1">Cihaz Limiti Aşıldı</h3>
                        <p class="text-gray-400 text-sm">Giriş yapabilmek için aktif bir cihazdan çıkış yapın</p>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div x-show="errorMessage" class="mx-6 mt-4">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
                    <span x-text="errorMessage"></span>
                </div>
            </div>

            <!-- Device List -->
            <div class="px-8 py-4 max-h-96 overflow-y-auto">
                <template x-for="device in devices" :key="device.id">
                    <div class="bg-spotify-gray/50 border border-white/5 rounded-xl p-5 mb-3 hover:border-spotify-green/50 hover:bg-spotify-gray/70 transition-all">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4 flex-1">
                                <div class="w-12 h-12 bg-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-2xl" x-text="getDeviceIcon(device.device_type)"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-semibold text-white" x-text="device.device_name"></h4>
                                        <span x-show="device.is_current" class="px-2 py-0.5 bg-spotify-green/20 text-spotify-green text-xs rounded-full font-medium">Mevcut</span>
                                    </div>
                                    <div class="flex flex-wrap gap-3 text-xs text-gray-400">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-globe w-3"></i>
                                            <span x-text="device.ip_address"></span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-clock w-3"></i>
                                            <span x-text="device.last_activity_human"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button
                                @click="terminateDevice(device.id)"
                                :disabled="loading || device.is_current"
                                class="ml-4 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-full transition-all disabled:opacity-30 disabled:cursor-not-allowed"
                            >
                                <span x-show="!loading">Çıkış Yap</span>
                                <span x-show="loading" class="inline-flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <span>İşleniyor</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="px-8 py-6 bg-spotify-black/50 border-t border-white/5">
                <div class="flex items-center justify-between gap-3">
                    <button
                        @click="terminateAllDevices()"
                        :disabled="loading"
                        class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-medium rounded-full transition-all disabled:opacity-30 disabled:cursor-not-allowed"
                    >
                        <span x-show="!loading">Tüm Cihazları Kapat</span>
                        <span x-show="loading" class="inline-flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>İşleniyor</span>
                        </span>
                    </button>
                    <button
                        @click="open = false"
                        class="px-6 py-3 bg-white/5 hover:bg-white/10 text-gray-300 font-medium rounded-full transition-all"
                    >
                        İptal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endif
