<?php

return [
    // Genel
    'payments' => 'Ödemeler',
    'payment_management' => 'Ödeme Yönetimi',
    'payment_methods' => 'Ödeme Yöntemleri',
    'payment_detail' => 'Ödeme Detayı',
    'new_payment_method' => 'Yeni Ödeme Yöntemi',
    'edit_payment_method' => 'Ödeme Yöntemi Düzenle',

    // Payment Fields
    'payment_number' => 'Ödeme Numarası',
    'transaction_id' => 'Transaction ID',
    'gateway_transaction_id' => 'Gateway Transaction ID',
    'amount' => 'Tutar',
    'currency' => 'Para Birimi',
    'status' => 'Durum',
    'gateway' => 'Gateway',
    'payment_type' => 'Ödeme Tipi',
    'card_info' => 'Kart Bilgileri',
    'card_last_four' => 'Son 4 Hane',
    'card_brand' => 'Kart Markası',
    'installment' => 'Taksit',
    'installment_count' => 'Taksit Sayısı',

    // Status
    'pending' => 'Bekliyor',
    'processing' => 'İşleniyor',
    'completed' => 'Tamamlandı',
    'failed' => 'Başarısız',
    'cancelled' => 'İptal Edildi',
    'refunded' => 'İade Edildi',

    // Payment Method Fields
    'title' => 'Başlık',
    'slug' => 'Slug',
    'description' => 'Açıklama',
    'gateway_name' => 'Gateway Adı',
    'gateway_mode' => 'Gateway Modu',
    'test_mode' => 'Test Modu',
    'live_mode' => 'Canlı Mod',
    'gateway_config' => 'Gateway Ayarları',
    'fixed_fee' => 'Sabit Ücret',
    'percentage_fee' => 'Yüzde Ücret',
    'supported_currencies' => 'Desteklenen Para Birimleri',
    'supports_installment' => 'Taksit Destekliyor',
    'max_installments' => 'Maksimum Taksit',
    'sort_order' => 'Sıra',
    'is_active' => 'Aktif',

    // Dates
    'created_at' => 'Oluşturulma',
    'paid_at' => 'Ödeme Tarihi',
    'failed_at' => 'Hata Tarihi',
    'cancelled_at' => 'İptal Tarihi',
    'refunded_at' => 'İade Tarihi',

    // Details
    'basic_info' => 'Temel Bilgiler',
    'order_info' => 'Sipariş Bilgileri',
    'dates' => 'Tarihler',
    'notes' => 'Notlar',
    'metadata' => 'Metadata',
    'gateway_response' => 'Gateway Response',
    'payable_model' => 'Payable Model',

    // Actions
    'view_detail' => 'Detay',
    'edit_detail' => 'Detaylı Düzenle',
    'close' => 'Kapat',
    'save' => 'Kaydet',
    'update' => 'Güncelle',
    'delete' => 'Sil',
    'activate' => 'Aktifleştir',
    'deactivate' => 'Pasifleştir',

    // Search & Filter
    'search_placeholder' => 'Payment Number / Transaction ID ile ara...',
    'all_statuses' => 'Tüm Durumlar',
    'all_gateways' => 'Tüm Gateway\'ler',
    'filter_by_status' => 'Duruma Göre Filtrele',
    'filter_by_gateway' => 'Gateway\'e Göre Filtrele',

    // Messages
    'no_payments_found' => 'Henüz ödeme kaydı yok',
    'no_payment_methods_found' => 'Henüz ödeme yöntemi tanımlanmamış',
    'payment_not_found' => 'Ödeme bulunamadı',
    'payment_method_not_found' => 'Ödeme yöntemi bulunamadı',
    'notes_updated' => 'Notlar güncellendi',
    'payment_method_created' => 'Ödeme yöntemi başarıyla oluşturuldu',
    'payment_method_updated' => 'Ödeme yöntemi başarıyla güncellendi',
    'payment_method_deleted' => 'Ödeme yöntemi başarıyla silindi',
    'no_notes' => 'Not yok',

    // Sections
    'basic_settings' => 'Temel Bilgiler',
    'gateway_settings' => 'Gateway Ayarları',
    'fees_limits' => 'Ücret & Limitler',
    'installment_settings' => 'Taksit Ayarları',
    'payment_types_support' => 'Ödeme Tipleri Desteği',

    // Payment Types
    'purchase' => 'Satış',
    'subscription' => 'Abonelik',
    'donation' => 'Bağış',

    // Gateways
    'paytr' => 'PayTR',
    'stripe' => 'Stripe',
    'iyzico' => 'Iyzico',
    'paypal' => 'PayPal',
    'manual' => 'Manuel',

    // Hints
    'gateway_config_hint' => 'Gateway config (API keys) JSON formatında girilmelidir.',
    'slug_hint' => 'Örnek: paytr-credit-card',
    'currencies_hint' => 'CTRL ile çoklu seçim yapabilirsiniz',
    'sort_order_hint' => 'Küçük sayı önce gelir',
];
