<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('extra_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 160);
            $table->string('activity_type', 40)->default('other');
            $table->decimal('amount_per_flat', 12, 2);
            $table->decimal('target_amount', 12, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at'], 'extra_activity_user_created_index');
        });

        Schema::create('extra_activity_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_activity_id')->constrained('extra_activities')->cascadeOnDelete();
            $table->foreignId('society_flat_id')->constrained('society_flats')->cascadeOnDelete();
            $table->string('status', 16)->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->string('transaction_id', 120)->nullable();
            $table->text('payment_note')->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('receipt_original_name')->nullable();
            $table->string('receipt_mime_type', 120)->nullable();
            $table->unsignedBigInteger('receipt_size')->nullable();
            $table->timestamps();

            $table->unique(['extra_activity_id', 'society_flat_id'], 'extra_activity_payment_activity_flat_unique');
            $table->index(['extra_activity_id', 'status'], 'extra_activity_payment_status_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('extra_activity_payments');
        Schema::dropIfExists('extra_activities');
    }
};
