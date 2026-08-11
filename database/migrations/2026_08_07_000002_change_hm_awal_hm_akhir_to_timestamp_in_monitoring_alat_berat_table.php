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
        DB::statement("ALTER TABLE monitoring_alat_berat MODIFY COLUMN hm_awal VARCHAR(50) NULL DEFAULT NULL");
        DB::statement("ALTER TABLE monitoring_alat_berat MODIFY COLUMN hm_akhir VARCHAR(50) NULL DEFAULT NULL");
        DB::statement("UPDATE monitoring_alat_berat SET hm_awal = NULL, hm_akhir = NULL");
        DB::statement("ALTER TABLE monitoring_alat_berat MODIFY COLUMN hm_awal TIMESTAMP NULL DEFAULT NULL");
        DB::statement("ALTER TABLE monitoring_alat_berat MODIFY COLUMN hm_akhir TIMESTAMP NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE monitoring_alat_berat MODIFY COLUMN hm_awal DECIMAL(8,2) NOT NULL DEFAULT 0.00");
        DB::statement("ALTER TABLE monitoring_alat_berat MODIFY COLUMN hm_akhir DECIMAL(8,2) NOT NULL DEFAULT 0.00");
    }
};
