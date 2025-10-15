{{--
    AUTO SEO FILL TRIGGER COMPONENT
    Premium tenant'lar için otomatik SEO doldurma tetikleyicisi

    Kullanım:
    <x-auto-seo-trigger
        :model="$page"
        model-type="page"
        :locale="$currentLocale"
    />

    Bu component sayfaya data attribute'ları ekler ve
    auto-seo-fill.js tarafından kontrol edilir.
--}}

@props([
    'model' => null,
    'modelType' => 'page',
    'locale' => app()->getLocale()
])

@php
    // Tenant kontrolü
    $tenant = tenant();
    $isPremium = $tenant && $tenant->isPremium();

    // SEO boş mu kontrolü
    $seoEmpty = false;
    if ($model && method_exists($model, 'seoSetting')) {
        $seoSetting = $model->seoSetting;
        if (!$seoSetting) {
            $seoEmpty = true;
        } else {
            $titles = $seoSetting->titles ?? [];
            $descriptions = $seoSetting->descriptions ?? [];
            $titleEmpty = empty(trim($titles[$locale] ?? ''));
            $descriptionEmpty = empty(trim($descriptions[$locale] ?? ''));
            $seoEmpty = $titleEmpty && $descriptionEmpty;
        }
    }

    // Debug info (sadece development)
    $debug = config('app.debug', false);
@endphp

{{-- Trigger Data Attributes --}}
@if($isPremium && $seoEmpty && $model)
    <div
        data-auto-seo-fill="true"
        data-premium-tenant="1"
        data-seo-empty="1"
        data-model-type="{{ $modelType }}"
        data-model-id="{{ $model->id }}"
        data-locale="{{ $locale }}"
        style="display: none;"
        id="auto-seo-trigger"
    >
        @if($debug)
            <!-- Auto SEO Fill: ENABLED -->
            <!-- Premium: YES | SEO Empty: YES | Model: {{ $modelType }}#{{ $model->id }} -->
        @endif
    </div>
@elseif($debug)
    <!-- Auto SEO Fill: DISABLED -->
    <!-- Premium: {{ $isPremium ? 'YES' : 'NO' }} | SEO Empty: {{ $seoEmpty ? 'YES' : 'NO' }} -->
@endif
