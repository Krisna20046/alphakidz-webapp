<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MajikanParentCommentController extends Controller
{
    /**
     * Proxy untuk Modul 11 — Parent Comments & Approval (role Majikan).
     * Majikan (role 2) bisa meninjau & menyetujui/menolak tugas akademik anaknya,
     * serta membaca riwayat approval/komentar. Token tetap tersimpan di session
     * (tidak diekspos ke browser), mengikuti pola proxy controller yang sudah ada.
     *
     * Backend (AlphaKidz-Backend ParentCommentController):
     *   POST parent-comments/approve  { id_anak, task_id, comment? }
     *   POST parent-comments/reject   { id_anak, task_id, comment (wajib) }
     *   POST parent-comments          { id_anak, task_id?, comment }
     *   GET  parent-comments?per_page&id_anak&task_id
     *   GET  parent-comments/{id}
     */

    private function apiUrl(string $path = ''): string
    {
        $base = rtrim(config('services.api.base_url', env('API_BASE_URL', 'http://localhost:8000/api')), '/');
        return $base . '/' . ltrim($path, '/');
    }

    private function headers(): array
    {
        return [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . session('token'),
        ];
    }

    private function isSuccess(array $json): bool
    {
        return ($json['success'] ?? null) === true || ($json['status'] ?? '') === 'success';
    }

    private function extractMessage($response): string
    {
        $json = $response->json();
        if (is_array($json)) {
            if (!empty($json['errors']) && is_array($json['errors'])) {
                $first = reset($json['errors']);
                return is_array($first) ? (string) reset($first) : (string) $first;
            }
            if (!empty($json['message'])) {
                return (string) $json['message'];
            }
        }
        return 'Something went wrong on the server.';
    }

    /**
     * Daftar anak milik majikan (untuk selector).
     * Response backend (getUserAnakByMajikan):
     *   { status:'success', data:[ { id, nama, foto, gender, ... } ] }
     */
    private function fetchChildren(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/user-anak-by-majikan'));

        if (!$response->successful()) {
            return [];
        }
        $body = $response->json();
        $data = $body['data'] ?? [];
        return is_array($data) && $this->isSuccess($body) ? $data : [];
    }

    private function fetchChildTasks(int $idAnak): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/academic-tasks'));

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return [];
        }
        return is_array($data) && array_key_exists('data', $data) ? (is_array($data['data']) ? $data['data'] : []) : $data;
    }

    /**
     * Riwayat komentar/approval untuk satu anak (per task). Backend paginated.
     */
    private function fetchApprovalHistory(int $idAnak, int $perPage = 50): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/parent-comments'), [
                'id_anak' => $idAnak,
                'per_page' => $perPage,
            ]);

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return [];
        }
        return is_array($data) && array_key_exists('data', $data) ? (is_array($data['data']) ? $data['data'] : []) : $data;
    }

    private function fetchChildName(int $idAnak): string
    {
        foreach ($this->fetchChildren() as $c) {
            if ((int) ($c['id'] ?? 0) === $idAnak) {
                return (string) ($c['nama'] ?? 'Child');
            }
        }
        return 'Child';
    }

    // ─── Index: pilih anak ────────────────────────────────────────────────────

    public function index()
    {
        $anakList = $this->fetchChildren();
        return view('majikan.approval.index', compact('anakList'));
    }

    // ─── Show: tasks + tombol approve/reject + riwayat ────────────────────────

    public function show(Request $request, int $idAnak)
    {
        $tasks     = $this->fetchChildTasks($idAnak);
        $history   = $this->fetchApprovalHistory($idAnak);
        $namaAnak  = $this->fetchChildName($idAnak);

        // Kelompokkan riwayat approval/komentar per task_id utk badge status
        $historyByTask = [];
        foreach ($history as $h) {
            if (!empty($h['task_id'])) {
                $historyByTask[(int) $h['task_id']] = $h; // riwayat terbaru menang (backend urut desc)
            }
        }

        return view('majikan.approval.show', [
            'tasks'         => $tasks,
            'history'       => $history,
            'historyByTask' => $historyByTask,
            'namaAnak'      => $namaAnak,
            'idAnak'        => $idAnak,
            'anakList'      => $this->fetchChildren(),
        ]);
    }

    // ─── Actions (POST) ───────────────────────────────────────────────────────

    public function approve(Request $request)
    {
        $request->validate([
            'id_anak'  => 'required|integer',
            'task_id'  => 'required|integer',
            'comment'  => 'nullable|string',
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/parent-comments/approve'), [
                'id_anak' => $request->id_anak,
                'task_id' => $request->task_id,
                'comment' => $request->comment ?? '',
            ]);

        return $this->redirectApproval($request, $response, 'Berhasil menyetujui tugas.');
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id_anak'  => 'required|integer',
            'task_id'  => 'required|integer',
            'comment'  => 'required|string',
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/parent-comments/reject'), [
                'id_anak' => $request->id_anak,
                'task_id' => $request->task_id,
                'comment' => $request->comment,
            ]);

        return $this->redirectApproval($request, $response, 'Tugas ditolak.');
    }

    public function comment(Request $request)
    {
        $request->validate([
            'id_anak'  => 'required|integer',
            'task_id'  => 'nullable|integer',
            'comment'  => 'required|string',
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/parent-comments'), [
                'id_anak' => $request->id_anak,
                'task_id' => $request->task_id,
                'comment' => $request->comment,
            ]);

        return $this->redirectApproval($request, $response, 'Komentar berhasil ditambahkan.');
    }

    private function redirectApproval(Request $request, $response, string $successMsg)
    {
        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('majikan-approval-show', $request->id_anak)
                ->with('success', $successMsg);
        }
        return redirect()->route('majikan-approval-show', $request->id_anak)
            ->with('error', $this->extractMessage($response));
    }
}
