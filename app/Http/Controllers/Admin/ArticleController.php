<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index()
    {
        return view('admin.articles.index', [
            'artikel' => Article::with('penulis')->orderByDesc('created_at')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.articles.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Article::create([
            'user_id'      => session('user_id'),
            'judul'        => $data['judul'],
            'slug'         => Article::buatSlug($data['judul']),
            'konten'       => $data['konten'],
            'kategori'     => $data['kategori'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil disimpan.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.form', ['editArtikel' => $article]);
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->validated($request);

        $article->update([
            'judul'        => $data['judul'],
            'slug'         => Article::buatSlug($data['judul'], $article->id),
            'konten'       => $data['konten'],
            'kategori'     => $data['kategori'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil diupdate.');
    }

    public function toggle(Article $article)
    {
        $article->update(['is_published' => ! $article->is_published]);

        return redirect()->route('admin.artikel.index')->with('success', 'Status artikel diubah.');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'judul'    => 'required|max:200',
            'konten'   => 'required',
            'kategori' => ['required', Rule::in(array_keys(Article::KATEGORI))],
        ]);
    }
}
