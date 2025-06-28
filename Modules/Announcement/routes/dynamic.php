<?php

/**
 * Announcement Modülü Dynamic Routes
 * 
 * Bu dosya dynamic route tanımlarını içeriyordu ancak web.php'taki
 * DynamicRouteService sistemi ile çakışma yaratıyordu.
 * 
 * Tüm dynamic routing artık DynamicRouteService tarafından 
 * otomatik olarak yönetiliyor.
 * 
 * Route handling: App\Services\DynamicRouteService
 * Controller: Modules\Announcement\app\Http\Controllers\Front\AnnouncementController
 */

// Dynamic routes artık DynamicRouteService tarafından otomatik yönetiliyor
// Bu dosya boş bırakılmıştır - çakışmaları önlemek için