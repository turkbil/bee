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
        Schema::create('tenant_widgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('widget_id');
            $table->integer('order')->default(0)->index();
            $table->json('settings')->nullable();
            $table->string('display_title')->nullable(); // Yeni eklenen alan
            $table->boolean('is_custom')->default(false);
            $table->longText('custom_html')->nullable();
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_widgets');
    }
};