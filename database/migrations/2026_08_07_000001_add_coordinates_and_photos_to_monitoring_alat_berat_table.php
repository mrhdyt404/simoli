<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('monitoring_alat_berat', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('lokasi_blok');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('foto_sebelum', 255)->nullable()->after('kondisi_alat');
            $table->string('foto_sesudah', 255)->nullable()->after('foto_sebelum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_alat_berat', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'foto_sebelum', 'foto_sesudah']);
        });
    }
};
