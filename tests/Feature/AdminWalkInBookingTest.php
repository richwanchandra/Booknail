<?php

namespace Tests\Feature;

use App\Models\ArtistSchedule;
use App\Models\Booking;
use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Services\SlotAvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminWalkInBookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected NailArtist $artist;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@booknail.test',
        ]);
        $this->admin->assignRole($adminRole);

        $artistUser = User::factory()->create([
            'name' => 'Terapis Siti',
            'email' => 'siti@booknail.test',
        ]);
        $this->artist = NailArtist::create([
            'user_id' => $artistUser->id,
            'bio' => 'Senior Nail Artist',
            'is_active' => true,
        ]);

        // Create schedule for every day 09:00 - 18:00
        for ($i = 0; $i <= 6; $i++) {
            ArtistSchedule::create([
                'artist_id' => $this->artist->id,
                'day_of_week' => $i,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'is_off' => false,
            ]);
        }

        $category = Category::create([
            'name' => 'Nail Art',
            'slug' => 'nail-art',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'category_id' => $category->id,
            'name' => 'Gel Nail Art Premium',
            'price' => 150000,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
    }

    public function test_guest_and_customer_cannot_access_walk_in_page(): void
    {
        $this->get(route('admin.walk-in.create'))
            ->assertRedirect('/login');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->get(route('admin.walk-in.create'))
            ->assertForbidden();
    }

    public function test_admin_can_view_walk_in_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.walk-in.create'));

        $response->assertOk()
            ->assertSee('Input Pesanan Walk-In')
            ->assertDontSee('Citra Garden 6')
            ->assertSee('Metland Puri')
            ->assertSee('Gel Nail Art Premium')
            ->assertSee('Terapis Siti');
    }

    public function test_admin_can_check_slot_availability_via_ajax(): void
    {
        $date = Carbon::today()->format('Y-m-d');

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.walk-in.checkSlot'), [
                'artist_id' => $this->artist->id,
                'booking_date' => $date,
                'start_time' => '14:00',
                'duration_minutes' => 60,
            ]);

        $response->assertOk()
            ->assertJson([
                'available' => true,
                'start_time' => '14:00',
                'end_time' => '15:00',
            ]);
    }

    public function test_admin_can_store_walk_in_order_with_manual_guest_and_lock_slot(): void
    {
        $date = Carbon::today()->format('Y-m-d');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.walk-in.store'), [
                'branch' => 'Citra Garden 6',
                'customer_type' => 'guest',
                'customer_name' => 'Ibu Diana',
                'customer_phone' => '081234567890',
                'service_ids' => [$this->service->id],
                'artist_id' => $this->artist->id,
                'booking_date' => $date,
                'start_time' => '14:00',
                'status' => 'in_progress',
                'payment_method' => 'moka_pos',
                'cashier_notes' => 'STRUK-MOKA-001',
                'design_notes' => 'Warna soft pink nude',
            ]);

        $response->assertRedirect(route('admin.bookings.index'))
            ->assertSessionHas('success');

        $booking = Booking::where('customer_name', 'Ibu Diana')->first();
        $this->assertNotNull($booking);
        $this->assertNull($booking->customer_id);
        $this->assertEquals('Citra Garden 6', $booking->branch);
        $this->assertEquals(Booking::SOURCE_WALK_IN, $booking->booking_source);
        $this->assertTrue($booking->is_walk_in);
        $this->assertTrue($booking->is_walk_in_order);
        $this->assertEquals('in_progress', $booking->status);
        $this->assertEquals('STRUK-MOKA-001', $booking->cashier_notes);
        $this->assertEquals(150000, $booking->total_price);
        $this->assertEquals('14:00:00', $booking->start_time);
        $this->assertEquals('15:00:00', $booking->end_time);

        // Verify payment is automatically created & verified
        $payment = Payment::where('booking_id', $booking->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(Payment::STATUS_VERIFIED, $payment->status);
        $this->assertEquals(Payment::TYPE_FULL, $payment->payment_type);
        $this->assertEquals(150000, $payment->amount);

        // Verify anti double-booking logic blocks overlapping slot (14:30 - 15:30)
        $slotService = app(SlotAvailabilityService::class);
        $conflictCheck = $slotService->check(
            $this->artist->id,
            $date,
            '14:30:00',
            60
        );

        $this->assertFalse($conflictCheck['available']);
        $this->assertStringContainsString('bentrok dengan booking lain', strtolower($conflictCheck['reason']));
    }

    public function test_admin_can_filter_walk_in_and_online_bookings_on_index(): void
    {
        $date = Carbon::today()->format('Y-m-d');

        // Create 1 online booking
        Booking::create([
            'booking_code' => 'BNONLINE001',
            'customer_id' => $this->admin->id,
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'total_price' => 100000,
            'dp_amount' => 30000,
            'status' => 'confirmed',
            'booking_source' => Booking::SOURCE_ONLINE,
            'is_walk_in' => false,
        ]);

        // Create 1 walk-in booking
        Booking::create([
            'booking_code' => 'BNWALKIN002',
            'customer_name' => 'Tamu Walkin Rina',
            'branch' => 'Metland Puri',
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => '16:00:00',
            'end_time' => '17:00:00',
            'total_price' => 200000,
            'dp_amount' => 200000,
            'status' => 'in_progress',
            'booking_source' => Booking::SOURCE_WALK_IN,
            'is_walk_in' => true,
        ]);

        // Filter: walk_in
        $resWalkIn = $this->actingAs($this->admin)
            ->get(route('admin.bookings.index', ['source' => 'walk_in']));

        $resWalkIn->assertOk()
            ->assertSee('BNWALKIN002')
            ->assertSee('Tamu Walkin Rina')
            ->assertSee('Walk-In')
            ->assertDontSee('BNONLINE001');

        // Filter: online
        $resOnline = $this->actingAs($this->admin)
            ->get(route('admin.bookings.index', ['source' => 'online']));

        $resOnline->assertOk()
            ->assertSee('BNONLINE001')
            ->assertDontSee('BNWALKIN002');
    }
}
