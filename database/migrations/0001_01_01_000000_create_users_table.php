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
            $table->timestamp('email_verified_at')->nullable();
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

            // Corporate Accounts
            $table->boolean('is_corporate')->default(false);
            $table->string('corporate_code')->nullable()->unique();
            $table->foreignId('parent_user_id')->nullable()->constrained('users')->onDelete('set null');

            // Dil & Tercihler
            $table->string('admin_locale', 10)->nullable()->comment('Admin panel dil tercihi');
            $table->string('tenant_locale', 5)->nullable()->comment('Tenant site dil tercihi');
            $table->json('dashboard_preferences')->nullable()->comment('Dashboard tercihleri');

            // Sistem
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // İndeksler - Performans optimizasyonu
            $table->index('admin_locale');
            $table->index('tenant_locale');
            $table->index('deleted_at');
            $table->index(['is_active', 'last_login_at'], 'users_active_last_login_idx');
            $table->index(['email_verified_at', 'is_active'], 'users_verified_active_idx');
            $table->index(['deleted_at', 'is_active'], 'users_deleted_active_idx');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable()->index();
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