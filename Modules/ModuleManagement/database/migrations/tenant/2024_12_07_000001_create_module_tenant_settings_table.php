<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('module_tenant_settings', function (Blueprint $table) {
            $table->id();
            $table->string('module_name')->index();
            $table->string('setting_key')->index();
            $table->json('setting_value');
            $table->string('setting_type')->default('string'); // string, array, boolean, integer
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_system')->default(false); // Sistem ayarı mı, kullanıcı ayarı mı
            $table->timestamps();
            
            // Composite index
            $table->unique(['module_name', 'setting_key'], 'module_setting_unique');
            $table->index(['module_name', 'is_active']);
            $table->index(['setting_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_tenant_settings');
    }
};