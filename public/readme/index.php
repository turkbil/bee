<?php
// README Index - Otomatik Rapor Listesi

// Handle AJAX preference save/load
$prefsFile = __DIR__ . '/prefs.json';

if (isset($_GET['action']) && $_GET['action'] === 'get_prefs') {
    header('Content-Type: application/json');
    if (!file_exists($prefsFile)) {
        file_put_contents($prefsFile, json_encode(['favorites' => [], 'hidden' => []], JSON_PRETTY_PRINT));
    }
    echo file_get_contents($prefsFile);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'save_prefs') {
    header('Content-Type: application/json');
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if ($data && isset($data['favorites']) && isset($data['hidden'])) {
        file_put_contents($prefsFile, json_encode($data, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
    exit;
}

$baseDir = __DIR__;
$reports = [];

function scanReports($dir) {
    $result = [];
    $years = glob($dir . '/[0-9][0-9][0-9][0-9]', GLOB_ONLYDIR);

    foreach ($years as $yearPath) {
        $year = basename($yearPath);
        $months = glob($yearPath . '/[0-9][0-9]', GLOB_ONLYDIR);

        foreach ($months as $monthPath) {
            $month = basename($monthPath);
            $days = glob($monthPath . '/[0-9][0-9]', GLOB_ONLYDIR);

            foreach ($days as $dayPath) {
                $day = basename($dayPath);
                $topics = glob($dayPath . '/*', GLOB_ONLYDIR);

                foreach ($topics as $topicPath) {
                    $topic = basename($topicPath);
                    $versions = [];
                    $versionDirs = glob($topicPath . '/v*', GLOB_ONLYDIR);

                    foreach ($versionDirs as $versionPath) {
                        $version = basename($versionPath);
                        $indexFile = $versionPath . '/index.html';

                        if (file_exists($indexFile)) {
                            $versions[] = [
                                'version' => $version,
                                'url' => "/readme/$year/$month/$day/$topic/$version/",
                                'modified' => filemtime($indexFile),
                            ];
                        }
                    }

                    usort($versions, function($a, $b) {
                        return $b['modified'] - $a['modified'];
                    });

                    if (!empty($versions)) {
                        $latestVersion = $versions[0];
                        $htmlContent = file_get_contents($topicPath . '/' . $latestVersion['version'] . '/index.html');
                        preg_match('/<title>(.*?)<\/title>/i', $htmlContent, $titleMatch);
                        $title = $titleMatch[1] ?? $topic;
                        $title = strip_tags($title);

                        $result[] = [
                            'date' => "$year-$month-$day",
                            'topic' => $topic,
                            'title' => $title,
                            'url' => "/readme/$year/$month/$day/$topic/", // 🔴 Ana klasör linki (DirectoryIndex ile v1'e yönlenir)
                            'versions' => $versions,
                            'latestModified' => $versions[0]['modified']
                        ];
                    }
                }
            }
        }
    }

    // Tarihe göre sırala (en yeni en üstte)
    usort($result, function($a, $b) {
        // Önce klasör tarihine göre (YYYY-MM-DD)
        $dateCompare = strcmp($b['date'], $a['date']);
        if ($dateCompare !== 0) {
            return $dateCompare;
        }
        // Aynı tarihte ise modification time'a göre
        return $b['latestModified'] - $a['latestModified'];
    });

    return $result;
}

$reports = scanReports($baseDir);

// Sıralama parametresi
$sortBy = $_GET['sort'] ?? 'date'; // 'date' veya 'modified'
if ($sortBy === 'modified') {
    usort($reports, function($a, $b) {
        return $b['latestModified'] - $a['latestModified'];
    });
}

$monthNames = [
    '01' => 'Oca', '02' => 'Şub', '03' => 'Mar', '04' => 'Nis',
    '05' => 'May', '06' => 'Haz', '07' => 'Tem', '08' => 'Ağu',
    '09' => 'Eyl', '10' => 'Eki', '11' => 'Kas', '12' => 'Ara'
];

$totalReports = count($reports);
$totalVersions = array_sum(array_map(function($r) { return count($r['versions']); }, $reports));
$currentDomain = $_SERVER['HTTP_HOST'] ?? 'ixtif.com';

// 3 kolona soldan sağa dağıt
$col1 = [];
$col2 = [];
$col3 = [];

foreach ($reports as $index => $report) {
    $colIndex = $index % 3;
    switch ($colIndex) {
        case 0: $col1[] = $report; break;
        case 1: $col2[] = $report; break;
        case 2: $col3[] = $report; break;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📚 README</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-950 text-slate-300">
    <!-- Single Line Header -->
    <div class="sticky top-0 bg-slate-900/98 backdrop-blur border-b border-slate-800 px-4 py-3 z-10">
        <div class="grid grid-cols-3 gap-4 items-center">
            <!-- Left: Logo -->
            <div class="flex items-center gap-2">
                <span class="text-lg font-bold text-blue-400">📚 README</span>
                <span class="text-xs text-slate-600"><?= htmlspecialchars($currentDomain) ?></span>
            </div>

            <!-- Center: Search -->
            <input type="text"
                   id="searchInput"
                   placeholder="🔍 Ara..."
                   class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-1.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

            <!-- Right: Stats + Filter + Sort -->
            <div class="flex justify-end gap-2 items-center">
                <button onclick="filterReports('all')" class="filter-btn active text-xs text-slate-500 hover:text-slate-300 px-2 py-1 rounded">Hepsi</button>
                <button onclick="filterReports('favorites')" class="filter-btn text-xs text-slate-500 hover:text-amber-400 px-2 py-1 rounded">⭐</button>
                <span class="text-xs text-slate-600">•</span>
                <div x-data="{ show: false }" class="relative">
                    <a href="?sort=date"
                       @mouseenter="show = true"
                       @mouseleave="show = false"
                       class="sort-btn text-xs <?= $sortBy === 'date' ? 'text-blue-400' : 'text-slate-500 hover:text-slate-300' ?> px-2 py-1 rounded">📅</a>
                    <div x-show="show"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute top-full right-0 mt-2 px-3 py-2 bg-slate-700 text-slate-100 text-xs rounded-lg whitespace-nowrap z-50 shadow-xl border border-slate-600">
                        📅 Tarihe göre sırala
                    </div>
                </div>
                <div x-data="{ show: false }" class="relative">
                    <a href="?sort=modified"
                       @mouseenter="show = true"
                       @mouseleave="show = false"
                       class="sort-btn text-xs <?= $sortBy === 'modified' ? 'text-blue-400' : 'text-slate-500 hover:text-slate-300' ?> px-2 py-1 rounded">🕐</a>
                    <div x-show="show"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute top-full right-0 mt-2 px-3 py-2 bg-slate-700 text-slate-100 text-xs rounded-lg whitespace-nowrap z-50 shadow-xl border border-slate-600">
                        🕐 Son güncellemeye göre
                    </div>
                </div>
                <span class="text-xs text-slate-600">•</span>
                <span id="totalReports" class="text-xs text-slate-500"><?= $totalReports ?> rapor</span>
                <span class="text-xs text-slate-600">•</span>
                <span class="text-xs text-slate-500"><?= $totalVersions ?> v</span>
            </div>
        </div>
    </div>

    <!-- 3 Column Tables -->
    <div class="grid grid-cols-3 gap-4 p-4">
        <!-- Table 1 -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <tbody>
                    <?php foreach ($col1 as $i => $report):
                        list($y, $m, $d) = explode('-', $report['date']);
                        $stripe = $i % 2 === 0 ? 'bg-slate-900/30' : 'bg-slate-900/60';
                        $reportId = $report['topic'];
                        $reportNumber = ($i * 3) + 1;
                    ?>
                        <tr class="report-row <?= $stripe ?> hover:bg-slate-800 border-b border-slate-900/50 transition-colors"
                            data-id="<?= htmlspecialchars($reportId) ?>"
                            data-title="<?= htmlspecialchars(strtolower($report['title'])) ?>">
                            <td class="px-3 py-3">
                                <a href="<?= htmlspecialchars($report['url']) ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-slate-100 hover:text-blue-400 text-sm font-medium leading-relaxed block">
                                    <?= htmlspecialchars($report['title']) ?>
                                </a>
                                <div class="flex gap-1.5 flex-wrap mt-2 items-center">
                                    <span class="report-number px-2 py-0.5 bg-slate-800 text-slate-500 rounded text-xs font-mono">#<?= $reportNumber ?></span>
                                    <?php foreach ($report['versions'] as $vi => $v): ?>
                                        <a href="<?= htmlspecialchars($v['url']) ?>"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 <?= $vi === 0 ? 'bg-emerald-600 hover:bg-emerald-500 text-white font-semibold' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' ?> rounded-md text-xs transition-colors"
                                           title="<?= date('d.m.Y H:i', $v['modified']) ?>">
                                            <?= $vi === 0 ? '🆕' : '📄' ?>
                                            <span><?= htmlspecialchars($v['version']) ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-2 py-3 align-top">
                                <div class="flex gap-1 mb-1">
                                    <button onclick="toggleFavorite('<?= htmlspecialchars($reportId) ?>')" class="fav-btn w-6 h-6 flex items-center justify-center text-slate-600 hover:text-amber-400 transition-colors" title="Favori">☆</button>
                                    <button onclick="hideReport('<?= htmlspecialchars($reportId) ?>')" class="w-6 h-6 flex items-center justify-center text-slate-600 hover:text-red-400 transition-colors" title="Gizle">✕</button>
                                </div>
                                <div class="text-slate-500 text-xs whitespace-nowrap"><?= $d ?> <?= $monthNames[$m] ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Table 2 -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <tbody>
                    <?php foreach ($col2 as $i => $report):
                        list($y, $m, $d) = explode('-', $report['date']);
                        $stripe = $i % 2 === 0 ? 'bg-slate-900/30' : 'bg-slate-900/60';
                        $reportId = $report['topic'];
                        $reportNumber = ($i * 3) + 2;
                    ?>
                        <tr class="report-row <?= $stripe ?> hover:bg-slate-800 border-b border-slate-900/50 transition-colors"
                            data-id="<?= htmlspecialchars($reportId) ?>"
                            data-title="<?= htmlspecialchars(strtolower($report['title'])) ?>">
                            <td class="px-3 py-3">
                                <a href="<?= htmlspecialchars($report['url']) ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-slate-100 hover:text-blue-400 text-sm font-medium leading-relaxed block">
                                    <?= htmlspecialchars($report['title']) ?>
                                </a>
                                <div class="flex gap-1.5 flex-wrap mt-2 items-center">
                                    <span class="report-number px-2 py-0.5 bg-slate-800 text-slate-500 rounded text-xs font-mono">#<?= $reportNumber ?></span>
                                    <?php foreach ($report['versions'] as $vi => $v): ?>
                                        <a href="<?= htmlspecialchars($v['url']) ?>"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 <?= $vi === 0 ? 'bg-emerald-600 hover:bg-emerald-500 text-white font-semibold' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' ?> rounded-md text-xs transition-colors"
                                           title="<?= date('d.m.Y H:i', $v['modified']) ?>">
                                            <?= $vi === 0 ? '🆕' : '📄' ?>
                                            <span><?= htmlspecialchars($v['version']) ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-2 py-3 align-top">
                                <div class="flex gap-1 mb-1">
                                    <button onclick="toggleFavorite('<?= htmlspecialchars($reportId) ?>')" class="fav-btn w-6 h-6 flex items-center justify-center text-slate-600 hover:text-amber-400 transition-colors" title="Favori">☆</button>
                                    <button onclick="hideReport('<?= htmlspecialchars($reportId) ?>')" class="w-6 h-6 flex items-center justify-center text-slate-600 hover:text-red-400 transition-colors" title="Gizle">✕</button>
                                </div>
                                <div class="text-slate-500 text-xs whitespace-nowrap"><?= $d ?> <?= $monthNames[$m] ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Table 3 -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <tbody>
                    <?php foreach ($col3 as $i => $report):
                        list($y, $m, $d) = explode('-', $report['date']);
                        $stripe = $i % 2 === 0 ? 'bg-slate-900/30' : 'bg-slate-900/60';
                        $reportId = $report['topic'];
                        $reportNumber = ($i * 3) + 3;
                    ?>
                        <tr class="report-row <?= $stripe ?> hover:bg-slate-800 border-b border-slate-900/50 transition-colors"
                            data-id="<?= htmlspecialchars($reportId) ?>"
                            data-title="<?= htmlspecialchars(strtolower($report['title'])) ?>">
                            <td class="px-3 py-3">
                                <a href="<?= htmlspecialchars($report['url']) ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-slate-100 hover:text-blue-400 text-sm font-medium leading-relaxed block">
                                    <?= htmlspecialchars($report['title']) ?>
                                </a>
                                <div class="flex gap-1.5 flex-wrap mt-2 items-center">
                                    <span class="report-number px-2 py-0.5 bg-slate-800 text-slate-500 rounded text-xs font-mono">#<?= $reportNumber ?></span>
                                    <?php foreach ($report['versions'] as $vi => $v): ?>
                                        <a href="<?= htmlspecialchars($v['url']) ?>"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 <?= $vi === 0 ? 'bg-emerald-600 hover:bg-emerald-500 text-white font-semibold' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' ?> rounded-md text-xs transition-colors"
                                           title="<?= date('d.m.Y H:i', $v['modified']) ?>">
                                            <?= $vi === 0 ? '🆕' : '📄' ?>
                                            <span><?= htmlspecialchars($v['version']) ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-2 py-3 align-top">
                                <div class="flex gap-1 mb-1">
                                    <button onclick="toggleFavorite('<?= htmlspecialchars($reportId) ?>')" class="fav-btn w-6 h-6 flex items-center justify-center text-slate-600 hover:text-amber-400 transition-colors" title="Favori">☆</button>
                                    <button onclick="hideReport('<?= htmlspecialchars($reportId) ?>')" class="w-6 h-6 flex items-center justify-center text-slate-600 hover:text-red-400 transition-colors" title="Gizle">✕</button>
                                </div>
                                <div class="text-slate-500 text-xs whitespace-nowrap"><?= $d ?> <?= $monthNames[$m] ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Gizlenenler (Katlanabilir) -->
    <div id="hiddenSection" class="hidden px-4 pb-4">
        <button onclick="toggleHidden()"
                class="w-full flex items-center justify-between bg-slate-900/30 hover:bg-slate-900/50 border border-slate-800 rounded-lg px-4 py-2 mb-3 transition-colors">
            <span class="text-slate-500 text-sm">🗂️ Gizlenenler (<span id="hiddenCount">0</span>)</span>
            <svg id="hiddenArrow" class="w-4 h-4 text-slate-600 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div id="hiddenReports" class="hidden">
            <table class="w-full text-sm">
                <tbody id="hiddenTableBody">
                    <!-- Gizlenen raporlar buraya -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- No Results -->
    <div id="noResults" class="hidden text-center py-20 text-slate-600">🔍 Sonuç bulunamadı</div>

    <script>
        // Global preferences (shared across all browsers/PCs)
        let globalPrefs = { favorites: [], hidden: [] };

        // Load preferences from server
        async function loadPrefs() {
            try {
                const response = await fetch('/readme/?action=get_prefs');
                globalPrefs = await response.json();
            } catch (error) {
                console.error('Failed to load preferences:', error);
            }
        }

        // Save preferences to server
        async function savePrefs() {
            try {
                await fetch('/readme/?action=save_prefs', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(globalPrefs)
                });
            } catch (error) {
                console.error('Failed to save preferences:', error);
            }
        }

        // Helpers
        function getFavorites() {
            return globalPrefs.favorites || [];
        }
        function saveFavorites(favorites) {
            globalPrefs.favorites = favorites;
            savePrefs();
        }
        function getHidden() {
            return globalPrefs.hidden || [];
        }
        function saveHidden(hidden) {
            globalPrefs.hidden = hidden;
            savePrefs();
        }

        // Favori Ekle/Çıkar
        function toggleFavorite(reportId) {
            let favorites = getFavorites();
            const index = favorites.indexOf(reportId);
            const favBtns = document.querySelectorAll(`[data-id="${reportId}"] .fav-btn`);

            if (index > -1) {
                // Favoriden çıkar
                favorites.splice(index, 1);
                favBtns.forEach(btn => {
                    btn.classList.remove('text-amber-400');
                    btn.classList.add('text-slate-600');
                    btn.textContent = '☆'; // Boş yıldız
                });
            } else {
                // Favoriye ekle
                favorites.push(reportId);
                favBtns.forEach(btn => {
                    btn.classList.add('text-amber-400');
                    btn.classList.remove('text-slate-600');
                    btn.textContent = '⭐'; // Dolu yıldız
                });
            }

            saveFavorites(favorites);
            updateDisplay();
        }

        // Raporu Gizle
        function hideReport(reportId) {
            let hidden = getHidden();
            if (!hidden.includes(reportId)) {
                hidden.push(reportId);
                saveHidden(hidden);
                updateDisplay();
            }
        }

        // Raporu Göster
        function showReport(reportId) {
            let hidden = getHidden();
            const index = hidden.indexOf(reportId);
            if (index > -1) {
                hidden.splice(index, 1);
                saveHidden(hidden);
                updateDisplay();
            }
        }

        // Gizlenenler Aç/Kapat
        function toggleHidden() {
            const hiddenReports = document.getElementById('hiddenReports');
            const arrow = document.getElementById('hiddenArrow');

            if (hiddenReports.classList.contains('hidden')) {
                hiddenReports.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            } else {
                hiddenReports.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        // Filtre
        function filterReports(filter) {
            const favorites = getFavorites();
            const reportRows = document.querySelectorAll('.report-row');

            // Filtre butonlarını güncelle
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active', 'text-blue-400');
                btn.classList.add('text-slate-500');
            });
            event.target.classList.add('active', 'text-blue-400');
            event.target.classList.remove('text-slate-500');

            // Raporları filtrele
            reportRows.forEach(row => {
                if (row.closest('#hiddenTableBody')) return;
                const reportId = row.getAttribute('data-id');
                if (filter === 'all') {
                    row.style.display = '';
                } else if (filter === 'favorites') {
                    row.style.display = favorites.includes(reportId) ? '' : 'none';
                }
            });

            updateRowNumbers();
        }

        // Sıra Numaralarını Güncelle
        function updateRowNumbers() {
            const visibleReports = Array.from(document.querySelectorAll('.report-row'))
                .filter(row => row.style.display !== 'none' && !row.closest('#hiddenTableBody'));

            visibleReports.forEach((row, index) => {
                const numberEl = row.querySelector('.report-number');
                if (numberEl) numberEl.textContent = `#${index + 1}`;
            });

            document.getElementById('totalReports').textContent = `${visibleReports.length} rapor`;
        }

        // Ekranı Güncelle
        function updateDisplay() {
            const hidden = getHidden();
            const favorites = getFavorites();
            const allRows = document.querySelectorAll('.report-row');
            const hiddenTableBody = document.getElementById('hiddenTableBody');
            const hiddenSection = document.getElementById('hiddenSection');

            hiddenTableBody.innerHTML = '';

            allRows.forEach(row => {
                const reportId = row.getAttribute('data-id');

                if (hidden.includes(reportId)) {
                    // Gizlenmişe taşı
                    const clone = row.cloneNode(true);
                    const hideBtn = clone.querySelector('button:last-child');
                    hideBtn.innerHTML = '↩';
                    hideBtn.title = 'Göster';
                    hideBtn.onclick = () => showReport(reportId);
                    hiddenTableBody.appendChild(clone);
                    row.style.display = 'none';
                } else {
                    row.style.display = '';
                }

                // Favori durumu
                const favBtns = document.querySelectorAll(`[data-id="${reportId}"] .fav-btn`);
                favBtns.forEach(btn => {
                    if (favorites.includes(reportId)) {
                        btn.classList.add('text-amber-400');
                        btn.classList.remove('text-slate-600');
                        btn.textContent = '⭐'; // Dolu yıldız
                    } else {
                        btn.classList.remove('text-amber-400');
                        btn.classList.add('text-slate-600');
                        btn.textContent = '☆'; // Boş yıldız
                    }
                });
            });

            // Gizlenenler bölümünü göster/gizle
            if (hidden.length > 0) {
                hiddenSection.classList.remove('hidden');
                document.getElementById('hiddenCount').textContent = hidden.length;
            } else {
                hiddenSection.classList.add('hidden');
            }

            updateRowNumbers();
        }

        // Arama
        const searchInput = document.getElementById('searchInput');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const reportRows = document.querySelectorAll('.report-row');
            let visibleCount = 0;

            reportRows.forEach(row => {
                if (row.closest('#hiddenTableBody')) return;
                const title = row.getAttribute('data-title');
                if (title.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
            updateRowNumbers();
        });

        // Scroll pozisyonunu geri yükle
        if (sessionStorage.scrollPos) {
            window.scrollTo(0, sessionStorage.scrollPos);
            sessionStorage.removeItem("scrollPos");
        }

        // Manuel yenileme için scroll pozisyonunu kaydet
        window.addEventListener('beforeunload', () => {
            sessionStorage.scrollPos = window.scrollY;
        });

        // Migrate localStorage to server (one-time)
        async function migrateLocalStorage() {
            // Check if localStorage has old data
            const oldFavorites = localStorage.getItem('readme_favorites');
            const oldHidden = localStorage.getItem('readme_hidden');

            if (oldFavorites || oldHidden) {
                console.log('📦 Migrating localStorage to server...');

                // Parse old data
                const favorites = oldFavorites ? JSON.parse(oldFavorites) : [];
                const hidden = oldHidden ? JSON.parse(oldHidden) : [];

                // Load current server data
                await loadPrefs();

                // Merge with existing server data (union, no duplicates)
                const mergedFavorites = [...new Set([...globalPrefs.favorites, ...favorites])];
                const mergedHidden = [...new Set([...globalPrefs.hidden, ...hidden])];

                // Save to server
                globalPrefs.favorites = mergedFavorites;
                globalPrefs.hidden = mergedHidden;
                await savePrefs();

                // Clear localStorage (no longer needed)
                localStorage.removeItem('readme_favorites');
                localStorage.removeItem('readme_hidden');

                console.log('✅ Migration complete!', {
                    favorites: mergedFavorites.length,
                    hidden: mergedHidden.length
                });

                return true;
            }

            return false;
        }

        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', async () => {
            // First, try to migrate localStorage
            const migrated = await migrateLocalStorage();

            // Load preferences from server
            if (!migrated) {
                await loadPrefs();
            }

            // Update display
            updateDisplay();
        });
    </script>

    <style>
        .filter-btn.active {
            color: #60a5fa !important;
        }
    </style>
</body>
</html>
