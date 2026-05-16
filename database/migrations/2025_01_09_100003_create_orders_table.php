<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('customer_id'); 
        $table->string('number');
        $table->unsignedBigInteger('service_id')->nullable();
        $table->unsignedBigInteger('type')->nullable(); 
        $table->unsignedBigInteger('customer_addresses_id')->nullable(); 
        $table->decimal('final_amount')->nullable(); 
        $table->string('description')->nullable();
        $table->unsignedBigInteger('vendor_id')->nullable(); 
        $table->string('razorpay_order_id')->nullable();
        $table->timestamps();

    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
