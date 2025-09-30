{{-- SEO TAB CONTENT - Parent Component Binding --}}
@foreach($availableLanguages as $lang)
    <div class="seo-language-content" data-language="{{ $lang }}"
        style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">

        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-search me-2"></i>Temel SEO - {{ strtoupper($lang) }}
                </h6>
            </div>
            <div class="card-body">
                {{-- SEO Title --}}
                <div class="mb-3">
                    <label class="form-label">SEO Başlık</label>
                    <input type="text" class="form-control seo-no-enter"
                        wire:model.live="{{ $wireModelPrefix }}.{{ $lang }}.seo_title"
                        placeholder="SEO Başlık">
                </div>

                {{-- SEO Description --}}
                <div class="mb-3">
                    <label class="form-label">SEO Açıklama</label>
                    <textarea class="form-control seo-no-enter" rows="3"
                        wire:model.live="{{ $wireModelPrefix }}.{{ $lang }}.seo_description"
                        placeholder="SEO Açıklama"></textarea>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fab fa-facebook me-2"></i>Open Graph (Facebook/LinkedIn) - {{ strtoupper($lang) }}
                </h6>
            </div>
            <div class="card-body">
                {{-- OG Title --}}
                <div class="mb-3">
                    <label class="form-label">OG Başlık</label>
                    <input type="text" class="form-control seo-no-enter"
                        wire:model.live="{{ $wireModelPrefix }}.{{ $lang }}.og_title"
                        placeholder="Facebook/LinkedIn başlık">
                </div>

                {{-- OG Description --}}
                <div class="mb-3">
                    <label class="form-label">OG Açıklama</label>
                    <textarea class="form-control seo-no-enter" rows="3"
                        wire:model.live="{{ $wireModelPrefix }}.{{ $lang }}.og_description"
                        placeholder="Facebook/LinkedIn açıklama"></textarea>
                </div>
            </div>
        </div>

    </div>
@endforeach