<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->cascadeOnDelete();
            $table->date('payment_date');
            $table->timestamps();
        });

        Schema::create('sales_invoice_payment_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_payment_id')->constrained('sales_invoice_payments')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('method', 10);
            $table->string('reference', 64)->nullable();
            $table->string('bank', 64)->nullable();
            $table->string('nom_tire', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_payment_lines');
        Schema::dropIfExists('sales_invoice_payments');
    }
};
