<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->onDelete('cascade');
            $table->foreignId('doctor_id')
                  ->constrained('doctors')
                  ->onDelete('cascade');
            $table->foreignId('appointment_id')
                  ->constrained('appointments')
                  ->onDelete('cascade');
            $table->tinyInteger('rating');   // 1 → 5 sao
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};