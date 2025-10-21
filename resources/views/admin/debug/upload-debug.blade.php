<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload Debug Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #1a1a2e;
            color: #eee;
            font-family: 'Courier New', monospace;
        }
        .test-card {
            background: #16213e;
            border: 1px solid #0f3460;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .test-card:hover {
            border-color: #00adb5;
        }
        .test-card .card-header {
            color: #fff;
            font-weight: bold;
        }
        .test-card .card-body {
            color: #eee;
        }
        .test-card .text-muted {
            color: #999 !important;
        }
        .test-card label {
            color: #fff;
        }
        .log-container {
            background: #0f0f0f;
            border: 1px solid #333;
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            font-size: 12px;
            border-radius: 8px;
        }
        .log-entry {
            margin-bottom: 10px;
            padding: 8px;
            border-left: 3px solid #00adb5;
            background: #1a1a1a;
            color: #fff;
        }
        .log-entry.error {
            border-left-color: #e94560;
            background: #2a1a1a;
            color: #fff;
        }
        .log-entry.success {
            border-left-color: #2ecc71;
            background: #1a2a1a;
            color: #fff;
        }
        .event-name {
            color: #00d4ff;
            font-weight: bold;
        }
        .timestamp {
            color: #aaa;
            font-size: 10px;
        }
        .log-entry pre {
            color: #ccc;
            background: #0a0a0a;
            padding: 5px;
            border-radius: 3px;
        }
        .btn-test {
            background: #00adb5;
            border: none;
            color: white;
        }
        .btn-test:hover {
            background: #00919a;
            color: white;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 11px;
            margin-left: 10px;
        }
        .status-idle {
            background: #555;
        }
        .status-uploading {
            background: #f39c12;
        }
        .status-success {
            background: #2ecc71;
        }
        .status-error {
            background: #e94560;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border: 2px solid #00adb5;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="text-center mb-3">🔬 Upload Debug Laboratory</h1>
                <p class="text-center text-muted">Multi-Method File Upload Testing with Real-Time Logging</p>
                <div class="text-center">
                    <button class="btn btn-sm btn-outline-warning" onclick="refreshLogs()">🔄 Logları Yenile</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="clearLogs()">🗑️ Logları Temizle</button>
                    <button class="btn btn-sm btn-outline-info" onclick="testStorage()">💾 Storage Testi</button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Test Methods -->
            <div class="col-lg-6">
                <!-- Method 1: Standard FormData -->
                <div class="card test-card">
                    <div class="card-header">
                        <strong>Method 1: Standard FormData Upload</strong>
                        <span id="status-formdata" class="status-badge status-idle">Idle</span>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Normal fetch() + FormData kullanarak POST request</p>
                        <input type="file" id="file-formdata" class="form-control form-control-sm mb-2" accept="image/*">
                        <div id="preview-formdata"></div>
                        <button class="btn btn-test btn-sm w-100" onclick="uploadFormData()">📤 FormData Upload</button>
                        <div id="result-formdata" class="mt-2"></div>
                    </div>
                </div>

                <!-- Method 2: Base64 -->
                <div class="card test-card">
                    <div class="card-header">
                        <strong>Method 2: Base64 Upload</strong>
                        <span id="status-base64" class="status-badge status-idle">Idle</span>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">FileReader ile Base64'e çevir, JSON olarak gönder</p>
                        <input type="file" id="file-base64" class="form-control form-control-sm mb-2" accept="image/*">
                        <div id="preview-base64"></div>
                        <button class="btn btn-test btn-sm w-100" onclick="uploadBase64()">📤 Base64 Upload</button>
                        <div id="result-base64" class="mt-2"></div>
                    </div>
                </div>

                <!-- Method 3: Chunked Upload -->
                <div class="card test-card">
                    <div class="card-header">
                        <strong>Method 3: Chunked Upload</strong>
                        <span id="status-chunked" class="status-badge status-idle">Idle</span>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Büyük dosyaları chunk'lara bölerek gönder (1MB chunks)</p>
                        <input type="file" id="file-chunked" class="form-control form-control-sm mb-2" accept="image/*">
                        <div id="preview-chunked"></div>
                        <div id="progress-chunked" class="progress mb-2" style="display: none;">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <button class="btn btn-test btn-sm w-100" onclick="uploadChunked()">📤 Chunked Upload</button>
                        <div id="result-chunked" class="mt-2"></div>
                    </div>
                </div>

                <!-- Method 4: XMLHttpRequest -->
                <div class="card test-card">
                    <div class="card-header">
                        <strong>Method 4: XMLHttpRequest Upload</strong>
                        <span id="status-xhr" class="status-badge status-idle">Idle</span>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Eski usul XMLHttpRequest ile progress tracking</p>
                        <input type="file" id="file-xhr" class="form-control form-control-sm mb-2" accept="image/*">
                        <div id="preview-xhr"></div>
                        <div id="progress-xhr" class="progress mb-2" style="display: none;">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <button class="btn btn-test btn-sm w-100" onclick="uploadXHR()">📤 XHR Upload</button>
                        <div id="result-xhr" class="mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Live Logs -->
            <div class="col-lg-6">
                <div class="card test-card" style="position: sticky; top: 20px;">
                    <div class="card-header">
                        <strong>🔴 Live Debug Logs</strong>
                        <span class="float-end small text-muted">Auto-refresh: 2s</span>
                    </div>
                    <div class="card-body p-0">
                        <div id="logs" class="log-container">
                            <div class="text-center text-muted py-5">Waiting for logs...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        // Auto-refresh logs
        setInterval(refreshLogs, 2000);

        // File preview handlers
        ['formdata', 'base64', 'chunked', 'xhr'].forEach(method => {
            document.getElementById(`file-${method}`).addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById(`preview-${method}`).innerHTML =
                            `<img src="${e.target.result}" class="preview-image">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        // Method 1: FormData Upload
        async function uploadFormData() {
            const fileInput = document.getElementById('file-formdata');
            const file = fileInput.files[0];

            if (!file) {
                alert('Lütfen bir dosya seçin');
                return;
            }

            setStatus('formdata', 'uploading');
            logClient('FORMDATA_START', `FormData upload başlıyor: ${file.name}`);

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', CSRF_TOKEN);

            try {
                logClient('FORMDATA_REQUEST', 'Fetch request gönderiliyor...', {
                    url: '/admin/debug/upload/formdata',
                    fileSize: file.size,
                    fileType: file.type
                });

                const response = await fetch('/admin/debug/upload/formdata', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });

                logClient('FORMDATA_RESPONSE', `Response alındı: ${response.status}`, {
                    status: response.status,
                    statusText: response.statusText,
                    headers: Object.fromEntries(response.headers.entries())
                });

                const result = await response.json();

                if (result.success) {
                    setStatus('formdata', 'success');
                    showResult('formdata', 'success', result.message, result);
                    logClient('FORMDATA_SUCCESS', 'Upload başarılı!', result);
                } else {
                    setStatus('formdata', 'error');
                    showResult('formdata', 'error', result.message);
                    logClient('FORMDATA_ERROR', 'Upload başarısız', result);
                }

            } catch (error) {
                setStatus('formdata', 'error');
                showResult('formdata', 'error', error.message);
                logClient('FORMDATA_EXCEPTION', 'JavaScript exception', {
                    error: error.message,
                    stack: error.stack
                });
            }
        }

        // Method 2: Base64 Upload
        async function uploadBase64() {
            const fileInput = document.getElementById('file-base64');
            const file = fileInput.files[0];

            if (!file) {
                alert('Lütfen bir dosya seçin');
                return;
            }

            setStatus('base64', 'uploading');
            logClient('BASE64_START', `Base64 upload başlıyor: ${file.name}`);

            try {
                logClient('BASE64_READ', 'FileReader ile dosya okunuyor...');

                const reader = new FileReader();

                reader.onload = async function(e) {
                    const base64Data = e.target.result;

                    logClient('BASE64_READ_SUCCESS', 'Dosya Base64\'e çevrildi', {
                        base64Length: base64Data.length
                    });

                    logClient('BASE64_REQUEST', 'JSON request gönderiliyor...');

                    const response = await fetch('/admin/debug/upload/base64', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({
                            file_data: base64Data,
                            file_name: file.name
                        })
                    });

                    logClient('BASE64_RESPONSE', `Response alındı: ${response.status}`);

                    const result = await response.json();

                    if (result.success) {
                        setStatus('base64', 'success');
                        showResult('base64', 'success', result.message, result);
                        logClient('BASE64_SUCCESS', 'Upload başarılı!', result);
                    } else {
                        setStatus('base64', 'error');
                        showResult('base64', 'error', result.message);
                        logClient('BASE64_ERROR', 'Upload başarısız', result);
                    }
                };

                reader.onerror = function(error) {
                    setStatus('base64', 'error');
                    logClient('BASE64_READ_ERROR', 'FileReader hatası', {error});
                };

                reader.readAsDataURL(file);

            } catch (error) {
                setStatus('base64', 'error');
                showResult('base64', 'error', error.message);
                logClient('BASE64_EXCEPTION', 'JavaScript exception', {
                    error: error.message,
                    stack: error.stack
                });
            }
        }

        // Method 3: Chunked Upload
        async function uploadChunked() {
            const fileInput = document.getElementById('file-chunked');
            const file = fileInput.files[0];

            if (!file) {
                alert('Lütfen bir dosya seçin');
                return;
            }

            setStatus('chunked', 'uploading');
            logClient('CHUNKED_START', `Chunked upload başlıyor: ${file.name}`);

            const CHUNK_SIZE = 1024 * 1024; // 1MB
            const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
            const fileId = 'file_' + Date.now();

            logClient('CHUNKED_INFO', 'Chunk bilgileri', {
                totalSize: file.size,
                chunkSize: CHUNK_SIZE,
                totalChunks: totalChunks,
                fileId: fileId
            });

            const progressBar = document.querySelector('#progress-chunked .progress-bar');
            document.getElementById('progress-chunked').style.display = 'block';

            try {
                for (let i = 0; i < totalChunks; i++) {
                    const start = i * CHUNK_SIZE;
                    const end = Math.min(start + CHUNK_SIZE, file.size);
                    const chunk = file.slice(start, end);

                    logClient('CHUNKED_UPLOAD', `Chunk ${i + 1}/${totalChunks} gönderiliyor...`, {
                        chunkIndex: i,
                        chunkSize: chunk.size
                    });

                    const formData = new FormData();
                    formData.append('chunk', chunk);
                    formData.append('chunk_index', i);
                    formData.append('total_chunks', totalChunks);
                    formData.append('file_id', fileId);
                    formData.append('file_name', file.name);
                    formData.append('_token', CSRF_TOKEN);

                    const response = await fetch('/admin/debug/upload/chunked', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        }
                    });

                    const result = await response.json();

                    const progress = ((i + 1) / totalChunks) * 100;
                    progressBar.style.width = progress + '%';
                    progressBar.textContent = Math.round(progress) + '%';

                    if (!result.success) {
                        throw new Error(result.message);
                    }

                    logClient('CHUNKED_CHUNK_SUCCESS', `Chunk ${i + 1} başarılı`);
                }

                setStatus('chunked', 'success');
                showResult('chunked', 'success', 'Chunked upload tamamlandı!');
                logClient('CHUNKED_COMPLETE', 'Tüm chunk\'lar yüklendi!');

            } catch (error) {
                setStatus('chunked', 'error');
                showResult('chunked', 'error', error.message);
                logClient('CHUNKED_EXCEPTION', 'Chunked upload hatası', {
                    error: error.message
                });
            } finally {
                setTimeout(() => {
                    document.getElementById('progress-chunked').style.display = 'none';
                    progressBar.style.width = '0%';
                }, 2000);
            }
        }

        // Method 4: XMLHttpRequest
        function uploadXHR() {
            const fileInput = document.getElementById('file-xhr');
            const file = fileInput.files[0];

            if (!file) {
                alert('Lütfen bir dosya seçin');
                return;
            }

            setStatus('xhr', 'uploading');
            logClient('XHR_START', `XHR upload başlıyor: ${file.name}`);

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', CSRF_TOKEN);

            const xhr = new XMLHttpRequest();
            const progressBar = document.querySelector('#progress-xhr .progress-bar');
            document.getElementById('progress-xhr').style.display = 'block';

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                    progressBar.textContent = Math.round(percentComplete) + '%';

                    logClient('XHR_PROGRESS', `Upload progress: ${Math.round(percentComplete)}%`, {
                        loaded: e.loaded,
                        total: e.total
                    });
                }
            });

            xhr.addEventListener('load', function() {
                logClient('XHR_RESPONSE', `XHR response alındı: ${xhr.status}`);

                try {
                    const result = JSON.parse(xhr.responseText);

                    if (result.success) {
                        setStatus('xhr', 'success');
                        showResult('xhr', 'success', result.message, result);
                        logClient('XHR_SUCCESS', 'XHR upload başarılı!', result);
                    } else {
                        setStatus('xhr', 'error');
                        showResult('xhr', 'error', result.message);
                        logClient('XHR_ERROR', 'XHR upload başarısız', result);
                    }
                } catch (e) {
                    setStatus('xhr', 'error');
                    showResult('xhr', 'error', 'Invalid JSON response');
                    logClient('XHR_PARSE_ERROR', 'JSON parse hatası', {error: e.message});
                }

                setTimeout(() => {
                    document.getElementById('progress-xhr').style.display = 'none';
                    progressBar.style.width = '0%';
                }, 2000);
            });

            xhr.addEventListener('error', function() {
                setStatus('xhr', 'error');
                showResult('xhr', 'error', 'Network error');
                logClient('XHR_NETWORK_ERROR', 'XHR network hatası');

                setTimeout(() => {
                    document.getElementById('progress-xhr').style.display = 'none';
                    progressBar.style.width = '0%';
                }, 2000);
            });

            logClient('XHR_SEND', 'XHR request gönderiliyor...');

            xhr.open('POST', '/admin/debug/upload/formdata', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', CSRF_TOKEN);
            xhr.send(formData);
        }

        // Storage test
        async function testStorage() {
            logClient('STORAGE_TEST_START', 'Storage testi başlıyor...');

            try {
                const response = await fetch('/admin/debug/upload/test-storage', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('✅ Storage Testi Başarılı!\n\n' + JSON.stringify(result, null, 2));
                    logClient('STORAGE_TEST_SUCCESS', 'Storage erişimi çalışıyor', result);
                } else {
                    alert('❌ Storage Testi Başarısız!\n\n' + result.message);
                    logClient('STORAGE_TEST_ERROR', 'Storage testi başarısız', result);
                }
            } catch (error) {
                alert('❌ Storage Test Hatası!\n\n' + error.message);
                logClient('STORAGE_TEST_EXCEPTION', 'Storage test exception', {error: error.message});
            }
        }

        // Get logs from server
        async function refreshLogs() {
            try {
                const response = await fetch('/admin/debug/upload/logs');
                const data = await response.json();

                if (data.logs && data.logs.length > 0) {
                    const logsHtml = data.logs.map(logLine => {
                        try {
                            const log = JSON.parse(logLine);
                            const isError = log.event.includes('ERROR') || log.event.includes('EXCEPTION');
                            const isSuccess = log.event.includes('SUCCESS') || log.event.includes('COMPLETE');
                            const className = isError ? 'error' : (isSuccess ? 'success' : '');

                            return `
                                <div class="log-entry ${className}">
                                    <div class="timestamp">${log.timestamp} | ${log.tenant}</div>
                                    <div class="event-name">${log.event}</div>
                                    <div>${log.message}</div>
                                    ${Object.keys(log.context || {}).length > 0 ?
                                        `<pre class="small mt-1 mb-0">${JSON.stringify(log.context, null, 2)}</pre>`
                                        : ''}
                                </div>
                            `;
                        } catch (e) {
                            return `<div class="log-entry">${logLine}</div>`;
                        }
                    }).join('');

                    document.getElementById('logs').innerHTML = logsHtml;
                    document.getElementById('logs').scrollTop = document.getElementById('logs').scrollHeight;
                }
            } catch (error) {
                console.error('Log refresh error:', error);
            }
        }

        // Clear logs
        async function clearLogs() {
            if (!confirm('Tüm logları temizlemek istediğinize emin misiniz?')) {
                return;
            }

            try {
                await fetch('/admin/debug/upload/clear-logs', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });

                document.getElementById('logs').innerHTML =
                    '<div class="text-center text-muted py-5">Loglar temizlendi. Yeni upload yapmayı deneyin...</div>';
            } catch (error) {
                console.error('Clear logs error:', error);
            }
        }

        // Client-side log - hem console'a hem server'a gönder
        function logClient(event, message, context = {}) {
            console.log(`[${event}]`, message, context);

            // Server'a da gönder (async, hata olsa bile devam et)
            fetch('/admin/debug/upload/log-client', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    event: event,
                    message: message,
                    context: context
                })
            }).catch(err => {
                console.error('Server log gönderimi başarısız:', err);
            });
        }

        // Set status badge
        function setStatus(method, status) {
            const badge = document.getElementById(`status-${method}`);
            badge.className = `status-badge status-${status}`;
            badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        }

        // Show result
        function showResult(method, type, message, data = null) {
            const resultDiv = document.getElementById(`result-${method}`);
            const className = type === 'success' ? 'alert-success' : 'alert-danger';

            resultDiv.innerHTML = `
                <div class="alert ${className} small">
                    ${message}
                    ${data ? `<pre class="mt-2 mb-0">${JSON.stringify(data, null, 2)}</pre>` : ''}
                </div>
            `;

            setTimeout(() => {
                resultDiv.innerHTML = '';
            }, 5000);
        }
    </script>
</body>
</html>
