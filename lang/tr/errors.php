<?php

return [
    // HTTP Hataları
    '400' => 'Geçersiz İstek',
    '401' => 'Yetkisiz Erişim',
    '403' => 'Erişim Reddedildi',
    '404' => 'Sayfa Bulunamadı',
    '405' => 'İzin Verilmeyen Metot',
    '408' => 'İstek Zaman Aşımı',
    '419' => 'Sayfa Süresi Dolmuş',
    '429' => 'Çok Fazla İstek',
    '500' => 'Sunucu Hatası',
    '502' => 'Geçersiz Ağ Geçidi',
    '503' => 'Hizmet Kullanılamıyor',
    '504' => 'Ağ Geçidi Zaman Aşımı',
    
    // Genel Hatalar
    'general_error' => 'Bir hata oluştu',
    'unknown_error' => 'Bilinmeyen hata',
    'system_error' => 'Sistem hatası',
    'database_error' => 'Veritabanı hatası',
    'connection_error' => 'Bağlantı hatası',
    'timeout_error' => 'Zaman aşımı hatası',
    'network_error' => 'Ağ hatası',
    'server_error' => 'Sunucu hatası',
    
    // Erişim Hataları
    'access_denied' => 'Bu işlem için yetkiniz yok',
    'permission_denied' => 'İzin reddedildi',
    'unauthorized' => 'Yetkisiz erişim',
    'forbidden' => 'Yasak erişim',
    'authentication_required' => 'Kimlik doğrulama gerekli',
    'session_expired' => 'Oturum süresi dolmuş',
    'login_required' => 'Giriş yapmanız gerekiyor',
    'admin_required' => 'Admin yetkisi gerekiyor',
    
    // Kayıt Hataları
    'not_found' => 'Kayıt bulunamadı',
    'record_not_found' => 'Kayıt bulunamadı',
    'user_not_found' => 'Kullanıcı bulunamadı',
    'file_not_found' => 'Dosya bulunamadı',
    'page_not_found' => 'Sayfa bulunamadı',
    'module_not_found' => 'Modül bulunamadı',
    'route_not_found' => 'Sayfa bulunamadı',
    'resource_not_found' => 'Kaynak bulunamadı',
    
    // Doğrulama Hataları
    'validation_failed' => 'Doğrulama başarısız',
    'invalid_input' => 'Geçersiz giriş',
    'invalid_data' => 'Geçersiz veri',
    'invalid_format' => 'Geçersiz format',
    'invalid_email' => 'Geçersiz e-posta adresi',
    'invalid_phone' => 'Geçersiz telefon numarası',
    'invalid_url' => 'Geçersiz URL',
    'invalid_date' => 'Geçersiz tarih',
    'invalid_file' => 'Geçersiz dosya',
    'invalid_image' => 'Geçersiz resim',
    
    // Dosya Hataları
    'file_upload_failed' => 'Dosya yükleme başarısız',
    'file_too_large' => 'Dosya çok büyük',
    'file_type_not_allowed' => 'Dosya türüne izin verilmiyor',
    'file_corrupted' => 'Dosya bozuk',
    'file_exists' => 'Dosya zaten mevcut',
    'file_permission_denied' => 'Dosya izni reddedildi',
    'disk_full' => 'Disk dolu',
    'storage_error' => 'Depolama hatası',
    
    // İşlem Hataları
    'operation_failed' => 'İşlem başarısız',
    'save_failed' => 'Kaydetme başarısız',
    'update_failed' => 'Güncelleme başarısız',
    'delete_failed' => 'Silme başarısız',
    'create_failed' => 'Oluşturma başarısız',
    'copy_failed' => 'Kopyalama başarısız',
    'move_failed' => 'Taşıma başarısız',
    'import_failed' => 'İçe aktarma başarısız',
    'export_failed' => 'Dışa aktarma başarısız',
    
    // Form Hataları
    'required_field' => 'Bu alan zorunludur',
    'field_too_short' => 'Bu alan çok kısa',
    'field_too_long' => 'Bu alan çok uzun',
    'password_mismatch' => 'Şifreler eşleşmiyor',
    'weak_password' => 'Şifre çok zayıf',
    'email_exists' => 'Bu e-posta adresi zaten kullanılıyor',
    'username_exists' => 'Bu kullanıcı adı zaten kullanılıyor',
    'duplicate_entry' => 'Tekrarlanan kayıt',
    
    // Ağ Hataları
    'no_internet' => 'İnternet bağlantısı yok',
    'dns_error' => 'DNS hatası',
    'ssl_error' => 'SSL hatası',
    'certificate_error' => 'Sertifika hatası',
    'proxy_error' => 'Proxy hatası',
    'firewall_blocked' => 'Güvenlik duvarı tarafından engellenmiş',
    
    // Maintenance
    'maintenance_mode' => 'Site bakım modunda',
    'service_unavailable' => 'Hizmet kullanılamıyor',
    'temporarily_unavailable' => 'Geçici olarak kullanılamıyor',
    'scheduled_maintenance' => 'Planlanmış bakım',
    
    // API Hataları
    'api_error' => 'API hatası',
    'api_limit_exceeded' => 'API limiti aşıldı',
    'api_key_invalid' => 'Geçersiz API anahtarı',
    'api_unavailable' => 'API kullanılamıyor',
    'rate_limit_exceeded' => 'Oran limiti aşıldı',
];