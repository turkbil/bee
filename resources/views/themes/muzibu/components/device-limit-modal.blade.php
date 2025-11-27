{{-- 
    Device Limit Modal - Sadece Tenant 1001 (Muzibu) için 
    
    ⚠️ ŞU ANDA DEVRE DIŞI ⚠️
    Backend handlePostLoginDeviceLimit() otomatik eski sessionları temizliyor
    
    Açmak için: layouts/app.blade.php'de include satırını uncomment et
--}}
@if(tenant() && tenant()->id == 1001)
<!-- External CSS -->
<link rel="stylesheet" href="{{ asset('themes/muzibu/css/components/device-limit-modal.css') }}?v={{ time() }}">

<div x-data="deviceLimitModal()">
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
        class="device-modal-backdrop"
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
        class="device-modal-container"
        @click.away="open = false"
    >
        <div class="device-modal-content" @click.stop>
            <!-- Header -->
            <div class="device-modal-header">
                <div class="flex items-start gap-4 mb-4">
                    <div class="device-modal-icon">
                        <i class="fas fa-devices text-2xl text-green-500"></i>
                    </div>
                    <div>
                        <h3 class="device-modal-title">Cihaz Limiti Aşıldı</h3>
                        <p class="device-modal-subtitle">Giriş yapabilmek için aktif bir cihazdan çıkış yapın</p>
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
            <div class="device-list-container">
                <template x-for="device in devices" :key="device.id">
                    <div class="device-item">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4 flex-1">
                                <div class="w-12 h-12 bg-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-2xl" x-text="getDeviceIcon(device.device_type)"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-semibold text-white" x-text="device.device_name"></h4>
                                        <span x-show="device.is_current" class="px-2 py-0.5 bg-green-500/20 text-green-500 text-xs rounded-full font-medium">Mevcut</span>
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
            <div class="device-modal-footer">
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

<!-- External JavaScript -->
<script src="{{ asset('themes/muzibu/js/components/device-limit-modal.js') }}?v={{ time() }}"></script>
@endif
