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
        Schema::create('tenant_conversation_flows', function (Blueprint $table) {
            // Birincil anahtar
            $table->id()
                ->comment('Akış ID - Benzersiz tanımlayıcı');

            // Tenant ilişkisi
            $table->unsignedInteger('tenant_id')
                ->comment('Hangi tenant (örn: 2=ixtif.com, 3=diğer)');

            // Akış bilgileri
            $table->string('flow_name', 255)
                ->comment('Akış adı - Admin panelde görünen isim (örn: "E-Ticaret Satış Akışı")');

            $table->text('flow_description')->nullable()
                ->comment('Akış açıklaması - Admin için bilgi notu, kullanıcı görmez');

            $table->json('flow_data')
                ->comment('Tüm akış yapısı: nodes (kutucuklar), edges (bağlantılar), positions - Drawflow JSON');

            $table->string('start_node_id', 50)
                ->comment('İlk çalışacak node ID - Akış buradan başlar (örn: "node_greeting_1")');

            // Durum kontrol
            $table->boolean('is_active')->default(true)
                ->comment('Aktif mi? 1=kullanımda, 0=devre dışı (sadece aktif olanlar çalışır)');

            $table->integer('priority')->default(0)
                ->comment('Öncelik - Birden fazla aktif flow varsa en düşük sayı çalışır (0 en yüksek öncelik)');

            // Audit bilgileri
            $table->unsignedBigInteger('created_by')->nullable()
                ->comment('Akışı oluşturan admin user ID - users tablosundan');

            $table->unsignedBigInteger('updated_by')->nullable()
                ->comment('Son güncelleyen admin user ID - users tablosundan');

            // Zaman damgaları
            $table->timestamps();

            // İndeksler (performans)
            $table->index(['tenant_id', 'is_active'])
                ->comment('Tenant aktif akış sorgusunu hızlandırır');

            $table->index(['tenant_id', 'priority'])
                ->comment('Öncelik sırasına göre seçim için - En düşük sayı önce');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_conversation_flows');
    }
};
