{{-- PayTR Taksit Tablosu Component --}}
{{-- Kullanım: <x-paytr-installment-table :amount="$cartTotal" /> --}}

@props(['amount' => 0])

@php
    // PayTR ödeme yöntemini al (gerçek taksit oranları için)
    $paytrMethod = \Modules\Payment\App\Models\PaymentMethod::where('gateway', 'paytr')
        ->where('is_active', true)
        ->first();

    // PayTR'den gelen GERÇEK oranları kullan (database'den)
    $installmentRates = $paytrMethod->installment_options ?? [];
    $maxInstallments = $paytrMethod->max_installments ?? 12;

    // Banka logoları (PayTR standart banka listesi)
    $banks = [
        [
            'name' => 'Akbank',
            'logo' => 'https://www.paytr.com/img/banks/akbank.png',
        ],
        [
            'name' => 'Garanti BBVA',
            'logo' => 'https://www.paytr.com/img/banks/garanti.png',
        ],
        [
            'name' => 'İş Bankası',
            'logo' => 'https://www.paytr.com/img/banks/isbank.png',
        ],
        [
            'name' => 'Yapı Kredi',
            'logo' => 'https://www.paytr.com/img/banks/yapikredi.png',
        ],
        [
            'name' => 'Ziraat Bankası',
            'logo' => 'https://www.paytr.com/img/banks/ziraat.png',
        ],
        [
            'name' => 'QNB Finansbank',
            'logo' => 'https://www.paytr.com/img/banks/finansbank.png',
        ],
    ];

    // Her banka için taksit seçenekleri oluştur
    foreach ($banks as &$bank) {
        $bank['installments'] = [];

        // Peşin (1 taksit)
        $bank['installments'][] = [
            'count' => 1,
            'rate' => 0,
            'commission' => 0
        ];

        // Gerçek taksit oranları
        foreach ($installmentRates as $count => $rate) {
            $bank['installments'][] = [
                'count' => (int) $count,
                'rate' => (float) $rate,
                'commission' => (float) $rate
            ];
        }
    }
@endphp

<div x-data="{
    productPrice: {{ $amount }},
    banks: @json($banks),
    calculateTotal(installment) {
        return this.productPrice * (1 + (installment.rate / 100));
    },
    calculateMonthly(installment) {
        return this.calculateTotal(installment) / installment.count;
    }
}">
    <!-- Ürün Fiyatı -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm mb-1">Sepet Toplamı</p>
                <p class="text-4xl font-bold" x-text="'₺' + productPrice.toFixed(2)"></p>
            </div>
            <div class="text-right">
                <p class="text-blue-100 text-sm mb-1">Peşin İndirim</p>
                <p class="text-2xl font-bold">%2</p>
                <p class="text-sm text-blue-100" x-text="'₺' + (productPrice * 0.98).toFixed(2)"></p>
            </div>
        </div>
    </div>

    <!-- Açıklama -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
        <div class="flex items-start space-x-3">
            <i class="fa-solid fa-info-circle text-blue-600 text-xl mt-0.5"></i>
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <strong>Taksit seçenekleri</strong> bankalara göre değişiklik gösterebilir. Aşağıdaki tabloda tüm bankaların taksit oranlarını görebilirsiniz.
                </p>
            </div>
        </div>
    </div>

    @if (!$paytrMethod || empty($installmentRates))
    <!-- HATA: PayTR oranları yüklenmemiş -->
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
        <div class="flex items-start space-x-3">
            <i class="fa-solid fa-circle-exclamation text-red-600 text-xl mt-0.5"></i>
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <strong>Hata:</strong> PayTR taksit oranları yüklenmemiş! Taksit tablosu gösterilemiyor.
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                    <strong>Çözüm:</strong> Admin panelden Payment Settings → PayTR → "Taksit Oranlarını Güncelle" butonuna tıklayın.
                </p>
            </div>
        </div>
    </div>
    @else
    {{-- Gerçek oranlar yüklü, bilgilendirme mesajı --}}
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
        <div class="flex items-start space-x-3">
            <i class="fa-solid fa-circle-check text-green-600 text-xl mt-0.5"></i>
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <strong>PayTR Gerçek Oranlar:</strong> Aşağıdaki taksit oranları PayTR'den güncellenmiştir.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Banka Kartları Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="bank in banks" :key="bank.name">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Banka Header -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 p-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-center h-10">
                        <img :src="bank.logo" :alt="bank.name + ' logo'" class="max-h-8 object-contain">
                    </div>
                </div>

                <!-- Taksit Seçenekleri -->
                <div class="p-4">
                    <template x-for="installment in bank.installments" :key="installment.count">
                        <div class="mb-2 last:mb-0">
                            <div class="bg-gray-50 dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg p-3 transition-colors cursor-pointer group">
                                <div class="flex items-center justify-between">
                                    <!-- Taksit Sayısı -->
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm" x-text="installment.count"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="installment.count === 1 ? 'Peşin' : installment.count + ' Taksit'"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-show="installment.rate > 0" x-text="'%' + installment.rate + ' komisyon'"></p>
                                            <p class="text-xs text-green-600 dark:text-green-400 font-semibold" x-show="installment.rate === 0">Komisyonsuz</p>
                                        </div>
                                    </div>

                                    <!-- Tutar -->
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="'₺' + calculateMonthly(installment).toFixed(2)"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-show="installment.count > 1">aylık</p>
                                    </div>
                                </div>

                                <!-- Toplam Tutar (komisyonlu taksitlerde) -->
                                <div x-show="installment.rate > 0" class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-600 dark:text-gray-400">Toplam:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white" x-text="'₺' + calculateTotal(installment).toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Banka Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-t border-gray-200 dark:border-gray-600">
                    <p class="text-xs text-gray-600 dark:text-gray-400 text-center">
                        <i class="fa-solid fa-shield-halved text-green-600 mr-1"></i>
                        Güvenli Ödeme
                    </p>
                </div>
            </div>
        </template>
    </div>

    <!-- Alt Bilgilendirme -->
    <div class="mt-8 grid md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 text-center">
            <i class="fa-solid fa-lock text-green-600 text-2xl mb-2"></i>
            <p class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Güvenli Ödeme</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">256-bit SSL şifreleme</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 text-center">
            <i class="fa-solid fa-credit-card text-blue-600 text-2xl mb-2"></i>
            <p class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Tüm Kartlar</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Visa, Mastercard, Troy</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 text-center">
            <i class="fa-solid fa-headset text-purple-600 text-2xl mb-2"></i>
            <p class="text-sm font-semibold text-gray-900 dark:text-white mb-1">7/24 Destek</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Müşteri hizmetleri</p>
        </div>
    </div>

    <!-- PayTR Logo -->
    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Güvenli ödeme altyapısı</p>
        <img src="https://www.paytr.com/img/logo.png" alt="PayTR" class="h-8 mx-auto opacity-70">
    </div>
</div>
