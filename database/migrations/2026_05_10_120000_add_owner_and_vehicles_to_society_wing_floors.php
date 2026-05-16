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
        Schema::table('society_wing_floors', function (Blueprint $table) {
            $table->string('owner_name')->nullable()->after('flats_count');
            $table->string('owner_mobile', 32)->nullable()->after('owner_name');
            $table->string('owner_email', 255)->nullable()->after('owner_mobile');
            $table->unsignedTinyInteger('vehicles_count')->default(0)->after('owner_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('society_wing_floors', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'owner_mobile', 'owner_email', 'vehicles_count']);
        });
    }
};
