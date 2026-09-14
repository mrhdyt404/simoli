<?php

namespace App\Console\Commands;

use App\Models\MonitoringAlatBerat;
use App\Models\Pks;
use App\Services\SidobeWaService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMissingWaReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simoli:send-missing-wa-reminders {--id_pks= : ID PKS tertentu} {--dry-run : Uji coba tanpa mengirim WA} {--force : Paksa kirim}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi WhatsApp pengingat ke Asisten Unit PKS jika belum ada input laporan kerja hari ini.';

    /**
     * Execute the console command.
     */
    public function handle(SidobeWaService $waService)
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $todayFormatted = $now->translatedFormat('l, d F Y');
        $isDryRun = $this->option('dry-run');

        $this->info("Memulai pengecekan input laporan harian SIMOLI untuk tanggal {$todayFormatted}..." . ($isDryRun ? " [MODE DRY-RUN]" : ""));

        $pksQuery = Pks::whereNotIn('id_pks', [13, 14, 15]);
        if ($this->option('id_pks')) {
            $pksQuery->where('id_pks', $this->option('id_pks'));
        }

        $allPks = $pksQuery->get();
        $sentCount = 0;
        $skippedCount = 0;
        $alreadyInputCount = 0;

        foreach ($allPks as $pks) {
            $hasInput = MonitoringAlatBerat::where('id_pks', $pks->id_pks)
                ->where(function ($q) use ($today) {
                    $q->whereDate('tanggal', $today)
                      ->orWhereDate('created_at', $today);
                })
                ->exists();

            if ($hasInput && !$this->option('force')) {
                $this->line("  [OK] Unit {$pks->nama}: Laporan sudah diinput hari ini.");
                $alreadyInputCount++;
                continue;
            }

            if (empty($pks->wa_asisten)) {
                $this->warn("  [SKIP] Unit {$pks->nama}: Nomor WA Asisten belum diatur.");
                $skippedCount++;
                continue;
            }

            if ($isDryRun) {
                $this->info("  [DRY-RUN] Akan dikirim WA ke {$pks->asisten} ({$pks->wa_asisten}) untuk unit {$pks->nama}.");
                $sentCount++;
                continue;
            }

            $this->line("  [MENGIRIM] Mengirim pengingat WA ke {$pks->asisten} ({$pks->wa_asisten}) untuk unit {$pks->nama}...");
            $res = $waService->sendMissingInputReminder($pks, $today);

            if ($res['success']) {
                $this->info("  [SUKSES] Pengingat berhasil dikirim ke {$pks->nama}.");
                $sentCount++;
            } else {
                $this->error("  [GAGAL] Gagal kirim ke {$pks->nama}: " . ($res['error'] ?? 'Unknown error'));
            }
        }

        $this->info("\nSelesai! Ringkasan: {$alreadyInputCount} PKS telah input, {$sentCount} WA pengingat terkirim, {$skippedCount} dilewati.");

        return Command::SUCCESS;
    }
}
