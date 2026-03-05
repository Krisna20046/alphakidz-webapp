<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', env('API_BASE_URL', '')), '/');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW VIEWS
    // ─────────────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        // Kalau sudah login, redirect ke dashboard
        if (session('token')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showRegister()
    {
        if (session('token')) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOGIN
    // ─────────────────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->post("{$this->apiBaseUrl}/login", [
                    'email'    => $request->email,
                    'password' => $request->password,
                ]);

            $data = $response->json();

            // API mengembalikan status 'success' seperti di React Native
            if ($response->successful() && isset($data['status']) && $data['status'] === 'success') {
                $token = $data['token'];

                // Simpan token & user data di session
                session([
                    'token'       => $token,
                    'user'        => $data['user'] ?? null,
                    'is_filled'   => $data['user']['is_filled'] ?? 0,
                    'user_id'     => $data['user']['id'] ?? null,
                ]);

                return response()->json([
                    'success'  => true,
                    'message'  => 'Login berhasil',
                    'redirect' => route('dashboard'),
                ]);
            }

            // Login gagal dari API
            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Email atau password salah.',
            ], 401);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Login - Connection error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke server. Periksa koneksi internet.',
            ], 503);
        } catch (\Exception $e) {
            Log::error('Login - Unexpected error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REGISTER
    // ─────────────────────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        // Validasi server-side (sebagai lapisan kedua setelah client-side)
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255',
            'password'              => [
                'required',
                'string',
                'min:8',
                'confirmed',         // membutuhkan password_confirmation
                'regex:/[A-Z]/',     // min 1 huruf kapital
                'regex:/[0-9]/',     // min 1 angka
            ],
            'password_confirmation' => 'required|string',
        ], [
            'name.required'           => 'Nama lengkap wajib diisi.',
            'email.required'          => 'Email wajib diisi.',
            'email.email'             => 'Format email tidak valid.',
            'password.required'       => 'Password wajib diisi.',
            'password.min'            => 'Password minimal 8 karakter.',
            'password.confirmed'      => 'Konfirmasi password tidak cocok.',
            'password.regex'          => 'Password harus mengandung minimal 1 huruf kapital dan 1 angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->post("{$this->apiBaseUrl}/register", [
                    'name'                  => $request->name,
                    'email'                 => $request->email,
                    'password'              => $request->password,
                    'password_confirmation' => $request->password_confirmation,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => $data['message'] ?? 'Registrasi berhasil! Silakan masuk.',
                ]);
            }

            // Error dari API (misal: email sudah terdaftar)
            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Gagal registrasi.',
                'errors'  => $data['errors'] ?? null,
            ], $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Register - Connection error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke server. Periksa koneksi internet.',
            ], 503);
        } catch (\Exception $e) {
            Log::error('Register - Unexpected error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOGOUT
    // ─────────────────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        $token = session('token');

        if ($token) {
            try {
                // Opsional: hit endpoint logout di API jika ada
                Http::timeout(10)
                    ->withToken($token)
                    ->post("{$this->apiBaseUrl}/logout");
            } catch (\Exception $e) {
                Log::warning('Logout - API call failed: ' . $e->getMessage());
            }
        }

        // Hapus semua session
        $request->session()->flush();

        return redirect()->route('login');
    }

    public function storeToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|min:10',
        ]);

        session(['token' => $request->token]);

        return response()->json(['ok' => true]);
    }
}