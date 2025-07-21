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
            $table->unsignedBigInteger('ai_provider_id')->nullable()->after('message_id');
            $table->string('provider_name')->nullable()->after('ai_provider_id');
            $table->string('feature_slug')->nullable()->after('usage_type');
            $table->decimal('cost_multiplier', 8, 4)->default(1.0000)->after('reference_id');
            $table->json('response_metadata')->nullable()->after('cost_multiplier');
            
            // Foreign key
            $table->foreign('ai_provider_id')->references('id')->on('ai_providers')->onDelete('set null');
            
            // Indexes for better performance
            $table->index('ai_provider_id');
            $table->index('feature_slug');
            $table->index(['tenant_id', 'ai_provider_id']);
            $table->index(['used_at', 'ai_provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_token_usage', function (Blueprint $table) {
            $table->dropForeign(['ai_provider_id']);
            $table->dropIndex(['ai_provider_id']);
            $table->dropIndex(['feature_slug']);
            $table->dropIndex(['tenant_id', 'ai_provider_id']);
            $table->dropIndex(['used_at', 'ai_provider_id']);
            
            $table->dropColumn([
                'ai_provider_id',
                'provider_name', 
                'feature_slug',
                'cost_multiplier',
                'response_metadata'
            ]);
        });
    }
};
