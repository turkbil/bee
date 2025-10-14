<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Bu migration HEM central HEM tenant veritabanlarında çalışacak
     */
    public function up(): void
    {
        Schema::create('shop_product_chat_placeholders', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->unique()->comment('Shop product ID');
            $table->json('conversation_json')->comment('AI-generated placeholder conversation');
            $table->timestamp('generated_at')->nullable()->comment('When placeholder was generated');
            $table->timestamps();

            // Index for faster lookups
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_product_chat_placeholders');
    }
};
