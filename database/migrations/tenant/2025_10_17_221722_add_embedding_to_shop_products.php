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
        Schema::table('shop_products', function (Blueprint $table) {
            // Embedding vector (JSON - 1536 dimensions for OpenAI text-embedding-3-small)
            $table->json('embedding')->nullable()->after('body');

            // Embedding metadata
            $table->timestamp('embedding_generated_at')->nullable();
            $table->string('embedding_model', 50)->default('text-embedding-3-small');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropColumn(['embedding', 'embedding_generated_at', 'embedding_model']);
        });
    }
};
