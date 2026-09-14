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
        Schema::table('pks', function (Blueprint $table) {
            if (!Schema::hasColumn('pks', 'asisten')) {
                $table->string('asisten', 100)->nullable()->after('manager');
            }
            if (!Schema::hasColumn('pks', 'wa_asisten')) {
                $table->string('wa_asisten', 30)->nullable()->after('asisten');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pks', function (Blueprint $table) {
            if (Schema::hasColumn('pks', 'asisten')) {
                $table->dropColumn('asisten');
            }
            if (Schema::hasColumn('pks', 'wa_asisten')) {
                $table->dropColumn('wa_asisten');
            }
        });
    }
};
