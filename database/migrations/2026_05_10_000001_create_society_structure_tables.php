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
        Schema::create('society_wings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code', 8);
            $table->string('label', 64)->nullable();
            $table->unsignedTinyInteger('floors_count');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('society_wing_floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('society_wing_id')->constrained('society_wings')->cascadeOnDelete();
            $table->unsignedTinyInteger('floor_number');
            $table->unsignedSmallInteger('flats_count')->nullable();
            $table->timestamps();

            $table->unique(['society_wing_id', 'floor_number']);
            $table->index('user_id');
        });

        Schema::create('society_flats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('society_wing_id')->constrained('society_wings')->cascadeOnDelete();
            $table->unsignedTinyInteger('floor_number');
            $table->unsignedSmallInteger('flat_index');
            $table->string('unit_code', 32);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'unit_code']);
            $table->index(['user_id', 'society_wing_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('society_flats');
        Schema::dropIfExists('society_wing_floors');
        Schema::dropIfExists('society_wings');
    }
};
