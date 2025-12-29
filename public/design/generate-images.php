<?php
/**
 * Template Gorsel Uretici
 * prompt.html'deki promptlari okuyup Leonardo.ai ile gorsel uretir
 *
 * Kullanim: /design/generate-images.php?template=genel-kurumsal/v1-20251229-0200
 */

require_once __DIR__ . '/../../vendor/autoload.php';

// Laravel bootstrap
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use App\Services\Media\LeonardoAIService;
use Illuminate\Support\Facades\Log;

header('Content-Type: application/json; charset=utf-8');

// Template parametresi
$template = $_GET['template'] ?? null;

if (!$template) {
    echo json_encode(['error' => 'Template parametresi gerekli. Ornek: ?template=genel-kurumsal/v1']);
    exit;
}

// Guvenlik: Sadece templates klasorunde calis
$template = preg_replace('/[^a-zA-Z0-9\-_\/]/', '', $template);
$templatePath = __DIR__ . '/templates/' . $template;
$promptFile = $templatePath . '/prompt.html';

if (!file_exists($promptFile)) {
    echo json_encode(['error' => 'prompt.html bulunamadi: ' . $template]);
    exit;
}

// prompt.html icerigini oku
$promptHtml = file_get_contents($promptFile);

// Gorsel promptlarini parse et
// Desteklenen formatlar: HTML kartlar, data attributes, JSON
$images = [];

// Pattern 1: v10 HTML format (border-l-4 kartlar)
// <span class="font-medium text-white">hero-bg.jpg</span>
// <span class="text-xs text-slate-400">1920x1080</span>
// <p class="text-slate-300...">prompt text</p>
preg_match_all('/<span[^>]*class="[^"]*font-medium[^"]*"[^>]*>([^<]+\.(jpg|png|webp))<\/span>.*?<span[^>]*class="[^"]*text-slate-400[^"]*"[^>]*>([^<]+)<\/span>.*?<p[^>]*class="[^"]*text-slate-300[^"]*"[^>]*>(.*?)<\/p>/is', $promptHtml, $matches1, PREG_SET_ORDER);

foreach ($matches1 as $match) {
    $prompt = trim(strip_tags($match[4]));
    $prompt = preg_replace('/\s+/', ' ', $prompt); // Fazla boslukları temizle
    if (!empty($prompt)) {
        $images[] = [
            'filename' => trim($match[1]),
            'size' => trim($match[3]),
            'prompt' => $prompt,
        ];
    }
}

// Pattern 2: <h4>dosya.jpg (boyut)</h4> <p class="prompt">...</p>
preg_match_all('/<h4[^>]*>([^<]+\.(jpg|png|webp))\s*\(([^)]+)\)<\/h4>\s*<p[^>]*class="prompt"[^>]*>([^<]+)<\/p>/i', $promptHtml, $matches2, PREG_SET_ORDER);

foreach ($matches2 as $match) {
    $images[] = [
        'filename' => trim($match[1]),
        'size' => trim($match[3]),
        'prompt' => trim($match[4]),
    ];
}

// Pattern 3: data-image="filename" data-prompt="..."
preg_match_all('/data-image="([^"]+)"\s+data-size="([^"]+)"\s+data-prompt="([^"]+)"/i', $promptHtml, $matches3, PREG_SET_ORDER);

foreach ($matches3 as $match) {
    $images[] = [
        'filename' => trim($match[1]),
        'size' => trim($match[2]),
        'prompt' => trim($match[3]),
    ];
}

// Pattern 4: JSON format <!-- IMAGES_JSON: [...] -->
if (preg_match('/<!--\s*IMAGES_JSON:\s*(\[.+?\])\s*-->/s', $promptHtml, $jsonMatch)) {
    $jsonImages = json_decode($jsonMatch[1], true);
    if ($jsonImages) {
        foreach ($jsonImages as $img) {
            $images[] = [
                'filename' => $img['filename'] ?? $img['file'] ?? 'image.jpg',
                'size' => $img['size'] ?? '1920x1080',
                'prompt' => $img['prompt'] ?? '',
            ];
        }
    }
}

if (empty($images)) {
    echo json_encode([
        'error' => 'prompt.html icinde gorsel promptu bulunamadi',
        'help' => 'Desteklenen formatlar: v10 HTML kartlar, IMAGES_JSON, veya data attributes',
        'template' => $template,
        'patterns_tried' => [
            'v10_html_cards' => 'span.font-medium + span.text-slate-400 + p.text-slate-300',
            'h4_format' => '<h4>file.jpg (size)</h4><p class="prompt">',
            'data_attrs' => 'data-image + data-size + data-prompt',
            'json' => '<!-- IMAGES_JSON: [...] -->',
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// images/ klasorunu olustur
$imagesDir = $templatePath . '/images';
if (!file_exists($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

// Leonardo.ai servisi
$leonardo = new LeonardoAIService();

// Sonuclar
$results = [
    'template' => $template,
    'total' => count($images),
    'generated' => [],
    'failed' => [],
];

foreach ($images as $image) {
    $filename = $image['filename'];
    $prompt = $image['prompt'];
    $size = $image['size'];

    // Boyut parse et
    $dimensions = explode('x', strtolower($size));
    $width = isset($dimensions[0]) ? (int)$dimensions[0] : 1920;
    $height = isset($dimensions[1]) ? (int)$dimensions[1] : 1080;

    // Leonardo.ai sinirlari
    $width = min(max($width, 512), 1536);
    $height = min(max($height, 512), 1536);

    echo json_encode(['status' => 'generating', 'file' => $filename, 'prompt' => substr($prompt, 0, 100) . '...']) . "\n";
    flush();

    try {
        // Gorsel uret
        $result = $leonardo->generateFromPrompt($prompt, [
            'width' => $width,
            'height' => $height,
            'style' => 'cinematic',
        ]);

        if ($result && !empty($result['content'])) {
            // Dosyayi kaydet
            $filePath = $imagesDir . '/' . $filename;
            file_put_contents($filePath, $result['content']);

            // Izinleri ayarla
            chmod($filePath, 0644);

            $results['generated'][] = [
                'filename' => $filename,
                'size' => filesize($filePath),
                'path' => '/design/templates/' . $template . '/images/' . $filename,
            ];
        } else {
            $results['failed'][] = [
                'filename' => $filename,
                'error' => 'Leonardo.ai gorsel uretemedi',
            ];
        }

    } catch (Exception $e) {
        $results['failed'][] = [
            'filename' => $filename,
            'error' => $e->getMessage(),
        ];
    }

    // Rate limit - her gorsel arasinda bekle
    sleep(2);
}

// Klasor izinlerini duzelt
exec("chown -R tuufi.com_:psaserv " . escapeshellarg($imagesDir));

echo "\n" . json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
