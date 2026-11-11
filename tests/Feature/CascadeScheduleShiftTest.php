<?php

namespace Tests\Feature;

use App\Models\ArtistSchedule;
use App\Models\Booking;
use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Service;
use App\Models\User;
use App\Models\WaLog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CascadeScheduleShiftTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected NailArtist $artist;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'name' => 'Admin Studio',
            'email' => 'admin@nailglam.test',
        ]);
        $this->admin->assignRole($adminRole);

        $this->customer = User::factory()->create([
            'name' => 'Pelanggan Maya',
            'email' => 'maya@nailglam.test',
            'phone' => '08123456789',
        ]);
        $this->customer->assignRole($customerRole);

        $artistUser = User::factory()->create([
            'name' => 'Terapis Febe',
            'email' => 'febe@nailglam.test',
        ]);

        $this->artist = NailArtist::create([
            'user_id' => $artistUser->id,
            'bio' => 'Senior Artist',
            'is_active' => true,
        ]);

        for ($i = 0; $i <= 6; $i++) {
            ArtistSchedule::create([
                'artist_id' => $this->artist->id,
                'day_of_week' => $i,
                'start_time' => '09:00:00',
                'end_time' => '20:00:00',
                'is_off' => false,
            ]);
        }

        $category = Category::create([
            'name' => 'Nail Treatment',
            'slug' => 'nail-treatment',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'category_id' => $category->id,
            'name' => 'Nail Gel Extension',
            'slug' => 'nail-gel-extension',
            'price' => 150000,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_preview_cascade_schedule_shift(): void
    {
        $date = Carbon::tomorrow()->toDateString();

        $b1 = Booking::create([
            'booking_code' => 'BN-PREV-01',
            'customer_name' => 'Tamu 1',
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'total_price' => 100000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_IN_PROGRESS,
            'branch' => 'Citra Garden 6',
        ]);

        $b2 = Booking::create([
            'booking_code' => 'BN-PREV-02',
            'customer_name' => 'Tamu 2',
            'customer_phone' => '0811111111',
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
            'total_price' => 120000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_CONFIRMED,
            'branch' => 'Citra Garden 6',
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.bookings.previewUpgrade', $b1), [
                'additional_minutes' => 45,
            ]);

        $response->assertOk();
        $response->assertJsonPath('total_affected', 1);
        $response->assertJsonPath('current_booking.new_end_time', '11:45');
        $response->assertJsonPath('shifted_bookings.0.booking_code', 'BN-PREV-02');
        $response->assertJsonPath('shifted_bookings.0.new_start_time', '11:45');
        $response->assertJsonPath('shifted_bookings.0.new_end_time', '12:45');
        $response->assertJsonPath('shifted_bookings.0.delay_minutes', 45);
    }

    public function test_admin_can_apply_service_upgrade_and_cascade_shift(): void
    {
        $date = Carbon::tomorrow()->toDateString();

        // 3 sequential bookings: 10:00-11:00, 11:00-12:00, 12:00-13:00
        $b1 = Booking::create([
            'booking_code' => 'BN-SHIFT-01',
            'customer_name' => 'Ibu Clara',
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'total_price' => 100000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_IN_PROGRESS,
            'branch' => 'Citra Garden 6',
        ]);

        $b2 = Booking::create([
            'booking_code' => 'BN-SHIFT-02',
            'customer_name' => 'Kak Jessica',
            'customer_phone' => '08123456789',
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
            'total_price' => 150000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_CONFIRMED,
            'branch' => 'Citra Garden 6',
        ]);

        $b3 = Booking::create([
            'booking_code' => 'BN-SHIFT-03',
            'customer_name' => 'Ibu Diana',
            'customer_phone' => '08987654321',
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
            'total_price' => 200000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_CONFIRMED,
            'branch' => 'Citra Garden 6',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.bookings.applyUpgrade', $b1), [
                'additional_minutes' => 60,
                'additional_price' => 75000,
                'upgrade_notes' => 'Tambah nail art 3D dan kuku palsu extension',
                'notify_customers' => 1,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        // Verify b1 end time extended
        $b1->refresh();
        $this->assertEquals('12:00:00', $b1->end_time);
        $this->assertEquals(175000, $b1->total_price);
        $this->assertStringContainsString('Tambah nail art 3D', $b1->service_upgrade_notes);

        // Verify b2 shifted: 11:00 -> 12:00, 12:00 -> 13:00 (+60 mins delay)
        $b2->refresh();
        $this->assertEquals('12:00:00', $b2->start_time);
        $this->assertEquals('13:00:00', $b2->end_time);
        $this->assertEquals('11:00:00', $b2->original_start_time);
        $this->assertEquals('12:00:00', $b2->original_end_time);
        $this->assertTrue($b2->is_delayed);
        $this->assertEquals(60, $b2->delay_minutes);

        // Verify b3 shifted: 12:00 -> 13:00, 13:00 -> 14:00 (+60 mins delay)
        $b3->refresh();
        $this->assertEquals('13:00:00', $b3->start_time);
        $this->assertEquals('14:00:00', $b3->end_time);
        $this->assertEquals('12:00:00', $b3->original_start_time);
        $this->assertEquals('13:00:00', $b3->original_end_time);
        $this->assertTrue($b3->is_delayed);
        $this->assertEquals(60, $b3->delay_minutes);

        // Verify automated WhatsApp notifications logged
        $this->assertDatabaseHas('wa_logs', [
            'booking_id' => $b2->id,
            'message_type' => WaLog::TYPE_SCHEDULE_DELAYED,
            'phone_number' => '08123456789',
        ]);

        $this->assertDatabaseHas('wa_logs', [
            'booking_id' => $b3->id,
            'message_type' => WaLog::TYPE_SCHEDULE_DELAYED,
            'phone_number' => '08987654321',
        ]);
    }

    public function test_idle_gap_absorbs_schedule_delay(): void
    {
        $date = Carbon::tomorrow()->toDateString();

        // Booking 1: 10:00 - 11:00
        $b1 = Booking::create([
            'booking_code' => 'BN-GAP-01',
            'customer_name' => 'Tamu 1',
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'total_price' => 100000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_IN_PROGRESS,
            'branch' => 'Citra Garden 6',
        ]);

        // Booking 2 has a 1-hour gap: 12:00 - 13:00
        $b2 = Booking::create([
            'booking_code' => 'BN-GAP-02',
            'customer_name' => 'Tamu 2',
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
            'total_price' => 120000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_CONFIRMED,
            'branch' => 'Citra Garden 6',
        ]);

        // Upgrade b1 with +30 mins (ends at 11:30)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.bookings.applyUpgrade', $b1), [
                'additional_minutes' => 30,
                'additional_price' => 0,
            ]);

        $response->assertSessionHasNoErrors();

        // b1 ends at 11:30
        $b1->refresh();
        $this->assertEquals('11:30:00', $b1->end_time);

        // b2 starts at 12:00, which is > 11:30. It should NOT be shifted!
        $b2->refresh();
        $this->assertEquals('12:00:00', $b2->start_time);
        $this->assertEquals('13:00:00', $b2->end_time);
        $this->assertFalse((bool) $b2->is_delayed);
    }

    public function test_customer_can_acknowledge_delay(): void
    {
        $date = Carbon::tomorrow()->toDateString();

        $booking = Booking::create([
            'booking_code' => 'BN-ACK-01',
            'customer_id' => $this->customer->id,
            'artist_id' => $this->artist->id,
            'booking_date' => $date,
            'start_time' => '14:30:00',
            'end_time' => '15:30:00',
            'original_start_time' => '14:00:00',
            'original_end_time' => '15:00:00',
            'is_delayed' => true,
            'delay_minutes' => 30,
            'total_price' => 100000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_CONFIRMED,
            'branch' => 'Citra Garden 6',
        ]);

        // Customer confirms new time
        $response = $this->actingAs($this->customer)
            ->post(route('booking.ackDelay', $booking), [
                'action' => 'confirmed',
            ]);

        $response->assertSessionHasNoErrors();
        $booking->refresh();
        $this->assertEquals('confirmed_new_time', $booking->customer_delay_ack);
    }
}
