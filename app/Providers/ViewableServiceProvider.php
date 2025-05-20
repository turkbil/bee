<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Events\TenantCreated;
use Stancl\Tenancy\Tenancy;

class ViewableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Tenant oluşturulduğunda views tablosunu oluşturmak için event dinleyicisi
        Event::listen(TenantCreated::class, function (TenantCreated $event) {
            $tenant = $event->tenant;
            
            // Tenant veritabanına geçiş yap
            tenancy()->initialize($tenant);
            
            // Views tablosunu oluştur
            if (!$this->viewsTableExists()) {
                $this->createViewsTable();
            }
            
            // Merkezi veritabanına geri dön
            tenancy()->end();
        });

        // Tenant bağlamına geçildiğinde eloquent-viewable konfigürasyonunu güncelle
        Event::listen(TenancyInitialized::class, function (TenancyInitialized $event) {
            // Tenant bağlamında çalışırken, eloquent-viewable'ın tenant veritabanını kullanmasını sağla
            config(['eloquent-viewable.models.view.connection' => config('database.default')]);
        });
    }

    /**
     * Views tablosunun var olup olmadığını kontrol et
     */
    protected function viewsTableExists(): bool
    {
        return \Illuminate\Support\Facades\Schema::hasTable('views');
    }

    /**
     * Views tablosunu oluştur
     */
    protected function createViewsTable(): void
    {
        \Illuminate\Support\Facades\Schema::create('views', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('viewable');
            $table->text('visitor')->nullable();
            $table->string('collection')->nullable();
            $table->timestamp('viewed_at')->useCurrent();
        });
    }
}
