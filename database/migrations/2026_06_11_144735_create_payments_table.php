<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')
                  ->constrained('appointments')
                  ->onDelete('cascade');
            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->onDelete('cascade');
            $table->decimal('amount', 10, 0);
            $table->enum('method', ['cash', 'momo'])->default('momo');
            $table->string('transaction_id')->nullable();   // mã giao dịch MoMo
            $table->string('order_id')->nullable();         // orderId gửi lên MoMo
            $table->string('request_id')->nullable();
            $table->text('raw_response')->nullable();       // JSON response từ MoMo
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};