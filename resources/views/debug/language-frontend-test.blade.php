<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frontend Language Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @livewireStyles
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">🔍 Frontend Language Switcher Debug</h1>
        
        <!-- Mevcut Durum -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">📊 Mevcut Durum</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <strong>Session Site Language:</strong> 
                    <span class="text-blue-600">{{ session('site_language', 'YOK') }}</span>
                </div>
                <div>
                    <strong>Session Locale:</strong> 
                    <span class="text-green-600">{{ session('locale', 'YOK') }}</span>
                </div>
                <div>
                    <strong>App Locale:</strong> 
                    <span class="text-purple-600">{{ app()->getLocale() }}</span>
                </div>
                <div>
                    <strong>Current Time:</strong> 
                    <span class="text-gray-600">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
        </div>

        <!-- Language Switcher Test -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">🔄 Language Switcher (Frontend Style)</h2>
            <div class="border-2 border-dashed border-blue-300 p-4 rounded">
                @livewire('languagemanagement::language-switcher', ['style' => 'buttons', 'showText' => false, 'showFlags' => true])
            </div>
        </div>

        <!-- Manual Test Buttons -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">⚡ Manuel Test Butonları</h2>
            <div class="flex gap-4 mb-4">
                <button onclick="switchLanguageManual('tr')" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    🇹🇷 Türkçe
                </button>
                <button onclick="switchLanguageManual('en')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    🇺🇸 English
                </button>
                <button onclick="switchLanguageManual('ar')" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    🇸🇦 العربية
                </button>
            </div>
            <div id="manual-result" class="text-sm bg-gray-100 p-3 rounded"></div>
        </div>

        <!-- Session Check -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">📋 Session Durumu</h2>
            <button onclick="checkSession()" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 mb-4">
                Session Kontrol Et
            </button>
            <div id="session-result" class="text-sm bg-gray-100 p-3 rounded"></div>
        </div>

        <!-- Livewire Component Data -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">🧩 Livewire Component Data</h2>
            <button onclick="checkLivewireData()" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 mb-4">
                Component Data Kontrol Et
            </button>
            <div id="livewire-result" class="text-sm bg-gray-100 p-3 rounded"></div>
        </div>

        <!-- Log Viewer -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">📄 Son Log Girişleri</h2>
            <button onclick="getLogs()" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 mb-4">
                Son 20 Log Al
            </button>
            <div class="flex gap-2 mb-2">
                <button onclick="copyToClipboard('log-result')" class="bg-gray-500 text-white px-3 py-1 rounded text-sm">
                    📋 Logları Kopyala
                </button>
                <span class="text-sm text-gray-600">← Bu logları Claude'a at</span>
            </div>
            <div id="log-result" class="text-sm bg-gray-100 p-3 rounded border-2 max-h-96 overflow-y-auto"></div>
        </div>

        <!-- Debug Copy Area -->
        <div class="bg-yellow-50 border-2 border-yellow-300 p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4 text-yellow-800">🎯 CLAUDE İÇİN KOPYA ALANI</h2>
            <p class="text-yellow-700 mb-4">Aşağıdaki butona tıkla, tüm debug verilerini tek seferde kopyala ve Claude'a yapıştır:</p>
            <button onclick="copyAllDebugData()" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 text-lg font-bold">
                📋 TÜM DEBUG VERİLERİNİ KOPYALA
            </button>
            <div id="copy-status" class="mt-2 text-sm"></div>
            <textarea id="debug-data-area" class="w-full h-32 mt-4 p-3 border rounded bg-white" readonly placeholder="Debug verileri burada görünecek..."></textarea>
        </div>

        <!-- Test Links -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">🔗 Test Linkleri</h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('debug.language.frontend') }}" class="bg-blue-500 text-white px-4 py-2 rounded text-center hover:bg-blue-600">
                    Frontend Test
                </a>
                <a href="{{ route('debug.language.admin') }}" class="bg-red-500 text-white px-4 py-2 rounded text-center hover:bg-red-600">
                    Admin Test
                </a>
                <a href="{{ route('debug.language.session') }}" target="_blank" class="bg-green-500 text-white px-4 py-2 rounded text-center hover:bg-green-600">
                    Session JSON
                </a>
                <a href="{{ route('debug.language.livewire') }}" target="_blank" class="bg-purple-500 text-white px-4 py-2 rounded text-center hover:bg-purple-600">
                    Livewire JSON
                </a>
            </div>
        </div>
    </div>

    @livewireScripts

    <script>
        // Manuel dil değiştirme
        async function switchLanguageManual(code) {
            try {
                const response = await fetch(`/debug-lang/switch-test/${code}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                document.getElementById('manual-result').innerHTML = 
                    `<strong>Sonuç:</strong> ${JSON.stringify(data, null, 2)}`;
                
                // Sayfayı yenile
                setTimeout(() => window.location.reload(), 1000);
            } catch (error) {
                document.getElementById('manual-result').innerHTML = 
                    `<strong>Hata:</strong> ${error.message}`;
            }
        }

        // Session kontrol
        async function checkSession() {
            try {
                const response = await fetch('/debug-lang/session-check');
                const data = await response.json();
                document.getElementById('session-result').innerHTML = 
                    `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            } catch (error) {
                document.getElementById('session-result').innerHTML = 
                    `<strong>Hata:</strong> ${error.message}`;
            }
        }

        // Livewire component data kontrol
        async function checkLivewireData() {
            try {
                const response = await fetch('/debug-lang/livewire-data');
                const data = await response.json();
                document.getElementById('livewire-result').innerHTML = 
                    `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            } catch (error) {
                document.getElementById('livewire-result').innerHTML = 
                    `<strong>Hata:</strong> ${error.message}`;
            }
        }

        // Livewire events dinle
        document.addEventListener('livewire:init', () => {
            console.log('🔥 Livewire initialized');
            
            // Dil değişikliği eventini dinle
            Livewire.on('languageChanged', (data) => {
                console.log('🌍 Language changed event:', data);
                alert(`Dil değiştirildi: ${JSON.stringify(data)}`);
            });
        });

        // Log viewer 
        async function getLogs() {
            try {
                const response = await fetch('/debug-lang/get-logs');
                const data = await response.json();
                document.getElementById('log-result').innerHTML = 
                    `<pre>${data.logs.join('\n')}</pre>`;
            } catch (error) {
                document.getElementById('log-result').innerHTML = 
                    `<strong>Hata:</strong> ${error.message}`;
            }
        }

        // Clipboard copy function
        async function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent || element.value;
            
            try {
                await navigator.clipboard.writeText(text);
                showCopyStatus('✅ Kopyalandı!', 'text-green-600');
            } catch (err) {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showCopyStatus('✅ Kopyalandı!', 'text-green-600');
            }
        }

        // Tüm debug verilerini toplama
        async function copyAllDebugData() {
            try {
                // Session data al
                const sessionResponse = await fetch('/debug-lang/session-check');
                const sessionData = await sessionResponse.json();
                
                // Livewire data al
                const livewireResponse = await fetch('/debug-lang/livewire-data');
                const livewireData = await livewireResponse.json();
                
                // Logs al
                const logsResponse = await fetch('/debug-lang/get-logs');
                const logsData = await logsResponse.json();
                
                // Tüm veriyi birleştir
                const allData = {
                    timestamp: new Date().toISOString(),
                    current_url: window.location.href,
                    session_data: sessionData,
                    livewire_data: livewireData,
                    recent_logs: logsData.logs,
                    browser_info: {
                        userAgent: navigator.userAgent,
                        language: navigator.language,
                        cookieEnabled: navigator.cookieEnabled
                    }
                };
                
                const debugText = `=== LANGUAGE SWITCHER DEBUG DATA ===
Timestamp: ${allData.timestamp}
URL: ${allData.current_url}

=== SESSION DATA ===
${JSON.stringify(allData.session_data, null, 2)}

=== LIVEWIRE DATA ===
${JSON.stringify(allData.livewire_data, null, 2)}

=== RECENT LOGS ===
${allData.recent_logs.join('\n')}

=== BROWSER INFO ===
${JSON.stringify(allData.browser_info, null, 2)}
`;
                
                // Textarea'ya yazdır
                document.getElementById('debug-data-area').value = debugText;
                
                // Clipboard'a kopyala
                await navigator.clipboard.writeText(debugText);
                showCopyStatus('✅ Tüm debug verileri kopyalandı! Claude\'a yapıştırabilirsin.', 'text-green-600');
                
            } catch (error) {
                showCopyStatus('❌ Hata: ' + error.message, 'text-red-600');
            }
        }

        function showCopyStatus(message, className) {
            const statusDiv = document.getElementById('copy-status');
            statusDiv.textContent = message;
            statusDiv.className = `mt-2 text-sm ${className}`;
            setTimeout(() => {
                statusDiv.textContent = '';
                statusDiv.className = 'mt-2 text-sm';
            }, 3000);
        }

        // Sayfa yüklendiğinde debug bilgilerini göster
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 Page loaded, checking session...');
            checkSession();
            checkLivewireData();
            getLogs();
        });
    </script>
</body>
</html>