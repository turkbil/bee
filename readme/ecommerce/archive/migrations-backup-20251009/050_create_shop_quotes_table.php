<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('shop_quotes')) {
            return;
        }

        Schema::create('shop_quotes', function (Blueprint $table) {
            // Primary Key
            $table->id('quote_id');

            // Relations
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi (null ise misafir)');

            // Quote Info
            $table->string('quote_number')->unique()->comment('Teklif numarası (QTE-2024-00001)');

            // Contact Info (for guest quotes)
            $table->string('contact_name')->nullable()->comment('İletişim adı');
            $table->string('contact_email')->nullable()->comment('İletişim e-posta');
            $table->string('contact_phone')->nullable()->comment('İletişim telefon');
            $table->string('company_name')->nullable()->comment('Şirket adı');

            // Status
            $table->enum('status', [
                'draft',            // Taslak
                'sent',             // Gönderildi
                'viewed',           // Görüntülendi
                'accepted',         // Kabul edildi
                'rejected',         // Reddedildi
                'expired',          // Süresi doldu
                'converted'         // Siparişe dönüştü
            ])->default('draft')->comment('Durum');

            // Totals
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Ara toplam (₺)');
            $table->decimal('discount_amount', 12, 2)->default(0)->comment('İndirim tutarı (₺)');
            $table->decimal('tax_amount', 12, 2)->default(0)->comment('Vergi tutarı (₺)');
            $table->decimal('shipping_cost', 10, 2)->default(0)->comment('Kargo ücreti (₺)');
            $table->decimal('total', 12, 2)->default(0)->comment('Toplam tutar (₺)');

            // Currency
            $table->string('currency', 3)->default('TRY')->comment('Para birimi (TRY, USD, EUR)');

            // Validity
            $table->timestamp('valid_until')->nullable()->comment('Geçerlilik tarihi');
            $table->integer('validity_days')->default(30)->comment('Geçerlilik süresi (gün)');

            // Terms & Notes
            $table->text('terms')->nullable()->comment('Şartlar ve koşullar');
            $table->text('notes')->nullable()->comment('Notlar');
            $table->text('internal_notes')->nullable()->comment('Dahili notlar (müşteri görmez)');

            // Assignment
            $table->foreignId('assigned_to_user_id')->nullable()->comment('Atanan satış temsilcisi ID');

            // Conversion
            $table->foreignId('converted_to_order_id')->nullable()->comment('Dönüştürülen sipariş ID');
            $table->timestamp('converted_at')->nullable()->comment('Siparişe dönüşme tarihi');

            // Tracking
            $table->timestamp('sent_at')->nullable()->comment('Gönderilme tarihi');
            $table->timestamp('viewed_at')->nullable()->comment('Görüntülenme tarihi');
            $table->integer('view_count')->default(0)->comment('Görüntülenme sayısı');
            $table->timestamp('accepted_at')->nullable()->comment('Kabul tarihi');
            $table->timestamp('rejected_at')->nullable()->comment('Red tarihi');
            $table->text('rejection_reason')->nullable()->comment('Red nedeni');

            // PDF
            $table->string('pdf_file')->nullable()->comment('PDF dosya yolu');

            // IP & Browser
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');

            // Additional Info
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('customer_id', 'idx_customer');
            $table->index('quote_number', 'idx_number');
            $table->index('status', 'idx_status');
            $table->index('assigned_to_user_id', 'idx_assigned');
            $table->index('converted_to_order_id', 'idx_converted');
            $table->index('valid_until', 'idx_valid_until');
            $table->index('created_at', 'idx_created');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur ama teklif kalır');

            $table->foreign('converted_to_order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ID null olur');
        })
        ->comment('Teklifler - Fiyat teklifi talepleri (B2B için önemli)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_quotes');
    }
};
