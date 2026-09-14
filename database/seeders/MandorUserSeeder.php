<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pks;
use App\Models\User;

class MandorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua PKS operasional pabrik (kode berawalan '05.PKS.')
        $pksList = Pks::where('kode', 'like', '05.PKS.%')
            ->orderBy('id_pks')
            ->get();

        foreach ($pksList as $pks) {
            $username = 'mandor_' . strtolower(trim($pks->akro));

            User::updateOrCreate(
                [
                    'username' => $username,
                ],
                [
                    'id_pks' => $pks->id_pks,
                    'password' => $username,
                    'level_akses' => 'mandor',
                ]
            );

            $this->command->info("Akun mandor untuk PKS {$pks->nama} ({$username}) berhasil diproses.");
        }
    }
}
