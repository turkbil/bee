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
            $table->string('name')->index();
            $table->string('email')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_login_at')->nullable()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('language', 5)->default('tr'); // Genel dil tercihi
            $table->string('admin_language_preference', 10)->nullable(); // System language preference
            $table->string('site_language_preference', 5)->nullable(); // Site içerik dil tercihi (SiteLanguage)
            $table->rememberToken();
            $table->timestamps();
            
            // Dil tercihleri için indeksler  
            $table->index('language');
            $table->index('admin_language_preference');
            $table->index('site_language_preference');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'last_login_at'], 'users_active_last_login_idx');
            $table->index(['email_verified_at', 'is_active'], 'users_verified_active_idx');
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