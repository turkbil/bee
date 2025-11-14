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
        Schema::table('blogs', function (Blueprint $table) {
            $table->json('faq_data')->nullable()->after('body')->comment('FAQ Schema data: [{"question":{"en":"...","tr":"..."},"answer":{"en":"...","tr":"..."}}]');
            $table->json('howto_data')->nullable()->after('faq_data')->comment('HowTo Schema data: {"name":{"en":"..."},"description":{"en":"..."},"steps":[{"name":{"en":"..."},"text":{"en":"..."}}]}');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['faq_data', 'howto_data']);
        });
    }
};
