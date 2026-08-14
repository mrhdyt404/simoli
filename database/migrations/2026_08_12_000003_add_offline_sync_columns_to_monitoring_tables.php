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
        Schema::table('monitoring_alat_berat', function (Blueprint $table) {
            if (!Schema::hasColumn('monitoring_alat_berat', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('monitoring_alat_berat', 'sync_version')) {
                $table->unsignedInteger('sync_version')->default(1)->after('catatan');
            }
            if (!Schema::hasColumn('monitoring_alat_berat', 'client_created_at')) {
                $table->timestamp('client_created_at')->nullable()->after('sync_version');
            }
            if (!Schema::hasColumn('monitoring_alat_berat', 'synced_at')) {
                $table->timestamp('synced_at')->nullable()->after('client_created_at');
            }
            if (!Schema::hasColumn('monitoring_alat_berat', 'device_id')) {
                $table->string('device_id', 100)->nullable()->after('synced_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_alat_berat', function (Blueprint $table) {
            $columns = ['uuid', 'sync_version', 'client_created_at', 'synced_at', 'device_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('monitoring_alat_berat', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
