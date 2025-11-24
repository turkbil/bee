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
        if (Schema::hasTable('muzibu_corporate_accounts')) {
            return;
        }
        Schema::create('muzibu_corporate_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Bu kaydın sahibi user
            $table->unsignedBigInteger('parent_id')->nullable(); // NULL = kurum, değer = üye
            $table->string('corporate_code', 20)->nullable()->unique(); // Davet kodu (sadece kurum)
            $table->string('company_name')->nullable(); // Şirket adı (sadece kurum)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('muzibu_corporate_accounts')->onDelete('cascade');
            $table->index('parent_id');
            $table->index('corporate_code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tenant-aware: Only drop if table exists
        Schema::dropIfExists('muzibu_corporate_accounts');
    }
};
