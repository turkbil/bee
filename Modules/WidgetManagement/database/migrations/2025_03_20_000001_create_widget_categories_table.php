<?php
// Modules/WidgetManagement/database/migrations/2025_03_20_000001_create_widget_categories_table.php

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
    
    public function up(): void
    {
        if (Schema::hasTable('widget_categories')) {
            return;
        }

        Schema::create('widget_categories', function (Blueprint $table) {
            $table->id('widget_category_id');
            $table->string('title', 255)->index();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('has_subcategories')->default(false); // Bu sütun eksikti, eklendi
            $table->softDeletes();
            $table->timestamps();
            
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('parent_id');
            
            // Yabancı anahtar kısıtlaması
            $table->foreign('parent_id')
                  ->references('widget_category_id')
                  ->on('widget_categories')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('widget_categories');
    }
};