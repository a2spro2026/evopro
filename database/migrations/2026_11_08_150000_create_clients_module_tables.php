<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->decimal('initial_balance', 14, 2)->default(0);
                $table->string('ice')->nullable();
                $table->string('payment_terms')->nullable();
                $table->enum('reglement', ['Esp', 'Chq', 'Eff', 'Vir', 'Vers'])->nullable();
                $table->enum('status', ['actif', 'inactif'])->default('actif');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('delivery_orders')) {
            Schema::create('delivery_orders', function (Blueprint $table) {
                $table->id();
                $table->date('order_date');
                $table->string('reference', 64)->unique();
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
                $table->string('client_code', 64)->nullable();
                $table->string('client_name');
                $table->string('payment_type', 20)->nullable();
                $table->string('due_term', 20)->nullable();
                $table->decimal('qty_total', 14, 3)->default(0);
                $table->decimal('total', 14, 2)->default(0);
                $table->decimal('montant_paye', 14, 2)->default(0);
                $table->string('payment_action', 20)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('delivery_order_items')) {
            Schema::create('delivery_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delivery_order_id')->constrained('delivery_orders')->cascadeOnDelete();
                $table->string('article_ref', 64)->nullable();
                $table->string('barcode', 64)->nullable();
                $table->string('equiv', 64)->nullable();
                $table->string('description');
                $table->string('brand', 120)->nullable();
                $table->string('family', 120)->nullable();
                $table->decimal('quantity', 14, 3);
                $table->decimal('unit_price', 14, 2)->default(0);
                $table->decimal('remise', 5, 2)->default(0);
                $table->decimal('total', 14, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('client_payments')) {
            Schema::create('client_payments', function (Blueprint $table) {
                $table->id();
                $table->string('reference', 64)->unique();
                $table->date('payment_date');
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
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

        if (! Schema::hasTable('client_payment_allocations')) {
            Schema::create('client_payment_allocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_payment_id')->constrained('client_payments')->cascadeOnDelete();
                $table->foreignId('delivery_order_id')->constrained('delivery_orders')->cascadeOnDelete();
                $table->decimal('amount', 14, 2)->default(0);
                $table->string('action', 20)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('client_payment_allocations');
        Schema::dropIfExists('client_payments');
        Schema::dropIfExists('delivery_order_items');
        Schema::dropIfExists('delivery_orders');
        Schema::dropIfExists('clients');
    }
};
