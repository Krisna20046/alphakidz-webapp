<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NexusController extends Controller
{
    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', env('API_BASE_URL', '')), '/');
    }

    public function index()
    {
        return view('nexus.index');
    }

    public function create()
    {
        return view('nexus.form');
    }

    public function store(Request $request)
    {
        $token = session('token');
        if (!$token) {
            return redirect()->route('login')->with('error', 'Sesi habis, silakan login ulang.');
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->post("{$this->apiBaseUrl}/nexus", [
                'judul' => $request->judul,
                'kategori' => $request->kategori,
            ]);

        $json = $response->json();

        if ($response->failed()) {
            return redirect()->route('nexus.create')
                ->with('error', $json['message'] ?? 'Gagal membuat pertanyaan.');
        }

        return redirect()->route('nexus.show', $json['data']['id'])
            ->with('success', 'Pertanyaan berhasil dibuat!');
    }

    public function show($id)
    {
        return view('nexus.show', compact('id'));
    }
}
