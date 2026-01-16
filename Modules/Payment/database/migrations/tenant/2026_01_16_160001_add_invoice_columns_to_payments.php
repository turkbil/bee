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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->after('payment_number')->unique();
            }
            if (!Schema::hasColumn('payments', 'invoice_path')) {
                $table->string('invoice_path')->nullable()->after('invoice_number');
            }
            if (!Schema::hasColumn('payments', 'invoice_uploaded_at')) {
                $table->timestamp('invoice_uploaded_at')->nullable();
            }
            if (!Schema::hasColumn('payments', 'invoice_uploaded_by')) {
                $table->unsignedBigInteger('invoice_uploaded_by')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $columns = ['invoice_number', 'invoice_path', 'invoice_uploaded_at', 'invoice_uploaded_by'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
