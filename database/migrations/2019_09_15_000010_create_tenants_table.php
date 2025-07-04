<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('tenancy_db_name')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('central')->default(false);
            $table->unsignedBigInteger('theme_id')->default(1)->index();
            $table->string('admin_default_locale', 10)->default('tr'); // Admin panel varsayılan dili
            $table->string('tenant_default_locale', 10)->default('tr'); // Tenant site varsayılan dili
            $table->foreign('theme_id')->references('theme_id')->on('themes');
            $table->json('data')->nullable();
            
            // AI Token Sistemi
            $table->unsignedInteger('ai_tokens_balance')->default(0); // Mevcut token bakiyesi
            $table->unsignedInteger('ai_tokens_used_this_month')->default(0); // Bu ay kullanılan tokenlar
            $table->unsignedInteger('ai_monthly_token_limit')->default(0); // Aylık token limiti
            $table->boolean('ai_enabled')->default(false); // AI kullanımı aktif mi?
            $table->timestamp('ai_monthly_reset_at')->nullable(); // Son aylık sıfırlama tarihi
            $table->timestamp('ai_last_used_at')->nullable(); // Son AI kullanım tarihi
            
            $table->timestamps();
            
            // İlave indeksler eklendi
            $table->index('title');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('admin_default_locale');
            $table->index('tenant_default_locale');
            $table->index(['ai_enabled']);
            $table->index(['ai_monthly_reset_at']);
            $table->index(['ai_last_used_at']);
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'central'], 'tenants_active_central_idx');
            $table->index(['theme_id', 'is_active'], 'tenants_theme_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}