<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->onDelete('cascade');
            $table->foreignId('doctor_id')
                  ->constrained('doctors')
                  ->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('symptoms')->nullable();
            $table->text('note')->nullable();
            $table->text('diagnosis')->nullable();        // kết quả chẩn đoán (sau khám)
            $table->decimal('fee', 10, 0)->default(0);
            $table->enum('payment_method', ['cash', 'momo'])->default('cash');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->string('payment_transaction_id')->nullable();
            $table->enum('status', [
                'pending',    // chờ xác nhận
                'confirmed',  // đã xác nhận
                'completed',  // đã khám xong
                'cancelled'   // đã hủy
            ])->default('pending');
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};