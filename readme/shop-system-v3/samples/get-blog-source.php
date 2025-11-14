<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

tenancy()->initialize(2);

$blog = Modules\Blog\App\Models\Blog::where('slug', 'forkliftlerin-bakim-surecleri-performansi-artirmak-icin-gerekenler')->first();

if (!$blog) {
    echo "❌ Blog bulunamadı!" . PHP_EOL;
    exit(1);
}

echo "=== VERİTABANI KAYNAK VERİSİ ===" . PHP_EOL . PHP_EOL;
echo "📌 TITLE:" . PHP_EOL;
echo json_encode($blog->title, JSON_UNESCAPED_UNICODE) . PHP_EOL . PHP_EOL;
echo "📌 BODY:" . PHP_EOL;
echo $blog->getTranslation('body', 'tr') . PHP_EOL . PHP_EOL;
echo "📌 EXCERPT:" . PHP_EOL;
echo $blog->getTranslation('excerpt', 'tr') . PHP_EOL . PHP_EOL;
$seo = $blog->seoSetting;
if ($seo) {
    echo "📌 SEO:" . PHP_EOL;
    echo "Title: " . json_encode($seo->title, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    echo "Description: " . json_encode($seo->description, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    echo "Keywords: " . json_encode($seo->keywords, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
