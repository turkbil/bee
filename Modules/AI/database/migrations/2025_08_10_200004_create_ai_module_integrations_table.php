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
        Schema::create('ai_module_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('module_name', 50);
            $table->enum('integration_type', ['button', 'modal', 'inline', 'bulk', 'api'])->default('button');
            $table->string('target_field', 100)->nullable()->comment('Hangi alan için');
            $table->string('target_action', 100)->comment('generate, optimize, translate, analyze');
            $table->json('button_config')->nullable()->comment('Buton ayarları');
            $table->json('modal_config')->nullable()->comment('Modal ayarları');
            $table->json('features_available')->comment('Bu modülde kullanılabilir feature_ids');
            $table->json('context_data')->nullable()->comment('Modül context bilgileri');
            $table->json('permissions')->nullable()->comment('Yetki gereksinimleri');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index'ler
            $table->unique(['module_name', 'target_field', 'target_action'], 'unique_module_field_action');
            $table->index(['module_name', 'is_active'], 'idx_module_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_module_integrations');
    }
};