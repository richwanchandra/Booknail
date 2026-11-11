<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtistLeave;
use App\Models\NailArtist;
use Illuminate\Http\Request;

class ArtistLeaveController extends Controller
{
    public function index(Request $request)
    {
        $artistId = $request->get('artist_id');
        $leaves = ArtistLeave::with('artist.user')
            ->when($artistId, fn ($q) => $q->where('artist_id', $artistId))
            ->orderBy('leave_date', 'desc')
            ->paginate(15);
        $artists = NailArtist::with('user')->active()->orderBy('id')->get();

        return view('admin.leaves.index', compact('leaves', 'artists', 'artistId'));
    }

    public function create()
    {
        $artists = NailArtist::with('user')->active()->orderBy('id')->get();

        return view('admin.leaves.form', ['leave' => null, 'artists' => $artists]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'artist_id' => 'required|integer|exists:nail_artists,id',
            'leave_date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:500',
            'leave_type' => 'nullable|in:annual,sick,personal,other',
            'is_paid' => 'nullable|boolean',
        ]);

        ArtistLeave::updateOrCreate(
            ['artist_id' => $v['artist_id'], 'leave_date' => $v['leave_date']],
            [
                'reason' => $v['reason'] ?? null,
                'leave_type' => $v['leave_type'] ?? 'personal',
                'is_paid' => (bool) ($v['is_paid'] ?? true),
            ]
        );

        return redirect()->route('admin.leaves.index')->with('success', 'Cuti berhasil dicatat.');
    }

    public function destroy(ArtistLeave $leave)
    {
        $leave->delete();

        return redirect()->route('admin.leaves.index')->with('success', 'Cuti dihapus.');
    }
}
