<?php
// Composer autoloader ÖNCE yüklenmeli!
require_once __DIR__ . '/../../../../../../vendor/autoload.php';

// Laravel bootstrap
$app = require_once __DIR__ . '/../../../../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Tenancy initialize
tenancy()->initialize(1001);

// ========================================
// LEONARDO AI KREDİ BİLGİSİ
// ========================================

use App\Services\Media\LeonardoAIService;

$leonardoCredits = null;
$leonardoApiStatus = 'unknown';

try {
    $leonardoService = new LeonardoAIService();
    $apiStatus = $leonardoService->checkApiStatus();

    if ($apiStatus['status'] === 'ok' && isset($apiStatus['user'][0])) {
        $userInfo = $apiStatus['user'][0];
        // API subscription tokens (ana kredi havuzu)
        $leonardoCredits = $userInfo['apiSubscriptionTokens'] ?? null;
        $leonardoApiStatus = 'ok';
    } else {
        $leonardoApiStatus = 'error';
    }
} catch (\Exception $e) {
    $leonardoApiStatus = 'error';
    Log::warning('Leonardo AI credit check failed', ['error' => $e->getMessage()]);
}

// ========================================
// FOTOĞRAF ÜRETİMİ VERİLERİ
// ========================================

// Toplam aktif şarkı
$photoTotal = DB::connection('tenant')->table('muzibu_songs')
    ->where('is_active', true)
    ->whereNull('deleted_at')
    ->count();

// Hero image'ı olan şarkılar (tüm model_type'lar - Song ve Modules\Muzibu\App\Models\Song)
$photoCompleted = DB::connection('tenant')->table('muzibu_songs')
    ->where('is_active', true)
    ->whereNull('deleted_at')
    ->whereExists(function($query) {
        $query->select(DB::raw(1))
              ->from('media')
              ->whereRaw('media.model_id = muzibu_songs.song_id')
              ->where('media.collection_name', 'hero')
              ->where(function($q) {
                  $q->where('media.model_type', 'Song')
                    ->orWhere('media.model_type', 'LIKE', '%Song');
              });
    })
    ->count();

$photoRemaining = $photoTotal - $photoCompleted;
$photoPercentage = $photoTotal > 0 ? round(($photoCompleted / $photoTotal) * 100, 2) : 0;

// AI Image Queue (Leonardo AI)
exec('redis-cli -n 0 llen queues:muzibu_my_playlist 2>/dev/null', $photoQueueOutput);
$photoQueue = (int)($photoQueueOutput[0] ?? 0);

// AI Image Workers
exec('ps aux | grep "horizon:work" | grep "muzibu_my_playlist" | grep -v grep | wc -l 2>/dev/null', $photoWorkerOutput);
$photoWorkers = (int)($photoWorkerOutput[0] ?? 0);

// Fotoğraf kaydı
$photoRecord = [
    'timestamp' => now()->format('Y-m-d H:i:s'),
    'total' => $photoTotal,
    'completed' => $photoCompleted,
    'remaining' => $photoRemaining,
    'percentage' => $photoPercentage,
    'queue' => $photoQueue,
    'workers' => $photoWorkers,
    'leonardo_credits' => $leonardoCredits,
    'leonardo_api_status' => $leonardoApiStatus,
];

// ========================================
// HLS ÜRETİMİ VERİLERİ (MEVCUT)
// ========================================

$hlsTotal = DB::connection('tenant')->table('muzibu_songs')
    ->where('is_active', true)
    ->whereNull('deleted_at')
    ->count();

$hlsCompleted = DB::connection('tenant')->table('muzibu_songs')
    ->where('is_active', true)
    ->whereNull('deleted_at')
    ->whereNotNull('hls_path')
    ->count();

$hlsRemaining = $hlsTotal - $hlsCompleted;
$hlsPercentage = $hlsTotal > 0 ? round(($hlsCompleted / $hlsTotal) * 100, 2) : 0;

