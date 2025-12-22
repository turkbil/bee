{{-- Device Selection Modal - KAPATILMAZ (Cihaz seçmeden devam edemez) --}}
<div x-show="showDeviceSelectionModal"
     x-cloak
     x-data="{
         deviceTerminateLoading: false,
         selectedDeviceIds: [],
         activeDevices: window.activeDevices || [],
         deviceLimit: window.deviceLimit || 2
     }"
     x-init="$watch('showDeviceSelectionModal', val => { if(val) { activeDevices = window.activeDevices || []; deviceLimit = window.deviceLimit || 2; selectedDeviceIds = []; } })"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 backdrop-blur-sm">

    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl shadow-2xl max-w-2xl w-full mx-4 border border-red-500/50">

        {{-- Header --}}
        <div class="bg-red-500/10 border-b border-red-500/30 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Cihaz Limiti Aşıldı</h3>
                    <p class="text-sm text-red-400/80">
                        Devam etmek için bazı cihazlardan çıkış yapmalısınız
                    </p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
            {{-- Info --}}
            <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700">
                <p class="text-slate-200 text-sm leading-relaxed">
                    <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                    Ayni anda maksimum <strong class="text-white" x-text="deviceLimit"></strong> cihazdan giris yapabilirsiniz.
                    <span class="block mt-2 text-slate-300">
                        Toplam <strong class="text-white" x-text="activeDevices.length"></strong> aktif cihaz bulundu.
                    </span>
                </p>
            </div>

            {{-- Device List --}}
            <div class="space-y-3">
                <template x-for="device in activeDevices" :key="device.id">
                    <label class="block bg-slate-800/50 rounded-lg border-2 transition-all duration-200"
                           :class="{
                               'border-blue-500 bg-blue-500/10': selectedDeviceIds.includes(device.session_id),
                               'border-slate-700 hover:border-slate-600': !selectedDeviceIds.includes(device.session_id),
                               'opacity-50 cursor-not-allowed': device.is_current,
                               'cursor-pointer': !device.is_current
                           }">

                        <div class="p-4 flex items-start gap-4">
                            {{-- Checkbox (Çoklu seçim) --}}
                            <input type="checkbox"
                                   :value="device.session_id"
                                   x-model="selectedDeviceIds"
                                   class="mt-1 w-5 h-5 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900 rounded"
                                   :disabled="device.is_current">

                            {{-- Device Icon --}}
                            <div class="w-12 h-12 bg-slate-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="text-2xl"
                                   :class="{
                                       'fas fa-desktop text-blue-400': device.device_type === 'desktop',
                                       'fas fa-mobile-alt text-green-400': device.device_type === 'mobile',
                                       'fas fa-tablet-alt text-purple-400': device.device_type === 'tablet'
                                   }"></i>
                            </div>

                            {{-- Device Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-white truncate" x-text="device.device_name"></h4>
                                    <span x-show="device.is_current"
                                          class="px-2 py-0.5 bg-green-600 text-white text-xs rounded-full">
                                        Mevcut Cihaz
                                    </span>
                                </div>

                                <div class="space-y-1 text-sm text-slate-400">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-globe w-4"></i>
                                        <span x-text="device.browser + ' - ' + device.platform"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-network-wired w-4"></i>
                                        <span x-text="device.ip_address"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock w-4"></i>
                                        <span x-text="device.last_activity_human"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                </template>

                {{-- Empty State --}}
                <div x-show="activeDevices.length === 0"
                     class="text-center py-8 text-slate-400">
                    <i class="fas fa-mobile-alt text-4xl mb-3 opacity-50"></i>
                    <p>Aktif cihaz bulunamadi</p>
                </div>
            </div>
        </div>

        {{-- Footer / Actions --}}
        <div class="px-6 pb-6 space-y-3">
            {{-- Seçilenleri Çıkar Button --}}
            <button @click="terminateSelectedDevices()"
                    :disabled="selectedDeviceIds.length === 0 || deviceTerminateLoading"
                    class="w-full px-6 py-3 bg-red-600 hover:bg-red-500 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-red-500/30 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-sign-out-alt mr-2"
                   :class="deviceTerminateLoading && 'fa-spin fa-spinner'"></i>
                <span x-text="deviceTerminateLoading ? 'İşleniyor...' : 'Seçilenleri Çıkar (' + selectedDeviceIds.length + ')'"></span>
            </button>

            {{-- Tümünü Çıkar Button --}}
            <button @click="terminateAllDevices()"
                    :disabled="deviceTerminateLoading || activeDevices.filter(d => !d.is_current).length === 0"
                    class="w-full px-6 py-3 bg-orange-600 hover:bg-orange-500 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-orange-500/30 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-users-slash mr-2"
                   :class="deviceTerminateLoading && 'fa-spin fa-spinner'"></i>
                <span x-text="deviceTerminateLoading ? 'İşleniyor...' : 'Diğer Tüm Cihazları Çıkar'"></span>
                <span class="text-sm opacity-80 ml-2" x-show="!deviceTerminateLoading">
                    (<span x-text="activeDevices.filter(d => !d.is_current).length"></span> cihaz)
                </span>
            </button>

            {{-- Ayırıcı --}}
            <div class="relative py-2">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-700"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="px-3 bg-slate-800 text-slate-500 text-sm">veya</span>
                </div>
            </div>

            {{-- Bu Cihazdan Çıkış Yap Button --}}
            <button @click="logoutFromThisDevice()"
                    :disabled="deviceTerminateLoading"
                    class="w-full px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-semibold transition-all duration-200 border border-slate-600 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-power-off mr-2"></i>
                Bu Cihazdan Çıkış Yap
            </button>

            {{-- Bilgi Notu --}}
            <p class="text-center text-slate-500 text-xs">
                <i class="fas fa-info-circle mr-1"></i>
                Diğer cihazı çıkarırsanız bu cihazda kalmaya devam edersiniz
            </p>
        </div>
    </div>
</div>
