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
        Schema::create('health_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // heart_rate, weight, sleep, hydration, blood_pressure
            $table->float('value')->nullable(); // for single value metrics
            $table->float('previous_value')->nullable();
            $table->float('change')->nullable();
            $table->string('status')->nullable(); // improving, deteriorate, needs_attention
            $table->string('status_color')->nullable(); // success, danger, warning
            $table->integer('systolic')->nullable(); // for blood pressure
            $table->integer('diastolic')->nullable();
            $table->date('date');
            $table->time('time');
            $table->text('notes')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_metrics');
    }
};
