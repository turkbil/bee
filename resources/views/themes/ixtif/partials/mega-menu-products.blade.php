{{-- Mega Menu Router - Tenant-Based Include --}}
{{-- ⚠️ DO NOT REMOVE - Performance optimization & Tenant isolation --}}

@php
// 🛡️ Admin sayfalarında mega-menu render etme
if (request()->is('admin/*')) {
    return;
}

// 🛡️ Tenant context yoksa render etme
if (!function_exists('tenant') || !tenant()) {
    return;
}

// 📂 Tenant ID'ye göre doğru mega menu dosyasını belirle
$tenantId = tenant()->id;
$megaMenuPath = "themes.ixtif.partials.mega-menu.{$tenantId}.products";

// 🔍 Dosya yoksa fallback (varsayılan boş döner)
if (!view()->exists($megaMenuPath)) {
    return;
}
@endphp

{{-- Tenant-specific mega menu include --}}
@include($megaMenuPath)
