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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->text("parent_id")->nullable();

            // User Details
            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->string('mobile')->nullable();
            $table->string('email')->nullable();

            // Society Details
            $table->string('society_name')->nullable();
            $table->text('address')->nullable();

            // Role
            $table->enum('role', [
                'ADMIN',
                'MEMBER'
            ])->default('ADMIN');

            $table->string('device_id')->nullable();

            $table->timestamp('email_verified_at')->nullable();

            // Login
            $table->string('password');

            // Status
            $table->string('status')->default('ACTIVE');

            $table->rememberToken();

            $table->timestamps();

            // Audit Fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};