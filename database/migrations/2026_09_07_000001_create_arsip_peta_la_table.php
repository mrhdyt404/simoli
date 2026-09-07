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
        if (!Schema::hasTable('arsip_peta_la')) {
            Schema::create('arsip_peta_la', function (Blueprint $table) {
                $table->id();
                $table->integer('id_pks')->nullable()->comment('FK ke tabel pks');
                $table->string('nama_peta', 200);
                $table->string('kategori_peta', 100)->default('Peta Lokasi Land Application');
                $table->string('tahun_peta', 10)->nullable();
                $table->string('file_peta', 255);
                $table->string('tipe_file', 20)->nullable()->comment('pdf, image, geojson, zip, etc');
                $table->bigInteger('ukuran_file')->nullable()->comment('ukuran dalam bytes');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_peta_la');
    }
};
