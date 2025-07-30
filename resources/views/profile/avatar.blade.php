<x-app-layout>
    <x-slot name="header">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            Hesap Yönetimi
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
            Avatar Yönetimi
        </h1>
    </x-slot>

    <x-profile-layout-styles />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Profile Sidebar -->
        <div class="lg:col-span-1">
            @livewire('profile-sidebar')
        </div>

        <!-- Content Area -->
        <div class="lg:col-span-3">
            <!-- Avatar Management -->
            <div x-data="avatarManager()" class="profile-card bg-white dark:bg-gray-800 rounded-xl p-6">
                
                <!-- Header -->
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Avatar Yönetimi</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Profil fotoğrafınızı yükleyin veya güncelleyin</p>
                    </div>
                </div>

                <!-- Alert Messages -->
                <div x-show="message" x-transition class="mb-6 p-4 rounded-lg" :class="messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg x-show="messageType === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <svg x-show="messageType === 'error'" class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" x-text="message"></p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="message = ''" class="inline-flex text-gray-400 hover:text-gray-600">
                                <span class="sr-only">Kapat</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Current Avatar Display -->
                <div class="mb-8">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Mevcut Avatar</h4>
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <!-- Avatar Image or Placeholder -->
                            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-gray-200 dark:border-gray-600">
                                <div x-show="avatarUrl" class="w-full h-full">
                                    <img x-ref="avatarImg" 
                                         :src="avatarUrl"
                                         alt="{{ $user->name }}" 
                                         class="w-full h-full object-cover">
                                </div>
                                <div x-show="!avatarUrl" class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                            
                            <!-- Remove Button (Only show if avatar exists) -->
                            <button x-show="avatarUrl" 
                                    @click="removeAvatar()" 
                                    :disabled="isLoading"
                                    x-transition
                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 disabled:bg-red-300 text-white rounded-full w-7 h-7 flex items-center justify-center transition-colors duration-200 shadow-lg">
                                <svg x-show="!isRemoving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <svg x-show="isRemoving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="flex-1">
                            <div x-show="avatarUrl">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Avatar mevcut</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Değiştirmek için yeni bir fotoğraf yükleyin</p>
                                @if($user->getFirstMedia('avatar'))
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Son güncelleme: {{ $user->getFirstMedia('avatar')->updated_at->diffForHumans() }}</p>
                                @endif
                            </div>
                            <div x-show="!avatarUrl">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Avatar yok</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Profil fotoğrafınızı yükleyin</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Section -->
                <div class="space-y-6">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Yeni Avatar Yükle</h4>
                    
                    <!-- Drop Zone -->
                    <div @drop="handleDrop($event)" 
                         @dragover.prevent 
                         @dragenter.prevent
                         @dragleave="isDragOver = false"
                         @dragover="isDragOver = true"
                         :class="isDragOver ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
                         class="border-2 border-dashed rounded-lg p-8 text-center transition-colors duration-200">
                        
                        <input type="file" 
                               x-ref="fileInput" 
                               @change="handleFileSelect($event)"
                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                               class="hidden">
                        
                        <!-- Upload Icon -->
                        <div class="mx-auto h-16 w-16 text-gray-400 mb-4">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"/>
                            </svg>
                        </div>
                        
                        <!-- Upload Text -->
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <button @click="$refs.fileInput.click()" class="font-semibold text-blue-600 hover:text-blue-500">Dosya seçin</button>
                                veya buraya sürükleyip bırakın
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (Maksimum 2MB)</p>
                        </div>
                        
                        <!-- Preview -->
                        <div x-show="previewUrl" class="mt-4">
                            <div class="inline-block relative">
                                <img :src="previewUrl" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                                <button @click="clearPreview()" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upload Progress -->
                    <div x-show="isUploading" x-transition class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Yükleniyor...</span>
                            <span class="text-gray-600 dark:text-gray-400" x-text="uploadProgress + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                        </div>
                    </div>
                    
                    <!-- Upload Button -->
                    <button @click="uploadAvatar()" 
                            :disabled="!selectedFile || isLoading"
                            x-show="selectedFile && !isUploading"
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span>Avatar Yükle</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function avatarManager() {
            return {
                selectedFile: null,
                previewUrl: null,
                isUploading: false,
                isRemoving: false,
                isLoading: false,
                isDragOver: false,
                uploadProgress: 0,
                message: '',
                messageType: 'success',
                avatarUrl: '{{ $user->getFirstMedia('avatar') ? $user->getFirstMedia('avatar')->getUrl() . '?v=' . time() : '' }}',

                init() {
                    // Laravel session mesajlarını kontrol et
                    @if(session('message'))
                        this.showMessage('{{ session('message') }}', 'success');
                    @endif
                    @if(session('error'))
                        this.showMessage('{{ session('error') }}', 'error');
                    @endif
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.validateAndSetFile(file);
                    }
                },

                handleDrop(event) {
                    event.preventDefault();
                    this.isDragOver = false;
                    
                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        this.validateAndSetFile(files[0]);
                    }
                },

                validateAndSetFile(file) {
                    // Dosya türü kontrolü
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        this.showMessage('Sadece resim dosyaları (JPEG, PNG, WebP) yükleyebilirsiniz.', 'error');
                        return;
                    }

                    // Dosya boyutu kontrolü (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        this.showMessage('Dosya boyutu maksimum 2MB olabilir.', 'error');
                        return;
                    }

                    this.selectedFile = file;
                    this.createPreview(file);
                },

                createPreview(file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previewUrl = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                clearPreview() {
                    this.selectedFile = null;
                    this.previewUrl = null;
                    this.$refs.fileInput.value = '';
                },

                async uploadAvatar() {
                    if (!this.selectedFile) return;

                    this.isUploading = true;
                    this.isLoading = true;
                    this.uploadProgress = 0;

                    const formData = new FormData();
                    formData.append('avatar', this.selectedFile);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch('{{ route("avatar.upload") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        // Progress simulation (gerçek progress için XMLHttpRequest kullanılabilir)
                        const progressInterval = setInterval(() => {
                            if (this.uploadProgress < 90) {
                                this.uploadProgress += Math.random() * 30;
                            }
                        }, 100);

                        const data = await response.json();
                        clearInterval(progressInterval);
                        this.uploadProgress = 100;

                        if (data.success) {
                            this.showMessage(data.message, 'success');
                            
                            // Avatar'ı güncelle
                            setTimeout(() => {
                                this.updateAvatarDisplay(data.avatar_url);
                                this.clearPreview();
                            }, 500);
                        } else {
                            this.showMessage(data.message, 'error');
                        }
                    } catch (error) {
                        this.showMessage('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
                    } finally {
                        this.isUploading = false;
                        this.isLoading = false;
                        setTimeout(() => {
                            this.uploadProgress = 0;
                        }, 1000);
                    }
                },

                async removeAvatar() {
                    this.isRemoving = true;
                    this.isLoading = true;

                    try {
                        const response = await fetch('{{ route("avatar.remove") }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showMessage(data.message, 'success');
                            this.updateAvatarToPlaceholder();
                        } else {
                            this.showMessage(data.message, 'error');
                        }
                    } catch (error) {
                        this.showMessage('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
                    } finally {
                        this.isRemoving = false;
                        this.isLoading = false;
                    }
                },

                updateAvatarDisplay(newUrl) {
                    // Avatar URL'ini güncelle - resim otomatik yüklenecek
                    this.avatarUrl = newUrl;
                },

                updateAvatarToPlaceholder() {
                    // Avatar URL'ini temizle - placeholder otomatik gösterilecek
                    this.avatarUrl = '';
                },

                showMessage(text, type = 'success') {
                    this.message = text;
                    this.messageType = type;
                    
                    // 5 saniye sonra mesajı otomatik kapat
                    setTimeout(() => {
                        this.message = '';
                    }, 5000);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>