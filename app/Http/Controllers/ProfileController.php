<?php

// ==========================================================================
// ProfileController.php
// Controller untuk mengelola profil pengguna yang sedang login.
// Menangani tampilan form profil, pembaruan data profil,
// dan penghapusan akun pengguna.
// ==========================================================================

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan formulir edit profil pengguna.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna.
     * Jika email diubah, status verifikasi email akan direset.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Isi data pengguna dengan data yang sudah tervalidasi
        $request->user()->fill($request->validated());

        // Jika email berubah, reset status verifikasi agar perlu verifikasi ulang
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Simpan perubahan profil ke database
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun pengguna secara permanen.
     * Memvalidasi password terlebih dahulu sebelum menghapus.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi password pengguna sebelum menghapus akun
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Logout pengguna dari sesi saat ini
        Auth::logout();

        // Hapus akun pengguna dari database
        $user->delete();

        // Invalidasi sesi dan regenerasi token CSRF untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
