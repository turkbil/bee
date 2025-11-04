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
        Schema::create('ai_tenant_directives', function (Blueprint $table) {
            // Birincil anahtar
            $table->id()
                ->comment('Directive ID - Benzersiz tanımlayıcı');

            // Tenant ilişkisi
            $table->unsignedInteger('tenant_id')
                ->comment('Hangi tenant (örn: 2=ixtif.com)');

            // Directive bilgileri
            $table->string('directive_key', 100)
                ->comment('Ayar anahtarı - Kod içinde kullanılan isim (örn: "greeting_style", "max_products")');

            $table->text('directive_value')
                ->comment('Ayar değeri - String, sayı, JSON olabilir (örn: "friendly", "5", "true")');

            $table->enum('directive_type', ['string', 'integer', 'boolean', 'json', 'array'])->default('string')
                ->comment('Değer tipi - Kod tarafında nasıl parse edileceğini belirler');

            // Kategorileme
            $table->string('category', 50)->default('general')
                ->comment('Kategori - Ayarları gruplamak için (general, behavior, pricing, contact, display, lead)');

            $table->string('description', 255)->nullable()
                ->comment('Açıklama - Admin için bilgi, bu ayar ne işe yarar');

            // Durum
            $table->boolean('is_active')->default(true)
                ->comment('Aktif mi? 1=kullanımda, 0=devre dışı (sadece aktif olanlar okunur)');

            // Zaman damgaları
            $table->timestamps();

            // Kısıtlamalar
            $table->unique(['tenant_id', 'directive_key'], 'uk_tenant_key')
                ->comment('Aynı tenant içinde aynı key tekrar edemez - Her ayar unique');

            $table->index(['tenant_id', 'category'])
                ->comment('Kategoriye göre hızlı filtreleme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_tenant_directives');
    }
};
