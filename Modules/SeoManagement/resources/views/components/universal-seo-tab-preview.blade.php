@props([
    'availableLanguages' => ['tr', 'en', 'ar'],
    'currentLanguage' => 'tr'
])

@php
$activeLanguages = \Modules\LanguageManagement\App\Models\TenantLanguage::where('is_active', true)
    ->orderBy('sort_order')
    ->get();
$availableLanguages = $activeLanguages->pluck('code')->toArray();
$currentLanguage = $activeLanguages->first()?->code ?? 'tr';
@endphp

{{-- Aynı universal component'i disabled=true ile kullan --}}
<x-seomanagement::universal-seo-tab 
    :available-languages="$availableLanguages" 
    :current-language="$currentLanguage" 
    :disabled="true" />

<!-- Preview Note -->
<div class="bg-success-lt p-3 rounded mt-4">
    <div class="d-flex align-items-center">
        <i class="fa-solid fa-check-circle me-2 text-success"></i>
        <strong>Universal SEO Component:</strong>
        <span class="ms-2">Bu önizleme A1'deki Page manage sayfasındaki SEO tab'ının birebir kopyasıdır. Aynı component'i kullanır.</span>
    </div>
</div>