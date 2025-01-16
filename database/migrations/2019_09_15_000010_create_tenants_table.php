<?php

declare (strict_types = 1);

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
            $table->id(); // unsignedBigInteger olarak tanımlanır.
            $table->timestamps();
            $table->softDeletes();                       // deleted_at sütunu eklenir
            $table->boolean('is_active')->default(true); // Tenant'ın aktif olup olmadığını belirten sütun
            $table->json('data')->nullable();            // Ek bilgiler için JSON formatında sütun
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
