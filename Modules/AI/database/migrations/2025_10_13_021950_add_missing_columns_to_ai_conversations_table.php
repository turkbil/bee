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
            // feature_slug: AI özelliğinin slug'ı (shop-assistant, seo-analyzer, etc.)
            // Kullanım: Konuşmanın hangi AI özelliği için olduğunu belirler
            // Örnek: 'shop-assistant', 'content-generator', 'translation-service'
            $table->string('feature_slug')->nullable()->after('feature_name');

            // context_data: Konuşma bağlamı (IP, user agent, locale, ürün/kategori bilgisi)
            // Kullanım: PublicAIController'da konuşma oluştururken context bilgilerini saklar
            // Örnek: {"ip":"127.0.0.1","user_agent":"Chrome","locale":"tr","product_id":266}
            $table->json('context_data')->nullable()->after('metadata');

            // is_active: Konuşmanın aktif olup olmadığı (aktif/arşivlenmiş)
            // Kullanım: Aktif konuşmaları filtrelemek için (scopeActive)
            // Örnek: true = aktif, false = arşivlenmiş veya silinmiş
            $table->boolean('is_active')->default(true)->after('is_demo');

            // last_message_at: Son mesajın gönderildiği zaman
            // Kullanım: Konuşmaları son mesaja göre sıralamak, eski konuşmaları bulmak için
            // Örnek: Her yeni mesajda otomatik güncellenir (AIMessage model event)
            $table->timestamp('last_message_at')->nullable()->after('total_tokens_used');

            // message_count: Konuşmadaki toplam mesaj sayısı
            // Kullanım: Performans için cache, mesaj sayısı göstermek için
            // Örnek: Her yeni mesajda +1 artar (AIMessage model event ile)
            $table->integer('message_count')->default(0)->after('last_message_at');

            // Add indexes for performance
            $table->index('feature_slug'); // Feature'a göre filtreleme için
            $table->index('is_active'); // Aktif konuşmaları hızlı bulmak için
            $table->index('last_message_at'); // Son mesaja göre sıralama için
        });

        // user_id: NULL yapılıyor - Guest kullanıcılar için (giriş yapmamış ziyaretçiler)
        // Kullanım: Guest user chat'lerde user_id = null, authenticated user'larda user_id dolu
        // Örnek: Shop assistant guest kullanıcılara açık, user_id = null
        DB::statement('ALTER TABLE ai_conversations MODIFY user_id BIGINT UNSIGNED NULL');

        // title: NULL yapılıyor - İlk mesajdan otomatik oluşturulacak
        // Kullanım: Eğer title boşsa, ilk kullanıcı mesajından otomatik üretilir
        // Örnek: "Merhaba, forklift hakkında bilgi..." → "Merhaba, forklift hakkın..."
        DB::statement('ALTER TABLE ai_conversations MODIFY title VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->dropIndex(['feature_slug']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['last_message_at']);

            $table->dropColumn([
                'feature_slug',
                'context_data',
                'is_active',
                'last_message_at',
                'message_count',
            ]);
        });
    }
};
