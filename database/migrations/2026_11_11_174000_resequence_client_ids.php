<?php

use App\Support\ClientSequencer;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        ClientSequencer::resequenceIfNeeded();
    }

    public function down(): void
    {
        //
    }
};
