<?php

namespace App\Http\Controllers;

use App\Services\SimoliAiAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AiAssistantController extends Controller
{
    protected SimoliAiAssistantService $aiService;

    public function __construct(SimoliAiAssistantService $aiService)
    {
        $this->aiService = $aiService;
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
