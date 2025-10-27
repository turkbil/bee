<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Debug Upload Test - Root User Unlimited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .log-container {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            max-height: 500px;
            overflow-y: auto;
        }
        .log-line { margin: 2px 0; }
        .log-info { color: #4ec9b0; }
        .log-error { color: #f48771; }
        .log-warning { color: #dcdcaa; }
        .log-success { color: #b5cea8; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">🔍 Debug Upload Test - Root User Unlimited</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Current User:</strong>
                            @auth
                                ID: {{ auth()->id() }} |
                                Email: {{ auth()->user()->email }} |
                                @if(auth()->user()->id === 1)
                                    <span class="badge bg-success">ROOT USER - UNLIMITED UPLOAD</span>
                                @else
                                    <span class="badge bg-warning">Non-Root - 20MB Limit</span>
                                @endif
                            @else
                                <span class="badge bg-danger">NOT LOGGED IN</span>
                            @endauth
                        </div>

                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="fileInput" class="form-label">Select File (Root user: Unlimited, Others: 20MB)</label>
                                <input type="file" class="form-control" id="fileInput" name="file" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <span id="uploadText">Upload File</span>
                                <span id="uploadSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                            <button type="button" class="btn btn-warning" onclick="clearLogs()">Clear Logs</button>
                            <button type="button" class="btn btn-info" onclick="refreshLogs()">Refresh Logs</button>
                        </form>

                        <div id="uploadResult" class="mt-3"></div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">📋 Live Logs</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="logsContainer" class="log-container">
                            <div class="log-line log-info">Waiting for upload...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const uploadForm = document.getElementById('uploadForm');
        const uploadBtn = document.getElementById('uploadBtn');
        const uploadText = document.getElementById('uploadText');
        const uploadSpinner = document.getElementById('uploadSpinner');
        const uploadResult = document.getElementById('uploadResult');
        const logsContainer = document.getElementById('logsContainer');

        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];

            if (!file) {
                alert('Please select a file');
                return;
            }

            // Show file info
            const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
            appendLog(`📁 Selected: ${file.name} (${fileSizeMB} MB)`, 'info');

            // Disable button
            uploadBtn.disabled = true;
            uploadText.classList.add('d-none');
            uploadSpinner.classList.remove('d-none');

            const formData = new FormData();
            formData.append('file', file);

            try {
                appendLog(`🚀 Uploading ${file.name}...`, 'info');

                const response = await fetch('/debug/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    appendLog(`✅ Upload SUCCESS!`, 'success');
                    uploadResult.innerHTML = `
                        <div class="alert alert-success">
                            <strong>✅ Upload Successful!</strong><br>
                            File: ${result.data.original_name}<br>
                            Size: ${result.data.size_mb} MB<br>
                            Temp Path: ${result.data.temp_path}<br>
                            User: ${result.data.is_root ? 'ROOT (Unlimited)' : 'Non-Root (20MB limit)'}
                        </div>
                    `;
                } else {
                    appendLog(`❌ Upload FAILED: ${result.message}`, 'error');
                    uploadResult.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>❌ Upload Failed!</strong><br>
                            ${result.message}
                        </div>
                    `;
                }

                // Refresh logs after 1 second
                setTimeout(refreshLogs, 1000);

            } catch (error) {
                appendLog(`❌ Network error: ${error.message}`, 'error');
                uploadResult.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>❌ Network Error!</strong><br>
                        ${error.message}
                    </div>
                `;
            } finally {
                uploadBtn.disabled = false;
                uploadText.classList.remove('d-none');
                uploadSpinner.classList.add('d-none');
            }
        });

        function appendLog(message, type = 'info') {
            const logLine = document.createElement('div');
            logLine.className = `log-line log-${type}`;
            logLine.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            logsContainer.appendChild(logLine);
            logsContainer.scrollTop = logsContainer.scrollHeight;
        }

        async function refreshLogs() {
            try {
                const response = await fetch('/debug/upload/logs');
                const result = await response.json();

                logsContainer.innerHTML = '<div class="log-line log-info">📋 Recent Logs:</div>';

                if (result.logs) {
                    const logLines = result.logs.split('\n').filter(line => line.trim());
                    logLines.forEach(line => {
                        const logLine = document.createElement('div');
                        logLine.className = 'log-line';

                        if (line.includes('❌') || line.includes('ERROR')) {
                            logLine.className += ' log-error';
                        } else if (line.includes('✅') || line.includes('SUCCESS')) {
                            logLine.className += ' log-success';
                        } else if (line.includes('⚠️') || line.includes('WARNING')) {
                            logLine.className += ' log-warning';
                        } else {
                            logLine.className += ' log-info';
                        }

                        logLine.textContent = line;
                        logsContainer.appendChild(logLine);
                    });
                }

                logsContainer.scrollTop = logsContainer.scrollHeight;
            } catch (error) {
                appendLog(`Failed to refresh logs: ${error.message}`, 'error');
            }
        }

        async function clearLogs() {
            if (confirm('Clear all logs?')) {
                try {
                    await fetch('/debug/upload/clear-logs', { method: 'POST' });
                    logsContainer.innerHTML = '<div class="log-line log-success">✅ Logs cleared</div>';
                } catch (error) {
                    appendLog(`Failed to clear logs: ${error.message}`, 'error');
                }
            }
        }

        // Auto-refresh logs every 5 seconds
        setInterval(refreshLogs, 5000);
    </script>
</body>
</html>
