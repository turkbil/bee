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
        Schema::create('ai_user_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('feature_id')->nullable();
            $table->string('preference_key', 100);
            $table->json('preference_value');
            $table->json('last_used_values')->nullable()->comment('Son kullanılan değerler');
            $table->integer('usage_count')->default(0);
            $table->json('favorite_prompts')->nullable();
            $table->json('custom_templates')->nullable();
            $table->timestamps();
            
            // Index'ler
            $table->unique(['user_id', 'feature_id', 'preference_key'], 'unique_user_feature_key');
            $table->index(['user_id', 'feature_id'], 'idx_user_feature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_user_preferences');
    }
};