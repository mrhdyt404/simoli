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
        if (!Schema::hasTable('perizinan_la')) {
            Schema::create('perizinan_la', function (Blueprint $table) {
                $table->id();
                $table->integer('id_pks')->nullable()->comment('FK ke tabel pks');
                $table->string('nomor_sk', 100);
                $table->string('tentang', 255)->default('Izin Pemanfaatan Air Limbah Pabrik Kelapa Sawit Pada Tanah (Land Application)');
                $table->string('instansi_penerbit', 150);
                $table->date('tanggal_terbit')->nullable();
                $table->date('tanggal_berakhir')->nullable();
                $table->integer('masa_berlaku_tahun')->default(5);
                
                // Baku Mutu & Batasan Teknis
                $table->integer('bod_maksimal')->default(5000)->comment('BOD maks dalam mg/L atau ppm');
                $table->decimal('ph_min', 4, 2)->default(6.00);
                $table->decimal('ph_max', 4, 2)->default(9.00);
                $table->decimal('debit_maksimal_harian', 10, 2)->default(400.00)->comment('m3 per hari');
                $table->decimal('luas_areal_izin', 10, 2)->default(0.00)->comment('Total luas areal berizin dalam Ha');
                $table->string('saluran_distribusi', 100)->default('Pipa PVC ukuran 6 inchi');
                
                // Titik Penaatan Effluent (Outlet IPAL)
                $table->string('nama_titik_penaatan', 100)->default('IPAL Kolam Anaerob Pond IV');
                $table->decimal('lat_titik_penaatan', 11, 8)->nullable();
                $table->decimal('long_titik_penaatan', 11, 8)->nullable();
                $table->string('koordinat_penaatan_text', 100)->nullable();
                
                // Berkas
                $table->string('file_sk', 255)->nullable();
                $table->string('file_peta', 255)->nullable();
                
                // Status & Catatan
                $table->enum('status_izin', ['Aktif', 'Proses Perpanjangan', 'Kedaluwarsa'])->default('Aktif');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('perizinan_sumur_pantau')) {
            Schema::create('perizinan_sumur_pantau', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('perizinan_la_id');
                $table->string('nama_sumur', 100);
                $table->enum('jenis_sumur', ['Sumur Pantau Aplikasi', 'Sumur Pantau Kontrol', 'Sumur Pantau Pemukiman', 'Lainnya'])->default('Sumur Pantau Aplikasi');
                $table->string('lokasi_blok', 100)->nullable();
                $table->decimal('latitude', 11, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('koordinat_text', 100)->nullable();
                $table->string('frekuensi_pantau', 50)->default('6 bulan sekali');
                $table->text('parameter_pantau')->nullable();
                $table->timestamps();

                $table->foreign('perizinan_la_id')->references('id')->on('perizinan_la')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('peta_blok_la')) {
            Schema::create('peta_blok_la', function (Blueprint $table) {
                $table->id();
                $table->integer('id_pks')->nullable()->comment('FK ke tabel pks');
                $table->string('afdeling', 50)->default('AFD I');
                $table->string('nama_blok', 50);
                $table->decimal('luas_ha', 8, 2)->default(0.00);
                $table->integer('no_bak_awal')->nullable();
                $table->integer('no_bak_akhir')->nullable();
                $table->integer('jumlah_bak')->default(0);
                $table->integer('jumlah_flat_bed')->default(0);
                $table->decimal('panjang_parit_meter', 10, 2)->default(0.00);
                $table->decimal('latitude_center', 11, 8)->nullable();
                $table->decimal('longitude_center', 11, 8)->nullable();
                $table->longText('polygon_geojson')->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinan_sumur_pantau');
        Schema::dropIfExists('peta_blok_la');
        Schema::dropIfExists('perizinan_la');
    }
};
