<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation - Herhangi bir model'e favori eklenebilir
            $table->morphs('favoritable'); // favoritable_id + favoritable_type

            // User relation
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            // NOT: morphs() zaten favoritable_type + favoritable_id için index oluşturur
            $table->index('created_at');

            // Unique constraint - Bir kullanıcı aynı içeriği birden fazla favorilere ekleyemez
            $table->unique(['user_id', 'favoritable_id', 'favoritable_type'], 'favorites_unique_user_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
