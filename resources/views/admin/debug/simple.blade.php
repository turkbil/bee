<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Debug Console</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/admin-assets/libs/fontawesome-pro@7.1.0/css/all.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Courier New', monospace; background: #1a1a1a; color: #00ff00; }
        .console { background: #000; border: 2px solid #333; border-radius: 8px; padding: 20px; margin: 15px; overflow: auto; }
        .log-entry { margin: 2px 0; font-size: 13px; line-height: 1.4; }
        .log-info { color: #00ff00; }
        .log-error { color: #ff0066; }
        .log-warning { color: #ffaa00; }
        .log-debug { color: #00aaff; }
        .timestamp { color: #888; font-size: 11px; }
        .header { background: linear-gradient(45deg, #2a2a2a, #404040); padding: 20px; margin: 15px; border-radius: 8px; border: 1px solid #555; }
        .status-good { color: #00ff00; }
        .status-bad { color: #ff0066; }
        .btn-console { background: #333; border: 1px solid #555; color: #00ff00; margin: 5px; }
        .btn-console:hover { background: #444; color: #00ff00; }
        .test-area { background: #111; border: 1px solid #333; padding: 15px; margin: 10px 0; border-radius: 5px; }
        #logOutput { height: 500px; overflow-y: scroll; font-size: 12px; }
        .input-console { background: #111; border: 1px solid #333; color: #00ff00; }
        .input-console:focus { background: #222; border-color: #00ff00; color: #00ff00; box-shadow: 0 0 5px #00ff00; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="header text-center">
            <h1><i class="fas fa-terminal"></i> AI TRANSLATION DEBUG CONSOLE</h1>
            <p class="mb-0">Gerçek zamanlı log takibi ve çeviri sistemi analizi</p>
        </div>

        <!-- Sistem Durumu -->
        <div class="row">
            <div class="col-md-6">
                <div class="console">
                    <h5><i class="fas fa-info-circle"></i> SİSTEM DURUMU</h5>
                    <div id="systemStatus">Yükleniyor...</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="console">
                    <h5><i class="fas fa-vial"></i> HIZLI ÇEVİRİ TEST</h5>
                    <div class="test-area">
                        <div class="row">
                            <div class="col-4">
                                <input type="text" id="testText" class="form-control input-console" placeholder="Test metni" value="Merhaba dünya">
                            </div>
                            <div class="col-3">
                                <select id="fromLang" class="form-control input-console">
                                    <option value="tr">Türkçe</option>
                                    <option value="en">English</option>
                                    <option value="ar">العربية</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <select id="toLang" class="form-control input-console">
                                    <option value="en">English</option>
                                    <option value="ar">العربية</option>
                                    <option value="tr">Türkçe</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <button onclick="testTranslation()" class="btn btn-console">
                                    <i class="fas fa-play"></i> TEST
                                </button>
                            </div>
                        </div>
                        <div id="testResult" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Kontrolleri -->
        <div class="console">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="fas fa-scroll"></i> CANLI LOG TAKİBİ</h5>
                <div>
                    <button onclick="clearLogs()" class="btn btn-console">
                        <i class="fas fa-trash"></i> LOG TEMİZLE
                    </button>
                    <button onclick="toggleAutoScroll()" class="btn btn-console" id="scrollBtn">
                        <i class="fas fa-arrow-down"></i> AUTO SCROLL: ON
                    </button>
                    <button onclick="copyLogs()" class="btn btn-console">
                        <i class="fas fa-copy"></i> KOPYALA
                    </button>
                </div>
            </div>
            
            <!-- Log Output -->
            <div id="logOutput" class="console" style="height: 500px; border: 1px solid #333;">
                <div class="log-entry log-info">🚀 Debug konsolu başlatıldı - Canlı log takibi aktif...</div>
            </div>
        </div>

        <!-- Talimatlar -->
        <div class="console">
            <h5><i class="fas fa-question-circle"></i> NASIL KULLANILIR</h5>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="status-good">🎯 ÇEVIRIDE SORUN YASARKEN:</h6>
                    <ol class="status-good">
                        <li>Bu sayfayı aç ve çalışır halde bırak</li>
                        <li>Page Management'a git ve çeviri yap</li>
                        <li>Buradaki log'lar otomatik akacak</li>
                        <li>Tüm log'ları <strong>CTRL+A</strong> ile seç</li>
                        <li><strong>CTRL+C</strong> ile kopyala ve Claude'a gönder</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h6 class="status-good">🔧 DEBUG ÖZELLİKLERİ:</h6>
                    <ul class="status-good">
                        <li><strong>Gerçek zamanlı log:</strong> Her işlem anında görünür</li>
                        <li><strong>Hızlı test:</strong> Yukarıdan test çevirisi yap</li>
                        <li><strong>Log temizleme:</strong> Temiz başlangıç için</li>
                        <li><strong>Kolay kopyalama:</strong> Tek tıkla tüm log'ları kopyala</li>
                        <li><strong>Sistem durumu:</strong> Anlık sistem kontrolü</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        let autoScroll = true;
        let eventSource = null;

        // CSRF token'ı ayarla
        window.csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Sayfa yüklendiğinde başlat
        document.addEventListener('DOMContentLoaded', function() {
            loadSystemStatus();
            startLogStream();
        });

        // Sistem durumunu yükle
        function loadSystemStatus() {
            fetch('/admin/debug/simple/status')
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    if (data.error) {
                        html = `<span class="status-bad">❌ HATA: ${data.error}</span>`;
                    } else {
                        html = `
                            <div class="status-good">✅ TENANT: ${JSON.stringify(data.tenant)}</div>
                            <div class="status-good">🌐 DİLLER: ${data.languages.length} aktif dil</div>
                            <div class="status-good">🤖 AI PROVIDER: ${data.ai_provider.name} (${data.ai_provider.model})</div>
                            <div class="timestamp">Son güncelleme: ${data.timestamp}</div>
                        `;
                    }
                    document.getElementById('systemStatus').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('systemStatus').innerHTML = `<span class="status-bad">❌ Sistem durumu alınamadı: ${error}</span>`;
                });
        }

        // Log stream başlat
        function startLogStream() {
            if (eventSource) {
                eventSource.close();
            }

            eventSource = new EventSource('/admin/debug/simple/stream-logs');
            
            eventSource.onmessage = function(event) {
                const data = JSON.parse(event.data);
                const logOutput = document.getElementById('logOutput');
                
                if (data.type === 'initial') {
                    // İlk log içeriği
                    logOutput.innerHTML = formatLogContent(data.content);
                } else if (data.type === 'new') {
                    // Yeni log girişi
                    logOutput.innerHTML += formatLogContent(data.content);
                }
                
                // Auto scroll
                if (autoScroll) {
                    logOutput.scrollTop = logOutput.scrollHeight;
                }
            };

            eventSource.onerror = function(event) {
                console.error('Log stream hatası:', event);
                addLogEntry('❌ Log stream bağlantısı kesildi. Yeniden bağlanılıyor...', 'error');
                setTimeout(startLogStream, 5000);
            };
        }

        // Log içeriğini formatla
        function formatLogContent(content) {
            return content.split('\n').map(line => {
                if (!line.trim()) return '';
                
                let className = 'log-info';
                if (line.includes('ERROR')) className = 'log-error';
                else if (line.includes('WARNING')) className = 'log-warning';
                else if (line.includes('DEBUG')) className = 'log-debug';
                
                // Timestamp ekle
                const timestamp = new Date().toLocaleTimeString();
                
                return `<div class="log-entry ${className}">[${timestamp}] ${escapeHtml(line)}</div>`;
            }).join('');
        }

        // HTML escape
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Log girişi ekle
        function addLogEntry(message, type = 'info') {
            const logOutput = document.getElementById('logOutput');
            const timestamp = new Date().toLocaleTimeString();
            const className = `log-${type}`;
            
            logOutput.innerHTML += `<div class="log-entry ${className}">[${timestamp}] ${escapeHtml(message)}</div>`;
            
            if (autoScroll) {
                logOutput.scrollTop = logOutput.scrollHeight;
            }
        }

        // Çeviri testi
        function testTranslation() {
            const text = document.getElementById('testText').value;
            const from = document.getElementById('fromLang').value;
            const to = document.getElementById('toLang').value;
            
            if (!text.trim()) {
                alert('Test metni girin!');
                return;
            }

            addLogEntry(`🧪 Manuel çeviri testi başlatılıyor: "${text}" (${from} → ${to})`, 'info');

            fetch('/admin/debug/simple/test-translation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrf_token
                },
                body: JSON.stringify({ text, from, to })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('testResult').innerHTML = `
                        <div class="status-good">✅ BAŞARILI</div>
                        <div><strong>Kaynak:</strong> ${escapeHtml(data.original)}</div>
                        <div><strong>Çeviri:</strong> ${escapeHtml(data.translated)}</div>
                        <div class="timestamp">${data.timestamp}</div>
                    `;
                    addLogEntry(`✅ Test başarılı: "${data.translated}"`, 'info');
                } else {
                    document.getElementById('testResult').innerHTML = `
                        <div class="status-bad">❌ BAŞARISIZ: ${escapeHtml(data.error)}</div>
                        <div class="timestamp">${data.timestamp}</div>
                    `;
                    addLogEntry(`❌ Test başarısız: ${data.error}`, 'error');
                }
            })
            .catch(error => {
                addLogEntry(`❌ Test isteği hatası: ${error}`, 'error');
            });
        }

        // Log'ları temizle
        function clearLogs() {
            if (!confirm('Tüm log\'lar silinecek. Emin misiniz?')) return;

            fetch('/admin/debug/simple/clear-logs', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.csrf_token
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('logOutput').innerHTML = '';
                    addLogEntry('🗑️ Log dosyası temizlendi', 'info');
                }
            })
            .catch(error => {
                addLogEntry(`❌ Log temizleme hatası: ${error}`, 'error');
            });
        }

        // Auto scroll toggle
        function toggleAutoScroll() {
            autoScroll = !autoScroll;
            const btn = document.getElementById('scrollBtn');
            btn.innerHTML = `<i class="fas fa-arrow-down"></i> AUTO SCROLL: ${autoScroll ? 'ON' : 'OFF'}`;
            btn.className = autoScroll ? 'btn btn-console' : 'btn btn-console status-bad';
        }

        // Log'ları kopyala
        function copyLogs() {
            const logOutput = document.getElementById('logOutput');
            const text = logOutput.innerText;
            
            navigator.clipboard.writeText(text).then(() => {
                addLogEntry('📋 Log\'lar panoya kopyalandı', 'info');
            }).catch(error => {
                addLogEntry(`❌ Kopyalama hatası: ${error}`, 'error');
            });
        }

        // Sayfa kapatılırken stream'i kapat
        window.addEventListener('beforeunload', function() {
            if (eventSource) {
                eventSource.close();
            }
        });
    </script>
</body>
</html>