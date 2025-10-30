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
        Schema::table('shop_orders', function (Blueprint $table) {
            // Address relations (optional reference)
            $table->foreignId('contact_address_id')->nullable()->after('tenant_id')->constrained('customer_addresses')->nullOnDelete();
            $table->foreignId('billing_address_id')->nullable()->after('contact_address_id')->constrained('customer_addresses')->nullOnDelete();
            $table->foreignId('shipping_address_id')->nullable()->after('billing_address_id')->constrained('customer_addresses')->nullOnDelete();

            // Billing snapshot (customer_* already exists for contact info)
            $table->string('billing_company')->nullable()->after('customer_tax_number');
            $table->string('billing_tax_office')->nullable()->after('billing_company');
            $table->string('billing_tax_number', 50)->nullable()->after('billing_tax_office');
            $table->text('billing_address')->nullable()->after('billing_tax_number');
            $table->string('billing_city', 100)->nullable()->after('billing_address');
            $table->string('billing_district', 100)->nullable()->after('billing_city');
            $table->string('billing_postal_code', 10)->nullable()->after('billing_district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['contact_address_id']);
            $table->dropForeign(['billing_address_id']);
            $table->dropForeign(['shipping_address_id']);

            // Drop columns
            $table->dropColumn([
                'contact_address_id',
                'billing_address_id',
                'shipping_address_id',
                'billing_company',
                'billing_tax_office',
                'billing_tax_number',
                'billing_address',
                'billing_city',
                'billing_district',
                'billing_postal_code',
            ]);
        });
    }
};