// HLS Queue
exec('redis-cli -n 0 llen queues:muzibu_tenant_1001_hls 2>/dev/null', $hlsQueueOutput);
$hlsQueue = (int)($hlsQueueOutput[0] ?? 0);

// HLS Workers
exec('ps aux | grep "horizon:work" | grep "muzibu_tenant_1001_hls" | grep -v grep | wc -l 2>/dev/null', $hlsWorkerOutput);
$hlsWorkers = (int)($hlsWorkerOutput[0] ?? 0);

// HLS kaydı
$hlsRecord = [
    'timestamp' => now()->format('Y-m-d H:i:s'),
    'total' => $hlsTotal,
    'completed' => $hlsCompleted,
    'remaining' => $hlsRemaining,
    'percentage' => $hlsPercentage,
    'queue' => $hlsQueue,
    'workers' => $hlsWorkers,
];

// ========================================
// DATA.JSON YÖNETİMİ
// ========================================

// Fotoğraf data
$photoDataFile = __DIR__ . '/v1/photo-data.json';
$photoData = [];
if (file_exists($photoDataFile)) {
    $photoData = json_decode(file_get_contents($photoDataFile), true) ?: [];
}

// HLS data (mevcut)
$hlsDataFile = __DIR__ . '/v1/data.json';
$hlsData = [];
if (file_exists($hlsDataFile)) {
    $hlsData = json_decode(file_get_contents($hlsDataFile), true) ?: [];
}

// Lock mekanizması
$lockFile = __DIR__ . '/v1/.update.lock';
$fp = fopen($lockFile, 'w');
$canUpdate = flock($fp, LOCK_EX | LOCK_NB);

if ($canUpdate) {
    $shouldUpdatePhoto = false;
    $shouldUpdateHls = false;

    // FOTOĞRAF güncelleme kontrolü
    if (empty($photoData) || !isset($photoData[0]) || !is_array($photoData[0])) {
        $shouldUpdatePhoto = true;
    } else {
        $lastPhotoRecord = $photoData[0];
        if ($lastPhotoRecord['completed'] != $photoCompleted) {
            $shouldUpdatePhoto = true;
        } else {
            $lastTime = strtotime($lastPhotoRecord['timestamp']);
            $minutesPassed = (time() - $lastTime) / 60;
            if ($minutesPassed >= 2) {
                $shouldUpdatePhoto = true;
            }
        }
    }

    // HLS güncelleme kontrolü
    if (empty($hlsData) || !isset($hlsData[0]) || !is_array($hlsData[0])) {
        $shouldUpdateHls = true;
    } else {
        $lastHlsRecord = $hlsData[0];
        if ($lastHlsRecord['completed'] != $hlsCompleted) {
            $shouldUpdateHls = true;
        } else {
            $lastTime = strtotime($lastHlsRecord['timestamp']);
            $minutesPassed = (time() - $lastTime) / 60;
            if ($minutesPassed >= 2) {
                $shouldUpdateHls = true;
            }
        }
    }

    // Fotoğraf kaydet
    if ($shouldUpdatePhoto) {
        array_unshift($photoData, $photoRecord);
        $photoData = array_slice($photoData, 0, 50);
        file_put_contents($photoDataFile, json_encode($photoData, JSON_PRETTY_PRINT));
        chmod($photoDataFile, 0644);
    }

    // HLS kaydet
    if ($shouldUpdateHls) {
        array_unshift($hlsData, $hlsRecord);
        $hlsData = array_slice($hlsData, 0, 50);
        file_put_contents($hlsDataFile, json_encode($hlsData, JSON_PRETTY_PRINT));
        chmod($hlsDataFile, 0644);
    }

    flock($fp, LOCK_UN);
}

fclose($fp);

