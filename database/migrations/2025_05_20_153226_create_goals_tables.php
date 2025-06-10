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
        Schema::create('current_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // 'workout', 'weight', 'heart_rate', 'custom'
            $table->string('description');
            $table->float('target_value')->nullable();
            $table->float('start_value')->nullable();
            $table->string('unit')->nullable();
            $table->float('current_value')->nullable();
            $table->integer('progress')->default(0); // percent
            $table->timestamps();
        });

        Schema::create('completed_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->string('description');
            $table->float('target_value')->nullable();
            $table->string('unit')->nullable();
            $table->float('final_value')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completed_goals');
        Schema::dropIfExists('current_goals');
    }
};
