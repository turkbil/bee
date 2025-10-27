<?php
/**
 * 🔧 SEO LAYOUT PARAGRAPH SİL
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Paragraph elementi kaldırılıyor...\n\n";

$group = DB::table('settings_groups')->where('id', 8)->first();
$layout = json_decode($group->layout, true);

// Paragraph elementi bul ve sil (index 5)
if (isset($layout['elements'][5]) && $layout['elements'][5]['type'] === 'paragraph') {
    unset($layout['elements'][5]);
    // Array'i yeniden indexle
    $layout['elements'] = array_values($layout['elements']);

    echo "✅ Paragraph elementi kaldırıldı\n";
} else {
    echo "⚠️ Paragraph elementi bulunamadı (zaten silinmiş olabilir)\n";
}

// Layout'u güncelle
$updatedLayout = json_encode($layout, JSON_UNESCAPED_UNICODE);

DB::table('settings_groups')
    ->where('id', 8)
    ->update([
        'layout' => $updatedLayout,
        'updated_at' => now()
    ]);

echo "✅ Layout güncellendi\n";
echo "📊 Element sayısı: " . count($layout['elements']) . "\n\n";
echo "✨ İşlem tamamlandı!\n";
