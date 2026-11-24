<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['supplier_payments', 'client_payments', 'charges'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'nom_tire')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->string('nom_tire', 255)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        foreach (['supplier_payments', 'client_payments', 'charges'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'nom_tire')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->string('nom_tire', 150)->nullable()->change();
            });
        }
    }
};
