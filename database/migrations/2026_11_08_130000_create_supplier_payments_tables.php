<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_orders', 'montant_paye')) {
                $table->decimal('montant_paye', 14, 2)->default(0)->after('total');
            }
            if (! Schema::hasColumn('purchase_orders', 'payment_action')) {
                $table->string('payment_action', 20)->nullable()->after('montant_paye');
            }
        });

        if (! Schema::hasTable('supplier_payments')) {
            Schema::create('supplier_payments', function (Blueprint $table) {
                $table->id();
                $table->string('reference', 64)->unique();
                $table->date('payment_date');
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
                $table->string('reglement', 10)->nullable();
                $table->string('numero', 50)->nullable();
                $table->string('banque', 100)->nullable();
                $table->string('nom_tire', 150)->nullable();
                $table->decimal('montant', 14, 2)->default(0);
                $table->date('date_decaissement')->nullable();
                $table->text('remarque')->nullable();
                $table->decimal('total_ttc', 14, 2)->default(0);
                $table->decimal('solde_ttc', 14, 2)->default(0);
                $table->string('statut', 20)->default('Inst');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('supplier_payment_allocations')) {
            Schema::create('supplier_payment_allocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_payment_id')->constrained('supplier_payments')->cascadeOnDelete();
                $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
                $table->decimal('amount', 14, 2)->default(0);
                $table->string('action', 20)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payment_allocations');
        Schema::dropIfExists('supplier_payments');

        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'payment_action')) {
                $table->dropColumn('payment_action');
            }
            if (Schema::hasColumn('purchase_orders', 'montant_paye')) {
                $table->dropColumn('montant_paye');
            }
        });
    }
};
