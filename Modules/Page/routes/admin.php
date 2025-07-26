<?php
// Modules/Page/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Livewire\Admin\PageComponent;
use Modules\Page\App\Http\Livewire\Admin\PageManageComponent;

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
                
                Route::get('/seo-data/{pageId}/{language}', function ($pageId, $language) {
                    $page = \Modules\Page\App\Models\Page::findOrFail($pageId);
                    $seoSettings = $page->seoSetting;
                    
                    $seoData = [
                        'seo_title' => '',
                        'seo_description' => '',
                        'seo_keywords' => '',
                        'canonical_url' => ''
                    ];
                    
                    if ($seoSettings) {
                        $titles = $seoSettings->titles ?? [];
                        $descriptions = $seoSettings->descriptions ?? [];
                        $keywords = $seoSettings->keywords ?? [];
                        
                        $seoData = [
                            'seo_title' => $titles[$language] ?? '',
                            'seo_description' => $descriptions[$language] ?? '',
                            'seo_keywords' => is_array($keywords[$language] ?? []) ? implode(', ', $keywords[$language]) : '',
                            'canonical_url' => $seoSettings->canonical_url ?? ''
                        ];
                    }
                    
                    return response()->json([
                        'success' => true,
                        'seoData' => $seoData
                    ]);
                })->name('seo-data');
                
            });
    });