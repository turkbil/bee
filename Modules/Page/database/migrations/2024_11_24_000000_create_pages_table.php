<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id('page_id');                       // Özel primary key
            $table->unsignedBigInteger('tenant_id');     // Tenancy için tenant ID
            $table->string('title');                     // Sayfa başlığı
            $table->string('slug')->unique();            // URL slug
            $table->text('body')->nullable();            // Sayfa içeriği
            $table->string('css')->nullable();           // CSS alanı
            $table->string('js')->nullable();            // JS alanı
            $table->string('metakey')->nullable();       // Meta anahtar kelimeler
            $table->string('metadesc')->nullable();      // Meta açıklama
            $table->boolean('is_active')->default(true); // Aktiflik durumu
            $table->timestamps();                        // created_at ve updated_at
            $table->softDeletes();                       // deleted_at

            // Yabancı anahtarlar
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
