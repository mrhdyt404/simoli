<?php

namespace App\Http\Controllers;

use App\Services\SimoliAiAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Symfony\Component\HttpFoundation\StreamedResponse;

class AiAssistantController extends Controller
{
    protected SimoliAiAssistantService $aiService;

    public function __construct(SimoliAiAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Endpoint Streaming SSE AI Assistant via Sisil / Ollama Local Engine
     */
    public function stream(Request $request): StreamedResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'current_page' => 'nullable|string',
        ]);

        $user = Auth::user();
        $userPrompt = trim($request->input('prompt'));
        $clientHistory = $request->input('history', []);
        $currentPage = $request->input('current_page', 'Dashboard');
        $today = date('Y-m-d');

        // Deteksi apakah query terkait data / operasional SIMOLI
        $lowerPrompt = strtolower($userPrompt);
        $isDomainQuery = str_contains($lowerPrompt, 'rekap') ||
            str_contains($lowerPrompt, 'pengaliran') ||
            str_contains($lowerPrompt, 'limbah') ||
            str_contains($lowerPrompt, 'rkp') ||
            str_contains($lowerPrompt, 'rencana') ||
            str_contains($lowerPrompt, 'pemeliharaan') ||
            str_contains($lowerPrompt, 'alat berat') ||
            str_contains($lowerPrompt, 'hm') ||
            str_contains($lowerPrompt, 'bbm') ||
            str_contains($lowerPrompt, 'kepatuhan') ||
            str_contains($lowerPrompt, 'input') ||
            str_contains($lowerPrompt, 'pks') ||
            str_contains($lowerPrompt, 'audit') ||
            str_contains($lowerPrompt, 'laporan') ||
            str_contains($lowerPrompt, 'volume') ||
            str_contains($lowerPrompt, 'flat bed');

        // Jika query adalah data SIMOLI, gunakan engine data langsung (Sangat cepat < 0.1 detik & 100% akurat)
        if ($isDomainQuery) {
            $dataResult = $this->aiService->answerQuery($userPrompt, $today);
            $fullAnswer = $dataResult['answer'] ?? 'Tidak ada data yang dapat ditampilkan.';

            return response()->stream(function () use ($fullAnswer) {
                if (function_exists('apache_setenv')) {
                    @apache_setenv('no-gzip', 1);
                }
                @ini_set('zlib.output_compression', 'Off');
                @ini_set('implicit_flush', 1);
                while (ob_get_level() > 0) {
                    ob_end_flush();
                }
                ob_implicit_flush(1);

                // Stream kata-per-kata secara dinamis agar UX halus dan instan
                $words = preg_split('/(?<=\s)|(?=\n)/', $fullAnswer);
                foreach ($words as $chunk) {
                    if ($chunk === '') continue;
                    echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                    usleep(10000); // 10ms jeda antar kata
                }

                echo "data: [DONE]\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache, no-transform',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ]);
        }

        // Untuk percakapan umum / konsultasi, gunakan Ollama dengan persona Sisil
        $systemPrompt = "Nama Anda adalah Sisil, Asisten AI Cerdas resmi sistem SIMOLI (PTPN IV Regional III). " .
            "Pengguna saat ini: " . ($user ? $user->username : 'Rekan') . " (" . ($user && $user->isAdmin() ? 'Administrator' : 'Unit PKS') . "). " .
            "Halaman Aktif: {$currentPage}. Tanggal: {$today}. " .
            "Aturan Utama: Jawablah selalu dalam BAHASA INDONESIA yang ramah, sopan, ringkas, dan profesional. " .
            "Jawab langsung tanpa pengantar berbelit dan tanpa menuliskan proses berpikir internal (<think>).";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        if (!empty($clientHistory) && is_array($clientHistory)) {
            $recent = array_slice($clientHistory, -4);
            foreach ($recent as $msg) {
                if (isset($msg['role'], $msg['content']) && in_array($msg['role'], ['user', 'assistant'])) {
                    $messages[] = [
                        'role' => $msg['role'],
                        'content' => (string) $msg['content']
                    ];
                }
            }
        }

        $messages[] = ['role' => 'user', 'content' => $userPrompt];

        $ollamaUrl = config('services.ollama.base_url', 'http://127.0.0.1:11434') . '/api/chat';
        $model = config('services.ollama.model', 'deepseek-r1:1.5b');

        return response()->stream(function () use ($ollamaUrl, $model, $messages) {
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', 1);
            }
            @ini_set('zlib.output_compression', 'Off');
            @ini_set('implicit_flush', 1);
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            ob_implicit_flush(1);

            $payload = json_encode([
                'model' => $model,
                'messages' => $messages,
                'stream' => true,
                'options' => [
                    'temperature' => 0.5,
                    'num_ctx' => 1024,
                    'num_thread' => 2,
                ]
            ]);

            $ch = curl_init($ollamaUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            $inThink = false;

            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) use (&$inThink) {
                $lines = explode("\n", $data);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    $json = json_decode($line, true);
                    if ($json && isset($json['message']['content'])) {
                        $chunk = $json['message']['content'];

                        // Filter tag <think> dan </think> agar tidak bocor dan tidak memperlambat UI
                        if (str_contains($chunk, '<think>')) {
                            $inThink = true;
                            $chunk = str_replace('<think>', '', $chunk);
                        }
                        if (str_contains($chunk, '</think>')) {
                            $inThink = false;
                            $chunk = str_replace('</think>', '', $chunk);
                        }

                        if ($inThink || empty(trim($chunk))) {
                            continue;
                        }

                        echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                }
                return strlen($data);
            });

            curl_exec($ch);
            if (curl_errno($ch)) {
                echo "data: " . json_encode(['error' => 'Maaf, Sisil sedang tidak dapat terhubung ke engine AI: ' . curl_error($ch)]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
            }
            curl_close($ch);

            echo "data: [DONE]\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Endpoint Chat AI Assistant SIMOLI
     */
    public function chat(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:1000',
            'tanggal' => 'nullable|date',
        ]);

        $query = $request->input('query');
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $result = $this->aiService->answerQuery($query, $tanggal);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban AI Assistant berhasil diproses',
            'data' => $result,
            'server_time' => Carbon::now('Asia/Jakarta')->toIso8601String(),
        ]);
    }

    /**
     * Endpoint Cek Status Kepatuhan & Notifikasi Harian Belum Input
     */
    public function dailyAudit(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $audit = $this->aiService->getDailyAudit($tanggal);
        $stats = $this->aiService->getMonthlyStatistics($tanggal);

        return response()->json([
            'success' => true,
            'message' => 'Status audit harian SIMOLI berhasil dimuat',
            'data' => [
                'audit' => $audit,
                'stats' => $stats,
            ],
            'server_time' => Carbon::now('Asia/Jakarta')->toIso8601String(),
        ]);
    }

    /**
     * Endpoint Kirim Pengingat WhatsApp ke Asisten PKS Tertentu
     */
    public function sendReminder(Request $request)
    {
        $request->validate([
            'id_pks' => 'required|integer|exists:pks,id_pks',
            'tanggal' => 'nullable|date',
        ]);

        $idPks = (int) $request->input('id_pks');
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $res = $this->aiService->sendReminderForPks($idPks, $tanggal);

        return response()->json([
            'success' => $res['success'],
            'message' => $res['message'],
            'data' => $res,
        ]);
    }

    /**
     * Endpoint Kirim Pengingat WhatsApp Sekaligus ke Seluruh PKS yang Belum Input
     */
    public function sendBatchReminder(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $res = $this->aiService->sendBatchReminderToAllMissing($tanggal);

        return response()->json([
            'success' => $res['success'],
            'message' => $res['message'],
            'data' => $res,
        ]);
    }
}
