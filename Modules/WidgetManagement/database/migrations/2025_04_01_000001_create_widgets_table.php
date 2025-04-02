<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Merkezi veritabanında çalışacak
     */
    public function getConnection()
    {
        return config('database.default');
    }
    
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type', 50)->index(); // static, dynamic, module, content
            $table->json('module_ids')->nullable();
            $table->longText('content_html')->nullable();
            $table->longText('content_css')->nullable();
            $table->json('css_files')->nullable();
            $table->longText('content_js')->nullable();
            $table->json('js_files')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('has_items')->default(false)->index();
            $table->json('item_schema')->nullable();
            $table->json('settings_schema')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_core')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};