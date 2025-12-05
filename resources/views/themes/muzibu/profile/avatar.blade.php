@extends('themes.muzibu.layouts.app')

@section('title', 'Avatar Yönetimi - Muzibu')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Left Sidebar --}}
        <div class="lg:col-span-1">
            @include('themes.muzibu.components.profile-sidebar', ['active' => 'avatar'])
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Header --}}
            <div class="mb-4">
                <h1 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-camera text-purple-400 mr-2"></i>
                    Avatar Yönetimi
                </h1>
                <p class="text-muzibu-text-gray">Profil fotoğrafınızı yükleyin veya güncelleyin</p>
            </div>

    {{-- Success/Error Messages --}}
    @if (session('message'))
        <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-6 py-4 rounded-lg mb-6">
            ✅ {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-500/20 border border-red-500/50 text-red-400 px-6 py-4 rounded-lg mb-6">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Avatar Management --}}
    <div x-data="{
        selectedFile: null,
        previewUrl: null,
        isUploading: false,
        isRemoving: false,
        isLoading: false,
        isDragOver: false,
        uploadProgress: 0,
        message: '',
        messageType: 'success',
        avatarUrl: '{{ $user->getFirstMedia("avatar") ? $user->getFirstMedia("avatar")->getUrl() . "?v=" . time() : "" }}',

        init() {
            @if(session('message'))
                this.showMessage('{{ session("message") }}', 'success');
            @endif
            @if(session('error'))
                this.showMessage('{{ session("error") }}', 'error');
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
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                this.showMessage('Sadece resim dosyaları (JPEG, PNG, WebP) yükleyebilirsiniz.', 'error');
                return;
            }
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
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

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
                    setTimeout(() => {
                        this.avatarUrl = data.avatar_url;
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
                setTimeout(() => { this.uploadProgress = 0; }, 1000);
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
                    this.avatarUrl = '';
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

        showMessage(text, type = 'success') {
            this.message = text;
            this.messageType = type;
            setTimeout(() => { this.message = ''; }, 5000);
        }
    }" class="bg-white/5 backdrop-blur-sm rounded-lg p-8 border border-white/10">
        
        {{-- Alert Messages --}}
        <div x-show="message" x-transition class="mb-6 p-4 rounded-lg" :class="messageType === 'success' ? 'bg-green-500/20 border border-green-500/50 text-green-400' : 'bg-red-500/20 border border-red-500/50 text-red-400'">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i x-show="messageType === 'success'" class="fas fa-check-circle"></i>
                    <i x-show="messageType === 'error'" class="fas fa-exclamation-circle"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium" x-text="message"></p>
                </div>
                <button @click="message = ''" class="ml-auto text-white/60 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Current Avatar Display --}}
        <div class="mb-8">
            <h4 class="text-sm font-medium text-white mb-4">Mevcut Avatar</h4>
            <div class="flex items-center space-x-6">
                <div class="relative">
                    {{-- Avatar Image or Placeholder --}}
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-muzibu-coral shadow-lg">
                        <div x-show="avatarUrl" class="w-full h-full">
                            <img x-ref="avatarImg"
                                 :src="avatarUrl"
                                 alt="{{ $user->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <div x-show="!avatarUrl" class="w-full h-full bg-gradient-to-br from-muzibu-coral to-muzibu-coral-light flex items-center justify-center text-white font-bold text-2xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>

                    {{-- Remove Button (Only show if avatar exists) --}}
                    <button x-show="avatarUrl"
                            @click="removeAvatar()"
                            :disabled="isLoading"
                            x-transition
                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 disabled:bg-red-300 text-white rounded-full w-7 h-7 flex items-center justify-center transition-colors duration-200 shadow-lg">
                        <i x-show="!isRemoving" class="fas fa-times text-sm"></i>
                        <i x-show="isRemoving" class="fas fa-spinner fa-spin text-sm"></i>
                    </button>
                </div>

                <div class="flex-1">
                    <div x-show="avatarUrl">
                        <p class="text-sm font-medium text-white">Avatar mevcut</p>
                        <p class="text-sm text-muzibu-text-gray">Değiştirmek için yeni bir fotoğraf yükleyin</p>
                        @if($user->getFirstMedia('avatar'))
                            <p class="text-xs text-muzibu-text-gray mt-1">Son güncelleme: {{ $user->getFirstMedia('avatar')->updated_at->diffForHumans() }}</p>
                        @endif
                    </div>
                    <div x-show="!avatarUrl">
                        <p class="text-sm font-medium text-white">Avatar yok</p>
                        <p class="text-sm text-muzibu-text-gray">Profil fotoğrafınızı yükleyin</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upload Section --}}
        <div class="space-y-6">
            <h4 class="text-sm font-medium text-white">Yeni Avatar Yükle</h4>

            {{-- Drop Zone --}}
            <div @drop="handleDrop($event)"
                 @dragover.prevent
                 @dragenter.prevent
                 @dragleave="isDragOver = false"
                 @dragover="isDragOver = true"
                 :class="isDragOver ? 'border-muzibu-coral bg-muzibu-coral/10' : 'border-white/20'"
                 class="border-2 border-dashed rounded-lg p-8 text-center transition-colors duration-200">

                <input type="file"
                       x-ref="fileInput"
                       @change="handleFileSelect($event)"
                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                       class="hidden">

                {{-- Upload Icon --}}
                <div class="mx-auto h-16 w-16 text-muzibu-text-gray mb-4">
                    <i class="fas fa-cloud-upload-alt text-6xl"></i>
                </div>

                {{-- Upload Text --}}
                <div class="space-y-2">
                    <p class="text-sm text-muzibu-text-gray">
                        <button @click="$refs.fileInput.click()" class="font-semibold text-muzibu-coral hover:text-muzibu-coral-light">Dosya seçin</button>
                        veya buraya sürükleyip bırakın
                    </p>
                    <p class="text-xs text-muzibu-text-gray">PNG, JPG, WebP (Maksimum 2MB)</p>
                </div>

                {{-- Preview --}}
                <div x-show="previewUrl" class="mt-4">
                    <div class="inline-block relative">
                        <img :src="previewUrl" class="w-20 h-20 rounded-full object-cover border-2 border-muzibu-coral">
                        <button @click="clearPreview()" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                    </div>
                </div>
            </div>

            {{-- Upload Progress --}}
            <div x-show="isUploading" x-transition class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-muzibu-text-gray">Yükleniyor...</span>
                    <span class="text-muzibu-text-gray" x-text="uploadProgress + '%'"></span>
                </div>
                <div class="w-full bg-white/10 rounded-full h-2">
                    <div class="bg-muzibu-coral h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                </div>
            </div>

            {{-- Upload Button --}}
            <button @click="uploadAvatar()"
                    :disabled="!selectedFile || isLoading"
                    x-show="selectedFile && !isUploading"
                    class="w-full bg-muzibu-coral hover:bg-muzibu-coral-light disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                <i class="fas fa-upload"></i>
                <span>Avatar Yükle</span>
            </button>
        </div>
        </div>
    </div>
</div>
@endsection
