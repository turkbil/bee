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
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_login_at')->nullable()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('admin_locale', 10)->nullable(); // Admin panel dil tercihi
            $table->string('tenant_locale', 5)->nullable(); // Tenant site dil tercihi
            $table->rememberToken();
            $table->timestamps();
            
            // Dil tercihleri için indeksler  
            $table->index('admin_locale');
            $table->index('tenant_locale');
            
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