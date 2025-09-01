<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->foreignId('menu_id')->constrained('menus', 'menu_id')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('menu_items', 'item_id')->onDelete('cascade');
            $table->json('title')->comment('Çoklu dil başlık: {"tr": "Anasayfa", "en": "Home"}');
            $table->enum('url_type', ['internal', 'external', 'module'])->default('internal');
            $table->json('url_data')->comment('URL verileri: {"page_id": 1} veya {"url": "https://..."}');
            $table->string('target')->default('_self')->comment('_self, _blank');
            $table->string('icon')->nullable()->comment('Menü ikonu (FontAwesome class)');
            $table->boolean('is_active')->default(true)->index();
            $table->string('visibility')->default('public')->comment('public, logged_in, guest');
            $table->integer('sort_order')->default(0)->comment('Parent içinde sıralama');
            $table->tinyInteger('depth_level')->default(0)->comment('Menü derinlik seviyesi: 0,1,2,3');
            $table->timestamps();
            $table->softDeletes(); // Soft delete desteği
            
            // İndeksler
            $table->index(['menu_id', 'parent_id', 'sort_order']);
            $table->index(['parent_id', 'sort_order']);
            $table->index(['is_active', 'sort_order']);
            $table->index(['depth_level', 'sort_order']);
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};