<?php

/**
 * Page Modülü Dynamic Routes
 * 
 * Bu dosya dynamic route tanımlarını içeriyordu ancak web.php'taki
 * DynamicRouteService sistemi ile çakışma yaratıyordu.
 * 
 * Tüm dynamic routing artık DynamicRouteService tarafından 
 * otomatik olarak yönetiliyor.
 * 
 * Route handling: App\Services\DynamicRouteService
 * Controller: Modules\Page\app\Http\Controllers\Front\PageController
 */

Log::info('📄 Page modülü dynamic route\'ları yükleniyor');

// Dynamic routes artık DynamicRouteService tarafından otomatik yönetiliyor
// Bu dosya boş bırakılmıştır - çakışmaları önlemek için

Log::info('✅ Page modülü dynamic route\'ları yüklendi');