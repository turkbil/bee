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
        Schema::table('ai_token_usage', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('tenant_id');
            $table->string('model')->default('gpt-3.5-turbo')->after('usage_type');
            $table->unsignedInteger('prompt_tokens')->default(0)->after('tokens_used');
            $table->unsignedInteger('completion_tokens')->default(0)->after('prompt_tokens');
            $table->string('purpose')->nullable()->after('model');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['model', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_token_usage', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['model', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropColumn(['user_id', 'model', 'prompt_tokens', 'completion_tokens', 'purpose']);
        });
    }
};
