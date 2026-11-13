<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_invoice_id');
            $table->date('payment_date');
            $table->timestamps();
            $table->foreign('supplier_invoice_id', 'si_pay_invoice_fk')
                ->references('id')->on('supplier_invoices')->cascadeOnDelete();
        });

        Schema::create('supplier_invoice_payment_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_invoice_payment_id');
            $table->decimal('amount', 14, 2);
            $table->string('method', 10);
            $table->string('reference', 64)->nullable();
            $table->string('bank', 64)->nullable();
            $table->string('nom_tire', 255)->nullable();
            $table->timestamps();
            $table->foreign('supplier_invoice_payment_id', 'si_pay_line_payment_fk')
                ->references('id')->on('supplier_invoice_payments')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_invoice_payment_lines');
        Schema::dropIfExists('supplier_invoice_payments');
    }
};
