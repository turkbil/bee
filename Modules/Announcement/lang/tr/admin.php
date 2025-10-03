<?php

return [
// Genel
'pages' => 'Sayfalar',
'page_management' => 'Sayfa Yönetimi',
'new_page' => 'Yeni Sayfa',
'media_management' => 'Medya Yönetimi',
'new_page_pretitle' => 'Yeni Duyuru Oluştur',
'edit_page_pretitle' => 'Duyuru Düzenle',
'save_to_upload_media' => 'Medya yüklemek için önce duyuruyu kaydedin',
'announcement_detail' => 'Duyuru Detayı',
'home' => 'Anasayfa',
'title_field' => 'Başlık',
'content' => 'İçerik',
'slug_field' => 'Slug',
'active' => 'Aktif',
'inactive' => 'Pasif',
'status' => 'Durum',
'activate' => 'Aktifleştir',
'deactivate' => 'Pasifleştir',

// Pretitle
'edit_announcement_pretitle' => 'Duyuru Düzenleme',
'new_announcement_pretitle' => 'Yeni Duyuru Ekleme',

// SEO
'seo' => 'SEO',
'meta_keywords' => 'Meta Anahtar Kelimeler',
'meta_description' => 'Meta Açıklama',
'focus_keywords' => 'Ana Odak Kelimeleri',
'focus_keywords_placeholder' => 'ana kelime1, ana kelime2, ana kelime3',
'focus_keywords_help' => 'Bu sayfa için en önemli anahtar kelimeleri girin. SEO odaklanması için kullanılır.',

// AI & Translation
'ai_bulk_translate' => 'Yapay Zeka ile Toplu Çeviri',
'ai_content_instructions' => 'Sayfa içerikleri üretimi. SEO uyumlu, kullanıcı dostu ve kapsamlı sayfa içerikleri oluştur.',

// Search & Filter
'search_placeholder' => 'Ara...',
'enter_new_title' => 'Yeni başlık giriniz',
'items_selected' => 'öğe seçildi',
'no_pages_found' => 'Sayfa bulunamadı',
'no_results' => 'Sonuç bulunamadı',

// Menu
'menu' => 'Menü',
'menu_title' => 'Sayfa Yönetimi',
'edit_page' => 'Sayfa Düzenle',
'create_page' => 'Yeni Sayfa',

// Validation & Messages
'title_validation_error' => 'Başlık geçersiz. Lütfen kontrol edin.',
'title_updated' => 'başlık güncellendi',
'title_updated_successfully' => 'Başlık başarıyla güncellendi.',
'page_not_found' => 'Sayfa bulunamadı',
'homepage_cannot_be_deactivated' => 'Anasayfa deaktifleştirilemez',
'operation_failed' => 'İşlem başarısız',
'no_pages_can_be_deleted' => 'Hiç sayfa silinemez',

// Dashboard Translations
'total_pages' => 'Toplam Sayfa',
'all_pages' => 'Tüm Sayfalar',
'create_page' => 'Sayfa Oluştur',
'recent_pages' => 'Son Sayfalar',
'no_pages_yet' => 'Henüz sayfa yok',
'create_first_page' => 'İlk Sayfayı Oluştur',
'view_all' => 'Tümünü Gör',
'manage' => 'Yönet',

// Content Editor
'content_placeholder' => 'Sayfa içeriğinizi buraya yazın',
'wysiwyg_editor' => 'Zengin Metin Editörü',

// SEO Management
'seo_title' => 'Meta Başlık',
'seo_title_placeholder' => 'Google\'da gözükecek başlık',
'seo_title_help' => 'Google arama sonuçlarında gözüken başlık. Tıklanmak isteyecek şekilde yazın.',
'seo_description' => 'Meta Açıklama',
'seo_description_placeholder' => 'Google\'da başlığın altında gözükecek açıklama',
'seo_description_help' => 'Google\'da başlığın altında gözüken açıklama. İnsanı tıklamaya teşvik etmeli.',
'seo_keywords' => 'Anahtar Kelimeler',
'seo_keywords_placeholder' => 'anahtar1, anahtar2, anahtar3',
'seo_keywords_help' => '5-10 kelime yeterli. Sayfanızın hangi kelimelerle bulunacağını belirler.',
'focus_keyword' => 'Ana Odak Kelime',
'focus_keyword_placeholder' => 'Ana odak kelime',
'focus_keyword_help' => 'Sayfanın ana odaklandığı tek kelime. Bu kelimeyi sayfada 3-5 kez geçirin.',
'editor_supports_html' => 'Editör HTML ve zengin metin formatlarını destekler',
'fullscreen' => 'Tam Ekran',
'initializing_editor' => 'Editör başlatılıyor',

// Service Messages
'announcement_created_successfully' => 'Duyuru başarıyla oluşturuldu',
'announcement_updated_successfully' => 'Duyuru başarıyla güncellendi',
'announcement_deleted_successfully' => 'Duyuru başarıyla silindi',
'update_failed' => 'Güncelleme başarısız',
'deletion_failed' => 'Silme işlemi başarısız',

// Backward compatibility (deprecated)
'page_created_successfully' => 'Duyuru başarıyla oluşturuldu',
'page_updated_successfully' => 'Duyuru başarıyla güncellendi',
'page_deleted_successfully' => 'Duyuru başarıyla silindi',

// Content Placeholders
'content_placeholder' => 'Sayfa içeriğinizi buraya yazın',

// Studio Editor
'studio.editor' => 'Studio ile Düzenle',

// Media Library
'media' => [
    'featured_image' => 'Öne Çıkan Görsel',
    'gallery' => 'Galeri',
    'upload_featured' => 'Görsel Yükle',
    'upload_gallery' => 'Galeri Görselleri Yükle',
    'delete_featured' => 'Görseli Sil',
    'delete_gallery_image' => 'Galeri Görselini Sil',
    'set_as_featured' => 'Kapak Yap',
    'drag_to_reorder' => 'Sıralamak için sürükleyin',
    'drag_drop_featured' => 'Görseli sürükle bırak veya tıklayarak seç',
    'drag_drop_gallery' => 'Görselleri sürükle bırak veya tıklayarak seç',
    'max_file_size' => 'Maksimum dosya boyutu: 10MB',
    'allowed_types' => 'İzin verilen format: JPG, PNG, WEBP, GIF',
    'featured_deleted' => 'Öne çıkan görsel silindi',
    'gallery_image_deleted' => 'Galeri görseli silindi',
    'featured_set_from_gallery' => 'Görsel kapak fotoğrafı olarak ayarlandı',
    'gallery_order_updated' => 'Galeri sıralaması güncellendi',
    'no_featured_image' => 'Henüz görsel yüklenmedi',
    'no_gallery_images' => 'Henüz galeri görseli yok',
    'uploading' => 'Yükleniyor...',
    'upload_success' => 'Görsel başarıyla yüklendi',
],
];
