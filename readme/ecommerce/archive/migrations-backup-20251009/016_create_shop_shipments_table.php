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
        if (Schema::hasTable('shop_shipments')) {
            return;
        }

        Schema::create('shop_shipments', function (Blueprint $table) {
            // Primary Key
            $table->id('shipment_id');

            // Relations
            $table->foreignId('order_id')->comment('Sipariş ID - shop_orders ilişkisi');
            $table->foreignId('shipping_method_id')->nullable()->comment('Kargo yöntemi ID - shop_shipping_methods ilişkisi');

            // Shipment Info
            $table->string('shipment_number')->unique()->comment('Sevkiyat numarası (SHP-2024-00001)');
            $table->string('tracking_number')->nullable()->comment('Kargo takip numarası');
            $table->string('tracking_url')->nullable()->comment('Kargo takip URL (tam link)');

            // Carrier Info
            $table->string('carrier_name')->nullable()->comment('Kargo firması adı (MNG, Yurtiçi, vb)');
            $table->string('carrier_service')->nullable()->comment('Kargo servisi (Standard, Express, vb)');

            // Package Info
            $table->integer('package_count')->default(1)->comment('Paket sayısı');
            $table->decimal('total_weight', 10, 2)->nullable()->comment('Toplam ağırlık (kg)');
            $table->json('package_dimensions')->nullable()->comment('Paket boyutları (JSON - {"length":100,"width":50,"height":30,"unit":"cm"})');
            $table->json('packages')->nullable()->comment('Paket detayları (JSON array - birden fazla paket varsa)');

            // Status
            $table->enum('status', [
                'preparing',        // Hazırlanıyor
                'ready_to_ship',    // Gönderilmeye hazır
                'shipped',          // Kargoya verildi
                'in_transit',       // Yolda
                'out_for_delivery', // Dağıtımda
                'delivered',        // Teslim edildi
                'failed',           // Teslimat başarısız
                'returned',         // İade edildi
                'cancelled'         // İptal edildi
            ])->default('preparing')->comment('Sevkiyat durumu');

            // Shipping Address (Snapshot)
            $table->json('shipping_address')->comment('Teslimat adresi (JSON snapshot)');

            // Important Dates
            $table->timestamp('prepared_at')->nullable()->comment('Hazırlandı tarihi');
            $table->timestamp('shipped_at')->nullable()->comment('Kargoya verilme tarihi');
            $table->timestamp('estimated_delivery_at')->nullable()->comment('Tahmini teslimat tarihi');
            $table->timestamp('delivered_at')->nullable()->comment('Teslim edilme tarihi');

            // Delivery Confirmation
            $table->string('delivered_to')->nullable()->comment('Teslim alan kişi adı');
            $table->text('delivery_notes')->nullable()->comment('Teslimat notları');
            $table->string('signature_file')->nullable()->comment('İmza dosya yolu');
            $table->json('proof_of_delivery')->nullable()->comment('Teslimat kanıtları (JSON - fotoğraflar, imzalar)');

            // Costs
            $table->decimal('shipping_cost', 10, 2)->default(0)->comment('Kargo ücreti (₺)');
            $table->decimal('insurance_cost', 8, 2)->default(0)->comment('Sigorta ücreti (₺)');
            $table->decimal('handling_cost', 8, 2)->default(0)->comment('Paketleme ücreti (₺)');
            $table->decimal('total_cost', 10, 2)->default(0)->comment('Toplam maliyet (₺)');

            // Shipping Labels
            $table->string('label_file')->nullable()->comment('Kargo etiketi dosya yolu (PDF)');
            $table->json('label_data')->nullable()->comment('Etiket verileri (JSON)');

            // Notifications
            $table->boolean('customer_notified')->default(false)->comment('Müşteriye bildirim gönderildi mi?');
            $table->timestamp('last_notification_at')->nullable()->comment('Son bildirim tarihi');

            // Return Info
            $table->boolean('is_return')->default(false)->comment('İade sevkiyatı mı?');
            $table->foreignId('return_for_shipment_id')->nullable()->comment('İade edilen sevkiyat ID');
            $table->text('return_reason')->nullable()->comment('İade nedeni');

            // Tracking Events (Timeline)
            $table->json('tracking_events')->nullable()->comment('Takip olayları (JSON array - timeline)');

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
            $table->index('order_id', 'idx_order');
            $table->index('shipping_method_id', 'idx_shipping_method');
            $table->index('shipment_number', 'idx_shipment_number');
            $table->index('tracking_number', 'idx_tracking_number');
            $table->index('status', 'idx_status');
            $table->index('shipped_at', 'idx_shipped_at');
            $table->index('delivered_at', 'idx_delivered_at');
            $table->index(['order_id', 'status'], 'idx_order_status');

            // Foreign Keys
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse sevkiyatlar da silinir');

            $table->foreign('shipping_method_id')
                  ->references('shipping_method_id')
                  ->on('shop_shipping_methods')
                  ->onDelete('cascade')
                  ->comment('Kargo yöntemi silinirse ID null olur');

            $table->foreign('return_for_shipment_id')
                  ->references('id')
                  ->on('shop_shipments')
                  ->onDelete('set null')
                  ->comment('İade edilen sevkiyat silinirse ID null olur');
        })
        ->comment('Sevkiyatlar - Kargo takibi ve teslimat bilgileri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_shipments');
    }
};
