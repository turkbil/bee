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
            // Ana Alanlar
            $table->id();
            $table->string('title');
            $table->string('tenancy_db_name')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('central')->default(false);
            
            // Yetkili Bilgileri (Kritik Alanlar)
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // Yapılandırma
            $table->unsignedBigInteger('theme_id')->default(1);
            $table->string('admin_default_locale', 10)->default('tr');
            $table->string('tenant_default_locale', 10)->default('tr');
            $table->json('data')->nullable();
            
            // AI Sistemi (Basit - Foreign Key Yok)
            $table->unsignedInteger('ai_credits_balance')->default(0);
            $table->timestamp('ai_last_used_at')->nullable();
            $table->unsignedBigInteger('tenant_ai_provider_id')->nullable();
            $table->unsignedBigInteger('tenant_ai_provider_model_id')->nullable();
            
            $table->timestamps();
            
            // Foreign Keys  
            $table->foreign('theme_id')->references('theme_id')->on('themes');
            
            // Performans İndeksleri
            $table->index(['is_active', 'central']);
            $table->index(['theme_id', 'is_active']);
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