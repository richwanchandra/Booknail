<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected NailArtist $artist;
    protected Service $service;
    protected Booking $bookingCompleted;
    protected Booking $bookingPending;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'name' => 'Owner Admin Studio',
            'email' => 'admin-report@nailglam.test',
        ]);
        $this->admin->assignRole($adminRole);

        $artistUser = User::factory()->create(['name' => 'Nadhira Specialist']);
        $this->artist = NailArtist::create([
            'user_id' => $artistUser->id,
            'bio' => 'Senior Nail Artist',
            'is_active' => true,
        ]);

        $cat = Category::create([
            'name' => 'Nail Art',
            'slug' => 'nail-art',
            'icon' => 'sparkles',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'category_id' => $cat->id,
            'name' => 'Gel Nail Art Premium',
            'slug' => 'gel-nail-art-premium',
            'price' => 250000,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        // 1. Completed booking this month
        $this->bookingCompleted = Booking::create([
            'booking_code' => 'BK-REP-001',
            'customer_name' => 'Clarissa Putri',
            'customer_phone' => '081299990001',
            'artist_id' => $this->artist->id,
            'booking_date' => today()->startOfMonth()->addDays(5)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'total_price' => 250000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_COMPLETED,
            'is_walk_in' => false,
            'booking_source' => Booking::SOURCE_ONLINE,
        ]);

        BookingDetail::create([
            'booking_id' => $this->bookingCompleted->id,
            'service_id' => $this->service->id,
            'price' => 250000,
            'duration_minutes' => 60,
        ]);

        Payment::create([
            'booking_id' => $this->bookingCompleted->id,
            'payment_type' => 'dp',
            'amount' => 50000,
            'payment_method' => 'bank_transfer',
            'status' => Payment::STATUS_VERIFIED,
        ]);

        // 2. Pending booking
        $this->bookingPending = Booking::create([
            'booking_code' => 'BK-REP-002',
            'customer_name' => 'Jessica Mila',
            'customer_phone' => '081299990002',
            'artist_id' => $this->artist->id,
            'booking_date' => today()->startOfMonth()->addDays(6)->toDateString(),
            'start_time' => '13:00:00',
            'end_time' => '14:00:00',
            'total_price' => 250000,
            'dp_amount' => 50000,
            'status' => Booking::STATUS_PENDING,
            'is_walk_in' => true,
            'booking_source' => Booking::SOURCE_WALK_IN,
        ]);

        BookingDetail::create([
            'booking_id' => $this->bookingPending->id,
            'service_id' => $this->service->id,
            'price' => 250000,
            'duration_minutes' => 60,
        ]);
    }

    public function test_guest_cannot_access_reports_or_export(): void
    {
        $this->get(route('admin.reports.index'))->assertRedirect(route('login'));
        $this->get(route('admin.reports.exportPdf'))->assertRedirect(route('login'));
        $this->get(route('admin.reports.exportCsv'))->assertRedirect(route('login'));
    }

    public function test_admin_can_view_reports_page_with_data_and_summary(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('Laporan & Ekspor Reservasi');
        $response->assertSee('BK-REP-001');
        $response->assertSee('BK-REP-002');
        $response->assertSee('Clarissa Putri');
        $response->assertSee('Jessica Mila');
        $response->assertSee('Unduh PDF');
        $response->assertSee('Unduh CSV (Excel)');
    }

    public function test_admin_can_filter_reports_by_status(): void
    {
        // Filter only completed
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'status' => Booking::STATUS_COMPLETED,
        ]));

        $response->assertOk();
        $response->assertSee('BK-REP-001');
        $response->assertDontSee('BK-REP-002');
    }

    public function test_admin_can_export_reports_as_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.exportPdf', [
            'status' => Booking::STATUS_COMPLETED,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());
    }

    public function test_admin_can_export_reports_as_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.exportCsv'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));

        // Capture streamed response content
        $csvContent = $response->streamedContent();

        // Must start with UTF-8 BOM
        $this->assertStringStartsWith(chr(0xEF).chr(0xBB).chr(0xBF), $csvContent);

        // Must contain header and data
        $this->assertStringContainsString('Kode Booking', $csvContent);
        $this->assertStringContainsString('BK-REP-001', $csvContent);
        $this->assertStringContainsString('Clarissa Putri', $csvContent);
        $this->assertStringContainsString('Gel Nail Art Premium', $csvContent);
        $this->assertStringContainsString('Nadhira Specialist', $csvContent);
    }
}

