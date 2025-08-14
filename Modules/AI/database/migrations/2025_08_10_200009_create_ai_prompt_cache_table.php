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
        Schema::create('ai_prompt_cache', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key', 255)->unique();
            $table->unsignedBigInteger('feature_id')->nullable();
            $table->string('input_hash', 64);
            $table->text('prompt_text');
            $table->text('response_text')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('hit_count')->default(0);
            $table->timestamp('last_accessed')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // Index'ler
            $table->index(['cache_key', 'expires_at'], 'idx_cache_key_expires');
            $table->index(['feature_id', 'input_hash'], 'idx_feature_hash');
            $table->index(['last_accessed'], 'idx_last_accessed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_prompt_cache');
    }
};