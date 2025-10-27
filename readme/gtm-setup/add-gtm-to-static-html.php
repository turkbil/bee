<?php
/**
 * GTM (Google Tag Manager) Static HTML İnjector - DYNAMIC VERSION
 *
 * Static HTML dosyalarına GTM kodlarını otomatik ekler
 * Setting'den dinamik olarak GTM ID alır (tenant-aware)
 *
 * Kullanım:
 * php readme/gtm-setup/add-gtm-to-static-html.php
 * php readme/gtm-setup/add-gtm-to-static-html.php --tenant=2
 * php readme/gtm-setup/add-gtm-to-static-html.php --tenant=3 --force
 */

// CLI parametrelerini parse et
$options = getopt('', ['tenant::', 'force::']);
$tenantId = $options['tenant'] ?? 2; // Varsayılan: tenant 2 (ixtif.com)
$forceUpdate = isset($options['force']); // Force update (zaten GTM varsa değiştir)

echo "🚀 GTM Static HTML Injector - Dynamic Version\n";
echo "═══════════════════════════════════════════════\n";

// Laravel'i bootstrap et
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Tenant'ı başlat
echo "📍 Tenant başlatılıyor: ID $tenantId\n";

try {
    tenancy()->initialize($tenantId);
    $currentTenant = tenant();

    if (!$currentTenant) {
        echo "❌ HATA: Tenant $tenantId bulunamadı!\n";
        exit(1);
    }

    echo "✅ Tenant başlatıldı: {$currentTenant->id}\n";
} catch (\Exception $e) {
    echo "❌ HATA: Tenant başlatılamadı: {$e->getMessage()}\n";
    exit(1);
}

// GTM Container ID'yi setting'den al
echo "🔍 GTM Container ID alınıyor (setting)...\n";

$gtmId = setting('seo_google_tag_manager_id');

if (!$gtmId) {
    echo "❌ HATA: GTM Container ID bulunamadı!\n";
    echo "⚠️  Lütfen admin panelden 'SEO Ayarları' > 'Google Tag Manager Container ID' ayarını yapın.\n";
    echo "💡 Veya manuel olarak eklemek için:\n";
    echo "   php artisan tinker\n";
    echo "   tenancy()->initialize($tenantId);\n";
    echo "   setting_update('seo_google_tag_manager_id', 'GTM-XXXXXXXX');\n";
    exit(1);
}

echo "✅ GTM Container ID: $gtmId\n\n";

// GTM Head Script
$gtmHeadScript = <<<HTML
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','$gtmId');</script>
<!-- End Google Tag Manager -->
HTML;

// GTM Body Noscript
$gtmBodyScript = <<<HTML
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=$gtmId"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
HTML;

// Static HTML dosyalarını bul
$directories = [
    'public/design/hakkimizda-alternatifler',
    'public/ixtif-designs',
    // İhtiyaca göre daha fazla klasör eklenebilir
];

$updatedFiles = [];
$skippedFiles = [];
$replacedFiles = [];

echo "📂 Dosyalar taranıyor...\n\n";

foreach ($directories as $directory) {
    $fullPath = __DIR__ . '/../../' . $directory;

    if (!is_dir($fullPath)) {
        echo "⚠️  Klasör bulunamadı: $directory\n";
        continue;
    }

    $files = glob($fullPath . '/*.html');

    foreach ($files as $file) {
        $content = file_get_contents($file);
        $originalContent = $content;

        // Zaten GTM var mı kontrol et
        $hasGTM = strpos($content, 'googletagmanager.com/gtm.js') !== false;

        if ($hasGTM) {
            if (!$forceUpdate) {
                $skippedFiles[] = basename($file);
                continue;
            } else {
                // Force update: Eski GTM kodlarını temizle
                // Head script'i temizle
                $content = preg_replace(
                    '/<!-- Google Tag Manager -->.*?<!-- End Google Tag Manager -->/s',
                    '',
                    $content
                );
                // Body noscript'i temizle
                $content = preg_replace(
                    '/<!-- Google Tag Manager \(noscript\) -->.*?<!-- End Google Tag Manager \(noscript\) -->/s',
                    '',
                    $content
                );
                $replacedFiles[] = basename($file);
            }
        }

        // Head içine GTM ekle (</head> tag'inden önce)
        if (strpos($content, '</head>') !== false) {
            $content = str_replace('</head>', $gtmHeadScript . "\n</head>", $content);
        } else {
            echo "⚠️  " . basename($file) . " dosyasında </head> tag'i bulunamadı!\n";
            continue;
        }

        // Body içine GTM noscript ekle (<body> tag'inden sonra)
        if (preg_match('/<body[^>]*>/i', $content, $matches)) {
            $bodyTag = $matches[0];
            $content = str_replace($bodyTag, $bodyTag . "\n" . $gtmBodyScript, $content);
        } else {
            echo "⚠️  " . basename($file) . " dosyasında <body> tag'i bulunamadı!\n";
            continue;
        }

        // Dosyayı kaydet (sadece değişiklik varsa)
        if ($content !== $originalContent) {
            file_put_contents($file, $content);

            if (!$hasGTM) {
                $updatedFiles[] = basename($file);
            }
        }
    }
}

// Rapor
echo "\n";
echo "✅ GTM Injection Tamamlandı!\n";
echo "═══════════════════════════════════════\n";
echo "Tenant ID: $tenantId\n";
echo "GTM Container ID: $gtmId (from setting)\n";
echo "Yeni eklenen: " . count($updatedFiles) . "\n";
echo "Güncellenen: " . count($replacedFiles) . "\n";
echo "Atlanan: " . count($skippedFiles) . "\n";
echo "\n";

if (!empty($updatedFiles)) {
    echo "📝 Yeni Eklenen Dosyalar:\n";
    foreach ($updatedFiles as $file) {
        echo "  ✓ $file\n";
    }
}

if (!empty($replacedFiles)) {
    echo "\n🔄 Güncellenen Dosyalar (force mode):\n";
    foreach ($replacedFiles as $file) {
        echo "  ↻ $file\n";
    }
}

if (!empty($skippedFiles)) {
    echo "\n⏭️  Atlanan Dosyalar (GTM zaten mevcut):\n";
    foreach ($skippedFiles as $file) {
        echo "  - $file\n";
    }
    echo "\n💡 Güncelleme için: php readme/gtm-setup/add-gtm-to-static-html.php --force\n";
}

echo "\n";
echo "🔍 Test Etmek İçin:\n";
echo "curl -s https://ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html | grep '$gtmId'\n";
echo "\n";
echo "📚 Kullanım:\n";
echo "  php readme/gtm-setup/add-gtm-to-static-html.php                 # Tenant 2 (ixtif.com)\n";
echo "  php readme/gtm-setup/add-gtm-to-static-html.php --tenant=3      # Tenant 3 (ixtif.com.tr)\n";
echo "  php readme/gtm-setup/add-gtm-to-static-html.php --force         # Mevcut GTM kodlarını güncelle\n";
echo "\n";
