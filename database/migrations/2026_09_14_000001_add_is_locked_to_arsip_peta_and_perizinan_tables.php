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
        if (Schema::hasTable('arsip_peta_la')) {
            Schema::table('arsip_peta_la', function (Blueprint $table) {
                if (!Schema::hasColumn('arsip_peta_la', 'is_locked')) {
                    $table->boolean('is_locked')->default(false)->after('keterangan')->comment('Status kunci edit oleh Admin');
                }
            });
        }

        if (Schema::hasTable('perizinan_la')) {
            Schema::table('perizinan_la', function (Blueprint $table) {
                if (!Schema::hasColumn('perizinan_la', 'is_locked')) {
                    $table->boolean('is_locked')->default(false)->after('keterangan')->comment('Status kunci edit oleh Admin');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('arsip_peta_la')) {
            Schema::table('arsip_peta_la', function (Blueprint $table) {
                if (Schema::hasColumn('arsip_peta_la', 'is_locked')) {
                    $table->dropColumn('is_locked');
                }
            });
        }

        if (Schema::hasTable('perizinan_la')) {
            Schema::table('perizinan_la', function (Blueprint $table) {
                if (Schema::hasColumn('perizinan_la', 'is_locked')) {
                    $table->dropColumn('is_locked');
                }
            });
        }
    }
};
