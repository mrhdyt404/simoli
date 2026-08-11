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
        Schema::create('monitoring_alat_berat', function (Blueprint $table) {
            $table->id();
            $table->integer('id_pks')->nullable()->comment('FK ke tabel pks');
            $table->foreignId('alat_berat_id')->constrained('alat_berat')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('operator', 100);
            $table->string('kegiatan', 150)->comment('Jenis kegiatan misal Pembersihan Kolam Limbah, Land Application, dll');
            $table->string('lokasi_blok', 100)->nullable();
            $table->timestamp('hm_awal')->nullable();
            $table->timestamp('hm_akhir')->nullable();
            $table->decimal('total_hm', 8, 2)->default(0);
            $table->decimal('bbm_liter', 8, 2)->default(0)->comment('Konsumsi solar dalam Liter');
            $table->enum('kondisi_alat', ['Normal', 'Perlu Perbaikan', 'Breakdown'])->default('Normal');
            $table->string('foto', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_alat_berat');
    }
};
