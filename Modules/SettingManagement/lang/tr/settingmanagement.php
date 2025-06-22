<?php

return [
    'title' => 'Ayar Yönetimi',
    'description' => 'Sistem ayarlarını yönetin',
    'list' => 'Ayar Listesi',
    'create' => 'Yeni Ayar',
    'edit' => 'Ayar Düzenle',
    
    'group' => [
        'title' => 'Grup',
        'list' => 'Grup Listesi',
        'create' => 'Yeni Grup',
        'edit' => 'Grup Düzenle',
        'name' => 'Grup Adı',
        'description' => 'Açıklama',
        'status' => 'Durum',
        'active' => 'Aktif',
        'inactive' => 'Pasif',
        'enabled' => 'Etkin',
        'disabled' => 'Devre Dışı',
    ],
    
    'messages' => [
        'success' => 'Başarılı!',
        'error' => 'Hata!',
        'group_created' => 'Grup başarıyla eklendi',
        'group_updated' => 'Grup başarıyla güncellendi',
        'group_deleted' => 'Grup başarıyla silindi',
        'group_status_updated' => 'Grup durumu güncellendi',
        'group_activated' => 'aktif edildi',
        'group_deactivated' => 'pasif edildi',
        'group_create_error' => 'Grup eklenirken bir hata oluştu',
        'group_delete_error' => 'Alt grupları olan bir grubu silemezsiniz',
        'form_layout_saved' => 'Form yapısı kaydedildi',
        'values_saved' => 'Değişiklikler kaydedildi.',
        'file_removed' => 'Dosya kaldırıldı.',
        'file_deleted' => 'Dosya silindi.',
        'file_upload_error' => 'Dosya yüklenirken bir hata oluştu: ',
        'multi_image_upload_error' => 'Çoklu resim yüklenirken bir hata oluştu: ',
    ],
    
    'actions' => [
        'created' => 'oluşturuldu',
        'updated' => 'güncellendi', 
        'deleted' => 'silindi',
        'form_layout_updated' => 'form layout güncellendi',
        'value_updated' => 'değeri güncellendi',
        'reset_to_default' => 'varsayılan değere döndürüldü',
    ],
    
    'fields' => [
        'name' => 'Ad',
        'value' => 'Değer',
        'default' => 'Varsayılan',
        'description' => 'Açıklama',
        'type' => 'Tip',
        'required' => 'Zorunlu',
        'options' => 'Seçenekler',
    ],
    
    'file_upload' => [
        'drag_drop' => 'Görseli sürükleyip bırakın veya tıklayın',
        'drop_here' => 'Bırakın!',
        'supported_formats' => 'PNG, JPG, WEBP, GIF - Maks 2MB - Toplu seçim yapabilirsiniz',
        'uploaded_photo' => 'Yüklenen Fotoğraf',
        'alt_text' => 'Görsel',
    ],
    
    'misc' => [
        'show_all' => 'Tümünü Göster',
        'loading' => 'Yükleniyor...',
        'no_results' => 'Sonuç bulunamadı',
        'search_placeholder' => 'Arama yapın...',
    ],
    
    'operations' => 'Ayar İşlemleri',
    'tenant_settings' => 'Tenant Ayarları',
];