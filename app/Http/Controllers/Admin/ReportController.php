<?php

// =============================================================================
// ReportController.php
// Controller untuk mengelola halaman laporan dan fitur export data reservasi
// pada dashboard admin Nail Glam Studio.
// Mendukung filtering komprehensif, preview interaktif, export PDF (DomPDF),
// dan export CSV (Excel) dengan standar encoding UTF-8 BOM.
// =============================================================================

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\NailArtist;
use App\Models\Payment;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Membangun query reservasi berdasarkan filter dari request.
     */
    protected function buildBookingsQuery(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');
        $artistId = $request->get('artist_id');
        $source = $request->get('source');
        $search = $request->get('q');
        $period = $request->get('period');

        // Preset periode otomatis jika dipilih
        if ($period) {
            switch ($period) {
                case 'today':
                    $startDate = today()->toDateString();
                    $endDate = today()->toDateString();
                    break;
                case 'this_week':
                    $startDate = today()->startOfWeek(Carbon::MONDAY)->toDateString();
                    $endDate = today()->endOfWeek(Carbon::SUNDAY)->toDateString();
                    break;
                case 'this_month':
                    $startDate = today()->startOfMonth()->toDateString();
                    $endDate = today()->endOfMonth()->toDateString();
                    break;
                case 'last_month':
                    $startDate = today()->subMonth()->startOfMonth()->toDateString();
                    $endDate = today()->subMonth()->endOfMonth()->toDateString();
                    break;
                case 'all':
                    $startDate = null;
                    $endDate = null;
                    break;
            }
        } elseif ($startDate === null && $endDate === null) {
            // Default ke bulan berjalan jika parameter kosong
            $startDate = today()->startOfMonth()->toDateString();
            $endDate = today()->endOfMonth()->toDateString();
        }

        $query = Booking::with(['customer', 'artist.user', 'bookingDetails.service', 'payments']);

        if ($startDate) {
            $query->whereDate('booking_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('booking_date', '<=', $endDate);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($artistId) {
            $query->where('artist_id', $artistId);
        }
        if ($source === 'walk_in') {
            $query->walkIn();
        } elseif ($source === 'online') {
            $query->online();
        }
        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cu) use ($search) {
                        $cu->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        return [
            'query' => $query->orderBy('booking_date', 'desc')->orderBy('start_time', 'desc'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'artist_id' => $artistId,
            'source' => $source,
            'search' => $search,
            'period' => $period,
        ];
    }

    /**
     * Menampilkan halaman preview laporan hasil reservasi dengan filter & KPI.
     */
    public function index(Request $request)
    {
        $filtered = $this->buildBookingsQuery($request);
        $query = $filtered['query'];

        // Ambil data bookings untuk tabel preview (paginated)
        $bookings = (clone $query)->paginate(15)->withQueryString();

        // Hitung metrik ringkasan keseluruhan dari query yang terfilter
        $allFilteredBookings = (clone $query)->get();
        $totalBookings = $allFilteredBookings->count();
        $totalRevenue = $allFilteredBookings->whereIn('status', [Booking::STATUS_COMPLETED, Booking::STATUS_CONFIRMED, Booking::STATUS_IN_PROGRESS])->sum('total_price');
        $completedRevenue = $allFilteredBookings->where('status', Booking::STATUS_COMPLETED)->sum('total_price');
        $totalDpCollected = $allFilteredBookings->sum('dp_amount');

        $statusCounts = [
            'completed' => $allFilteredBookings->where('status', Booking::STATUS_COMPLETED)->count(),
            'confirmed' => $allFilteredBookings->where('status', Booking::STATUS_CONFIRMED)->count(),
            'in_progress' => $allFilteredBookings->where('status', Booking::STATUS_IN_PROGRESS)->count(),
            'pending' => $allFilteredBookings->where('status', Booking::STATUS_PENDING)->count(),
            'cancelled' => $allFilteredBookings->where('status', Booking::STATUS_CANCELLED)->count(),
            'online' => $allFilteredBookings->where('is_walk_in', false)->count(),
            'walk_in' => $allFilteredBookings->where('is_walk_in', true)->count(),
        ];

        $summary = [
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'completed_revenue' => $completedRevenue,
            'total_dp' => $totalDpCollected,
            'counts' => $statusCounts,
        ];

        // Master data untuk dropdown filter
        $artists = NailArtist::with('user')->active()->orderBy('id')->get();
        $statusOptions = [
            Booking::STATUS_PENDING => 'Pending (Menunggu DP)',
            Booking::STATUS_CONFIRMED => 'Dikonfirmasi',
            Booking::STATUS_IN_PROGRESS => 'Sedang Berlangsung',
            Booking::STATUS_COMPLETED => 'Selesai',
            Booking::STATUS_CANCELLED => 'Dibatalkan',
        ];

        $startDate = $filtered['start_date'];
        $endDate = $filtered['end_date'];
        $status = $filtered['status'];
        $artistId = $filtered['artist_id'];
        $source = $filtered['source'];
        $search = $filtered['search'];
        $period = $filtered['period'];

        return view('admin.reports.index', compact(
            'bookings',
            'summary',
            'artists',
            'statusOptions',
            'startDate',
            'endDate',
            'status',
            'artistId',
            'source',
            'search',
            'period'
        ));
    }

    /**
     * Export laporan hasil reservasi ke dalam format PDF (Landscape) via DomPDF.
     */
    public function exportPdf(Request $request)
    {
        $filtered = $this->buildBookingsQuery($request);
        $bookings = $filtered['query']->get();

        $totalBookings = $bookings->count();
        $totalRevenue = $bookings->whereIn('status', [Booking::STATUS_COMPLETED, Booking::STATUS_CONFIRMED, Booking::STATUS_IN_PROGRESS])->sum('total_price');
        $completedRevenue = $bookings->where('status', Booking::STATUS_COMPLETED)->sum('total_price');
        $totalDp = $bookings->sum('dp_amount');

        $statusCounts = [
            'completed' => $bookings->where('status', Booking::STATUS_COMPLETED)->count(),
            'confirmed' => $bookings->where('status', Booking::STATUS_CONFIRMED)->count(),
            'in_progress' => $bookings->where('status', Booking::STATUS_IN_PROGRESS)->count(),
            'pending' => $bookings->where('status', Booking::STATUS_PENDING)->count(),
            'cancelled' => $bookings->where('status', Booking::STATUS_CANCELLED)->count(),
        ];

        $startDate = $filtered['start_date'];
        $endDate = $filtered['end_date'];
        $artist = $filtered['artist_id'] ? NailArtist::with('user')->find($filtered['artist_id']) : null;
        $statusLabel = $filtered['status'] ? ucfirst($filtered['status']) : 'Semua Status';
        $printedAt = now()->locale('id')->isoFormat('D MMMM Y, HH:mm') . ' WIB';
        $adminName = auth()->user()->name ?? 'Administrator';

        $filename = 'laporan-reservasi-' . now()->format('Ymd-His') . '.pdf';

        $pdf = Pdf::loadView('admin.reports.pdf', compact(
            'bookings',
            'totalBookings',
            'totalRevenue',
            'completedRevenue',
            'totalDp',
            'statusCounts',
            'startDate',
            'endDate',
            'artist',
            'statusLabel',
            'printedAt',
            'adminName'
        ))->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Export laporan hasil reservasi ke dalam format CSV kompatibel dengan Microsoft Excel.
     * Menggunakan encoding UTF-8 dengan BOM (\xEF\xBB\xBF) agar karakter dan angka tampil sempurna.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $filtered = $this->buildBookingsQuery($request);
        $bookings = $filtered['query']->get();

        $filename = 'laporan-reservasi-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            // Tulis Byte Order Mark (BOM) UTF-8 agar Microsoft Excel otomatis mengenali encoding dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header kolom CSV
            fputcsv($file, [
                'No',
                'Kode Booking',
                'Tanggal Reservasi',
                'Jam Mulai',
                'Jam Selesai',
                'Nama Pelanggan',
                'No. Telepon',
                'Layanan Treatment',
                'Durasi (Menit)',
                'Nail Artist / Teknisi',
                'Sumber Pemesanan',
                'Status Reservasi',
                'Total Biaya (IDR)',
                'Uang Muka / DP (IDR)',
                'Status Pembayaran DP',
                'Catatan Kasir / Desain',
                'Waktu Pemesanan',
            ]);

            $no = 1;
            foreach ($bookings as $b) {
                // Ambil daftar nama layanan
                $servicesList = $b->bookingDetails->map(function ($d) {
                    return $d->service->name ?? 'Layanan';
                })->implode(', ');

                $totalDuration = $b->bookingDetails->sum('duration_minutes') ?: 60;

                // Cek status DP dari pembayaran
                $dpPayment = $b->payments->firstWhere('payment_type', 'dp');
                $dpStatus = $dpPayment ? ucfirst($dpPayment->status) : ($b->dp_amount > 0 ? 'Pending' : 'N/A');

                fputcsv($file, [
                    $no++,
                    $b->booking_code,
                    $b->booking_date ? $b->booking_date->format('Y-m-d') : '-',
                    substr($b->start_time, 0, 5),
                    substr($b->end_time, 0, 5),
                    $b->customer_display_name,
                    $b->customer_display_phone,
                    $servicesList,
                    $totalDuration,
                    $b->artist->user->name ?? 'Belum Ditugaskan',
                    $b->is_walk_in ? 'Walk-In Kasir' : 'Online Website',
                    $b->status_label,
                    $b->total_price,
                    $b->dp_amount,
                    $dpStatus,
                    $b->cashier_notes ?: ($b->design_notes ?: '-'),
                    $b->created_at ? $b->created_at->format('Y-m-d H:i:s') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

