<div>
    <!-- Mevcut Avatar -->
    <div class="mb-6">
        <h4 class="text-sm font-medium text-gray-700 mb-4">Mevcut Avatar</h4>
        <div class="flex items-center space-x-4">
            @if($user->getFirstMedia('avatar'))
                <div class="relative">
                    <img src="{{ $user->getFirstMedia('avatar')->getUrl() }}?v={{ $user->getFirstMedia('avatar')->updated_at->timestamp ?? time() }}" 
                         alt="{{ $user->name }}" 
                         class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                    
                    <form action="{{ route('avatar.remove') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                </div>
                <div class="text-sm text-gray-600">
                    <p class="font-medium">Avatar yüklendi</p>
                    <p>Değiştirmek için yeni bir fotoğraf yükleyin</p>
                </div>
            @else
                <div class="w-24 h-24 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="text-sm text-gray-600">
                    <p class="font-medium">Avatar yok</p>
                    <p>Profil fotoğrafınızı yükleyin</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Upload Form -->
    <form action="{{ route('avatar.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
            <div class="text-center">
                <label for="avatar-upload" class="cursor-pointer block">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"/>
                    </svg>
                    
                    <p class="mb-2 text-sm text-gray-600">
                        <span class="font-semibold">Tıklayın</span> veya dosya seçin
                    </p>
                    <p class="text-xs text-gray-500">PNG, JPG, WebP (Max. 2MB)</p>
                </label>
                
                <input id="avatar-upload" 
                       name="avatar"
                       type="file" 
                       class="mt-4 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                       accept="image/*"
                       onchange="this.form.submit()">
            </div>
        </div>
        
        <button type="submit" class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
            Avatar Yükle
        </button>
    </form>

    <!-- Hata mesajları -->
    @error('avatar')
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600">{{ $message }}</p>
        </div>
    @enderror

    <!-- Başarı mesajları -->
    @if (session()->has('message'))
        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-600">{{ session('message') }}</p>
        </div>
    @endif
</div>