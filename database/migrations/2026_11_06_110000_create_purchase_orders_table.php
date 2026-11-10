<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->date('order_date');
            $table->string('reference', 64)->unique();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier_code', 64)->nullable();
            $table->string('supplier_name');
            $table->string('payment_type', 20);
            $table->string('due_term', 20)->nullable();
            $table->decimal('qty_total', 14, 3)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->string('article_ref', 64)->nullable();
            $table->string('barcode', 64)->nullable();
            $table->string('equiv', 64)->nullable();
            $table->string('description');
            $table->decimal('quantity', 14, 3);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('remise', 5, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
