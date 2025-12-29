<?php
/**
 * Hazir Taslaklar - Full Width Tasarim Galerisi
 */

$baseDir = __DIR__;
$baseUrl = '/design/templates';

// Tum taslak kategorilerini tara
$categories = [];
$allDesigns = [];

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
$totalDesigns = count($allDesigns);
$promptedDesigns = count(array_filter($allDesigns, fn($d) => $d['hasPrompt']));
$designsJson = json_encode($allDesigns, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .design-card.favorite .fav-icon { color: #f59e0b !important; }

        /* Fav icon */
        .fav-icon { transition: color 0.15s; }

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

        /* Tooltip */
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

            <select id="filterKategori" onchange="filterByKategori()" class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg px-3 py-2 shrink-0">
                <option value="">Kategori</option>
            </select>
            <select id="filterMarka" onchange="filterByMarka()" class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg px-3 py-2 shrink-0">
                <option value="">Marka</option>
            </select>

            <span class="text-slate-700">|</span>

            <!-- Sort & Toggle -->
            <select id="sortOrder" onchange="sortDesigns()" class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg px-3 py-2 shrink-0">
                <option value="date-desc" selected>Yeni</option>
                <option value="date-asc">Eski</option>
                <option value="alpha">A-Z</option>
                <option value="alpha-desc">Z-A</option>
            </select>

            <button onclick="toggleAllDetails()" class="tooltip px-3 py-2 text-sm rounded-lg bg-violet-900/50 text-violet-300 hover:bg-violet-800 shrink-0" data-tip="Detaylar">
                <i class="fas fa-eye" id="toggleAllIcon"></i>
            </button>

            <span class="text-slate-700">|</span>

            <!-- Kategori & Marka Yönetimi -->
            <button onclick="openManageModal()" class="tooltip px-3 py-2 text-sm rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 shrink-0" data-tip="Kategori/Marka">
                <i class="fas fa-tags"></i>
            </button>

            <!-- Stats -->
            <span class="ml-auto text-xs shrink-0"><span class="text-violet-400"><?= $promptedDesigns ?>p</span> <span class="text-slate-600">|</span> <span class="text-amber-400" id="favCount">0f</span></span>
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
                             data-id="<?= htmlspecialchars($design['id']) ?>"
                             data-category="<?= htmlspecialchars($design['category']) ?>"
                             data-hasprompt="<?= $design['hasPrompt'] ? 'true' : 'false' ?>">

                            <!-- Header -->
                            <div class="p-4 pb-3">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-xs font-medium text-violet-400 bg-violet-500/20 px-2 py-0.5 rounded">
                                        <?= htmlspecialchars($design['categoryDisplay']) ?>
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <div class="star-rating" data-id="<?= htmlspecialchars($design['id']) ?>">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star text-slate-700 text-xs hover:text-yellow-400 transition-colors" onclick="event.stopPropagation(); setRating('<?= htmlspecialchars($design['id']) ?>', <?= $i ?>)"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <button onclick="event.stopPropagation(); toggleFavorite('<?= htmlspecialchars($design['id']) ?>')" class="fav-icon text-slate-600 hover:text-amber-400 transition text-sm" title="Favori">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>
                                </div>
                                <a href="<?= $design['url'] ?>" target="_blank" class="block">
                                    <h3 class="text-sm font-semibold text-white leading-snug line-clamp-2 hover:text-violet-400 transition-colors" title="<?= htmlspecialchars($design['title']) ?>">
                                        <?= htmlspecialchars($design['title']) ?>
                                    </h3>
                                </a>
                            </div>

                            <!-- Hover Actions (sağ alt) -->
                            <div class="absolute bottom-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php if ($design['hasPrompt']): ?>
                                <a href="<?= $design['promptUrl'] ?>" target="_blank" onclick="event.stopPropagation()" class="w-6 h-6 flex items-center justify-center rounded bg-violet-600 hover:bg-violet-500 text-white text-xs" title="Prompt">
                                    <i class="fas fa-terminal"></i>
                                </a>
                                <?php endif; ?>
                                <button onclick="event.stopPropagation(); confirmDelete('<?= htmlspecialchars($design['id']) ?>', '<?= htmlspecialchars($design['title']) ?>')" class="w-6 h-6 flex items-center justify-center rounded bg-slate-700 hover:bg-red-600 text-slate-400 hover:text-white text-xs" title="Sil">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>

                            <!-- Details (Hidden) -->
                            <div class="details-section border-t border-slate-800/50" data-details="<?= htmlspecialchars($design['id']) ?>">
                                <div class="p-5 space-y-4">
                                    <!-- Kategori & Marka -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs text-slate-500 block mb-2">Kategori</label>
                                            <input type="text" class="small-input kategori-input" placeholder="..." data-id="<?= htmlspecialchars($design['id']) ?>" onchange="saveKategori('<?= htmlspecialchars($design['id']) ?>', this.value)">
                                        </div>
                                        <div>
                                            <label class="text-xs text-slate-500 block mb-2">Marka</label>
                                            <input type="text" class="small-input marka-input" placeholder="..." data-id="<?= htmlspecialchars($design['id']) ?>" onchange="saveMarka('<?= htmlspecialchars($design['id']) ?>', this.value)">
                                        </div>
                                    </div>
                                    <!-- Not -->
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-2">Not</label>
                                        <textarea class="w-full bg-slate-800/50 text-sm text-slate-300 resize-none rounded-lg p-3" rows="2" placeholder="..." data-id="<?= htmlspecialchars($design['id']) ?>" onchange="saveNote('<?= htmlspecialchars($design['id']) ?>', this.value)"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Indicators (kategori/marka varsa göster) -->
                            <div class="indicators px-4 py-1.5 hidden" data-indicators="<?= htmlspecialchars($design['id']) ?>">
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
    let designData = JSON.parse(localStorage.getItem('designData') || '{}');
    let allDetailsOpen = false;

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
    }

    function toggleFavorite(id) {
        if (!designData[id]) designData[id] = {};
        designData[id].favorite = !designData[id].favorite;
        saveData(); updateUI();
        showToast(designData[id].favorite ? 'Favorilere eklendi' : 'Favorilerden cikarildi');
    }

    function setRating(id, rating) {
        if (!designData[id]) designData[id] = {};
        designData[id].rating = designData[id].rating === rating ? 0 : rating;
        saveData(); updateUI();
    }

    function saveNote(id, note) {
        if (!designData[id]) designData[id] = {};
        designData[id].note = note.trim();
        saveData();
    }

    function saveKategori(id, kategori) {
        if (!designData[id]) designData[id] = {};
        designData[id].kategori = kategori.trim().toLowerCase();
        saveData(); updateFilterDropdowns();
    }

    function saveMarka(id, marka) {
        if (!designData[id]) designData[id] = {};
        designData[id].marka = marka.trim();
        saveData(); updateFilterDropdowns();
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
        document.getElementById('favCount').textContent = favCount + 'f';
    }

    function saveData() { localStorage.setItem('designData', JSON.stringify(designData)); }

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
            const res = await fetch('/design/templates/template-action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ templateId: deleteTargetId, password })
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

        // LocalStorage'daki listeye ekle
        const listKey = addModalType === 'kategori' ? 'designKategoriler' : 'designMarkalar';
        let list = JSON.parse(localStorage.getItem(listKey) || '[]');
        const normalizedValue = addModalType === 'kategori' ? value.toLowerCase() : value;

        if (!list.includes(normalizedValue)) {
            list.push(normalizedValue);
            list.sort();
            localStorage.setItem(listKey, JSON.stringify(list));
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
        let kategoriler = JSON.parse(localStorage.getItem('designKategoriler') || '[]');
        Object.values(designData).forEach(d => {
            if (d.kategori && !kategoriler.includes(d.kategori)) kategoriler.push(d.kategori);
        });
        kategoriler = [...new Set(kategoriler)].sort();

        // Markaları al
        let markalar = JSON.parse(localStorage.getItem('designMarkalar') || '[]');
        Object.values(designData).forEach(d => {
            if (d.marka && !markalar.includes(d.marka)) markalar.push(d.marka);
        });
        markalar = [...new Set(markalar)].sort();

        // Kategori listesi
        const katList = document.getElementById('manageKategoriList');
        katList.innerHTML = kategoriler.length ? kategoriler.map(k => {
            const count = Object.values(designData).filter(d => d.kategori === k).length;
            return `
                <div class="flex items-center justify-between bg-slate-800/50 rounded-lg px-3 py-2 group">
                    <span class="text-sm text-slate-300"><i class="fas fa-folder text-emerald-500 mr-2 text-xs"></i>${k}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-600">${count}</span>
                        <button onclick="editTag('kategori', '${k}')" class="text-slate-600 hover:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity" title="Düzenle">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <button onclick="deleteTagFromModal('kategori', '${k}')" class="text-slate-600 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-opacity" title="Sil">
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
                        <button onclick="editTag('marka', '${m}')" class="text-slate-600 hover:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity" title="Düzenle">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <button onclick="deleteTagFromModal('marka', '${m}')" class="text-slate-600 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-opacity" title="Sil">
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

        // LocalStorage listesini güncelle
        const listKey = type === 'kategori' ? 'designKategoriler' : 'designMarkalar';
        let list = JSON.parse(localStorage.getItem(listKey) || '[]');
        const idx = list.indexOf(oldValue);
        if (idx > -1) {
            list[idx] = trimmedNew;
        } else {
            list.push(trimmedNew);
        }
        list = [...new Set(list)].sort();
        localStorage.setItem(listKey, JSON.stringify(list));

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

        saveData();
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
        // Kategorileri al (localStorage + designData'dan)
        let kategoriler = JSON.parse(localStorage.getItem('designKategoriler') || '[]');
        Object.values(designData).forEach(d => {
            if (d.kategori && !kategoriler.includes(d.kategori)) {
                kategoriler.push(d.kategori);
            }
        });
        kategoriler = [...new Set(kategoriler)].sort();

        // Markaları al
        let markalar = JSON.parse(localStorage.getItem('designMarkalar') || '[]');
        Object.values(designData).forEach(d => {
            if (d.marka && !markalar.includes(d.marka)) {
                markalar.push(d.marka);
            }
        });
        markalar = [...new Set(markalar)].sort();

        // Kategori zone'larını oluştur
        const katContainer = document.getElementById('kategoriDropZones');
        katContainer.innerHTML = kategoriler.length ? kategoriler.map(k => `
            <div class="drop-zone group relative px-5 py-3 bg-slate-800/80 rounded-xl text-base text-emerald-300 cursor-pointer hover:bg-slate-700"
                 data-type="kategori" data-value="${k}"
                 ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                <i class="fas fa-folder mr-2"></i>${k}
                <button onclick="event.stopPropagation(); deleteTag('kategori', '${k}')"
                        class="absolute -top-2 -right-2 w-5 h-5 bg-red-600 hover:bg-red-500 rounded-full text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('') : '<span class="text-sm text-slate-600 italic">Henuz kategori yok - Header\'dan ekle</span>';

        // Marka zone'larını oluştur
        const markaContainer = document.getElementById('markaDropZones');
        markaContainer.innerHTML = markalar.length ? markalar.map(m => `
            <div class="drop-zone group relative px-5 py-3 bg-slate-800/80 rounded-xl text-base text-blue-300 cursor-pointer hover:bg-slate-700"
                 data-type="marka" data-value="${m}"
                 ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                <i class="fas fa-building mr-2"></i>${m}
                <button onclick="event.stopPropagation(); deleteTag('marka', '${m}')"
                        class="absolute -top-2 -right-2 w-5 h-5 bg-red-600 hover:bg-red-500 rounded-full text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('') : '<span class="text-sm text-slate-600 italic">Henuz marka yok - Header\'dan ekle</span>';
    }

    function deleteTag(type, value) {
        const listKey = type === 'kategori' ? 'designKategoriler' : 'designMarkalar';
        let list = JSON.parse(localStorage.getItem(listKey) || '[]');
        list = list.filter(item => item !== value);
        localStorage.setItem(listKey, JSON.stringify(list));

        // designData'dan da temizle
        Object.keys(designData).forEach(id => {
            if (type === 'kategori' && designData[id].kategori === value) {
                designData[id].kategori = '';
                const input = document.querySelector(`.kategori-input[data-id="${id}"]`);
                if (input) input.value = '';
            }
            if (type === 'marka' && designData[id].marka === value) {
                designData[id].marka = '';
                const input = document.querySelector(`.marka-input[data-id="${id}"]`);
                if (input) input.value = '';
            }
        });

        saveData();
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

        saveData();
        updateUI();
        updateFilterDropdowns();
        showToast(`${type === 'kategori' ? 'Kategori' : 'Marka'}: ${value}`);
    }

    // ============================================
    // INIT
    // ============================================
    document.addEventListener('DOMContentLoaded', () => {
        updateUI();
        updateFilterDropdowns();
        sortDesigns();
        initDragDrop();
    });
    </script>

</body>
</html>
