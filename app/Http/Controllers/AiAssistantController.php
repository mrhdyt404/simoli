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
            'prompt' => 'required|string|max:500',
            'history' => 'nullable|array',
            'current_page' => 'nullable|string',
        ]);

        $user = Auth::user();
        $userPrompt = mb_substr(trim($request->input('prompt')), 0, 500);
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

        // Untuk percakapan umum / konsultasi, gunakan model lokal dengan persona SISIL (Ultra-ringan di RAM 2GB)
        $systemPrompt = "Nama Anda adalah SISIL (SIMOLI Smart Intelligence Assistant), asisten virtual cerdas resmi sistem monitoring limbah dan Land Application PTPN IV Regional III. " .
            "Pengguna saat ini: " . ($user ? $user->username : 'Rekan') . " (" . ($user && $user->isAdmin() ? 'Administrator Regional' : 'Unit PKS') . "). " .
            "Halaman Aktif: {$currentPage}. Tanggal: {$today}. " .
            "Aturan: Jawablah selalu dalam BAHASA INDONESIA secara PADAT, RINGKAS, dan LANGSUNG KE INTI JAWABAN (maksimal 2-3 paragraf singkat atau poin ringkas). Hindari berbelit-belit atau tag berpikir.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Hemat RAM 2GB: Ambil hanya 2 pesan percakapan terakhir
        if (!empty($clientHistory) && is_array($clientHistory)) {
            $recent = array_slice($clientHistory, -2);
            foreach ($recent as $msg) {
                if (isset($msg['role'], $msg['content']) && in_array($msg['role'], ['user', 'assistant'])) {
                    $messages[] = [
                        'role' => $msg['role'],
                        'content' => mb_substr((string) $msg['content'], 0, 300)
                    ];
                }
            }
        }

        $messages[] = ['role' => 'user', 'content' => $userPrompt];

        $ollamaUrl = config('services.ollama.base_url', 'http://127.0.0.1:11434') . '/api/chat';
        $model = config('services.ollama.model', 'qwen2.5:1.5b');

        return response()->stream(function () use ($ollamaUrl, $model, $messages, $userPrompt) {
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
                    'temperature' => 0.3, // Lebih deterministik & cepat
                    'num_predict' => 150, // Respon cepat 1-3 detik, cegah looping CPU
                    'num_ctx' => 768,    // Sangat hemat memori RAM 2GB
                    'num_thread' => 2,
                ]
            ]);

            $ch = curl_init($ollamaUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 180);

            $inThink = false;
            $hasOutput = false;

            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) use (&$inThink, &$hasOutput) {
                $lines = explode("\n", $data);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    $json = json_decode($line, true);
                    if ($json && isset($json['message']['content'])) {
                        $chunk = $json['message']['content'];

                        // Filter tag <think> dan </think>
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

                        $hasOutput = true;
                        echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                }
                return strlen($data);
            });

            curl_exec($ch);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Jika ada error atau tidak ada output sama sekali dari LLM, fallback ke pesan informatif Sisil
            if (!empty($curlError) && !$hasOutput) {
                $fallbackText = "Halo! Sisil siap membantu Anda. Untuk saat ini, Anda dapat menanyakan rekap data pengaliran, data RKP, pemeliharaan, atau status input harian PKS pada menu yang tersedia.";
                echo "data: " . json_encode(['text' => $fallbackText]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
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
