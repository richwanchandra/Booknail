<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->withCount('services')->paginate(12);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => null]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'slug' => 'nullable|string|max:120|unique:categories,slug',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        Category::create([
            'name' => $v['name'],
            'slug' => $v['slug'] ?? Str::slug($v['name']),
            'description' => $v['description'] ?? null,
            'is_active' => (bool) ($v['is_active'] ?? true),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $v = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,'.$category->id,
            'slug' => 'nullable|string|max:120|unique:categories,slug,'.$category->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $v['name'],
            'slug' => $v['slug'] ?? Str::slug($v['name']),
            'description' => $v['description'] ?? null,
            'is_active' => (bool) ($v['is_active'] ?? $request->has('is_active')),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->services()->count() > 0) {
            return back()->withErrors(['cat' => 'Kategori masih memiliki layanan. Hapus layanan terlebih dahulu.']);
        }
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori dihapus.');
    }
}
