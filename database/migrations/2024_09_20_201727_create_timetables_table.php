<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('on_time');
            $table->dateTime('off_time');
            $table->dateTime('checkin_start');
            $table->dateTime('checkin_end');
            $table->dateTime('checkout_start');
            $table->dateTime('checkout_end');
            $table->integer('late_time');
            $table->integer('leave_early_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
