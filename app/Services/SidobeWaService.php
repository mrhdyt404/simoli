<?php

namespace App\Services;

use App\Models\MonitoringAlatBerat;
use App\Models\Pks;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SidobeWaService
{
    protected string $baseUrl;
    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.sidobe.url', 'https://api.sidobe.com/wa/v1'), '/');
        $this->secretKey = config('services.sidobe.secret_key', 'lWRBndGzxoDBZDilgoKTsLvePaYEaspKZCyFbirxRTyaxzrOEO');
    }

    /**
     * Format target phone number to E.164 format required by Sidobe (+628123456789)
     */
    public function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Remove non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', trim($phone));

        if (empty($cleaned)) {
            return null;
        }

        // Convert 08... or 8... to +628...
        if (str_starts_with($cleaned, '08')) {
            $cleaned = '+62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '+62' . $cleaned;
        } elseif (str_starts_with($cleaned, '62')) {
            $cleaned = '+' . $cleaned;
        } else {
            $cleaned = '+' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Send WhatsApp message via Sidobe Gateway
     */
    public function sendMessage(string $targetPhone, string $message): array
    {
        $phone = $this->formatPhoneNumber($targetPhone);
        if (!$phone) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tujuan tidak valid.',
            ];
        }

        $url = $this->baseUrl . '/send-message';

        $payload = [
            'phone' => $phone,
            'message' => $message,
            'is_async' => false,
        ];

        $headers = [
            'X-Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->post($url, $payload);

            $responseData = $response->json();

            if ($response->successful() && ($responseData['is_success'] ?? false) === true) {
                Log::info("[Sidobe WA] Pesan berhasil dikirim ke {$phone}");
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim via WhatsApp Gateway Sidobe.',
                    'data' => $responseData['data'] ?? null,
                ];
            }

            $errMsg = $responseData['message'] ?? ("HTTP " . $response->status() . ": " . $response->body());
            Log::warning("[Sidobe WA] Respon gagal dari gateway ke {$phone}: {$errMsg}");

            return [
                'success' => false,
                'message' => $errMsg,
            ];
        } catch (\Throwable $e) {
            Log::error("[Sidobe WA] Exception saat mengirim ke {$phone}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send WhatsApp notification when an operator submits a work log
     */
    public function sendOperatorInputNotification(MonitoringAlatBerat $log): array
    {
        $pks = $log->pks ?? Pks::find($log->id_pks);
        if (!$pks) {
            return ['success' => false, 'message' => 'Data PKS tidak ditemukan.'];
        }

        $waAsisten = $pks->wa_asisten;
        if (empty($waAsisten)) {
            Log::info("[Sidobe WA] Dilewati: Nomor WA Asisten untuk PKS {$pks->nama} belum diisi.");
            return ['success' => false, 'message' => "Nomor WA Asisten untuk PKS {$pks->nama} belum diatur di sistem."];
        }

        $asistenName = !empty($pks->asisten) ? $pks->asisten : 'Asisten ' . $pks->nama;
        $tglFormatted = Carbon::parse($log->tanggal)->translatedFormat('l, d F Y');
        $alatKode = $log->alatBerat ? $log->alatBerat->kode_alat : 'Unit';
        $alatNama = $log->alatBerat ? $log->alatBerat->nama_alat : 'Alat Berat';
        $alatJenis = $log->alatBerat ? $log->alatBerat->jenis_alat : 'Unit';

        $hmAwalStr = $log->hm_awal ? Carbon::parse($log->hm_awal)->format('H:i') : '-';
        $hmAkhirStr = $log->hm_akhir ? Carbon::parse($log->hm_akhir)->format('H:i') : '(Sedang Berjalan)';
        $totalHmStr = $log->total_hm > 0 ? number_format($log->total_hm, 2) : '0';

        $statusShift = $log->isCompleted() ? '✅ *Shift Selesai*' : '⏱️ *Shift Berjalan (Awal)*';

        $message = "🌿 *SIMOLI - NOTIFIKASI LAPORAN KERJA PKS*\n"
            . "━━━━━━━━━━━━━━━━━━━━━\n"
            . "Yth. Bapak/Ibu *{$asistenName}*\n"
            . "Asisten Unit PKS *{$pks->nama}*\n\n"
            . "Laporan kerja operasional baru telah berhasil diinput:\n\n"
            . "📅 *Tanggal:* {$tglFormatted}\n"
            . "🚜 *Unit Alat:* [{$alatKode}] {$alatNama} ({$alatJenis})\n"
            . "👷 *Operator:* *{$log->operator}*\n"
            . "⚙️ *Kegiatan:* {$log->kegiatan}\n"
            . "📍 *Lokasi/Blok:* " . ($log->lokasi_blok ?: '-') . "\n"
            . "⏱️ *Jam Kerja (HM):* {$hmAwalStr} s/d {$hmAkhirStr} ({$totalHmStr} Jam)\n"
            . "🌱 *Aplikasi Bed:* " . number_format($log->flat_bed ?: $log->jumlah_bed, 0, ',', '.') . " Bed\n"
            . "⛽ *Konsumsi BBM:* " . ($log->bbm_liter > 0 ? $log->bbm_liter . ' Liter' : '0 Liter') . "\n"
            . "🛠️ *Kondisi Alat:* {$log->kondisi_alat}\n"
            . "📊 *Status:* {$statusShift}\n";

        if (!empty($log->catatan)) {
            $message .= "📝 *Catatan:* {$log->catatan}\n";
        }

        $message .= "\nData telah tersimpan di sistem SIMOLI PTPN IV Regional III.\n"
            . "━━━━━━━━━━━━━━━━━━━━━\n"
            . "_SIMOLI (PTPN IV Regional III)_";

        return $this->sendMessage($waAsisten, $message);
    }

    /**
     * Send WhatsApp reminder to PKS Asisten when operators have NOT inputted data today
     */
    public function sendMissingInputReminder(Pks $pks, ?string $date = null): array
    {
        $waAsisten = $pks->wa_asisten;
        if (empty($waAsisten)) {
            return ['success' => false, 'message' => "Nomor WA Asisten untuk PKS {$pks->nama} belum diatur."];
        }

        $asistenName = !empty($pks->asisten) ? $pks->asisten : 'Asisten ' . $pks->nama;
        $dateObj = $date ? Carbon::parse($date) : Carbon::now('Asia/Jakarta');
        $tglFormatted = $dateObj->translatedFormat('l, d F Y');

        $message = "⚠️ *SIMOLI - PENGINGAT INPUT LAPORAN HARIAN*\n"
            . "━━━━━━━━━━━━━━━━━━━━━\n"
            . "Yth. Bapak/Ibu *{$asistenName}*\n"
            . "Asisten Unit PKS *{$pks->nama}*\n\n"
            . "Pemberitahuan bahwa hingga saat ini *BELUM ADA* laporan operasional alat berat & aplikasi bed yang diinput oleh operator untuk unit *{$pks->nama}* pada hari ini (*{$tglFormatted}*).\n\n"
            . "Mohon bantuan Bapak/Ibu untuk mengarahkan operator lapangan agar segera menginput laporan kerja harian melalui aplikasi SIMOLI.\n\n"
            . "Terima kasih atas perhatian dan kerja samanya.\n"
            . "━━━━━━━━━━━━━━━━━━━━━\n"
            . "_SIMOLI - PTPN IV Regional III_";

        return $this->sendMessage($waAsisten, $message);
    }
}
