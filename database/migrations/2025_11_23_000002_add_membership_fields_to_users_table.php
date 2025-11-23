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
        Schema::table('users', function (Blueprint $table) {
            // Device management
            if (!Schema::hasColumn('users', 'device_limit')) {
                $table->integer('device_limit')->nullable()->after('remember_token');
            }

            // Approval system
            if (!Schema::hasColumn('users', 'is_approved')) {
                $table->boolean('is_approved')->default(true)->after('device_limit');
            }

            // Security - failed login tracking
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->integer('failed_login_attempts')->default(0)->after('is_approved');
            }

            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            }

            // Two-factor authentication
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('locked_until');
            }

            if (!Schema::hasColumn('users', 'two_factor_phone')) {
                $table->string('two_factor_phone')->nullable()->after('two_factor_enabled');
            }

            // Corporate accounts
            if (!Schema::hasColumn('users', 'is_corporate')) {
                $table->boolean('is_corporate')->default(false)->after('two_factor_phone');
            }

            if (!Schema::hasColumn('users', 'corporate_code')) {
                $table->string('corporate_code')->nullable()->unique()->after('is_corporate');
            }

            if (!Schema::hasColumn('users', 'parent_user_id')) {
                $table->foreignId('parent_user_id')->nullable()->after('corporate_code')
                    ->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('users', 'parent_user_id')) {
                $table->dropForeign(['parent_user_id']);
                $table->dropColumn('parent_user_id');
            }

            $columns = [
                'device_limit',
                'is_approved',
                'failed_login_attempts',
                'locked_until',
                'two_factor_enabled',
                'two_factor_phone',
                'is_corporate',
                'corporate_code',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
