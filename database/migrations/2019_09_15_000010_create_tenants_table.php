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
            $table->string('admin_default_language', 10)->default('tr'); // System language reference
            $table->foreign('theme_id')->references('theme_id')->on('themes');
            $table->json('data')->nullable();
            $table->timestamps();
            
            // İlave indeksler eklendi
            $table->index('title');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('admin_default_language');
            
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