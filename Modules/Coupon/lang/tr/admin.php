<?php

return [
    // Menu
    'coupon_management' => 'Kupon Yönetimi',
    'menu' => 'Menü',

    // Coupons
    'coupons' => 'Kuponlar',
    'new_coupon' => 'Yeni Kupon',
    'create' => 'Yeni Kupon',
    'edit' => 'Kupon Düzenle',
    'code' => 'Kupon Kodu',
    'discount' => 'İndirim',
    'usage' => 'Kullanım',
    'valid_until' => 'Geçerlilik',
    'no_coupons' => 'Henüz kupon eklenmedi',

    // Stats
    'total_coupons' => 'Toplam Kupon',
    'active_coupons' => 'Aktif Kupon',
    'total_usage' => 'Toplam Kullanım',

    // Filters
    'all_types' => 'Tüm Tipler',

    // Types
    'percentage' => 'Yüzde',
    'fixed_amount' => 'Sabit Tutar',
    'free_shipping' => 'Ücretsiz Kargo',
    'buy_x_get_y' => 'Al X Öde Y',

    // Scope
    'applies_to' => 'Uygulama Alanı',
    'scope_all' => 'Tümü',
    'scope_shop' => 'Mağaza (E-ticaret)',
    'scope_subscription' => 'Abonelik',

    // Status
    'active' => 'Aktif',
    'inactive' => 'Pasif',
    'expired' => 'Süresi Doldu',
    'limit_reached' => 'Limit Doldu',
    'scheduled' => 'Planlandı',

    // Form
    'basic_info' => 'Temel Bilgiler',
    'generate' => 'Oluştur',
    'search_code' => 'Kupon kodu ara',
    'discount_type' => 'İndirim Tipi',
    'discount_value' => 'İndirim Değeri',
    'limits' => 'Limitler',
    'usage_limit' => 'Kullanım Limiti',
    'usage_per_user' => 'Kullanıcı Başı Limit',
    'used_count' => 'Kullanım Sayısı',
    'validity' => 'Geçerlilik',
    'starts_at' => 'Başlangıç Tarihi',
    'expires_at' => 'Bitiş Tarihi',
    'min_amount' => 'Min. Tutar',
    'max_discount' => 'Max. İndirim',
    'optional' => 'Opsiyonel',
    'unlimited' => 'Limitsiz',
    'no_expiry' => 'Süresiz',

    // Step titles (UX)
    'step_code' => 'Kupon Kodu Oluştur',
    'step_discount' => 'İndirim Türünü Seç',
    'step_scope' => 'Nerede Kullanılacak?',

    // Hints (UX)
    'code_hint' => 'Müşterilerinizin hatırlayacağı bir kod yazın veya otomatik oluşturun',
    'hint_percentage' => 'Örn: %20 indirim',
    'hint_fixed' => 'Örn: 50₺ indirim',
    'hint_shipping' => 'Kargo bedava',
    'hint_bogo' => '2 Al 1 Öde',
    'hint_scope_all' => 'Her yerde geçerli',
    'hint_scope_shop' => 'Sadece ürünlerde',
    'hint_scope_sub' => 'Sadece üyelikte',
    'example' => 'Örnek',
    'hint_min_amount' => 'Minimum sepet tutarı (boş bırakılabilir)',
    'hint_max_discount' => 'Maksimum indirim tutarı (yüzde için)',
    'hint_usage_limit' => 'Toplam kaç kez kullanılabilir?',
    'hint_usage_per_user' => 'Her kullanıcı kaç kez kullanabilir?',
    'hint_starts_at' => 'Boş bırakılırsa hemen başlar',
    'hint_expires_at' => 'Boş bırakılırsa süresiz geçerli',

    // Errors
    'errors' => [
        'not_found' => 'Kupon bulunamadı',
        'invalid' => 'Geçersiz kupon',
        'inactive' => 'Bu kupon aktif değil',
        'expired' => 'Bu kuponun süresi dolmuş',
        'limit_reached' => 'Bu kuponun kullanım limiti dolmuş',
        'not_started' => 'Bu kupon henüz başlamamış',
        'user_limit_reached' => 'Bu kuponu kullanım hakkınız dolmuş',
        'min_amount' => 'Minimum sepet tutarı :amount ₺ olmalıdır',
    ],
];
