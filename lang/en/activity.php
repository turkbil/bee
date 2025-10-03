<?php

/**
 * Activity Log Event Translations (EN)
 *
 * Common activity event translations for all modules
 * Used by log_activity() helper function
 */

return [
    // CRUD Operations
    'eklendi' => 'created',
    'güncellendi' => 'updated',
    'silindi' => 'deleted',
    'kalıcı-silindi' => 'permanently deleted',

    // Bulk Operations
    'toplu-güncellendi' => 'bulk updated',
    'toplu-silindi' => 'bulk deleted',

    // Status Changes
    'etkinleştirildi' => 'activated',
    'devre-dışı' => 'deactivated',
    'aktif-edildi' => 'enabled',
    'pasif-edildi' => 'disabled',

    // Translation
    'çevrildi' => 'translated',
    'otomatik-çevrildi' => 'auto-translated',

    // Special Operations
    'yayınlandı' => 'published',
    'taslak-edildi' => 'drafted',
    'arşivlendi' => 'archived',
    'geri-yüklendi' => 'restored',
    'kopyalandı' => 'duplicated',
    'taşındı' => 'moved',
    'sıralandı' => 'reordered',

    // Permission & Access
    'erişim-verildi' => 'access granted',
    'erişim-kaldırıldı' => 'access revoked',
    'yetki-verildi' => 'permission granted',
    'yetki-kaldırıldı' => 'permission revoked',

    // Media & Files
    'yüklendi' => 'uploaded',
    'değiştirildi' => 'replaced',
    'kırpıldı' => 'cropped',
    'optimize-edildi' => 'optimized',

    // Cache & System
    'önbellek-temizlendi' => 'cache cleared',
    'yenilendi' => 'refreshed',
    'senkronize-edildi' => 'synchronized',
    'içe-aktarıldı' => 'imported',
    'dışa-aktarıldı' => 'exported',
];