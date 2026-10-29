<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Proxy role Nexus (5) untuk kaitan nanny↔konsultan & assignment majikan-nanny.
 * Mirip KonsultanController tapi: view `nexus.*`, route `nexus-*`, dan
 * addNanny mewajibkan pemilihan konsultan (id_konsultan) — diverifikasi backend.
 */
class NexusKonsultanController extends Controller
{
    private function apiUrl(string $path): string
    {
        return rtrim(config('services.api.base_url', env('API_BASE_URL')), '/') . '/' . ltrim($path, '/');
    }

    private function token(): ?string
    {
        return session('token');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Nanny List (semua nanny, belum tentu dikaitkan)
    // ─────────────────────────────────────────────────────────────────────────

    public function indexNanny(Request $request)
    {
        $search = $request->input('search', '');

        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->post($this->apiUrl('/user-nanny'), [
                    'search' => $search,
                ]);

            $json    = $response->json();
            $nannies = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];

        } catch (\Throwable $e) {
            $nannies = [];
        }

        return view('nexus.nanny-list', compact('nannies'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Nanny Detail — tambah kaitan ke konsultan terpilih
    // ─────────────────────────────────────────────────────────────────────────

    public function showNanny(int $id)
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->get($this->apiUrl('/nanny-with-konsultan'), [
                    'id_nanny' => $id,
                ]);

            $json  = $response->json();
            $nanny = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? null) : null;

        } catch (\Throwable $e) {
            $nanny = null;
        }

        // Daftar konsultan untuk dropdown pemilihan pemilik kaitan (role 5).
        $konsultans = [];
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->post($this->apiUrl('/user-konsultan'), ['search' => '']);

            $json       = $response->json();
            $konsultans = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];

        } catch (\Throwable $e) {
            $konsultans = [];
        }

        return view('nexus.nanny-detail', compact('nanny', 'konsultans'));
    }

    /**
     * POST /nexus/nanny/add — kaitkan nanny dengan konsultan terpilih.
     */
    public function addNanny(Request $request)
    {
        $request->validate([
            'id_nanny'    => ['required', 'integer'],
            'id_konsultan' => ['required', 'integer'],
        ]);

        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->asJson()
                ->post($this->apiUrl('/konsultan-nanny'), [
                    'id_nanny'    => (int) $request->input('id_nanny'),
                    'id_konsultan' => (int) $request->input('id_konsultan'),
                ]);

            $json = $response->json();

            if (($json['status'] ?? '') === 'success') {
                return redirect()
                    ->route('nexus-nanny-anda')
                    ->with('success', 'Nanny successfully linked to the selected consultant.');
            }

            return redirect()
                ->back()
                ->with('error', is_array($json['message'] ?? null)
                    ? implode(' ', $json['message'])
                    : ($json['message'] ?? 'Failed to link nanny.'));

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * POST /nexus/nanny-anda/unlink — lepas kaitan nanny↔konsultan (untuk nexus).
     */
    public function unlinkNanny(Request $request)
    {
        $request->validate([
            'id_nanny' => ['required', 'integer'],
        ]);

        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->delete($this->apiUrl('/konsultan-nanny'), [
                    'id_nanny' => (int) $request->input('id_nanny'),
                ]);

            $json = $response->json();

            if (($json['status'] ?? '') === 'success') {
                return redirect()
                    ->route('nexus-nanny-anda')
                    ->with('success', 'Nanny successfully unlinked from the consultant.');
            }

            return redirect()
                ->back()
                ->with('error', $json['message'] ?? 'Failed to unlink nanny.');

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Nanny Anda (semua kaitan nanny↔konsultan, milik konsultan mana pun)
    // ─────────────────────────────────────────────────────────────────────────

    public function indexNannyAnda()
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->get($this->apiUrl('/konsultan-nanny'));

            $json    = $response->json();
            $nannies = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];

        } catch (\Throwable $e) {
            $nannies = [];
        }

        // Nama konsultan pemilik tiap kaitan (id_konsultan dari backend).
        $konsultanNames = [];
        $ids = collect($nannies)->pluck('id_konsultan')->filter()->unique();
        foreach ($ids as $kid) {
            try {
                $r = Http::withToken($this->token())->acceptJson()
                    ->get($this->apiUrl('/konsultan-user-detail'), ['id_user' => $kid]);
                $j = $r->json();
                if (($j['status'] ?? '') === 'success') {
                    $konsultanNames[$kid] = $j['data']['name'] ?? null;
                }
            } catch (\Throwable $e) {
                // lanjut tanpa nama
            }
        }

        return view('nexus.nanny-anda', compact('nannies', 'konsultanNames'));
    }

    public function showNannyAnda(int $id)
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->get($this->apiUrl('/konsultan-user-detail'), [
                    'id_user' => $id,
                ]);

            $json  = $response->json();
            $raw   = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? null) : null;

            $nanny = $raw ? array_merge($raw, [
                'id'        => (int) ($raw['id']        ?? 0),
                'id_user'   => (int) ($raw['id_user']   ?? 0),
                'is_active' => (int) ($raw['is_active'] ?? 1),
            ]) : null;

        } catch (\Throwable $e) {
            $nanny = null;
        }

        // Konsultan pemilik kaitan nanny ini.
        $konsultanName = null;
        try {
            $r = Http::withToken($this->token())->acceptJson()
                ->get($this->apiUrl('/konsultan-by-nanny'), ['id_nanny' => $id]);
            $j = $r->json();
            if (($j['status'] ?? '') === 'success') {
                $konsultanName = $j['data']['name'] ?? null;
            }
        } catch (\Throwable $e) {
            // lanjut tanpa nama
        }

        return view('nexus.nanny-anda-detail', compact('nanny', 'konsultanName'));
    }

    public function updateStatusNanny(Request $request)
    {
        $request->validate([
            'id'          => ['required', 'integer'],
            'is_active'   => ['required', 'in:0,1'],
            'redirect_id' => ['nullable', 'integer'],
        ]);

        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->asMultipart()
                ->post($this->apiUrl('/update-status-nanny'), [
                    ['name' => 'id',        'contents' => (string) $request->input('id')],
                    ['name' => 'is_active', 'contents' => (string) $request->input('is_active')],
                ]);

            $json   = $response->json();
            $active = (int) $request->input('is_active');

            if (($json['status'] ?? '') === 'success') {
                $redirectId = (int) ($request->input('redirect_id') ?: $request->input('id'));

                return redirect()
                    ->route('nexus-nanny-anda-detail', $redirectId)
                    ->with('success', 'Nanny account successfully ' . ($active === 1 ? 'activated' : 'deactivated') . '.');
            }

            return redirect()
                ->back()
                ->with('error', $json['message'] ?? 'Failed to update nanny status.');

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Majikan Nanny (semua assignment aktif, semua konsultan)
    // ─────────────────────────────────────────────────────────────────────────

    public function indexMajikanNanny()
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->get($this->apiUrl('/majikan-nanny-assignment'));

            $json        = $response->json();
            $assignments = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];

        } catch (\Throwable $e) {
            $assignments = [];
        }

        return view('nexus.majikan-nanny', compact('assignments'));
    }

    public function showMajikanNanny(int $idMajikan)
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->asMultipart()
                ->post($this->apiUrl('/majikan-nanny-assignment-detail'), [
                    ['name' => 'id_majikan', 'contents' => (string) $idMajikan],
                ]);

            $json       = $response->json();
            $assignment = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? null) : null;

        } catch (\Throwable $e) {
            $assignment = null;
        }

        return view('nexus.majikan-nanny-detail', compact('assignment'));
    }
}
