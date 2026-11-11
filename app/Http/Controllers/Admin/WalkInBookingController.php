<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Services\SlotAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalkInBookingController extends Controller
{
    public function create()
    {
        // Hanya cabang aktif yang dapat dipilih (opsi Citra Garden 6 telah dihapus dari pilihan)
        $branches = ['Metland Puri'];

        $categories = Category::active()
            ->with(['services' => fn ($q) => $q->active()->orderBy('name')])
            ->orderBy('name')
            ->get();

        $services = Service::active()
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $artists = NailArtist::active()
            ->with(['user', 'schedules'])
            ->get();

        $registeredCustomers = User::whereDoesntHave('roles', fn ($q) => $q->where('name', 'admin'))
            ->orderBy('name')
            ->select('id', 'name', 'phone', 'email', 'loyalty_points')
            ->get();

        $defaultDate = request('date', today()->format('Y-m-d'));
        $defaultTime = '11:00';

        $dateOptions = collect(range(0, 6))->map(function ($offset) use ($defaultDate) {
            $date = Carbon::parse(today())->addDays($offset);

            return [
                'date' => $date->format('Y-m-d'),
                'dayLabel' => strtoupper(substr($date->locale('id')->isoFormat('ddd'), 0, 3)),
                'dayNumber' => $date->format('d'),
                'monthLabel' => $date->locale('id')->isoFormat('MMM'),
                'active' => $date->format('Y-m-d') === $defaultDate,
            ];
        })->all();

        $slotService = app(SlotAvailabilityService::class);
        $artistSlots = [];
        $artistSummaries = [];
        $defaultServiceDuration = $services->first()?->duration_minutes ?? 90;

        $existingBookings = Booking::with('customer')
            ->where('booking_date', $defaultDate)
            ->whereIn('status', ['confirmed', 'in_progress', 'completed', 'pending_payment'])
            ->get();

        // Jam operasional studio salon: 11:00 sampai 19:00 WIB
        $studioOpen = '11:00:00';
        $studioClose = '19:00:00';

        foreach ($artists as $artist) {
            $artistSlots[$artist->id] = [];

            // Inisial foto / avatar
            $name = $artist->user->name ?? 'Nail Artist';
            $initials = strtoupper(substr($name, 0, 1));
            $parts = explode(' ', $name);
            if (count($parts) > 1) {
                $initials .= strtoupper(substr($parts[1], 0, 1));
            }
            $artist->initials = $initials;
            $artist->specialty = $artist->bio ?: 'Nail Artist · All Nail Art';
            $artist->avg_rating = '4.8';
            $artist->review_count = 0;

            $schedule = $artist->schedules
                ->first(fn ($item) => (int) $item->day_of_week === Carbon::parse($defaultDate)->dayOfWeek);

            // Jika secara eksplisit terapis diatur libur (is_off = true)
            if ($schedule && $schedule->is_off) {
                $artistSummaries[$artist->id] = [
                    'total' => 0,
                    'active' => 0,
                    'booked' => 0,
                    'available' => 0,
                    'text' => 'Libur / Tidak Ada Jadwal',
                    'has_available' => false,
                ];
                continue;
            }

            // Jam operasional salon: 11:00 sampai 19:00 WIB
            $shiftStart = Carbon::parse("{$defaultDate} {$studioOpen}");
            $shiftEnd = Carbon::parse("{$defaultDate} {$studioClose}");
            $cursor = $shiftStart->copy();

            $basePrice = 220000;
            if ($services->isNotEmpty() && $services->first()->price > 0) {
                $basePrice = (int) $services->first()->price;
            }

            // Generate jam slot waktu mulai 11:00 sampai maksimal jam selesai 19:00
            $startTimes = ['11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '17:30'];

            foreach ($startTimes as $sTime) {
                $startCarbon = Carbon::parse("{$defaultDate} {$sTime}:00");
                $slotEndCarbon = $startCarbon->copy()->addMinutes($defaultServiceDuration);

                // Slot tidak boleh melewati jam tutup operasional (19:00 WIB)
                if ($slotEndCarbon->gt($shiftEnd)) {
                    continue;
                }

                $slotStartStr = $startCarbon->format('H:i:s');
                $slotEndStr = $slotEndCarbon->format('H:i:s');

                $bookingOverlap = $existingBookings->first(function ($b) use ($artist, $slotStartStr, $slotEndStr) {
                    return (int) $b->artist_id === (int) $artist->id
                        && $b->start_time < $slotEndStr
                        && $b->end_time > $slotStartStr;
                });

                $status = 'available';
                $customerName = null;
                $bookingCode = null;

                if ($bookingOverlap) {
                    $status = 'booked';
                    $customerName = $bookingOverlap->customer_name ?: ($bookingOverlap->customer?->name ?? 'Pelanggan');
                    $bookingCode = $bookingOverlap->booking_code;
                }

                $slotId = 'slot_' . $artist->id . '_' . str_replace(':', '', $sTime);

                $artistSlots[$artist->id][] = [
                    'id' => $slotId,
                    'value' => $sTime,
                    'start_time' => $sTime,
                    'end_time' => $slotEndCarbon->format('H:i'),
                    'display' => $sTime . ' – ' . $slotEndCarbon->format('H:i'),
                    'price' => $basePrice,
                    'formatted_price' => 'Rp ' . number_format($basePrice, 0, ',', '.'),
                    'status' => $status,
                    'customer_name' => $customerName,
                    'booking_code' => $bookingCode,
                    'reason' => null,
                ];
            }

            $totalSlots = count($artistSlots[$artist->id]);
            $bookedSlots = collect($artistSlots[$artist->id])->where('status', 'booked')->count();
            $availableSlots = collect($artistSlots[$artist->id])->where('status', 'available')->count();
            $activeSlots = $totalSlots - collect($artistSlots[$artist->id])->where('status', 'inactive')->count();

            $artistSummaries[$artist->id] = [
                'total' => $totalSlots,
                'active' => $activeSlots,
                'booked' => $bookedSlots,
                'available' => $availableSlots,
                'text' => "{$activeSlots} Slot Aktif / {$bookedSlots} Terisi",
                'has_available' => $availableSlots > 0,
            ];
        }

        return view('admin.bookings.walk-in', compact(
            'branches',
            'categories',
            'services',
            'artists',
            'registeredCustomers',
            'defaultDate',
            'defaultTime',
            'dateOptions',
            'artistSlots',
            'artistSummaries',
            'defaultServiceDuration'
        ));
    }

    public function toggleSlot(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Status slot jam berhasil diperbarui.',
            'new_status' => $request->status,
        ]);
    }

    public function updatePrice(Request $request)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tarif slot jam berhasil diubah.',
            'price' => (int) $validated['price'],
            'formatted_price' => 'Rp ' . number_format((int) $validated['price'], 0, ',', '.'),
        ]);
    }

    public function addSlot(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'required|integer|exists:nail_artists,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'price' => 'nullable|numeric|min:0',
        ]);

        $price = (int) ($validated['price'] ?? 220000);

        return response()->json([
            'status' => 'success',
            'message' => 'Slot jam baru berhasil ditambahkan.',
            'slot' => [
                'id' => 'slot_' . $validated['artist_id'] . '_' . str_replace(':', '', $validated['start_time']),
                'value' => $validated['start_time'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'display' => $validated['start_time'] . ' – ' . $validated['end_time'],
                'price' => $price,
                'formatted_price' => 'Rp ' . number_format($price, 0, ',', '.'),
                'status' => 'available',
                'customer_name' => null,
            ],
        ]);
    }

    public function deleteSlot(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Slot jam berhasil dihapus dari daftar.',
        ]);
    }

    public function checkSlot(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'required|integer|exists:nail_artists,id',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'duration_minutes' => 'required|integer|min:15',
        ]);

        $slotService = app(SlotAvailabilityService::class);
        $result = $slotService->check(
            (int) $validated['artist_id'],
            $validated['booking_date'],
            $validated['start_time'],
            (int) $validated['duration_minutes']
        );

        $endCarbon = Carbon::parse("{$validated['booking_date']} {$validated['start_time']}")
            ->addMinutes((int) $validated['duration_minutes']);

        return response()->json([
            'available' => $result['available'],
            'reason' => $result['reason'],
            'start_time' => substr($validated['start_time'], 0, 5),
            'end_time' => $endCarbon->format('H:i'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch' => 'required|string|in:Citra Garden 6,Metland Puri',
            'customer_type' => 'required|in:guest,member',
            'customer_id' => 'nullable|required_if:customer_type,member|exists:users,id',
            'customer_name' => 'nullable|required_if:customer_type,guest|string|max:100',
            'customer_phone' => 'nullable|string|max:30',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'integer|exists:services,id',
            'artist_id' => 'required|integer|exists:nail_artists,id',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'status' => 'required|in:in_progress,confirmed',
            'payment_method' => 'required|string|in:cash,moka_pos,edc,qris,transfer,unpaid',
            'cashier_notes' => 'nullable|string|max:500',
            'design_notes' => 'nullable|string|max:500',
            'custom_design_photo' => 'nullable|image|max:5120',
        ], [
            'service_ids.required' => 'Pilih minimal satu layanan perawatan.',
            'artist_id.required' => 'Pilih terapis / nail artist yang bertugas.',
            'customer_name.required_if' => 'Nama pelanggan walk-in wajib diisi.',
            'customer_id.required_if' => 'Pilih member pelanggan terdaftar.',
        ]);

        $services = Service::whereIn('id', $validated['service_ids'])->get();
        if ($services->isEmpty()) {
            return back()->withErrors(['service_ids' => 'Layanan yang dipilih tidak valid.'])->withInput();
        }

        $totalDuration = (int) $services->sum('duration_minutes');
        $totalPrice = (int) $services->sum('price');

        $startFormatted = date('H:i:s', strtotime($validated['start_time']));
        $endCarbon = Carbon::parse("{$validated['booking_date']} {$startFormatted}")->addMinutes($totalDuration);
        $endTime = $endCarbon->format('H:i:s');

        // Check slot availability and prevent double booking
        $slotService = app(SlotAvailabilityService::class);
        $check = $slotService->check(
            (int) $validated['artist_id'],
            $validated['booking_date'],
            $startFormatted,
            $totalDuration
        );

        if (! $check['available']) {
            return back()->withErrors([
                'slot_conflict' => 'Jadwal Bentrok: '.$check['reason'],
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            $bookingCode = Booking::generateBookingCode();

            $customerId = null;
            $customerName = trim($validated['customer_name'] ?? '');
            $customerPhone = trim($validated['customer_phone'] ?? '');

            if ($validated['customer_type'] === 'member' && ! empty($validated['customer_id'])) {
                $member = User::find($validated['customer_id']);
                if ($member) {
                    $customerId = $member->id;
                    $customerName = $member->name;
                    $customerPhone = $customerPhone ?: $member->phone;
                }
            }

            if (empty($customerName)) {
                $customerName = 'Tamu Walk-In ('.($validated['branch']).')';
            }

            $photoPath = null;
            if ($request->hasFile('custom_design_photo')) {
                $photoPath = $request->file('custom_design_photo')->store('custom_designs', 'public');
            }

            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'artist_id' => (int) $validated['artist_id'],
                'branch' => $validated['branch'],
                'booking_date' => $validated['booking_date'],
                'start_time' => $startFormatted,
                'end_time' => $endTime,
                'total_price' => $totalPrice,
                'dp_amount' => $validated['payment_method'] !== 'unpaid' ? $totalPrice : 0,
                'loyalty_points_used' => 0,
                'loyalty_discount' => 0,
                'status' => $validated['status'],
                'booking_source' => Booking::SOURCE_WALK_IN,
                'is_walk_in' => true,
                'custom_design_photo' => $photoPath,
                'cashier_notes' => $validated['cashier_notes'] ?? null,
                'design_notes' => $validated['design_notes'] ?? null,
            ]);

            foreach ($services as $service) {
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'service_id' => $service->id,
                    'price' => $service->price,
                    'duration_minutes' => $service->duration_minutes,
                ]);
            }

            if ($validated['payment_method'] !== 'unpaid') {
                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_type' => Payment::TYPE_FULL,
                    'amount' => $totalPrice,
                    'status' => Payment::STATUS_VERIFIED,
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.bookings.index')
                ->with('success', "Pesanan Walk-In berhasil disimpan! Kode #{$booking->booking_code}. Slot jadwal terapis langsung TERKUNCI di sistem.");
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Gagal menyimpan pesanan walk-in: '.$e->getMessage()])->withInput();
        }
    }
}
