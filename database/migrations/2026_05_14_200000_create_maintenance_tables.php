<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month', 'year'], 'maint_period_user_month_year_unique');
            $table->index(['user_id', 'year', 'month'], 'maint_period_user_year_month_index');
        });

        Schema::create('maintenance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_period_id')->constrained('maintenance_periods')->cascadeOnDelete();
            $table->foreignId('society_flat_id')->constrained('society_flats')->cascadeOnDelete();
            $table->string('status', 16)->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['maintenance_period_id', 'society_flat_id'], 'maint_payment_period_flat_unique');
            $table->index(['maintenance_period_id', 'status'], 'maint_payment_period_status_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_payments');
        Schema::dropIfExists('maintenance_periods');
    }
};
