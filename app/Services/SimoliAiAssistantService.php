<?php

namespace App\Services;

use App\Models\Pks;
use App\Models\Pengaliran;
use App\Models\Pemeliharaan;
use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use App\Models\Rencana;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimoliAiAssistantService
{
    protected SidobeWaService $waService;
    protected ?string $geminiApiKey;
    protected string $geminiModel;

    public function __construct(SidobeWaService $waService)
    {
        $this->waService = $waService;
        $this->geminiApiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        $this->geminiModel = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-1.5-flash'));
    }

    /**
     * Dapatkan ringkasan status input harian untuk Pengaliran, Pemeliharaan, dan Alat Berat
     */
    public function getDailyAudit(?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $pksList = Pks::whereNotIn('akro', ['TEP', 'DTM', 'DBR'])->orderBy('nama')->get();

        $auditData = [];
        $missingPengaliran = [];
        $missingPemeliharaan = [];
        $missingAlatBerat = [];
        $fullyCompleted = [];
        $partiallyCompleted = [];
        $unsubmitted = [];

        foreach ($pksList as $pks) {
            $hasPengaliran = Pengaliran::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $targetDate)
                ->exists();

            $hasPemeliharaan = Pemeliharaan::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $targetDate)
                ->exists();

            $hasAlatBerat = MonitoringAlatBerat::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $targetDate)
                ->exists();

            $missingItems = [];
            if (!$hasPengaliran) $missingItems[] = 'Pengaliran LA';
            if (!$hasPemeliharaan) $missingItems[] = 'Pemeliharaan Kolam/Bed';
            if (!$hasAlatBerat) $missingItems[] = 'Operasional Alat Berat';

            $item = [
                'id_pks' => $pks->id_pks,
                'nama' => $pks->nama,
                'akro' => $pks->akro ?: $pks->nama,
                'asisten' => $pks->asisten ?: 'Belum diset',
                'wa_asisten' => $pks->wa_asisten ?: null,
                'pengaliran' => $hasPengaliran,
                'pemeliharaan' => $hasPemeliharaan,
                'alat_berat' => $hasAlatBerat,
                'missing_items' => $missingItems,
                'is_complete' => empty($missingItems),
            ];

            $auditData[] = $item;

            if (!$hasPengaliran) $missingPengaliran[] = $item;
            if (!$hasPemeliharaan) $missingPemeliharaan[] = $item;
            if (!$hasAlatBerat) $missingAlatBerat[] = $item;

            if (empty($missingItems)) {
                $fullyCompleted[] = $item;
            } elseif (count($missingItems) === 3) {
                $unsubmitted[] = $item;
            } else {
                $partiallyCompleted[] = $item;
            }
        }

        return [
            'tanggal' => $targetDate,
            'tanggal_formatted' => Carbon::parse($targetDate)->locale('id')->translatedFormat('l, d F Y'),
            'total_pks' => count($pksList),
            'count_lengkap' => count($fullyCompleted),
            'count_sebagian' => count($partiallyCompleted),
            'count_belum_ada' => count($unsubmitted),
            'missing_pengaliran_count' => count($missingPengaliran),
            'missing_pemeliharaan_count' => count($missingPemeliharaan),
            'missing_alat_berat_count' => count($missingAlatBerat),
            'missing_pengaliran' => $missingPengaliran,
            'missing_pemeliharaan' => $missingPemeliharaan,
            'missing_alat_berat' => $missingAlatBerat,
            'fully_completed' => $fullyCompleted,
            'partially_completed' => $partiallyCompleted,
            'unsubmitted' => $unsubmitted,
            'all_pks' => $auditData,
        ];
    }

    /**
     * Dapatkan ringkasan metrik statistik operasional bulan berjalan
     */
    public function getMonthlyStatistics(?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date) : Carbon::now('Asia/Jakarta');
        $startOfMonth = $targetDate->copy()->startOfMonth();
        $endOfMonth = $targetDate->copy()->endOfMonth();

        // Pengaliran
        $pengaliranQ = Pengaliran::whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        $totalVolDihasilkan = (clone $pengaliranQ)->sum('vol_limbah_dihasilkan') ?: 0;
        $totalVolDialirkan = (clone $pengaliranQ)->sum('vol_limbah_dialirkan') ?: 0;
        $totalFlatBedPengaliran = (clone $pengaliranQ)->sum('flat_bed') ?: 0;
        $totalLuasArea = (clone $pengaliranQ)->sum('luas_area') ?: 0;
        $countPengaliranRecords = (clone $pengaliranQ)->count();

        // Pemeliharaan
        $pemeliharaanQ = Pemeliharaan::whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        $totalFlatBedPemeliharaan = (clone $pemeliharaanQ)->sum('flat_bed') ?: 0;
        $totalLongBedPemeliharaan = (clone $pemeliharaanQ)->sum('long_bed') ?: 0;
        $totalHk = (clone $pemeliharaanQ)->sum('jumlah_hk') ?: 0;
        $totalMekanis = (clone $pemeliharaanQ)->where('jenis_pemeliharaan', '1')->count();
        $totalManual = (clone $pemeliharaanQ)->where('jenis_pemeliharaan', '2')->count();

        // Alat Berat
        $monitoringAbQ = MonitoringAlatBerat::whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        $totalHm = (clone $monitoringAbQ)->sum('total_hm') ?: 0;
        $totalBbm = (clone $monitoringAbQ)->sum('bbm_liter') ?: 0;
        $countLogAb = (clone $monitoringAbQ)->count();

        // Status Master Alat Berat
        $totalUnitAlat = AlatBerat::count();
        $unitReady = AlatBerat::where('status', 'Operational')->count();
        $unitMaintenance = AlatBerat::where('status', 'Maintenance')->count();
        $unitBreakdown = AlatBerat::where('status', 'Breakdown')->count();
        $unitRolling = AlatBerat::where('status', 'Rolling')->count();

        return [
            'bulan_label' => $targetDate->locale('id')->translatedFormat('F Y'),
            'pengaliran' => [
                'records' => $countPengaliranRecords,
                'vol_dihasilkan' => $totalVolDihasilkan,
                'vol_dialirkan' => $totalVolDialirkan,
                'flat_bed' => $totalFlatBedPengaliran,
                'luas_area' => $totalLuasArea,
            ],
            'pemeliharaan' => [
                'records' => (clone $pemeliharaanQ)->count(),
                'flat_bed' => $totalFlatBedPemeliharaan,
                'long_bed' => $totalLongBedPemeliharaan,
                'jumlah_hk' => $totalHk,
                'mekanis' => $totalMekanis,
                'manual' => $totalManual,
            ],
            'alat_berat' => [
                'total_unit' => $totalUnitAlat,
                'operational' => $unitReady,
                'maintenance' => $unitMaintenance,
                'breakdown' => $unitBreakdown,
                'rolling' => $unitRolling,
                'total_hm' => $totalHm,
                'total_bbm' => $totalBbm,
                'total_log' => $countLogAb,
            ],
        ];
    }

    /**
     * Memproses Pertanyaan User ke AI Assistant
     */
    public function answerQuery(string $query, ?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $audit = $this->getDailyAudit($targetDate);
        $stats = $this->getMonthlyStatistics($targetDate);

        // 1. Coba gunakan Gemini API jika key tersedia
        if (!empty($this->geminiApiKey)) {
            try {
                $geminiResponse = $this->callGeminiApi($query, $audit, $stats);
                if (!empty($geminiResponse)) {
                    return [
                        'success' => true,
                        'source' => 'gemini',
                        'answer' => $geminiResponse,
                        'audit' => $audit,
                        'stats' => $stats,
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning("[SIMOLI AI] Gemini API call failed, falling back to heuristic engine: " . $e->getMessage());
            }
        }

        // 2. Gunakan Intelligent Heuristic & NLP SIMOLI Engine
        $localAnswer = $this->generateIntelligentLocalAnswer($query, $audit, $stats);

        return [
            'success' => true,
            'source' => 'simoli_engine',
            'answer' => $localAnswer['text'],
            'badges' => $localAnswer['badges'] ?? [],
            'suggested_actions' => $localAnswer['actions'] ?? [],
            'audit' => $audit,
            'stats' => $stats,
        ];
    }

    /**
     * Panggil Google Gemini API
     */
    protected function callGeminiApi(string $query, array $audit, array $stats): ?string
    {
        $systemContext = "Anda adalah SIMOLI AI Assistant untuk Tim Admin TEP (Bagian Teknik & Pengolahan) PTPN IV Regional III. "
            . "Tugas Anda adalah memantau dan memberikan laporan real-time mengenai pengaliran limbah (Land Application/LA), pemeliharaan kolam IPAL/bed, dan operasional alat berat pada 12 unit PKS.\n\n"
            . "=== DATA REAL-TIME TANGGAL: {$audit['tanggal_formatted']} ===\n"
            . "- Total PKS: {$audit['total_pks']}\n"
            . "- PKS Lengkap input (Pengaliran, Pemeliharaan, Alat Berat): {$audit['count_lengkap']}\n"
            . "- PKS Sebagian input: {$audit['count_sebagian']}\n"
            . "- PKS Belum ada input sama sekali: {$audit['count_belum_ada']}\n\n"
            . "PKS BELUM INPUT PENGALIRAN (" . $audit['missing_pengaliran_count'] . " unit): " . implode(', ', array_map(fn($p) => $p['nama'] . " (" . $p['akro'] . ")", $audit['missing_pengaliran'])) . "\n"
            . "PKS BELUM INPUT PEMELIHARAAN (" . $audit['missing_pemeliharaan_count'] . " unit): " . implode(', ', array_map(fn($p) => $p['nama'] . " (" . $p['akro'] . ")", $audit['missing_pemeliharaan'])) . "\n"
            . "PKS BELUM INPUT ALAT BERAT (" . $audit['missing_alat_berat_count'] . " unit): " . implode(', ', array_map(fn($p) => $p['nama'] . " (" . $p['akro'] . ")", $audit['missing_alat_berat'])) . "\n\n"
            . "=== STATISTIK BULANAN ({$stats['bulan_label']}) ===\n"
            . "- Vol Limbah Dihasilkan: " . number_format($stats['pengaliran']['vol_dihasilkan'], 2) . " m³\n"
            . "- Vol Limbah Dialirkan: " . number_format($stats['pengaliran']['vol_dialirkan'], 2) . " m³\n"
            . "- Total Flat Bed Dialirkan: " . number_format($stats['pengaliran']['flat_bed']) . " Bed\n"
            . "- Luas Area Aplikasi: " . number_format($stats['pengaliran']['luas_area'], 2) . " Ha\n"
            . "- Pemeliharaan Flat Bed: " . number_format($stats['pemeliharaan']['flat_bed']) . " Bed, Long Bed: " . number_format($stats['pemeliharaan']['long_bed']) . " Bed, Total Tenaga Kerja: " . number_format($stats['pemeliharaan']['jumlah_hk']) . " HK\n"
            . "- Alat Berat: {$stats['alat_berat']['total_unit']} Unit (Ready/Operational: {$stats['alat_berat']['operational']}, Maintenance: {$stats['alat_berat']['maintenance']}, Breakdown: {$stats['alat_berat']['breakdown']}, Rolling: {$stats['alat_berat']['rolling']})\n"
            . "- Total Jam Kerja Alat (HM): " . number_format($stats['alat_berat']['total_hm'], 2) . " Jam, Total BBM: " . number_format($stats['alat_berat']['total_bbm'], 2) . " Liter.\n\n"
            . "Gunakan format Markdown profesional dengan emoji, poin-poin tegas, bolding, dan tabel jika relevan. Berikan rekomendasi tindak lanjut yang jelas untuk Admin TEP.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key=" . $this->geminiApiKey;

        $response = Http::timeout(20)->post($url, [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemContext . "\n\nPertanyaan Admin TEP: " . $query]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 1200,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        return null;
    }

    /**
     * Engine NLP & Analisis Heuristik Internal SIMOLI
     */
    protected function generateIntelligentLocalAnswer(string $rawQuery, array $audit, array $stats): array
    {
        $q = strtolower(trim($rawQuery));
        $actions = [];
        $badges = [];

        // 1. Cek Unit / PKS yang Belum Input Hari Ini
        if (
            str_contains($q, 'belum input') ||
            str_contains($q, 'belum isi') ||
            str_contains($q, 'belum ada input') ||
            str_contains($q, 'pemberitahuan') ||
            str_contains($q, 'peringatan') ||
            str_contains($q, 'alert') ||
            str_contains($q, 'notifikasi') ||
            str_contains($q, 'kepatuhan') ||
            str_contains($q, 'siapa saja') ||
            str_contains($q, 'pks mana')
        ) {
            $text = "### 🚨 **Laporan Kepatuhan Input Harian SIMOLI**\n";
            $text .= "📅 **Tanggal Pantauan:** {$audit['tanggal_formatted']}\n\n";

            $text .= "📊 **Ringkasan Status 12 PKS:**\n";
            $text .= "- ✅ **Lengkap (3 Modul):** {$audit['count_lengkap']} Unit PKS\n";
            $text .= "- ⚠️ **Sebagian Input:** {$audit['count_sebagian']} Unit PKS\n";
            $text .= "- ❌ **Belum Ada Input Sama Sekali:** {$audit['count_belum_ada']} Unit PKS\n\n";

            $text .= "---\n";

            // Rincian Pengaliran
            $text .= "#### 💧 **1. Pengaliran Land Aplikasi (LA)**\n";
            if ($audit['missing_pengaliran_count'] === 0) {
                $text .= "✅ *Semua 12 Unit PKS telah menginput data pengaliran hari ini.*\n\n";
            } else {
                $text .= "⚠️ **{$audit['missing_pengaliran_count']} PKS Belum Input Pengaliran:**\n";
                foreach ($audit['missing_pengaliran'] as $idx => $p) {
                    $text .= ($idx + 1) . ". **{$p['nama']} ({$p['akro']})** — Asisten: {$p['asisten']}\n";
                }
                $text .= "\n";
            }

            // Rincian Pemeliharaan
            $text .= "#### 🛠️ **2. Pemeliharaan Kolam IPAL & Bed**\n";
            if ($audit['missing_pemeliharaan_count'] === 0) {
                $text .= "✅ *Semua 12 Unit PKS telah menginput data pemeliharaan hari ini.*\n\n";
            } else {
                $text .= "⚠️ **{$audit['missing_pemeliharaan_count']} PKS Belum Input Pemeliharaan:**\n";
                foreach ($audit['missing_pemeliharaan'] as $idx => $p) {
                    $text .= ($idx + 1) . ". **{$p['nama']} ({$p['akro']})** — Asisten: {$p['asisten']}\n";
                }
                $text .= "\n";
            }

            // Rincian Alat Berat
            $text .= "#### 🚜 **3. Operasional Alat Berat**\n";
            if ($audit['missing_alat_berat_count'] === 0) {
                $text .= "✅ *Semua 12 Unit PKS telah menginput laporan kerja alat berat hari ini.*\n\n";
            } else {
                $text .= "⚠️ **{$audit['missing_alat_berat_count']} PKS Belum Input Alat Berat:**\n";
                foreach ($audit['missing_alat_berat'] as $idx => $p) {
                    $text .= ($idx + 1) . ". **{$p['nama']} ({$p['akro']})** — Asisten: {$p['asisten']}\n";
                }
                $text .= "\n";
            }

            $text .= "💡 *Rekomendasi:* Anda dapat mengirimkan pengingat instan melalui WhatsApp ke seluruh Asisten PKS yang belum melakukan input hari ini.";

            $actions[] = [
                'label' => '📲 Kirim WhatsApp Pengingat ke Semua PKS Tertunda',
                'action_type' => 'trigger_reminder_all',
            ];

            return [
                'text' => $text,
                'actions' => $actions,
                'badges' => [
                    'Belum Pengaliran: ' . $audit['missing_pengaliran_count'],
                    'Belum Pemeliharaan: ' . $audit['missing_pemeliharaan_count'],
                    'Belum Alat Berat: ' . $audit['missing_alat_berat_count'],
                ],
            ];
        }

        // 2. Pertanyaan Seputar Alat Berat
        if (
            str_contains($q, 'alat berat') ||
            str_contains($q, 'excavator') ||
            str_contains($q, 'traktor') ||
            str_contains($q, 'breakdown') ||
            str_contains($q, 'maintenance') ||
            str_contains($q, 'bbm') ||
            str_contains($q, 'hm') ||
            str_contains($q, 'jam kerja')
        ) {
            $ab = $stats['alat_berat'];
            $text = "### 🚜 **Laporan Operasional Alat Berat SIMOLI**\n";
            $text .= "📅 **Periode:** Bulan {$stats['bulan_label']}\n\n";

            $text .= "📦 **Ketersediaan Unit Alat Berat:**\n";
            $text .= "| Status | Jumlah Unit | Persentase |\n";
            $text .= "| :--- | :---: | :---: |\n";
            $totalUnit = max(1, $ab['total_unit']);
            $text .= "| 🟢 **Operational (Ready)** | **{$ab['operational']}** Unit | " . round(($ab['operational'] / $totalUnit) * 100) . "% |\n";
            $text .= "| 🟡 **Maintenance (Perawatan)** | **{$ab['maintenance']}** Unit | " . round(($ab['maintenance'] / $totalUnit) * 100) . "% |\n";
            $text .= "| 🔴 **Breakdown (Rusak)** | **{$ab['breakdown']}** Unit | " . round(($ab['breakdown'] / $totalUnit) * 100) . "% |\n";
            $text .= "| 🔵 **Rolling (Mutasi)** | **{$ab['rolling']}** Unit | " . round(($ab['rolling'] / $totalUnit) * 100) . "% |\n";
            $text .= "| **Total Populasi Unit** | **{$ab['total_unit']}** Unit | 100% |\n\n";

            $text .= "⏱️ **Kinerja Jam Kerja & Bahan Bakar (Bulan Berjalan):**\n";
            $text .= "- 🕒 **Total Jam Kerja (HM):** " . number_format($ab['total_hm'], 2, ',', '.') . " Jam\n";
            $text .= "- ⛽ **Total Konsumsi BBM:** " . number_format($ab['total_bbm'], 2, ',', '.') . " Liter\n";
            $text .= "- 📝 **Total Log Shift Masuk:** " . number_format($ab['total_log']) . " Laporan\n\n";

            if ($ab['breakdown'] > 0) {
                $text .= "⚠️ **Perhatian TEP:** Terdapat **{$ab['breakdown']} unit** alat berat berstatus *Breakdown* yang memerlukan koordinasi perbaikan segera agar tidak menghambat aplikasi limbah di lapangan.";
            }

            return [
                'text' => $text,
                'badges' => [
                    "Ready: {$ab['operational']}",
                    "Breakdown: {$ab['breakdown']}",
                    "Total HM: " . number_format($ab['total_hm'], 1) . " Jam",
                ],
            ];
        }

        // 3. Pertanyaan Seputar Pengaliran / Limbah
        if (
            str_contains($q, 'pengaliran') ||
            str_contains($q, 'limbah') ||
            str_contains($q, 'volume') ||
            str_contains($q, 'debit') ||
            str_contains($q, 'dialirkan') ||
            str_contains($q, 'dihasilkan')
        ) {
            $p = $stats['pengaliran'];
            $text = "### 💧 **Laporan Pengaliran Limbah Land Application (LA)**\n";
            $text .= "📅 **Periode:** Bulan {$stats['bulan_label']}\n\n";

            $text .= "📊 **Akumulasi Kinerja Pengaliran:**\n";
            $text .= "- 🧪 **Limbah Dihasilkan (PKS):** " . number_format($p['vol_dihasilkan'], 2, ',', '.') . " m³\n";
            $text .= "- 🌊 **Limbah Dialirkan (LA):** " . number_format($p['vol_dialirkan'], 2, ',', '.') . " m³\n";
            $persenDialirkan = $p['vol_dihasilkan'] > 0 ? round(($p['vol_dialirkan'] / $p['vol_dihasilkan']) * 100, 1) : 0;
            $text .= "- 📈 **Rasio Pengaliran Limbah:** **{$persenDialirkan}%**\n";
            $text .= "- 🌱 **Aplikasi Flat Bed:** " . number_format($p['flat_bed'], 0, ',', '.') . " Bed\n";
            $text .= "- 🗺️ **Luas Area Aplikasi:** " . number_format($p['luas_area'], 2, ',', '.') . " Hektar\n";
            $text .= "- 📋 **Total Entri Data:** " . number_format($p['records']) . " Transaksi\n\n";

            $text .= "📌 *Status Harian ({$audit['tanggal_formatted']}):* ";
            if ($audit['missing_pengaliran_count'] === 0) {
                $text .= "✅ Seluruh 12 Unit PKS telah menginput data pengaliran hari ini.";
            } else {
                $text .= "⚠️ Masih ada **{$audit['missing_pengaliran_count']} PKS** yang belum input pengaliran hari ini.";
            }

            return [
                'text' => $text,
                'badges' => [
                    'Vol Dialirkan: ' . number_format($p['vol_dialirkan'], 0) . ' m³',
                    'Luas: ' . number_format($p['luas_area'], 1) . ' Ha',
                    'Aplikasi: ' . number_format($p['flat_bed']) . ' Bed',
                ],
            ];
        }

        // 4. Pertanyaan Seputar Pemeliharaan
        if (
            str_contains($q, 'pemeliharaan') ||
            str_contains($q, 'rawat') ||
            str_contains($q, 'kolam') ||
            str_contains($q, 'long bed') ||
            str_contains($q, 'hk') ||
            str_contains($q, 'tenaga kerja')
        ) {
            $m = $stats['pemeliharaan'];
            $text = "### 🛠️ **Laporan Pemeliharaan Kolam IPAL & Bed**\n";
            $text .= "📅 **Periode:** Bulan {$stats['bulan_label']}\n\n";

            $text .= "📊 **Realisasi Pemeliharaan Fisik:**\n";
            $text .= "- 🟫 **Flat Bed Dibersihkan/Dirawat:** " . number_format($m['flat_bed'], 0, ',', '.') . " Bed\n";
            $text .= "- 🟩 **Long Bed Dirawat:** " . number_format($m['long_bed'], 0, ',', '.') . " Bed\n";
            $text .= "- 👷 **Penggunaan Tenaga Kerja (HK):** " . number_format($m['jumlah_hk'], 0, ',', '.') . " HK\n";
            $text .= "- 🚜 **Metode Mekanis (Alat Berat):** " . number_format($m['mekanis']) . " Kegiatan\n";
            $text .= "- 🧤 **Metode Manual:** " . number_format($m['manual']) . " Kegiatan\n\n";

            $text .= "📌 *Status Harian ({$audit['tanggal_formatted']}):* ";
            if ($audit['missing_pemeliharaan_count'] === 0) {
                $text .= "✅ Seluruh 12 Unit PKS telah menginput data pemeliharaan hari ini.";
            } else {
                $text .= "⚠️ Masih ada **{$audit['missing_pemeliharaan_count']} PKS** yang belum input pemeliharaan hari ini.";
            }

            return [
                'text' => $text,
                'badges' => [
                    'Flat Bed: ' . number_format($m['flat_bed']) . ' Bed',
                    'Long Bed: ' . number_format($m['long_bed']) . ' Bed',
                    'Tenaga: ' . number_format($m['jumlah_hk']) . ' HK',
                ],
            ];
        }

        // 5. Default: Executive Overview / Briefing Harian
        $text = "### 🌿 **Executive Daily Briefing — SIMOLI PTPN IV**\n";
        $text .= "📅 **Tanggal:** {$audit['tanggal_formatted']} | **Bulan:** {$stats['bulan_label']}\n\n";

        $text .= "#### 🚨 **Audit Kepatuhan Input Hari Ini:**\n";
        $text .= "- 💧 **Pengaliran:** " . ($audit['missing_pengaliran_count'] === 0 ? '✅ 12/12 Lengkap' : "⚠️ {$audit['missing_pengaliran_count']} PKS Belum Input") . "\n";
        $text .= "- 🛠️ **Pemeliharaan:** " . ($audit['missing_pemeliharaan_count'] === 0 ? '✅ 12/12 Lengkap' : "⚠️ {$audit['missing_pemeliharaan_count']} PKS Belum Input") . "\n";
        $text .= "- 🚜 **Alat Berat:** " . ($audit['missing_alat_berat_count'] === 0 ? '✅ 12/12 Lengkap' : "⚠️ {$audit['missing_alat_berat_count']} PKS Belum Input") . "\n\n";

        $text .= "#### 📈 **Sorotan Kinerja Bulan {$stats['bulan_label']}:**\n";
        $text .= "- **Volume Limbah Dialirkan:** " . number_format($stats['pengaliran']['vol_dialirkan'], 2, ',', '.') . " m³\n";
        $text .= "- **Total Luas Aplikasi:** " . number_format($stats['pengaliran']['luas_area'], 2, ',', '.') . " Ha\n";
        $text .= "- **Pemeliharaan Flat Bed:** " . number_format($stats['pemeliharaan']['flat_bed']) . " Bed (" . number_format($stats['pemeliharaan']['jumlah_hk']) . " HK)\n";
        $text .= "- **Jam Kerja Alat Berat (HM):** " . number_format($stats['alat_berat']['total_hm'], 2) . " Jam (Konsumsi BBM: " . number_format($stats['alat_berat']['total_bbm'], 2) . " L)\n";
        $text .= "- **Kesiapan Unit Alat:** {$stats['alat_berat']['operational']} Ready / {$stats['alat_berat']['breakdown']} Breakdown dari {$stats['alat_berat']['total_unit']} Unit\n\n";

        $text .= "💬 *Anda dapat menanyakan hal spesifik seperti:* `pks mana belum input pengaliran?`, `rekap alat berat breakdown`, `volume limbah bulan ini`, atau `kirim pengingat wa`.";

        return [
            'text' => $text,
            'badges' => [
                'Executive Ready',
                '12 Unit PKS Terpantau',
            ],
            'actions' => [
                [
                    'label' => '🚨 Cek PKS Belum Input Hari Ini',
                    'query' => 'pks mana saja yang belum input data hari ini?',
                ],
                [
                    'label' => '🚜 Cek Kesiapan Alat Berat',
                    'query' => 'bagaimana status alat berat dan konsumsi bbm?',
                ],
                [
                    'label' => '💧 Cek Volume Pengaliran',
                    'query' => 'berapa total volume limbah dialirkan bulan ini?',
                ],
            ],
        ];
    }

    /**
     * Kirim Pengingat WhatsApp ke Asisten PKS untuk Laporan yang Belum Diinput
     */
    public function sendReminderForPks(int $idPks, ?string $date = null): array
    {
        $pks = Pks::find($idPks);
        if (!$pks) {
            return ['success' => false, 'message' => 'Data PKS tidak ditemukan.'];
        }

        if (empty($pks->wa_asisten)) {
            return [
                'success' => false,
                'message' => "Nomor WhatsApp Asisten untuk PKS {$pks->nama} ({$pks->akro}) belum diatur di menu Data Pengguna/PKS.",
            ];
        }

        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $tglFormatted = Carbon::parse($targetDate)->locale('id')->translatedFormat('l, d F Y');

        $hasPengaliran = Pengaliran::where('id_pks', $pks->id_pks)->whereDate('tanggal', $targetDate)->exists();
        $hasPemeliharaan = Pemeliharaan::where('id_pks', $pks->id_pks)->whereDate('tanggal', $targetDate)->exists();
        $hasAlatBerat = MonitoringAlatBerat::where('id_pks', $pks->id_pks)->whereDate('tanggal', $targetDate)->exists();

        $missingList = [];
        if (!$hasPengaliran) $missingList[] = "• *Laporan Pengaliran Land Aplikasi (LA)*";
        if (!$hasPemeliharaan) $missingList[] = "• *Laporan Pemeliharaan Kolam IPAL & Bed*";
        if (!$hasAlatBerat) $missingList[] = "• *Laporan Operasional Alat Berat & Aplikasi*";

        if (empty($missingList)) {
            return [
                'success' => true,
                'message' => "PKS {$pks->nama} sudah lengkap menginput seluruh data untuk tanggal {$tglFormatted}.",
            ];
        }

        $asistenName = !empty($pks->asisten) ? $pks->asisten : 'Asisten ' . $pks->nama;
        $missingText = implode("\n", $missingList);

        $message = "⚠️ *SIMOLI PTPN IV - PENGINGAT INPUT LAPORAN HARIAN*\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n"
            . "Yth. Bapak/Ibu *{$asistenName}*\n"
            . "Asisten Unit PKS *{$pks->nama} ({$pks->akro})*\n\n"
            . "Pemberitahuan dari *Admin TEP SIMOLI* bahwa hingga saat ini laporan berikut *BELUM DIINPUT* untuk tanggal *{$tglFormatted}*:\n\n"
            . "{$missingText}\n\n"
            . "Mohon kesediaannya untuk segera mengarahkan tim operator dan petugas lapangan agar menginput data harian melalui aplikasi *SIMOLI* sebelum batas waktu operasional hari ini.\n\n"
            . "🌐 Akses SIMOLI: https://simoli.ptpn4.co.id\n"
            . "Terima kasih atas kerja sama dan dedikasinya.\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n"
            . "_SIMOLI - Bagian Teknik & Pengolahan (TEP) PTPN IV_";

        $res = $this->waService->sendMessage($pks->wa_asisten, $message);

        return [
            'success' => $res['success'],
            'message' => $res['success']
                ? "Pengingat WhatsApp berhasil dikirim ke Asisten {$pks->nama} ({$pks->wa_asisten})."
                : "Gagal mengirim ke {$pks->nama}: " . ($res['message'] ?? 'Error gateway'),
            'pks' => $pks->nama,
            'phone' => $pks->wa_asisten,
        ];
    }

    /**
     * Kirim Pengingat WhatsApp ke Semua PKS yang Belum Input Hari Ini
     */
    public function sendBatchReminderToAllMissing(?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $audit = $this->getDailyAudit($targetDate);

        $sentCount = 0;
        $failedCount = 0;
        $skippedNoPhoneCount = 0;
        $details = [];

        // Gabungkan semua PKS yang memiliki minimal 1 modul belum diinput
        $allPending = array_merge($audit['partially_completed'], $audit['unsubmitted']);
        $uniquePending = [];
        foreach ($allPending as $p) {
            $uniquePending[$p['id_pks']] = $p;
        }

        if (empty($uniquePending)) {
            return [
                'success' => true,
                'message' => 'Luar biasa! Seluruh 12 Unit PKS telah menginput data lengkap hari ini. Tidak ada notifikasi yang perlu dikirim.',
                'sent_count' => 0,
                'failed_count' => 0,
                'details' => [],
            ];
        }

        foreach ($uniquePending as $item) {
            $pks = Pks::find($item['id_pks']);
            if (!$pks || empty($pks->wa_asisten)) {
                $skippedNoPhoneCount++;
                $details[] = [
                    'pks' => $item['nama'],
                    'status' => 'skipped',
                    'message' => 'Nomor WhatsApp Asisten belum diatur',
                ];
                continue;
            }

            $res = $this->sendReminderForPks($pks->id_pks, $targetDate);
            if ($res['success']) {
                $sentCount++;
                $details[] = [
                    'pks' => $item['nama'],
                    'status' => 'sent',
                    'phone' => $pks->wa_asisten,
                    'message' => 'Terkirim',
                ];
            } else {
                $failedCount++;
                $details[] = [
                    'pks' => $item['nama'],
                    'status' => 'failed',
                    'phone' => $pks->wa_asisten,
                    'message' => $res['message'],
                ];
            }
        }

        return [
            'success' => ($sentCount > 0 || $failedCount === 0),
            'message' => "Proses pengiriman selesai: {$sentCount} berhasil dikirim, {$failedCount} gagal, {$skippedNoPhoneCount} dilewati (nomor WA belum diisi).",
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'skipped_count' => $skippedNoPhoneCount,
            'details' => $details,
        ];
    }
}
