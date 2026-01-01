<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('muzibu_corporate_spot_plays')) {
            return;
        }

        Schema::create('muzibu_corporate_spot_plays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spot_id')->constrained('muzibu_corporate_spots')->cascadeOnDelete();
            $table->foreignId('corporate_account_id')->constrained('muzibu_corporate_accounts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->comment('IPv4 or IPv6 address');
            $table->string('user_agent', 255)->nullable()->comment('Browser user agent');
            $table->string('device_type', 255)->nullable()->comment('mobile, tablet, desktop');
            $table->string('browser', 50)->nullable();
            $table->string('platform', 50)->nullable();
            $table->unsignedInteger('listened_duration')->nullable()->comment('Kaç saniye dinledi');
            $table->boolean('was_skipped')->default(false)->comment('Atlandı mı?');
            $table->string('source_type', 50)->nullable()->comment('Kaynak tipi');
            $table->unsignedBigInteger('source_id')->nullable()->comment('Kaynak ID');
            $table->timestamps();
            $table->timestamp('ended_at')->nullable()->comment('Bitiş zamanı');

            // Indexes
            $table->index('spot_id');
            $table->index('corporate_account_id');
            $table->index('user_id');
            $table->index('ip_address');
            $table->index('created_at');
            $table->index('device_type');
            $table->index(['spot_id', 'created_at'], 'spot_plays_spot_created_idx');
            $table->index(['corporate_account_id', 'created_at'], 'spot_plays_corp_created_idx');
            $table->index(['user_id', 'created_at'], 'spot_plays_user_created_idx');
            $table->index(['user_id', 'browser', 'created_at'], 'spot_plays_user_browser_idx');
            $table->index(['user_id', 'ended_at'], 'spot_plays_user_ended_idx');
            $table->index(['source_type', 'source_id'], 'spot_plays_source_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_corporate_spot_plays');
    }
};
