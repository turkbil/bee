<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Module Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .copy-btn {
            transition: all 0.2s;
        }
        .copy-btn:hover {
            background-color: #3b82f6;
            color: white;
        }
        .debug-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-6 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Portfolio Module Debug</h1>
            <p class="text-gray-600">Modül slug ayarları ve veritabanı durumu detaylı analizi</p>
            <button onclick="copyAllData()" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                📋 Tüm Verileri Kopyala
            </button>
        </div>

        <!-- Tenant Bilgileri -->
        <div class="debug-section rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                🏢 Tenant Bilgileri
                <button onclick="copySection('tenant-info')" class="ml-auto copy-btn bg-gray-200 px-3 py-1 rounded text-sm">Kopyala</button>
            </h2>
            <div id="tenant-info" class="bg-white rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div><span class="font-medium">Tenant Modu:</span> 
                            <span class="px-2 py-1 rounded text-sm {{ $debugData['tenant_info']['is_tenant'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $debugData['tenant_info']['is_tenant'] ? 'TENANT' : 'CENTRAL' }}
                            </span>
                        </div>
                        <div><span class="font-medium">Tenant ID:</span> {{ $debugData['tenant_info']['tenant_id'] ?? 'YOK' }}</div>
                        <div><span class="font-medium">Domain:</span> {{ $debugData['tenant_info']['tenant_domain'] ?? 'YOK' }}</div>
                    </div>
                    <div class="space-y-2">
                        <div><span class="font-medium">Veritabanı:</span> {{ $debugData['tenant_info']['database_name'] }}</div>
                        <div><span class="font-medium">Bağlantı Tipi:</span> 
                            <span class="px-2 py-1 rounded text-sm {{ $debugData['tenant_info']['connection_type'] === 'tenant' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $debugData['tenant_info']['connection_type'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Veritabanı Durumu -->
        <div class="debug-section rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                🗄️ Veritabanı Durumu
                <button onclick="copySection('database-status')" class="ml-auto copy-btn bg-gray-200 px-3 py-1 rounded text-sm">Kopyala</button>
            </h2>
            <div id="database-status" class="bg-white rounded-lg p-4">
                @if($debugData['database_status']['connection'] === 'OK')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><span class="font-medium">Bağlantı:</span> <span class="text-green-600 font-semibold">{{ $debugData['database_status']['connection'] }}</span></div>
                        <div><span class="font-medium">Aktif DB:</span> {{ $debugData['database_status']['current_db'] }}</div>
                        <div><span class="font-medium">Tablo Var mı:</span> <span class="{{ $debugData['database_status']['tables_exist'] ? 'text-green-600' : 'text-red-600' }}">{{ $debugData['database_status']['tables_exist'] ? 'EVET' : 'HAYIR' }}</span></div>
                        <div><span class="font-medium">Toplam Ayar:</span> {{ $debugData['database_status']['total_settings'] }}</div>
                    </div>
                @else
                    <div class="text-red-600 font-semibold">
                        HATA: {{ $debugData['database_status']['error'] }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Config Dosyası -->
        <div class="debug-section rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                📁 Config Dosyası
                <button onclick="copySection('config-file')" class="ml-auto copy-btn bg-gray-200 px-3 py-1 rounded text-sm">Kopyala</button>
            </h2>
            <div id="config-file" class="bg-white rounded-lg p-4">
                <div class="mb-3"><span class="font-medium">Dosya Var mı:</span> 
                    <span class="{{ $debugData['config_file']['exists'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $debugData['config_file']['exists'] ? 'EVET' : 'HAYIR' }}
                    </span>
                </div>
                <div class="mb-3"><span class="font-medium">Yol:</span> <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $debugData['config_file']['path'] }}</code></div>
                @if($debugData['config_file']['exists'] && $debugData['config_file']['slugs'] !== 'YOK')
                    <div><span class="font-medium">Slug Ayarları:</span></div>
                    <pre class="bg-gray-100 p-3 rounded mt-2 text-sm overflow-x-auto">{{ json_encode($debugData['config_file']['slugs'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
        </div>

        <!-- Veritabanı Ayarları -->
        <div class="debug-section rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                💾 Veritabanındaki Portfolio Ayarları
                <button onclick="copySection('database-settings')" class="ml-auto copy-btn bg-gray-200 px-3 py-1 rounded text-sm">Kopyala</button>
            </h2>
            <div id="database-settings" class="bg-white rounded-lg p-4">
                @if($debugData['database_settings']['exists'])
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><span class="font-medium">ID:</span> {{ $debugData['database_settings']['id'] }}</div>
                        <div><span class="font-medium">Modül:</span> {{ $debugData['database_settings']['module_name'] }}</div>
                        <div><span class="font-medium">Oluşturulma:</span> {{ $debugData['database_settings']['created_at'] }}</div>
                        <div><span class="font-medium">Güncellenme:</span> {{ $debugData['database_settings']['updated_at'] }}</div>
                    </div>
                    <div class="mb-3"><span class="font-medium">Slug Ayarları:</span></div>
                    <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto">{{ json_encode($debugData['database_settings']['slugs'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    <div class="mt-3"><span class="font-medium">Tüm Ayarlar:</span></div>
                    <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto">{{ json_encode($debugData['database_settings']['settings'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <div class="text-red-600">
                        @if(isset($debugData['database_settings']['error']))
                            HATA: {{ $debugData['database_settings']['error'] }}
                        @else
                            {{ $debugData['database_settings']['message'] }}
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- ModuleSlugService Test -->
        <div class="debug-section rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                🔧 ModuleSlugService Test
                <button onclick="copySection('slug-service')" class="ml-auto copy-btn bg-gray-200 px-3 py-1 rounded text-sm">Kopyala</button>
            </h2>
            <div id="slug-service" class="bg-white rounded-lg p-4">
                @if(isset($debugData['slug_service']['error']))
                    <div class="text-red-600 font-semibold">HATA: {{ $debugData['slug_service']['error'] }}</div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="font-medium text-blue-800">Index Slug</div>
                            <div class="text-2xl font-bold text-blue-600 mt-2">{{ $debugData['slug_service']['index_slug'] }}</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="font-medium text-green-800">Show Slug</div>
                            <div class="text-2xl font-bold text-green-600 mt-2">{{ $debugData['slug_service']['show_slug'] }}</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="font-medium text-purple-800">Category Slug</div>
                            <div class="text-2xl font-bold text-purple-600 mt-2">{{ $debugData['slug_service']['category_slug'] }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Cache Bilgileri -->
        <div class="debug-section rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                🗂️ Cache Durumu
                <button onclick="copySection('cache-info')" class="ml-auto copy-btn bg-gray-200 px-3 py-1 rounded text-sm">Kopyala</button>
            </h2>
            <div id="cache-info" class="bg-white rounded-lg p-4">
                <div class="space-y-2">
                    @foreach($debugData['cache_info'] as $key => $value)
                        <div class="flex justify-between items-center">
                            <span class="font-medium">{{ str_replace('_', ' ', ucfirst($key)) }}:</span>
                            <span class="px-2 py-1 rounded text-sm {{ is_bool($value) ? ($value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') : 'bg-gray-100 text-gray-800' }}">
                                {{ is_bool($value) ? ($value ? 'VAR' : 'YOK') : $value }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tüm Module Settings -->
        <div class="debug-section rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                📊 Tüm Modül Ayarları
                <button onclick="copySection('all-settings')" class="ml-auto copy-btn bg-gray-200 px-3 py-1 rounded text-sm">Kopyala</button>
            </h2>
            <div id="all-settings" class="bg-white rounded-lg p-4">
                @if(isset($debugData['all_module_settings']['error']))
                    <div class="text-red-600">HATA: {{ $debugData['all_module_settings']['error'] }}</div>
                @else
                    @if(count($debugData['all_module_settings']) > 0)
                        @foreach($debugData['all_module_settings'] as $setting)
                            <div class="border-b pb-4 mb-4 last:border-b-0">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="font-semibold text-lg">{{ $setting['module_name'] }}</h3>
                                    <span class="text-sm text-gray-500">ID: {{ $setting['id'] }}</span>
                                </div>
                                <div class="text-sm text-gray-600 mb-2">Güncelleme: {{ $setting['updated_at'] }}</div>
                                <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto">{{ json_encode($setting['settings'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endforeach
                    @else
                        <div class="text-gray-500">Hiç modül ayarı bulunamadı.</div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Route Bilgileri -->
        <div class="debug-section rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                🛣️ Route Bilgileri
                <button onclick="copySection('route-info')" class="ml-auto copy-btn bg-gray-200 px-3 py-1 rounded text-sm">Kopyala</button>
            </h2>
            <div id="route-info" class="bg-white rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($debugData['route_info'] as $key => $value)
                        <div><span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> {{ $value }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function copySection(sectionId) {
            const element = document.getElementById(sectionId);
            const text = element.innerText;
            navigator.clipboard.writeText(text).then(() => {
                showToast('Bölüm kopyalandı!');
            });
        }

        function copyAllData() {
            const debugData = @json($debugData);
            const formattedData = JSON.stringify(debugData, null, 2);
            navigator.clipboard.writeText(formattedData).then(() => {
                showToast('Tüm veriler kopyalandı!');
            });
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 2000);
        }
    </script>
</body>
</html>