<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_workflow_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('node_key')->unique()->comment('Unique identifier: ai_response, condition, etc.');
            $table->string('node_class')->comment('Full PHP class path');
            $table->json('node_name')->comment('Multilingual name: {"en":"AI Response","tr":"AI Yanıtı"}');
            $table->json('node_description')->nullable()->comment('Multilingual description');
            $table->string('category')->default('common')->comment('common, ecommerce, communication, etc.');
            $table->string('icon')->default('fa-circle')->comment('FontAwesome icon class');
            $table->integer('order')->default(0)->comment('Display order in palette');
            $table->boolean('is_global')->default(true)->comment('Available to all tenants');
            $table->boolean('is_active')->default(true)->comment('Active/Inactive');
            $table->json('tenant_whitelist')->nullable()->comment('Array of tenant IDs if not global');
            $table->json('default_config')->nullable()->comment('Default configuration for new instances');
            $table->timestamps();

            $table->index(['category', 'is_active', 'order']);
            $table->index('is_global');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_workflow_nodes');
    }
};
