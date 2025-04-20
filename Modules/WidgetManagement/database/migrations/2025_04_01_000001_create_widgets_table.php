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
        if (!Schema::hasTable('widget_categories')) {
            throw new \Exception('widget_categories tablosu bulunamadı. Lütfen önce bu tabloyu oluşturun.');
        }

        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('widget_category_id')->nullable();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type', 50)->index();
            $table->string('file_path')->nullable(); // Eklenen sütun
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

            $table->foreign('widget_category_id')
                  ->references('widget_category_id')
                  ->on('widget_categories')
                  ->onDelete('set null');
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
