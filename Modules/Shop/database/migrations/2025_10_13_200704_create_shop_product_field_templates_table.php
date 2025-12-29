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
        Schema::create('shop_product_field_templates', function (Blueprint $table) {
            $table->id('template_id');
            $table->string('name', 191)->unique(); // "Kitap Ürünü", "Elektronik Cihaz"
            $table->text('description')->nullable();
            $table->json('fields'); // [{"name": "author", "type": "input", "order": 1}, ...]
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_product_field_templates');
    }
};
