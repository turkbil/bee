<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modül İzin Kontrol Ayarları
    |--------------------------------------------------------------------------
    |
    | Burada modül bazlı izin kontrolleri için konfigürasyon ayarlarını belirleyebilirsiniz
    |
    */
    
    // Temel rol tipleri
    'role_types' => [
        'root' => 'Tam Yetkili Yönetici',
        'admin' => 'Yönetici',
        'editor' => 'Editör'
    ],
    
    // Her modül için izin tipleri
    'permission_types' => [
        'view' => 'Görüntüleme',
        'create' => 'Oluşturma',
        'update' => 'Güncelleme',
        'delete' => 'Silme'
    ],
    
    // Central'da admin tarafından görüntülenemeyen modüller (sadece root erişebilir)
    'admin_restricted_modules' => [
        'tenantmanagement', // Tenant yönetimi sadece root'a açık
        'modulemanagement', // Modül yönetimi sadece root'a açık
        'settingmanagement', // Ayarlar yönetimi sadece root'a açık
    ],
    
    // Önbellek süreleri (dakika cinsinden)
    'cache_durations' => [
        'module_list' => 60 * 24, // 1 gün
        'module_tenant_assignment' => 60 * 12, // 12 saat
        'user_permissions' => 60 * 6, // 6 saat
    ],
];