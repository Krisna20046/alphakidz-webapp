<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ArtikelController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 4;
        $page = $request->input('page', 1);

        $response = Http::get('https://pnpro.id/wp-json/wp/v2/posts', [
            'per_page' => $perPage,
            'page' => $page,
            '_embed' => 'wp:featuredmedia',
        ]);

        if ($response->successful()) {
            $posts = $response->collect();
            $totalPosts = $response->header('X-WP-Total', 0);

            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $posts,
                (int) $totalPosts,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('artikel.index', compact('paginator'));
        }

        // Fallback jika API gagal
        $paginator = collect([]);
        return view('artikel.index', compact('paginator'));
    }

    public function show($id)
    {
        $response = Http::get("https://pnpro.id/wp-json/wp/v2/posts/{$id}", [
            '_embed' => 'wp:featuredmedia',
        ]);

        if ($response->successful()) {
            $post = $response->json();

            // Ambil 3 artikel terkait (kecuali yang sedang dibaca)
            $relatedResponse = Http::get('https://pnpro.id/wp-json/wp/v2/posts', [
                'per_page' => 3,
                'exclude' => [$id],
                '_embed' => 'wp:featuredmedia',
            ]);
            $relatedPosts = $relatedResponse->successful() ? $relatedResponse->collect() : collect([]);

            return view('artikel.show', compact('post', 'relatedPosts'));
        }

        // Fallback jika post tidak ditemukan
        abort(404, 'Artikel tidak ditemukan');
    }
}
