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
        Schema::table('pengaliran', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaliran', 'kesesuaian_izin')) {
                $table->enum('kesesuaian_izin', ['Sesuai Izin', 'Di Luar Izin'])->default('Sesuai Izin')->after('keterangan');
            }
            if (!Schema::hasColumn('pengaliran', 'alasan_tidak_sesuai_izin')) {
                $table->text('alasan_tidak_sesuai_izin')->nullable()->after('kesesuaian_izin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaliran', function (Blueprint $table) {
            $table->dropColumn(['kesesuaian_izin', 'alasan_tidak_sesuai_izin']);
        });
    }
};
