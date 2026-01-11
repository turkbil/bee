<?php
// Composer autoloader ÖNCE yüklenmeli!
require_once __DIR__ . '/../../../../../../vendor/autoload.php';

// Laravel bootstrap
$app = require_once __DIR__ . '/../../../../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Tenancy initialize
tenancy()->initialize(1001);

// Güncel verileri çek
$total = DB::connection('tenant')->table('muzibu_songs')
    ->where('is_active', true)
    ->whereNull('deleted_at')
    ->count();

$completed = DB::connection('tenant')->table('muzibu_songs')
    ->where('is_active', true)
    ->whereNull('deleted_at')
    ->whereNotNull('hls_path')
    ->count();

$remaining = $total - $completed;
$percentage = round(($completed / $total) * 100, 2);

// Queue ve worker
exec('redis-cli -n 0 llen queues:muzibu_tenant_1001_hls', $queueOutput);
$queue = (int)($queueOutput[0] ?? 0);

exec('ps aux | grep "horizon:work" | grep "muzibu_tenant_1001_hls" | grep -v grep | wc -l', $workerOutput);
$workers = (int)($workerOutput[0] ?? 0);

// Yeni kayıt
$newRecord = [
    'timestamp' => now()->format('Y-m-d H:i:s'),
    'total' => $total,
    'completed' => $completed,
    'remaining' => $remaining,
    'percentage' => $percentage,
    'queue' => $queue,
    'workers' => $workers,
];

// Mevcut data.json'ı oku
$dataFile = __DIR__ . '/v1/data.json';
$data = [];
if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true) ?: [];
}

// Lock mekanizması (concurrent access kontrolü)
$lockFile = __DIR__ . '/v1/.update.lock';
$fp = fopen($lockFile, 'w');
$canUpdate = flock($fp, LOCK_EX | LOCK_NB); // Non-blocking lock

// Güncelleme kontrolü: Completed değişmişse VEYA 2 dakika geçmişse
$shouldUpdate = false;

// Lock alınabiliyorsa güncelleme yap
if ($canUpdate) {
    if (empty($data)) {
        $shouldUpdate = true;
    } else {
        $lastRecord = $data[0];

        // Completed değişmişse güncelle
        if ($lastRecord['completed'] != $completed) {
            $shouldUpdate = true;
        } else {
            // Completed değişmemişse de, 2 dakika geçmişse queue/workers güncellemesi için ekle
            $lastTime = strtotime($lastRecord['timestamp']);
            $currentTime = time();
            $minutesPassed = ($currentTime - $lastTime) / 60;

            if ($minutesPassed >= 2) {
                $shouldUpdate = true;
            }
        }
    }

    if ($shouldUpdate) {
        // Başa ekle
        array_unshift($data, $newRecord);

        // Son 50 kaydı sakla
        $data = array_slice($data, 0, 50);

        // Kaydet
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
        chmod($dataFile, 0644);
    }

    // Lock'u serbest bırak
    flock($fp, LOCK_UN);
}
// Lock alınamazsa (başkası güncelliyor), mevcut veriyi göster (skip)

fclose($fp);

// JSON olarak data'yı hazırla
$jsonData = json_encode($data);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HLS İşleme Durumu - Muzibu</title>
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
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">🎵 HLS İşleme Durumu</h1>
                    <p class="text-blue-100">Muzibu Platform - Canlı İzleme</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-blue-100">Toplam Kayıt</div>
                    <div class="text-xl font-bold" id="recordCount">-</div>
                    <div class="flex items-center gap-2 justify-end mt-2">
                        <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
                        <span class="text-sm">Sayfa yenilendiğinde güncellenir</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Güncel Durum (Büyük Kartlar) -->
        <div id="currentStatus" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- JavaScript ile doldurulacak -->
        </div>

        <!-- Tarihçe Tablosu -->
        <div class="bg-gray-800 rounded-lg overflow-hidden border border-gray-700">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h2 class="text-xl font-bold">📊 Zaman İçinde İlerleme</h2>
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
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Sil</th>
                        </tr>
                    </thead>
                    <tbody id="historyTable" class="divide-y divide-gray-700">
                        <!-- JavaScript ile doldurulacak -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>💡 Güncel verileri görmek için sayfayı yenileyin (F5)</p>
            <p class="mt-2">Son güncelleme: <?php echo date('d.m.Y H:i:s'); ?></p>
        </div>

    </div>

    <script>
        // PHP'den gelen veri
        const data = <?php echo $jsonData; ?>;

        // Render
        renderCurrentStatus(data[0]);
        renderHistoryTable(data);
        document.getElementById('recordCount').textContent = data.length;

        // Güncel durumu göster
        function renderCurrentStatus(current) {
            const html = `
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <div class="text-gray-400 text-sm mb-2">Toplam Şarkı</div>
                    <div class="text-4xl font-bold text-white">${current.total.toLocaleString('tr-TR')}</div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6 border border-green-700">
                    <div class="text-gray-400 text-sm mb-2">Tamamlanan HLS</div>
                    <div class="text-4xl font-bold text-green-400">${current.completed.toLocaleString('tr-TR')}</div>
                    <div class="text-sm text-green-300 mt-2">${current.percentage}%</div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6 border border-yellow-700">
                    <div class="text-gray-400 text-sm mb-2">Kalan HLS</div>
                    <div class="text-4xl font-bold text-yellow-400">${current.remaining.toLocaleString('tr-TR')}</div>
                    <div class="text-sm text-yellow-300 mt-2">${(100 - current.percentage).toFixed(2)}%</div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6 border border-blue-700">
                    <div class="text-gray-400 text-sm mb-2">Aktif Worker</div>
                    <div class="text-4xl font-bold text-blue-400">${current.workers}</div>
                    <div class="text-sm text-blue-300 mt-2">Queue: ${current.queue.toLocaleString('tr-TR')}</div>
                </div>
            `;
            document.getElementById('currentStatus').innerHTML = html;
        }

        // Tarihçe tablosunu doldur
        function renderHistoryTable(data) {
            let html = '';

            for (let i = 0; i < data.length; i++) {
                const current = data[i];
                const previous = data[i + 1];

                let increase = 0;
                let increaseText = '-';
                let increaseClass = 'text-gray-400';

                if (previous) {
                    increase = current.completed - previous.completed;
                    if (increase > 0) {
                        increaseText = `+${increase.toLocaleString('tr-TR')}`;
                        increaseClass = 'text-green-400 font-semibold';
                    } else if (increase < 0) {
                        increaseText = increase.toLocaleString('tr-TR');
                        increaseClass = 'text-red-400';
                    } else {
                        increaseText = '0';
                    }
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

                const rowClass = i === 0 ? 'bg-blue-900/20' : 'hover:bg-gray-750';

                html += `
                    <tr class="${rowClass}" id="row-${i}">
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
                        <td class="px-4 py-4 text-center text-sm text-yellow-400">
                            ${speedText}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <button onclick="deleteRow(${i})" class="text-red-400 hover:text-red-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;
            }

            document.getElementById('historyTable').innerHTML = html;
        }

        // Satır silme fonksiyonu
        function deleteRow(index) {
            if (confirm('Bu kaydı silmek istediğinden emin misin?')) {
                // Veriyi array'den sil
                data.splice(index, 1);

                // Tabloyu yeniden render et
                renderHistoryTable(data);
                renderCurrentStatus(data[0]);
                document.getElementById('recordCount').textContent = data.length;

                // Not: Sadece frontend'de silindi, sayfa yenilenince geri gelecek
                // Kalıcı silme için backend'e istek atılmalı
            }
        }
    </script>
</body>
</html>
