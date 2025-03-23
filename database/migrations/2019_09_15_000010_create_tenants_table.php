<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('tenancy_db_name')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('central')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
            
            // İlave indeksler eklendi
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}