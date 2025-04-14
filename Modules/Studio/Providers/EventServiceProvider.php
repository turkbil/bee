<?php

namespace Modules\Studio\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Modules\Studio\Events\StudioEditorOpened' => [
            'Modules\Studio\Listeners\StudioEditorOpenedListener',
        ],
        'Modules\Studio\Events\StudioContentSaved' => [
            'Modules\Studio\Listeners\StudioContentSavedListener',
        ],
        'Modules\Studio\Events\StudioWidgetUpdated' => [
            'Modules\Studio\Listeners\StudioWidgetUpdatedListener',
        ],
        'Modules\Studio\Events\StudioThemeChanged' => [
            'Modules\Studio\Listeners\StudioThemeChangedListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        
        // Modül sistemi ile entegrasyon için olaylar
        $this->registerModuleIntegrationEvents();
    }
    
    /**
     * Diğer modüllerle entegrasyon için olayları kaydeder
     */
    protected function registerModuleIntegrationEvents(): void
    {
        // Page modülü ile entegrasyon
        if (class_exists('\Modules\Page\App\Models\Page')) {
            // Page düzenleme sayfasında Studio düğmesini göster
            $this->app['events']->listen('page.edit', function ($page) {
                view()->composer('page::admin.livewire.page-manage-component', function ($view) {
                    $view->with('studioEnabled', true);
                });
            });
        }
        
        // Gelecekteki diğer modül entegrasyonları
    }
}