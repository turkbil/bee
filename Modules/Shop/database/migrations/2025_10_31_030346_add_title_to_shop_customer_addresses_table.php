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
        Schema::table('shop_customer_addresses', function (Blueprint $table) {
            // Title field for address nickname ("Ev Adresim", "İş Adresi", etc.) - REQUIRED
            $table->string('title')->after('address_id')->comment('Adres başlığı (Ev, İş, vb.) - ZORUNLU');

            // Personal info now nullable (collected in checkout contact, not in address)
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_customer_addresses', function (Blueprint $table) {
            $table->dropColumn('title');

            // Revert to required
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
        });
    }
};
