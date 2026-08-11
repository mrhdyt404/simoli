<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('monitoring_alat_berat', function (Blueprint $table) {
            $table->integer('flat_bed')->default(0)->after('lokasi_blok')->comment('Jumlah flat bed yang dikerjakan');
            $table->integer('long_bed')->default(0)->after('flat_bed')->comment('Jumlah long bed yang dikerjakan');
        });

        // Copy existing jumlah_bed values to flat_bed for backward compatibility if present
        if (Schema::hasColumn('monitoring_alat_berat', 'jumlah_bed')) {
            DB::statement('UPDATE monitoring_alat_berat SET flat_bed = jumlah_bed WHERE (flat_bed IS NULL OR flat_bed = 0) AND jumlah_bed > 0');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_alat_berat', function (Blueprint $table) {
            $table->dropColumn(['flat_bed', 'long_bed']);
        });
    }
};
