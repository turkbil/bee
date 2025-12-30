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
        Schema::table('muzibu_songs', function (Blueprint $table) {
            // HLS Streaming alanları (hls_path zaten var, sadece eksikleri ekle)
            if (!Schema::hasColumn('muzibu_songs', 'hls_path')) {
                $table->string('hls_path')->nullable()->after('file_path')
                    ->comment('HLS .m3u8 playlist dosya yolu');
            }

            if (!Schema::hasColumn('muzibu_songs', 'encryption_key')) {
                $table->string('encryption_key', 32)->nullable()->after('hls_path')
                    ->comment('AES-128 encryption key (16 byte hex)');
            }

            if (!Schema::hasColumn('muzibu_songs', 'is_encrypted')) {
                $table->boolean('is_encrypted')->default(false)->after('encryption_key')
                    ->comment('HLS parçaları encrypt mi?');
            }

            if (!Schema::hasColumn('muzibu_songs', 'hls_converted_at')) {
                $table->timestamp('hls_converted_at')->nullable()->after('is_encrypted')
                    ->comment('HLS conversion zamanı');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_songs', function (Blueprint $table) {
            $table->dropColumn(['hls_path', 'encryption_key', 'is_encrypted', 'hls_converted_at']);
        });
    }
};
