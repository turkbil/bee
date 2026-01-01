<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('muzibu_corporate_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('muzibu_corporate_accounts', 'spot_enabled')) {
                $table->boolean('spot_enabled')->default(true)->after('is_active')->comment('Spot sistemi açık mı?');
            }
            if (!Schema::hasColumn('muzibu_corporate_accounts', 'spot_songs_between')) {
                $table->unsignedInteger('spot_songs_between')->default(10)->after('spot_enabled')->comment('Kaç şarkıda bir spot çalsın?');
            }
            if (!Schema::hasColumn('muzibu_corporate_accounts', 'spot_current_index')) {
                $table->unsignedInteger('spot_current_index')->default(0)->after('spot_songs_between')->comment('Rotation index');
            }
            if (!Schema::hasColumn('muzibu_corporate_accounts', 'spot_is_paused')) {
                $table->boolean('spot_is_paused')->default(false)->after('spot_current_index')->comment('Bu şube için spot durduruldu mu?');
            }
        });
    }

    public function down(): void
    {
        Schema::table('muzibu_corporate_accounts', function (Blueprint $table) {
            $table->dropColumn(['spot_enabled', 'spot_songs_between', 'spot_current_index', 'spot_is_paused']);
        });
    }
};
