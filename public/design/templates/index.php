<?php
/**
 * Hazir Taslaklar - Full Width Tasarim Galerisi
 * API Mode: POST ile metadata kaydet
 */

// API Mode - POST ile metadata kaydet VE silme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['api'])) {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? null;
    $metadataFile = __DIR__ . '/metadata.json';

    // Silme işlemi
    if ($action === 'delete') {
        // Şifre hash'i - template-action.php ile aynı
        define('DELETE_PASSWORD_HASH', '$2y$10$yfVqtNkw3aNQrlLGJ5i18ekhDZv94reeaQ.yZOrK/Fu9xB1Oq.JBq');

        $password = $input['password'] ?? '';
        $templateId = $input['templateId'] ?? '';

        // Şifre kontrolü
        if (!password_verify($password, DELETE_PASSWORD_HASH)) {
            http_response_code(403);
            die(json_encode(['success' => false, 'error' => 'Yanlis sifre!']));
        }

        // Template ID kontrolü
        if (empty($templateId) || !preg_match('/^[a-z0-9\-]+\/v[0-9]+[a-z0-9\-]*$/i', $templateId)) {
            http_response_code(400);
            die(json_encode(['success' => false, 'error' => 'Gecersiz template ID']));
        }

        // Klasör yolu
        $templatePath = __DIR__ . '/' . $templateId;

        // Güvenlik kontrolü
        $realPath = realpath($templatePath);
        $realBase = realpath(__DIR__);

        if (!$realPath || strpos($realPath, $realBase) !== 0) {
            http_response_code(400);
            die(json_encode(['success' => false, 'error' => 'Gecersiz yol']));
        }

        // Klasör var mı?
        if (!is_dir($templatePath)) {
            http_response_code(404);
            die(json_encode(['success' => false, 'error' => 'Klasor bulunamadi']));
        }

        // Recursive silme fonksiyonu
        function deleteDirectory($dir) {
            if (!is_dir($dir)) return false;
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . '/' . $file;
                is_dir($path) ? deleteDirectory($path) : unlink($path);
            }
            return rmdir($dir);
        }

        // Sil
        if (deleteDirectory($templatePath)) {
            // Kategori klasörü boş kaldıysa onu da sil
            $categoryPath = dirname($templatePath);
            $remaining = array_diff(scandir($categoryPath), ['.', '..']);
            if (empty($remaining)) {
                rmdir($categoryPath);
            }

            // Cache'i temizle
            $cacheFile = __DIR__ . '/.designs-cache.json';
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }

            die(json_encode(['success' => true, 'message' => 'Taslak silindi: ' . $templateId]));
        } else {
            http_response_code(500);
            die(json_encode(['success' => false, 'error' => 'Silme islemi basarisiz']));
        }
    }

    if ($action === 'saveMetadata') {
        $templateId = $input['templateId'] ?? null;
        $metadata = $input['metadata'] ?? null;

        if (!$templateId || !is_array($metadata)) {
            http_response_code(400);
            die(json_encode(['success' => false, 'error' => 'Invalid input']));
        }

        $allMetadata = [];
        if (file_exists($metadataFile)) {
            $content = @file_get_contents($metadataFile);
            if ($content) {
                $allMetadata = json_decode($content, true) ?: [];
            }
        }

        $allMetadata[$templateId] = $metadata;

        $json = json_encode($allMetadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($metadataFile, $json, LOCK_EX) === false) {
            http_response_code(500);
            die(json_encode(['success' => false, 'error' => 'Write failed']));
        }

        die(json_encode(['success' => true]));
    }

    if ($action === 'saveAllMetadata') {
        $allData = $input['data'] ?? null;

        if (!is_array($allData)) {
            http_response_code(400);
            die(json_encode(['success' => false, 'error' => 'Invalid data']));
        }

        $json = json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($metadataFile, $json, LOCK_EX) === false) {
            http_response_code(500);
            die(json_encode(['success' => false, 'error' => 'Write failed']));
        }

        die(json_encode(['success' => true]));
    }

    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid action']));
}

// Normal sayfa modu
$baseDir = __DIR__;
$baseUrl = '/design/templates';

// Cache sistemi - 5 dakika cache
$cacheFile = $baseDir . '/.designs-cache.json';
$cacheLifetime = 300; // 5 dakika
$useCache = false;

if (file_exists($cacheFile)) {
    $cacheAge = time() - filemtime($cacheFile);
    if ($cacheAge < $cacheLifetime) {
        $useCache = true;
    }
}

$categories = [];
$allDesigns = [];

if ($useCache) {
    // Cache'den yükle
    $cached = json_decode(file_get_contents($cacheFile), true);
    $categories = $cached['categories'] ?? [];
    $allDesigns = $cached['allDesigns'] ?? [];
} else {
    // Klasörleri tara (normal işlem)

foreach (glob("$baseDir/*", GLOB_ONLYDIR) as $categoryPath) {
    $categoryName = basename($categoryPath);
    $versions = [];

    foreach (glob("$categoryPath/*", GLOB_ONLYDIR) as $versionPath) {
        $versionName = basename($versionPath);
        $indexFile = "$versionPath/index.html";

        if (file_exists($indexFile)) {
            $html = @file_get_contents($indexFile);
            if ($html) {
                preg_match('/<title>(.*?)<\/title>/iu', $html, $matches);
                $title = !empty($matches[1]) ? html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8') : $versionName;
            } else {
                $title = $versionName;
            }

            $promptFile = "$versionPath/prompt.html";
            $hasPrompt = file_exists($promptFile);

            $designId = $categoryName . '/' . $versionName;
            $design = [
                'id' => $designId,
                'name' => $versionName,
                'title' => $title,
                'category' => $categoryName,
                'categoryDisplay' => ucwords(str_replace('-', ' ', $categoryName)),
                'url' => "$baseUrl/$categoryName/$versionName/",
                'promptUrl' => $hasPrompt ? "$baseUrl/$categoryName/$versionName/prompt.html" : null,
                'hasPrompt' => $hasPrompt,
                'modified' => filemtime($indexFile)
            ];

            $versions[] = $design;
            $allDesigns[] = $design;
        }
    }

    if (!empty($versions)) {
        usort($versions, fn($a, $b) => strcmp($a['name'], $b['name']));
        $categories[] = [
            'name' => $categoryName,
            'displayName' => ucwords(str_replace('-', ' ', $categoryName)),
            'versions' => $versions,
            'count' => count($versions)
        ];
    }
}

    usort($categories, fn($a, $b) => strcmp($a['name'], $b['name']));

    // Cache'e kaydet
    file_put_contents($cacheFile, json_encode([
        'categories' => $categories,
        'allDesigns' => $allDesigns
    ], JSON_UNESCAPED_UNICODE), LOCK_EX);
}

