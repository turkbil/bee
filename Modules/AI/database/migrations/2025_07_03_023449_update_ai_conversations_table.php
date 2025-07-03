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
        Schema::table('ai_conversations', function (Blueprint $table) {
            // Test sistemi için yeni alanlar
            $table->string('type')->default('chat')->after('title'); // chat, feature_test, admin_chat
            $table->string('feature_name')->nullable()->after('type'); // test edilen özellik adı
            $table->boolean('is_demo')->default(false)->after('feature_name'); // demo test mi gerçek mi
            $table->unsignedBigInteger('tenant_id')->nullable()->after('user_id'); // hangi tenant
            $table->integer('total_tokens_used')->default(0)->after('prompt_id'); // toplam token kullanımı
            $table->json('metadata')->nullable()->after('total_tokens_used'); // ek bilgiler
            $table->string('status')->default('active')->after('metadata'); // active, archived, deleted
            
            // Indexler
            $table->index('type');
            $table->index('feature_name');
            $table->index('tenant_id');
            $table->index('status');
            $table->index(['type', 'created_at'], 'ai_conversations_type_created_idx');
            $table->index(['tenant_id', 'created_at'], 'ai_conversations_tenant_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->dropIndex(['type', 'created_at']);
            $table->dropIndex(['tenant_id', 'created_at']);
            $table->dropIndex('type');
            $table->dropIndex('feature_name');
            $table->dropIndex('tenant_id');
            $table->dropIndex('status');
            
            $table->dropColumn([
                'type',
                'feature_name', 
                'is_demo',
                'tenant_id',
                'total_tokens_used',
                'metadata',
                'status'
            ]);
        });
    }
};
