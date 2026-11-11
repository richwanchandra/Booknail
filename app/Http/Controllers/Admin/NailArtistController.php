<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NailArtist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class NailArtistController extends Controller
{
    public function index()
    {
        $artists = NailArtist::with('user', 'schedules')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('id')
            ->paginate(12);

        return view('admin.artists.index', compact('artists'));
    }

    public function create()
    {
        $existingArtistUserIds = NailArtist::pluck('user_id');
        $availableUsers = User::whereNotIn('id', $existingArtistUserIds)->orderBy('name')->get();

        return view('admin.artists.form', ['artist' => null, 'availableUsers' => $availableUsers]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id|unique:nail_artists,user_id',
            'artist_name' => 'required_without:user_id|nullable|string|max:100',
            'artist_username' => 'nullable|string|max:50|unique:users,username',
            'artist_email' => 'required_without:user_id|nullable|email|unique:users,email',
            'artist_password' => 'nullable|string|min:8',
            'artist_phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:2000',
            'specialization' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'artist_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $artistRole = Role::firstOrCreate(['name' => 'artist', 'guard_name' => 'web']);
            $userId = $v['user_id'] ?? null;

            if (! $userId) {
                $rawPassword = ! empty($v['artist_password']) ? $v['artist_password'] : 'Artist12345!';
                $name = ! empty($v['artist_name']) ? $v['artist_name'] : (! empty($v['artist_username']) ? $v['artist_username'] : 'Nail Artist');
                $username = ! empty($v['artist_username']) ? $v['artist_username'] : strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name)).rand(10, 99);

                $user = User::create([
                    'username' => $username,
                    'name' => $name,
                    'email' => $v['artist_email'] ?? ($username.'@nailglam.local'),
                    'phone' => $v['artist_phone'] ?? null,
                    'password' => Hash::make($rawPassword),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole($artistRole);
                $userId = $user->id;
            } else {
                $user = User::findOrFail($userId);
                if (! $user->hasRole('artist')) {
                    $user->assignRole($artistRole);
                }
            }

            $photoBase64 = null;
            if ($request->hasFile('artist_image')) {
                $file = $request->file('artist_image');
                $mime = $file->getMimeType() ?: 'image/jpeg';
                $photoBase64 = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));
            }

            NailArtist::create([
                'user_id' => $userId,
                'bio' => $v['bio'] ?? null,
                'specialization' => $v['specialization'] ?? null,
                'hire_date' => $v['hire_date'] ?? null,
                'photo' => $photoBase64,
                'is_active' => (bool) ($request->has('is_active')),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['a' => $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.artists.index')->with('success', 'Akun Teknisi (Nail Artist) berhasil ditambahkan.');
    }

    public function edit(NailArtist $artist)
    {
        $availableUsers = User::where(function ($q) use ($artist) {
            $q->whereNotIn('id', NailArtist::where('id', '!=', $artist->id)->pluck('user_id'))
                ->orWhere('id', $artist->user_id);
        })->orderBy('name')->get();

        return view('admin.artists.form', compact('artist', 'availableUsers'));
    }

    public function update(Request $request, NailArtist $artist)
    {
        $v = $request->validate([
            'user_id' => 'required|integer|exists:users,id|unique:nail_artists,user_id,'.$artist->id,
            'artist_name' => 'nullable|string|max:100',
            'artist_email' => 'nullable|email|unique:users,email,'.$artist->user_id,
            'artist_password' => 'nullable|string|min:8',
            'artist_phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:2000',
            'specialization' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'artist_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Update credentials user jika diedit
            $user = User::findOrFail($v['user_id']);
            $userData = [];
            if (! empty($v['artist_name'])) {
                $userData['name'] = $v['artist_name'];
            }
            if (! empty($v['artist_email'])) {
                $userData['email'] = $v['artist_email'];
            }
            if (! empty($v['artist_phone'])) {
                $userData['phone'] = $v['artist_phone'];
            }
            if (! empty($v['artist_password'])) {
                $userData['password'] = Hash::make($v['artist_password']);
            }
            if (! empty($userData)) {
                $user->update($userData);
            }

            $artistRole = Role::firstOrCreate(['name' => 'artist', 'guard_name' => 'web']);
            if (! $user->hasRole('artist')) {
                $user->assignRole($artistRole);
            }

            $photoBase64 = $artist->photo;
            if ($request->hasFile('artist_image')) {
                $file = $request->file('artist_image');
                $mime = $file->getMimeType() ?: 'image/jpeg';
                $photoBase64 = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));
            }

            $artist->update([
                'user_id' => $v['user_id'],
                'bio' => $v['bio'] ?? null,
                'specialization' => $v['specialization'] ?? null,
                'hire_date' => $v['hire_date'] ?? null,
                'photo' => $photoBase64,
                'is_active' => (bool) ($request->has('is_active')),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['a' => 'Gagal memperbarui artist: '.$e->getMessage()]);
        }

        return redirect()->route('admin.artists.index')->with('success', 'Data & Hak Akses Akun Teknisi berhasil diperbarui.');
    }

    public function destroy(NailArtist $artist)
    {
        if ($artist->bookings()->count() > 0) {
            return back()->withErrors(['del' => 'Artist ini pernah menangani booking. Nonaktifkan saja (is_active=false) agar data riwayat terjaga.']);
        }
        if ($artist->artist_image && Storage::disk('public')->exists($artist->artist_image)) {
            Storage::disk('public')->delete($artist->artist_image);
        }
        $artist->schedules()->delete();
        $artist->leaves()->delete();
        $artist->delete();

        return redirect()->route('admin.artists.index')->with('success', 'Data teknisi dihapus.');
    }

    public function toggleStatus(NailArtist $artist)
    {
        $newStatus = ! $artist->is_active;
        $artist->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan kembali (On-Duty)' : 'dinonaktifkan (Off)';

        return back()->with('success', "Akun teknisi {$artist->user->name} berhasil {$statusText}.");
    }
}
