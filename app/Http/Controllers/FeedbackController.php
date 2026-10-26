<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    private function apiUrl(string $path): string
    {
        return rtrim(config('services.api.base_url', env('API_BASE_URL', 'http://localhost:8000/api')), '/') . '/' . $path;
    }

    public function index()
    {
        return view('feedback.index', ['adminView' => false]);
    }

    public function adminIndex(Request $request)
    {
        abort_unless((int) session('user.id_role') === 1, 403);
        $allFeedbacks = [];
        $pagination = [];
        $error = null;

        try {
            $response = Http::withToken(session('token'))->acceptJson()->timeout(20)
                ->get($this->apiUrl('feedbacks'), ['page' => max(1, (int) $request->input('page', 1))]);
            if ($response->successful() && $response->json('success')) {
                $allFeedbacks = $response->json('data') ?? [];
                $pagination = $response->json('pagination') ?? [];
            } else {
                $error = 'Gagal memuat laporan. Silakan coba kembali.';
            }
        } catch (ConnectionException $e) {
            Log::warning('Feedback API unavailable: ' . $e->getMessage());
            $error = 'Server laporan tidak dapat dihubungi.';
        }

        return view('feedback.index', compact('allFeedbacks', 'pagination', 'error') + ['adminView' => true]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:feedback,bug_report',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        try {
            // Multipart agar screenshot ikut terkirim ke API backend.
            $multipart = [
                ['name' => 'type', 'contents' => $data['type']],
                ['name' => 'title', 'contents' => $data['title']],
                ['name' => 'description', 'contents' => $data['description']],
            ];
            if ($request->hasFile('screenshot')) {
                $file = $request->file('screenshot');
                $multipart[] = [
                    'name'     => 'screenshot',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ];
            }

            $response = Http::withToken(session('token'))->acceptJson()->timeout(30)
                ->asMultipart()
                ->post($this->apiUrl('feedbacks'), $multipart);
            if ($response->successful() && $response->json('success')) {
                return redirect()->route('feedback.index')->with('success', 'Laporan berhasil dikirim ke admin.');
            }
            if ($response->status() === 422) {
                return back()->withErrors($response->json('errors') ?? [
                    'feedback' => 'Data laporan tidak valid.',
                ])->withInput();
            }
        } catch (ConnectionException $e) {
            Log::warning('Feedback submission failed: ' . $e->getMessage());
        }

        return back()->with('error', 'Laporan gagal dikirim. Silakan coba kembali.')->withInput();
    }

    public function resolve(Request $request, int $id)
    {
        abort_unless((int) session('user.id_role') === 1, 403);
        $data = $request->validate(['action' => 'required|in:resolved,reopen']);

        try {
            $response = Http::withToken(session('token'))->acceptJson()->timeout(20)
                ->post($this->apiUrl('feedbacks/' . $id . '/resolve'), $data);
            if ($response->successful() && $response->json('success')) {
                return back()->with('success', 'Status laporan diperbarui.');
            }
        } catch (ConnectionException $e) {
            Log::warning('Feedback status update failed: ' . $e->getMessage());
        }

        return back()->with('error', 'Status laporan gagal diperbarui.');
    }
}
