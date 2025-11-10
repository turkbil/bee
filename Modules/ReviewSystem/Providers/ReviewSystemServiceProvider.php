<?php

namespace Modules\ReviewSystem\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\ReviewSystem\App\Services\ReviewService;
use Modules\ReviewSystem\App\Http\Livewire\Admin\ReviewComponent;
use Modules\ReviewSystem\App\Http\Livewire\Admin\PendingReviewsComponent;
use Modules\ReviewSystem\App\Http\Livewire\Admin\ReviewStatisticsComponent;
use Modules\ReviewSystem\App\Http\Livewire\Admin\ReviewManageComponent;

class ReviewSystemServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'ReviewSystem';
    protected string $moduleNameLower = 'reviewsystem';

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));

        // Route'ları yükle
        $this->loadRoutesFrom(module_path($this->moduleName, 'routes/admin.php'));
        $this->loadRoutesFrom(module_path($this->moduleName, 'routes/api.php'));

        // Views'ları yükle
        $this->loadViewsFrom(module_path($this->moduleName, 'resources/views'), $this->moduleNameLower);

        // Livewire Component'leri kaydet
        Livewire::component('review-component', ReviewComponent::class);
        Livewire::component('pending-reviews-component', PendingReviewsComponent::class);
        Livewire::component('review-statistics-component', ReviewStatisticsComponent::class);
        Livewire::component('review-manage-component', ReviewManageComponent::class);
    }

    public function register(): void
    {
        $this->app->singleton(ReviewService::class, function ($app) {
            return new ReviewService();
        });
    }
}
