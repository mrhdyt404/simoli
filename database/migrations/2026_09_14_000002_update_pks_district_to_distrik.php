<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('pks')->where('id_pks', 14)->update(['nama' => 'DISTRIK TIMUR']);
        DB::table('pks')->where('id_pks', 15)->update(['nama' => 'DISTRIK BARAT']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('pks')->where('id_pks', 14)->update(['nama' => 'DISTRICT TIMUR']);
        DB::table('pks')->where('id_pks', 15)->update(['nama' => 'DISTRICT BARAT']);
    }
};
