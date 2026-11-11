<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtistSchedule;
use App\Models\NailArtist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArtistScheduleController extends Controller
{
    public function index(Request $request)
    {
        $artistId = $request->get('artist_id');
        $artists = NailArtist::with('user')->active()->orderBy('id')->get();

        $schedules = ArtistSchedule::when($artistId, fn ($q) => $q->where('artist_id', $artistId))
            ->with('artist.user')
            ->orderBy('artist_id')
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('artist_id');

        return view('admin.schedules.index', compact('artists', 'schedules', 'artistId'));
    }

    public function bulkStore(Request $request)
    {
        $v = $request->validate([
            'artist_id' => 'required|integer|exists:nail_artists,id',
            'schedules' => 'required|array|size:7',
            'schedules.*.day_of_week' => 'required|integer|between:0,6',
            'schedules.*.is_off' => 'nullable|boolean',
            'schedules.*.start_time' => 'nullable|date_format:H:i,H:i:s',
            'schedules.*.end_time' => 'nullable|date_format:H:i,H:i:s',
        ]);

        DB::beginTransaction();
        try {
            ArtistSchedule::where('artist_id', $v['artist_id'])->delete();
            foreach ($v['schedules'] as $row) {
                $isOff = (bool) ($row['is_off'] ?? false);
                ArtistSchedule::create([
                    'artist_id' => $v['artist_id'],
                    'day_of_week' => (int) $row['day_of_week'],
                    'is_off' => $isOff,
                    'start_time' => $isOff ? '00:00:00' : ($row['start_time'] ?? '09:00:00'),
                    'end_time' => $isOff ? '00:00:00' : ($row['end_time'] ?? '17:00:00'),
                ]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['sch' => $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.schedules.index', ['artist_id' => $v['artist_id']])->with('success', 'Jadwal 7 hari berhasil disimpan.');
    }
}
