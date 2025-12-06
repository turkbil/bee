<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop redundant fields:
     * - is_encrypted: Replaced by encryption_key IS NOT NULL check
     * - hls_converted: Replaced by hls_path IS NOT NULL check
     * - hls_converted_at: Unnecessary audit log
     */
    public function up(): void
    {
        Schema::table('muzibu_songs', function (Blueprint $table) {
            $table->dropColumn(['is_encrypted', 'hls_converted', 'hls_converted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_songs', function (Blueprint $table) {
            $table->boolean('is_encrypted')->default(false)->comment('DEPRECATED - Use encryption_key IS NOT NULL');
            $table->boolean('hls_converted')->default(false)->comment('DEPRECATED - Use hls_path IS NOT NULL');
            $table->timestamp('hls_converted_at')->nullable()->comment('DEPRECATED - Removed');
        });
    }
};
