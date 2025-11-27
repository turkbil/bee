<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 🔐 SESSION CHECK - Tenant 1001 (Muzibu) için session kontrolü
Route::get('/session/check', function (Request $request) {
    // Tenant 1001 kontrolü
    if (!tenant() || tenant()->id != 1001) {
        return response()->json(['authenticated' => false]);
    }

    // Auth kontrolü
    if (!auth()->check()) {
        return response()->json(['authenticated' => false], 401);
    }

    // ✅ Kullanıcı authenticated ise, session aktif demektir
    // DB'de session yoksa bile sorun yok (garbage collection olmuş olabilir)
    // Önemli olan: Laravel'in auth()->check() true dönmesi
    return response()->json([
        'authenticated' => true,
        'user' => [
            'id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ]
    ]);
})->middleware('web')->name('api.session.check');

// 🔐 AUTH ROUTES - Muzibu Authentication
Route::prefix('auth')->middleware(['web'])->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\Auth\AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/register', [\App\Http\Controllers\Api\Auth\AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/check-email', [\App\Http\Controllers\Api\Auth\AuthController::class, 'checkEmail'])->name('api.auth.check-email');
    Route::post('/logout', [\App\Http\Controllers\Api\Auth\AuthController::class, 'logout'])->middleware('auth:sanctum')->name('api.auth.logout');
    Route::get('/me', [\App\Http\Controllers\Api\Auth\AuthController::class, 'me'])->name('api.auth.me');
    Route::post('/forgot-password', [\App\Http\Controllers\Api\Auth\AuthController::class, 'forgotPassword'])->name('api.auth.forgot');
    Route::post('/reset-password', [\App\Http\Controllers\Api\Auth\AuthController::class, 'resetPassword'])->name('api.auth.reset');
});

// Mobile App Endpoints
// Module Discovery for Dynamic Loading
Route::get('/discover-modules', function (Request $request) {
    // Mobil uygulamada kullanılacak modüller
    $modules = [
        [
            'name' => 'MenuManagement',
            'slug' => 'menu-management',
            'hasApi' => true,
            'endpoints' => [
                'index' => '/menus',
                'show' => '/menus/{id}',
                'headerMenu' => '/header-menu',
                'resolveUrl' => '/menu-items/{id}/resolve-url'
            ]
        ],
        [
            'name' => 'LanguageManagement',
            'slug' => 'languagemanagement',
            'hasApi' => true,
            'endpoints' => [
                'index' => '/v1/languagemanagement',
                'tenantLanguages' => '/admin/tenant-languages',
                'defaultLanguage' => '/v1/languagemanagement',
                'switchLanguage' => '/v1/languagemanagement'
            ]
        ],
        [
            'name' => 'Page',
            'slug' => 'page',
            'hasApi' => true,
            'endpoints' => [
                'index' => '/pages',
                'show' => '/pages/{id}',
                'homepage' => '/homepage',
                'bySlug' => '/pages/slug/{slug}'
            ]
        ],
        [
            'name' => 'Portfolio',
            'slug' => 'portfolio',
            'hasApi' => true,
            'endpoints' => [
                'index' => '/items',
                'show' => '/items/{id}',
                'categories' => '/categories'
            ]
        ],
        [
            'name' => 'Announcement',
            'slug' => 'announcement',
            'hasApi' => true,
            'endpoints' => [
                'index' => '/items',
                'show' => '/items/{id}',
                'latest' => '/latest'
            ]
        ]
    ];

    return response()->json($modules);
});

// Menu Management Endpoints
Route::prefix('menu-management')->group(function () {
    Route::get('/header-menu', function (Request $request) {
        // Mock header menu data for mobile
        $menuItems = [
            [
                'item_id' => 1,
                'title' => [
                    'tr' => 'Ana Sayfa',
                    'en' => 'Home',
                    'ar' => 'الرئيسية'
                ],
                'url_type' => 'internal',
                'url_data' => ['page_id' => 1],
                'is_active' => true,
                'visibility' => 'public',
                'icon' => '🏠',
                'children' => []
            ],
            [
                'item_id' => 2,
                'title' => [
                    'tr' => 'Hakkımızda', 
                    'en' => 'About',
                    'ar' => 'من نحن'
                ],
                'url_type' => 'internal',
                'url_data' => ['page_id' => 2],
                'is_active' => true,
                'visibility' => 'public',
                'icon' => 'ℹ️',
                'children' => []
            ],
            [
                'item_id' => 3,
                'title' => [
                    'tr' => 'Portfolyo',
                    'en' => 'Portfolio', 
                    'ar' => 'المشاريع'
                ],
                'url_type' => 'module',
                'url_data' => ['module' => 'Portfolio'],
                'is_active' => true,
                'visibility' => 'public',
                'icon' => '💼',
                'children' => []
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $menuItems
        ]);
    });

    Route::get('/menu-items/{id}/resolve-url', function (Request $request, $id) {
        // Mock URL resolution
        return response()->json([
            'success' => true,
            'data' => [
                'resolved_url' => "/page/{$id}",
                'url_type' => 'internal'
            ]
        ]);
    });
});

