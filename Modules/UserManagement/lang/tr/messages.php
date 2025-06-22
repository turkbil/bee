<?php

return [
    // Başarı Mesajları
    'user_created' => 'Kullanıcı başarıyla oluşturuldu.',
    'user_updated' => 'Kullanıcı başarıyla güncellendi.',
    'user_deleted' => 'Kullanıcı başarıyla silindi.',
    'user_activated' => '"{name}" aktif yapıldı.',
    'user_deactivated' => '"{name}" pasif yapıldı.',
    'selected_users_status_updated' => 'Seçili kayıtların durumları güncellendi.',
    
    'role_created' => 'Rol başarıyla oluşturuldu.',
    'role_updated' => 'Rol başarıyla güncellendi.',
    'role_deleted' => 'Rol başarıyla silindi.',
    'role_cannot_be_edited' => 'Bu rol düzenlenemez.',
    
    'permission_created' => 'Yetki başarıyla oluşturuldu.',
    'permission_updated' => 'Yetki başarıyla güncellendi.',
    'permission_deleted' => 'Yetki başarıyla silindi.',
    'permissions_saved' => 'Yetkiler başarıyla kaydedildi.',
    
    'avatar_updated' => 'Avatar başarıyla güncellendi.',
    'avatar_removed' => 'Avatar başarıyla kaldırıldı.',
    'avatar_upload_loading' => 'Avatar yönetimi sistemi yükleniyor...',
    'user_info' => 'Kullanıcı: {name}',
    
    // Hata Mesajları
    'operation_error' => 'İşlem sırasında bir hata oluştu: {error}',
    'role_protected' => 'Bu rol adı sistem tarafından korunmaktadır.',
    'user_not_found' => 'Kullanıcı bulunamadı.',
    'role_not_found' => 'Rol bulunamadı.',
    'permission_not_found' => 'Yetki bulunamadı.',
    'no_permission_for_module' => 'Bu modül için kullanılabilir izin bulunamadı. Önce modül izinlerini tanımlayın.',
    
    // Onay Mesajları
    'confirm_delete_user' => 'Bu kullanıcıyı silmek istediğinize emin misiniz?',
    'confirm_delete_role' => 'Bu rolü silmek istediğinize emin misiniz?',
    'confirm_delete_permission' => 'Bu yetkiyi silmek istediğinizden emin misiniz?',
    'confirm_clear_all_logs' => 'Tüm kayıtları temizlemek istediğinize emin misiniz?',
    'confirm_clear_user_logs' => 'Bu kullanıcının tüm kayıtlarını temizlemek istediğinize emin misiniz?',
    
    // Rol Açıklamaları
    'user_role_description' => 'Normal üye rolündeki kullanıcılar, sadece temel kullanıcı işlemlerini yapabilirler. Yönetim paneline ve modüllere erişimleri yoktur.',
    'editor_role_description' => 'Editörler, aşağıda seçilen modüllere erişebilir ve bu modüllerle ilgili işlemleri yapabilirler. Her modül için ayrı CRUD yetkileri tanımlanabilir.',
    'admin_role_description' => 'Admin kullanıcısı, kendi tenant\'ı içerisindeki tüm modüllere ve fonksiyonlara tam erişime sahiptir. Bu rol için özel izin ataması gerekmez.',
    'root_role_warning' => 'Root kullanıcısı, sistemdeki tüm modüllere ve fonksiyonlara tam erişime sahiptir. Bu rol, sadece sistem yöneticileri için tasarlanmıştır.',
    
    // Modül Yetkilendirme
    'module_authorization' => 'Modül Yetkilendirme',
    'detailed_authorization' => 'Detaylı Yetkilendirme',
    'simple_view' => 'Basit Görünüm',
    'standard_permissions_view' => 'Standart Görünüm',
    'detailed_crud_permissions' => 'Detaylı CRUD İzinleri Görünümü',
    
    // Log Açıklamaları
    'user_roles_cleared' => '"{name}" kullanıcısının rolleri temizlendi',
    'user_direct_permissions_cleared' => '"{name}" kullanıcısının direkt izinleri temizlendi',
    'user_module_permissions_cleared' => '"{name}" kullanıcısının modül izinleri temizlendi',
    'user_module_permissions_updated' => '"{name}" kullanıcısının modül izinleri güncellendi',
    'user_permissions_updated' => '{count} adet izin güncellendi',
    
    // Validation Mesajları
    'name_required' => 'İsim zorunludur.',
    'name_min' => 'İsim en az 3 karakter olmalıdır.',
    'email_required' => 'E-posta zorunludur.',
    'email_valid' => 'Geçerli bir e-posta adresi giriniz.',
    'email_unique' => 'Bu e-posta adresi zaten kullanılıyor.',
    'password_min' => 'Şifre en az 6 karakter olmalıdır.',
    'role_name_required' => 'Rol adı zorunludur.',
    'role_name_min' => 'Rol adı en az 3 karakter olmalıdır.',
    'role_name_max' => 'Rol adı en fazla 255 karakter olabilir.',
    'role_name_unique' => 'Bu rol adı zaten kullanılıyor.',
    'guard_name_required' => 'Guard name zorunludur.',
    'permission_name_required' => 'Yetki adı zorunludur.',
    'permission_name_min' => 'Yetki adı en az 3 karakter olmalıdır.',
    'permission_name_max' => 'Yetki adı en fazla 255 karakter olabilir.',
    'permission_name_unique' => 'Bu yetki adı zaten kullanılıyor.',
    'module_name_required' => 'Modül adı zorunludur.',
    'module_name_min' => 'Modül adı en az 3 karakter olmalıdır.',
    'module_name_max' => 'Modül adı en fazla 255 karakter olabilir.',
    'permission_types_required' => 'En az bir yetki tipi seçmelisiniz.',
    'permission_types_min' => 'En az bir yetki tipi seçmelisiniz.',
    'manual_permission_min' => 'Manuel yetki adı en az 3 karakter olmalıdır.',
    'manual_permission_max' => 'Manuel yetki adı en fazla 255 karakter olabilir.',
    
    // Avatar Upload Mesajları
    'avatar_upload_error' => 'Avatar yüklenirken hata oluştu.',
    'avatar_format_error' => 'Desteklenmeyen dosya formatı. Lütfen PNG, JPG veya WebP formatında bir dosya seçin.',
    'avatar_size_error' => 'Dosya boyutu çok büyük. Maksimum 2MB olabilir.',
    
    // Activity Log Mesajları
    'created' => 'oluşturuldu',
    'updated' => 'güncellendi',
    'deleted' => 'silindi',
    'activated' => 'aktif',
    'deactivated' => 'pasif',
    'login' => 'giriş yaptı',
    'logout' => 'çıkış yaptı',
    'password_changed' => 'şifre değiştirildi',
    'profile_updated' => 'profil güncellendi',
    'role_assigned' => 'rol atandı',
    'role_removed' => 'rol kaldırıldı',
    'permission_granted' => 'izin verildi',
    'permission_revoked' => 'izin alındı',
];