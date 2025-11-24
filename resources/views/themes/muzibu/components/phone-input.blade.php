{{--
    Universal Phone Input Component for Muzibu Theme
    Usage: @include('themes.muzibu.components.phone-input', ['name' => 'contact_phone', 'label' => 'Telefon', 'required' => true, 'wire' => true])
--}}

@php
    $name = $name ?? 'phone';
    $label = $label ?? 'Telefon';
    $required = $required ?? false;
    $wire = $wire ?? false; // Livewire binding mi Alpine mi?
    $alpineModel = $alpineModel ?? null; // Alpine parent model (e.g., 'contactPhone')
    $value = $value ?? '';
    $error = $error ?? null;
@endphp

<div x-data="{
    phoneCountry: {
        code: '+90',
        flag: '🇹🇷',
        name: 'Türkiye',
        placeholder: '5__ ___ __ __',
        format: 'XXX XXX XX XX'
    },
    phoneCountries: [
        { code: '+90', flag: '🇹🇷', name: 'Türkiye', placeholder: '5__ ___ __ __', format: 'XXX XXX XX XX' },
        { code: '+1', flag: '🇺🇸', name: 'Amerika', placeholder: '(___) ___-____', format: '(XXX) XXX-XXXX' },
        { code: '+44', flag: '🇬🇧', name: 'İngiltere', placeholder: '____ ______', format: 'XXXX XXXXXX' },
        { code: '+49', flag: '🇩🇪', name: 'Almanya', placeholder: '___ ________', format: 'XXX XXXXXXXX' },
        { code: '+33', flag: '🇫🇷', name: 'Fransa', placeholder: '__ __ __ __ __', format: 'XX XX XX XX XX' },
        { code: '+39', flag: '🇮🇹', name: 'İtalya', placeholder: '___ ___ ____', format: 'XXX XXX XXXX' },
        { code: '+34', flag: '🇪🇸', name: 'İspanya', placeholder: '___ __ __ __', format: 'XXX XX XX XX' },
        { code: '+31', flag: '🇳🇱', name: 'Hollanda', placeholder: '__ ________', format: 'XX XXXXXXXX' },
        { code: '+32', flag: '🇧🇪', name: 'Belçika', placeholder: '___ __ __ __', format: 'XXX XX XX XX' },
        { code: '+41', flag: '🇨🇭', name: 'İsviçre', placeholder: '__ ___ __ __', format: 'XX XXX XX XX' },
        { code: '+43', flag: '🇦🇹', name: 'Avusturya', placeholder: '___ _______', format: 'XXX XXXXXXX' },
        { code: '+45', flag: '🇩🇰', name: 'Danimarka', placeholder: '__ __ __ __', format: 'XX XX XX XX' },
        { code: '+46', flag: '🇸🇪', name: 'İsveç', placeholder: '__ ___ __ __', format: 'XX XXX XX XX' },
        { code: '+47', flag: '🇳🇴', name: 'Norveç', placeholder: '___ __ ___', format: 'XXX XX XXX' },
        { code: '+358', flag: '🇫🇮', name: 'Finlandiya', placeholder: '__ ___ ____', format: 'XX XXX XXXX' },
        { code: '+30', flag: '🇬🇷', name: 'Yunanistan', placeholder: '___ ___ ____', format: 'XXX XXX XXXX' },
        { code: '+7', flag: '🇷🇺', name: 'Rusya', placeholder: '(___)___-__-__', format: '(XXX)XXX-XX-XX' }
    ],
    phoneValue: '{{ $value }}',
    phoneValid: false,
    phoneError: '',
    countryOpen: false,

    selectCountry(country) {
        this.phoneCountry = country;
        this.phoneValue = '';
        this.phoneValid = false;
        this.countryOpen = false;
        @if($wire)
        $wire.set('{{ $name }}', '');
        @endif
    },

    formatPhoneNumber() {
        let phone = this.phoneValue.replace(/\D/g, '');

        // Turkey specific formatting
        if (this.phoneCountry.code === '+90') {
            if (phone.length > 0) {
                if (phone.length <= 3) {
                    this.phoneValue = phone;
                } else if (phone.length <= 6) {
                    this.phoneValue = phone.substring(0, 3) + ' ' + phone.substring(3);
                } else if (phone.length <= 8) {
                    this.phoneValue = phone.substring(0, 3) + ' ' + phone.substring(3, 6) + ' ' + phone.substring(6);
                } else {
                    this.phoneValue = phone.substring(0, 3) + ' ' + phone.substring(3, 6) + ' ' + phone.substring(6, 8) + ' ' + phone.substring(8, 10);
                    phone = phone.substring(0, 10);
                }
            }

            // Validate Turkey phone
            if (phone.length === 0) {
                this.phoneValid = false;
                this.phoneError = 'Telefon numarası gereklidir';
            } else if (!phone.startsWith('5')) {
                this.phoneValid = false;
                this.phoneError = 'Cep telefonu 5 ile başlamalıdır';
            } else if (phone.length !== 10) {
                this.phoneValid = false;
                this.phoneError = 'Telefon numarası 10 haneli olmalıdır';
            } else if (!['50', '51', '52', '53', '54', '55', '56', '58', '59'].includes(phone.substring(0, 2))) {
                this.phoneValid = false;
                this.phoneError = 'Geçersiz operatör kodu';
            } else {
                this.phoneValid = true;
                this.phoneError = '';
            }
        } else {
            // Generic international validation
            this.phoneValue = phone;

            if (phone.length === 0) {
                this.phoneValid = false;
                this.phoneError = 'Telefon numarası gereklidir';
            } else if (phone.length < 7) {
                this.phoneValid = false;
                this.phoneError = 'Telefon numarası çok kısa';
            } else if (phone.length > 15) {
                this.phoneValid = false;
                this.phoneError = 'Telefon numarası çok uzun';
            } else {
                this.phoneValid = true;
                this.phoneError = '';
            }
        }

        @if($wire)
        // Livewire sync
        $wire.set('{{ $name }}', this.phoneValue);
        @elseif($alpineModel)
        // Alpine parent sync
        $parent.{{ $alpineModel }} = this.phoneValue;
        @endif
    }
}">
    <div>
        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>

        <div class="relative">
            <div class="flex gap-2">
                {{-- Country Code Selector --}}
                <div class="relative">
                    <button type="button"
                            @click="countryOpen = !countryOpen"
                            class="px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-all flex items-center gap-2">
                        <span x-text="phoneCountry.flag" class="text-lg">🇹🇷</span>
                        <span x-text="phoneCountry.code" class="text-sm font-medium">+90</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </button>

                    {{-- Country Dropdown --}}
                    <div x-show="countryOpen"
                         @click.away="countryOpen = false"
                         x-transition
                         class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-gray-700 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-600 overflow-hidden z-50 max-h-64 overflow-y-auto">
                        <template x-for="country in phoneCountries" :key="country.code">
                            <button type="button"
                                    @click="selectCountry(country)"
                                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 transition-all text-left">
                                <span x-text="country.flag" class="text-lg">🏳️</span>
                                <div class="flex-1">
                                    <div x-text="country.name" class="text-sm font-medium text-gray-900 dark:text-white"></div>
                                    <div x-text="country.code" class="text-xs text-gray-500 dark:text-gray-400"></div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Phone Number Input --}}
                <input type="tel"
                       x-model="phoneValue"
                       @input="formatPhoneNumber()"
                       :placeholder="phoneCountry.placeholder"
                       maxlength="20"
                       class="flex-1 px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm focus:border-gray-500 transition-all">

                {{-- Hidden input for traditional form submission --}}
                @if(!$wire && !$alpineModel)
                <input type="hidden" name="{{ $name }}" :value="phoneValue">
                @endif
            </div>

            {{-- Validation message with fixed height --}}
            <div class="h-5 mt-1">
                @if($error)
                    <div class="flex items-center gap-1 text-red-500 text-xs">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $error }}</span>
                    </div>
                @else
                    <div x-show="!phoneValid && phoneValue.length > 0" class="flex items-center gap-1 text-red-500 text-xs">
                        <i class="fas fa-exclamation-circle"></i>
                        <span x-text="phoneError"></span>
                    </div>
                    <div x-show="phoneValid" class="flex items-center gap-1 text-green-500 text-xs">
                        <i class="fas fa-check-circle"></i>
                        <span>Geçerli telefon numarası</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
