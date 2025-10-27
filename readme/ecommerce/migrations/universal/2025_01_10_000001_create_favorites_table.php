<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * UNIVERSAL FAVORITES SYSTEM
     * Polymorphic favorites for all models (Product, Post, Portfolio, etc.)
     */
    public function up(): void
    {
        if (Schema::hasTable('favorites')) {
            return;
        }

        Schema::create('favorites', function (Blueprint $table) {
            $table->id('favorite_id');

            // User relation
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relation (favoritable)
            $table->morphs('favoritable');  // favoritable_type, favoritable_id

            // Metadata
            $table->json('metadata')->nullable()->comment('Ek veriler: {"note":"...", "tags":[...], vs.}');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'favoritable_type', 'favoritable_id'], 'favorites_user_favoritable_idx');
            $table->unique(['user_id', 'favoritable_type', 'favoritable_id'], 'favorites_unique_idx');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
