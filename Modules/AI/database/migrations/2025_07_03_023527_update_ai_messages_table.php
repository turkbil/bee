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
        Schema::table('ai_messages', function (Blueprint $table) {
            // Test sistemi için yeni alanlar
            $table->integer('prompt_tokens')->default(0)->after('tokens'); // gelen token sayısı
            $table->integer('completion_tokens')->default(0)->after('prompt_tokens'); // dönen token sayısı
            $table->string('model_used')->nullable()->after('completion_tokens'); // kullanılan AI modeli
            $table->integer('processing_time_ms')->default(0)->after('model_used'); // işlem süresi
            $table->json('metadata')->nullable()->after('processing_time_ms'); // ek bilgiler (istek/yanıt detayları)
            $table->string('message_type')->default('normal')->after('metadata'); // normal, test, system
            
            // Indexler
            $table->index('model_used');
            $table->index('message_type');
            $table->index(['conversation_id', 'message_type'], 'ai_messages_conversation_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_messages', function (Blueprint $table) {
            $table->dropIndex(['conversation_id', 'message_type']);
            $table->dropIndex('model_used');
            $table->dropIndex('message_type');
            
            $table->dropColumn([
                'prompt_tokens',
                'completion_tokens',
                'model_used',
                'processing_time_ms',
                'metadata',
                'message_type'
            ]);
        });
    }
};
