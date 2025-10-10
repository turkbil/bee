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
        if (Schema::hasTable('shop_service_requests')) {
            return;
        }

        Schema::create('shop_service_requests', function (Blueprint $table) {
            // Primary Key
            $table->id('service_request_id');

            // Relations
            $table->foreignId('customer_id')->comment('Müşteri ID - shop_customers ilişkisi');
            $table->foreignId('product_id')->nullable()->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('order_id')->nullable()->comment('Sipariş ID - shop_orders ilişkisi (garanti için)');

            // Request Info
            $table->string('request_number')->unique()->comment('Talep numarası (SRV-2024-00001)');

            // Service Type
            $table->enum('service_type', [
                'maintenance',      // Bakım
                'repair',           // Onarım
                'installation',     // Kurulum
                'inspection',       // Muayene
                'warranty',         // Garanti
                'consultation',     // Danışmanlık
                'other'             // Diğer
            ])->comment('Servis tipi');

            // Priority
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])
                  ->default('normal')
                  ->comment('Öncelik: low=Düşük, normal=Normal, high=Yüksek, urgent=Acil');

            // Status
            $table->enum('status', [
                'pending',          // Beklemede
                'approved',         // Onaylandı
                'scheduled',        // Planlandı
                'in_progress',      // İşlemde
                'completed',        // Tamamlandı
                'cancelled',        // İptal edildi
                'rejected'          // Reddedildi
            ])->default('pending')->comment('Durum');

            // Request Details
            $table->text('description')->comment('Talep açıklaması');
            $table->json('issues')->nullable()->comment('Sorunlar/Arızalar (JSON array)');
            $table->json('images')->nullable()->comment('Ürün/Arıza görselleri (JSON array)');

            // Product Info (Snapshot)
            $table->string('product_name')->nullable()->comment('Ürün adı (snapshot)');
            $table->string('serial_number')->nullable()->comment('Seri numarası');
            $table->date('purchase_date')->nullable()->comment('Satın alma tarihi');
            $table->boolean('under_warranty')->default(false)->comment('Garanti kapsamında mı?');

            // Schedule
            $table->timestamp('preferred_date')->nullable()->comment('Tercih edilen tarih');
            $table->timestamp('scheduled_at')->nullable()->comment('Planlanan tarih');
            $table->timestamp('completed_at')->nullable()->comment('Tamamlanma tarihi');

            // Assignment
            $table->foreignId('assigned_to_user_id')->nullable()->comment('Atanan teknisyen/kullanıcı ID');
            $table->text('technician_notes')->nullable()->comment('Teknisyen notları');

            // Location
            $table->enum('service_location', ['on_site', 'workshop', 'remote'])
                  ->default('on_site')
                  ->comment('Servis yeri: on_site=Yerinde, workshop=Atölye, remote=Uzaktan');

            $table->json('address')->nullable()->comment('Servis adresi (JSON)');

            // Costs
            $table->decimal('estimated_cost', 12, 2)->nullable()->comment('Tahmini maliyet (₺)');
            $table->decimal('final_cost', 12, 2)->nullable()->comment('Nihai maliyet (₺)');
            $table->text('cost_breakdown')->nullable()->comment('Maliyet detayları');

            // Parts Used
            $table->json('parts_used')->nullable()->comment('Kullanılan parçalar (JSON array)');

            // Customer Satisfaction
            $table->integer('rating')->nullable()->comment('Müşteri puanı (1-5)');
            $table->text('feedback')->nullable()->comment('Müşteri geri bildirimi');

            // Additional Info
            $table->text('notes')->nullable()->comment('Notlar');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('customer_id', 'idx_customer');
            $table->index('product_id', 'idx_product');
            $table->index('order_id', 'idx_order');
            $table->index('request_number', 'idx_number');
            $table->index('service_type', 'idx_type');
            $table->index('priority', 'idx_priority');
            $table->index('status', 'idx_status');
            $table->index('assigned_to_user_id', 'idx_assigned');
            $table->index('scheduled_at', 'idx_scheduled');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse talepleri de silinir');

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse ID null olur');

            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ID null olur');
        })
        ->comment('Servis talepleri - Bakım, onarım, kurulum talepleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_service_requests');
    }
};
