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
            $table->decimal('latitude_awal', 10, 8)->nullable()->after('longitude');
            $table->decimal('longitude_awal', 11, 8)->nullable()->after('latitude_awal');
            $table->decimal('latitude_akhir', 10, 8)->nullable()->after('longitude_awal');
            $table->decimal('longitude_akhir', 11, 8)->nullable()->after('latitude_akhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_alat_berat', function (Blueprint $table) {
            $table->dropColumn(['latitude_awal', 'longitude_awal', 'latitude_akhir', 'longitude_akhir']);
        });
    }
};
