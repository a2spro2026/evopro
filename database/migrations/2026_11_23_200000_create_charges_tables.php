<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('charge_categories')) {
            Schema::create('charge_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150)->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('charges')) {
            Schema::create('charges', function (Blueprint $table) {
                $table->id();
                $table->string('reference', 64)->unique();
                $table->date('charge_date');
                $table->foreignId('charge_category_id')->nullable()->constrained('charge_categories')->nullOnDelete();
                $table->string('designation', 255)->nullable();
                $table->string('beneficiaire', 255)->nullable();
                $table->string('reglement', 10)->nullable();
                $table->string('numero', 50)->nullable();
                $table->string('banque', 100)->nullable();
                $table->string('nom_tire', 150)->nullable();
                $table->decimal('montant', 14, 2)->default(0);
                $table->date('date_encaissement')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('charges');
        Schema::dropIfExists('charge_categories');
    }
};
