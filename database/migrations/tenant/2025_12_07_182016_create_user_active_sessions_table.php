<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * User Active Sessions - Redis Session Tracking
 *
 * Bu tablo Redis session driver ile birlikte kullanilir.
 * Login/logout event'lerinde guncellenir.
 * DeviceService bu tabloyu kullanarak device limit kontrolu yapar.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_active_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id', 255)->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type', 20)->default('desktop'); // desktop, mobile, tablet
            $table->string('device_name', 100)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('platform', 50)->nullable();
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'last_activity']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_active_sessions');
    }
};
