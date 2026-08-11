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
        Schema::create('alat_berat', function (Blueprint $table) {
            $table->id();
            $table->integer('id_pks')->nullable()->comment('FK ke tabel pks');
            $table->string('kode_alat', 30);
            $table->string('nama_alat', 100);
            $table->enum('jenis_alat', ['Excavator', 'Wheel Loader', 'Bulldozer', 'Dump Truck', 'Compactor', 'Lainnya'])->default('Excavator');
            $table->string('merk_tipe', 100)->nullable();
            $table->year('tahun_pengadaan')->nullable();
            $table->enum('status', ['Operational', 'Maintenance', 'Breakdown', 'Standby'])->default('Operational');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat_berat');
    }
};