$totalDesigns = count($allDesigns);
$promptedDesigns = count(array_filter($allDesigns, fn($d) => $d['hasPrompt']));
$designsJson = json_encode($allDesigns, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Tasarim Merkezi | Design Templates</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Nunito', system-ui, sans-serif; }

        /* Card */
        .design-card { transition: border-color 0.2s, box-shadow 0.2s; }
        .design-card:hover { border-color: #6366f1; box-shadow: 0 4px 20px -5px rgba(99, 102, 241, 0.2); }
        .design-card.favorite { border-color: #f59e0b !important; }
        .design-card.favorite .fav-btn { color: #f59e0b !important; }

        /* Fav button */
        .fav-btn { transition: color 0.15s; }

        /* Stars */
        .star-rating { display: flex; gap: 2px; }
        .star-rating i { cursor: pointer; transition: color 0.15s; }
        .star-rating:hover i { color: #fbbf24; }
        .star-rating:hover i:hover ~ i { color: #334155; }

        /* Action buttons */
        .action-btn { transition: background-color 0.15s, color 0.15s; }

        /* Toast */
        .toast {
            position: fixed; bottom: 2rem; right: 2rem;
            background: #10b981; color: white;
            padding: 0.5rem 1rem; border-radius: 0.5rem;
            transform: translateY(100px); opacity: 0;
            transition: all 0.3s; z-index: 9999;
            font-size: 0.875rem;
        }
        .toast.show { transform: translateY(0); opacity: 1; }

        /* Inputs */
        .small-input {
            background: transparent; border: none;
            border-bottom: 1px dashed #475569;
            padding: 4px 0; font-size: 13px;
            color: #94a3b8; width: 100%; outline: none;
            transition: border-color 0.2s;
        }
        .small-input:focus { border-color: #8b5cf6; color: white; }

        /* Details */
        .details-section { display: none; }
        .details-section.show { display: block; }

        /* Tooltip (altında) */
        .tooltip { position: relative; }
        .tooltip::after {
            content: attr(data-tip);
            position: absolute;
            bottom: -26px; left: 50%;
            transform: translateX(-50%);
            background: #0f172a; border: 1px solid #334155;
            color: #e2e8f0;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s;
            z-index: 50;
        }
        .tooltip:hover::after { opacity: 1; }

        /* Tooltip (üstünde) */
        .tooltip-top { position: relative; }
        .tooltip-top::after {
            content: attr(data-tip);
            position: absolute;
            top: -28px; left: 50%;
            transform: translateX(-50%);
            background: #0f172a; border: 1px solid #334155;
            color: #e2e8f0;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s;
            z-index: 50;
        }
        .tooltip-top:hover::after { opacity: 1; }

        /* Drag & Drop */
        .design-card { cursor: grab; }
        .design-card.dragging { opacity: 0.3; cursor: grabbing; }
        .drop-zone {
            transition: all 0.2s;
            border: 2px dashed #475569;
            min-width: 120px;
            text-align: center;
        }
        .drop-zone:hover { border-color: #64748b; }
        .drop-zone.drag-over {
            border-color: #a78bfa;
            background: rgba(139, 92, 246, 0.2);
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.3);
        }
        .drop-panel {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(to top, rgba(15,23,42,0.98), rgba(15,23,42,0.95));
            border-top: 2px solid #6366f1;
            padding: 1.5rem 2rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
            z-index: 60;
            backdrop-filter: blur(8px);
        }
        .drop-panel.visible { transform: translateY(0); }

        /* Drag ghost (küçük kart) */
        .drag-ghost {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            background: #1e293b;
            border: 2px solid #8b5cf6;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 12px;
            color: #e2e8f0;
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen">

    <!-- Toast -->
    <div id="toast" class="toast"><i class="fas fa-check-circle mr-2"></i><span></span></div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden items-center justify-center">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-6 max-w-sm w-full mx-4">
            <div class="text-center mb-4">
                <i class="fas fa-trash-alt text-red-500 text-3xl mb-3"></i>
                <h3 class="text-lg font-bold text-white">Sil</h3>
                <p class="text-slate-400 text-sm" id="deleteModalTitle"></p>
            </div>
            <input type="text" id="deletePassword" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm mb-2" placeholder="Sifre" autocomplete="off">
            <p id="deleteError" class="text-red-500 text-xs mb-3 hidden"></p>
            <div class="flex gap-2">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-slate-800 rounded-lg text-slate-300 text-sm">Vazgec</button>
                <button onclick="executeDelete()" id="deleteBtn" class="flex-1 px-4 py-2 bg-red-600 rounded-lg text-white text-sm">Sil</button>
            </div>
        </div>
    </div>

    <!-- Add Kategori/Marka Modal -->
    <div id="addModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden items-center justify-center">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-6 max-w-sm w-full mx-4">
            <div class="text-center mb-4">
                <i id="addModalIcon" class="fas fa-folder-plus text-emerald-400 text-3xl mb-3"></i>
                <h3 class="text-lg font-bold text-white" id="addModalTitle">Kategori Ekle</h3>
            </div>
            <input type="text" id="addModalInput" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm mb-4" placeholder="Isim girin..." autocomplete="off">
            <div class="flex gap-2">
                <button onclick="closeAddModal()" class="flex-1 px-4 py-2 bg-slate-800 rounded-lg text-slate-300 text-sm">Vazgec</button>
                <button onclick="executeAdd()" id="addBtn" class="flex-1 px-4 py-2 bg-emerald-600 rounded-lg text-white text-sm">Ekle</button>
            </div>
        </div>
    </div>

    <!-- Manage Kategori/Marka Modal -->
    <div id="manageModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden items-center justify-center">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-6 max-w-lg w-full mx-4 max-h-[80vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-tags text-violet-400 mr-2"></i>Kategori & Marka</h3>
                <button onclick="closeManageModal()" class="text-slate-500 hover:text-white"><i class="fas fa-times"></i></button>
            </div>

            <div class="grid grid-cols-2 gap-6 overflow-y-auto flex-1">
                <!-- Kategoriler -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-emerald-400"><i class="fas fa-folder mr-2"></i>Kategoriler</span>
                        <button onclick="closeManageModal(); openAddModal('kategori')" class="text-xs text-slate-500 hover:text-emerald-400"><i class="fas fa-plus"></i></button>
                    </div>
                    <div id="manageKategoriList" class="space-y-2"></div>
                </div>

                <!-- Markalar -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-blue-400"><i class="fas fa-building mr-2"></i>Markalar</span>
                        <button onclick="closeManageModal(); openAddModal('marka')" class="text-xs text-slate-500 hover:text-blue-400"><i class="fas fa-plus"></i></button>
                    </div>
                    <div id="manageMarkaList" class="space-y-2"></div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-t border-slate-800 text-center">
                <p class="text-xs text-slate-600"><i class="fas fa-info-circle mr-1"></i>Silmek için X'e tıkla. Kartları sürükleyerek de atama yapabilirsin.</p>
            </div>
        </div>
    </div>

    <!-- Drop Panel (Drag sırasında görünür) -->
    <div id="dropPanel" class="drop-panel">
        <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-hand-pointer text-violet-400 text-xl"></i>
            <span class="text-base text-white font-medium">Bırakmak için sürükle</span>
            <span id="draggedTitle" class="ml-auto text-sm text-violet-300 bg-violet-900/50 px-3 py-1.5 rounded-lg"></span>
        </div>
        <div class="grid grid-cols-2 gap-6">
            <!-- Kategoriler -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-folder text-emerald-400"></i>
                    <span class="text-sm text-slate-400 font-medium">Kategoriler</span>
                </div>
                <div id="kategoriDropZones" class="flex flex-wrap gap-3"></div>
            </div>
            <!-- Markalar -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-building text-blue-400"></i>
                    <span class="text-sm text-slate-400 font-medium">Markalar</span>
                </div>
                <div id="markaDropZones" class="flex flex-wrap gap-3"></div>
            </div>
        </div>
    </div>

    <!-- Drag Ghost Element -->
    <div id="dragGhost" class="drag-ghost" style="display: none;"></div>

    <!-- Header -->
    <header class="border-b border-slate-800 sticky top-0 bg-slate-950/95 backdrop-blur z-50">
        <div class="w-full px-4 py-2.5 flex items-center gap-2 overflow-visible">
            <!-- Tabs -->
            <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-lg shrink-0">
                <a href="/design/templates/" class="px-3 py-1.5 rounded-md text-sm font-medium bg-violet-600 text-white">Taslaklar</a>
                <a href="/design/sectors/" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800">Tarz</a>
                <a href="/design/prompt/" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800">Prompt</a>
            </div>

            <span class="text-slate-700">|</span>

            <!-- Filters -->
            <button onclick="filterDesigns('all')" class="filter-btn active px-4 py-2 text-sm rounded-lg bg-slate-800 text-white shrink-0">Tümü</button>
            <button onclick="filterDesigns('favorites')" class="filter-btn tooltip px-3 py-2 text-sm rounded-lg bg-slate-900 text-slate-400 hover:bg-slate-800 shrink-0" data-tip="Favoriler"><i class="fas fa-heart text-amber-400"></i></button>
            <button onclick="filterDesigns('rated')" class="filter-btn tooltip px-3 py-2 text-sm rounded-lg bg-slate-900 text-slate-400 hover:bg-slate-800 shrink-0" data-tip="Puanlılar"><i class="fas fa-star text-yellow-400"></i></button>
            <button onclick="filterDesigns('noted')" class="filter-btn tooltip px-3 py-2 text-sm rounded-lg bg-slate-900 text-slate-400 hover:bg-slate-800 shrink-0" data-tip="Notlular"><i class="fas fa-sticky-note text-emerald-400"></i></button>
            <button onclick="filterDesigns('prompted')" class="filter-btn tooltip px-3 py-2 text-sm rounded-lg bg-slate-900 text-slate-400 hover:bg-slate-800 shrink-0" data-tip="Promptlu"><i class="fas fa-terminal text-violet-400"></i></button>

            <!-- Filtre Dropdown'ları -->
            <div class="tooltip shrink-0" data-tip="Kategoriye göre filtrele">
                <select id="filterKategori" onchange="filterByKategori()" class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg px-3 py-2 cursor-pointer hover:border-slate-600">
                    <option value="">Kategori</option>
                </select>
            </div>
            <div class="tooltip shrink-0" data-tip="Markaya göre filtrele">
                <select id="filterMarka" onchange="filterByMarka()" class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg px-3 py-2 cursor-pointer hover:border-slate-600">
                    <option value="">Marka</option>
                </select>
            </div>

            <span class="text-slate-700">|</span>

            <!-- Sıralama & Görünüm -->
            <div class="tooltip shrink-0" data-tip="Sıralama düzeni">
                <select id="sortOrder" onchange="sortDesigns()" class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg px-3 py-2 cursor-pointer hover:border-slate-600">
                    <option value="date-desc" selected>Yeni</option>
                    <option value="date-asc">Eski</option>
                    <option value="alpha">A-Z</option>
                    <option value="alpha-desc">Z-A</option>
                </select>
            </div>

            <button onclick="toggleAllDetails()" class="tooltip px-3 py-2 text-sm rounded-lg bg-violet-900/50 text-violet-300 hover:bg-violet-800 shrink-0" data-tip="Tüm detayları aç/kapat">
                <i class="fas fa-eye" id="toggleAllIcon"></i>
            </button>

            <span class="text-slate-700">|</span>

            <!-- Yönetim -->
            <button onclick="openManageModal()" class="tooltip px-3 py-2 text-sm rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 shrink-0" data-tip="Kategori ve marka yönetimi">
                <i class="fas fa-tags"></i>
            </button>

            <!-- Stats -->
            <div class="ml-auto flex items-center gap-2 text-xs shrink-0">
                <span class="tooltip cursor-default" data-tip="Promptlu Tasarım Sayısı">
                    <span class="text-violet-400"><?= $promptedDesigns ?></span>
                    <span class="text-slate-500 ml-0.5">prompt</span>
                </span>
                <span class="text-slate-700">|</span>
                <span class="tooltip cursor-default" data-tip="Favori Sayısı">
                    <span class="text-amber-400" id="favCount">0</span>
                    <span class="text-slate-500 ml-0.5">favori</span>
                </span>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="w-full px-6 py-6">
        <?php if (empty($categories)): ?>
            <div class="text-center py-20">
                <i class="fas fa-folder-open text-6xl text-slate-700 mb-4"></i>
                <p class="text-slate-500">Henuz tasarim yok</p>
            </div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6" id="designsGrid">
                <?php foreach ($categories as $category): ?>
                    <?php foreach ($category['versions'] as $design): ?>
                        <div class="design-card group relative bg-slate-900 border border-slate-800 rounded-xl overflow-hidden hover:border-violet-500/50 transition-all"
                             data-id="<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>"
                             data-category="<?= htmlspecialchars($design['category'], ENT_QUOTES, 'UTF-8') ?>"
                             data-hasprompt="<?= $design['hasPrompt'] ? 'true' : 'false' ?>">

                            <!-- Header -->
                            <div class="p-4 pb-3">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-xs font-medium text-violet-400 bg-violet-500/20 px-2 py-0.5 rounded">
                                        <?= htmlspecialchars($design['categoryDisplay'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <div class="star-rating tooltip" data-id="<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>" data-tip="Puanla (1-5)">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="star-icon fas fa-star text-slate-700 text-xs hover:text-yellow-400 transition-colors" data-rating="<?= $i ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <button class="fav-btn tooltip text-slate-600 hover:text-amber-400 transition text-sm" data-tip="Favorilere ekle">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>
                                </div>
                                <a href="<?= $design['url'] ?>" target="_blank" class="block">
                                    <h3 class="text-sm font-semibold text-white leading-snug line-clamp-2 hover:text-violet-400 transition-colors" title="<?= htmlspecialchars($design['title'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($design['title'], ENT_QUOTES, 'UTF-8') ?>
                                    </h3>
                                </a>
                            </div>

                            <!-- Hover Actions (sağ alt) -->
                            <div class="absolute bottom-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button class="toggle-details-btn tooltip-top w-6 h-6 flex items-center justify-center rounded bg-violet-600 hover:bg-violet-500 text-white text-xs"
                                        data-id="<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-tip="Detayları göster">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($design['hasPrompt']): ?>
                                <a href="<?= $design['promptUrl'] ?>" target="_blank" onclick="event.stopPropagation()" class="tooltip-top w-6 h-6 flex items-center justify-center rounded bg-violet-600 hover:bg-violet-500 text-white text-xs" data-tip="Prompt dosyasını aç">
                                    <i class="fas fa-terminal"></i>
                                </a>
                                <?php endif; ?>
                                <button class="delete-btn tooltip-top w-6 h-6 flex items-center justify-center rounded bg-slate-700 hover:bg-red-600 text-slate-400 hover:text-white text-xs"
                                        data-id="<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-title="<?= htmlspecialchars($design['title'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-tip="Taslağı sil">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>

                            <!-- Details (Hidden) -->
                            <div class="details-section border-t border-slate-800/50" data-details="<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <div class="p-5 space-y-4">
                                    <!-- Kategori & Marka -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs text-slate-500 block mb-2">Kategori</label>
                                            <input type="text"
                                                   class="small-input kategori-input"
                                                   placeholder="Seç veya yaz..."
                                                   list="kategoriler-<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>"
                                                   data-id="<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>">
                                            <datalist id="kategoriler-<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>" class="kategori-datalist"></datalist>
                                        </div>
                                        <div>
                                            <label class="text-xs text-slate-500 block mb-2">Marka</label>
                                            <input type="text"
                                                   class="small-input marka-input"
                                                   placeholder="Seç veya yaz..."
                                                   list="markalar-<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>"
                                                   data-id="<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>">
                                            <datalist id="markalar-<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>" class="marka-datalist"></datalist>
                                        </div>
                                    </div>
                                    <!-- Not -->
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-2">Not</label>
                                        <textarea class="w-full bg-slate-800/50 text-sm text-slate-300 resize-none rounded-lg p-3 note-input" rows="2" placeholder="..." data-id="<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Indicators (kategori/marka varsa göster) -->
                            <div class="indicators px-4 py-1.5 hidden" data-indicators="<?= htmlspecialchars($design['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="kategori-indicator text-xs bg-emerald-900/50 text-emerald-400 px-1.5 py-0.5 rounded hidden"></span>
                                    <span class="marka-indicator text-xs bg-blue-900/50 text-blue-400 px-1.5 py-0.5 rounded hidden"></span>
                                    <span class="not-indicator text-xs text-slate-500 hidden" title=""><i class="fas fa-sticky-note"></i></span>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
    const designs = <?= $designsJson ?>;
    let designData = {};
    let allDetailsOpen = false;

    // Escape helper for onclick attributes
    function escapeAttr(str) {
        return String(str).replace(/&/g, '&amp;').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
    }

    // Her kelimenin ilk harfini büyük yap (Türkçe destekli)
    function capitalizeWords(str) {
        return str.trim().split(' ').map(word => {
            if (!word) return '';
            return word.charAt(0).toLocaleUpperCase('tr-TR') + word.slice(1);
        }).join(' ');
    }

    // Metadata'yı JSON'dan yükle
    async function loadMetadata() {
        try {
            const res = await fetch('/design/templates/metadata.json?t=' + Date.now());
            if (res.ok) {
                designData = await res.json();

                // localStorage'dan kategoriler/markalar (backward compatibility)
                const oldKategoriler = JSON.parse(localStorage.getItem('designKategoriler') || '[]');
                const oldMarkalar = JSON.parse(localStorage.getItem('designMarkalar') || '[]');

                if (oldKategoriler.length || oldMarkalar.length) {
                    // localStorage'daki kategori/markaları global listeye ekle
                    window.kategoriler = oldKategoriler;
                    window.markalar = oldMarkalar;
                }

                // localStorage'dan eski veri varsa ve JSON boşsa → migration yap
                const oldData = JSON.parse(localStorage.getItem('designData') || '{}');
                if (Object.keys(oldData).length > 0 && Object.keys(designData).length === 0) {
                    console.log('🔄 localStorage verisi tespit edildi, JSON\'a taşınıyor...');
                    await migrateFromLocalStorage(oldData);
                }

                updateUI();
                updateFilterDropdowns();
            }
        } catch (e) {
            console.error('Metadata yüklenemedi:', e);
        }
    }

    // localStorage'dan JSON'a migration
    async function migrateFromLocalStorage(oldData) {
        try {
            const res = await fetch('/design/templates/?api=1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'saveAllMetadata',
                    data: oldData
                })
            });
            const result = await res.json();
            if (result.success) {
                designData = oldData;
                console.log('✅ Migration tamamlandı! localStorage verisi JSON\'a aktarıldı.');
                showToast('Verileriniz kalıcı depolamaya taşındı!');
                // localStorage'ı temizle (artık kullanmıyoruz)
                localStorage.removeItem('designData');
            }
        } catch (e) {
            console.error('Migration hatası:', e);
        }
    }

    // Metadata'yı kaydet (API'ye POST)
    async function saveMetadataToServer(templateId, metadata) {
        try {
            const res = await fetch('/design/templates/?api=1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'saveMetadata',
                    templateId: templateId,
                    metadata: metadata
                })
            });
            const result = await res.json();
            if (!result.success) {
                console.error('Metadata kaydedilemedi:', result.error);
            }
        } catch (e) {
            console.error('Metadata kaydetme hatası:', e);
        }
    }

    function toggleAllDetails() {
        const allDetails = document.querySelectorAll('.details-section');
        const icon = document.getElementById('toggleAllIcon');
        const btn = icon?.closest('button');
        allDetailsOpen = !allDetailsOpen;

        if (allDetailsOpen) {
            allDetails.forEach(d => d.classList.add('show'));
            icon?.classList.replace('fa-eye', 'fa-eye-slash');
            if (btn) btn.setAttribute('data-tip', 'Gizle');
        } else {
            allDetails.forEach(d => d.classList.remove('show'));
            icon?.classList.replace('fa-eye-slash', 'fa-eye');
            if (btn) btn.setAttribute('data-tip', 'Detaylar');
        }

        // Karttaki toggle butonlarını da güncelle
        updateAllToggleIcons();
    }

    function toggleCardDetails(id) {
        // Tüm kartların detaylarını aç/kapat
        toggleAllDetails();

        // Tüm karttaki toggle butonlarının ikonlarını güncelle
        updateAllToggleIcons();
    }

    function updateAllToggleIcons() {
        const allToggleBtns = document.querySelectorAll('.toggle-details-btn');
        allToggleBtns.forEach(btn => {
            const icon = btn.querySelector('i');
            if (!icon) return;

            if (allDetailsOpen) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                btn.title = 'Gizle';
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                btn.title = 'Detaylar';
            }
        });
    }

    function toggleFavorite(id) {
        if (!designData[id]) designData[id] = {};
        designData[id].favorite = !designData[id].favorite;
        saveData(id); updateUI();
        showToast(designData[id].favorite ? 'Favorilere eklendi' : 'Favorilerden cikarildi');
    }

    function setRating(id, rating) {
        if (!designData[id]) designData[id] = {};
        designData[id].rating = designData[id].rating === rating ? 0 : rating;
        saveData(id); updateUI();
    }

    function saveNote(id, note) {
        if (!designData[id]) designData[id] = {};
        designData[id].note = note.trim();
        saveData(id);
    }

    function saveKategori(id, kategori) {
        if (!designData[id]) designData[id] = {};
        designData[id].kategori = kategori.trim().toLowerCase();
        saveData(id); updateFilterDropdowns();
    }

    function saveMarka(id, marka) {
        if (!designData[id]) designData[id] = {};
        // Marka için ilk harfleri büyük yap
        designData[id].marka = capitalizeWords(marka);
        saveData(id); updateFilterDropdowns();
    }

    function updateFilterDropdowns() {
        const kategoriler = new Set();
        const markalar = new Set();
        Object.values(designData).forEach(d => {
            if (d.kategori) kategoriler.add(d.kategori);
            if (d.marka) markalar.add(d.marka);
        });

        const katSelect = document.getElementById('filterKategori');
        const selectedKat = katSelect.value;
        katSelect.innerHTML = '<option value="">Kategori</option>';
        [...kategoriler].sort().forEach(k => {
            katSelect.innerHTML += `<option value="${k}" ${k === selectedKat ? 'selected' : ''}>${k}</option>`;
        });

        const markaSelect = document.getElementById('filterMarka');
        const selectedMarka = markaSelect.value;
        markaSelect.innerHTML = '<option value="">Marka</option>';
        [...markalar].sort().forEach(m => {
            markaSelect.innerHTML += `<option value="${m}" ${m === selectedMarka ? 'selected' : ''}>${m}</option>`;
        });

        // Datalist'leri de güncelle (her kart için)
        updateDataLists(kategoriler, markalar);
    }

    function updateDataLists(kategoriler, markalar) {
        // Tüm kategori datalist'leri
        document.querySelectorAll('.kategori-datalist').forEach(datalist => {
            datalist.innerHTML = [...kategoriler].sort().map(k => `<option value="${k}">`).join('');
        });

        // Tüm marka datalist'leri
        document.querySelectorAll('.marka-datalist').forEach(datalist => {
            datalist.innerHTML = [...markalar].sort().map(m => `<option value="${m}">`).join('');
        });
    }

    function filterByKategori() {
        const value = document.getElementById('filterKategori').value;
        document.querySelectorAll('.design-card').forEach(card => {
            const data = designData[card.dataset.id] || {};
            card.style.display = (!value || data.kategori === value) ? '' : 'none';
        });
        document.getElementById('filterMarka').value = '';
        resetFilterButtons();
    }

    function filterByMarka() {
        const value = document.getElementById('filterMarka').value;
        document.querySelectorAll('.design-card').forEach(card => {
            const data = designData[card.dataset.id] || {};
            card.style.display = (!value || data.marka === value) ? '' : 'none';
        });
        document.getElementById('filterKategori').value = '';
        resetFilterButtons();
    }

    function resetFilterButtons() {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-slate-800', 'text-white');
            btn.classList.add('bg-slate-900', 'text-slate-400');
        });
    }

    function filterDesigns(filter) {
        document.getElementById('filterKategori').value = '';
        document.getElementById('filterMarka').value = '';

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-slate-800', 'text-white');
            btn.classList.add('bg-slate-900', 'text-slate-400');
        });
        event.target.classList.add('active', 'bg-slate-800', 'text-white');
        event.target.classList.remove('bg-slate-900', 'text-slate-400');

        document.querySelectorAll('.design-card').forEach(card => {
            const data = designData[card.dataset.id] || {};
            let show = true;
            switch(filter) {
                case 'favorites': show = data.favorite === true; break;
                case 'rated': show = data.rating > 0; break;
                case 'noted': show = data.note && data.note.length > 0; break;
                case 'prompted': show = card.dataset.hasprompt === 'true'; break;
            }
            card.style.display = show ? '' : 'none';
        });
    }

    function updateUI() {
        let favCount = 0;
        document.querySelectorAll('.design-card').forEach(card => {
            const id = card.dataset.id;
            const data = designData[id] || {};

            if (data.favorite) { card.classList.add('favorite'); favCount++; }
            else { card.classList.remove('favorite'); }

            // Basit yıldız sistemi (5 üzerinden)
            const rating = data.rating || 0;
            const stars = card.querySelectorAll('.star-rating i');
            stars.forEach((star, i) => {
                if (i < rating) {
                    star.classList.remove('text-slate-700');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-slate-700');
                }
            });

            const noteInput = card.querySelector('textarea');
            if (noteInput && data.note) noteInput.value = data.note;

            const kategoriInput = card.querySelector('.kategori-input');
            if (kategoriInput && data.kategori) kategoriInput.value = data.kategori;

            const markaInput = card.querySelector('.marka-input');
            if (markaInput && data.marka) markaInput.value = data.marka;

            // Göstergeleri güncelle (kategori, marka, not)
            const indicators = card.querySelector('.indicators');
            if (indicators) {
                const hasData = data.kategori || data.marka || data.note;
                indicators.classList.toggle('hidden', !hasData);

                const katInd = indicators.querySelector('.kategori-indicator');
                if (katInd) {
                    katInd.classList.toggle('hidden', !data.kategori);
                    katInd.textContent = data.kategori || '';
                }

                const markaInd = indicators.querySelector('.marka-indicator');
                if (markaInd) {
                    markaInd.classList.toggle('hidden', !data.marka);
                    markaInd.textContent = data.marka || '';
                }

                const notInd = indicators.querySelector('.not-indicator');
                if (notInd) {
                    notInd.classList.toggle('hidden', !data.note);
                    notInd.title = data.note || '';
                }
            }
        });
        document.getElementById('favCount').textContent = favCount;
    }

    function saveData(templateId) {
        // Tek template'i kaydet (performans için)
        if (templateId && designData[templateId]) {
            saveMetadataToServer(templateId, designData[templateId]);
        }
    }

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.querySelector('span').textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2000);
    }

    // Delete
    let deleteTargetId = null;

    function confirmDelete(id, title) {
        deleteTargetId = id;
        document.getElementById('deleteModalTitle').textContent = title;
        document.getElementById('deletePassword').value = '';
        document.getElementById('deleteError').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
        document.getElementById('deletePassword').focus();
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }

    async function executeDelete() {
        const password = document.getElementById('deletePassword').value;
        const errorEl = document.getElementById('deleteError');
        const btn = document.getElementById('deleteBtn');

        if (!password) { errorEl.textContent = 'Sifre gerekli'; errorEl.classList.remove('hidden'); return; }

        btn.disabled = true; btn.textContent = '...';

        try {
            const res = await fetch('/design/templates/?api=1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', templateId: deleteTargetId, password })
            });
            const result = await res.json();

            if (result.success) {
                closeDeleteModal();
                showToast('Silindi');
                const card = document.querySelector(`[data-id="${deleteTargetId}"]`);
                if (card) { card.style.opacity = '0'; setTimeout(() => card.remove(), 200); }
            } else {
                errorEl.textContent = result.error || 'Hata';
                errorEl.classList.remove('hidden');
            }
        } catch (e) {
            errorEl.textContent = 'Baglanti hatasi';
            errorEl.classList.remove('hidden');
        }
        btn.disabled = false; btn.textContent = 'Sil';
    }

    document.getElementById('deletePassword').addEventListener('keypress', e => { if (e.key === 'Enter') executeDelete(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeDeleteModal(); closeAddModal(); closeManageModal(); } });

    function sortDesigns() {
        const grid = document.getElementById('designsGrid');
        const cards = Array.from(grid.querySelectorAll('.design-card'));
        const order = document.getElementById('sortOrder').value;

        cards.sort((a, b) => {
            const aData = designs.find(d => d.id === a.dataset.id);
            const bData = designs.find(d => d.id === b.dataset.id);

            switch(order) {
                case 'alpha':
                    return aData.title.localeCompare(bData.title, 'tr');
                case 'alpha-desc':
                    return bData.title.localeCompare(aData.title, 'tr');
                case 'date-desc':
                    return bData.modified - aData.modified;
                case 'date-asc':
                    return aData.modified - bData.modified;
                default:
                    return 0;
            }
        });

        cards.forEach(card => grid.appendChild(card));
    }

    // ============================================
    // ADD MODAL (Kategori/Marka Ekleme)
    // ============================================
    let addModalType = 'kategori';

    function openAddModal(type) {
        addModalType = type;
        const modal = document.getElementById('addModal');
        const icon = document.getElementById('addModalIcon');
        const title = document.getElementById('addModalTitle');
        const btn = document.getElementById('addBtn');
        const input = document.getElementById('addModalInput');

        if (type === 'kategori') {
            icon.className = 'fas fa-folder-plus text-emerald-400 text-3xl mb-3';
            title.textContent = 'Kategori Ekle';
            btn.className = 'flex-1 px-4 py-2 bg-emerald-600 rounded-lg text-white text-sm';
            input.placeholder = 'orn: saglik, belediye, fabrika...';
        } else {
            icon.className = 'fas fa-building text-blue-400 text-3xl mb-3';
            title.textContent = 'Marka Ekle';
            btn.className = 'flex-1 px-4 py-2 bg-blue-600 rounded-lg text-white text-sm';
            input.placeholder = 'orn: Apple, Nike, Acme Corp...';
        }

        input.value = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        input.focus();
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.getElementById('addModal').classList.remove('flex');
    }

    function executeAdd() {
        const input = document.getElementById('addModalInput');
        const value = input.value.trim();
        if (!value) return;

        // Global listeye ekle
        const listKey = addModalType === 'kategori' ? 'kategoriler' : 'markalar';
        let list = window[listKey] || [];
        // Kategori: lowercase, Marka: capitalize
        const normalizedValue = addModalType === 'kategori' ? value.toLowerCase() : capitalizeWords(value);

        if (!list.includes(normalizedValue)) {
            list.push(normalizedValue);
            list.sort();
            window[listKey] = list;
            showToast(`${addModalType === 'kategori' ? 'Kategori' : 'Marka'} eklendi: ${value}`);
            updateDropZones();
            updateFilterDropdowns();
        } else {
            showToast('Zaten mevcut!');
        }
        closeAddModal();
    }

    document.getElementById('addModalInput').addEventListener('keypress', e => { if (e.key === 'Enter') executeAdd(); });

    // ============================================
    // MANAGE MODAL (Kategori/Marka Yönetimi)
    // ============================================
    function openManageModal() {
        updateManageLists();
        document.getElementById('manageModal').classList.remove('hidden');
        document.getElementById('manageModal').classList.add('flex');
    }

    function closeManageModal() {
        document.getElementById('manageModal').classList.add('hidden');
        document.getElementById('manageModal').classList.remove('flex');
    }

    function updateManageLists() {
        // Kategorileri al
        let kategoriler = window.kategoriler || [];
        Object.values(designData).forEach(d => {
            if (d.kategori && !kategoriler.includes(d.kategori)) kategoriler.push(d.kategori);
        });
        kategoriler = [...new Set(kategoriler)].sort();
        window.kategoriler = kategoriler;

        // Markaları al
        let markalar = window.markalar || [];
        Object.values(designData).forEach(d => {
            if (d.marka && !markalar.includes(d.marka)) markalar.push(d.marka);
        });
        markalar = [...new Set(markalar)].sort();
        window.markalar = markalar;

        // Kategori listesi
        const katList = document.getElementById('manageKategoriList');
        katList.innerHTML = kategoriler.length ? kategoriler.map(k => {
            const count = Object.values(designData).filter(d => d.kategori === k).length;
            return `
                <div class="flex items-center justify-between bg-slate-800/50 rounded-lg px-3 py-2 group">
                    <span class="text-sm text-slate-300"><i class="fas fa-folder text-emerald-500 mr-2 text-xs"></i>${k}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-600">${count}</span>
                        <button onclick="editTag('kategori', '${escapeAttr(k)}')" class="text-slate-600 hover:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity" title="Düzenle">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <button onclick="deleteTagFromModal('kategori', '${escapeAttr(k)}')" class="text-slate-600 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-opacity" title="Sil">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('') : '<p class="text-sm text-slate-600 italic">Henuz kategori yok</p>';

        // Marka listesi
        const markaList = document.getElementById('manageMarkaList');
        markaList.innerHTML = markalar.length ? markalar.map(m => {
            const count = Object.values(designData).filter(d => d.marka === m).length;
            return `
                <div class="flex items-center justify-between bg-slate-800/50 rounded-lg px-3 py-2 group">
                    <span class="text-sm text-slate-300"><i class="fas fa-building text-blue-500 mr-2 text-xs"></i>${m}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-600">${count}</span>
                        <button onclick="editTag('marka', '${escapeAttr(m)}')" class="text-slate-600 hover:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity" title="Düzenle">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <button onclick="deleteTagFromModal('marka', '${escapeAttr(m)}')" class="text-slate-600 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-opacity" title="Sil">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('') : '<p class="text-sm text-slate-600 italic">Henuz marka yok</p>';
    }

    function deleteTagFromModal(type, value) {
        deleteTag(type, value);
        updateManageLists();
    }

    function editTag(type, oldValue) {
        const newValue = prompt(`${type === 'kategori' ? 'Kategori' : 'Marka'} adını düzenle:`, oldValue);
        if (!newValue || newValue.trim() === '') return;

        const trimmedNew = newValue.trim();
        if (trimmedNew === oldValue) return; // Aynıysa çık (büyük/küçük harf dahil)

        // Global listesini güncelle
        const listKey = type === 'kategori' ? 'kategoriler' : 'markalar';
        let list = window[listKey] || [];
        const idx = list.indexOf(oldValue);
        if (idx > -1) {
            list[idx] = trimmedNew;
        } else {
            list.push(trimmedNew);
        }
        list = [...new Set(list)].sort();
        window[listKey] = list;

        // designData'daki tüm referansları güncelle
        Object.keys(designData).forEach(id => {
            if (type === 'kategori' && designData[id].kategori === oldValue) {
                designData[id].kategori = trimmedNew;
                const input = document.querySelector(`.kategori-input[data-id="${id}"]`);
                if (input) input.value = trimmedNew;
            }
            if (type === 'marka' && designData[id].marka === oldValue) {
                designData[id].marka = trimmedNew;
                const input = document.querySelector(`.marka-input[data-id="${id}"]`);
                if (input) input.value = trimmedNew;
            }
        });

        // Tüm değişen template'leri kaydet
        Object.keys(designData).forEach(id => {
            if ((type === 'kategori' && designData[id].kategori === trimmedNew) ||
                (type === 'marka' && designData[id].marka === trimmedNew)) {
                saveData(id);
            }
        });

        updateUI();
        updateManageLists();
        updateFilterDropdowns();
        showToast(`${type === 'kategori' ? 'Kategori' : 'Marka'} güncellendi: ${trimmedNew}`);
    }

    // ============================================
    // DRAG & DROP
    // ============================================
    let draggedCard = null;

    function initDragDrop() {
        const cards = document.querySelectorAll('.design-card');
        const ghost = document.getElementById('dragGhost');

        cards.forEach(card => {
            card.setAttribute('draggable', 'true');

            card.addEventListener('dragstart', (e) => {
                draggedCard = card;
                card.classList.add('dragging');

                // Taslak başlığını göster
                const title = card.querySelector('h3')?.textContent?.trim() || card.dataset.id;
                document.getElementById('draggedTitle').textContent = title;

                // Custom drag image (küçük ghost)
                ghost.textContent = title.length > 25 ? title.substring(0, 25) + '...' : title;
                ghost.style.display = 'block';
                ghost.style.left = '-9999px';
                ghost.style.top = '-9999px';
                e.dataTransfer.setDragImage(ghost, 90, 20);

                // Drop panel'i göster
                showDropPanel();

                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', card.dataset.id);
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('dragging');
                draggedCard = null;
                ghost.style.display = 'none';
                hideDropPanel();
            });
        });
    }

    function showDropPanel() {
        updateDropZones();
        document.getElementById('dropPanel').classList.add('visible');
    }

    function hideDropPanel() {
        document.getElementById('dropPanel').classList.remove('visible');
        // Tüm drag-over'ları temizle
        document.querySelectorAll('.drop-zone').forEach(z => z.classList.remove('drag-over'));
    }

    function updateDropZones() {
        // Kategorileri al (designData'dan)
        let kategoriler = window.kategoriler || [];
        Object.values(designData).forEach(d => {
            if (d.kategori && !kategoriler.includes(d.kategori)) {
                kategoriler.push(d.kategori);
            }
        });
        kategoriler = [...new Set(kategoriler)].sort();
        window.kategoriler = kategoriler;

        // Markaları al
        let markalar = window.markalar || [];
        Object.values(designData).forEach(d => {
            if (d.marka && !markalar.includes(d.marka)) {
                markalar.push(d.marka);
            }
        });
        markalar = [...new Set(markalar)].sort();
        window.markalar = markalar;

        // Kategori zone'larını oluştur
        const katContainer = document.getElementById('kategoriDropZones');
        katContainer.innerHTML = kategoriler.length ? kategoriler.map(k => `
            <div class="drop-zone group relative px-5 py-3 bg-slate-800/80 rounded-xl text-base text-emerald-300 cursor-pointer hover:bg-slate-700"
                 data-type="kategori" data-value="${escapeAttr(k)}"
                 ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                <i class="fas fa-folder mr-2"></i>${escapeAttr(k)}
                <button onclick="event.stopPropagation(); deleteTag('kategori', '${escapeAttr(k)}')"
                        class="absolute -top-2 -right-2 w-5 h-5 bg-red-600 hover:bg-red-500 rounded-full text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('') : '<span class="text-sm text-slate-600 italic">Henuz kategori yok - Header\'dan ekle</span>';

        // Marka zone'larını oluştur
        const markaContainer = document.getElementById('markaDropZones');
        markaContainer.innerHTML = markalar.length ? markalar.map(m => `
            <div class="drop-zone group relative px-5 py-3 bg-slate-800/80 rounded-xl text-base text-blue-300 cursor-pointer hover:bg-slate-700"
                 data-type="marka" data-value="${escapeAttr(m)}"
                 ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                <i class="fas fa-building mr-2"></i>${escapeAttr(m)}
                <button onclick="event.stopPropagation(); deleteTag('marka', '${escapeAttr(m)}')"
                        class="absolute -top-2 -right-2 w-5 h-5 bg-red-600 hover:bg-red-500 rounded-full text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('') : '<span class="text-sm text-slate-600 italic">Henuz marka yok - Header\'dan ekle</span>';
    }

    function deleteTag(type, value) {
        const listKey = type === 'kategori' ? 'kategoriler' : 'markalar';
        let list = window[listKey] || [];
        list = list.filter(item => item !== value);
        window[listKey] = list;

        // designData'dan da temizle
        Object.keys(designData).forEach(id => {
            if (type === 'kategori' && designData[id].kategori === value) {
                designData[id].kategori = '';
                const input = document.querySelector(`.kategori-input[data-id="${id}"]`);
                if (input) input.value = '';
                saveData(id);
            }
            if (type === 'marka' && designData[id].marka === value) {
                designData[id].marka = '';
                const input = document.querySelector(`.marka-input[data-id="${id}"]`);
                if (input) input.value = '';
                saveData(id);
            }
        });

        updateUI();
        updateDropZones();
        updateFilterDropdowns();
        showToast(`${type === 'kategori' ? 'Kategori' : 'Marka'} silindi: ${value}`);
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        e.currentTarget.classList.add('drag-over');
    }

    function handleDragLeave(e) {
        e.currentTarget.classList.remove('drag-over');
    }

    function handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');

        if (!draggedCard) return;

        const type = e.currentTarget.dataset.type;
        const value = e.currentTarget.dataset.value;
        const id = draggedCard.dataset.id;

        if (!designData[id]) designData[id] = {};

        if (type === 'kategori') {
            designData[id].kategori = value;
            // Input'u da güncelle
            const input = draggedCard.querySelector('.kategori-input');
            if (input) input.value = value;
        } else {
            designData[id].marka = value;
            const input = draggedCard.querySelector('.marka-input');
            if (input) input.value = value;
        }

        saveData(id);
        updateUI();
        updateFilterDropdowns();
        showToast(`${type === 'kategori' ? 'Kategori' : 'Marka'}: ${value}`);
    }

    // ============================================
    // INIT
    // ============================================
    document.addEventListener('DOMContentLoaded', async () => {
        // İlk önce metadata'yı yükle
        await loadMetadata();

        // Sonra UI'ı güncelle
        updateUI();
        updateFilterDropdowns();
        sortDesigns();
        initDragDrop();

        // Delete butonlarına event listener (event delegation)
        document.getElementById('designsGrid').addEventListener('click', (e) => {
            // Toggle details button
            const toggleBtn = e.target.closest('.toggle-details-btn');
            if (toggleBtn) {
                e.stopPropagation();
                const id = toggleBtn.dataset.id;
                if (id) toggleCardDetails(id);
                return;
            }

            // Delete button
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                e.stopPropagation();
                const id = deleteBtn.dataset.id;
                const title = deleteBtn.dataset.title;
                confirmDelete(id, title);
                return;
            }

            // Favorite button
            const favBtn = e.target.closest('.fav-btn');
            if (favBtn) {
                e.stopPropagation();
                const card = favBtn.closest('.design-card');
                const id = card?.dataset.id;
                if (id) toggleFavorite(id);
                return;
            }

            // Star rating
            const starIcon = e.target.closest('.star-icon');
            if (starIcon) {
                e.stopPropagation();
                const starRating = starIcon.closest('.star-rating');
                const id = starRating?.dataset.id;
                const rating = parseInt(starIcon.dataset.rating);
                if (id && rating) setRating(id, rating);
                return;
            }
        });

        // Input change events (kategori, marka, note)
        document.getElementById('designsGrid').addEventListener('change', (e) => {
            const target = e.target;

            // Kategori input
            if (target.classList.contains('kategori-input')) {
                const id = target.dataset.id;
                const value = target.value.trim().toLowerCase();
                saveKategori(id, value);
                return;
            }

            // Marka input
            if (target.classList.contains('marka-input')) {
                const id = target.dataset.id;
                const value = target.value.trim();
                saveMarka(id, value);
                return;
            }

            // Note textarea
            if (target.classList.contains('note-input')) {
                const id = target.dataset.id;
                const value = target.value.trim();
                saveNote(id, value);
                return;
            }
        });
    });
    </script>

</body>
</html>
