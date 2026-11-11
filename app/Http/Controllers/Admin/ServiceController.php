<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')->orderBy('category_id')->orderBy('name')->paginate(15);

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.services.form', ['service' => null, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|integer|min:0',
            'duration_minutes' => 'required|integer|min:10|max:720',
            'description' => 'nullable|string|max:2000',
            'service_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        $base64Image = null;
        if ($request->hasFile('service_image')) {
            $file = $request->file('service_image');
            $mime = $file->getMimeType() ?: 'image/jpeg';
            $base64Image = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));
        }

        Service::create([
            'name' => $v['name'],
            'category_id' => $v['category_id'],
            'price' => (int) $v['price'],
            'duration_minutes' => (int) $v['duration_minutes'],
            'description' => $v['description'] ?? null,
            'image' => $base64Image,
            'is_active' => (bool) ($v['is_active'] ?? true),
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dibuat (gambar disimpan sebagai Base64).');
    }

    public function edit(Service $service)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.services.form', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $v = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|integer|min:0',
            'duration_minutes' => 'required|integer|min:10|max:720',
            'description' => 'nullable|string|max:2000',
            'service_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        $base64Image = $service->image;
        if ($request->hasFile('service_image')) {
            // Hapus file fisik lama jika sebelumnya tersimpan di storage lokal (bukan base64)
            if ($base64Image && !str_starts_with($base64Image, 'data:image') && Storage::disk('public')->exists($base64Image)) {
                Storage::disk('public')->delete($base64Image);
            }

            $file = $request->file('service_image');
            $mime = $file->getMimeType() ?: 'image/jpeg';
            $base64Image = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));
        }

        $service->update([
            'name' => $v['name'],
            'category_id' => $v['category_id'],
            'price' => (int) $v['price'],
            'duration_minutes' => (int) $v['duration_minutes'],
            'description' => $v['description'] ?? null,
            'image' => $base64Image,
            'is_active' => (bool) ($request->has('is_active')),
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Layanan diperbarui (gambar disimpan sebagai Base64).');
    }

    public function destroy(Service $service)
    {
        if ($service->bookingDetails()->count() > 0) {
            return back()->withErrors(['del' => 'Layanan ini pernah dipesan. Nonaktifkan saja (is_active=false) agar riwayat tidak hilang.']);
        }
        if ($service->image && !str_starts_with($service->image, 'data:image') && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Layanan dihapus.');
    }
}
