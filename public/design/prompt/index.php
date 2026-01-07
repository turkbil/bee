<?php
/**
 * Tasarim Promptu - v1/v2 Secimi
 */

$version = $_GET['v'] ?? 'v3';
$promptFile = __DIR__ . "/{$version}/prompt.txt";
$promptContent = file_exists($promptFile) ? file_get_contents($promptFile) : '';
$promptContent = htmlspecialchars($promptContent, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prompt <?= $version === 'v3' ? '(v3 FINAL - 26 İyileştirme)' : ($version === 'v2' ? '(600+ Layout)' : '(Klasik)') ?> | Tasarim Merkezi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Nunito', system-ui, sans-serif; }
        .toast {
            position: fixed; bottom: 2rem; right: 2rem;
            background: #10b981; color: white;
            padding: 0.5rem 1rem; border-radius: 0.5rem;
            transform: translateY(100px); opacity: 0;
            transition: all 0.3s; z-index: 9999;
            font-size: 0.875rem;
        }
        .toast.show { transform: translateY(0); opacity: 1; }
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
        .info-box { transition: border-color 0.2s; }
        .info-box:hover { border-color: #6366f1; }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen">

    <!-- Toast -->
    <div id="toast" class="toast"><i class="fas fa-check-circle mr-2"></i><span>Kopyalandi!</span></div>

    <!-- Header -->
    <header class="border-b border-slate-800 sticky top-0 bg-slate-950/95 backdrop-blur z-50">
        <div class="w-full px-4 py-2.5 flex items-center gap-2 overflow-visible">
            <!-- Tabs -->
            <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-lg shrink-0">
                <a href="/design/templates/" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800">Taslaklar</a>
                <a href="/design/sectors/" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800">Tarz</a>
                <a href="/design/prompt/" class="px-3 py-1.5 rounded-md text-sm font-medium bg-violet-600 text-white">Prompt</a>
            </div>

            <span class="text-slate-700">|</span>

            <!-- Version Toggle -->
            <a href="?v=v1" class="px-3 py-1.5 rounded-lg text-sm transition <?= $version === 'v1' ? 'bg-slate-700 text-white' : 'bg-slate-900 text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                v1 Klasik
            </a>
            <a href="?v=v2" class="px-3 py-1.5 rounded-lg text-sm transition <?= $version === 'v2' ? 'bg-emerald-600 text-white' : 'bg-slate-900 text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fas fa-sparkles mr-1"></i>v2 Ozgunluk
            </a>
            <a href="?v=v3" class="px-3 py-1.5 rounded-lg text-sm transition <?= $version === 'v3' ? 'bg-violet-600 text-white' : 'bg-slate-900 text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fas fa-crown mr-1"></i>v3 FINAL
            </a>

            <span class="text-slate-700">|</span>

            <!-- Actions -->
            <button onclick="copyPrompt()" class="tooltip px-3 py-1.5 text-sm rounded-lg bg-violet-900/50 text-violet-300 hover:bg-violet-800 shrink-0" data-tip="Kopyala">
                <i class="fas fa-copy"></i>
            </button>

            <!-- Style Stats -->
            <div id="styleStats" class="ml-auto text-xs text-slate-500 hidden items-center gap-2">
                <i class="fas fa-palette text-violet-400"></i>
                <span id="styleCountText">0 stil</span>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="w-full px-6 py-6">
        <div class="max-w-4xl mx-auto">

            <?php if ($version === 'v3'): ?>
            <!-- v3 Info -->
            <div class="mb-6 bg-violet-900/20 border border-violet-800/50 rounded-xl p-4 info-box">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-violet-500/20 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fas fa-crown text-violet-400"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold text-violet-300">v3 FINAL (26 İyileştirme - 100/100 Skor)</h3>
                            <span class="bg-violet-600 text-white text-xs px-2 py-0.5 rounded-full">Maksimum Özgünlük</span>
                        </div>
                        <p class="text-sm text-slate-400 mb-2">En gelişmiş prompt versiyonu. FontAwesome Pro 7 local, gradient text & animated borders, dark/light mode uyumu, hover'da border > shadow, renk bütünlüğü.</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                            <div class="bg-violet-950/50 rounded px-2 py-1"><i class="fas fa-check text-violet-400 mr-1"></i>FontAwesome Pro 7</div>
                            <div class="bg-violet-950/50 rounded px-2 py-1"><i class="fas fa-check text-violet-400 mr-1"></i>Gradient Text</div>
                            <div class="bg-violet-950/50 rounded px-2 py-1"><i class="fas fa-check text-violet-400 mr-1"></i>Animated Borders</div>
                            <div class="bg-violet-950/50 rounded px-2 py-1"><i class="fas fa-check text-violet-400 mr-1"></i>Dark/Light Mode</div>
                            <div class="bg-violet-950/50 rounded px-2 py-1"><i class="fas fa-check text-violet-400 mr-1"></i>Border > Shadow</div>
                            <div class="bg-violet-950/50 rounded px-2 py-1"><i class="fas fa-check text-violet-400 mr-1"></i>Renk Bütünlüğü</div>
                            <div class="bg-violet-950/50 rounded px-2 py-1"><i class="fas fa-check text-violet-400 mr-1"></i>Icon fat→fas</div>
                            <div class="bg-violet-950/50 rounded px-2 py-1"><i class="fas fa-check text-violet-400 mr-1"></i>Mega Menu Fix</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php elseif ($version === 'v2'): ?>
            <!-- v2 Info -->
            <div class="mb-6 bg-emerald-900/20 border border-emerald-800/50 rounded-xl p-4 info-box">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fas fa-sparkles text-emerald-400"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-emerald-300 mb-1">Ozgunluk Promptu (600+ Layout)</h3>
                        <p class="text-sm text-slate-400">Her section icin 100+ layout secenegi. AI sectigini prompt modal'inda belirtir.</p>
                        <a href="?v=v3" class="text-xs text-emerald-400 hover:text-emerald-300 mt-2 inline-flex items-center gap-1">
                            <i class="fas fa-arrow-right"></i>v3 FINAL versiyonunu dene (18 iyileştirme)
                        </a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- v1 Warning -->
            <div class="mb-6 bg-amber-900/20 border border-amber-800/50 rounded-xl p-4 info-box">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fas fa-exclamation-triangle text-amber-400"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-amber-300 mb-1">Klasik Prompt</h3>
                        <p class="text-sm text-slate-400">Bu prompt'ta layout cesitliligi sinirli. Benzer tasarimlar uretebilir.</p>
                        <a href="?v=v3" class="text-xs text-amber-400 hover:text-amber-300 mt-2 inline-flex items-center gap-1">
                            <i class="fas fa-arrow-right"></i>v3 FINAL versiyonunu dene (18 iyileştirme)
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Secilen Tarz -->
            <div class="mb-6" id="designStylesContainer" style="display: none;">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm text-slate-400 flex items-center gap-2">
                        <i class="fas fa-palette text-violet-400"></i>
                        Secilen Tarz
                        <span id="styleCountBadge" class="bg-violet-600 text-white text-xs px-2 py-0.5 rounded-full"></span>
                    </label>
                    <div class="flex items-center gap-3">
                        <a href="/design/sectors/" class="text-xs text-slate-500 hover:text-violet-400">
                            <i class="fas fa-edit mr-1"></i>Duzenle
                        </a>
                        <button onclick="clearDesignStyles()" class="text-xs text-slate-500 hover:text-red-400">
                            <i class="fas fa-times mr-1"></i>Temizle
                        </button>
                    </div>
                </div>
                <textarea id="designStylesInput" readonly rows="4" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-4 py-3 text-violet-300 text-sm focus:outline-none resize-none"></textarea>
            </div>

            <!-- No Styles -->
            <div id="noStylesMessage" class="mb-6 bg-slate-900 border border-slate-800 rounded-lg p-6 text-center info-box">
                <i class="fas fa-palette text-slate-700 text-3xl mb-3"></i>
                <p class="text-slate-500 mb-3 text-sm">Henuz tarz secimi yapilmadi (opsiyonel)</p>
                <a href="/design/sectors/" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm rounded-lg">
                    <i class="fas fa-palette"></i>Tarz Sec
                </a>
            </div>

            <!-- Inputs -->
            <div class="grid sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs text-slate-500 mb-1.5">Sektor</label>
                    <input type="text" id="sektorInput" placeholder="orn: saglik, belediye, fabrika..." class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2.5 text-white text-sm placeholder-slate-600 focus:outline-none focus:border-slate-600">
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1.5">Tarz</label>
                    <input type="text" id="tarzInput" placeholder="orn: minimal, prestijli, samimi..." class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2.5 text-white text-sm placeholder-slate-600 focus:outline-none focus:border-slate-600">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-xs text-slate-500 mb-1.5">Ek Bilgiler <span class="text-slate-700">(opsiyonel)</span></label>
                <textarea id="ekBilgiInput" rows="2" placeholder="orn: Ana faaliyet madencilik (%60), yan kollar muhendislik..." class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2.5 text-white text-sm placeholder-slate-600 focus:outline-none focus:border-slate-600 resize-none"></textarea>
            </div>

            <!-- Prompt Box -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="bg-slate-800/50 px-4 py-2.5 border-b border-slate-700 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        <i class="fas fa-code mr-1.5"></i>Prompt (<?= $version ?>)
                    </span>
                    <button onclick="copyPrompt()" class="text-xs bg-violet-600 hover:bg-violet-500 px-3 py-1.5 rounded-lg">
                        <i class="fas fa-copy mr-1.5"></i>Kopyala
                    </button>
                </div>
                <div class="p-4">
                    <pre id="promptText" class="text-sm text-slate-300 whitespace-pre-wrap leading-relaxed max-h-[50vh] overflow-y-auto"><span id="designStylesSection"></span><?= $promptContent ?></pre>
                </div>
            </div>

            <p class="mt-4 text-center text-xs text-slate-600">
                <i class="fas fa-info-circle mr-1"></i>
                AI bu prompt ile site uretirken <code class="bg-slate-900 px-1.5 py-0.5 rounded">prompt.html</code> dosyasi da olusturur.
            </p>

        </div>
    </main>

    <script>
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.querySelector('span').textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2000);
    }

    function copyPrompt() {
        navigator.clipboard.writeText(document.getElementById('promptText').innerText).then(() => showToast('Prompt kopyalandi!'));
    }

    function clearDesignStyles() {
        localStorage.removeItem('designStyles');
        localStorage.removeItem('designStylesCount');
        document.getElementById('designStylesContainer').style.display = 'none';
        document.getElementById('noStylesMessage').style.display = 'block';
        document.getElementById('designStylesInput').value = '';
        document.getElementById('designStylesSection').textContent = '';
        document.getElementById('styleStats').classList.add('hidden');
        showToast('Tarz secimleri temizlendi');
    }

    function updateDesignStylesInPrompt(styles) {
        const section = document.getElementById('designStylesSection');
        section.textContent = styles ? 'TASARIM STILLERI (Secilen - ONCELIKLI):\n' + styles + '\n\n---\n\n' : '';
    }

    function updateTarzFromStyles(styles) {
        if (!styles) return;
        const lines = styles.split('\n').filter(l => l.trim() && !l.startsWith('#'));
        const summary = lines.map(l => l.replace(/^-\s*/, '').trim()).filter(l => l).join(', ');
        if (summary) {
            document.getElementById('promptText').innerHTML = document.getElementById('promptText').innerHTML.replace(/tarz: .*$/m, 'tarz: ' + summary);
            document.getElementById('tarzInput').value = summary;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const designStyles = localStorage.getItem('designStyles');
        const styleCount = localStorage.getItem('designStylesCount');

        if (designStyles) {
            document.getElementById('designStylesContainer').style.display = 'block';
            document.getElementById('noStylesMessage').style.display = 'none';
            document.getElementById('designStylesInput').value = designStyles;

            if (styleCount) {
                document.getElementById('styleCountBadge').textContent = styleCount + ' stil';
                document.getElementById('styleCountText').textContent = styleCount + ' stil';
                document.getElementById('styleStats').classList.remove('hidden');
                document.getElementById('styleStats').classList.add('flex');
            }

            updateDesignStylesInPrompt(designStyles);
            updateTarzFromStyles(designStyles);
        }

        const promptText = document.getElementById('promptText');

        document.getElementById('sektorInput').addEventListener('input', function() {
            promptText.innerHTML = promptText.innerHTML.replace(/sektor: .*$/m, 'sektor: ' + (this.value.trim() || 'genel kurumsal'));
        });

        document.getElementById('tarzInput').addEventListener('input', function() {
            promptText.innerHTML = promptText.innerHTML.replace(/tarz: .*$/m, 'tarz: ' + (this.value.trim() || 'profesyonel ve dengeli'));
        });

        document.getElementById('ekBilgiInput').addEventListener('input', function() {
            const val = this.value.trim() || '(kullanici tarafindan doldurulacak)';
            promptText.innerHTML = promptText.innerHTML.replace(/ek_bilgiler: .*?(?=\n\n|$)/s, 'ek_bilgiler: ' + val.replace(/\n/g, '\n   '));
        });
    });
    </script>

</body>
</html>
