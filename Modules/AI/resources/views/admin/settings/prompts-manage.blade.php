@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('pretitle', 'AI Ayarları')
@section('title', $prompt ? 'Prompt Düzenle' : 'Yeni Prompt Oluştur')

@section('content')
    <div class="row">
        <div class="col-3">
            @include('ai::admin.settings.sidebar')
        </div>
        <div class="col-9">
            <form method="POST" action="{{ $prompt ? route('admin.ai.settings.prompts.update', $prompt->id) : route('admin.ai.settings.prompts.store') }}">
                @csrf
                @if($prompt)
                    @method('PUT')
                @endif
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-{{ $prompt ? 'edit' : 'plus' }} me-2"></i>
                            {{ $prompt ? 'Prompt Düzenle: ' . $prompt->name : 'Yeni Prompt Oluştur' }}
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" id="name" placeholder="Prompt Adı" 
                                           value="{{ old('name', $prompt->name ?? '') }}" required>
                                    <label for="name">Prompt Adı</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">Prompt için açıklayıcı bir ad girin (örn: "Eğlenceli Asistan", "Resmi Yanıtlar")</div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              name="content" id="content" placeholder="Prompt İçeriği" 
                                              style="height: 250px;" required>{{ old('content', $prompt->content ?? '') }}</textarea>
                                    <label for="content">Prompt İçeriği</label>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">AI'ya nasıl davranması gerektiğini söyleyen talimatları yazın.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', $prompt->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Prompt Aktif
                                        <span class="form-check-description">Aktif promptlar kullanılabilir</span>
                                    </label>
                                </div>
                            </div>
                            
                            @if(!$prompt || !$prompt->is_system)
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1" 
                                           id="is_default" {{ old('is_default', $prompt->is_default ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Varsayılan Prompt
                                        <span class="form-check-description">Bu prompt varsayılan olarak kullanılır</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Portfolio tarzı form footer -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Sol taraf - İptal butonu -->
                            <a href="{{ route('admin.ai.settings.prompts') }}" class="btn btn-link text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>
                                {{ __('ai::admin.cancel') }}
                            </a>
                            
                            <!-- Sağ taraf - Kaydet butonları -->
                            <div class="d-flex gap-2">
                                <!-- Kaydet ve Yeni Oluştur -->
                                <button type="button" class="btn" onclick="saveAndNew()">
                                    <i class="fa-thin fa-plus me-2"></i> 
                                    Kaydet ve Yeni Oluştur
                                </button>
                                
                                <!-- Ana Kaydet Butonu -->
                                <button type="submit" class="btn btn-primary ms-4">
                                    <i class="fa-thin fa-floppy-disk me-2"></i> 
                                    {{ __('ai::admin.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function saveAndNew() {
    // Form'u submit et ve başarılı olursa yeni sayfaya yönlendir
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            // Başarılı ise yeni prompt sayfasına yönlendir
            window.location.href = '{{ route("admin.ai.settings.prompts.manage") }}';
        } else {
            // Hata varsa normal submit yap
            form.submit();
        }
    })
    .catch(error => {
        // Hata durumunda normal submit
        form.submit();
    });
}
</script>
@endpush