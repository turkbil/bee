<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Şablon anahtarı: welcome, trial_ending vb.');
            $table->string('name')->comment('Görünen ad');
            $table->json('subject')->comment('Mail konusu (çok dilli)');
            $table->json('content')->comment('Mail içeriği HTML (çok dilli)');
            $table->json('variables')->nullable()->comment('Kullanılabilir değişkenler');
            $table->string('category')->default('system')->comment('Kategori: auth, payment, subscription, corporate');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_templates');
    }
};
