<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmController extends Controller
{
    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', env('API_BASE_URL', '')), '/');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE FCM TOKEN
    // POST /fcm/update-token
    // Dipanggil saat: login, app dibuka kembali, token di-refresh
    // ─────────────────────────────────────────────────────────────────────────

    public function updateToken(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'fcm_token'   => 'required|string',
            'device_type' => 'required|string|in:android,ios,web',
        ]);

        try {
            $response = Http::timeout(10)
                ->withToken($token)
                ->acceptJson()
                ->post("{$this->apiBaseUrl}/fcm/update-token", [
                    'fcm_token'   => $validated['fcm_token'],
                    'device_type' => $validated['device_type'],
                ]);

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'FCM token updated']);
            }

            Log::warning('FcmController@updateToken - API failed: ' . $response->status());
            return response()->json(['success' => false, 'message' => 'Failed to update token'], 500);

        } catch (\Exception $e) {
            Log::error('FcmController@updateToken - ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REMOVE FCM TOKEN
    // POST /fcm/remove-token
    // Dipanggil saat logout
    // ─────────────────────────────────────────────────────────────────────────

    public function removeToken(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        try {
            $response = Http::timeout(10)
                ->withToken($token)
                ->acceptJson()
                ->post("{$this->apiBaseUrl}/fcm/remove-token");

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'FCM token removed']);
            }

            Log::warning('FcmController@removeToken - API failed: ' . $response->status());
            return response()->json(['success' => false, 'message' => 'Failed to remove token'], 500);

        } catch (\Exception $e) {
            Log::error('FcmController@removeToken - ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
}