<?php

use Illuminate\Database\Migrations\Migration;
use Modules\SettingManagement\App\Models\SettingGroup;
use Modules\SettingManagement\App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOT: Bu migration TABLO OLUŞTURMUYOR!
     * Mevcut settings_groups ve settings tablolarına VERİ ekliyor.
     */
    public function up()
    {
        // ========================================
        // ANA GRUP: Payment Gateway Ayarları
        // ========================================
        $mainGroup = SettingGroup::create([
            'parent_id' => null,
            'name' => 'Payment Gateway Ayarları',
            'slug' => 'payment-gateway-settings',
            'description' => 'Ödeme gateway entegrasyon ayarları - PayTR, Stripe, İyzico, Havale/EFT',
            'icon' => 'fa-solid fa-credit-card',
            'is_active' => true,
            'sort_order' => 100,
        ]);

        // ========================================
        // AKTİF GATEWAY 1: PayTR (TAM AYARLAR)
        // ========================================
        $this->createPayTRGateway($mainGroup->id);

        // ========================================
        // AKTİF GATEWAY 2: Havale/EFT (TAM AYARLAR)
        // ========================================
        $this->createBankTransferGateway($mainGroup->id);

        // ========================================
        // PLACEHOLDER GATEWAY'LER (Gelecek için hazır)
        // ========================================
        $this->createPlaceholderGateway($mainGroup->id, 'stripe', 'Stripe Payment Gateway', 'fa-brands fa-stripe', 20);
        $this->createPlaceholderGateway($mainGroup->id, 'iyzico', 'İyzico Payment Gateway', 'fa-solid fa-money-bill-wave', 30);
        $this->createPlaceholderGateway($mainGroup->id, 'paypal', 'PayPal Payment Gateway', 'fa-brands fa-paypal', 40);
    }

    /**
     * PayTR Gateway (Tam Ayarlar)
     */
    private function createPayTRGateway(int $parentId)
    {
        $group = SettingGroup::create([
            'parent_id' => $parentId,
            'name' => 'PayTR Ödeme Gateway',
            'slug' => 'paytr-gateway',
            'description' => 'PayTR ödeme sistemi - Kredi kartı ile taksitli ödeme',
            'icon' => 'fa-solid fa-turkish-lira-sign',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $settings = [
            // Aktif/Pasif
            [
                'label' => 'PayTR Aktif',
                'key' => 'paytr_enabled',
                'type' => 'boolean',
                'default_value' => '0',
                'sort_order' => 1,
            ],
            // Display Name
            [
                'label' => 'Gösterim Adı',
                'key' => 'paytr_display_name',
                'type' => 'text',
                'default_value' => 'Kredi Kartı ile Ödeme',
                'sort_order' => 2,
            ],
            // Description
            [
                'label' => 'Açıklama',
                'key' => 'paytr_description',
                'type' => 'textarea',
                'default_value' => 'Kredi kartı ile güvenli ödeme yapabilirsiniz. Taksit imkanı mevcut.',
                'sort_order' => 3,
            ],
            // Sort Order
            [
                'label' => 'Sıralama Önceliği',
                'key' => 'paytr_sort_order',
                'type' => 'number',
                'default_value' => '1',
                'sort_order' => 4,
            ],
            // Logo URL
            [
                'label' => 'Logo URL',
                'key' => 'paytr_logo_url',
                'type' => 'text',
                'default_value' => '/images/payment/paytr-logo.png',
                'sort_order' => 5,
            ],
            // Min Amount
            [
                'label' => 'Minimum Tutar (TL)',
                'key' => 'paytr_min_amount',
                'type' => 'number',
                'default_value' => '10',
                'sort_order' => 6,
            ],
            // Max Amount
            [
                'label' => 'Maksimum Tutar (TL)',
                'key' => 'paytr_max_amount',
                'type' => 'number',
                'default_value' => '50000',
                'sort_order' => 7,
            ],

            // ========== PAYTR CREDENTIALS ==========

            // Merchant ID
            [
                'label' => 'Mağaza Numarası (Merchant ID)',
                'key' => 'paytr_merchant_id',
                'type' => 'text',
                'default_value' => '',
                'is_required' => false,
                'sort_order' => 10,
            ],
            // Merchant Key (Şifreli)
            [
                'label' => 'Mağaza Parolası (Merchant Key)',
                'key' => 'paytr_merchant_key',
                'type' => 'password',
                'default_value' => '',
                'is_required' => false,
                'sort_order' => 11,
            ],
            // Merchant Salt (Şifreli)
            [
                'label' => 'Mağaza Gizli Anahtarı (Merchant Salt)',
                'key' => 'paytr_merchant_salt',
                'type' => 'password',
                'default_value' => '',
                'is_required' => false,
                'sort_order' => 12,
            ],
            // Test Mode
            [
                'label' => 'Test Modu',
                'key' => 'paytr_test_mode',
                'type' => 'boolean',
                'default_value' => '1',
                'sort_order' => 13,
            ],
            // Debug Mode
            [
                'label' => 'Debug Modu (Geliştirme)',
                'key' => 'paytr_debug',
                'type' => 'boolean',
                'default_value' => '1',
                'sort_order' => 14,
            ],

            // ========== PAYTR OPTIONS ==========

            // Max Installment
            [
                'label' => 'Maksimum Taksit Sayısı',
                'key' => 'paytr_max_installment',
                'type' => 'select',
                'options' => [
                    'choices' => [
                        '0' => 'Sistem Varsayılanı (Tüm taksitler)',
                        '1' => 'Sadece Tek Çekim',
                        '3' => 'Maksimum 3 Taksit',
                        '6' => 'Maksimum 6 Taksit',
                        '9' => 'Maksimum 9 Taksit',
                        '12' => 'Maksimum 12 Taksit',
                    ]
                ],
                'default_value' => '0',
                'sort_order' => 15,
            ],
            // Currency
            [
                'label' => 'Para Birimi',
                'key' => 'paytr_currency',
                'type' => 'select',
                'options' => [
                    'choices' => [
                        'TL' => 'Türk Lirası (TL)',
                        'TRY' => 'Türk Lirası (TRY)',
                        'USD' => 'Amerikan Doları ($)',
                        'EUR' => 'Euro (€)',
                        'GBP' => 'İngiliz Sterlini (£)',
                        'RUB' => 'Rus Rublesi (₽)',
                    ]
                ],
                'default_value' => 'TL',
                'sort_order' => 16,
            ],
            // Timeout
            [
                'label' => 'Ödeme Zaman Aşımı (Dakika)',
                'key' => 'paytr_timeout_limit',
                'type' => 'number',
                'default_value' => '30',
                'sort_order' => 17,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create(array_merge($setting, [
                'group_id' => $group->id,
            ]));
        }
    }

    /**
     * Havale/EFT Gateway (Tam Ayarlar)
     */
    private function createBankTransferGateway(int $parentId)
    {
        $group = SettingGroup::create([
            'parent_id' => $parentId,
            'name' => 'Havale / EFT Ödeme',
            'slug' => 'bank-transfer-gateway',
            'description' => 'Banka havalesi veya EFT ile ödeme - Manuel onay gerektirir',
            'icon' => 'fa-solid fa-building-columns',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $settings = [
            // Aktif/Pasif
            [
                'label' => 'Havale/EFT Aktif',
                'key' => 'bank_transfer_enabled',
                'type' => 'boolean',
                'default_value' => '0',
                'sort_order' => 1,
            ],
            // Display Name
            [
                'label' => 'Gösterim Adı',
                'key' => 'bank_transfer_display_name',
                'type' => 'text',
                'default_value' => 'Havale / EFT',
                'sort_order' => 2,
            ],
            // Description
            [
                'label' => 'Açıklama',
                'key' => 'bank_transfer_description',
                'type' => 'textarea',
                'default_value' => 'Banka hesabımıza havale veya EFT yaparak ödeyebilirsiniz.',
                'sort_order' => 3,
            ],
            // Sort Order
            [
                'label' => 'Sıralama Önceliği',
                'key' => 'bank_transfer_sort_order',
                'type' => 'number',
                'default_value' => '10',
                'sort_order' => 4,
            ],
            // Logo URL
            [
                'label' => 'Logo URL',
                'key' => 'bank_transfer_logo_url',
                'type' => 'text',
                'default_value' => '/images/payment/bank-icon.png',
                'sort_order' => 5,
            ],
            // Approval Days
            [
                'label' => 'Ödeme Onay Bekleme Süresi (Gün)',
                'key' => 'bank_transfer_approval_days',
                'type' => 'number',
                'default_value' => '3',
                'sort_order' => 6,
            ],
            // Auto Cancel Days
            [
                'label' => 'Otomatik İptal Süresi (Gün)',
                'key' => 'bank_transfer_auto_cancel_days',
                'type' => 'number',
                'default_value' => '7',
                'sort_order' => 7,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create(array_merge($setting, [
                'group_id' => $group->id,
            ]));
        }
    }

    /**
     * Placeholder Gateway (Gelecek için hazır)
     * Sadece grup + 1 disabled checkbox
     */
    private function createPlaceholderGateway(int $parentId, string $slug, string $name, string $icon, int $sortOrder)
    {
        $group = SettingGroup::create([
            'parent_id' => $parentId,
            'name' => $name,
            'slug' => $slug . '-gateway',
            'description' => 'Bu ödeme yöntemi yakında aktif olacak. Detaylı dokümantasyon için lütfen bekleyin.',
            'icon' => $icon,
            'is_active' => false, // ❌ ŞİMDİLİK PASIF (Admin'de gözükmez)
            'sort_order' => $sortOrder,
        ]);

        // Sadece 1 disabled checkbox (gelecek için placeholder)
        Setting::create([
            'group_id' => $group->id,
            'label' => ucfirst($slug) . ' Aktif (Yakında)',
            'key' => $slug . '_enabled',
            'type' => 'boolean',
            'default_value' => '0',
            'is_active' => false, // ❌ Disabled
            'sort_order' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Ana grubu bul ve sil (cascade delete - alt gruplar ve settings de silinir)
        $mainGroup = SettingGroup::where('slug', 'payment-gateway-settings')->first();

        if ($mainGroup) {
            // Soft delete (deleted_at set edilir)
            $mainGroup->delete();
        }
    }
};
