<?php
// Modules/Page/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Livewire\Admin\PageComponent;
use Modules\Page\App\Http\Livewire\Admin\PageManageComponent;
use Modules\Page\App\Http\Controllers\Admin\PageTranslationController;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('page')
            ->name('page.')
            ->group(function () {
                Route::get('/', PageComponent::class)
                    ->middleware('module.permission:page,view')
                    ->name('index');
                Route::get('/manage/{id?}', PageManageComponent::class)
                    ->middleware('module.permission:page,update')
                    ->name('manage');
                Route::post('/set-editing-language', function () {
                    return response()->json(['status' => 'success']);
                })->name('set-editing-language');

                // Client-side language session update (no loading bar)
                Route::post('/manage/update-language-session', function (\Illuminate\Http\Request $request) {
                    $language = $request->input('language');
                    if ($language && in_array($language, config('app.available_languages', ['tr', 'en', 'ar']))) {
                        session(['page_manage_language' => $language]);
                        return response()->json(['status' => 'success', 'language' => $language]);
                    }
                    return response()->json(['status' => 'error'], 400);
                })->name('manage.update-language-session');
                
                // Universal SEO Management System kullanılıyor
                // Eski seo-data route'u kaldırıldı
                
                // AI Translation endpoints - Yeni controller
                Route::prefix('ai/translation')->name('ai.translation.')->group(function () {
                    Route::post('/translate-multi', [PageTranslationController::class, 'translateMulti'])->name('translate-multi');
                    Route::post('/check-progress', [PageTranslationController::class, 'checkProgress'])->name('check-progress');
                });
                
            });
    });