<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expense_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month', 'year'], 'expense_period_user_month_year_unique');
            $table->index(['user_id', 'year', 'month'], 'expense_period_user_year_month_index');
        });

        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_period_id')->constrained('expense_periods')->cascadeOnDelete();
            $table->string('category', 40);
            $table->string('title', 160);
            $table->string('payee_name', 160)->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('expense_date')->nullable();
            $table->string('payment_mode', 40)->nullable();
            $table->string('reference_no', 120)->nullable();
            $table->text('note')->nullable();
            $table->string('bill_path')->nullable();
            $table->string('bill_original_name')->nullable();
            $table->string('bill_mime_type', 120)->nullable();
            $table->unsignedBigInteger('bill_size')->nullable();
            $table->timestamps();

            $table->index(['expense_period_id', 'category'], 'expense_item_period_category_index');
            $table->index(['expense_period_id', 'expense_date'], 'expense_item_period_date_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('expense_items');
        Schema::dropIfExists('expense_periods');
    }
};