// Page Management Endpoints
Route::prefix('page')->group(function () {
    Route::get('/pages/{id}', function (Request $request, $id) {
        // Mock page data
        $pages = [
            1 => [
                'id' => 1,
                'title' => [
                    'tr' => 'Ana Sayfa',
                    'en' => 'Home Page',
                    'ar' => 'الرئيسية'
                ],
                'content' => [
                    'tr' => '<h1>Ana Sayfa</h1><p>Hoş geldiniz! Bu dinamik tenant sistemi ile çalışan React Native uygulamasıdır.</p>',
                    'en' => '<h1>Home Page</h1><p>Welcome! This is a React Native app with dynamic tenant system.</p>',
                    'ar' => '<h1>الرئيسية</h1><p>مرحباً! هذا تطبيق React Native مع نظام المستأجرين الديناميكي.</p>'
                ],
                'slug' => [
                    'tr' => 'ana-sayfa',
                    'en' => 'home-page',
                    'ar' => 'الرئيسية'
                ],
                'is_active' => true,
                'meta' => [
                    'description' => [
                        'tr' => 'Ana sayfa açıklaması',
                        'en' => 'Home page description',
                        'ar' => 'وصف الرئيسية'
                    ]
                ]
            ],
            2 => [
                'id' => 2,
                'title' => [
                    'tr' => 'Hakkımızda',
                    'en' => 'About Us',
                    'ar' => 'من نحن'
                ],
                'content' => [
                    'tr' => '<h1>Hakkımızda</h1><p>Bu sayfa dinamik olarak Laravel backend\'den gelmektedir.</p>',
                    'en' => '<h1>About Us</h1><p>This page is dynamically loaded from Laravel backend.</p>',
                    'ar' => '<h1>من نحن</h1><p>هذه الصفحة يتم تحميلها ديناميكياً من Laravel.</p>'
                ],
                'slug' => [
                    'tr' => 'hakkimizda',
                    'en' => 'about-us',
                    'ar' => 'من-نحن'
                ],
                'is_active' => true
            ]
        ];

        $page = $pages[$id] ?? null;
        
        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    });

    Route::get('/homepage', function (Request $request) {
        // Return homepage (page with id = 1)
        return redirect('/api/page/pages/1');
    });
});

// Admin Endpoints for Language Management
Route::prefix('admin')->group(function () {
    Route::get('/tenant-languages', function (Request $request) {
        // Mock tenant languages
        $languages = [
            [
                'id' => 1,
                'code' => 'tr',
                'name' => 'Türkçe',
                'native_name' => 'Türkçe',
                'is_default' => true,
                'is_active' => true,
                'is_visible' => true,
                'flag' => '🇹🇷'
            ],
            [
                'id' => 2, 
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'is_default' => false,
                'is_active' => true,
                'is_visible' => true,
                'flag' => '🇺🇸'
            ],
            [
                'id' => 3,
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'is_default' => false,
                'is_active' => true,
                'is_visible' => true,
                'flag' => '🇸🇦'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $languages
        ]);
    });
});

