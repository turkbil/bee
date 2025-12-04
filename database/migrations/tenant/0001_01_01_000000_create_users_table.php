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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Temel Bilgiler
            $table->string('name')->index();
            $table->string('surname')->nullable();
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->text('bio')->nullable();

            // Hesap Durumu & Aktivite
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_login_at')->nullable()->index();
            $table->timestamp('email_verified_at')->nullable()->index();
            $table->boolean('has_used_trial')->default(false)->comment('Kullanıcı trial kullandı mı? (Ömür boyu 1 kere)');

            // Güvenlik
            $table->string('password');
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            // Two-Factor Authentication
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_phone')->nullable();

            // Device Management
            $table->integer('device_limit')->nullable()->comment('Maksimum cihaz sayısı');

            // Approval System
            $table->boolean('is_approved')->default(true);

            // Dil & Tercihler
            $table->string('admin_locale', 10)->nullable()->comment('Admin panel language preference');
            $table->string('tenant_locale', 5)->nullable()->comment('Tenant site language preference');
            $table->json('dashboard_preferences')->nullable()->comment('Dashboard tercihleri');

            // Sistem
            $table->rememberToken()->index();
            $table->timestamps();
            $table->softDeletes();

            // İndeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('admin_locale');
            $table->index('tenant_locale');
            $table->index('deleted_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable()->index();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};