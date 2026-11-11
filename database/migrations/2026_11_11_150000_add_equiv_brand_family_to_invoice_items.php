<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_invoice_items', function (Blueprint $table) {
            if (! Schema::hasColumn('supplier_invoice_items', 'equiv')) {
                $table->string('equiv', 64)->nullable()->after('article_ref');
            }
            if (! Schema::hasColumn('supplier_invoice_items', 'brand')) {
                $table->string('brand', 120)->nullable()->after('description');
            }
            if (! Schema::hasColumn('supplier_invoice_items', 'family')) {
                $table->string('family', 120)->nullable()->after('brand');
            }
        });

        Schema::table('sales_invoice_items', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_invoice_items', 'equiv')) {
                $table->string('equiv', 64)->nullable()->after('article_ref');
            }
            if (! Schema::hasColumn('sales_invoice_items', 'brand')) {
                $table->string('brand', 120)->nullable()->after('description');
            }
            if (! Schema::hasColumn('sales_invoice_items', 'family')) {
                $table->string('family', 120)->nullable()->after('brand');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supplier_invoice_items', function (Blueprint $table) {
            foreach (['equiv', 'brand', 'family'] as $column) {
                if (Schema::hasColumn('supplier_invoice_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('sales_invoice_items', function (Blueprint $table) {
            foreach (['equiv', 'brand', 'family'] as $column) {
                if (Schema::hasColumn('sales_invoice_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
