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
            $table->integer('jumlah_bed')->default(0)->after('lokasi_blok')->comment('Jumlah bed yang dikerjakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_alat_berat', function (Blueprint $table) {
            $table->dropColumn('jumlah_bed');
        });
    }
};
