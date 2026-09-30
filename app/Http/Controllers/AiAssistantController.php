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
     * Endpoint Streaming SSE AI Assistant via Ollama DeepSeek
     */
    public function stream(Request $request): StreamedResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'current_page' => 'nullable|string',
        ]);

        $user = Auth::user();
        $userPrompt = $request->input('prompt');
        $clientHistory = $request->input('history', []);
        $currentPage = $request->input('current_page', 'Dashboard');
        $today = date('Y-m-d');

        // Data audit SIMOLI terkini sebagai konteks (RAG)
        $auditData = $this->aiService->getDailyAudit($today);
        $missingCount = count($auditData['missing_pks'] ?? []);
        $inputtedCount = count($auditData['inputted_pks'] ?? []);
        $missingNames = implode(', ', array_column($auditData['missing_pks'] ?? [], 'akro'));

        $systemPrompt = "Anda adalah Asisten Cerdas SIMOLI (Sistem Informasi Monitoring Limbah & Land Aplikasi PTPN IV Regional III). " .
            "Identitas Pengguna: " . ($user ? $user->username : 'Guest') . " (" . ($user && $user->isAdmin() ? 'Administrator' : 'Unit PKS') . "). " .
            "Halaman Aktif: {$currentPage}. Tanggal Hari Ini: {$today}. " .
            "Status Input Hari Ini: {$inputtedCount} PKS sudah input, {$missingCount} PKS belum input" . ($missingCount > 0 ? " (PKS belum input: {$missingNames})" : "") . ". " .
            "Panduan: Jawablah dengan akurat, ramah, dan profesional dalam Bahasa Indonesia. Gunakan format Markdown (bold, list, tabel) untuk mempermudah pembacaan. " .
            "Jika pengguna menanyakan data tertentu, gunakan konteks sistem di atas.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Ambil maksimal 4 interaksi terakhir dari client untuk efisiensi RAM 2GB
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
                    'temperature' => 0.6,
                    'num_ctx' => 2048,
                ]
            ]);

            $ch = curl_init($ollamaUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);

            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) {
                $lines = explode("\n", $data);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    $json = json_decode($line, true);
                    if ($json && isset($json['message']['content'])) {
                        $chunk = $json['message']['content'];
                        echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                }
                return strlen($data);
            });

            curl_exec($ch);
            if (curl_errno($ch)) {
                echo "data: " . json_encode(['error' => 'Gagal terhubung ke engine Ollama: ' . curl_error($ch)]) . "\n\n";
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
