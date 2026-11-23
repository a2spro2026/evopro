<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('supplier_payments') && ! Schema::hasColumn('supplier_payments', 'amount_on_initial')) {
            Schema::table('supplier_payments', function (Blueprint $table) {
                $table->decimal('amount_on_initial', 14, 2)->default(0)->after('montant');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('supplier_payments') && Schema::hasColumn('supplier_payments', 'amount_on_initial')) {
            Schema::table('supplier_payments', function (Blueprint $table) {
                $table->dropColumn('amount_on_initial');
            });
        }
    }
};
