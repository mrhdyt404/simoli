<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pks;
use App\Models\User;

class OperatorUserSeeder extends Seeder
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
            $username = 'opt_' . strtolower(trim($pks->akro));

            User::updateOrCreate(
                [
                    'username' => $username,
                ],
                [
                    'id_pks' => $pks->id_pks,
                    'password' => $username,
                    'level_akses' => 'operator',
                ]
            );

            $this->command->info("Akun operator untuk PKS {$pks->nama} ({$username}) berhasil diproses.");
        }

        // Sinkronisasi akun opt_terantam jika ada di database
        $optTerantam = User::where('username', 'opt_terantam')->first();
        if ($optTerantam) {
            $optTerantam->update([
                'password' => 'opt_terantam',
                'level_akses' => 'operator',
            ]);
            $this->command->info("Akun opt_terantam berhasil diselaraskan password-nya.");
        }
    }
}
