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
            // model: Kullanılan AI modelinin adı (model_used ile aynı, yeni kod için)
            // Kullanım: Yeni kod 'model' kullanır, eski kod 'model_used' kullanır
            // Örnek: 'gpt-4o', 'claude-3-sonnet', 'deepseek-chat'
            // Her ikisi de desteklenir, accessor/mutator ile sync edilir
            $table->string('model')->nullable()->after('model_used');

            // tokens_used: Toplam kullanılan token (tokens ile aynı, yeni kod için)
            // Kullanım: prompt_tokens + completion_tokens = tokens_used
            // Örnek: 150 prompt + 350 completion = 500 total tokens
            // Her ikisi de desteklenir, accessor ile hesaplanır
            $table->integer('tokens_used')->default(0)->after('tokens');

            // context_data: Mesaja özel bağlam verisi
            // Kullanım: Mesaj gönderilirken hangi sayfa, ürün, kategori gibi bilgiler
            // Örnek: {"product_id":266,"category_id":null,"page":"shop-detail"}
            // PublicAIController'dan gelen context bilgileri burada saklanır
            $table->json('context_data')->nullable()->after('metadata');

            // Index for token-based queries (maliyet analizi için)
            $table->index('tokens_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_messages', function (Blueprint $table) {
            $table->dropIndex(['tokens_used']);

            $table->dropColumn([
                'model',
                'tokens_used',
                'context_data',
            ]);
        });
    }
};
