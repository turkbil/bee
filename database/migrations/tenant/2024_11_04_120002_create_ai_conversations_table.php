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
        Schema::create('ai_conversations', function (Blueprint $table) {
            // Birincil anahtar
            $table->id()
                ->comment('Sohbet ID - Her sohbet oturumu için benzersiz');

            // İlişkiler
            $table->unsignedInteger('tenant_id')
                ->comment('Hangi tenant (örn: 2=ixtif.com)');

            $table->unsignedBigInteger('flow_id')
                ->comment('Hangi akış kullanılıyor - tenant_conversation_flows tablosundan');

            // Durum takibi
            $table->string('current_node_id', 50)->nullable()
                ->comment('Şu anda hangi node\'da - Akış içinde konum (örn: "node_greeting_1")');

            $table->string('session_id', 100)->unique()
                ->comment('Browser session ID - Her ziyaretçi için unique (cookie/localStorage)');

            $table->unsignedBigInteger('user_id')->nullable()
                ->comment('Kayıtlı kullanıcı ID - Varsa users tablosundan, yoksa NULL (guest)');

            // Sohbet verisi
            $table->json('context_data')->nullable()
                ->comment('Sohbet sırasında toplanan veriler - Telefon, email, tercihler vb. JSON formatında');

            $table->json('state_history')->nullable()
                ->comment('Node geçiş geçmişi - Hangi node\'lardan geçti, ne zaman, JSON array [{node_id, timestamp, success}]');

            // Zaman damgaları
            $table->timestamps();

            // İndeksler
            $table->index('session_id')
                ->comment('Session ile hızlı erişim - Her mesajda kullanılır');

            $table->index(['tenant_id', 'flow_id'])
                ->comment('Tenant akış istatistikleri için');

            // Foreign keys
            $table->foreign('flow_id')
                ->references('id')
                ->on('tenant_conversation_flows')
                ->onDelete('cascade')
                ->comment('Akış silinirse o akışın sohbetlerini sil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
