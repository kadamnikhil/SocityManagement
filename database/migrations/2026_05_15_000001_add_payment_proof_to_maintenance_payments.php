<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_payments', function (Blueprint $table) {
            $table->string('transaction_id', 120)->nullable()->after('paid_at');
            $table->text('payment_note')->nullable()->after('transaction_id');
            $table->string('receipt_path')->nullable()->after('payment_note');
            $table->string('receipt_original_name')->nullable()->after('receipt_path');
            $table->string('receipt_mime_type', 120)->nullable()->after('receipt_original_name');
            $table->unsignedBigInteger('receipt_size')->nullable()->after('receipt_mime_type');
        });
    }

    public function down()
    {
        Schema::table('maintenance_payments', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_id',
                'payment_note',
                'receipt_path',
                'receipt_original_name',
                'receipt_mime_type',
                'receipt_size',
            ]);
        });
    }
};
