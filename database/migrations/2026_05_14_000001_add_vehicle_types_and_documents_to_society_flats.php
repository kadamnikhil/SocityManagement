<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('society_flats', function (Blueprint $table) {
            if (! Schema::hasColumn('society_flats', 'vehicles_2w')) {
                $table->unsignedTinyInteger('vehicles_2w')->default(0)->after('vehicles_count');
            }
            if (! Schema::hasColumn('society_flats', 'vehicles_3w')) {
                $table->unsignedTinyInteger('vehicles_3w')->default(0)->after('vehicles_2w');
            }
            if (! Schema::hasColumn('society_flats', 'vehicles_4w')) {
                $table->unsignedTinyInteger('vehicles_4w')->default(0)->after('vehicles_3w');
            }
        });

        if (! Schema::hasTable('society_flat_documents')) {
            Schema::create('society_flat_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('society_flat_id')->constrained('society_flats')->cascadeOnDelete();
                $table->string('name', 120);
                $table->string('file_path', 512);
                $table->string('file_original_name', 255);
                $table->unsignedInteger('file_size')->default(0);
                $table->string('mime_type', 128)->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['user_id', 'society_flat_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('society_flat_documents');

        Schema::table('society_flats', function (Blueprint $table) {
            foreach (['vehicles_2w', 'vehicles_3w', 'vehicles_4w'] as $col) {
                if (Schema::hasColumn('society_flats', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
