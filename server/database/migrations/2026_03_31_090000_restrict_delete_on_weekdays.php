<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('days', function (Blueprint $table) {
            $table->dropForeign(['weekday_id']);
            $table->foreign('weekday_id')
                ->references('id')
                ->on('weekdays')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('days', function (Blueprint $table) {
            $table->dropForeign(['weekday_id']);
            $table->foreign('weekday_id')
                ->references('id')
                ->on('weekdays')
                ->cascadeOnDelete();
        });
    }
};
