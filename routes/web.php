<?php

use App\Http\Controllers\Admin\AppointmentScheduleController;
use App\Http\Controllers\Admin\ArtistLeaveController;
use App\Http\Controllers\Admin\ArtistScheduleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\NailArtistController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\WalkInBookingController;
use App\Http\Controllers\Api\FcmTokenController;
use App\Http\Controllers\Artist\DashboardController as ArtistDashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/services', [FrontendController::class, 'services'])->name('services.index');
Route::get('/services/{service}', [FrontendController::class, 'serviceShow'])->name('services.show');
Route::get('/booking', [FrontendController::class, 'booking'])->name('booking.index');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [FrontendController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // FCM Device Token endpoints (called from JS after notification permission granted)
    Route::post('/api/fcm-token', [FcmTokenController::class, 'store'])->name('fcm.token.store');
    Route::delete('/api/fcm-token', [FcmTokenController::class, 'destroy'])->name('fcm.token.destroy');

    // Booking flow (customer only / all authenticated users)
    Route::post('/booking/store-step1', [FrontendController::class, 'storeStep1'])->name('booking.storeStep1');
    Route::post('/booking/submit', [BookingController::class, 'submitBooking'])->name('booking.submit');
    Route::post('/booking/reset', [BookingController::class, 'resetFlow'])->name('booking.reset');
    Route::post('/booking/upload-design', [BookingController::class, 'uploadDesign'])->name('booking.uploadDesign');
    Route::post('/booking/select-slot', [BookingController::class, 'selectSlot'])->name('booking.selectSlot');

    // Review (per booking, unique per completed booking)
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/bookings/{booking}/ack-delay', [BookingController::class, 'ackDelay'])->name('booking.ackDelay');
    Route::post('/bookings/{booking}/upload-proof', [BookingController::class, 'uploadPaymentProof'])->name('booking.uploadProof');
    Route::get('/customer/bookings/check-updates', [BookingController::class, 'checkCustomerUpdates'])->name('customer.bookings.checkUpdates');

    // ============ ARTIST (TEKNISI) ROUTE GROUP ============
    Route::prefix('artist')->name('artist.')->middleware('artist')->group(function () {
        Route::get('/dashboard', [ArtistDashboardController::class, 'index'])->name('dashboard');
        Route::get('/check-new', [ArtistDashboardController::class, 'checkNewBookings'])->name('checkNew');
        Route::post('/bookings/{booking}/confirm', [ArtistDashboardController::class, 'confirm'])->name('bookings.confirm');
        Route::post('/bookings/{booking}/reject', [ArtistDashboardController::class, 'reject'])->name('bookings.reject');
        Route::post('/bookings/{booking}/start', [ArtistDashboardController::class, 'start'])->name('bookings.start');
        Route::post('/bookings/{booking}/complete', [ArtistDashboardController::class, 'complete'])->name('bookings.complete');
        Route::post('/toggle-duty', [ArtistDashboardController::class, 'toggleDuty'])->name('toggleDuty');
    });

    // ============ ADMIN ROUTE GROUP ============
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.bookings.index'))->name('index');

        Route::resource('categories', CategoryController::class);
        Route::resource('services', ServiceController::class);
        Route::resource('portfolios', PortfolioController::class);
        Route::patch('portfolios/{portfolio}/toggle-status', [PortfolioController::class, 'toggleStatus'])->name('portfolios.toggleStatus');
        Route::resource('artists', NailArtistController::class);
        Route::patch('artists/{artist}/toggle-status', [NailArtistController::class, 'toggleStatus'])->name('artists.toggleStatus');

        Route::get('/schedules', [ArtistScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/schedules/bulk', [ArtistScheduleController::class, 'bulkStore'])->name('schedules.bulkStore');

        Route::resource('leaves', ArtistLeaveController::class);

        // Walk-In Bookings
        Route::get('/walk-in', [WalkInBookingController::class, 'create'])->name('walk-in.create');
        Route::post('/walk-in', [WalkInBookingController::class, 'store'])->name('walk-in.store');
        Route::post('/walk-in/check-slot', [WalkInBookingController::class, 'checkSlot'])->name('walk-in.checkSlot');
        Route::post('/walk-in/toggle-slot', [WalkInBookingController::class, 'toggleSlot'])->name('walk-in.toggleSlot');
        Route::post('/walk-in/update-price', [WalkInBookingController::class, 'updatePrice'])->name('walk-in.updatePrice');
        Route::post('/walk-in/add-slot', [WalkInBookingController::class, 'addSlot'])->name('walk-in.addSlot');
        Route::post('/walk-in/delete-slot', [WalkInBookingController::class, 'deleteSlot'])->name('walk-in.deleteSlot');

        Route::get('/bookings', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{booking}/approve', [App\Http\Controllers\Admin\BookingController::class, 'approvePayment'])->name('bookings.approve');
        Route::post('/bookings/{booking}/reject', [App\Http\Controllers\Admin\BookingController::class, 'rejectPayment'])->name('bookings.reject');
        Route::post('/bookings/{booking}/start', [App\Http\Controllers\Admin\BookingController::class, 'setInProgress'])->name('bookings.start');
        Route::post('/bookings/{booking}/complete', [App\Http\Controllers\Admin\BookingController::class, 'setCompleted'])->name('bookings.complete');
        Route::post('/bookings/{booking}/cancel', [App\Http\Controllers\Admin\BookingController::class, 'cancelBooking'])->name('bookings.cancel');
        Route::post('/bookings/{booking}/preview-upgrade', [App\Http\Controllers\Admin\BookingController::class, 'previewUpgrade'])->name('bookings.previewUpgrade');
        Route::post('/bookings/{booking}/apply-upgrade', [App\Http\Controllers\Admin\BookingController::class, 'applyUpgrade'])->name('bookings.applyUpgrade');
        Route::post('/bookings/test-notification', [App\Http\Controllers\Admin\BookingController::class, 'testNotification'])->name('bookings.testNotification');
        Route::get('/bookings/check-new', [App\Http\Controllers\Admin\BookingController::class, 'checkNewBookings'])->name('bookings.checkNew');

        // Weekly Appointments Schedule
        Route::get('/appointments', [AppointmentScheduleController::class, 'index'])->name('appointments.index');
        Route::post('/appointments/block-slot', [AppointmentScheduleController::class, 'blockSlot'])->name('appointments.blockSlot');

        // Laporan & Export Reservasi (PDF & CSV / Excel)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
        Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.exportCsv');
    });
});

require __DIR__.'/auth.php';
