<?php

namespace Tests\Feature;

use App\Models\ArtistSchedule;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArtistPortalAndReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $artistUser;
    protected NailArtist $artist;
    protected User $customer;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $artistRole = Role::firstOrCreate(['name' => 'artist', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'name' => 'Owner Admin Studio',
            'email' => 'owner@nailglam.test',
        ]);
        $this->admin->assignRole($adminRole);

        $this->artistUser = User::factory()->create([
            'name' => 'Nadhira Artisan',
            'email' => 'nadhira@nailglam.test',
        ]);
        $this->artistUser->assignRole($artistRole);

        $this->artist = NailArtist::create([
            'user_id' => $this->artistUser->id,
            'bio' => 'Senior Nail Artist Spesialis 3D',
            'is_active' => true,
        ]);

        for ($i = 0; $i <= 6; $i++) {
            ArtistSchedule::create([
                'artist_id' => $this->artist->id,
                'day_of_week' => $i,
                'start_time' => '09:00:00',
                'end_time' => '21:00:00',
                'is_working' => true,
            ]);
        }

        $this->customer = User::factory()->create([
            'name' => 'Aulia Putri',
            'email' => 'aulia@nailglam.test',
            'phone' => '081234567890',
            'loyalty_points' => 0,
        ]);
        $this->customer->assignRole($customerRole);

        $cat = Category::create([
            'name' => 'Manicure',
            'slug' => 'manicure',
            'icon' => 'sparkles',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'category_id' => $cat->id,
            'name' => 'Gel Nail Art Glam',
            'price' => 200000,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
    }

    protected function createBooking(string $status = Booking::STATUS_CONFIRMED, ?string $date = null, string $startTime = '10:00:00', int $duration = 60): Booking
    {
        $date = $date ?? today()->toDateString();
        $endTime = Carbon::parse("$date $startTime")->addMinutes($duration)->format('H:i:s');

        $booking = Booking::create([
            'booking_code' => 'BN'.date('ymd').rand(100, 999),
            'customer_id' => $this->customer->id,
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => 200000,
            'dp_amount' => 60000,
            'status' => $status,
            'artist_confirmation_status' => 'pending',
            'is_walk_in' => false,
            'booking_source' => Booking::SOURCE_ONLINE,
        ]);

        BookingDetail::create([
            'booking_id' => $booking->id,
            'service_id' => $this->service->id,
            'price' => 200000,
            'duration_minutes' => $duration,
        ]);

        return $booking;
    }

    public function test_customer_can_submit_review_and_rating_for_completed_booking()
    {
        $booking = $this->createBooking(Booking::STATUS_COMPLETED);

        $photo = UploadedFile::fake()->image('nail_result.jpg', 600, 600);

        $response = $this->actingAs($this->customer)
            ->post(route('reviews.store', $booking), [
                'rating' => 5,
                'comment' => 'Hasil nail art sangat rapi, warnanya elegan dan tahan lama!',
                'review_photo' => $photo,
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,
            'artist_id' => $this->artist->id,
            'rating' => 5,
            'comment' => 'Hasil nail art sangat rapi, warnanya elegan dan tahan lama!',
        ]);

        $review = Review::where('booking_id', $booking->id)->first();
        $this->assertNotNull($review->photo);
        $this->assertStringStartsWith('data:image/', $review->photo);
        $this->assertStringContainsString(';base64,', $review->photo);
    }

    public function test_customer_cannot_review_pending_or_uncompleted_booking()
    {
        $booking = $this->createBooking(Booking::STATUS_CONFIRMED);

        $response = $this->actingAs($this->customer)
            ->post(route('reviews.store', $booking), [
                'rating' => 5,
                'comment' => 'Belum dikerjakan',
            ]);

        $response->assertStatus(400);
        $this->assertDatabaseMissing('reviews', ['booking_id' => $booking->id]);
    }

    public function test_customer_cannot_review_same_booking_twice()
    {
        $booking = $this->createBooking(Booking::STATUS_COMPLETED);

        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,
            'artist_id' => $this->artist->id,
            'rating' => 5,
            'comment' => 'Review pertama',
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('reviews.store', $booking), [
                'rating' => 4,
                'comment' => 'Review kedua',
            ]);

        $response->assertStatus(409);
    }

    public function test_reviews_update_artist_average_rating()
    {
        $booking1 = $this->createBooking(Booking::STATUS_COMPLETED);
        $booking2 = $this->createBooking(Booking::STATUS_COMPLETED, today()->addDay()->toDateString());

        Review::create([
            'booking_id' => $booking1->id,
            'customer_id' => $this->customer->id,
            'artist_id' => $this->artist->id,
            'rating' => 5,
            'comment' => 'Luar biasa!',
        ]);

        Review::create([
            'booking_id' => $booking2->id,
            'customer_id' => $this->customer->id,
            'artist_id' => $this->artist->id,
            'rating' => 4,
            'comment' => 'Bagus sekali.',
        ]);

        $this->assertEquals(4.5, $this->artist->fresh()->average_rating);
        $this->assertEquals(2, $this->artist->fresh()->review_count);
    }

    public function test_artist_can_login_and_access_artist_dashboard()
    {
        $response = $this->actingAs($this->artistUser)
            ->get(route('artist.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Portal Teknisi Artisan');
        $response->assertSee('Antrean Hari Ini');
    }

    public function test_artist_cannot_access_admin_routes_rbac()
    {
        $response = $this->actingAs($this->artistUser)
            ->get(route('admin.bookings.index'));

        $response->assertStatus(403);
    }

    public function test_customer_cannot_access_artist_dashboard_rbac()
    {
        $response = $this->actingAs($this->customer)
            ->get(route('artist.dashboard'));

        $response->assertStatus(403);
    }

    public function test_inactive_or_off_duty_artist_can_still_access_dashboard_and_activate_on_duty()
    {
        $this->artist->update(['is_active' => false]);

        $response = $this->actingAs($this->artistUser)
            ->get(route('artist.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Status Ketersediaan: Sedang Istirahat / OFF');
        $response->assertSee('Aktifkan Mode Bertugas (On-Duty)');

        // Toggle back to active
        $this->actingAs($this->artistUser)
            ->post(route('artist.toggleDuty'))
            ->assertSessionHas('success');

        $this->assertTrue($this->artist->fresh()->is_active);
    }

    public function test_artist_can_confirm_booking_two_way()
    {
        $booking = $this->createBooking(Booking::STATUS_CONFIRMED);

        $response = $this->actingAs($this->artistUser)
            ->post(route('artist.bookings.confirm', $booking));

        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('confirmed', $booking->artist_confirmation_status);
        $this->assertNotNull($booking->artist_confirmed_at);
    }

    public function test_artist_can_report_clash_or_reject_booking()
    {
        $booking = $this->createBooking(Booking::STATUS_CONFIRMED);

        $response = $this->actingAs($this->artistUser)
            ->post(route('artist.bookings.reject', $booking), [
                'reject_reason' => 'Pengerjaan tamu sebelumnya overtime 45 menit, jadwal bentrok',
            ]);

        $response->assertSessionHas('warning');

        $booking->refresh();
        $this->assertEquals('rejected', $booking->artist_confirmation_status);
        $this->assertEquals('Pengerjaan tamu sebelumnya overtime 45 menit, jadwal bentrok', $booking->artist_notes);
    }

    public function test_artist_can_start_and_complete_session_awarding_points()
    {
        $booking = $this->createBooking(Booking::STATUS_CONFIRMED);

        // Start session
        $this->actingAs($this->artistUser)
            ->post(route('artist.bookings.start', $booking))
            ->assertSessionHas('success');

        $this->assertEquals(Booking::STATUS_IN_PROGRESS, $booking->fresh()->status);

        // Complete session
        $this->actingAs($this->artistUser)
            ->post(route('artist.bookings.complete', $booking))
            ->assertSessionHas('success');

        $this->assertEquals(Booking::STATUS_COMPLETED, $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->completed_at);

        // 200000 / 10000 = 20 loyalty points
        $this->assertEquals(20, $this->customer->fresh()->loyalty_points);
    }

    public function test_admin_owner_rbac_create_and_manage_artist_user()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.artists.store'), [
                'artist_name' => 'Siti Terapis Baru',
                'artist_email' => 'siti_baru@nailglam.test',
                'artist_password' => 'Password123!',
                'artist_phone' => '08988776655',
                'specialization' => 'Japanese 3D Nail Art',
                'bio' => 'Terapis handal bersertifikat',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.artists.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Siti Terapis Baru',
            'email' => 'siti_baru@nailglam.test',
        ]);

        $createdUser = User::where('email', 'siti_baru@nailglam.test')->first();
        // $this->assertTrue($createdUser->isArtist());
        $this->assertTrue(Hash::check('Password123!', $createdUser->password));

        $createdArtist = NailArtist::where('user_id', $createdUser->id)->first();
        $this->assertNotNull($createdArtist);
        $this->assertTrue($createdArtist->is_active);
    }

    public function test_customer_can_render_dashboard_without_errors()
    {
        $this->createBooking(Booking::STATUS_COMPLETED);

        $response = $this->actingAs($this->customer)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Booking Saya');
        $response->assertSee('Beri Review');
    }

    public function test_admin_can_toggle_artist_status_active_and_inactive()
    {
        $this->assertTrue($this->artist->is_active);

        // Deactivate via quick toggle
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.artists.toggleStatus', $this->artist));

        $response->assertSessionHas('success');
        $this->assertFalse($this->artist->fresh()->is_active);

        // Reactivate via quick toggle
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.artists.toggleStatus', $this->artist));

        $response->assertSessionHas('success');
        $this->assertTrue($this->artist->fresh()->is_active);
    }

    public function test_artist_dashboard_displays_notification_center_and_supports_ajax_check_new()
    {
        $this->createBooking(Booking::STATUS_CONFIRMED);

        // 1. Artist views dashboard with notification center
        $response = $this->actingAs($this->artistUser)
            ->get(route('artist.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Notifikasi Terapis');
        $response->assertSee('Notifikasi Penugasan:');
        $response->assertSee('Tes Suara 🔔');
        $response->assertSee('Perlu Konfirmasi Janji Temu');

        // 2. Artist polls AJAX check-new endpoint
        $checkResponse = $this->actingAs($this->artistUser)
            ->getJson(route('artist.checkNew'));

        $checkResponse->assertStatus(200);
        $checkResponse->assertJsonStructure(['has_new', 'pending_confirm_count', 'server_time']);
        $this->assertEquals(1, $checkResponse->json('pending_confirm_count'));
    }
}
