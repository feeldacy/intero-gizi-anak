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
        Schema::create('nutrition_records', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('child_id');
            $table->unsignedBigInteger('user_id');
            $table->float('height_cm');
            $table->float('weight_kg');
            $table->float('bmi');
            $table->string('nutrition_status');
            $table->timestamps();

            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_records');
    }
};
