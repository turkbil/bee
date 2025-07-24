{{-- Global SEO Form Component --}}
@props([
    'currentSeoData' => [],
    'currentLanguage' => 'tr'
])

<!-- SEO Form - Global Component -->
<div class="seo-form-container">
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <small>SEO ayarları şu anda seçili dil için düzenleniyor. Dil değiştirmek için sağ üstteki dil butonlarını kullanın.</small>
    </div>
    
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-6">
            <!-- Title Field -->
            <div class="form-group mb-4">
                <div class="form-floating input-with-counter">
                    <input type="text" 
                           class="form-control"
                           placeholder="SEO optimizasyonlu başlık yazın..."
                           id="seo-title"
                           maxlength="60"
                           value="{{ $currentSeoData['title'] ?? '' }}"
                           style="border-radius: 0.25rem !important;">
                    <label for="seo-title">
                        SEO Başlık <span class="required-star">★</span>
                    </label>
                    <div class="character-counter">
                        <span class="counter-text" id="title-counter">
                            {{ strlen($currentSeoData['title'] ?? '') }}/60
                        </span>
                        <div class="counter-bar">
                            <div class="counter-progress" id="title-progress" 
                                 style="width: {{ min(100, (strlen($currentSeoData['title'] ?? '') / 60) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
                <small class="form-text text-muted">
                    Ideal: 50-60 karakter. Arama motorlarında görünecek başlık.
                </small>
            </div>

            <!-- Description Field -->
            <div class="form-group mb-4">
                <div class="form-floating input-with-counter">
                    <textarea class="form-control" 
                              id="seo-description"
                              rows="3"
                              style="height: 100px; border-radius: 0.25rem !important;"
                              maxlength="160"
                              placeholder="Bu sayfa hakkında kısa bir açıklama yazın...">{{ $currentSeoData['description'] ?? '' }}</textarea>
                    <label for="seo-description">
                        Meta Açıklama <span class="required-star">★</span>
                    </label>
                    <div class="character-counter">
                        <span class="counter-text" id="description-counter">
                            {{ strlen($currentSeoData['description'] ?? '') }}/160
                        </span>
                        <div class="counter-bar">
                            <div class="counter-progress" id="description-progress" 
                                 style="width: {{ min(100, (strlen($currentSeoData['description'] ?? '') / 160) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
                <small class="form-text text-muted">
                    Ideal: 150-160 karakter. Arama sonuçlarında görünecek açıklama.
                </small>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
            <!-- Keywords Field -->
            <div class="form-group mb-4">
                <label class="form-label">
                    Anahtar Kelimeler <span class="required-star">★</span>
                </label>
                
                <!-- Keyword Display Area -->
                <div class="mb-3" id="keywords-display" style="min-height: 50px; padding: 10px; border: 1px solid #dee2e6; border-radius: 0.25rem; background-color: #f8f9fa;">
                    @if(isset($currentSeoData['keywords']) && is_array($currentSeoData['keywords']) && count($currentSeoData['keywords']) > 0)
                        @foreach($currentSeoData['keywords'] as $keyword)
                            @if($keyword && trim($keyword))
                                <span class="badge badge-outline d-inline-flex align-items-center border me-2 mb-2" 
                                      style="font-size: 1rem; padding: 8px 12px; border-radius: 0.25rem !important;" 
                                      data-keyword="{{ $keyword }}">
                                    {{ $keyword }}
                                    <button type="button" class="btn-close ms-2 remove-keyword" 
                                            style="font-size: 0.7em; padding: 4px;" 
                                            aria-label="Kaldır"></button>
                                </span>
                            @endif
                        @endforeach
                    @else
                        <span class="text-muted">Henüz anahtar kelime eklenmemiş.</span>
                    @endif
                </div>
                
                <!-- Keyword Input -->
                <div class="input-group">
                    <input type="text" 
                           class="form-control" 
                           id="new-keyword-input"
                           placeholder="Yeni anahtar kelime ekleyin..."
                           style="border-radius: 0.25rem 0 0 0.25rem !important;">
                    <button class="btn btn-primary" 
                            type="button" 
                            id="add-keyword-btn"
                            style="border-radius: 0 0.25rem 0.25rem 0 !important;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <small class="form-text text-muted">
                    Enter tuşu ile veya + butonuna tıklayarak ekleyebilirsiniz.
                </small>
            </div>
        </div>
    </div>
</div>