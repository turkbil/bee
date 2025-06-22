<?php

return [
    // Başarı Mesajları
    'page_created' => 'Sayfa başarıyla oluşturuldu',
    'page_updated' => 'Sayfa başarıyla güncellendi',
    'page_deleted' => 'Sayfa başarıyla silindi',
    'page_published' => 'Sayfa başarıyla yayınlandı',
    'page_unpublished' => 'Sayfa yayından kaldırıldı',
    'page_archived' => 'Sayfa arşivlendi',
    'page_restored' => 'Sayfa geri yüklendi',
    'page_duplicated' => 'Sayfa çoğaltıldı',
    'page_activated' => '":title" aktif edildi',
    'page_deactivated' => '":title" pasif edildi',
    'pages_reordered' => 'Sayfalar yeniden sıralandı',
    'settings_updated' => 'Ayarlar başarıyla güncellendi',
    'cache_cleared' => 'Önbellek temizlendi',
    
    // Hata Mesajları
    'page_not_found' => 'Sayfa bulunamadı',
    'page_create_failed' => 'Sayfa oluşturulamadı',
    'page_update_failed' => 'Sayfa güncellenemedi',
    'page_delete_failed' => 'Sayfa silinemedi',
    'page_publish_failed' => 'Sayfa yayınlanamadı',
    'page_access_denied' => 'Bu sayfaya erişim izniniz yok',
    'slug_already_exists' => 'Bu URL adresi zaten kullanılıyor',
    'parent_not_found' => 'Üst sayfa bulunamadı',
    'cannot_delete_with_children' => 'Alt sayfaları olan sayfa silinemez',
    'invalid_template' => 'Geçersiz şablon seçimi',
    'upload_failed' => 'Dosya yüklenemedi',
    
    // Uyarı Mesajları
    'confirm_delete' => 'Bu sayfayı silmek istediğinizden emin misiniz?',
    'confirm_archive' => 'Bu sayfayı arşivlemek istediğinizden emin misiniz?',
    'confirm_unpublish' => 'Bu sayfayı yayından kaldırmak istediğinizden emin misiniz?',
    'confirm_bulk_delete' => 'Seçili sayfaları silmek istediğinizden emin misiniz?',
    'unsaved_changes' => 'Kaydedilmemiş değişiklikleriniz var',
    'leave_without_saving' => 'Değişikliklerinizi kaydetmeden çıkmak istediğinizden emin misiniz?',
    
    // Bilgi Mesajları
    'page_auto_saved' => 'Sayfa otomatik olarak kaydedildi',
    'page_scheduled' => 'Sayfa zamanlı yayın için ayarlandı',
    'no_pages_found' => 'Hiç sayfa bulunamadı',
    'no_results' => 'Aramanızla eşleşen sayfa bulunamadı',
    'draft_mode' => 'Taslak modunda çalışıyorsunuz',
    'preview_mode' => 'Önizleme modundasınız',
    
    // Toplu İşlemler
    'bulk_published' => ':count sayfa yayınlandı',
    'bulk_unpublished' => ':count sayfa yayından kaldırıldı',
    'bulk_archived' => ':count sayfa arşivlendi',
    'bulk_deleted' => ':count sayfa silindi',
    'bulk_restored' => ':count sayfa geri yüklendi',
    'no_items_selected' => 'Hiçbir öğe seçilmemiş',
    'items_selected' => 'öğe seçildi',
    'select_action' => 'Bir işlem seçin',
    
    // Validasyon
    'title_required' => 'Başlık zorunludur',
    'title_min' => 'Başlık en az 3 karakter olmalıdır',
    'title_max' => 'Başlık 255 karakteri geçemez',
    'content_required' => 'İçerik zorunludur',
    'slug_required' => 'URL adresi zorunludur',
    'slug_unique' => 'Bu URL adresi zaten kullanılıyor',
    'slug_format' => 'URL adresi sadece harf, rakam ve tire içerebilir',
    'parent_invalid' => 'Geçersiz üst sayfa seçimi',
    'template_required' => 'Şablon seçimi zorunludur',
    'homepage_cannot_be_deactivated' => 'Ana sayfa pasif yapılamaz!',
    
    // Yardımcı Metinler
    'slug_help' => 'URL\'de görünecek adres. Boş bırakırsanız başlıktan otomatik oluşturulur.',
    'excerpt_help' => 'Sayfanın kısa açıklaması. Liste görünümlerinde kullanılır.',
    'meta_description_help' => 'Arama motorları için sayfa açıklaması (160 karakter önerilir)',
    'parent_help' => 'Bu sayfayı başka bir sayfanın alt sayfası yapmak için seçin',
    'template_help' => 'Sayfa için kullanılacak görünüm şablonu',
    'visibility_help' => 'Sayfanın kimler tarafından görülebileceğini belirler',
];