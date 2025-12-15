<?php
// README Index - Otomatik Rapor Listesi
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
                            'url' => "/readme/$year/$month/$day/$topic/",
                            'versions' => $versions,
                            'latestModified' => $versions[0]['modified']
                        ];
                    }
                }
            }
        }
    }

    usort($result, function($a, $b) {
        return $b['latestModified'] - $a['latestModified'];
    });

    return $result;
}

$reports = scanReports($baseDir);

$monthNames = [
    '01' => 'Oca', '02' => 'Şub', '03' => 'Mar', '04' => 'Nis',
    '05' => 'May', '06' => 'Haz', '07' => 'Tem', '08' => 'Ağu',
    '09' => 'Eyl', '10' => 'Eki', '11' => 'Kas', '12' => 'Ara'
];

$totalReports = count($reports);
$totalVersions = array_sum(array_map(function($r) { return count($r['versions']); }, $reports));
$currentDomain = $_SERVER['HTTP_HOST'] ?? 'ixtif.com';

// 3'e böl
$chunk = ceil($totalReports / 3);
$col1 = array_slice($reports, 0, $chunk);
$col2 = array_slice($reports, $chunk, $chunk);
$col3 = array_slice($reports, $chunk * 2);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📚 README</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

            <!-- Right: Stats -->
            <div class="flex justify-end gap-2 text-xs text-slate-500">
                <span><?= $totalReports ?> rapor</span>
                <span>•</span>
                <span><?= $totalVersions ?> v</span>
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
                    ?>
                        <tr class="report-row <?= $stripe ?> hover:bg-slate-800 border-b border-slate-900/50 transition-colors"
                            data-title="<?= htmlspecialchars(strtolower($report['title'])) ?>">
                            <td class="px-3 py-3">
                                <a href="<?= htmlspecialchars($report['url']) ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-slate-100 hover:text-blue-400 text-sm font-medium leading-relaxed block">
                                    <?= htmlspecialchars($report['title']) ?>
                                </a>
                                <div class="flex gap-1.5 flex-wrap mt-2">
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
                            <td class="px-3 py-3 text-slate-400 text-xs whitespace-nowrap align-top">
                                <?= $d ?> <?= $monthNames[$m] ?>
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
                    ?>
                        <tr class="report-row <?= $stripe ?> hover:bg-slate-800 border-b border-slate-900/50 transition-colors"
                            data-title="<?= htmlspecialchars(strtolower($report['title'])) ?>">
                            <td class="px-3 py-3">
                                <a href="<?= htmlspecialchars($report['url']) ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-slate-100 hover:text-blue-400 text-sm font-medium leading-relaxed block">
                                    <?= htmlspecialchars($report['title']) ?>
                                </a>
                                <div class="flex gap-1.5 flex-wrap mt-2">
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
                            <td class="px-3 py-3 text-slate-400 text-xs whitespace-nowrap align-top">
                                <?= $d ?> <?= $monthNames[$m] ?>
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
                    ?>
                        <tr class="report-row <?= $stripe ?> hover:bg-slate-800 border-b border-slate-900/50 transition-colors"
                            data-title="<?= htmlspecialchars(strtolower($report['title'])) ?>">
                            <td class="px-3 py-3">
                                <a href="<?= htmlspecialchars($report['url']) ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-slate-100 hover:text-blue-400 text-sm font-medium leading-relaxed block">
                                    <?= htmlspecialchars($report['title']) ?>
                                </a>
                                <div class="flex gap-1.5 flex-wrap mt-2">
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
                            <td class="px-3 py-3 text-slate-400 text-xs whitespace-nowrap align-top">
                                <?= $d ?> <?= $monthNames[$m] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- No Results -->
    <div id="noResults" class="hidden text-center py-20 text-slate-600">🔍 Sonuç bulunamadı</div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const reportRows = document.querySelectorAll('.report-row');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            let visibleCount = 0;

            reportRows.forEach(row => {
                if (row.dataset.title.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
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
    </script>
</body>
</html>
