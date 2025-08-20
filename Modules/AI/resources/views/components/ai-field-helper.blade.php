{{-- AI Field Helper Component --}}
<div class="ai-field-helper d-inline-block" data-field="{{ $fieldName }}">
    <button type="button" class="btn btn-sm btn-outline-primary ai-assist-btn" 
            title="AI Yardımcısı" data-bs-toggle="dropdown">
        <i class="fa-solid fa-sparkles"></i>
    </button>
    <div class="dropdown-menu ai-actions-menu">
        <h6 class="dropdown-header">AI Yardımcısı</h6>
        <a class="dropdown-item" href="#" data-action="generate">
            <i class="fa-solid fa-magic-wand-sparkles"></i> İçerik Oluştur
        </a>
        <a class="dropdown-item" href="#" data-action="optimize">
            <i class="fa-solid fa-sliders"></i> Optimize Et
        </a>
        <a class="dropdown-item" href="#" data-action="translate">
            <i class="fa-solid fa-language"></i> Çevir
        </a>
    </div>
</div>
