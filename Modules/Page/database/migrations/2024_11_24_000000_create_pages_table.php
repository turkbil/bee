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
            $table->string('title', 255);                // Sayfa başlığı (255 karakter)
            $table->string('slug')->unique();            // URL slug
            $table->text('body')->nullable();            // Sayfa içeriği
            $table->text('css')->nullable();             // CSS alanı (text olarak)
            $table->text('js')->nullable();              // JS alanı (text olarak)
            $table->string('metakey', 255)->nullable();  // Meta anahtar kelimeler (255 karakter)
            $table->string('metadesc', 255)->nullable(); // Meta açıklama (255 karakter)
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