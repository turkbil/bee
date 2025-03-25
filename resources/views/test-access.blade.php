<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Erişim Testi') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Kullanıcı Bilgileri -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Kullanıcı Bilgileri</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>ID:</strong> {{ $userData['id'] }}</p>
                            <p><strong>Ad:</strong> {{ $userData['name'] }}</p>
                            <p><strong>E-posta:</strong> {{ $userData['email'] }}</p>
                            <p><strong>Aktif mi:</strong> {{ $userData['is_active'] ? 'Evet' : 'Hayır' }}</p>
                        </div>
                        <div>
                            <p><strong>Roller:</strong> {{ implode(', ', $userData['roles']) }}</p>
                            <p><strong>Root mu:</strong> {{ $userData['is_root'] ? 'Evet' : 'Hayır' }}</p>
                            <p><strong>Admin mi:</strong> {{ $userData['is_admin'] ? 'Evet' : 'Hayır' }}</p>
                            <p><strong>Editör mü:</strong> {{ $userData['is_editor'] ? 'Evet' : 'Hayır' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tenant Bilgileri -->
            @if($tenantData)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Tenant Bilgileri</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>ID:</strong> {{ $tenantData['id'] }}</p>
                            <p><strong>Ad:</strong> {{ $tenantData['name'] }}</p>
                        </div>
                        <div>
                            <p><strong>Domain:</strong> {{ $tenantData['domain'] }}</p>
                            <p><strong>Aktif mi:</strong> {{ $tenantData['is_active'] ? 'Evet' : 'Hayır' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Erişilebilir Modüller -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Erişilebilir Modüller</h3>
                    <div class="grid grid-cols-4 gap-4">
                        @foreach($accessibleModules as $module)
                        <div class="bg-green-100 p-2 rounded">{{ $module }}</div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tüm Modüller ve İzinler -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Modül İzinleri</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modül</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Görünen Ad</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Görüntüleme</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oluşturma</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Güncelleme</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Silme</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bağlantı</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($moduleData as $module)
                                <tr>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $module['id'] }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $module['name'] }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $module['display_name'] }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $module['type'] }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $module['permissions']['view'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $module['permissions']['view'] ? 'Evet' : 'Hayır' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-200">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $module['permissions']['create'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $module['permissions']['create'] ? 'Evet' : 'Hayır' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-200">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $module['permissions']['update'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $module['permissions']['update'] ? 'Evet' : 'Hayır' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-200">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $module['permissions']['delete'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $module['permissions']['delete'] ? 'Evet' : 'Hayır' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-200">
                                        @if($module['permissions']['view'])
                                        <a href="{{ $module['routes']['index'] }}" class="text-blue-600 hover:text-blue-900">Görüntüle</a>
                                        @else
                                        <span class="text-gray-400">Erişim Yok</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>