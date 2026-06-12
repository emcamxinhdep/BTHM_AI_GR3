<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialty_id')
                  ->constrained('specialties')
                  ->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->string('degree')->nullable();     // BS, ThS, TS, PGS, GS
            $table->string('clinic_name')->nullable();
            $table->string('clinic_address')->nullable();
            $table->string('clinic_district')->nullable();
            $table->string('clinic_city')->default('Hà Nội');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('experience_years')->default(0);
            $table->decimal('consultation_fee', 10, 0)->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->text('description')->nullable();
            $table->text('working_hours')->nullable(); // JSON string
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctors');
    }
};