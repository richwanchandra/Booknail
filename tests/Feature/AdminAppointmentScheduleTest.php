<?php

namespace Tests\Feature;

use App\Models\ArtistLeave;
use App\Models\ArtistSchedule;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminAppointmentScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected NailArtist $artist1;
    protected NailArtist $artist2;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'nail_artist', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'name' => 'Studio Admin',
            'email' => 'admin@booknail.test',
        ]);
        $this->admin->assignRole($adminRole);

        $artistUser1 = User::factory()->create(['name' => 'Elena Ross']);
        $this->artist1 = NailArtist::create([
            'user_id' => $artistUser1->id,
            'bio' => 'Master Gel Specialist',
            'is_active' => true,
        ]);

        $artistUser2 = User::factory()->create(['name' => 'Chloe Bennett']);
        $this->artist2 = NailArtist::create([
            'user_id' => $artistUser2->id,
            'bio' => '3D Nail Artist',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Nail Art Specialist',
            'slug' => 'nail-art-specialist',
        ]);

        $this->service = Service::create([
            'category_id' => $category->id,
            'name' => 'Custom 3D Gel Art',
            'slug' => 'custom-3d-gel-art',
            'price' => 250000,
            'duration_minutes' => 90,
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_weekly_appointments(): void
    {
        $response = $this->get(route('admin.appointments.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_weekly_appointments_schedule(): void
    {
        // Create booking for Wednesday of current week
        $wednesday = today()->startOfWeek(Carbon::MONDAY)->addDays(2);

        $booking = Booking::create([
            'booking_code' => 'BK-TEST-001',
            'customer_name' => 'Sophia Taylor',
            'customer_phone' => '08123456789',
            'artist_id' => $this->artist1->id,
            'booking_date' => $wednesday->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:30:00',
            'total_price' => 250000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_CONFIRMED,
        ]);

        BookingDetail::create([
            'booking_id' => $booking->id,
            'service_id' => $this->service->id,
            'price' => 250000,
            'duration_minutes' => 90,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.appointments.index'));

        $response->assertStatus(200);
        $response->assertSee('Jadwal Janji Temu Mingguan');
        $response->assertSee('Sophia Taylor');
        $response->assertSee('Custom 3D Gel Art');
        $response->assertSee('Elena Ross');
        $response->assertSee('Station 1');
        $response->assertSee('BK-TEST-001');
    }

    public function test_cross_month_week_navigation(): void
    {
        // Select a week spanning across months (e.g. October 28 to November 3, 2024)
        $crossMonthDate = '2024-10-30';

        $response = $this->actingAs($this->admin)->get(route('admin.appointments.index', ['week' => $crossMonthDate]));

        $response->assertStatus(200);
        $response->assertSee('Lintas Bulan');
    }

    public function test_filter_by_artist_and_view_mode(): void
    {
        $wednesday = today()->startOfWeek(Carbon::MONDAY)->addDays(2);

        // Booking 1 for artist 1
        $b1 = Booking::create([
            'booking_code' => 'BK-ART1',
            'customer_name' => 'Client Elena',
            'customer_phone' => '0811111111',
            'artist_id' => $this->artist1->id,
            'booking_date' => $wednesday->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:30:00',
            'total_price' => 250000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_CONFIRMED,
        ]);
        BookingDetail::create(['booking_id' => $b1->id, 'service_id' => $this->service->id, 'price' => 250000, 'duration_minutes' => 90]);

        // Booking 2 for artist 2
        $b2 = Booking::create([
            'booking_code' => 'BK-ART2',
            'customer_name' => 'Client Chloe',
            'customer_phone' => '0822222222',
            'artist_id' => $this->artist2->id,
            'booking_date' => $wednesday->format('Y-m-d'),
            'start_time' => '13:00:00',
            'end_time' => '14:30:00',
            'total_price' => 250000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_CONFIRMED,
        ]);
        BookingDetail::create(['booking_id' => $b2->id, 'service_id' => $this->service->id, 'price' => 250000, 'duration_minutes' => 90]);

        // Filter by artist 1
        $response = $this->actingAs($this->admin)->get(route('admin.appointments.index', ['artist_id' => $this->artist1->id]));
        $response->assertStatus(200);
        $response->assertSee('Client Elena');
        $response->assertDontSee('Client Chloe');

        // Test list view mode
        $listResponse = $this->actingAs($this->admin)->get(route('admin.appointments.index', ['view' => 'list']));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('Daftar Reservasi Mingguan');
        $listResponse->assertSee('BK-ART1');
        $listResponse->assertSee('BK-ART2');

        // Test day view mode
        $dayResponse = $this->actingAs($this->admin)->get(route('admin.appointments.index', ['view' => 'day', 'week' => $wednesday->format('Y-m-d')]));
        $dayResponse->assertStatus(200);
        $dayResponse->assertSee('Station 1');
        $dayResponse->assertSee('Station 2');
        $dayResponse->assertSee('Client Elena');
    }

    public function test_admin_can_block_slot(): void
    {
        $blockDate = today()->addDays(3)->format('Y-m-d');

        $response = $this->actingAs($this->admin)->post(route('admin.appointments.blockSlot'), [
            'artist_id' => $this->artist1->id,
            'date' => $blockDate,
            'reason' => 'Studio station maintenance & staff leave',
            'leave_type' => 'personal',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue(
            ArtistLeave::where('artist_id', $this->artist1->id)
                ->whereDate('leave_date', $blockDate)
                ->where('reason', 'Studio station maintenance & staff leave')
                ->exists()
        );
    }
}