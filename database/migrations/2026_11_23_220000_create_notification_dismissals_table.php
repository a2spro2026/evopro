<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notification_dismissals')) {
            return;
        }

        Schema::create('notification_dismissals', function (Blueprint $table) {
            $table->id();
            $table->string('user_key', 80);
            $table->string('alert_key', 120);
            $table->timestamps();
            $table->unique(['user_key', 'alert_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_dismissals');
    }
};
