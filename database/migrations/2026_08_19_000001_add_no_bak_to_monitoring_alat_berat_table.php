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
        if (!Schema::hasColumn('monitoring_alat_berat', 'no_bak')) {
            Schema::table('monitoring_alat_berat', function (Blueprint $table) {
                $table->string('no_bak', 100)->nullable()->after('lokasi_blok')->comment('Nomor Bak Distribusi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('monitoring_alat_berat', 'no_bak')) {
            Schema::table('monitoring_alat_berat', function (Blueprint $table) {
                $table->dropColumn('no_bak');
            });
        }
    }
};
