<?php
/**
 * Hazır Taslaklar - 3 Sekmeli Tasarım Galerisi
 * Taslaklar | Tarz | Prompt
 */

$baseDir = __DIR__;
$baseUrl = '/design/templates';

// Tüm taslak kategorilerini tara
$categories = [];
$allDesigns = [];

foreach (glob("$baseDir/*", GLOB_ONLYDIR) as $categoryPath) {
    $categoryName = basename($categoryPath);
    $versions = [];

    foreach (glob("$categoryPath/*", GLOB_ONLYDIR) as $versionPath) {
        $versionName = basename($versionPath);
        $indexFile = "$versionPath/index.html";

        if (file_exists($indexFile)) {
            $html = file_get_contents($indexFile);
            preg_match('/<title>(.*?)<\/title>/i', $html, $matches);
            $title = $matches[1] ?? $versionName;

            $designId = $categoryName . '/' . $versionName;
            $design = [
                'id' => $designId,
                'name' => $versionName,
                'title' => $title,
                'category' => $categoryName,
                'categoryDisplay' => ucwords(str_replace('-', ' ', $categoryName)),
                'url' => "$baseUrl/$categoryName/$versionName/",
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
$designsJson = json_encode($allDesigns, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasarım Merkezi | Design Templates</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .star-rating { display: flex; gap: 2px; }
        .star-rating i { cursor: pointer; transition: all 0.15s; }
        .star-rating i:hover { transform: scale(1.2); }
        .design-card.favorite { border-color: #f59e0b !important; }
        .design-card.favorite .fav-icon { color: #f59e0b !important; }
        .toast {
            position: fixed; bottom: 2rem; right: 2rem;
            background: #10b981; color: white;
            padding: 0.75rem 1.5rem; border-radius: 0.5rem;
            transform: translateY(100px); opacity: 0;
            transition: all 0.3s; z-index: 9999;
        }
        .toast.show { transform: translateY(0); opacity: 1; }
        .small-input {
            background: transparent;
            border: none;
            border-bottom: 1px dashed #475569;
            padding: 2px 0;
            font-size: 11px;
            color: #94a3b8;
            width: 100%;
            outline: none;
        }
        .small-input:focus { border-color: #8b5cf6; color: white; }
        .small-input::placeholder { color: #64748b; }
        .details-section { display: none; }
        .details-section.show { display: block; }
        .toggle-details { transition: transform 0.2s; }
        .toggle-details.open { transform: rotate(180deg); }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen">

    <!-- Toast -->
    <div id="toast" class="toast"><i class="fas fa-check-circle mr-2"></i><span>Kaydedildi!</span></div>

    <!-- Header -->
    <header class="border-b border-slate-800 sticky top-0 bg-slate-950/95 backdrop-blur z-50">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Tabs -->
                <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-lg">
                    <button onclick="switchTab('taslaklar')" id="tab-taslaklar" class="tab-btn px-4 py-2 rounded-md text-sm font-medium transition bg-violet-600 text-white">
                        <i class="fas fa-layer-group mr-2"></i>Taslaklar
                    </button>
                    <a href="/design/sectors/" id="tab-tarz" class="tab-btn px-4 py-2 rounded-md text-sm font-medium transition text-slate-400 hover:text-white hover:bg-slate-800">
                        <i class="fas fa-palette mr-2"></i>Tarz
                    </a>
                    <a href="/design/prompt/" id="tab-prompt" class="tab-btn px-4 py-2 rounded-md text-sm font-medium transition text-slate-400 hover:text-white hover:bg-slate-800">
                        <i class="fas fa-terminal mr-2"></i>Prompt
                    </a>
                </div>

                <!-- Stats -->
                <div class="flex items-center gap-4">
                    <div class="text-xs text-slate-500 bg-slate-900 px-3 py-2 rounded-lg hidden sm:flex items-center gap-3">
                        <span><span class="text-lg font-bold text-white"><?= $totalDesigns ?></span> tasarım</span>
                        <span class="text-slate-700">|</span>
                        <span id="favCount"><span class="text-lg font-bold text-amber-400">0</span> favori</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: TASLAKLAR -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div id="content-taslaklar" class="tab-content active">
        <main class="max-w-7xl mx-auto px-6 py-8">
            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3 mb-8">
                <button onclick="filterDesigns('all')" class="filter-btn active px-3 py-1.5 text-sm rounded-lg bg-slate-800 text-white">
                    Tümü
                </button>
                <button onclick="filterDesigns('favorites')" class="filter-btn px-3 py-1.5 text-sm rounded-lg bg-slate-900 text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    <i class="fas fa-heart mr-1 text-amber-400"></i> Favoriler
                </button>
                <button onclick="filterDesigns('rated')" class="filter-btn px-3 py-1.5 text-sm rounded-lg bg-slate-900 text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    <i class="fas fa-star mr-1 text-yellow-400"></i> Puanlı
                </button>
                <button onclick="filterDesigns('noted')" class="filter-btn px-3 py-1.5 text-sm rounded-lg bg-slate-900 text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    <i class="fas fa-sticky-note mr-1 text-emerald-400"></i> Notlu
                </button>

                <span class="text-slate-700">|</span>

                <!-- Kategori Dropdown -->
                <div class="relative">
                    <select id="filterKategori" onchange="filterByKategori()" class="appearance-none bg-slate-900 border border-slate-700 text-slate-400 text-sm rounded-lg px-3 py-1.5 pr-8 focus:outline-none focus:border-slate-500 cursor-pointer">
                        <option value="">Kategori</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 text-xs pointer-events-none"></i>
                </div>

                <!-- Marka Dropdown -->
                <div class="relative">
                    <select id="filterMarka" onchange="filterByMarka()" class="appearance-none bg-slate-900 border border-slate-700 text-slate-400 text-sm rounded-lg px-3 py-1.5 pr-8 focus:outline-none focus:border-slate-500 cursor-pointer">
                        <option value="">Marka</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 text-xs pointer-events-none"></i>
                </div>
            </div>

            <?php if (empty($categories)): ?>
                <div class="text-center py-20">
                    <i class="fas fa-folder-open text-6xl text-slate-700 mb-4"></i>
                    <p class="text-slate-500">Henüz tasarım eklenmemiş</p>
                </div>
            <?php else: ?>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" id="designsGrid">
                    <?php foreach ($categories as $category): ?>
                        <?php foreach ($category['versions'] as $design): ?>
                            <div class="design-card bg-slate-900 border border-slate-800 rounded-xl overflow-hidden hover:border-slate-700 transition group"
                                 data-id="<?= htmlspecialchars($design['id']) ?>"
                                 data-category="<?= htmlspecialchars($design['category']) ?>">

                                <!-- Card Header -->
                                <div class="p-3 border-b border-slate-800/50">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <span class="text-[10px] font-mono text-slate-500 bg-slate-800 px-1.5 py-0.5 rounded">
                                                <?= htmlspecialchars($design['categoryDisplay']) ?>
                                            </span>
                                            <h3 class="text-xs font-medium text-white mt-1.5 truncate" title="<?= htmlspecialchars($design['title']) ?>">
                                                <?= htmlspecialchars($design['title']) ?>
                                            </h3>
                                        </div>
                                        <!-- Favorite Button -->
                                        <button onclick="toggleFavorite('<?= htmlspecialchars($design['id']) ?>')" class="fav-icon text-slate-600 hover:text-amber-400 transition">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Toggle Details Button -->
                                <div class="px-3 py-2 border-b border-slate-800/50">
                                    <button onclick="toggleDetails('<?= htmlspecialchars($design['id']) ?>')" class="w-full flex items-center justify-between text-[10px] text-slate-500 hover:text-slate-300 transition">
                                        <span class="details-label">Detayları göster</span>
                                        <i class="fas fa-chevron-down toggle-details"></i>
                                    </button>
                                </div>

                                <!-- Details Section (Hidden by default) -->
                                <div class="details-section" data-details="<?= htmlspecialchars($design['id']) ?>">
                                    <!-- Rating -->
                                    <div class="px-3 py-2 border-b border-slate-800/50 flex items-center justify-between">
                                        <span class="text-[10px] text-slate-500">Puan</span>
                                        <div class="star-rating" data-id="<?= htmlspecialchars($design['id']) ?>">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star text-xs text-slate-700" data-rating="<?= $i ?>" onclick="setRating('<?= htmlspecialchars($design['id']) ?>', <?= $i ?>)"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <!-- Kategori & Marka -->
                                    <div class="px-3 py-2 border-b border-slate-800/50 grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="text-[9px] text-slate-600 block mb-0.5">Kategori</label>
                                            <input type="text"
                                                class="small-input kategori-input"
                                                placeholder="örn: kurumsal"
                                                data-id="<?= htmlspecialchars($design['id']) ?>"
                                                onchange="saveKategori('<?= htmlspecialchars($design['id']) ?>', this.value)">
                                        </div>
                                        <div>
                                            <label class="text-[9px] text-slate-600 block mb-0.5">Marka</label>
                                            <input type="text"
                                                class="small-input marka-input"
                                                placeholder="örn: Apple"
                                                data-id="<?= htmlspecialchars($design['id']) ?>"
                                                onchange="saveMarka('<?= htmlspecialchars($design['id']) ?>', this.value)">
                                        </div>
                                    </div>

                                    <!-- Note -->
                                    <div class="px-3 py-2 border-b border-slate-800/50">
                                        <textarea
                                            class="w-full bg-transparent text-[11px] text-slate-400 placeholder-slate-600 focus:outline-none focus:text-slate-300 resize-none"
                                            placeholder="Not ekle..."
                                            rows="2"
                                            data-id="<?= htmlspecialchars($design['id']) ?>"
                                            onchange="saveNote('<?= htmlspecialchars($design['id']) ?>', this.value)"
                                        ></textarea>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="p-3 flex items-center justify-between">
                                    <span class="text-[10px] text-slate-600"><?= htmlspecialchars($design['name']) ?></span>
                                    <a href="<?= $design['url'] ?>" target="_blank" class="flex items-center gap-1.5 px-2.5 py-1 bg-slate-800 hover:bg-slate-700 rounded text-[10px] text-slate-300 hover:text-white transition">
                                        <i class="fas fa-external-link-alt"></i>
                                        Aç
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>


    <!-- Footer -->
    <footer class="border-t border-slate-800 mt-12">
        <div class="max-w-7xl mx-auto px-6 py-6 text-center text-sm text-slate-500">
            tuufi.com/design
        </div>
    </footer>

    <script>
    // ═══════════════════════════════════════════════════════════════
    // DATA & STATE
    // ═══════════════════════════════════════════════════════════════
    const designs = <?= $designsJson ?>;
    let designData = JSON.parse(localStorage.getItem('designData') || '{}');


    // ═══════════════════════════════════════════════════════════════
    // TOGGLE DETAILS
    // ═══════════════════════════════════════════════════════════════
    function toggleDetails(id) {
        const detailsSection = document.querySelector(`[data-details="${id}"]`);
        const card = document.querySelector(`[data-id="${id}"]`);
        const toggleBtn = card.querySelector('.toggle-details');
        const label = card.querySelector('.details-label');

        if (detailsSection.classList.contains('show')) {
            detailsSection.classList.remove('show');
            toggleBtn.classList.remove('open');
            label.textContent = 'Detayları göster';
        } else {
            detailsSection.classList.add('show');
            toggleBtn.classList.add('open');
            label.textContent = 'Detayları gizle';
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // FAVORITES
    // ═══════════════════════════════════════════════════════════════
    function toggleFavorite(id) {
        if (!designData[id]) designData[id] = {};
        designData[id].favorite = !designData[id].favorite;
        saveData();
        updateUI();
        showToast(designData[id].favorite ? 'Favorilere eklendi!' : 'Favorilerden çıkarıldı');
    }

    // ═══════════════════════════════════════════════════════════════
    // RATINGS
    // ═══════════════════════════════════════════════════════════════
    function setRating(id, rating) {
        if (!designData[id]) designData[id] = {};
        designData[id].rating = designData[id].rating === rating ? 0 : rating;
        saveData();
        updateUI();
        showToast('Puan kaydedildi!');
    }

    // ═══════════════════════════════════════════════════════════════
    // NOTES
    // ═══════════════════════════════════════════════════════════════
    function saveNote(id, note) {
        if (!designData[id]) designData[id] = {};
        designData[id].note = note.trim();
        saveData();
        showToast('Not kaydedildi!');
    }

    // ═══════════════════════════════════════════════════════════════
    // KATEGORİ & MARKA
    // ═══════════════════════════════════════════════════════════════
    function saveKategori(id, kategori) {
        if (!designData[id]) designData[id] = {};
        designData[id].kategori = kategori.trim().toLowerCase();
        saveData();
        updateFilterDropdowns();
        showToast('Kategori kaydedildi!');
    }

    function saveMarka(id, marka) {
        if (!designData[id]) designData[id] = {};
        designData[id].marka = marka.trim();
        saveData();
        updateFilterDropdowns();
        showToast('Marka kaydedildi!');
    }

    function updateFilterDropdowns() {
        const kategoriler = new Set();
        const markalar = new Set();

        Object.values(designData).forEach(d => {
            if (d.kategori) kategoriler.add(d.kategori);
            if (d.marka) markalar.add(d.marka);
        });

        // Kategori dropdown
        const katSelect = document.getElementById('filterKategori');
        const selectedKat = katSelect.value;
        katSelect.innerHTML = '<option value="">Kategori</option>';
        [...kategoriler].sort().forEach(k => {
            katSelect.innerHTML += `<option value="${k}" ${k === selectedKat ? 'selected' : ''}>${k}</option>`;
        });

        // Marka dropdown
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
            const id = card.dataset.id;
            const data = designData[id] || {};
            card.style.display = (!value || data.kategori === value) ? '' : 'none';
        });
        // Reset other filters
        document.getElementById('filterMarka').value = '';
        resetFilterButtons();
    }

    function filterByMarka() {
        const value = document.getElementById('filterMarka').value;
        document.querySelectorAll('.design-card').forEach(card => {
            const id = card.dataset.id;
            const data = designData[id] || {};
            card.style.display = (!value || data.marka === value) ? '' : 'none';
        });
        // Reset other filters
        document.getElementById('filterKategori').value = '';
        resetFilterButtons();
    }

    function resetFilterButtons() {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-slate-800', 'text-white');
            btn.classList.add('bg-slate-900', 'text-slate-400');
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // FILTERING
    // ═══════════════════════════════════════════════════════════════
    function filterDesigns(filter) {
        // Reset dropdowns
        document.getElementById('filterKategori').value = '';
        document.getElementById('filterMarka').value = '';

        // Update buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-slate-800', 'text-white');
            btn.classList.add('bg-slate-900', 'text-slate-400');
        });
        event.target.classList.add('active', 'bg-slate-800', 'text-white');
        event.target.classList.remove('bg-slate-900', 'text-slate-400');

        // Filter cards
        document.querySelectorAll('.design-card').forEach(card => {
            const id = card.dataset.id;
            const data = designData[id] || {};
            let show = true;

            switch(filter) {
                case 'favorites':
                    show = data.favorite === true;
                    break;
                case 'rated':
                    show = data.rating > 0;
                    break;
                case 'noted':
                    show = data.note && data.note.length > 0;
                    break;
            }

            card.style.display = show ? '' : 'none';
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // UI UPDATE
    // ═══════════════════════════════════════════════════════════════
    function updateUI() {
        let favCount = 0;

        document.querySelectorAll('.design-card').forEach(card => {
            const id = card.dataset.id;
            const data = designData[id] || {};

            // Favorite
            if (data.favorite) {
                card.classList.add('favorite');
                favCount++;
            } else {
                card.classList.remove('favorite');
            }

            // Rating
            const stars = card.querySelectorAll('.star-rating i');
            stars.forEach((star, index) => {
                if (index < (data.rating || 0)) {
                    star.classList.remove('text-slate-700');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.add('text-slate-700');
                    star.classList.remove('text-yellow-400');
                }
            });

            // Note
            const noteInput = card.querySelector('textarea');
            if (noteInput && data.note) {
                noteInput.value = data.note;
            }

            // Kategori
            const kategoriInput = card.querySelector('.kategori-input');
            if (kategoriInput && data.kategori) {
                kategoriInput.value = data.kategori;
            }

            // Marka
            const markaInput = card.querySelector('.marka-input');
            if (markaInput && data.marka) {
                markaInput.value = data.marka;
            }
        });

        document.getElementById('favCount').innerHTML = `<span class="text-lg font-bold text-amber-400">${favCount}</span> favori`;
    }

    // ═══════════════════════════════════════════════════════════════
    // STORAGE
    // ═══════════════════════════════════════════════════════════════
    function saveData() {
        localStorage.setItem('designData', JSON.stringify(designData));
    }

    // ═══════════════════════════════════════════════════════════════
    // TOAST
    // ═══════════════════════════════════════════════════════════════
    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.querySelector('span').textContent = message;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2000);
    }


    // ═══════════════════════════════════════════════════════════════
    // INITIALIZATION
    // ═══════════════════════════════════════════════════════════════
    document.addEventListener('DOMContentLoaded', function() {
        updateUI();
        updateFilterDropdowns();
    });
    </script>

</body>
</html>