// Mobile app dynamic tenant endpoint
Route::put('/mobile/profile', function (Request $request) {
    // Get tenant config from request headers
    $tenantId = $request->header('X-Tenant-ID');
    $tenantDomain = $request->header('X-Tenant-Domain');
    
    // Tenant configuration mapping
    $tenantConfigs = [
        '2' => [
            'name' => 'Kırmızı',
            'domain' => 'a.test',
            'database' => 'tenant_a'
        ],
        '3' => [
            'name' => 'Sarı', 
            'domain' => 'b.test',
            'database' => 'tenant_b'
        ],
        '4' => [
            'name' => 'Mavi',
            'domain' => 'c.test', 
            'database' => 'tenant_c'
        ]
    ];
    
    if (!$tenantId || !isset($tenantConfigs[$tenantId])) {
        return response()->json(['error' => 'Invalid tenant configuration'], 400);
    }
    
    $tenantConfig = $tenantConfigs[$tenantId];
    
    \Log::info("🔄 Mobile Profile Update Request (Tenant {$tenantId}):", [
        'tenant_name' => $tenantConfig['name'],
        'tenant_domain' => $tenantConfig['domain'],
        'database' => $tenantConfig['database'],
        'data' => $request->all()
    ]);
    
    try {
        // Switch to tenant database dynamically
        \DB::purge('tenant');
        config(['database.connections.tenant.database' => $tenantConfig['database']]);
        \DB::reconnect('tenant');
        
        \Log::info("🔄 Switched to tenant database: {$tenantConfig['database']}");
        
        // Find user in tenant database
        $user = \DB::connection('tenant')->table('users')
            ->where('email', 'nurullah@nurullah.net')
            ->first();
        
        if (!$user) {
            \Log::error("❌ User not found in {$tenantConfig['database']}: nurullah@nurullah.net");
            return response()->json(['error' => 'User not found in tenant database'], 404);
        }
        
        // Update user data in tenant database
        $updateData = array_filter($request->only(['name', 'email', 'phone', 'bio']));
        
        \DB::connection('tenant')->table('users')
            ->where('id', $user->id)
            ->update(array_merge($updateData, ['updated_at' => now()]));
        
        // Get updated user data
        $updatedUser = \DB::connection('tenant')->table('users')->where('id', $user->id)->first();
        
        \Log::info('✅ TENANT Database Updated:', [
            'tenant_id' => $tenantId,
            'tenant_db' => $tenantConfig['database'],
            'user_id' => $user->id,
            'old_name' => $user->name,
            'new_name' => $updatedUser->name,
            'updated_fields' => $updateData
        ]);
        
        return response()->json([
            'user' => [
                'id' => $updatedUser->id,
                'name' => $updatedUser->name,
                'email' => $updatedUser->email,
                'phone' => $updatedUser->phone,
                'bio' => $updatedUser->bio,
                'updated_at' => $updatedUser->updated_at
            ],
            'tenant_info' => [
                'tenant_id' => (int)$tenantId,
                'tenant_name' => $tenantConfig['name'],
                'tenant_domain' => $tenantConfig['domain'],
                'database' => $tenantConfig['database']
            ],
            'message' => 'Profile updated successfully in tenant database'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('❌ Tenant Profile Update Failed:', [
            'error' => $e->getMessage(),
            'tenant_id' => $tenantId,
            'database' => $tenantConfig['database']
        ]);
        return response()->json(['error' => 'Tenant update failed: ' . $e->getMessage()], 500);
    }
});

// API v1 routes
Route::prefix('v1')->group(function () {
    
    // API v1 base endpoint
    Route::get('/', function () {
        return response()->json([
            'message' => 'API v1 is running',
            'version' => '1.0.0',
            'timestamp' => now()->toISOString(),
            'endpoints' => [
                'auth' => '/api/v1/auth/*',
                'profile' => '/api/v1/profile',
                'tokens' => '/api/v1/tokens',
                'tenant' => '/api/v1/tenant'
            ]
        ]);
    });
    
    // Auth routes (public)
    Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/auth/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    // Logout - public endpoint, controller session kontrolü yapar
    Route::post('/auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {

        // Auth management
        Route::get('/auth/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
        
        // User profile management
        Route::get('/profile', [App\Http\Controllers\Api\UserProfileController::class, 'getProfile']);
        Route::put('/profile', [App\Http\Controllers\Api\UserProfileController::class, 'updateProfile']);
        Route::post('/profile/change-password', [App\Http\Controllers\Api\UserProfileController::class, 'changePassword']);
        Route::post('/profile/avatar', [App\Http\Controllers\Api\UserProfileController::class, 'uploadAvatar']);
        Route::delete('/profile/avatar', [App\Http\Controllers\Api\UserProfileController::class, 'deleteAvatar']);
        
        // Token management
        Route::get('/tokens', [App\Http\Controllers\Api\TokenController::class, 'getTokens']);
        Route::post('/tokens/refresh', [App\Http\Controllers\Api\TokenController::class, 'refreshToken']);
        Route::delete('/tokens/{token_id}', [App\Http\Controllers\Api\TokenController::class, 'revokeToken']);
        Route::delete('/tokens', [App\Http\Controllers\Api\TokenController::class, 'revokeAllTokens']);
        Route::get('/tokens/current', [App\Http\Controllers\Api\TokenController::class, 'getTokenInfo']);
        Route::get('/tokens/validate', [App\Http\Controllers\Api\TokenController::class, 'validateToken']);
        
        // Tenant info
        Route::get('/tenant', [App\Http\Controllers\Api\TenantController::class, 'getCurrentTenant']);
        Route::get('/tenant/details', [App\Http\Controllers\Api\TenantController::class, 'getTenantDetails']);

    });

});