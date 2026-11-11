<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Portfolio;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the portfolio items (CRUD Main Page).
     */
    public function index(Request $request)
    {
        $query = Portfolio::with(['category', 'nailArtist.user', 'service']);

        // Search filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%")
                  ->orWhereHas('nailArtist.user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function ($q3) use ($search) {
                      $q3->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Nail artist filter
        if ($request->filled('nail_artist_id')) {
            $query->where('nail_artist_id', $request->nail_artist_id);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'newest');
        if ($sortBy === 'order') {
            $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $portfolios = $query->paginate(12)->withQueryString();

        // Data for filters and statistics
        $categories = Category::orderBy('name')->get();
        $artists = NailArtist::with('user')->get();

        $stats = [
            'total' => Portfolio::count(),
            'active' => Portfolio::where('is_active', true)->count(),
            'inactive' => Portfolio::where('is_active', false)->count(),
            'categories_count' => Category::whereHas('services')->count(),
        ];

        return view('admin.portfolios.index', compact('portfolios', 'categories', 'artists', 'stats'));
    }

    /**
     * Show the form for creating a new portfolio item.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $artists = NailArtist::with('user')->active()->get();
        $services = Service::active()->orderBy('name')->get();

        return view('admin.portfolios.form', [
            'portfolio' => null,
            'categories' => $categories,
            'artists' => $artists,
            'services' => $services,
        ]);
    }

    /**
     * Store a newly created portfolio item in storage.
     */
    public function store(Request $request)
    {
        $v = $request->validate([
            'title' => 'required|string|max:200',
            'category_id' => 'nullable|integer|exists:categories,id',
            'nail_artist_id' => 'nullable|integer|exists:nail_artists,id',
            'service_id' => 'nullable|integer|exists:services,id',
            'portfolio_image' => 'required_without:image_url|nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'image_url' => 'nullable|string|url|max:2000',
            'description' => 'nullable|string|max:2000',
            'tags' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $image = null;
        if ($request->hasFile('portfolio_image')) {
            $file = $request->file('portfolio_image');
            $mime = $file->getMimeType() ?: 'image/jpeg';
            $image = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));
        } elseif ($request->filled('image_url')) {
            $image = $request->image_url;
        }

        Portfolio::create([
            'title' => $v['title'],
            'category_id' => $v['category_id'] ?? null,
            'nail_artist_id' => $v['nail_artist_id'] ?? null,
            'service_id' => $v['service_id'] ?? null,
            'image' => $image,
            'description' => $v['description'] ?? null,
            'tags' => $v['tags'] ?? null,
            'sort_order' => (int) ($v['sort_order'] ?? 0),
            'is_active' => (bool) ($request->has('is_active') ? $request->is_active : true),
        ]);

        return redirect()->route('admin.portfolios.index')
            ->with('success', 'Karya portofolio berhasil ditambahkan ke galeri.');
    }

    /**
     * Show the form for editing the specified portfolio item.
     */
    public function edit(Portfolio $portfolio)
    {
        $categories = Category::orderBy('name')->get();
        $artists = NailArtist::with('user')->get();
        $services = Service::orderBy('name')->get();

        return view('admin.portfolios.form', compact('portfolio', 'categories', 'artists', 'services'));
    }

    /**
     * Update the specified portfolio item in storage.
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        $v = $request->validate([
            'title' => 'required|string|max:200',
            'category_id' => 'nullable|integer|exists:categories,id',
            'nail_artist_id' => 'nullable|integer|exists:nail_artists,id',
            'service_id' => 'nullable|integer|exists:services,id',
            'portfolio_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'image_url' => 'nullable|string|url|max:2000',
            'description' => 'nullable|string|max:2000',
            'tags' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $image = $portfolio->image;

        if ($request->hasFile('portfolio_image')) {
            // Delete old physical file if exists
            if ($image && !str_starts_with($image, 'data:image') && Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }
            $file = $request->file('portfolio_image');
            $mime = $file->getMimeType() ?: 'image/jpeg';
            $image = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));
        } elseif ($request->filled('image_url')) {
            $image = $request->image_url;
        }

        $portfolio->update([
            'title' => $v['title'],
            'category_id' => $v['category_id'] ?? null,
            'nail_artist_id' => $v['nail_artist_id'] ?? null,
            'service_id' => $v['service_id'] ?? null,
            'image' => $image,
            'description' => $v['description'] ?? null,
            'tags' => $v['tags'] ?? null,
            'sort_order' => (int) ($v['sort_order'] ?? 0),
            'is_active' => (bool) ($request->has('is_active')),
        ]);

        return redirect()->route('admin.portfolios.index')
            ->with('success', 'Karya portofolio berhasil diperbarui.');
    }

    /**
     * Remove the specified portfolio item from storage.
     */
    public function destroy(Portfolio $portfolio)
    {
        if ($portfolio->image && !str_starts_with($portfolio->image, 'data:image') && Storage::disk('public')->exists($portfolio->image)) {
            Storage::disk('public')->delete($portfolio->image);
        }

        $portfolio->delete();

        return redirect()->route('admin.portfolios.index')
            ->with('success', 'Karya portofolio berhasil dihapus dari galeri.');
    }

    /**
     * Toggle active/inactive status of the portfolio item.
     */
    public function toggleStatus(Portfolio $portfolio)
    {
        $portfolio->update([
            'is_active' => !$portfolio->is_active,
        ]);

        $statusText = $portfolio->is_active ? 'diaktifkan' : 'dinonaktifkan';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $portfolio->is_active,
                'message' => "Portofolio berhasil {$statusText}.",
            ]);
        }

        return back()->with('success', "Portofolio berhasil {$statusText}.");
    }
}
