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
        Schema::create('module_tenant_settings', function (Blueprint $table) {
            $table->id();
            $table->string('module_name'); // portfolio, page, announcement etc.
            $table->json('settings'); // {"slugs": {"index": "portfolios", "show": "portfolio"}, "display_name": "Projelerim", "other_settings": {}}
            $table->json('title')->nullable(); // Title eklendi
            $table->timestamps();
            
            $table->unique('module_name');
            $table->index('module_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_tenant_settings');
    }
};