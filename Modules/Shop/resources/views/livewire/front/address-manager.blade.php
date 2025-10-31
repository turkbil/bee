<div class="address-manager">
    {{-- Seçili Adres Gösterimi --}}
    @if($selectedAddressId && $addresses->count() > 0)
        @php
            $selectedAddress = $addresses->firstWhere('address_id', $selectedAddressId);
        @endphp

        @if($selectedAddress)
            <div class="bg-white dark:bg-gray-800 rounded-lg border-2 border-blue-500 p-4 mb-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            @if($addressType === 'billing' ? $selectedAddress->is_default_billing : $selectedAddress->is_default_shipping)
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 text-xs font-semibold rounded">
                                    <i class="fa-solid fa-star"></i>
                                    Varsayılan
                                </span>
                            @endif
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $selectedAddress->title }}
                            </h4>
                        </div>

                        <p class="text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fa-solid fa-user text-gray-400 w-4"></i>
                            {{ $selectedAddress->full_name }}
                        </p>

                        @if($selectedAddress->phone)
                            <p class="text-gray-700 dark:text-gray-300 mb-1">
                                <i class="fa-solid fa-phone text-gray-400 w-4"></i>
                                {{ $selectedAddress->phone }}
                            </p>
                        @endif

                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            <i class="fa-solid fa-location-dot text-gray-400 w-4"></i>
                            {{ $selectedAddress->full_address }}
                        </p>
                    </div>

                    <button
                        wire:click="openSelectModal"
                        class="ml-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition-colors">
                        <i class="fa-solid fa-pen-to-square mr-1"></i>
                        Değiştir
                    </button>
                </div>
            </div>
        @endif
    @else
        {{-- Adres Yok - Yeni Ekle --}}
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-300 dark:border-yellow-600 rounded-lg p-6 text-center">
            <i class="fa-solid fa-map-location-dot text-4xl text-yellow-600 dark:text-yellow-400 mb-3"></i>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                Kayıtlı {{ $addressType === 'billing' ? 'fatura' : 'teslimat' }} adresiniz yok
            </p>
            <button
                wire:click="openEditModal"
                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all">
                <i class="fa-solid fa-plus"></i>
                Yeni Adres Ekle
            </button>
        </div>
    @endif

    {{-- Adres Seçim Modalı --}}
    @if($showSelectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showSelectModal', false)"></div>

                {{-- Modal --}}
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    {{-- Header --}}
                    <div class="bg-blue-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white">
                                <i class="fa-solid fa-map-location-dot mr-2"></i>
                                {{ $addressType === 'billing' ? 'Fatura' : 'Teslimat' }} Adresi Seçin
                            </h3>
                            <button wire:click="$set('showSelectModal', false)" class="text-white hover:text-gray-200">
                                <i class="fa-solid fa-times text-2xl"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-4 max-h-96 overflow-y-auto">
                        @if($addresses->count() > 0)
                            <div class="space-y-3">
                                @foreach($addresses as $address)
                                    <div class="border-2 @if($selectedAddressId == $address->address_id) border-blue-500 bg-blue-50 dark:bg-blue-900/20 @else border-gray-200 dark:border-gray-700 @endif rounded-lg p-4 hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    @if(($addressType === 'billing' && $address->is_default_billing) || ($addressType === 'shipping' && $address->is_default_shipping))
                                                        <i class="fa-solid fa-star text-yellow-500"></i>
                                                    @endif
                                                    <h4 class="font-bold text-gray-900 dark:text-white">
                                                        {{ $address->title }}
                                                    </h4>
                                                </div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $address->full_name }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $address->full_address }}
                                                </p>
                                            </div>

                                            <div class="flex flex-col gap-2 ml-4">
                                                <button
                                                    wire:click="selectAddress({{ $address->address_id }})"
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition-colors">
                                                    Seç
                                                </button>
                                                <button
                                                    wire:click="openEditModal({{ $address->address_id }})"
                                                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded transition-colors">
                                                    Düzenle
                                                </button>
                                                <button
                                                    wire:click="deleteAddress({{ $address->address_id }})"
                                                    wire:confirm="Bu adresi silmek istediğinize emin misiniz?"
                                                    class="px-4 py-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 text-sm rounded transition-colors">
                                                    Sil
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                Henüz kayıtlı adresiniz yok
                            </p>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex justify-between">
                        <button
                            wire:click="openEditModal"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Yeni Adres Ekle
                        </button>
                        <button
                            wire:click="$set('showSelectModal', false)"
                            class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition-colors">
                            Kapat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Adres Ekle/Düzenle Modalı --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showEditModal', false)"></div>

                {{-- Modal --}}
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    {{-- Header --}}
                    <div class="bg-green-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white">
                                <i class="fa-solid fa-plus-circle mr-2"></i>
                                {{ $editingAddressId ? 'Adresi Düzenle' : 'Yeni Adres Ekle' }}
                            </h3>
                            <button wire:click="$set('showEditModal', false)" class="text-white hover:text-gray-200">
                                <i class="fa-solid fa-times text-2xl"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Form - Minimal Hepsiburada Style --}}
                    <form wire:submit.prevent="saveAddress" class="px-6 py-4">
                        <div class="space-y-3">
                            {{-- Adres Adı --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Bu adrese bir ad verin <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="title" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="Örnek: Evim, İş yerim" required>
                                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Telefon (Opsiyonel) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Telefon <span class="text-gray-400 text-xs">(Opsiyonel - Teslimat için farklı telefon)</span>
                                </label>
                                <input wire:model="phone" type="tel"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="05XX XXX XX XX">
                            </div>

                            {{-- Adres --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Adres <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="address_line_1" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="Bina, site, iş yeri, kurum ismi vb." required></textarea>
                                @error('address_line_1') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- İl İlçe --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        İl <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="city"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                        required>
                                        <option value="">İl Seçin</option>
                                        @foreach($cities as $cityId => $cityName)
                                            <option value="{{ $cityName }}">{{ $cityName }}</option>
                                        @endforeach
                                    </select>
                                    @error('city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        İlçe <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="district"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                        required @if(empty($districts)) disabled @endif>
                                        <option value="">{{ empty($districts) ? 'Önce il seçin' : 'İlçe Seçin' }}</option>
                                        @foreach($districts as $districtId => $districtName)
                                            <option value="{{ $districtName }}">{{ $districtName }}</option>
                                        @endforeach
                                    </select>
                                    @error('district') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Posta Kodu + Teslimat Notu --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Posta Kodu <span class="text-gray-400 text-xs">(Opsiyonel)</span>
                                    </label>
                                    <input wire:model="postal_code" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                        placeholder="34000">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Teslimat Notu <span class="text-gray-400 text-xs">(Opsiyonel)</span>
                                    </label>
                                    <input wire:model="delivery_notes" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                        placeholder="Kapıcıya bırakabilirsiniz">
                                </div>
                            </div>

                            {{-- Varsayılan --}}
                            <div class="flex items-center pt-2">
                                <input wire:model="is_default" type="checkbox" id="is_default"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="is_default" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Varsayılan {{ $addressType === 'billing' ? 'fatura' : 'teslimat' }} adresi olarak kaydet
                                </label>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-3 mt-4 flex justify-end gap-2 -mx-6 -mb-4">
                            <button type="button" wire:click="$set('showEditModal', false)"
                                class="px-5 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition-colors text-sm">
                                İptal
                            </button>
                            <button type="submit"
                                class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors text-sm">
                                <i class="fa-solid fa-save mr-1"></i> Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
