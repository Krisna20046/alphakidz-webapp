<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', env('API_BASE_URL', '')), '/');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CHAT LIST  GET /chat
    // ─────────────────────────────────────────────────────────────────────────

    public function list()
    {
        return view('chat.list');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CHAT ROOM  GET /chat/{id}
    // ─────────────────────────────────────────────────────────────────────────

    public function room(Request $request, int $idPenerima)
    {
        $namaPenerima = $request->query('nama', 'Pengguna');
        return view('chat.room', compact('idPenerima', 'namaPenerima'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API PROXY: GET chat list  GET /api/chat-list
    // ─────────────────────────────────────────────────────────────────────────

    public function apiChatList()
    {
        $token = session('token');
        if (!$token) return response()->json(['success' => false], 401);

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->get("{$this->apiBaseUrl}/all-chat");

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'data' => $data['data'] ?? []]);
            }

            return response()->json(['success' => false, 'data' => []]);

        } catch (\Exception $e) {
            Log::error('ChatController@apiChatList - ' . $e->getMessage());
            return response()->json(['success' => false, 'data' => []], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API PROXY: GET chat history  GET /api/chat
    // ─────────────────────────────────────────────────────────────────────────

    public function apiGetChat(Request $request)
    {
        $token      = session('token');
        $idPenerima = $request->query('id_penerima');
        $page       = $request->query('page', 1);

        if (!$token || !$idPenerima) {
            return response()->json(['success' => false, 'message' => 'Parameter tidak lengkap.'], 400);
        }

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->get("{$this->apiBaseUrl}/chat", [
                    'id_penerima' => $idPenerima,
                    'page'        => $page,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return response()->json([
                    'status'   => 'success',
                    'data'     => $data['data']     ?? [],
                    'has_more' => $data['has_more'] ?? false,
                ]);
            }

            return response()->json(['status' => 'error', 'data' => [], 'has_more' => false]);

        } catch (\Exception $e) {
            Log::error('ChatController@apiGetChat - ' . $e->getMessage());
            return response()->json(['status' => 'error', 'data' => []], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API PROXY: POST send message  POST /api/chat
    // ─────────────────────────────────────────────────────────────────────────

    public function apiSendChat(Request $request)
    {
        $token = session('token');
        if (!$token) return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->post("{$this->apiBaseUrl}/chat", $request->only([
                    'id_pengirim', 'id_penerima', 'pesan', 'is_read',
                ]));

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return response()->json(['status' => 'success', 'chat' => $data['data'] ?? $data['chat'] ?? null]);
            }

            return response()->json(['status' => 'error', 'message' => $data['message'] ?? 'Gagal mengirim pesan.'], 422);

        } catch (\Exception $e) {
            Log::error('ChatController@apiSendChat - ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server error'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API PROXY: GET unread count  GET /api/unread-count
    // Already handled in HomeController@unreadCount — kept here for reference
    // ─────────────────────────────────────────────────────────────────────────
}