// JSON olarak data'ları hazırla
$photoJsonData = json_encode($photoData);
$hlsJsonData = json_encode($hlsData);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Muzibu İşlem Durumu - HLS & Fotoğraf</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .pulse-dot { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen p-8">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">🎵 Muzibu İşlem Durumu</h1>
                    <p class="text-blue-100">Fotoğraf Üretimi & HLS Dönüşümü - Canlı İzleme</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-blue-100">Otomatik Yenileme</div>
                    <div class="text-xl font-bold">30 saniye</div>
                    <div class="flex items-center gap-2 justify-end mt-2">
                        <div class="w-3 h-3 bg-green-400 rounded-full pulse-dot"></div>
                        <span class="text-sm">Canlı İzleme</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================
             FOTOĞRAF ÜRETİMİ TABLOSU
        ========================================= -->

        <div class="mb-12">
            <div class="bg-gradient-to-r from-purple-700 to-purple-600 rounded-t-lg px-6 py-4">
                <h2 class="text-2xl font-bold">🎨 Şarkı Fotoğraf Üretimi (Leonardo AI)</h2>
            </div>

            <!-- Güncel Durum (Fotoğraf) -->
            <div id="photoCurrentStatus" class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6 bg-gray-800 p-6 rounded-b-lg">
                <!-- JavaScript ile doldurulacak -->
            </div>

            <!-- Tarihçe Tablosu (Fotoğraf) -->
            <div class="bg-gray-800 rounded-lg overflow-hidden border border-gray-700 mt-6">
                <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-bold">📊 Fotoğraf Üretim Tarihçesi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Tarih/Saat</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Tamamlanan</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Artış</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Geçen Süre</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">İlerleme</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Queue</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Workers</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Leonardo AI</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Hız</th>
                            </tr>
                        </thead>
                        <tbody id="photoHistoryTable" class="divide-y divide-gray-700">
                            <!-- JavaScript ile doldurulacak -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================
             HLS ÜRETİMİ TABLOSU (MEVCUT)
        ========================================= -->

        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-700 to-blue-600 rounded-t-lg px-6 py-4">
                <h2 class="text-2xl font-bold">🎵 HLS Dönüşümü (Audio Streaming)</h2>
            </div>

            <!-- Güncel Durum (HLS) -->
            <div id="hlsCurrentStatus" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 bg-gray-800 p-6 rounded-b-lg">
                <!-- JavaScript ile doldurulacak -->
            </div>

            <!-- Tarihçe Tablosu (HLS) -->
            <div class="bg-gray-800 rounded-lg overflow-hidden border border-gray-700 mt-6">
                <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-bold">📊 HLS Üretim Tarihçesi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Tarih/Saat</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Tamamlanan</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Artış</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Geçen Süre</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">İlerleme</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Queue</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Workers</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Hız</th>
                            </tr>
                        </thead>
                        <tbody id="hlsHistoryTable" class="divide-y divide-gray-700">
                            <!-- JavaScript ile doldurulacak -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>💡 Sayfa otomatik olarak 30 saniyede bir yenileniyor</p>
            <p class="mt-2">Son güncelleme: <?php echo date('d.m.Y H:i:s'); ?></p>
        </div>

    </div>

    <script>
        // PHP'den gelen veriler
        const photoData = <?php echo $photoJsonData; ?>;
        const hlsData = <?php echo $hlsJsonData; ?>;

        // İlk render
        if (photoData.length > 0) {
            renderCurrentStatus('photo', photoData[0]);
            renderHistoryTable('photo', photoData);
        }

        if (hlsData.length > 0) {
            renderCurrentStatus('hls', hlsData[0]);
            renderHistoryTable('hls', hlsData);
        }

        // Otomatik yenileme (30 saniye)
        setTimeout(() => {
            window.location.reload();
        }, 30000);

        // Güncel durumu göster
        function renderCurrentStatus(type, current) {
            const color = type === 'photo' ? 'purple' : 'blue';
            const label = type === 'photo' ? 'Fotoğraf' : 'HLS';

            let creditCard = '';
            if (type === 'photo' && current.leonardo_credits !== undefined && current.leonardo_credits !== null) {
                const creditColor = current.leonardo_credits < 100 ? 'red' : current.leonardo_credits < 500 ? 'yellow' : 'cyan';
                const creditStatus = current.leonardo_api_status === 'ok' ? '✓' : '⚠';

                // 20 token/şarkı varsayımıyla kaç şarkıya yeter
                const songsCanGenerate = Math.floor(current.leonardo_credits / 20);
                const canFinishAll = songsCanGenerate >= current.remaining;
                const capacityColor = canFinishAll ? 'text-green-400' : 'text-orange-400';
                const capacityText = canFinishAll
                    ? `✅ Tümüne yeter (${songsCanGenerate.toLocaleString('tr-TR')} şarkı)`
                    : `⚠️ ${songsCanGenerate.toLocaleString('tr-TR')} şarkıya yeter`;

                creditCard = `
                    <div class="bg-gray-800 rounded-lg p-6 border border-${creditColor}-700">
                        <div class="text-gray-400 text-sm mb-2">Leonardo AI Kredi ${creditStatus}</div>
                        <div class="text-4xl font-bold text-${creditColor}-400">${current.leonardo_credits.toLocaleString('tr-TR')}</div>
                        <div class="text-xs ${capacityColor} mt-2 font-semibold">${capacityText}</div>
                    </div>
                `;
            }

            const html = `
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <div class="text-gray-400 text-sm mb-2">Toplam Şarkı</div>
                    <div class="text-4xl font-bold text-white">${current.total.toLocaleString('tr-TR')}</div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6 border border-green-700">
                    <div class="text-gray-400 text-sm mb-2">Tamamlanan ${label}</div>
                    <div class="text-4xl font-bold text-green-400">${current.completed.toLocaleString('tr-TR')}</div>
                    <div class="text-sm text-green-300 mt-2">${current.percentage}%</div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6 border border-yellow-700">
                    <div class="text-gray-400 text-sm mb-2">Kalan ${label}</div>
                    <div class="text-4xl font-bold text-yellow-400">${current.remaining.toLocaleString('tr-TR')}</div>
                    <div class="text-sm text-yellow-300 mt-2">${(100 - current.percentage).toFixed(2)}%</div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6 border border-${color}-700">
                    <div class="text-gray-400 text-sm mb-2">Aktif Worker</div>
                    <div class="text-4xl font-bold text-${color}-400">${current.workers}</div>
                    <div class="text-sm text-${color}-300 mt-2">Queue: ${current.queue.toLocaleString('tr-TR')}</div>
                </div>

                ${creditCard}
            `;
            document.getElementById(`${type}CurrentStatus`).innerHTML = html;
        }

        // Tarihçe tablosunu doldur
        function renderHistoryTable(type, data) {
            let html = '';

            console.log(`[${type}] Rendering history table with ${data.length} records`);

            for (let i = 0; i < Math.min(data.length, 10); i++) {
                const current = data[i];
                const previous = data[i + 1];

                let increase = 0;
                let increaseText = '-';
                let increaseClass = 'text-gray-400';

                if (previous) {
                    increase = current.completed - previous.completed;
                    console.log(`[${type}] Row ${i}: current=${current.completed}, previous=${previous.completed}, increase=${increase}`);
                    if (increase > 0) {
                        increaseText = `+${increase.toLocaleString('tr-TR')}`;
                        increaseClass = 'text-green-400 font-semibold';
                    } else if (increase < 0) {
                        increaseText = increase.toLocaleString('tr-TR');
                        increaseClass = 'text-red-400';
                    } else {
                        increaseText = '0';
                    }
                } else {
                    console.log(`[${type}] Row ${i}: No previous record`);
                }

                let elapsedText = '-';
                let speedText = '-';

                if (previous) {
                    const currentTime = new Date(current.timestamp);
                    const previousTime = new Date(previous.timestamp);
                    const diffMs = currentTime - previousTime;
                    const diffMinutes = Math.floor(diffMs / 1000 / 60);
                    const diffHours = Math.floor(diffMinutes / 60);
                    const remainingMinutes = diffMinutes % 60;

                    if (diffHours > 0) {
                        elapsedText = `${diffHours}s ${remainingMinutes}dk`;
                    } else if (diffMinutes > 0) {
                        elapsedText = `${diffMinutes}dk`;
                    } else {
                        elapsedText = '< 1dk';
                    }

                    if (diffMinutes > 0 && increase > 0) {
                        const speed = Math.round((increase / diffMinutes) * 60);
                        speedText = `${speed.toLocaleString('tr-TR')}/saat`;
                    }
                }

                const progressWidth = current.percentage;
                const progressColor = current.percentage < 30 ? 'bg-red-500' :
                                     current.percentage < 70 ? 'bg-yellow-500' : 'bg-green-500';

                const rowClass = i === 0 ? 'bg-purple-900/20' : 'hover:bg-gray-750';

                // Leonardo AI credits (sadece photo için)
                let leonardoCell = '';
                if (type === 'photo' && current.leonardo_credits !== undefined && current.leonardo_credits !== null) {
                    const creditColor = current.leonardo_credits < 100 ? 'text-red-400' : current.leonardo_credits < 500 ? 'text-yellow-400' : 'text-cyan-400';

                    // Harcanan kredi hesapla (önceki - şimdiki)
                    let spentText = '';
                    if (previous && previous.leonardo_credits !== undefined && previous.leonardo_credits !== null) {
                        const spent = previous.leonardo_credits - current.leonardo_credits;
                        if (spent > 0) {
                            spentText = ` <span class="text-red-400">(-${spent.toLocaleString('tr-TR')})</span>`;
                        } else if (spent < 0) {
                            spentText = ` <span class="text-green-400">(+${Math.abs(spent).toLocaleString('tr-TR')})</span>`;
                        }
                    }

                    leonardoCell = `<td class="px-4 py-4 text-center text-sm ${creditColor}">
                        ${current.leonardo_credits.toLocaleString('tr-TR')}${spentText}
                    </td>`;
                } else if (type === 'photo') {
                    leonardoCell = `<td class="px-4 py-4 text-center text-sm text-gray-500">-</td>`;
                }

                html += `
                    <tr class="${rowClass}">
                        <td class="px-4 py-4 text-sm whitespace-nowrap">
                            ${i === 0 ? '<span class="text-green-400">●</span> ' : ''}
                            ${current.timestamp}
                        </td>
                        <td class="px-4 py-4 text-center text-lg font-semibold text-white">
                            ${current.completed.toLocaleString('tr-TR')}
                        </td>
                        <td class="px-4 py-4 text-center ${increaseClass}">
                            ${increaseText}
                        </td>
                        <td class="px-4 py-4 text-center text-sm text-gray-300">
                            ${elapsedText}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-700 rounded-full h-2">
                                    <div class="${progressColor} h-2 rounded-full" style="width: ${progressWidth}%"></div>
                                </div>
                                <span class="text-xs text-gray-400 w-12 text-right">${current.percentage}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center text-sm text-blue-400">
                            ${current.queue.toLocaleString('tr-TR')}
                        </td>
                        <td class="px-4 py-4 text-center text-sm text-purple-400">
                            ${current.workers}
                        </td>
                        ${leonardoCell}
                        <td class="px-4 py-4 text-center text-sm text-yellow-400">
                            ${speedText}
                        </td>
                    </tr>
                `;
            }

            document.getElementById(`${type}HistoryTable`).innerHTML = html;
        }
    </script>
</body>
</html>
